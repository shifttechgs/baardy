<?php

namespace App\Filament\Resources\Leads;

use App\Filament\Resources\Leads\Pages\CreateLead;
use App\Filament\Resources\Leads\Pages\EditLead;
use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Filament\Resources\Leads\Pages\ViewLead;
use App\Filament\Resources\Leads\Schemas\LeadForm;
use App\Filament\Resources\Leads\Schemas\LeadInfolist;
use App\Filament\Resources\Leads\Tables\LeadsTable;
use App\LeadStage;
use App\Models\Lead;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

/**
 * Leads: everyone who asked to be called back, and where each one is in the
 * funnel. The sidebar badge counts leads nobody has spoken to yet, and turns
 * red once any of them has waited longer than the response target.
 */
class LeadResource extends Resource
{
    /** How long a new lead may wait for a first call before it is overdue. */
    public const RESPONSE_TARGET_HOURS = 2;

    protected static ?string $model = Lead::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return LeadForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LeadInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeadsTable::configure($table);
    }

    /**
     * @return list<string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'reference', 'phone', 'email'];
    }

    /** An application with no movement for this many days is "quiet". */
    public const QUIET_APPLICATION_DAYS = 3;

    /**
     * The sidebar badge counts what is waiting on someone: leads nobody has
     * called yet, plus applications that have gone quiet. It pulses (red, or
     * amber when only applications are quiet) once something is overdue.
     */
    public static function getNavigationBadge(): ?string
    {
        $waiting = Lead::query()->where('stage', LeadStage::New)->count() + static::quietApplicationCount();

        return $waiting > 0 ? (string) $waiting : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return match (true) {
            static::overdueCount() > 0 => 'danger',
            static::quietApplicationCount() > 0 => 'warning',
            default => 'primary',
        };
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        $overdue = static::overdueCount();
        $quiet = static::quietApplicationCount();

        return collect([
            $overdue > 0 ? "{$overdue} waiting longer than ".static::RESPONSE_TARGET_HOURS.' hours for a first call' : null,
            $quiet > 0 ? "{$quiet} application".($quiet === 1 ? '' : 's').' quiet for '.static::QUIET_APPLICATION_DAYS.' days or more' : null,
        ])->filter()->implode('; ') ?: 'New leads, not yet contacted';
    }

    public static function quietApplicationCount(): int
    {
        return Lead::query()
            ->where('stage', LeadStage::Application)
            ->where('updated_at', '<', now()->subDays(static::QUIET_APPLICATION_DAYS))
            ->count();
    }

    public static function overdueCount(): int
    {
        return Lead::query()
            ->where('stage', LeadStage::New)
            ->where('created_at', '<', now()->subHours(static::RESPONSE_TARGET_HOURS))
            ->count();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeads::route('/'),
            'create' => CreateLead::route('/create'),
            'view' => ViewLead::route('/{record}'),
            'edit' => EditLead::route('/{record}/edit'),
        ];
    }
}
