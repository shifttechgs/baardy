<?php

namespace Tests\Feature;

use App\Http\Requests\StoreEnquiryRequest;
use Tests\TestCase;

/**
 * The "Get in touch" section. Every "Start an application" button on the
 * site points at #contact, so #contact has to be a real form -- it used to
 * be the closing panel, whose own button linked back to itself.
 */
class HomeContactTest extends TestCase
{
    public function test_the_primary_call_to_action_lands_on_the_enquiry_form(): void
    {
        $this->assertSame('/#contact', config('company.cta.primary.href'));

        $content = $this->get('/')->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '/id="contact".*?<form[^>]*action="'.preg_quote(route('enquiries.store'), '/').'"/s',
            $content,
            'The #contact section must contain the enquiry form.'
        );
    }

    public function test_the_form_offers_every_product_and_branch(): void
    {
        $response = $this->get('/')->assertOk();

        foreach (StoreEnquiryRequest::interests() as $interest) {
            $response->assertSee('value="'.$interest.'"', false);
        }

        foreach (StoreEnquiryRequest::branches() as $branch) {
            $this->assertMatchesRegularExpression(
                '/name="branch"\s+value="'.preg_quote($branch, '/').'"/',
                $response->getContent()
            );
        }
    }

    public function test_it_links_to_whatsapp(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('href="https://wa.me/263715351004"', false);
    }
}
