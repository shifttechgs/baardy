<?php

namespace Tests\Feature;

use App\Mail\EnquiryReceived;
use Tests\TestCase;

/**
 * The enquiry email is what the client's staff actually read, so it must
 * carry every detail needed to call the visitor back.
 */
class EnquiryReceivedTest extends TestCase
{
    /**
     * @return array{name: string, phone: string, email: ?string, interest: string, branch: string, message: ?string}
     */
    private function enquiry(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Tendai Moyo',
            'phone' => '+263 77 123 4567',
            'email' => 'tendai@example.com',
            'interest' => 'SME Bridging Finance',
            'branch' => 'Harare',
            'message' => 'Stock for December.',
        ], $overrides);
    }

    public function test_it_shows_every_detail_needed_to_call_back(): void
    {
        $mail = new EnquiryReceived($this->enquiry());

        $mail->assertHasSubject('Website enquiry: SME Bridging Finance (Harare)');
        $mail->assertSeeInHtml('Tendai Moyo');
        $mail->assertSeeInHtml('+263 77 123 4567');
        $mail->assertSeeInHtml('tendai@example.com');
        $mail->assertSeeInHtml('Stock for December.');
    }

    public function test_replies_go_to_the_visitor_when_they_gave_an_email(): void
    {
        $mail = new EnquiryReceived($this->enquiry());

        $mail->assertHasReplyTo('tendai@example.com', 'Tendai Moyo');
    }

    public function test_it_has_no_reply_to_when_the_visitor_gave_no_email(): void
    {
        $mail = new EnquiryReceived($this->enquiry(['email' => null]));

        $this->assertSame([], $mail->envelope()->replyTo);
    }

    /**
     * Visitor input goes into an HTML email; markup in it must arrive as
     * text, not as markup.
     */
    public function test_it_escapes_markup_in_the_message(): void
    {
        $mail = new EnquiryReceived($this->enquiry(['message' => '<script>alert(1)</script>']));

        $mail->assertDontSeeInHtml('<script>alert(1)</script>', false);
    }
}
