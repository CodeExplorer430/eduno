import { test, expect } from '@playwright/test';
import { loginAs } from './fixtures/auth';

test.beforeEach(async ({ page }) => {
    await loginAs(page, 'instructor@eduno.test', 'password');
});

test('instructor can navigate to Section A modules page', async ({ page }) => {
    await page.goto('/instructor/courses');
    await page.getByRole('link', { name: /Section A/i }).click();
    await expect(page).toHaveURL(/\/instructor\/courses\/\d+\/modules/);
});

test('instructor can navigate to assignment create form from modules page', async ({ page }) => {
    await page.goto('/instructor/courses');
    await page.getByRole('link', { name: /Section A/i }).click();
    await expect(page).toHaveURL(/\/instructor\/courses\/\d+\/modules/);

    // Switch to the Classwork tab to reveal the assignments section
    await page.getByRole('tab', { name: /classwork/i }).click();
    await expect(page.getByRole('link', { name: /add assignment/i })).toBeVisible();
    await page.getByRole('link', { name: /add assignment/i }).click();
    await expect(page).toHaveURL(/\/instructor\/courses\/\d+\/assignments\/create/);
});

test('instructor can create a new assignment', async ({ page }) => {
    // Get the section ID by navigating to modules page
    await page.goto('/instructor/courses');
    await page.getByRole('link', { name: /Section A/i }).click();
    await page.waitForURL(/\/instructor\/courses\/\d+\/modules/);
    const sectionId = page.url().match(/\/instructor\/courses\/(\d+)/)![1];

    await page.goto(`/instructor/courses/${sectionId}/assignments/create`);
    await expect(page.getByRole('heading', { name: /create assignment/i })).toBeVisible();

    await page.locator('#assignment-title').fill('E2E New Assignment');
    await page.locator('#assignment-max-score').fill('100');

    await page.getByRole('button', { name: /create assignment/i }).click();
    // Store redirects to the courses index on success
    await page.waitForURL(/\/instructor\/courses$/, { timeout: 15000 });
    await page.goto(`/instructor/courses/${sectionId}/assignments`);
    await expect(page.getByText('E2E New Assignment')).toBeVisible();
});
