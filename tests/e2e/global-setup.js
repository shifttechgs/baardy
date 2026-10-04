/*
 * Before the suite: a fresh throwaway database, seeded with the fixed world
 * the specs expect, and the staff password generated for this run only.
 */
import { spawnSync } from 'node:child_process';
import crypto from 'node:crypto';
import fs from 'node:fs';
import path from 'node:path';
import { appEnv, database, root } from './env.js';

function artisan(args, env) {
    const result = spawnSync('php', ['artisan', ...args, '--no-interaction'], { cwd: root, env: { ...process.env, ...env }, encoding: 'utf8' });

    if (result.status !== 0) {
        throw new Error(`php artisan ${args.join(' ')} failed:\n${result.stdout}\n${result.stderr}`);
    }
}

export default async function globalSetup() {
    // A password for this run only. Workers inherit it; nothing is written down.
    const password = `E2e-${crypto.randomBytes(12).toString('base64url')}`;
    process.env.E2E_PASSWORD = password;

    fs.rmSync(database, { force: true });
    fs.writeFileSync(database, '');

    // The promotion picture lives in shared storage; copy a repo image in for the run.
    const target = path.join(root, 'storage', 'app', 'public', 'e2e');
    fs.mkdirSync(target, { recursive: true });
    fs.copyFileSync(path.join(root, 'public', 'images', 'products', 'salary-1600.webp'), path.join(target, 'promo.webp'));

    const env = { ...appEnv, E2E_PASSWORD: password };
    artisan(['migrate:fresh', '--force'], env);
    artisan(['db:seed', '--class=E2eSeeder', '--force'], env);
}
