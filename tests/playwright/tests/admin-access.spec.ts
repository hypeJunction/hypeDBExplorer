import { test, expect } from '@playwright/test';
import { loginAs, logout } from '../helpers/elgg';

const ADMIN_USER = process.env.ELGG_ADMIN_USER || 'admin';
const ADMIN_PASS = process.env.ELGG_ADMIN_PASS || 'admin12345';
const TEST_USER = process.env.ELGG_TEST_USER || 'testuser';
const TEST_PASS = process.env.ELGG_TEST_PASS || 'testuser12345';

/**
 * Admin page access enforcement.
 *
 * The DB explorer is a sensitive admin-only tool. It must:
 *   1. Render cleanly for an admin
 *   2. Block anonymous access
 *   3. Block non-admin logged-in users
 */
test.describe('hypeDBExplorer: admin-only access', () => {

  test('admin can render the DB explorer admin page', async ({ page }) => {
    await loginAs(page, ADMIN_USER, ADMIN_PASS);
    const resp = await page.goto('/admin/developers/db_explorer');
    expect(resp?.ok()).toBeTruthy();

    // Page should render without system-error messages
    const errors = page.locator('.elgg-system-messages .elgg-message-error');
    await expect(errors).toHaveCount(0);

    // Page should render Elgg admin chrome
    await expect(page.locator('body')).toBeVisible();
  });

  test('anonymous user cannot access DB explorer admin page', async ({ page }) => {
    await logout(page).catch(() => {});
    const resp = await page.goto('/admin/developers/db_explorer');

    // Elgg admin gating redirects to /login or returns 403
    const url = page.url();
    const status = resp?.status() ?? 0;
    expect(
      url.includes('/login') || status === 403 || status === 401
    ).toBeTruthy();
  });

  test('non-admin logged-in user cannot access DB explorer admin page', async ({ page }) => {
    await loginAs(page, TEST_USER, TEST_PASS);
    const resp = await page.goto('/admin/developers/db_explorer');

    const url = page.url();
    const status = resp?.status() ?? 0;
    // Non-admin on /admin/* either gets bounced home or 403
    expect(
      !url.includes('/admin/developers/db_explorer') || status === 403
    ).toBeTruthy();
  });
});
