import { expect, test } from '@playwright/test';
import { promotion } from './env.js';
import { expectNoProblems, scrollThrough, watch } from './helpers.js';

const pages = [
    ['/', 'Home'],
    ['/loans', 'Loans'],
    ['/how-to-apply', 'How to apply'],
    ['/faq', 'Questions'],
    ['/glossary', 'Glossary'],
    ['/loans/salary-based-loans', 'A loan page'],
    ['/loans/sme-bridging-finance', 'Another loan page'],
    ['/about', 'About'],
    ['/careers', 'Careers'],
    ['/contact', 'Contact'],
    ['/partners', 'Partners'],
    ['/promotions', 'Promotions'],
    [`/promotions/${promotion.slug}`, 'A promotion'],
    ['/insights', 'Insights'],
    ['/privacy', 'Privacy notice'],
    ['/terms', 'Terms of use'],
    ['/responsible-lending', 'Responsible lending'],
    ['/complaints', 'Complaints procedure'],
];

for (const [path, name] of pages) {
    test.describe(`${name} (${path})`, () => {
        test('loads cleanly, with the basics every page needs', async ({ page }) => {
            const problems = watch(page);
            const response = await page.goto(path);

            expect(response?.status()).toBe(200);
            await page.waitForLoadState('networkidle');
            await scrollThrough(page);

            await expect(page).toHaveTitle(/Baardy/i);
            await expect(page.locator('html')).toHaveAttribute('lang', /.+/);
            expect(await page.locator('meta[name="description"]').getAttribute('content')).toBeTruthy();
            await expect(page.locator('h1')).toHaveCount(1);

            // Nothing sideways: a visitor cannot scroll the page horizontally at this width.
            const scrolledTo = await page.evaluate(() => {
                window.scrollTo(10000, 0);

                return window.scrollX;
            });
            expect(scrolledTo, 'the page scrolls sideways').toBeLessThanOrEqual(1);

            // And a phone is not forced to zoom the page out to fit something too wide.
            const scale = await page.evaluate(() => window.visualViewport?.scale ?? 1);
            expect(scale, 'the browser has zoomed the page out to fit overflowing content').toBeGreaterThanOrEqual(0.99);

            // Every picture that was asked for actually loaded.
            const broken = await page.evaluate(() => [...document.images].filter((img) => img.complete && img.naturalWidth === 0 && img.currentSrc).map((img) => img.currentSrc));
            expect(broken, 'broken images').toEqual([]);

            // A skip link, a main landmark and a footer.
            await expect(page.locator('main#main')).toHaveCount(1);
            await expect(page.locator('footer')).toHaveCount(1);

            await expectNoProblems(problems);
        });

        test('every image has alt text (decorative ones an empty alt)', async ({ page }) => {
            await page.goto(path);
            const missing = await page.evaluate(() => [...document.images].filter((img) => !img.hasAttribute('alt')).map((img) => img.currentSrc || img.src));
            expect(missing, 'images with no alt attribute').toEqual([]);
        });
    });
}

test('an unknown address gives a real 404, not a stack trace', async ({ page }) => {
    const response = await page.goto('/no-such-page');
    expect(response?.status()).toBe(404);

    const html = await page.content();
    expect(html).not.toMatch(/Illuminate\\|vendor\/laravel|APP_KEY|Stack trace/i);
});

test('robots.txt and the favicon are served', async ({ request }) => {
    expect((await request.get('/robots.txt')).status()).toBe(200);
    expect((await request.get('/favicon.ico')).status()).toBe(200);
});
