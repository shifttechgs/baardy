<?php

namespace App\Models;

use Database\Factories\VacancyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A job opening. Open means published and not past its closing date.
 */
#[Fillable(['title', 'slug', 'location', 'employment_type', 'summary', 'description', 'requirements', 'closes_on', 'is_published'])]
class Vacancy extends Model
{
    /** @use HasFactory<VacancyFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'closes_on' => 'date',
            'is_published' => 'boolean',
        ];
    }

    /**
     * @return HasMany<JobApplication, $this>
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * @param  Builder<Vacancy>  $query
     */
    public function scopeOpen(Builder $query): void
    {
        $query->where('is_published', true)
            ->where(fn (Builder $closing) => $closing->whereNull('closes_on')->orWhere('closes_on', '>=', today()));
    }
}
