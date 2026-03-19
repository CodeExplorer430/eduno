import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
    testDir: './e2e',
    globalSetup: './e2e/setup/global-setup.ts',
    outputDir: 'playwright-report',
    fullyParallel: false,
    forbidOnly: !!process.env.CI,
    retries: process.env.CI ? 1 : 0,
    workers: 1,
    reporter: 'html',
    use: {
        baseURL: 'http://127.0.0.1:8000',
        trace: 'on-first-retry',
    },
    projects: [
        { name: 'chromium', use: { ...devices['Desktop Chrome'] } },
    ],
    webServer: {
        command: 'php artisan serve --env=e2e',
        url: 'http://127.0.0.1:8000',
        reuseExistingServer: !process.env.CI,
        timeout: 120_000,
    },
});
