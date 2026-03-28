import { test, expect } from '@playwright/test';
import { loginAs } from './fixtures/auth';

test.beforeEach(async ({ page }) => {
    await loginAs(page, 'instructor@eduno.test', 'password');
});

test('instructor courses page shows E2E Test Course', async ({ page }) => {
    await page.goto('/instructor/courses');
    await expect(page.getByText('E2E Test Course')).toBeVisible();
});

test('instructor can navigate to course create form', async ({ page }) => {
    await page.goto('/instructor/courses');
    await page.getByRole('link', { name: /new course|create course/i }).first().click();
    await expect(page).toHaveURL(/\/instructor\/courses\/create/);
});

test('instructor create course form renders heading', async ({ page }) => {
    await page.goto('/instructor/courses/create');
    await expect(page.getByRole('heading', { name: /create course/i })).toBeVisible();
});

test('instructor can create a new course', async ({ page }) => {
    await page.goto('/instructor/courses/create');

    await page.locator('#course-code').fill('E2ENEW');
    await page.locator('#course-title').fill('E2E Created Course');
    await page.locator('#course-department').fill('CCS');
    await page.locator('#course-term').fill('2nd Semester');
    await page.locator('#course-academic-year').fill('2025-2026');

    await page.getByRole('button', { name: /create|save/i }).click();
    await expect(page).toHaveURL(/\/instructor\/courses/);
    await expect(page.getByText('E2E Created Course')).toBeVisible();
});
