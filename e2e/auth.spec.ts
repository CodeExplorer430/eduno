import { test, expect } from '@playwright/test';
import { loginAs } from './fixtures/auth';

test('student logs in and lands on /dashboard', async ({ page }) => {
    await loginAs(page, 'student@eduno.test', 'password');
    await expect(page).toHaveURL(/\/dashboard/);
});

test('logout redirects to /', async ({ page }) => {
    await loginAs(page, 'student@eduno.test', 'password');
    await page.getByRole('button', { name: 'E2E Student' }).click();
    await page.getByRole('button', { name: 'Log Out' }).click();
    await expect(page).toHaveURL('/');
});
