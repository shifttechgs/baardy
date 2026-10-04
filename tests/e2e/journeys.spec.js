import { expect, test } from '@playwright/test';
import { promotion } from './env.js';
import { expectNoProblems, resetThrottle, watch } from './helpers.js';

/*
 * The journeys that make money: a visitor lands on a promotion, enquires, and
 * the lead arrives with the right source. Plus the cases that must fail safely.
 */

const ended = 'e2e-ended-offer';

test.describe('promotions', () => {
    test('the live promotion page sells, and its call to action leads to the enquiry', async ({ page }) => {
        const problems = watch(page);
        await page.goto(`/promotions/${promotion.slug}`);

        await expect(page.getByRole('heading', { name: promotion.title, level: 1 })).toBeVisible();
        await expect(page.getByRole('link', { name: /ask on whatsapp|whatsapp us/i }).first()).toHaveAttribute('href', /wa\.me/);

        const cta = page.getByRole('link', { name: 'Visit a branch' }).last();
        await expect(cta).toBeVisible();
        await expect(page.getByRole('link', { name: /start an application/i })).toHaveCount(0);

        await expectNoProblems(problems);
    });

    test('an ended promotion says so and sends people on, instead of selling', async ({ page }) => {
        const response = await page.goto(`/promotions/${ended}`);

        expect(response.status()).toBeLessThan(500);
        await expect(page.getByRole('link', { name: /see what is on now/i }).first()).toBeVisible();
    });

    test('an unknown promotion is a clean 404, not an error page with internals', async ({ page }) => {
        const response = await page.goto('/promotions/no-such-offer');

        expect(response.status()).toBe(404);
        await expect(page.locator('body')).not.toContainText(/Illuminate|vendor\/|Stack trace|SQLSTATE/);
    });

    test('the promotions list shows the live offer', async ({ page }) => {
        await page.goto('/promotions');
        await expect(page.getByRole('main').getByText(promotion.title).first()).toBeVisible();
    });
});

test.describe('where a lead came from', () => {
    test.beforeAll(() => resetThrottle());

    test('a visitor from a tagged promotion link is recorded against it', async ({ page }) => {
        await page.goto(`/promotions/${promotion.slug}?utm_source=facebook&utm_medium=social&utm_campaign=${promotion.slug}`);
        await page.goto('/contact');

        await page.locator('#enquiry-name').fill('E2E Attributed Person');
        await page.locator('#enquiry-phone').fill('+263 77 666 0001');
        await page.locator('#enquiry-interest').selectOption('Salary-Based Loans');
        await page.locator('label:has(input[name="branch"][value="Harare"])').click();
        await page.getByRole('button', { name: /send enquiry/i }).click();
        await expect(page.getByRole('heading', { name: 'Message received' })).toBeVisible();
    });
});

test.describe('safe failures', () => {
    for (const path of ['/.env', '/.git/config', '/storage/app', '/vendor/autoload.php', '/artisan', '/composer.json', '/database/database.sqlite', '/admin/../.env']) {
        test(`${path} is not served`, async ({ request }) => {
            const response = await request.get(path, { maxRedirects: 0 });

            expect(response.status(), `${path} must not be readable`).toBeGreaterThanOrEqual(300);
            expect(await response.text()).not.toMatch(/APP_KEY|DB_PASSWORD|"require"/);
        });
    }

    test('a missing page is a friendly 404', async ({ page }) => {
        const response = await page.goto('/this-page-does-not-exist');

        expect(response.status()).toBe(404);
        await expect(page.locator('body')).not.toContainText(/Illuminate|Stack trace|vendor\//);
        await expect(page.getByRole('heading', { name: 'This page is not here' })).toBeVisible();
        await expect(page.getByRole('link', { name: 'Back to the home page' })).toBeVisible();
    });

    test('security headers are present', async ({ request }) => {
        const response = await request.get('/');
        const headers = response.headers();

        expect(headers['x-content-type-options']).toBe('nosniff');
        expect(headers['x-frame-options'] ?? headers['content-security-policy'], 'framing is restricted').toBeTruthy();
    });

    test('the enquiry form refuses a sixth post inside a minute', async ({ request }) => {
        resetThrottle();
        const statuses = [];
        const token = (await (await request.get('/contact')).text()).match(/name="_token" value="([^"]+)"/)?.[1];

        expect(token, 'the form carries a CSRF token').toBeTruthy();

        for (let i = 0; i < 7; i++) {
            const response = await request.post('/enquiries', { headers: { Accept: 'application/json', 'X-CSRF-TOKEN': token }, form: { name: '', phone: '' } });
            statuses.push(response.status());
        }

        expect(statuses, 'rejected five times, then throttled').toEqual([422, 422, 422, 422, 422, 429, 429]);
        resetThrottle();
    });
});
