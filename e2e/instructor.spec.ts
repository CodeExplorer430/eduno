import { test, expect } from '@playwright/test';
import { loginAs } from './fixtures/auth';

test.beforeEach(async ({ page }) => {
    await loginAs(page, 'instructor@eduno.test', 'password');
});

test('dashboard has My Sections heading', async ({ page }) => {
    await expect(page.getByRole('heading', { name: 'My Sections' })).toBeVisible();
});

test('dashboard has Pending Submissions heading', async ({ page }) => {
    await expect(page.getByRole('heading', { name: 'Pending Submissions' })).toBeVisible();
});

test('E2E Test Course is visible in sections list', async ({ page }) => {
    await expect(page.getByText('E2E Test Course')).toBeVisible();
});
