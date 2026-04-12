import { Page } from '@playwright/test';
import mysql from 'mysql2/promise';

const DB_CONFIG = {
  host: process.env.ELGG_DB_HOST || 'localhost',
  port: parseInt(process.env.ELGG_DB_PORT || '3307', 10),
  user: process.env.ELGG_DB_USER || 'elgg',
  password: process.env.ELGG_DB_PASS || 'elgg',
  database: process.env.ELGG_DB_NAME || 'elgg',
};

const TABLE_PREFIX = process.env.ELGG_DB_PREFIX || 'elgg_';

/**
 * Login to Elgg as the given user. Caller must know valid credentials.
 */
export async function loginAs(page: Page, username: string, password: string): Promise<void> {
  await page.goto('/login');
  await page.fill('input[name="username"]', username);
  await page.fill('input[name="password"]', password);
  await page.click('button[type="submit"], input[type="submit"]');
  await page.waitForURL((url) => !url.pathname.includes('/login'), { timeout: 10000 });
}

export async function logout(page: Page): Promise<void> {
  await page.goto('/logout');
}

export async function queryDb(sql: string, params: any[] = []): Promise<any[]> {
  const connection = await mysql.createConnection(DB_CONFIG);
  try {
    const [rows] = await connection.execute(sql, params);
    return rows as any[];
  } finally {
    await connection.end();
  }
}

export async function getEntity(guid: number): Promise<any | null> {
  const rows = await queryDb(
    `SELECT * FROM ${TABLE_PREFIX}entities WHERE guid = ?`,
    [guid]
  );
  return rows.length > 0 ? rows[0] : null;
}

export async function countEntitiesOfType(type: string): Promise<number> {
  const rows = await queryDb(
    `SELECT COUNT(*) AS c FROM ${TABLE_PREFIX}entities WHERE type = ?`,
    [type]
  );
  return rows.length > 0 ? Number((rows[0] as any).c) : 0;
}

export const TABLE = TABLE_PREFIX;
