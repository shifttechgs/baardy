<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Items from the client's final "BMC Website Design" feedback.
 */
class ClientFeedbackTest extends TestCase
{
    public function test_floating_whatsapp_button_is_on_every_page(): void
    {
        foreach ([route('home'), route('careers'), route('partners'), route('legal.privacy')] as $url) {
            $this->get($url)->assertOk()->assertSee('https://wa.me/263715351004?text=', false)->assertSee('Chat with us on WhatsApp');
        }
    }

    public function test_addresses_are_stated_as_the_client_gave_them_and_link_to_google_maps(): void
    {
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('4th Floor, Office 400, Construction House')
            ->assertSee('110 Leopold Takawira Street')
            ->assertSee('3rd Floor, Mership House')
            ->assertSee('Corner 9th Avenue & J. Nkomo Street')
            ->assertSee('https://www.google.com/maps/search/?api=1&amp;query=', false)
            ->assertSee('0242-777254')
            ->assertSee('0292-883657')
            ->assertSee('+263 775 399 113')
            ->assertSee('+263 715 351 013');
    }

    public function test_company_profile_and_target_market_are_on_the_about_page(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSee('proudly Zimbabwean-owned', false)
            ->assertSee('Public Service Commission')
            ->assertSee('SMEs');
    }
}
