<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * `is_admin` and `can_approve_promotions` are deliberately not fillable: they
 * are granted, never mass-assigned from a form.
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'can_approve_promotions' => 'boolean',
        ];
    }

    /**
     * Only admins sign in to the admin panel -- locally as well as in
     * production, so the rule is exercised before it matters.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Cast: a user not yet reloaded from the database has no value set.
        return (bool) $this->is_admin;
    }
}
