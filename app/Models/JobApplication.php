<?php

namespace App\Models;

use Database\Factories\JobApplicationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * An application with its CV, kept in the database (`cv_data`, base64).
 * Lists should use withoutCv() so the document is not loaded for every row.
 */
#[Fillable(['vacancy_id', 'name', 'phone', 'email', 'message', 'cv_original_name', 'cv_mime', 'cv_size', 'cv_data', 'reviewed_at'])]
class JobApplication extends Model
{
    /** @use HasFactory<JobApplicationFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['reviewed_at' => 'datetime'];
    }

    /**
     * Everything except the CV document itself.
     *
     * @param  Builder<JobApplication>  $query
     */
    public function scopeWithoutCv(Builder $query): void
    {
        $query->select(['id', 'vacancy_id', 'name', 'phone', 'email', 'message', 'cv_original_name', 'cv_mime', 'cv_size', 'reviewed_at', 'created_at', 'updated_at']);
    }

    /**
     * The CV file's bytes.
     */
    public function cvContents(): string
    {
        return base64_decode($this->cv_data, true) ?: '';
    }

    public function isReviewed(): bool
    {
        return $this->reviewed_at !== null;
    }

    public function telUrl(): string
    {
        return 'tel:+'.Lead::normalizePhone($this->phone);
    }

    public function whatsappUrl(): string
    {
        $greeting = 'Hello '.Str::before($this->name, ' ').', this is '.config('company.name').' about your job application.';

        return 'https://wa.me/'.Lead::normalizePhone($this->phone).'?text='.rawurlencode($greeting);
    }

    /**
     * @return BelongsTo<Vacancy, $this>
     */
    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class);
    }
}
