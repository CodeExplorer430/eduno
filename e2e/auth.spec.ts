import { test, expect } from '@playwright/test';
import { loginAs } from './fixtures/auth';

test('student logs in and lands on /dashboard', async ({ page }) => {
    await loginAs(page, 'student@eduno.test', 'password');
    await expect(page).toHaveURL(/\/dashboard/);
});

test('logout redirects to /', async ({ page }) => {
    await loginAs(page, 'student@eduno.test', 'password');
    // Sidebar has a Log Out link rendered as a button — no dropdown needed
    await page.getByRole('button', { name: 'Log Out' }).click();
    await expect(page).toHaveURL('/');
});

test('login page has Continue with Google button', async ({ page }) => {
    await page.goto('/login');
    await expect(page.getByRole('link', { name: /google/i })).toBeVisible();
});

test('register page has Continue with Google button', async ({ page }) => {
    await page.goto('/register');
    await expect(page.getByRole('link', { name: /google/i })).toBeVisible();
});
