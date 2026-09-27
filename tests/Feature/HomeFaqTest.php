<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * The FAQ is native, exclusive <details> rows: every answer is in the markup,
 * clicking works without script, and on fine pointers resting on a question
 * opens it.
 */
class HomeFaqTest extends TestCase
{
    public function test_every_question_is_an_exclusive_native_row_with_its_answer(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        $faqs = config('marketing.faqs');

        $this->assertSame(count($faqs), preg_match_all('/<details\s+name="faq"/', $content));

        foreach ($faqs as $faq) {
            $this->assertStringContainsString(e($faq['question']), $content);
            $this->assertStringContainsString(e($faq['answer']), preg_replace('/\s+/', ' ', $content));
        }
    }

    public function test_resting_on_a_question_is_wired_to_open_it(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '/x-data="hoverAccordion"\s+x-on:mousemove="intend\(\$event\)"\s+x-on:mouseleave="cancel\(\)"/',
            $content
        );
    }
}
