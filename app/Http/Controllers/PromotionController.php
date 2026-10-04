<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\PromotionStatus;
use Illuminate\Contracts\View\View;

/**
 * The public promotions pages.
 *
 *   /promotions          every live promotion
 *   /promotions/{slug}   one promotion, with its full terms
 *
 * A promotion page is shared over WhatsApp and social posts, so its link
 * outlives the promotion: once an approved promotion has ended, the page
 * stays up and says so, with no call to action, rather than returning a
 * 404 to someone following an old link. Drafts, promotions awaiting
 * approval and approved ones that have not started are not public.
 */
class PromotionController extends Controller
{
    public function index(): View
    {
        return view('pages.promotions.index', [
            'promotions' => Promotion::query()->live()->orderBy('ends_at')->get(),
        ]);
    }

    public function show(Promotion $promotion): View
    {
        abort_unless(
            $promotion->status === PromotionStatus::Approved && $promotion->starts_at->isPast(),
            404,
        );

        return view('pages.promotions.show', [
            'promotion' => $promotion,
            'others' => Promotion::query()->live()->whereKeyNot($promotion->getKey())->orderBy('ends_at')->take(3)->get(),
        ]);
    }
}
