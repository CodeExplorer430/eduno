import { test, expect } from '@playwright/test';
import { loginAs } from './fixtures/auth';

test.beforeEach(async ({ page }) => {
    await loginAs(page, 'student@eduno.test', 'password');
});

test('student courses page has My Courses heading', async ({ page }) => {
    await page.goto('/student/courses');
    await expect(page.getByRole('heading', { name: 'My Courses' })).toBeVisible();
});

test('student sees E2E Test Course in courses list', async ({ page }) => {
    await page.goto('/student/courses');
    await expect(page.getByText('E2E Test Course')).toBeVisible();
});

test('student can navigate to course detail page', async ({ page }) => {
    await page.goto('/student/courses');
    await page.getByRole('link', { name: /E2E Test Course/i }).click();
    await expect(page).toHaveURL(/\/student\/courses\/\d+/);
});
