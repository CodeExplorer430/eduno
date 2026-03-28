import { test, expect } from '@playwright/test';
import { loginAs } from './fixtures/auth';

test.beforeEach(async ({ page }) => {
    await loginAs(page, 'student@eduno.test', 'password');
});

test('student grades page shows released grade in the table', async ({ page }) => {
    await page.goto('/student/grades');
    // The desktop table (hidden sm:block) contains the grade row
    await expect(page.getByRole('table', { name: 'My grades' })).toBeVisible();
    await expect(page.getByRole('cell', { name: 'E2E Grading Assignment' })).toBeVisible();
});

test('student grades page shows score of 42', async ({ page }) => {
    await page.goto('/student/grades');
    // Score cell is labelled "Score: 42.00 out of 50"
    await expect(page.getByLabel('Score: 42.00 out of 50')).toBeVisible();
});

test('student grades page shows course name in the table', async ({ page }) => {
    await page.goto('/student/grades');
    await expect(page.getByRole('cell', { name: /E2E Test Course/ })).toBeVisible();
});
