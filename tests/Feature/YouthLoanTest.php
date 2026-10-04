<?php

namespace Tests\Feature;

use App\Http\Requests\StoreEnquiryRequest;
use Tests\TestCase;

class YouthLoanTest extends TestCase
{
    public function test_youth_empowerment_loans_is_offered_across_the_site(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Youth Empowerment Loans')
            ->assertSee('Six loans, matched to', false);

        $this->assertContains('Youth Empowerment Loans', StoreEnquiryRequest::interests());
    }
}
