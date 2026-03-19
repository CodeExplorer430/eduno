import { test, expect } from '@playwright/test';

test('welcome page renders with a Log in link', async ({ page }) => {
    await page.goto('/');
    await expect(page.getByRole('link', { name: 'Log in' })).toBeVisible();
});

test('accessing /dashboard unauthenticated redirects to /login', async ({ page }) => {
    await page.goto('/dashboard');
    await expect(page).toHaveURL(/\/login/);
});
