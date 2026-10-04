<?php

namespace Tests\Feature;

use Tests\TestCase;

class AboutPageTest extends TestCase
{
    public function test_about_page_carries_the_company_profile_and_offices(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSee('proudly Zimbabwean-owned', false)
            ->assertSee('Public Service Commission');
    }

    public function test_homepage_no_longer_has_the_who_we_are_section(): void
    {
        $this->get(route('home'))->assertOk()->assertDontSee('id="about"', false)->assertDontSee('proudly Zimbabwean-owned', false);
    }

    public function test_menus_link_to_the_about_page(): void
    {
        $this->get(route('home'))->assertOk()->assertSee('href="/about"', false);
    }
}
