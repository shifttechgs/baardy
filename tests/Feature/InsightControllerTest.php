<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * The Insights articles: listed on /insights, three on the homepage, and
 * each readable at its own URL.
 */
class InsightControllerTest extends TestCase
{
    public function test_the_index_lists_every_article(): void
    {
        $response = $this->get(route('insights.index'))->assertOk();

        foreach (config('insights.articles') as $article) {
            $response->assertSee($article['title'])
                ->assertSee(route('insights.show', $article['slug']), false);
        }
    }

    public function test_an_article_renders_its_title_and_body(): void
    {
        $article = config('insights.articles')[0];

        $this->get(route('insights.show', $article['slug']))
            ->assertOk()
            ->assertSee($article['title'])
            ->assertSee('Find your floor');
    }

    public function test_an_article_suggests_the_other_articles_but_not_itself(): void
    {
        [$first, $second, $third] = config('insights.articles');

        $content = $this->get(route('insights.show', $first['slug']))->assertOk()->getContent();

        // The page's own URL legitimately appears in its canonical link, so
        // only the "More insights" block is checked.
        $more = str($content)->after('More insights')->toString();

        $this->assertStringContainsString(route('insights.show', $second['slug']), $more);
        $this->assertStringContainsString(route('insights.show', $third['slug']), $more);
        $this->assertStringNotContainsString(route('insights.show', $first['slug']), $more);
    }

    public function test_an_unknown_article_returns_404(): void
    {
        $this->get('/insights/no-such-article')->assertNotFound();
    }

    /**
     * Every configured article needs a body partial, or its page errors
     * instead of rendering.
     */
    public function test_every_configured_article_renders(): void
    {
        foreach (config('insights.articles') as $article) {
            $this->get(route('insights.show', $article['slug']))->assertOk();
        }
    }

    public function test_the_homepage_shows_the_three_newest_articles(): void
    {
        $response = $this->get('/')->assertOk();

        foreach (array_slice(config('insights.articles'), 0, 3) as $article) {
            $response->assertSee($article['title']);
        }

        $response->assertSee(route('insights.index'), false);
    }
}
