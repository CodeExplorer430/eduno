import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
    testDir: './e2e',
    globalSetup: './e2e/setup/global-setup.ts',
    outputDir: 'playwright-report',
    fullyParallel: false,
    forbidOnly: !!process.env.CI,
    retries: process.env.CI ? 2 : 0,
    workers: 1,
    reporter: 'html',
    timeout: process.env.CI ? 90_000 : 30_000,
    use: {
        baseURL: 'http://127.0.0.1:8000',
        trace: 'on-first-retry',
        actionTimeout: process.env.CI ? 60_000 : 15_000,
        navigationTimeout: process.env.CI ? 60_000 : 15_000,
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
