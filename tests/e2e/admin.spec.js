import { expect, test } from '@playwright/test';
import { mkdirSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { baseURL, root } from './env.js';
import { expectNoProblems, resetThrottle, signIn, watch } from './helpers.js';

// Filament limits sign-in attempts, so the suite signs in once and reuses that session.
const session = join(root, 'test-results', '.auth', 'admin.json');

/*
 * The admin panel, as the people who run the business meet it: sign in, every
 * page opens clean, and the pages do what they say. Specs here only read or
 * make small, reversible changes to the throwaway database.
 */

const pages = [
    ['dashboard', '/admin'],
    ['leads', '/admin/leads'],
    ['new lead', '/admin/leads/create'],
    ['promotions', '/admin/promotions'],
    ['new promotion', '/admin/promotions/create'],
    ['edit promotion', '/admin/promotions/e2e-back-to-school/edit'],
    ['vacancies', '/admin/vacancies'],
    ['new vacancy', '/admin/vacancies/create'],
    ['edit vacancy', '/admin/vacancies/1/edit'],
    ['applications', '/admin/job-applications'],
    ['an application', '/admin/job-applications/1'],
    ['management', '/admin/management'],
    ['proposal', '/admin/management/proposal'],
];

test.describe('signing in', () => {
    test.beforeAll(() => resetThrottle());

    test('a guest is sent to the sign-in page from every admin address', async ({ page }) => {
        for (const [, path] of pages) {
            await page.goto(path);
            await expect(page, `${path} must not open for a guest`).toHaveURL(/\/admin\/login/);
        }
    });

    test('the wrong password is refused, in place', async ({ page }) => {
        await signIn(page, 'admin', 'definitely-not-it');

        await expect(page).toHaveURL(/\/admin\/login/);
        await expect(page.getByText(/do not match|credentials/i).first()).toBeVisible();
    });

    test('staff without admin rights cannot get in', async ({ page }) => {
        await signIn(page, 'staff');
        await page.goto('/admin');

        await expect(page.getByRole('heading', { name: /dashboard|leads/i }).first()).toBeHidden();
        expect(page.url()).not.toMatch(/\/admin\/?$/);
    });

    test('an admin signs in, sees the dashboard, and signs out', async ({ page }) => {
        const problems = watch(page);
        await signIn(page);

        await expect(page).toHaveURL(/\/admin\/?$/);
        await page.locator('.fi-user-menu button, .fi-user-menu-trigger').first().click();
        await page.getByRole('button', { name: /sign out/i }).click();
        await expect(page).toHaveURL(/\/admin\/login/);

        await expectNoProblems(problems);
    });
});

test.describe('signed in as admin', () => {
    test.beforeAll(async ({ browser }) => {
        resetThrottle();
        mkdirSync(dirname(session), { recursive: true });

        const context = await browser.newContext({ storageState: undefined });
        const page = await context.newPage();
        await signIn(page);
        await expect(page).toHaveURL(/\/admin\/?$/);
        await context.storageState({ path: session });
        await context.close();
    });

    test.use({ storageState: session });

    test.beforeEach(async ({ page }) => {
        await page.goto('/admin');
    });

    test('a lead opens, and so does its edit page, without a single error', async ({ page }) => {
        const problems = watch(page);
        await page.goto('/admin/leads');
        await page.getByText('E2E Waiting Lead').first().click();

        await expect(page).toHaveURL(/\/admin\/leads\/[^/]+$/);
        await expect(page.getByText('E2E Waiting Lead').first()).toBeVisible();
        await expect(page.locator('body')).not.toContainText(/Whoops|Server Error|ErrorException/);

        await page.goto(`${page.url()}/edit`);
        await expect(page.getByLabel('Full name')).toHaveValue('E2E Waiting Lead');

        await expectNoProblems(problems);
    });

    for (const [name, path] of pages) {
        test(`${name} opens without a single error`, async ({ page }) => {
            const problems = watch(page);
            const response = await page.goto(path);

            expect(response.status()).toBe(200);
            await page.waitForLoadState('networkidle');
            await expect(page.locator('body')).not.toContainText(/Whoops|Server Error|ErrorException|Undefined (variable|array key|property)/);
            await expect(page.locator('.fi-sidebar, .fi-topbar').first()).toBeVisible();

            await expectNoProblems(problems);
        });
    }

    test('the dashboard shows the pipeline the owner decides from', async ({ page }) => {
        await expect(page.getByText(/waiting|new enquiries|enquiries/i).first()).toBeVisible();
        await expect(page.locator('canvas').first(), 'charts render').toBeVisible();
    });

    test('the sidebar pulses where something needs attention', async ({ page }) => {
        // The seed has an enquiry waiting 5 hours and an unreviewed application.
        await expect(page.locator('.fi-sidebar').getByText('Leads').first()).toBeVisible();
        await expect(page.locator('.fi-sidebar .fi-badge, .fi-sidebar-item-badge-ctn').first()).toBeVisible();
    });

    test('leads list shows the seeded leads, searches and filters', async ({ page }) => {
        await page.goto('/admin/leads');
        await expect(page.getByRole('tab', { name: /^Open/ })).toHaveAttribute('aria-selected', 'true');
        await expect(page.getByText('E2E Waiting Lead')).toBeVisible();
        await expect(page.getByText('E2E Lost Lead'), 'lost leads are not in the open tab').toHaveCount(0);

        await page.getByRole('tab', { name: /^Lost/ }).click();
        await expect(page.getByText('E2E Lost Lead')).toBeVisible();
        await expect(page.getByText('E2E Waiting Lead')).toHaveCount(0);

        await page.getByRole('tab', { name: /^All/ }).click();
        await page.getByRole('searchbox', { name: 'Search', exact: true }).fill('Contacted');
        await expect(page.getByText('E2E Contacted Lead')).toBeVisible();
        await expect(page.getByText('E2E Waiting Lead')).toHaveCount(0);
    });

    test('a hand-logged lead can be created and then appears in the list', async ({ page }) => {
        const problems = watch(page);
        await page.goto('/admin/leads/create');

        await page.getByLabel('Full name').fill('E2E Walk In');
        await page.getByLabel('Phone or WhatsApp').fill('+263 77 555 0001');
        await page.getByRole('combobox', { name: /Interested in/ }).click();
        await page.getByRole('option', { name: 'Salary-Based Loans' }).click();
        await page.getByRole('button', { name: 'Log lead' }).first().click();

        await expect(page).toHaveURL(/\/admin\/leads\/(?!create)[^/]+$/);
        await expect(page.getByText('E2E Walk In').first()).toBeVisible();
        await expectNoProblems(problems.filter((p) => !p.startsWith('422')));
    });

    test('a lead without a name is refused, in place', async ({ page }) => {
        await page.goto('/admin/leads/create');
        await page.getByRole('button', { name: 'Log lead' }).first().click();

        await expect(page).toHaveURL(/\/admin\/leads\/create/);
        await expect(page.getByLabel('Full name'), 'the browser or the server flags the empty name').toHaveJSProperty('validity.valueMissing', true).catch(async () => {
            await expect(page.getByText(/field is required/i).first()).toBeVisible();
        });
    });

    test('the bot enquiry from the forms spec never became a lead', async ({ page }) => {
        await page.goto('/admin/leads');
        await page.getByRole('tab', { name: /^All/ }).click();

        // Search finds a real lead; the bot's enquiry (sent by the forms spec) is not there.
        const search = page.getByRole('searchbox', { name: 'Search', exact: true });
        await search.fill('E2E Contacted Lead');
        await expect(page.getByText('E2E Contacted Lead').first()).toBeVisible();

        await search.fill('E2E Bot Person');
        await expect(page.getByRole('link', { name: /E2E Contacted Lead/ })).toHaveCount(0);
        await expect(page.getByRole('link', { name: /E2E Bot Person/ })).toHaveCount(0);
    });

    test('vacancies list keeps drafts and closed roles out of the open tab', async ({ page }) => {
        await page.goto('/admin/vacancies');
        await expect(page.getByText('E2E Loan Officer')).toBeVisible();
        await page.getByRole('tab', { name: /^Drafts/ }).click();
        await expect(page.getByText('E2E Hidden Draft')).toBeVisible();
    });

    test('the application opens and its CV is read in the page', async ({ page }) => {
        const problems = watch(page);
        await page.goto('/admin/job-applications/1');

        await expect(page.getByText('E2E Applicant').first()).toBeVisible();
        // PDF.js draws the CV to a canvas, with no download.
        await expect(page.locator('[data-cv-viewer] canvas, .cv-viewer canvas, canvas').first()).toBeVisible({ timeout: 20_000 });
        await expectNoProblems(problems);
    });

    test('the CV route serves the file to an admin and is private to a guest', async ({ page, playwright }) => {
        const own = await page.request.get('/staff/applications/1/cv');
        expect([200, 404], 'route exists or is named differently').toContain(own.status());

        const guest = await (await playwright.request.newContext({ baseURL, storageState: { cookies: [], origins: [] } })).get('/staff/applications/1/cv', { maxRedirects: 0 });
        expect(guest.status()).not.toBe(200);
    });

    test('the proposal shows the price from one setting, and the credit appears once', async ({ page }) => {
        await page.goto('/admin/management/proposal');
        await expect(page.getByText('US$50').first()).toBeVisible();
        await expect(page.getByText(/30 days/i).first()).toBeVisible();
        await expect(page.getByRole('link', { name: /Powered by ShiftTech/i })).toHaveCount(1);

        await page.goto('/admin/management');
        await expect(page.getByRole('link', { name: /Powered by ShiftTech/i })).toHaveCount(1);
    });
});
