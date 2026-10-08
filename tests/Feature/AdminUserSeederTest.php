<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_admin_from_the_environment(): void
    {
        config(['app.admin.email' => 'owner@example.test', 'app.admin.password' => 'Sup3r-secret!']);

        $this->seed(AdminUserSeeder::class);
        $this->seed(AdminUserSeeder::class);

        $user = User::where('email', 'owner@example.test')->sole();
        $this->assertTrue($user->is_admin);
        $this->assertTrue(Hash::check('Sup3r-secret!', $user->password));
        $this->assertSame(1, User::count());
    }

    public function test_it_does_nothing_without_credentials(): void
    {
        config(['app.admin.email' => '', 'app.admin.password' => '']);

        $this->seed(AdminUserSeeder::class);

        $this->assertSame(0, User::count());
    }
}
