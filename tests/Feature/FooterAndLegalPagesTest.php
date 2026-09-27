<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Going live means nothing in the footer is dead: every link lands on a real
 * page or section, and unconfirmed details stay hidden rather than shown.
 */
class FooterAndLegalPagesTest extends TestCase
{
    public function test_every_legal_page_renders_with_its_title(): void
    {
        foreach (config('company.legal') as $page) {
            $this->get(route($page['route']))
                ->assertOk()
                ->assertSee('<h1', false)
                ->assertSee($page['label'])
                ->assertSee('Last updated');
        }
    }

    public function test_every_footer_link_resolves(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        foreach (config('company.footer') as $heading => $links) {
            foreach ($links as $link) {
                $this->assertNotSame('#', $link['href'], "Footer link \"{$link['label']}\" goes nowhere.");

                [$path, $anchor] = array_pad(explode('#', $link['href'], 2), 2, null);

                $response = $this->get($path === '' ? '/' : $path)->assertOk();

                if ($anchor) {
                    $response->assertSee('id="'.$anchor.'"', false);
                }
            }
        }
    }

    public function test_unconfirmed_details_stay_hidden(): void
    {
        config()->set('company.contact.email', 'hello@example.com');
        config()->set('company.social', [['label' => 'LinkedIn', 'href' => null, 'icon' => 'linkedin']]);

        $content = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('example.com', $content);
        $this->assertStringNotContainsString('on LinkedIn', $content);
    }

    public function test_confirmed_details_appear(): void
    {
        config()->set('company.contact.email', 'hello@baardy.co.zw');
        config()->set('company.social', [['label' => 'LinkedIn', 'href' => 'https://linkedin.com/company/baardy', 'icon' => 'linkedin']]);

        $this->get('/')
            ->assertOk()
            ->assertSee('mailto:hello@baardy.co.zw', false)
            ->assertSee('href="https://linkedin.com/company/baardy"', false);
    }

    public function test_the_enquiry_form_links_to_the_privacy_notice(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        $contact = substr($content, strpos($content, 'id="contact"'));

        $this->assertStringContainsString('href="'.route('legal.privacy').'"', $contact);
    }
}
