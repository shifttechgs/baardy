/*
 * The environment the end-to-end suite runs the application in.
 *
 * It is a SEPARATE server on its own port, with its own throwaway SQLite
 * database, set up like production: debug off, mail written to the log, no
 * dev tooling. Real environment variables win over .env, so nothing here
 * touches the development database, sessions or mail settings.
 */
import path from 'node:path';
import { fileURLToPath } from 'node:url';

export const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..', '..');

export const port = Number(process.env.E2E_PORT ?? 8001);
export const baseURL = `http://127.0.0.1:${port}`;
export const database = path.join(root, 'database', 'e2e.sqlite');

export const appEnv = {
    APP_ENV: 'production',
    APP_DEBUG: 'false',
    APP_URL: baseURL,
    DB_CONNECTION: 'sqlite',
    DB_DATABASE: database,
    SESSION_DRIVER: 'database',
    CACHE_STORE: 'database',
    QUEUE_CONNECTION: 'sync',
    MAIL_MAILER: 'log',
    LOG_LEVEL: 'debug',
};

/** The staff accounts the seeder creates (database/seeders/E2eSeeder.php). */
export const accounts = {
    admin: 'e2e-admin@example.test',
    staff: 'e2e-user@example.test',
};

export const promotion = { slug: 'e2e-back-to-school', code: 'E2ETEST', title: 'E2E Back to School' };
