import { test, expect } from '@playwright/test';
import * as path from 'path';
import { loginAs } from './fixtures/auth';

test.beforeEach(async ({ page }) => {
    await loginAs(page, 'student@eduno.test', 'password');
});

test('student can navigate to submission create form from assignment page', async ({ page }) => {
    await page.getByRole('link', { name: 'E2E Assignment' }).first().click();
    await expect(page).toHaveURL(/\/student\/assignments\/\d+/);
    await page.getByRole('link', { name: /submit assignment/i }).click();
    await expect(page).toHaveURL(/\/submit$/);
});

test('student can attach a file to the submission form', async ({ page }) => {
    await page.getByRole('link', { name: 'E2E Assignment' }).first().click();
    await page.getByRole('link', { name: /submit assignment/i }).click();

    const fixturePath = path.join(__dirname, 'fixtures', 'test-upload.txt');
    await page.locator('input[type="file"]').setInputFiles(fixturePath);

    await expect(page.getByText('test-upload.txt')).toBeVisible();
});

test('student can submit assignment and see submission detail', async ({ page }) => {
    await page.getByRole('link', { name: 'E2E Assignment' }).first().click();
    await page.getByRole('link', { name: /submit assignment/i }).click();

    const fixturePath = path.join(__dirname, 'fixtures', 'test-upload.txt');
    await page.locator('input[type="file"]').setInputFiles(fixturePath);

    await page.getByRole('button', { name: /submit assignment/i }).click();
    await expect(page).toHaveURL(/\/student\/submissions\/\d+/);
});
