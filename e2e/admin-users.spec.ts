import { test, expect } from '@playwright/test';
import { loginAs } from './fixtures/auth';

test.beforeEach(async ({ page }) => {
    await loginAs(page, 'admin@eduno.test', 'password');
});

test('admin users page lists seeded users', async ({ page }) => {
    await page.goto('/admin/users');
    await expect(page.getByText('E2E Student')).toBeVisible();
});

test('admin users page lists E2E Instructor', async ({ page }) => {
    await page.goto('/admin/users');
    await expect(page.getByText('E2E Instructor')).toBeVisible();
});

test('admin can navigate to edit user form', async ({ page }) => {
    await page.goto('/admin/users');
    await page.getByRole('link', { name: /Edit E2E Student/i }).click();
    await expect(page).toHaveURL(/\/admin\/users\/\d+\/edit/);
});

test('admin edit user form renders user name as heading', async ({ page }) => {
    await page.goto('/admin/users');
    await page.getByRole('link', { name: /Edit E2E Student/i }).click();
    await expect(page.getByRole('heading', { name: 'E2E Student' })).toBeVisible();
});
