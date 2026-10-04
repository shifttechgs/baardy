<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * "How it works" shows every step, plainly, and offers both ways in.
 */
class HomeProcessTest extends TestCase
{
    /**
     * @return string the markup of the #how-it-works section only
     */
    private function section(): string
    {
        $content = $this->get('/')->assertOk()->getContent();

        $start = strpos($content, 'id="how-it-works"');
        $this->assertNotFalse($start, 'The process section must be on the page.');

        return substr($content, $start, strpos($content, '</section>', $start) - $start);
    }

    public function test_every_step_is_on_screen_in_order(): void
    {
        $section = $this->section();

        $previous = -1;

        foreach (config('marketing.steps') as $step) {
            $title = strpos($section, e($step['title']));

            $this->assertNotFalse($title, "Step title \"{$step['title']}\" is missing.");
            $this->assertGreaterThan($previous, $title, 'The steps must appear in their real order.');
            $this->assertStringContainsString(e($step['body']), $section);

            $previous = $title;
        }
    }

    /**
     * Plain, after the reference: no photographs, no paper-stack visual, and
     * the steps are CSS-sticky cards dealt one over another (no script).
     */
    public function test_it_is_plain_with_sticky_step_cards(): void
    {
        $section = $this->section();

        $this->assertStringNotContainsString('<img', $section);
        $this->assertStringNotContainsString('paperStack', $section);
        $this->assertStringNotContainsString('processRail', $section);
        $this->assertSame(count(config('marketing.steps')), substr_count($section, 'bg-mist p-6 sm:p-8 lg:sticky'));
    }

    /**
     * Step one is a branch visit, so the section offers both routes: the
     * enquiry form and WhatsApp.
     */
    public function test_it_ends_on_both_ways_in(): void
    {
        $section = $this->section();

        $this->assertStringContainsString('href="'.config('company.cta.primary.href').'"', $section);
        $this->assertStringContainsString('href="https://wa.me/', $section);
        $this->assertStringContainsString('WhatsApp us before you visit', $section);
    }
}
