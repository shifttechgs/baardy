<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * The hero carries the page's only regulatory claims above the fold, so its
 * content is tested rather than left to visual review.
 */
class HomeHeroTest extends TestCase
{
    /**
     * The headline is two lines, each set word by word, so it is asserted
     * line by line and in order rather than as one string.
     */
    public function test_it_shows_the_headline_and_lead(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSeeInOrder(config('marketing.hero.heading'), false)
            ->assertSee(config('marketing.hero.lead'), false);
    }

    /**
     * The headline makes no speed claim: turnaround is unconfirmed, and the
     * largest type on the site is the last place to state it.
     */
    public function test_the_headline_makes_no_speed_claim(): void
    {
        $headline = implode(' ', config('marketing.hero.heading'));

        foreach (['days', 'weeks', 'hours', 'instant', 'fast'] as $word) {
            $this->assertStringNotContainsStringIgnoringCase($word, $headline);
        }
    }

    /**
     * One photograph, not a slideshow: nothing rotates and there is no slide
     * control to operate.
     */
    public function test_it_shows_one_photograph_without_a_slide_control(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        $hero = substr($content, 0, strpos($content, 'id="trust"'));

        $this->assertSame(1, substr_count($hero, 'hero-photo-img'));
        $this->assertStringNotContainsString('Pause the photographs', $hero);
        $this->assertStringNotContainsString('Show photograph', $hero);
    }

    /**
     * The credentials line states a fact checkable in the RBZ register,
     * counted from the year of first listing. Nothing in the hero may carry an
     * invented rating, customer count or savings figure.
     */
    public function test_it_backs_the_hero_with_a_verifiable_figure(): void
    {
        $years = now()->year - config('marketing.hero.credentials.years.since');

        $this->get('/')
            ->assertOk()
            ->assertSee('data-count-to="'.$years.'"', false)
            ->assertSee(config('marketing.hero.credentials.years.label'));
    }

    /**
     * The promises are off the page for now but kept in config to restore.
     * Each is qualified for a reason: dropping a qualifier turns a scoped
     * statement into an unconditional one, which on a lending site is a
     * representation. See config/marketing.php.
     */
    public function test_the_stored_promises_keep_their_qualifiers(): void
    {
        $this->assertSame([
            'A decision on complete applications in one working day',
            'Every fee and the total repayable, in writing, before you accept',
            'Settle early and pay less interest, with no penalty',
        ], config('marketing.hero.promises'));
    }

    /**
     * The loan offer writes itself in, and signs only while the reader is
     * on the primary call to action: the section, the button and the
     * signature carry the hooks app.css reads.
     */
    public function test_the_loan_offer_is_wired_to_write_in_and_sign(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<section[^>]+class="hero /', $content);
        $this->assertStringContainsString('hero-cta', $content);
        $this->assertSame(3, substr_count($content, 'class="offer-ink"'));
        $this->assertSame(3, substr_count($content, 'class="offer-tick"'));
        $this->assertSame(1, substr_count($content, 'class="offer-sign"'));
    }

    public function test_it_offers_both_calls_to_action(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(config('company.cta.primary.label'))
            ->assertSee(config('company.cta.secondary.label'));
    }

    /**
     * What is on offer, then who it is for, then the ask directly under the
     * promise, then the credentials -- a two-second read, top to bottom.
     */
    public function test_it_orders_the_hero_for_a_two_second_read(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSeeInOrder([
                config('marketing.hero.heading')[0],
                'reviewed by a person',
                config('company.cta.primary.label'),
                'Licensed by the Reserve Bank of Zimbabwe',
            ], false);
    }

    /**
     * The headline animates word by word, so the full sentence must also be
     * present as one run of text for screen readers and search.
     */
    public function test_it_gives_the_animated_headline_as_one_sentence(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(
                '<span class="sr-only">'.e(implode(' ', config('marketing.hero.heading'))).'</span>',
                false
            );
    }
}
