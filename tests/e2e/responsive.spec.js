import { expect, test } from '@playwright/test';
import { expectNoProblems, watch } from './helpers.js';

/*
 * The header, in the form each screen gets: dropdown menus on a desktop, the
 * full-screen menu on a phone. This file runs in both projects.
 */

test.describe('header menus', () => {
    test('the Loans and Company menus open, list their cards, link to real pages and close', async ({ page, isMobile }) => {
        test.skip(isMobile, 'dropdown menus are the desktop header');

        const problems = watch(page);
        await page.goto('/careers');

        const loans = page.locator('a[aria-controls="menu-loans"]');
        await loans.click();
        await expect(page.locator('#menu-loans')).toBeVisible();
        await expect(loans).toHaveAttribute('aria-expanded', 'true');

        // Six loans, plus the featured promotion in the last slot.
        await expect(page.locator('#menu-loans a')).toHaveCount(7);

        await page.keyboard.press('Escape');
        await expect(page.locator('#menu-loans')).toBeHidden();
        await expect(loans).toHaveAttribute('aria-expanded', 'false');

        const company = page.locator('a[aria-controls="menu-about"]');
        await company.click();
        await expect(page.locator('#menu-about')).toBeVisible();
        await expect(page.locator('#menu-about a')).toHaveCount(5);

        // Switching from one menu to the other leaves exactly one open.
        await loans.hover();
        await expect(page.locator('#menu-loans')).toBeVisible();
        await expect(page.locator('#menu-about')).toBeHidden();

        await expectNoProblems(problems);
    });

    test('every link in the dropdown menus goes somewhere that exists', async ({ page, request, isMobile }) => {
        test.skip(isMobile, 'dropdown menus are the desktop header');

        await page.goto('/');
        const hrefs = await page.evaluate(() => [...document.querySelectorAll('#menu-loans a, #menu-about a')].map((a) => a.getAttribute('href')));
        expect(hrefs.length).toBeGreaterThan(10);

        for (const href of new Set(hrefs)) {
            const url = href.startsWith('http') ? href : new URL(href, 'http://127.0.0.1').pathname;
            const response = await request.get(url);
            expect(response.status(), `${href} -> ${url}`).toBe(200);
        }
    });

    test('the promotion bar links to the promotion and sits within the hero edges', async ({ page, isMobile }) => {
        await page.goto('/');

        const banner = page.locator('#promo-banner a');
        await expect(banner).toBeVisible();
        await expect(banner).toContainText('Limited offer');
        await expect(banner).toContainText('Term fees, spread across the term');
        await expect(banner).toHaveAttribute('href', /\/promotions\/e2e-back-to-school/);

        // Lined up with the hero: inset 8px each side, like the hero frame.
        const box = await banner.boundingBox();
        const viewport = page.viewportSize();
        expect(box.x).toBeGreaterThanOrEqual(7);
        expect(box.x).toBeLessThanOrEqual(9);
        expect(Math.round(viewport.width - (box.x + box.width))).toBeLessThanOrEqual(24);

        if (!isMobile) {
            await banner.click();
            await expect(page).toHaveURL(/\/promotions\/e2e-back-to-school/);
        }
    });
});

test.describe('mobile menu', () => {
    test('opens as a full menu, lists every section and closes again', async ({ page, isMobile }) => {
        test.skip(!isMobile, 'the full-screen menu is the phone header');

        const problems = watch(page);
        await page.goto('/careers');

        const toggle = page.locator('button[aria-controls="mobile-menu"]');
        await expect(toggle).toBeVisible();
        await toggle.click();

        const menu = page.locator('#mobile-menu');
        await expect(menu).toBeVisible();
        await expect(menu.getByRole('link', { name: 'Who we are' })).toBeVisible();
        await expect(menu.getByRole('link', { name: 'Careers' })).toBeVisible();
        await expect(menu.getByRole('link', { name: 'Partners' })).toBeVisible();

        await toggle.click();
        await expect(menu).toBeHidden();

        await expectNoProblems(problems);
    });

    test('a link in the mobile menu navigates', async ({ page, isMobile }) => {
        test.skip(!isMobile, 'the full-screen menu is the phone header');

        await page.goto('/');
        await page.locator('button[aria-controls="mobile-menu"]').click();
        await page.locator('#mobile-menu').getByRole('link', { name: 'Partners' }).click();
        await expect(page).toHaveURL(/\/partners/);
    });
});
