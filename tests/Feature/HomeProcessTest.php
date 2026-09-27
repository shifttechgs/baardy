<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * "How it works" shows all four steps at once: nothing pinned, paced by
 * scrolling or hidden until reached, and it ends on both ways in.
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
            $this->assertStringContainsString(e($step['meta']), $section);

            $previous = $title;
        }
    }

    /**
     * The old 300vh scroll track hid three of the four steps until reached.
     */
    public function test_nothing_is_pinned_or_paced_by_scrolling(): void
    {
        $section = $this->section();

        $this->assertStringNotContainsString('processRail', $section);
        $this->assertStringNotContainsString('lg:sticky', $section);
        $this->assertDoesNotMatchRegularExpression('/lg:h-\[\d+vh\]/', $section);
    }

    /**
     * The paper stack is driven by the steps: one document per step, and
     * every step can bring its document forward.
     */
    public function test_the_paper_stack_has_a_document_for_every_step(): void
    {
        $section = $this->section();
        $steps = count(config('marketing.steps'));

        $this->assertStringContainsString("paperStack({$steps})", $section);
        $this->assertSame($steps, substr_count($section, 'x-bind:style="sheetStyle('));

        for ($index = 0; $index < $steps; $index++) {
            $this->assertStringContainsString("choose({$index})", $section);
        }
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
