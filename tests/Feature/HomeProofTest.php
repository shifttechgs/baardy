<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * The verifiable facts -- licence, years on the register, offices -- now live
 * in the section under the hero (sections/home/trust). Its whole
 * premise is that a reader can check every claim, so these tests guard that
 * premise rather than the layout.
 */
class HomeProofTest extends TestCase
{
    public function test_it_shows_the_three_verifiable_figures(): void
    {
        $compliance = config('company.compliance');

        $this->get('/')
            ->assertOk()
            ->assertSee($compliance['licence'])
            ->assertSee((string) (now()->year - (int) $compliance['licensed_since']))
            ->assertSee((string) count(config('company.branches')));
    }

    /**
     * The figures must come from the same config the footer and hero read, so
     * the licence number cannot say 658 in one place and something else here.
     */
    public function test_its_figures_agree_with_the_rest_of_the_site(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        $licence = config('company.compliance.licence');

        $this->assertGreaterThanOrEqual(
            2,
            substr_count($content, $licence),
            'The licence number should appear in the proof section and the footer.'
        );
    }

    public function test_it_links_out_to_the_public_register(): void
    {
        $url = config('company.compliance.register_url');

        $this->assertNotEmpty($url, 'A verify link is the point of this section.');

        $this->get('/')
            ->assertOk()
            ->assertSee($url, false)
            ->assertSee('rel="noopener noreferrer"', false);
    }

    /**
     * The placeholder testimonials are what this section replaced. If they
     * come back onto the page while still unattributed, that is a regression.
     */
    public function test_the_placeholder_testimonials_are_not_on_the_page(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('[Customer name]', $content);
        $this->assertStringNotContainsString('These are not real customers', $content);
    }

    /**
     * The section under the hero replaced a strip of placeholder aggregates. It
     * must carry only computed, checkable facts -- none of the old invented
     * figures, and so no need for an "illustrative" disclaimer.
     */
    public function test_the_standing_section_carries_no_placeholder_figures(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(config('marketing.trust.heading'))
            ->assertSee('data-count-to="'.count(config('marketing.products')).'"', false)
            ->assertDontSee('12,400')
            ->assertDontSee('Figures are illustrative placeholders');
    }
}
