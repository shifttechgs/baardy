<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * No eligibility criteria have been verified for this business, so the
 * homepage must never tell a visitor they qualify, are approved, or are
 * rejected. The eligibility section and its "who we lend to" list are off the
 * page; these tests keep the guard rail and make sure nothing still points
 * at the anchor they used to own.
 */
class HomeEligibilityTest extends TestCase
{
    public function test_nothing_links_to_the_removed_eligibility_anchor(): void
    {
        $this->assertNotContains('/#eligibility', array_column(config('company.nav'), 'href'));

        $content = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('#eligibility"', $content);
    }

    /**
     * If someone adds decision wording later, this fails and they have to
     * think about it first.
     */
    public function test_it_never_states_a_credit_decision(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        $forbidden = [
            'you qualify',
            'you do not qualify',
            'you are eligible',
            'pre-approved',
            'pre-qualified',
            'guaranteed approval',
            'instant approval',
            'no credit check',
        ];

        foreach ($forbidden as $phrase) {
            $this->assertStringNotContainsStringIgnoringCase(
                $phrase,
                $content,
                "The page must not claim a credit decision, but it contains \"{$phrase}\"."
            );
        }
    }
}
