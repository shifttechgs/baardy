<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProductionHardeningTest extends TestCase
{
    public function test_pages_carry_the_browser_protection_headers(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->assertNull($response->headers->get('Strict-Transport-Security'));
    }

    public function test_hsts_is_sent_over_https_only(): void
    {
        $this->get('https://localhost/')->assertHeader('Strict-Transport-Security');
    }

    public function test_a_missing_page_is_the_sites_own_404_with_a_way_back(): void
    {
        $this->get('/no-such-page')
            ->assertNotFound()
            ->assertSee('This page is not here')
            ->assertSee(route('home'), false)
            ->assertDontSee('Illuminate');
    }
}
