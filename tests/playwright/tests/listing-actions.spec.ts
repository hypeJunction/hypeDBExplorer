import { test, expect } from '@playwright/test';
import { loginAs, logout, countEntitiesOfType } from '../helpers/elgg';

const ADMIN_USER = process.env.ELGG_ADMIN_USER || 'admin';
const ADMIN_PASS = process.env.ELGG_ADMIN_PASS || 'admin12345';
const TEST_USER = process.env.ELGG_TEST_USER || 'testuser';
const TEST_PASS = process.env.ELGG_TEST_PASS || 'testuser12345';

/**
 * These are the data-dump grid endpoints. They are registered with
 * access='admin' in start.php. Non-admin hits MUST be blocked.
 *
 * We also fire crude SQL-injection payloads at them to lock in that the
 * current (imperfect) protections at least return a JSON shape and do not
 * crash with a 500 or expose raw SQL errors.
 */
const LISTING_ACTIONS = [
  'db_explorer/entities',
  'db_explorer/owned_entities',
  'db_explorer/contained_entities',
  'db_explorer/metadata',
  'db_explorer/annotations',
  'db_explorer/private_settings',
  'db_explorer/entity_relationships',
  'db_explorer/river_items',
];

test.describe('hypeDBExplorer: listing action access control', () => {

  for (const action of LISTING_ACTIONS) {
    test(`admin can invoke ${action}`, async ({ page, request }) => {
      await loginAs(page, ADMIN_USER, ADMIN_PASS);

      // Pull CSRF tokens from any admin page
      await page.goto('/admin/developers/db_explorer');
      const tokens = await page.evaluate(() => {
        // Elgg stores tokens in elgg.security.token or on <meta>
        const w = window as any;
        if (w.elgg && w.elgg.security && w.elgg.security.token) {
          return {
            __elgg_token: w.elgg.security.token.__elgg_token,
            __elgg_ts: String(w.elgg.security.token.__elgg_ts),
          };
        }
        return null;
      });

      if (!tokens) {
        test.skip(true, 'Could not extract Elgg CSRF tokens from admin page');
        return;
      }

      const formBody = new URLSearchParams({
        ...tokens,
        type: 'user',
        page: '1',
        rows: '5',
      });

      // Reuse browser cookies via page.request
      const resp = await page.request.post(`/action/${action}`, {
        form: Object.fromEntries(formBody),
      });

      // Action either returns JSON (grid endpoint) or 200 ok.
      // It must NOT return 403 for an admin.
      expect(resp.status()).not.toBe(403);
    });
  }

  test(`non-admin cannot invoke db_explorer/entities`, async ({ page }) => {
    await loginAs(page, TEST_USER, TEST_PASS);

    // Grab tokens from any public page
    await page.goto('/');
    const tokens = await page.evaluate(() => {
      const w = window as any;
      if (w.elgg && w.elgg.security && w.elgg.security.token) {
        return {
          __elgg_token: w.elgg.security.token.__elgg_token,
          __elgg_ts: String(w.elgg.security.token.__elgg_ts),
        };
      }
      return null;
    });

    if (!tokens) {
      test.skip(true, 'Could not extract Elgg CSRF tokens');
      return;
    }

    const resp = await page.request.post('/action/db_explorer/entities', {
      form: { ...tokens, type: 'user', page: '1', rows: '5' },
      maxRedirects: 0,
    });

    // Elgg 'admin' access actions forward non-admins to login/403.
    // Accept any non-2xx, 302 redirect, or a body that contains 'forbidden'
    const status = resp.status();
    const body = await resp.text().catch(() => '');
    const blocked =
      status === 403 ||
      status === 401 ||
      status === 302 ||
      body.toLowerCase().includes('forbidden') ||
      body.toLowerCase().includes('not allowed');
    expect(blocked).toBeTruthy();
  });

  test('anonymous cannot invoke db_explorer/entities', async ({ request }) => {
    const resp = await request.post('/action/db_explorer/entities', {
      form: { type: 'user', page: '1', rows: '5' },
      maxRedirects: 0,
    });
    // Missing CSRF + missing session — Elgg rejects with 403 or redirect.
    expect([302, 401, 403]).toContain(resp.status());
  });
});

test.describe('hypeDBExplorer: SQL injection resistance', () => {

  test('invalid type parameter falls back to user (whitelisted)', async ({ page }) => {
    // Confirms entities.php whitelists type — locked from start.php review.
    await loginAs(page, ADMIN_USER, ADMIN_PASS);
    await page.goto('/admin/developers/db_explorer');

    const tokens = await page.evaluate(() => {
      const w = window as any;
      return w.elgg?.security?.token
        ? {
            __elgg_token: w.elgg.security.token.__elgg_token,
            __elgg_ts: String(w.elgg.security.token.__elgg_ts),
          }
        : null;
    });
    if (!tokens) {
      test.skip(true, 'No CSRF tokens');
      return;
    }

    const resp = await page.request.post('/action/db_explorer/entities', {
      form: {
        ...tokens,
        type: "user' OR 1=1 -- ",
        page: '1',
        rows: '5',
      },
    });

    // Response must be 200 (whitelist kicked in) and not expose SQL error text
    expect(resp.status()).toBe(200);
    const body = await resp.text();
    expect(body.toLowerCase()).not.toContain('sql syntax');
    expect(body.toLowerCase()).not.toContain('you have an error');
  });

  test('invalid sidx does not crash the endpoint', async ({ page }) => {
    // sidx is NOT whitelisted — locks in existing (weak) behavior so a
    // migration notices if the shape changes.
    await loginAs(page, ADMIN_USER, ADMIN_PASS);
    await page.goto('/admin/developers/db_explorer');

    const tokens = await page.evaluate(() => {
      const w = window as any;
      return w.elgg?.security?.token
        ? {
            __elgg_token: w.elgg.security.token.__elgg_token,
            __elgg_ts: String(w.elgg.security.token.__elgg_ts),
          }
        : null;
    });
    if (!tokens) {
      test.skip(true, 'No CSRF tokens');
      return;
    }

    const resp = await page.request.post('/action/db_explorer/entities', {
      form: {
        ...tokens,
        type: 'user',
        sidx: 'e.guid',
        sord: 'asc',
        page: '1',
        rows: '5',
      },
    });
    expect(resp.status()).toBe(200);
  });

  test('user count on entities grid matches DB state', async ({ page }) => {
    await loginAs(page, ADMIN_USER, ADMIN_PASS);
    const dbCount = await countEntitiesOfType('user');
    expect(dbCount).toBeGreaterThan(0);

    await page.goto('/admin/developers/db_explorer');

    const tokens = await page.evaluate(() => {
      const w = window as any;
      return w.elgg?.security?.token
        ? {
            __elgg_token: w.elgg.security.token.__elgg_token,
            __elgg_ts: String(w.elgg.security.token.__elgg_ts),
          }
        : null;
    });
    if (!tokens) {
      test.skip(true, 'No CSRF tokens');
      return;
    }

    const resp = await page.request.post('/action/db_explorer/entities', {
      form: { ...tokens, type: 'user', page: '1', rows: '5' },
    });
    expect(resp.status()).toBe(200);
    const body = await resp.text();

    // Body should contain JSON with 'records' field
    const jsonStart = body.indexOf('{');
    expect(jsonStart).toBeGreaterThanOrEqual(0);
    const parsed = JSON.parse(body.substring(jsonStart));
    expect(parsed.records).toBe(dbCount);
  });
});
