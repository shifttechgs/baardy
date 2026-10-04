import { defineConfig, devices } from '@playwright/test';
import { appEnv, baseURL, port } from './tests/e2e/env.js';

/*
 * End-to-end tests (tests/e2e). Run with `npm run e2e`.
 *
 * They start their own production-mode server on a separate port and a
 * throwaway database, so they never touch development data. One worker: PHP's
 * built-in server on Windows handles one request at a time.
 */
export default defineConfig({
    testDir: './tests/e2e',
    fullyParallel: false,
    workers: 1,
    retries: 0,
    timeout: 60_000,
    expect: { timeout: 10_000 },
    reporter: [['list'], ['html', { open: 'never', outputFolder: 'playwright-report' }]],
    globalSetup: './tests/e2e/global-setup.js',
    globalTeardown: './tests/e2e/global-teardown.js',
    use: {
        baseURL,
        trace: 'retain-on-failure',
        screenshot: 'only-on-failure',
    },
    webServer: {
        // Laravel's router script must run from inside public/, as `artisan serve` does.
        command: `php -S 127.0.0.1:${port} ../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php`,
        cwd: 'public',
        url: `${baseURL}/up`,
        reuseExistingServer: false,
        timeout: 60_000,
        env: appEnv,
    },
    projects: [
        { name: 'desktop', use: { ...devices['Desktop Chrome'], viewport: { width: 1440, height: 900 } } },
        { name: 'mobile', use: { ...devices['Pixel 7'] }, testMatch: /(public|responsive)\.spec\.js/ },
    ],
});
