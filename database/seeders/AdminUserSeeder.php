<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * The first admin-panel user, taken from the environment so the password is
 * never committed:
 *
 *   ADMIN_EMAIL=...   ADMIN_PASSWORD=...   ADMIN_NAME="..." (optional)
 *
 * Run once with `php artisan db:seed --class=AdminUserSeeder --force`. Safe to
 * run again: the user is matched on email, and the password is reset to the
 * one in the environment.
 */
class AdminUserSeeder extends Seeder
{
    /**
     * Seed the admin user.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (blank($email) || blank($password)) {
            $this->command?->error('Set ADMIN_EMAIL and ADMIN_PASSWORD in .env first.');

            return;
        }

        $user = User::firstOrNew(['email' => $email]);
        $user->name = env('ADMIN_NAME', 'Administrator');
        $user->password = $password;
        $user->email_verified_at ??= now();
        $user->is_admin = true;
        $user->can_approve_promotions = true;
        $user->save();
    }
}
