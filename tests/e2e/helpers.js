import { expect } from '@playwright/test';
import { spawnSync } from 'node:child_process';
import { accounts, appEnv, baseURL, root } from './env.js';

/**
 * Records everything that should never happen on a healthy page: script
 * errors, console errors and failed requests to this site. Requests to other
 * sites (map tiles, fonts) are not the application's to fix and are ignored.
 */
export function watch(page) {
    const problems = [];

    page.on('pageerror', (error) => problems.push(`script error: ${error.message}`));
    page.on('console', (message) => {
        if (message.type() !== 'error') {
            return;
        }

        const text = message.text();

        // The browser logs a failed load here as well; the response handler below names the URL.
        if (/Failed to load resource/.test(text)) {
            return;
        }

        problems.push(`console error: ${text}`);
    });
    page.on('response', (response) => {
        const url = response.url();

        if (url.startsWith(baseURL) && response.status() >= 400) {
            problems.push(`${response.status()} ${url.replace(baseURL, '')}`);
        }
    });
    page.on('requestfailed', (request) => {
        const url = request.url();

        if (url.startsWith(baseURL) && request.failure()?.errorText !== 'net::ERR_ABORTED') {
            problems.push(`request failed: ${url.replace(baseURL, '')} (${request.failure()?.errorText})`);
        }
    });

    return problems;
}

/** Scrolls top to bottom in steps so lazy images load and scroll reveals run. */
export async function scrollThrough(page) {
    await page.evaluate(async () => {
        const step = Math.max(window.innerHeight * 0.8, 400);

        for (let y = 0; y < document.documentElement.scrollHeight; y += step) {
            window.scrollTo(0, y);
            await new Promise((resolve) => setTimeout(resolve, 120));
        }

        window.scrollTo(0, 0);
    });
}

/** Signs in to the admin panel through the real form. */
export async function signIn(page, who = 'admin', password = process.env.E2E_PASSWORD) {
    await page.goto('/admin/login');
    await page.locator('[id="form.email"]').fill(accounts[who]);
    await page.locator('[id="form.password"]').fill(password);
    await page.getByRole('button', { name: /sign in/i }).click();
}

export async function expectNoProblems(problems) {
    expect(problems, `unexpected problems:\n${problems.join('\n')}`).toEqual([]);
}

/**
 * Clears the rate limiter (it lives in the throwaway database's cache table),
 * so a spec that posts a form starts with a full allowance of five a minute.
 */
export function resetThrottle() {
    const result = spawnSync('php', ['artisan', 'cache:clear', '--no-interaction'], { cwd: root, env: { ...process.env, ...appEnv }, encoding: 'utf8' });

    if (result.status !== 0) {
        throw new Error(`cache:clear failed: ${result.stderr}`);
    }
}
