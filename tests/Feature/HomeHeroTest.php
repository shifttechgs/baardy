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
     * One still photograph at full resolution (the client asked for no
     * slideshow): a single <img> in the hero, the widest candidate offered,
     * no slide control and no slideshow component.
     */
    public function test_it_shows_one_static_high_resolution_photograph(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        $hero = substr($content, strpos($content, '<section aria-labelledby="hero-heading"'));
        $hero = substr($hero, 0, strpos($hero, '</section>'));
        $widest = array_key_last(config('marketing.hero.slides')[0]['sources']);

        $this->assertSame(1, substr_count($hero, '<img'));
        $this->assertStringContainsString(basename($widest), $hero);
        $this->assertStringNotContainsString('heroSlides', $hero);
        $this->assertStringNotContainsString('Pause the photographs', $hero);
        $this->assertStringNotContainsString('Show photograph', $hero);
    }

    /**
     * The credentials line states a fact checkable in the RBZ register,
     * counted from the year of first listing. Nothing in the hero may carry an
     * invented rating, customer count or savings figure.
     */
    public function test_the_hero_makes_its_one_claim_licensed_and_established(): void
    {
        $content = $this->get('/')->assertOk()->getContent();
        $hero = substr($content, strpos($content, '<section aria-labelledby="hero-heading"'));
        $hero = substr($hero, 0, strpos($hero, '</section>'));

        $this->assertStringContainsString('Licensed Microfinance', $hero);
        $this->assertStringContainsString('Est. 2015', $hero);
        $this->assertStringNotContainsString('years licensed and lending', $hero);
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
     * The hero is deliberately bare (after trova-travel): no loan-offer card,
     * no signature flourish. The primary button keeps its hook.
     */
    public function test_the_hero_carries_no_offer_card(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<section[^>]+class="hero /', $content);
        $this->assertStringContainsString('hero-cta', $content);
        $this->assertStringNotContainsString('class="offer-ink"', $content);
        $this->assertStringNotContainsString('Your signature', $content);
    }

    public function test_it_has_one_call_to_action(): void
    {
        $hero = $this->get('/')->assertOk()->getContent();
        $hero = substr($hero, strpos($hero, '<section aria-labelledby="hero-heading"'));
        $hero = substr($hero, 0, strpos($hero, '</section>'));

        $this->assertStringContainsString(config('company.cta.primary.label'), $hero);
        $this->assertStringNotContainsString(config('company.cta.secondary.label'), $hero);
    }

    /**
     * The licence line, then the promise, then the ask, then who it is for.
     */
    public function test_it_orders_the_hero_for_a_two_second_read(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSeeInOrder([
                'Licensed Microfinance',
                config('marketing.hero.heading')[0],
                config('company.cta.primary.label'),
                'reviewed by a person',
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

    /**
     * The headline and actions must sit inside the hero's own wrapper. A stray
     * closing tag after the slide loop once pushed them outside it, leaving a
     * bare photograph.
     */
    public function test_the_headline_is_inside_the_hero_wrapper(): void
    {
        $doc = new \DOMDocument;
        @$doc->loadHTML($this->get('/')->assertOk()->getContent());
        $xpath = new \DOMXPath($doc);

        $this->assertSame(1, $xpath->query('//section[contains(@class,"hero")]//*[@id="hero-heading"]')->length);
        $this->assertSame(1, $xpath->query('//section[contains(@class,"hero")]//div[contains(concat(" ", normalize-space(@class), " "), " bg-ink ")][.//*[@id="hero-heading"]]')->length);
    }

    /**
     * The client wants a headline of six words at most.
     */
    public function test_the_headline_is_six_words_at_most(): void
    {
        $words = str_word_count(implode(' ', config('marketing.hero.heading')));

        $this->assertLessThanOrEqual(6, $words);
    }
}
