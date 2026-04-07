import { test, expect } from '@playwright/test';
import { loginAs } from './fixtures/auth';

test.beforeEach(async ({ page }) => {
    await loginAs(page, 'admin@eduno.test', 'password');
});

test('admin can download audit log as CSV', async ({ page }) => {
    await page.goto('/admin/audit-logs');

    const [download] = await Promise.all([
        page.waitForEvent('download'),
        page.getByRole('link', { name: /export/i }).click(),
    ]);

    expect(download.suggestedFilename()).toMatch(/\.csv$/i);
});

test('admin can download submission report as XLSX', async ({ page }) => {
    await page.goto('/admin/reports');

    await page.selectOption('#export-format', 'xlsx');

    const [download] = await Promise.all([
        page.waitForEvent('download'),
        page.getByRole('link', { name: /export/i }).click(),
    ]);

    expect(download.suggestedFilename()).toMatch(/\.xlsx$/i);
});

test('admin can download audit log as PDF', async ({ page }) => {
    await page.goto('/admin/audit-logs');

    await page.selectOption('#audit-export-format', 'pdf');

    const [download] = await Promise.all([
        page.waitForEvent('download'),
        page.getByRole('link', { name: /export/i }).click(),
    ]);

    expect(download.suggestedFilename()).toMatch(/\.pdf$/i);
});
