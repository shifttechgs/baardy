<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

/**
 * The Insights articles. Content is config-driven (config/insights.php, with
 * each body in resources/views/insights/articles/) until the site has a CMS.
 */
class InsightController extends Controller
{
    public function index(): View
    {
        return view('pages.insights.index', [
            'articles' => $this->articles(),
        ]);
    }

    public function show(string $slug): View
    {
        $articles = $this->articles();
        $article = $articles->firstWhere('slug', $slug);

        abort_if($article === null, 404);

        return view('pages.insights.show', [
            'article' => $article,
            'more' => $articles->where('slug', '!=', $slug)->take(2)->values(),
        ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function articles(): Collection
    {
        return collect(config('insights.articles'));
    }
}
