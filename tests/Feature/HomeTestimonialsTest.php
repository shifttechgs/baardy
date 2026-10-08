<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Testimonials are published only when real: a quote reaches the page only
 * when it is marked `consented`, and the section is absent while none is.
 */
class HomeTestimonialsTest extends TestCase
{
    public function test_placeholder_quotes_stay_off_the_page_outside_the_demo_preview(): void
    {
        config()->set('marketing.testimonials_preview', false);
        config()->set('marketing.customer_stories', []);
        config()->set('marketing.testimonials', [$this->placeholder()]);

        $content = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('id="testimonials"', $content);
        $this->assertStringNotContainsString('A placeholder nobody said.', $content);
    }

    /**
     * @return array<string, mixed>
     */
    private function placeholder(): array
    {
        return ['quote' => 'A placeholder nobody said.', 'name' => 'Grocery retailer', 'role' => 'Sample quote', 'initials' => 'GR', 'consented' => false];
    }

    public function test_the_published_reviews_are_all_consented_and_unlabelled(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('id="testimonials"', $content);
        $this->assertStringNotContainsString('Sample quote', $content);
        $this->assertStringNotContainsString('Sample story', $content);

        foreach (config('marketing.testimonials') as $testimonial) {
            $this->assertTrue($testimonial['consented']);
            $this->assertStringContainsString(e($testimonial['quote']), $content);
        }
    }

    public function test_the_demo_preview_labels_its_quotes_as_samples(): void
    {
        config()->set('marketing.testimonials_preview', true);
        config()->set('marketing.customer_stories', []);
        config()->set('marketing.testimonials', [$this->placeholder()]);

        $content = preg_replace('/\s+/', ' ', $this->get('/')->assertOk()->getContent());

        $this->assertStringContainsString('id="testimonials"', $content);
        $this->assertStringContainsString('A placeholder nobody said.', $content);

        $this->assertSame(
            1,
            preg_match_all('/<figure(?![^>]*aria-hidden)[^>]*>(?:(?!<\/figure>).)*Sample quote(?:(?!<\/figure>).)*<\/figure>/s', $content),
            'Each review should be exposed to assistive technology exactly once, labelled "Sample quote".'
        );
    }

    /**
     * Social proof sits straight after the loans, before the reasons and the
     * process, so a reader meets it while still deciding.
     */
    public function test_it_comes_straight_after_the_loans(): void
    {
        config()->set('marketing.testimonials_preview', true);

        $content = $this->get('/')->assertOk()->getContent();

        $products = strpos($content, 'id="products"');
        $testimonials = strpos($content, 'id="testimonials"');
        $whyUs = strpos($content, 'id="why-us"');

        $this->assertNotFalse($testimonials);
        $this->assertGreaterThan($products, $testimonials);
        $this->assertLessThan($whyUs, $testimonials);
    }

    public function test_only_consented_quotes_are_published(): void
    {
        config()->set('marketing.testimonials_preview', true);
        config()->set('marketing.customer_stories', []);

        config()->set('marketing.testimonials', [
            [
                'quote' => 'They wrote the full cost down before I signed.',
                'name' => 'Tendai M.',
                'role' => 'Market trader, Harare',
                'initials' => 'TM',
                'consented' => true,
            ],
            [
                'quote' => 'A quote nobody agreed to share.',
                'name' => 'Not Consented',
                'role' => 'Somewhere',
                'initials' => 'NC',
                'consented' => false,
            ],
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('id="testimonials"', false)
            ->assertSee('They wrote the full cost down before I signed.')
            ->assertSee('Tendai M.')
            ->assertDontSee('A quote nobody agreed to share.')
            ->assertDontSee('Not Consented')
            ->assertDontSee('Sample quote');
    }

    public function test_two_stories_and_the_reviews_share_one_section(): void
    {
        config()->set('marketing.testimonials_preview', false);
        config()->set('marketing.customer_stories', [
            ['story' => 'Story one in their own words.', 'name' => 'Tendai M.', 'role' => 'Tailor, Bulawayo', 'consented' => true],
            ['story' => 'Story two in their own words.', 'name' => 'Rudo C.', 'role' => 'Farmer, Mazowe', 'consented' => true],
            ['story' => 'A third story that should wait.', 'name' => 'Farai N.', 'role' => 'Trader, Harare', 'consented' => true],
        ]);
        config()->set('marketing.testimonials', [
            ['quote' => 'A short review.', 'name' => 'Chipo M.', 'role' => 'Teacher, Gweru', 'initials' => 'CM', 'consented' => true],
        ]);

        $content = $this->get('/')->assertOk()->getContent();

        $this->assertSame(1, substr_count($content, 'id="testimonials"'));
        $this->assertStringContainsString('Story one in their own words.', $content);
        $this->assertStringContainsString('Story two in their own words.', $content);
        $this->assertStringNotContainsString('A third story that should wait.', $content);
        $this->assertStringContainsString('A short review.', $content);
    }
}
