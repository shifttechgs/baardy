<?php

namespace Tests\Feature;

use App\Filament\Pages\Management;
use App\Filament\Pages\Proposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagementPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_management_page_lists_the_modules_for_admins(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(Management::getUrl())
            ->assertOk()
            ->assertSee('Management')
            ->assertSee('Clients')
            ->assertSee('Loans')
            ->assertSee('Collections')
            ->assertSee('Who do we need to speak to today?')
            ->assertSee('30-day free trial');
    }

    public function test_the_makers_credit_appears_once_in_the_sidebar_and_nowhere_else(): void
    {
        $admin = User::factory()->admin()->create();

        foreach ([Management::getUrl(), Proposal::getUrl()] as $url) {
            $html = (string) $this->actingAs($admin)->get($url)->assertOk()->getContent();

            $this->assertSame(1, substr_count($html, 'aria-label="Powered by ShiftTech"'), "{$url} should carry the credit exactly once");
            $this->assertStringContainsString('https://shifttechgs.com', $html);
            $this->assertStringNotContainsString('Built on Partnership', $html);
        }

        auth()->logout();

        $this->get('/admin/login')
            ->assertOk()
            ->assertDontSee('Powered by ShiftTech', false);
    }

    public function test_it_is_grouped_under_a_management_menu(): void
    {
        $this->assertSame('Management', Management::getNavigationGroup());
    }

    public function test_it_is_closed_to_anyone_who_is_not_an_admin(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(Management::getUrl())
            ->assertForbidden();

        auth()->logout();

        $this->get(Management::getUrl())->assertRedirect();
    }
}
