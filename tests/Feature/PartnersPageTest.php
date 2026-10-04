<?php

namespace Tests\Feature;

use Tests\TestCase;

class PartnersPageTest extends TestCase
{
    public function test_partners_page_loads_with_a_way_to_get_in_touch(): void
    {
        $this->get(route('partners'))
            ->assertOk()
            ->assertSee('Better borrowing, together')
            ->assertSee('Start a conversation');
    }

    public function test_header_links_to_partners(): void
    {
        $this->get(route('home'))->assertOk()->assertSee(route('partners'), false);
    }
}
