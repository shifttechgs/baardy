<?php

namespace Tests\Feature;

use App\LeadActivityType;
use App\LeadStage;
use App\Mail\EnquiryReceived;
use App\Models\Lead;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\TestWith;
use RuntimeException;
use Tests\TestCase;

/**
 * The "Get in touch" form. An enquiry must be kept as a lead and reach the
 * team's inbox, bad input must come back with a message a person can act
 * on, and bots must get nothing through.
 */
class EnquiryControllerTest extends TestCase
{
    /**
     * @return array<string, string>
     */
    private function validEnquiry(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Tendai Moyo',
            'phone' => '+263 77 123 4567',
            'email' => 'tendai@example.com',
            'interest' => 'Agricultural Loans',
            'branch' => 'Bulawayo',
            'message' => 'Inputs for the coming season.',
        ], $overrides);
    }

    public function test_a_valid_enquiry_is_mailed_to_the_enquiries_inbox(): void
    {
        Mail::fake();
        config(['company.enquiries.to' => 'loans@baardy.test']);

        $this->post(route('enquiries.store'), $this->validEnquiry())
            ->assertRedirect(route('contact').'#contact')
            ->assertSessionHas('enquiry_sent');

        Mail::assertSent(EnquiryReceived::class, function (EnquiryReceived $mail): bool {
            return $mail->hasTo('loans@baardy.test')
                && $mail->enquiry['name'] === 'Tendai Moyo'
                && $mail->enquiry['interest'] === 'Agricultural Loans'
                && $mail->enquiry['branch'] === 'Bulawayo';
        });
    }

    public function test_a_json_submission_returns_the_confirmation_message(): void
    {
        Mail::fake();

        $response = $this->postJson(route('enquiries.store'), $this->validEnquiry())
            ->assertOk()
            ->assertJson(['message' => 'Thank you. We have your details and will call or WhatsApp you shortly.']);

        $this->assertSame(Lead::sole()->reference, $response->json('reference'));
        Mail::assertSent(EnquiryReceived::class);
    }

    public function test_an_enquiry_is_stored_as_a_new_lead_before_it_is_mailed(): void
    {
        Mail::fake();

        $this->postJson(route('enquiries.store'), $this->validEnquiry())->assertOk();

        $lead = Lead::sole();
        $this->assertSame('Tendai Moyo', $lead->name);
        $this->assertSame('263771234567', $lead->phone_normalized);
        $this->assertSame(LeadStage::New, $lead->stage);
        $this->assertMatchesRegularExpression('/^BMC-[A-Z2-9]{6}$/', $lead->reference);
        $this->assertSame(LeadActivityType::Enquired, $lead->activities()->sole()->type);

        Mail::assertSent(EnquiryReceived::class, fn (EnquiryReceived $mail): bool => $mail->lead->is($lead));
    }

    /**
     * The same person enquiring twice -- even with the number written
     * differently -- is one lead with two entries, not two leads.
     */
    public function test_a_repeat_enquiry_from_the_same_number_joins_the_open_lead(): void
    {
        Mail::fake();

        $this->postJson(route('enquiries.store'), $this->validEnquiry())->assertOk();
        $this->postJson(route('enquiries.store'), $this->validEnquiry(['phone' => '077 123 4567', 'message' => 'Any news?']))->assertOk();

        $lead = Lead::sole();
        $this->assertSame(2, $lead->activities()->count());
        $this->assertSame(LeadActivityType::EnquiredAgain, $lead->activities()->first()->type);
        $this->assertStringContainsString('Any news?', $lead->activities()->first()->body);
    }

    public function test_an_enquiry_after_a_lead_was_closed_opens_a_new_lead(): void
    {
        Mail::fake();
        Lead::factory()->lost()->create(['phone' => '+263 77 123 4567']);

        $this->postJson(route('enquiries.store'), $this->validEnquiry())->assertOk();

        $this->assertSame(2, Lead::count());
    }

    public function test_the_lead_is_kept_when_the_email_cannot_be_sent(): void
    {
        Mail::shouldReceive('to')->andThrow(new RuntimeException('Mail server down'));

        $this->postJson(route('enquiries.store'), $this->validEnquiry())
            ->assertOk()
            ->assertJsonStructure(['message', 'reference']);

        $this->assertSame(1, Lead::count());
    }

    public function test_the_lead_records_the_campaign_and_site_that_sent_the_visitor(): void
    {
        Mail::fake();

        $this->withHeader('referer', 'https://www.facebook.com/')
            ->get('/?utm_source=facebook&utm_medium=social&utm_campaign=harvest')
            ->assertOk();

        $this->postJson(route('enquiries.store'), $this->validEnquiry())->assertOk();

        $lead = Lead::sole();
        $this->assertSame('facebook', $lead->utm_source);
        $this->assertSame('harvest', $lead->utm_campaign);
        $this->assertSame('https://www.facebook.com/', $lead->referrer);
        $this->assertStringStartsWith('/?utm_source=facebook', $lead->landing_page);
        $this->assertSame('Campaign: harvest (facebook)', $lead->sourceLabel());
    }

    public function test_email_and_message_are_optional(): void
    {
        Mail::fake();

        $this->postJson(route('enquiries.store'), $this->validEnquiry(['email' => null, 'message' => null]))
            ->assertOk();

        Mail::assertSent(EnquiryReceived::class);
    }

    public function test_an_empty_submission_is_rejected_with_messages_a_visitor_can_act_on(): void
    {
        Mail::fake();

        $this->postJson(route('enquiries.store'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name' => 'Tell us your name so we know who to ask for.',
                'phone' => 'We need a number to call or WhatsApp you back on.',
                'interest' => 'Choose what you would like to talk about.',
                'branch' => 'Choose the branch nearest to you.',
            ]);

        Mail::assertNothingSent();
    }

    #[TestWith(['call me'])]
    #[TestWith(['12'])]
    public function test_a_phone_number_that_is_not_a_number_is_rejected(string $phone): void
    {
        $this->postJson(route('enquiries.store'), $this->validEnquiry(['phone' => $phone]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['phone' => 'That does not look like a phone number.']);
    }

    /**
     * The form's choices are built from the product list; anything outside
     * that list did not come from the form.
     */
    public function test_an_interest_that_is_not_offered_is_rejected(): void
    {
        $this->postJson(route('enquiries.store'), $this->validEnquiry(['interest' => 'Emergency credit']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('interest');
    }

    public function test_a_branch_that_does_not_exist_is_rejected(): void
    {
        $this->postJson(route('enquiries.store'), $this->validEnquiry(['branch' => 'Mutare']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('branch');
    }

    /**
     * A bot that fills the hidden field gets the normal success response, so
     * it learns nothing, but no lead is stored and no mail is sent.
     */
    public function test_a_filled_honeypot_is_accepted_but_not_mailed(): void
    {
        Mail::fake();

        $this->postJson(route('enquiries.store'), $this->validEnquiry(['website' => 'https://spam.example']))
            ->assertOk()
            ->assertJsonStructure(['message', 'reference']);

        Mail::assertNothingSent();
        $this->assertSame(0, Lead::count());
    }

    public function test_the_sixth_enquiry_in_a_minute_is_throttled(): void
    {
        Mail::fake();

        for ($i = 0; $i < 5; $i++) {
            $this->postJson(route('enquiries.store'), $this->validEnquiry())->assertOk();
        }

        $this->postJson(route('enquiries.store'), $this->validEnquiry())->assertTooManyRequests();

        Mail::assertSentCount(5);
    }
}
