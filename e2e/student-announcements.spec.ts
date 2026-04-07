import { test, expect } from '@playwright/test';
import { loginAs } from './fixtures/auth';

test.beforeEach(async ({ page }) => {
    await loginAs(page, 'student@eduno.test', 'password');
});

test('student announcements page has Announcements heading', async ({ page }) => {
    await page.goto('/student/announcements');
    await expect(page.getByRole('heading', { name: 'Announcements' })).toBeVisible();
});

test('student sees seeded E2E Announcement in announcements list', async ({ page }) => {
    await page.goto('/student/announcements');
    await expect(page.getByText('E2E Announcement')).toBeVisible();
});

test('seeded announcement shows correct course context', async ({ page }) => {
    await page.goto('/student/announcements');
    await expect(page.getByText('E2E Test Course').first()).toBeVisible();
});
