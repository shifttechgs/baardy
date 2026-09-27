<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * The closing call to action is an application slip: two answers, then on to
 * the enquiry form, which arrives with both answers already chosen.
 */
class HomeApplicationSlipTest extends TestCase
{
    public function test_the_slip_is_a_get_form_to_the_enquiry_form(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '/<form\s+method="GET"\s+action="'.preg_quote(route('home').'#contact', '/').'"\s+x-data="applicationSlip\(/',
            $content
        );
    }

    public function test_the_slip_is_on_the_insights_page_too(): void
    {
        $this->get(route('insights.index'))
            ->assertOk()
            ->assertSee('x-data="applicationSlip(', false);
    }

    public function test_the_enquiry_form_preselects_the_answers_it_is_sent(): void
    {
        $content = $this->get('/?interest=SME%20Bridging%20Finance&branch=Bulawayo')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<option value="SME Bridging Finance"\s+selected/', $content);
        $this->assertMatchesRegularExpression('/name="branch"\s+value="Bulawayo"\s+class="sr-only"\s+checked/', $content);
    }

    public function test_the_enquiry_form_ignores_answers_it_does_not_offer(): void
    {
        $content = $this->get('/?interest=Bogus&branch=Mars')->assertOk()->getContent();

        $this->assertStringNotContainsString('value="Bogus"', $content);
        $this->assertMatchesRegularExpression('/<option value="" disabled\s+selected>Choose one/', $content);
        $this->assertMatchesRegularExpression('/name="branch"\s+value="Harare"\s+class="sr-only"\s+checked/', $content);
    }
}
