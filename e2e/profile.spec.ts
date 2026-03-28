import { test, expect } from '@playwright/test';
import { loginAs } from './fixtures/auth';

test.beforeEach(async ({ page }) => {
    await loginAs(page, 'student@eduno.test', 'password');
});

test('profile page loads with Profile heading', async ({ page }) => {
    await page.goto('/profile');
    // The page has both a layout h1 and section h2 — target the top-level h1
    await expect(page.getByRole('heading', { name: 'Profile', level: 1 })).toBeVisible();
});

test('profile page shows Account Information section', async ({ page }) => {
    await page.goto('/profile');
    await expect(page.getByText('Account Information')).toBeVisible();
});

test('accessibility settings page loads', async ({ page }) => {
    await page.goto('/profile/accessibility');
    await expect(page.getByRole('heading', { name: /accessibility/i })).toBeVisible();
});

test('accessibility settings page includes Filipino language option', async ({ page }) => {
    await page.goto('/profile/accessibility');
    await expect(page.getByRole('radio', { name: /filipino/i })).toBeVisible();
});

test('student can update profile name', async ({ page }) => {
    await page.goto('/profile');

    const nameInput = page.getByLabel('Name').first();
    await nameInput.fill('E2E Student Updated');

    await page.getByRole('button', { name: /save|update/i }).first().click();

    // Expect success message or name persists
    await expect(page.getByText(/saved|updated|success/i)).toBeVisible();
});
