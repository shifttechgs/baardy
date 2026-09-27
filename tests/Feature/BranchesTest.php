<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Baardy operates from two offices. Both must be offered to a visitor, and
 * the configured numbers must stay dialable.
 *
 * Branch addresses and the per-branch phone list are no longer printed on
 * the page -- removed at the client's request, first from the footer and
 * then from "Get in touch" -- so these tests no longer look for them there.
 */
class BranchesTest extends TestCase
{
    public function test_both_branches_are_configured(): void
    {
        $branches = config('company.branches');

        $this->assertCount(2, $branches);
        $this->assertSame(['Harare', 'Bulawayo'], array_column($branches, 'name'));
        $this->assertSame('Head office', $branches[0]['role']);
    }

    /**
     * Both branches remain a choice in the enquiry form's "Nearest branch".
     */
    public function test_the_enquiry_form_offers_both_branches(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        foreach (config('company.branches') as $branch) {
            $this->assertMatchesRegularExpression(
                '/name="branch"\s+value="'.preg_quote($branch['name'], '/').'"/',
                $content
            );
        }
    }

    /**
     * A tel: href must be dialable from any network, so it carries the full
     * international prefix and no spaces or punctuation. The displayed form is
     * free to differ - Bulawayo's landline is printed locally as (0)29 ...
     * The WhatsApp link in "Get in touch" is built from one of these.
     */
    public function test_branch_phone_numbers_are_dialable(): void
    {
        foreach (config('company.branches') as $branch) {
            foreach ($branch['phones'] as $phone) {
                $this->assertMatchesRegularExpression(
                    '/^\+263\d{9}$/',
                    $phone['tel'],
                    "Branch phone {$phone['tel']} is not a full international number."
                );
            }
        }
    }
}
