import { expect, test } from '@playwright/test';

/*
 * Every internal link on every public page goes somewhere that exists, and
 * every in-page anchor points at an element that is there.
 */
const pages = ['/', '/how-to-apply', '/faq', '/glossary', '/loans', '/loans/salary-based-loans', '/loans/educational-loans', '/about', '/careers', '/contact', '/partners', '/promotions', '/promotions/e2e-back-to-school', '/insights', '/privacy', '/terms', '/responsible-lending', '/complaints'];

test('no internal link on any public page is broken', async ({ page, request }) => {
    const checked = new Map();
    const broken = [];

    for (const path of pages) {
        await page.goto(path);

        const links = await page.evaluate(() => [...document.querySelectorAll('a[href]')].map((a) => a.getAttribute('href')));

        for (const href of new Set(links)) {
            if (/^(mailto:|tel:|sms:|javascript:|https?:\/\/(?!127\.0\.0\.1))/.test(href) || href === '#' || href.startsWith('#')) {
                continue;
            }

            const target = new URL(href, 'http://127.0.0.1:8001' + path).pathname;

            if (!checked.has(target)) {
                const response = await request.get(target);
                checked.set(target, response.status());
            }

            if (checked.get(target) >= 400) {
                broken.push(`${path} -> ${href} (${checked.get(target)})`);
            }
        }
    }

    expect(checked.size, 'the crawl should have found real links').toBeGreaterThan(10);
    expect(broken, `broken links:\n${broken.join('\n')}`).toEqual([]);
});

test('in-page anchors point at elements that exist', async ({ page }) => {
    const missing = [];

    for (const path of pages) {
        await page.goto(path);

        const anchors = await page.evaluate(() => [...document.querySelectorAll('a[href^="#"], a[href*="/#"]')]
            .map((a) => a.getAttribute('href'))
            .filter((href) => href.length > 1));

        for (const href of new Set(anchors)) {
            const hash = href.slice(href.indexOf('#') + 1);
            const isHere = href.startsWith('#') || new URL(href, 'http://127.0.0.1:8001' + path).pathname === path;

            if (isHere && (await page.locator(`[id="${hash}"], [name="${hash}"]`).count()) === 0) {
                missing.push(`${path}: ${href}`);
            }
        }
    }

    expect(missing, `anchors with no target:\n${missing.join('\n')}`).toEqual([]);
});
