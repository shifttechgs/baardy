<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CanonicalRedirectTest extends TestCase
{
    use RefreshDatabase;

    private function inProduction(): void
    {
        $this->app['env'] = 'production';
        config(['app.url' => 'https://www.example.test']);
    }

    public function test_another_host_is_sent_to_the_canonical_address(): void
    {
        $this->inProduction();

        $this->get('https://example.test/contact?x=1')
            ->assertStatus(301)
            ->assertRedirect('https://www.example.test/contact?x=1');
    }

    public function test_plain_http_is_sent_to_https(): void
    {
        $this->inProduction();

        $this->get('http://www.example.test/admin/login')
            ->assertStatus(301)
            ->assertRedirect('https://www.example.test/admin/login');
    }

    public function test_the_canonical_address_is_served(): void
    {
        $this->inProduction();

        $this->get('https://www.example.test/contact')->assertOk();
    }

    public function test_a_proxied_request_is_not_redirected_in_a_loop(): void
    {
        $this->inProduction();

        $this->get('http://www.example.test/contact', ['X-Forwarded-Proto' => 'https'])->assertOk();
    }

    public function test_nothing_is_redirected_outside_production(): void
    {
        config(['app.url' => 'https://www.example.test']);

        $this->get('http://localhost/contact')->assertOk();
    }
}
