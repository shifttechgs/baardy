<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

/**
 * Log an enquiry that came in by phone, WhatsApp or at a branch, so the
 * funnel counts every lead, not only the website's. It goes through the
 * same Lead::capture() as the website: a number that already has an open
 * lead is added to it rather than duplicated. The person logging it is
 * assigned to it.
 */
class CreateLead extends CreateRecord
{
    protected static string $resource = LeadResource::class;

    protected static bool $canCreateAnother = false;

    public function getTitle(): string
    {
        return 'Log a lead';
    }

    public function getSubheading(): ?string
    {
        return 'Phone call, WhatsApp or walk-in? Log it here so it counts in the funnel.';
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()->label('Log lead');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $source = $data['source'] ?? null;
        unset($data['source']);

        $lead = Lead::capture($data, ['utm_source' => $source, 'utm_medium' => 'offline']);

        if ($lead->assigned_to === null) {
            $lead->assignTo(auth()->user(), auth()->user());
        }

        return $lead;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title($this->record->wasRecentlyCreated
                ? 'Lead '.$this->record->reference.' logged'
                : 'This number already has an open lead, so the enquiry was added to it');
    }

    protected function getRedirectUrl(): string
    {
        return LeadResource::getUrl('view', ['record' => $this->record]);
    }
}
