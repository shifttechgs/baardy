<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * The hero can show a review rating, but only a real one.
 */
class HeroRatingTest extends TestCase
{
    /**
     * Baardy has no Google Business Profile and therefore no reviews. Until
     * that changes the config must stay null and the hero must show no stars.
     */
    public function test_no_rating_is_configured_or_rendered(): void
    {
        $this->assertNull(
            config('marketing.hero.rating'),
            'A rating must not be configured until real reviews exist.'
        );

        $content = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('out of 5', $content);
        $this->assertStringNotContainsStringIgnoringCase('Google review', $content);
    }

    public function test_it_renders_a_configured_rating_with_an_accessible_label(): void
    {
        config()->set('marketing.hero.rating', [
            'score' => 4.8,
            'count' => 19,
            'source' => 'Google',
            'href' => 'https://example.test/profile',
        ]);

        $content = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('4.8 out of 5 from 19 reviews on Google', $content);
        $this->assertStringContainsString('19 Google reviews', $content);
        $this->assertStringContainsString('https://example.test/profile', $content);
    }

    /**
     * A fractional score must draw a partially filled star, which is what the
     * per-star gradient is for. Whole scores should not emit one.
     */
    public function test_a_fractional_score_draws_a_partial_star(): void
    {
        config()->set('marketing.hero.rating', ['score' => 4.6, 'count' => 5, 'source' => 'Google']);
        $this->assertStringContainsString('linearGradient', $this->get('/')->getContent());

        config()->set('marketing.hero.rating', ['score' => 5, 'count' => 5, 'source' => 'Google']);
        $this->assertStringNotContainsString('linearGradient', $this->get('/')->getContent());
    }

    public function test_it_handles_a_single_review_without_a_stray_plural(): void
    {
        config()->set('marketing.hero.rating', ['score' => 5, 'count' => 1, 'source' => 'Google']);

        $content = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('5 out of 5 from 1 review on Google', $content);
        $this->assertStringNotContainsString('1 review on Google', str_replace('from 1 review on Google', '', $content));
    }

    /**
     * The design-preview sample renders only in a local environment, so it
     * can never reach a live site, and a real rating replaces it outright.
     */
    public function test_the_preview_rating_never_renders_outside_local(): void
    {
        config()->set('marketing.hero.rating_preview', ['score' => 4.8, 'count' => 19, 'source' => 'Google']);

        $this->get('/')->assertOk()->assertDontSee('4.8 out of 5');

        $this->app['env'] = 'local';

        $this->get('/')->assertOk()->assertSee('4.8 out of 5');

        config()->set('marketing.hero.rating', ['score' => 4.5, 'count' => 3, 'source' => 'Google']);

        $this->get('/')->assertOk()->assertSee('4.5 out of 5')->assertDontSee('4.8 out of 5');
    }
}
