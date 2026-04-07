import { test, expect } from '@playwright/test';
import { loginAs } from './fixtures/auth';

test.beforeEach(async ({ page }) => {
    await loginAs(page, 'instructor@eduno.test', 'password');
});

test('instructor dashboard has a gradebook link for E2E Test Course', async ({ page }) => {
    const gradebookLink = page.getByRole('link', { name: /gradebook/i }).first();
    await expect(gradebookLink).toBeVisible();
});

test('instructor can open gradebook from dashboard', async ({ page }) => {
    await page.getByRole('link', { name: /gradebook/i }).first().click();
    await expect(page).toHaveURL(/\/instructor\/sections\/\d+\/gradebook/);
});

test('gradebook page shows Gradebook heading', async ({ page }) => {
    await page.getByRole('link', { name: /gradebook/i }).first().click();
    await expect(page.getByRole('heading', { name: /gradebook/i })).toBeVisible();
});

test('gradebook page shows E2E Student in the roster', async ({ page }) => {
    await page.getByRole('link', { name: /gradebook/i }).first().click();
    await expect(page.getByText('E2E Student')).toBeVisible();
});
