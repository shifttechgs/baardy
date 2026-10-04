<?php

namespace App\Filament\Resources\Promotions;

use App\Filament\Resources\Promotions\Pages\CreatePromotion;
use App\Filament\Resources\Promotions\Pages\EditPromotion;
use App\Filament\Resources\Promotions\Pages\ListPromotions;
use App\Filament\Resources\Promotions\Schemas\PromotionForm;
use App\Filament\Resources\Promotions\Tables\PromotionsTable;
use App\Models\Promotion;
use App\PromotionStatus;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PromotionResource extends Resource
{
    protected static ?string $model = Promotion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static ?int $navigationSort = 2;

    /**
     * What is waiting on the owner: promotions needing sign-off (pulses amber)
     * and live ones ending within a week (a quiet blue, no pulse).
     */
    public static function getNavigationBadge(): ?string
    {
        $total = static::awaitingApprovalCount() + static::endingSoonCount();

        return $total > 0 ? (string) $total : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::awaitingApprovalCount() > 0 ? 'warning' : 'info';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        $awaiting = static::awaitingApprovalCount();
        $ending = static::endingSoonCount();

        return collect([
            $awaiting > 0 ? "{$awaiting} awaiting your sign-off" : null,
            $ending > 0 ? "{$ending} ending this week" : null,
        ])->filter()->implode('; ') ?: null;
    }

    public static function awaitingApprovalCount(): int
    {
        return Promotion::query()->where('status', PromotionStatus::AwaitingApproval)->count();
    }

    public static function endingSoonCount(): int
    {
        return Promotion::query()->live()->where('ends_at', '<=', now()->addWeek())->count();
    }

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return PromotionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PromotionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPromotions::route('/'),
            'create' => CreatePromotion::route('/create'),
            'edit' => EditPromotion::route('/{record}/edit'),
        ];
    }
}
