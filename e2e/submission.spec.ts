import { test, expect } from '@playwright/test';
import { fileURLToPath } from 'url';
import path from 'path';
import { loginAs } from './fixtures/auth';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

test.beforeEach(async ({ page }) => {
    await loginAs(page, 'student@eduno.test', 'password');
    // Navigate to the E2E Assignment detail page from the assignments index
    await page.goto('/student/assignments');
    await page.getByRole('link', { name: /E2E Assignment/i }).first().click();
    await expect(page).toHaveURL(/\/student\/assignments\/\d+/);
});

test('student can navigate to submission create form from assignment page', async ({ page }) => {
    await page.getByRole('link', { name: /submit assignment/i }).click();
    await expect(page).toHaveURL(/\/submit$/);
});

test('student can attach a file to the submission form', async ({ page }) => {
    await page.getByRole('link', { name: /submit assignment/i }).click();

    const fixturePath = path.join(__dirname, 'fixtures', 'test-upload.txt');
    await page.locator('input[type="file"]').setInputFiles(fixturePath);

    await expect(page.getByText('test-upload.txt', { exact: true })).toBeVisible();
});

test('student can submit assignment and see submission detail', async ({ page }) => {
    await page.getByRole('link', { name: /submit assignment/i }).click();

    const fixturePath = path.join(__dirname, 'fixtures', 'test-upload.txt');
    await page.locator('input[type="file"]').setInputFiles(fixturePath);

    await page.getByRole('button', { name: /submit assignment/i }).click();
    await expect(page).toHaveURL(/\/student\/submissions\/\d+/);
});
