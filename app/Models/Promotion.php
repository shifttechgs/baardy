<?php

namespace App\Models;

use App\PromotionStatus;
use Database\Factories\PromotionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * A promotion published from the admin panel.
 *
 * LIVE means approved AND now between starts_at and ends_at. Nothing else
 * reaches the public site. Each placement (pill above the header, hero
 * chip, Loans menu card, product tag) shows at most one promotion; when two overlap, the one
 * ending soonest wins.
 *
 * SIGN-OFF IS KEPT HONEST: changing any public-facing field of an approved
 * promotion sends it back to Draft and clears the approval, so what is live
 * is always exactly what was signed off.
 */
#[Fillable([
    'title', 'slug', 'summary', 'body', 'terms', 'image_path', 'image_alt', 'product',
    'cta_label', 'tracking_code', 'show_in_banner', 'show_in_hero', 'show_in_menu', 'show_on_product',
    'starts_at', 'ends_at',
])]
class Promotion extends Model
{
    /** @use HasFactory<PromotionFactory> */
    use HasFactory;

    /**
     * Fields the public sees. Editing any of these un-approves the promotion.
     *
     * @var list<string>
     */
    public const PUBLIC_FIELDS = [
        'title', 'slug', 'summary', 'body', 'terms', 'image_path', 'image_alt', 'product',
        'cta_label', 'show_in_banner', 'show_in_hero', 'show_in_menu', 'show_on_product', 'starts_at', 'ends_at',
    ];

    /**
     * Placements and the flag that switches each on.
     *
     * @var array<string, string>
     */
    public const PLACEMENTS = [
        'banner' => 'show_in_banner',
        'hero' => 'show_in_hero',
        'menu' => 'show_in_menu',
        'product' => 'show_on_product',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PromotionStatus::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'approved_at' => 'datetime',
            'show_in_banner' => 'boolean',
            'show_in_hero' => 'boolean',
            'show_in_menu' => 'boolean',
            'show_on_product' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Promotion $promotion): void {
            if ($promotion->exists
                && $promotion->getOriginal('status') === PromotionStatus::Approved
                && $promotion->isDirty(self::PUBLIC_FIELDS)
                && ! $promotion->isDirty('status')) {
                $promotion->status = PromotionStatus::Draft;
                $promotion->approved_by = null;
                $promotion->approved_at = null;
            }
        });
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Enquiries this promotion brought in.
     *
     * @return HasMany<Lead, $this>
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * Where staff share a promotion, and the tags each channel's link carries.
     * The source is what the leads chart reports; the medium groups channels.
     *
     * @var array<string, array{label: string, medium: string}>
     */
    public const SHARE_CHANNELS = [
        'facebook' => ['label' => 'Facebook', 'medium' => 'social'],
        'instagram' => ['label' => 'Instagram', 'medium' => 'social'],
        'whatsapp' => ['label' => 'WhatsApp', 'medium' => 'messaging'],
        'email' => ['label' => 'Email', 'medium' => 'email'],
        'sms' => ['label' => 'SMS', 'medium' => 'sms'],
    ];

    /**
     * The promotion's public link, tagged with where it is being shared, so
     * every enquiry it brings in says which channel sent it. The campaign is
     * the promotion's own link name.
     */
    public function trackedUrl(string $source): string
    {
        return route('promotions.show', $this).'?'.http_build_query([
            'utm_source' => $source,
            'utm_medium' => self::SHARE_CHANNELS[$source]['medium'] ?? 'other',
            'utm_campaign' => $this->slug,
        ]);
    }

    /**
     * Approved and running now.
     *
     * @param  Builder<Promotion>  $query
     */
    public function scopeLive(Builder $query): void
    {
        $query->where('status', PromotionStatus::Approved)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>', now());
    }

    /**
     * The one live promotion for a placement, or null.
     */
    public static function forPlacement(string $placement): ?self
    {
        $flag = self::PLACEMENTS[$placement] ?? null;

        if ($flag === null) {
            return null;
        }

        return self::query()
            ->live()
            ->where($flag, true)
            ->orderBy('ends_at')
            ->first();
    }

    /**
     * Live promotions tagged on products, keyed by product name.
     *
     * @return array<string, self>
     */
    public static function byProduct(): array
    {
        return self::query()
            ->live()
            ->where('show_on_product', true)
            ->whereNotNull('product')
            ->orderByDesc('ends_at')
            ->get()
            ->keyBy('product')
            ->all();
    }

    public function isLive(): bool
    {
        return $this->status === PromotionStatus::Approved
            && $this->starts_at->isPast()
            && $this->ends_at->isFuture();
    }

    public function hasEnded(): bool
    {
        return $this->status === PromotionStatus::Approved && $this->ends_at->isPast();
    }

    /**
     * The photograph's description for screen readers, falling back to the
     * title when the editor left it blank.
     */
    public function imageAlt(): string
    {
        return filled($this->image_alt) ? $this->image_alt : $this->title;
    }

    public function imageUrl(): ?string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
    }

    /**
     * Width over height of the uploaded image, kept between a tall poster
     * (4:5) and a wide banner (16:9) so no card is ever absurdly tall or flat.
     * Cards frame the picture to its own proportions instead of cropping it
     * or shrinking a square poster into a wide strip. Falls back to 4:3 when
     * the file is missing or unreadable.
     */
    public function imageAspectRatio(): float
    {
        $fallback = 4 / 3;

        if (! $this->image_path) {
            return $fallback;
        }

        $path = Storage::disk('public')->path($this->image_path);
        $size = is_file($path) ? @getimagesize($path) : false;

        if (! $size || ($size[1] ?? 0) === 0) {
            return $fallback;
        }

        return round(max(0.8, min(16 / 9, $size[0] / $size[1])), 3);
    }

    /**
     * Where the promotion's call to action goes: the enquiry form, with the
     * loan preselected and the tracking code carried through, so the team
     * knows which promotion brought the enquiry in.
     */
    public function enquiryUrl(): string
    {
        return route('contact', array_filter([
            'interest' => $this->product,
            'promo' => $this->tracking_code,
        ])).'#contact';
    }

    /**
     * Whole days until the promotion ends, counted by calendar day: 0 on its
     * last day, never negative.
     */
    public function daysLeft(): int
    {
        return max(0, (int) now()->startOfDay()->diffInDays($this->ends_at->copy()->startOfDay()));
    }

    /**
     * How long is left, in words a visitor reads at a glance.
     */
    public function timeLeftLabel(): string
    {
        return match ($days = $this->daysLeft()) {
            0 => 'Ends today',
            1 => 'Ends tomorrow',
            default => "{$days} days left",
        };
    }

    /**
     * The WhatsApp message a visitor sends from this promotion's page, naming
     * the offer and its code so the team knows which one it is about.
     */
    public function whatsappMessage(): string
    {
        return 'Hello '.config('company.name').', I would like to know more about "'.$this->title.'" (ref '.$this->tracking_code.').';
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
