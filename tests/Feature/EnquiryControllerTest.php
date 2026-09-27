<?php

namespace Tests\Feature;

use App\Mail\EnquiryReceived;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

/**
 * The "Get in touch" form. An enquiry must reach the client's inbox, bad
 * input must come back with a message a person can act on, and bots must
 * get nothing through.
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
            ->assertRedirect(route('home').'#contact')
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

        $this->postJson(route('enquiries.store'), $this->validEnquiry())
            ->assertOk()
            ->assertJson(['message' => 'Thank you. We have your details and will be in touch shortly.']);

        Mail::assertSent(EnquiryReceived::class);
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
     * it learns nothing, but no mail is sent.
     */
    public function test_a_filled_honeypot_is_accepted_but_not_mailed(): void
    {
        Mail::fake();

        $this->postJson(route('enquiries.store'), $this->validEnquiry(['website' => 'https://spam.example']))
            ->assertOk();

        Mail::assertNothingSent();
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
