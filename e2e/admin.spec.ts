import { test, expect } from '@playwright/test';
import { loginAs } from './fixtures/auth';

test.beforeEach(async ({ page }) => {
    await loginAs(page, 'admin@eduno.test', 'password');
});

test('dashboard contains total courses stat', async ({ page }) => {
    await expect(page.getByText(/total courses/i)).toBeVisible();
});

test('View full report link navigates to /admin/reports', async ({ page }) => {
    await page.getByRole('link', { name: 'View full report →' }).click();
    await expect(page).toHaveURL(/\/admin\/reports/);
});

test('reports page shows all six stat labels', async ({ page }) => {
    await page.getByRole('link', { name: 'View full report →' }).click();
    await expect(page).toHaveURL(/\/admin\/reports/);

    const labels = [
        'Total Courses',
        'Total Sections',
        'Total Students',
        'Total Submissions',
        'Late Submissions',
        'Graded Submissions',
    ];

    for (const label of labels) {
        await expect(page.getByText(label)).toBeVisible();
    }
});
