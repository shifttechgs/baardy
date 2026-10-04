<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * The loans: /loans lists them, /loans/{slug} gives each its own page.
 *
 * A loan's facts (name, photograph, range, term) live in
 * config/marketing.php -> products; the words of its page live in
 * config/loans.php, matched by slug.
 */
class LoanController extends Controller
{
    public function index(): View
    {
        return view('pages.loans.index', [
            'loans' => $this->loans(),
            'promotions' => Promotion::byProduct(),
        ]);
    }

    public function show(string $slug): View
    {
        $loans = $this->loans();
        $loan = $loans->firstWhere('slug', $slug);

        abort_if($loan === null, 404);

        $profiles = collect(config('marketing.eligibility.profiles'))->keyBy('key');

        return view('pages.loans.show', [
            'loan' => $loan,
            'documents' => $profiles->has($loan['profile'])
                ? $profiles[$loan['profile']]['documents']
                : ['A valid national ID'],
            'documentsAreComplete' => $profiles->has($loan['profile']),
            'others' => $loans->where('slug', '!=', $slug)->values(),
            'promotion' => Promotion::byProduct()[$loan['name']] ?? null,
        ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function loans(): Collection
    {
        return collect(config('marketing.products'))
            ->filter(fn (array $product): bool => isset($product['slug'], config('loans')[$product['slug']]))
            ->map(fn (array $product): array => $product + config('loans')[$product['slug']])
            ->values();
    }
}
