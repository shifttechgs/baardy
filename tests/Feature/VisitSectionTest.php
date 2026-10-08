<?php

namespace Tests\Feature;

use Tests\TestCase;

class VisitSectionTest extends TestCase
{
    public function test_homepage_shows_each_branch_s_full_address_with_directions(): void
    {
        $response = $this->get(route('contact'))->assertOk();

        foreach (config('company.branches') as $branch) {
            foreach ($branch['address'] as $line) {
                $response->assertSee($line);
            }
        }

        $response->assertSee('Get directions')->assertSee('id="visit"', false);
    }

    public function test_each_branch_shows_its_own_numbers(): void
    {
        $response = $this->get(route('contact'))->assertOk();

        foreach (config('company.branches') as $branch) {
            foreach ($branch['phones'] as $phone) {
                $response->assertSee('tel:'.$phone['tel'], false);
            }
        }

        $response->assertSee('https://wa.me/263715351004', false);
    }

    public function test_customer_stories_publish_only_with_consent(): void
    {
        config(['marketing.testimonials_preview' => false, 'marketing.customer_stories' => [], 'marketing.testimonials' => []]);

        $this->get(route('home'))->assertOk()->assertDontSee('id="testimonials"', false);

        config(['marketing.customer_stories' => [['story' => 'Real words.', 'name' => 'Tendai M.', 'role' => 'Tailor, Bulawayo', 'consented' => true]]]);

        $this->get(route('home'))->assertOk()->assertSee('id="testimonials"', false)->assertSee('Real words.');
    }
}
