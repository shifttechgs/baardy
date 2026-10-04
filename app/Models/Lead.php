<?php

namespace App\Models;

use App\LeadActivityType;
use App\LeadLostReason;
use App\LeadStage;
use Database\Factories\LeadFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * A person who asked to be called back, from the "Get in touch" form.
 *
 * CAPTURED FIRST, MAILED SECOND. Lead::capture() stores the enquiry before
 * anything is emailed, so a mail outage can never lose one.
 *
 * ONE PERSON, ONE OPEN LEAD. An enquiry from a phone number that already has
 * an open lead is added to that lead's timeline ("Enquired again") instead of
 * opening a second one, so two staff never call the same person cold.
 *
 * THE FUNNEL (LeadStage) only moves through moveTo(), which stamps
 * first_contacted_at the first time a lead leaves New (the speed-to-lead
 * measure), stamps or clears closed_at, and writes the timeline.
 */
#[Fillable([
    'name', 'phone', 'email', 'interest', 'branch', 'message', 'promotion_id',
    'utm_source', 'utm_medium', 'utm_campaign', 'referrer', 'landing_page',
])]
class Lead extends Model
{
    /** @use HasFactory<LeadFactory> */
    use HasFactory;

    /**
     * Unambiguous characters for references read out over the phone: no 0/O,
     * 1/I/L.
     */
    private const REFERENCE_ALPHABET = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'stage' => LeadStage::class,
            'lost_reason' => LeadLostReason::class,
            'first_contacted_at' => 'datetime',
            'closed_at' => 'datetime',
            'last_enquired_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Lead $lead): void {
            $lead->reference ??= self::newReference();
            $lead->phone_normalized = self::normalizePhone($lead->phone);
            $lead->stage ??= LeadStage::New;
            $lead->last_enquired_at ??= now();
        });

        static::updating(function (Lead $lead): void {
            if ($lead->isDirty('phone')) {
                $lead->phone_normalized = self::normalizePhone($lead->phone);
            }
        });
    }

    /**
     * Store an enquiry from the website: a new lead, or -- when this phone
     * number already has an open lead -- another entry on that one.
     *
     * @param  array{name: string, phone: string, email?: ?string, interest: string, branch: string, message?: ?string, promotion_id?: ?int}  $enquiry
     * @param  array{utm_source?: ?string, utm_medium?: ?string, utm_campaign?: ?string, referrer?: ?string, landing_page?: ?string}  $attribution
     */
    public static function capture(array $enquiry, array $attribution = []): self
    {
        return DB::transaction(function () use ($enquiry, $attribution): self {
            $existing = self::query()
                ->open()
                ->where('phone_normalized', self::normalizePhone($enquiry['phone']))
                ->lockForUpdate()
                ->latest()
                ->first();

            if ($existing) {
                $existing->forceFill([
                    'email' => $existing->email ?? ($enquiry['email'] ?? null),
                    'promotion_id' => $existing->promotion_id ?? ($enquiry['promotion_id'] ?? null),
                    'last_enquired_at' => now(),
                ])->save();

                $existing->log(LeadActivityType::EnquiredAgain, self::describeEnquiry($enquiry));

                return $existing;
            }

            $lead = self::create([
                ...$enquiry,
                ...array_map(fn (?string $value): ?string => $value === null ? null : Str::limit($value, 250, ''), $attribution),
            ]);

            $lead->log(LeadActivityType::Enquired, self::describeEnquiry($enquiry));

            return $lead;
        });
    }

    /**
     * Move the lead through the funnel. Closing as Lost needs a reason.
     */
    public function moveTo(LeadStage $stage, ?User $by = null, ?LeadLostReason $reason = null, ?string $note = null): void
    {
        if ($stage === LeadStage::Lost && $reason === null) {
            throw new \InvalidArgumentException('A lead can only be closed as lost with a reason.');
        }

        if ($stage === $this->stage) {
            return;
        }

        $from = $this->stage;

        $this->stage = $stage;
        $this->lost_reason = $stage === LeadStage::Lost ? $reason : null;
        $this->closed_at = $stage->isOpen() ? null : now();

        if ($from === LeadStage::New && $this->first_contacted_at === null) {
            $this->first_contacted_at = now();
        }

        $this->assigned_to ??= $by?->getKey();
        $this->save();

        $this->log(
            LeadActivityType::StageChanged,
            trim($from->getLabel().' → '.$stage->getLabel().($reason ? ' ('.$reason->getLabel().')' : '')."\n".($note ?? '')),
            $by,
        );
    }

    public function assignTo(?User $assignee, ?User $by = null): void
    {
        if ($this->assigned_to === $assignee?->getKey()) {
            return;
        }

        $this->assigned_to = $assignee?->getKey();
        $this->save();

        $this->log(LeadActivityType::Assigned, $assignee ? 'To '.$assignee->name : 'Unassigned', $by);
    }

    public function addNote(string $body, ?User $by = null): LeadActivity
    {
        return $this->log(LeadActivityType::Note, $body, $by);
    }

    public function log(LeadActivityType $type, ?string $body = null, ?User $by = null): LeadActivity
    {
        return $this->activities()->create([
            'type' => $type,
            'body' => filled($body) ? $body : null,
            'user_id' => $by?->getKey(),
        ]);
    }

    /**
     * @return HasMany<LeadActivity, $this>
     */
    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class)->latest('created_at')->latest('id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * @return BelongsTo<Promotion, $this>
     */
    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    /**
     * @param  Builder<Lead>  $query
     */
    public function scopeOpen(Builder $query): void
    {
        $query->whereIn('stage', LeadStage::open());
    }

    /**
     * A WhatsApp chat with the lead, opened with a greeting that says who is
     * writing and why.
     */
    public function whatsappUrl(?User $from = null): string
    {
        $firstName = Str::before($this->name, ' ');
        $greeting = "Hello {$firstName}, this is ".($from ? Str::before($from->name, ' ').' from ' : '')
            .config('company.name')." about your {$this->interest} enquiry (ref {$this->reference}).";

        return 'https://wa.me/'.$this->phone_normalized.'?text='.rawurlencode($greeting);
    }

    public function telUrl(): string
    {
        return 'tel:+'.$this->phone_normalized;
    }

    /**
     * Where the lead came from, in a few words: the promotion, the campaign,
     * or the site that sent them.
     */
    public function sourceLabel(): string
    {
        return match (true) {
            $this->promotion_id !== null => 'Promotion: '.($this->promotion?->title ?? 'deleted')
                .(filled($this->utm_source) && $this->utm_medium !== 'offline' ? ' ('.ucfirst((string) $this->utm_source).')' : ''),
            filled($this->utm_campaign) => 'Campaign: '.$this->utm_campaign.(filled($this->utm_source) ? ' ('.$this->utm_source.')' : ''),
            filled($this->utm_source) => ucfirst((string) $this->utm_source),
            filled($this->referrer) => (string) (parse_url((string) $this->referrer, PHP_URL_HOST) ?: $this->referrer),
            default => 'Direct',
        };
    }

    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    /**
     * Digits only, with Zimbabwe's country code: "077 123 4567",
     * "+263 77 123 4567" and "263771234567" are the same person.
     */
    public static function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        return match (true) {
            str_starts_with($digits, '00') => substr($digits, 2),
            str_starts_with($digits, '0') => '263'.substr($digits, 1),
            default => $digits,
        };
    }

    private static function newReference(): string
    {
        do {
            $reference = 'BMC-'.collect(range(1, 6))
                ->map(fn (): string => self::REFERENCE_ALPHABET[random_int(0, strlen(self::REFERENCE_ALPHABET) - 1)])
                ->join('');
        } while (self::where('reference', $reference)->exists());

        return $reference;
    }

    /**
     * @param  array{interest: string, branch: string, message?: ?string}  $enquiry
     */
    private static function describeEnquiry(array $enquiry): string
    {
        return trim("{$enquiry['interest']}, {$enquiry['branch']} branch\n".($enquiry['message'] ?? ''));
    }
}
