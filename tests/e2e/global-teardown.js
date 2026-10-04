/*
 * After the suite: remove the throwaway database and the copied picture.
 */
import fs from 'node:fs';
import path from 'node:path';
import { database, root } from './env.js';

export default async function globalTeardown() {
    fs.rmSync(database, { force: true });
    fs.rmSync(path.join(root, 'storage', 'app', 'public', 'e2e'), { recursive: true, force: true });
}
