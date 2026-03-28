import { test, expect } from '@playwright/test';
import { loginAs } from './fixtures/auth';

test.beforeEach(async ({ page }) => {
    await loginAs(page, 'student@eduno.test', 'password');
});

test('dashboard has My Courses heading', async ({ page }) => {
    await expect(page.getByRole('heading', { name: 'My Courses' })).toBeVisible();
});

test('dashboard has Upcoming Assignments heading', async ({ page }) => {
    await expect(page.getByRole('heading', { name: 'Upcoming Assignments' })).toBeVisible();
});

test('dashboard has Recent Announcements heading', async ({ page }) => {
    await expect(page.getByRole('heading', { name: 'Recent Announcements' })).toBeVisible();
});

test('dashboard has Recent Grades heading', async ({ page }) => {
    await expect(page.getByRole('heading', { name: 'Recent Grades' })).toBeVisible();
});

test('E2E Assignment is visible on assignments page and navigates to detail', async ({ page }) => {
    await page.goto('/student/assignments');
    const link = page.getByRole('link', { name: /E2E Assignment/i }).first();
    await expect(link).toBeVisible();
    await link.click();
    await expect(page).toHaveURL(/\/student\/assignments\/\d+/);
});
