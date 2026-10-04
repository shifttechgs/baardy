<?php

namespace Tests\Feature;

use App\Filament\Resources\Leads\LeadResource;
use App\Mail\EnquiryReceived;
use App\Models\Lead;
use Tests\TestCase;

/**
 * The enquiry email is what the team reads first, so it must carry every
 * detail needed to call the visitor back, and a way into the lead.
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

    private function mail(array $overrides = []): EnquiryReceived
    {
        $enquiry = $this->enquiry($overrides);

        return new EnquiryReceived(Lead::capture($enquiry), $enquiry);
    }

    public function test_it_shows_every_detail_needed_to_call_back(): void
    {
        $mail = $this->mail();
        $lead = Lead::sole();

        $mail->assertHasSubject("New lead {$lead->reference}: SME Bridging Finance (Harare)");
        $mail->assertSeeInHtml('Tendai Moyo');
        $mail->assertSeeInHtml('+263 77 123 4567');
        $mail->assertSeeInHtml('tendai@example.com');
        $mail->assertSeeInHtml('Stock for December.');
        $mail->assertSeeInHtml(LeadResource::getUrl('view', ['record' => $lead], panel: 'admin'), false);
    }

    public function test_a_repeat_enquiry_is_flagged_as_one(): void
    {
        $this->mail();
        $mail = $this->mail(['message' => 'Following up.']);

        $mail->assertHasSubject('Repeat enquiry '.Lead::sole()->reference.': SME Bridging Finance (Harare)');
        $mail->assertSeeInHtml('enquired again');
    }

    public function test_replies_go_to_the_visitor_when_they_gave_an_email(): void
    {
        $this->mail()->assertHasReplyTo('tendai@example.com', 'Tendai Moyo');
    }

    public function test_it_has_no_reply_to_when_the_visitor_gave_no_email(): void
    {
        $this->assertSame([], $this->mail(['email' => null])->envelope()->replyTo);
    }

    /**
     * Visitor input goes into an HTML email; markup in it must arrive as
     * text, not as markup.
     */
    public function test_it_escapes_markup_in_the_message(): void
    {
        $this->mail(['message' => '<script>alert(1)</script>'])
            ->assertDontSeeInHtml('<script>alert(1)</script>', false);
    }
}
