import { test, expect } from '@playwright/test';
import { loginAs } from './fixtures/auth';

test.describe('Instructor creates announcement', () => {
    test.beforeEach(async ({ page }) => {
        await loginAs(page, 'instructor@eduno.test', 'password');
    });

    test('instructor can navigate to create announcement page', async ({ page }) => {
        await page.goto('/instructor/announcements/create');
        await expect(page).toHaveURL(/announcements\/create/);
        await expect(page.getByRole('heading', { name: /new announcement/i })).toBeVisible();
    });

    test('instructor can publish a new announcement', async ({ page }) => {
        await page.goto('/instructor/announcements/create');

        await page.selectOption('#course_section_id', { index: 1 });
        await page.getByLabel('Title').fill('E2E New Announcement');
        await page.locator('#body').fill('This announcement was created by the E2E test suite.');
        await page.getByRole('button', { name: /publish announcement/i }).click();

        await expect(page).toHaveURL(/\/instructor\/announcements/);
        await expect(page.getByText('E2E New Announcement')).toBeVisible();
    });
});

test('student sees seeded announcement on dashboard', async ({ page }) => {
    await loginAs(page, 'student@eduno.test', 'password');
    await expect(page.getByText('E2E Announcement')).toBeVisible();
});
