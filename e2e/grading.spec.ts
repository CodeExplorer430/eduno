import { test, expect } from '@playwright/test';
import { loginAs } from './fixtures/auth';

test.beforeEach(async ({ page }) => {
    await loginAs(page, 'instructor@eduno.test', 'password');
});

test('instructor sees pre-seeded submission in all-submissions list', async ({ page }) => {
    await page.goto('/instructor/submissions');
    await expect(page.getByText('E2E Student')).toBeVisible();
});

test('instructor can open submission detail and see grade form', async ({ page }) => {
    await page.goto('/instructor/submissions');
    await page.getByRole('link', { name: /view|grade|E2E Grading Assignment/i }).first().click();
    await expect(page).toHaveURL(/\/instructor\/submissions\/\d+/);
    await expect(page.getByRole('heading', { name: 'Grading', exact: true })).toBeVisible();
});

test('instructor can save a grade for the pre-seeded submission', async ({ page }) => {
    await page.goto('/instructor/submissions');
    await page.getByRole('link', { name: /view|grade/i }).first().click();

    await page.locator('#grade-score').fill('42');
    await page.locator('#grade-feedback').fill('E2E grading test feedback.');
    await page.getByRole('button', { name: /save grade|update grade/i }).click();

    await expect(page.getByText(/grade saved successfully/i)).toBeVisible();
});
