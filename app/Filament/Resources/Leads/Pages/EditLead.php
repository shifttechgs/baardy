<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Correct a lead's contact details. The funnel is moved from the lead's own
 * page, not here; saving or cancelling returns there.
 */
class EditLead extends EditRecord
{
    protected static string $resource = LeadResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Edit '.$this->getRecord()->name;
    }

    public function getSubheading(): string|Htmlable|null
    {
        /** @var Lead $lead */
        $lead = $this->getRecord();

        return "{$lead->reference} · Correct how to reach them or what they asked for.";
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to lead')
                ->icon(Heroicon::OutlinedArrowLeft)
                ->color('gray')
                ->url(fn (): string => $this->getRedirectUrl()),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()->label('Save changes');
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->url($this->getRedirectUrl());
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()->success()->title('Contact details saved');
    }

    protected function getRedirectUrl(): string
    {
        return LeadResource::getUrl('view', ['record' => $this->record]);
    }
}
