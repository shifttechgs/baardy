<?php

namespace Tests\Feature;

use App\LeadActivityType;
use App\LeadLostReason;
use App\LeadStage;
use App\Models\Lead;
use App\Models\User;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

/**
 * The funnel rules on the model: phone matching, what each move stamps, and
 * that every move is on the timeline.
 */
class LeadTest extends TestCase
{
    #[TestWith(['+263 77 123 4567', '263771234567'])]
    #[TestWith(['077 123 4567', '263771234567'])]
    #[TestWith(['00263771234567', '263771234567'])]
    #[TestWith(['(263) 77-123-4567', '263771234567'])]
    public function test_phone_numbers_are_matched_however_they_are_written(string $phone, string $normalized): void
    {
        $this->assertSame($normalized, Lead::normalizePhone($phone));
    }

    public function test_the_first_move_out_of_new_stamps_the_first_contact(): void
    {
        $this->travelTo(now()->startOfMinute());
        $lead = Lead::factory()->create();
        $user = User::factory()->admin()->create();

        $this->travel(45)->minutes();
        $lead->moveTo(LeadStage::Contacted, $user);

        $this->assertSame(45, (int) $lead->created_at->diffInMinutes($lead->first_contacted_at));
        $this->assertTrue($lead->assignee->is($user));
        $this->assertNull($lead->closed_at);

        $activity = $lead->activities()->first();
        $this->assertSame(LeadActivityType::StageChanged, $activity->type);
        $this->assertSame('New → Contacted', $activity->body);
        $this->assertTrue($activity->user->is($user));
    }

    public function test_closing_stamps_closed_at_and_reopening_clears_it(): void
    {
        $lead = Lead::factory()->contacted()->create();

        $lead->moveTo(LeadStage::Lost, null, LeadLostReason::Unreachable, 'Three calls, no answer.');
        $this->assertNotNull($lead->closed_at);
        $this->assertSame(LeadLostReason::Unreachable, $lead->lost_reason);
        $this->assertStringContainsString('Three calls, no answer.', $lead->activities()->first()->body);

        $lead->moveTo(LeadStage::Contacted);
        $this->assertNull($lead->closed_at);
        $this->assertNull($lead->lost_reason);
    }

    public function test_a_lead_cannot_be_lost_without_a_reason(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Lead::factory()->create()->moveTo(LeadStage::Lost);
    }

    public function test_the_whatsapp_link_opens_a_greeting_with_the_reference(): void
    {
        $lead = Lead::factory()->create(['name' => 'Rudo Chikwanha', 'phone' => '077 123 4567', 'interest' => 'Educational Loans']);
        $user = User::factory()->admin()->create(['name' => 'Farai Ndlovu']);

        $url = $lead->whatsappUrl($user);

        $this->assertStringStartsWith('https://wa.me/263771234567?text=', $url);
        $this->assertStringContainsString(rawurlencode('Hello Rudo, this is Farai from'), $url);
        $this->assertStringContainsString(rawurlencode("(ref {$lead->reference})"), $url);
    }
}
