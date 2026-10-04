<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * "Why borrow from us" shows every reason at once: nothing to swipe or click
 * through, and the grid ends on an ask.
 */
class HomeWhyUsTest extends TestCase
{
    public function test_every_reason_is_on_the_page_without_a_carousel(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        $section = substr($content, strpos($content, 'id="why-us"'));
        $section = substr($section, 0, strpos($section, '</section>'));

        foreach (['schedule', 'advisory', 'privacy'] as $tile) {
            $benefit = config('marketing.benefits')[config("marketing.why_us.tiles.{$tile}.benefit")];

            $this->assertStringContainsString(e($benefit['title']), $section);
        }

        $promise = config('marketing.hero.promises')[config('marketing.why_us.tiles.decision.promise')];
        $this->assertStringContainsString(e($promise), $section);

        $this->assertStringNotContainsString('carousel', $section);
        $this->assertStringNotContainsString('snap-x', $section);
    }

    public function test_the_schedule_tile_leads_to_the_enquiry_form(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '/href="\/contact"[^>]*>\s*Ask about this schedule/',
            $content
        );
    }
}
