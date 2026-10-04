<?php

namespace Tests\Feature;

use Tests\TestCase;

class ContactPageTest extends TestCase
{
    public function test_the_contact_page_has_the_offices_the_map_and_the_enquiry_form(): void
    {
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('id="visit"', false)
            ->assertSee('Mership House')
            ->assertSee('google.com/maps?q=', false)
            ->assertSee('id="contact"', false)
            ->assertSee('action="'.route('enquiries.store').'"', false);
    }

    public function test_the_landing_page_no_longer_carries_the_offices_or_the_form(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('id="visit"', false)
            ->assertDontSee('id="contact"', false)
            ->assertDontSee('action="'.route('enquiries.store').'"', false);
    }

    public function test_every_start_an_application_button_lands_on_the_contact_page(): void
    {
        $this->assertSame('/contact', config('company.cta.primary.href'));

        $this->get(route('home'))->assertOk()->assertSee('href="/contact"', false);
    }

    public function test_a_branch_in_the_link_is_preselected_in_the_form(): void
    {
        $this->get(route('contact', ['branch' => 'Bulawayo']))
            ->assertOk()
            ->assertSeeInOrder(['name="branch"', 'value="Bulawayo"', 'checked'], false);
    }

    public function test_the_landing_page_has_a_short_talk_to_a_person_section(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('id="talk"', false)
            ->assertSee('Talk to a person')
            ->assertSee('href="'.route('contact').'"', false)
            ->assertSee('tel:+263242777254', false)
            ->assertSee('https://wa.me/263715351004', false);
    }
}
