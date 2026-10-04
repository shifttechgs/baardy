<?php

namespace App\Filament\Resources\Promotions\Pages;

use App\Filament\Resources\Promotions\PromotionResource;
use App\PromotionStatus;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

/**
 * Creating a promotion ends in one click, not three:
 *
 *   Publish               approvers only: creates it already approved, so it
 *                         goes live on its start date; records who and when
 *   Submit for approval   creates it and puts it in the sign-off queue
 *   Save draft            creates it for later
 *
 * They sit in the page header, top right. Everyone lands back on the list
 * with a message saying what happened.
 */
class CreatePromotion extends CreateRecord
{
    protected static string $resource = PromotionResource::class;

    protected static bool $canCreateAnother = false;

    /** The status the record is given once created. */
    protected PromotionStatus $statusOnCreate = PromotionStatus::Draft;

    public function getTitle(): string
    {
        return 'New promotion';
    }

    /**
     * The finishing actions sit in the page header, top right, so they are in
     * view from the start rather than under the whole form.
     *
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        $canApprove = $this->canApprove();

        return array_values(array_filter([
            $this->getCancelFormAction()->link(),
            Action::make('saveDraft')
                ->label('Save draft')
                ->action(fn () => $this->saveDraft())
                ->color('gray'),
            Action::make('submitForApproval')
                ->label('Submit for approval')
                ->action(fn () => $this->submitForApproval())
                ->color($canApprove ? 'gray' : 'primary'),
            $canApprove
                ? Action::make('publish')->label('Publish')->action(fn () => $this->publish())->keyBindings(['mod+s'])
                : null,
        ]));
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [];
    }

    public function publish(): void
    {
        abort_unless($this->canApprove(), 403);

        $this->statusOnCreate = PromotionStatus::Approved;
        $this->create();
    }

    public function submitForApproval(): void
    {
        $this->statusOnCreate = PromotionStatus::AwaitingApproval;
        $this->create();
    }

    public function saveDraft(): void
    {
        $this->statusOnCreate = PromotionStatus::Draft;
        $this->create();
    }

    protected function afterCreate(): void
    {
        $this->record->status = $this->statusOnCreate;

        if ($this->statusOnCreate === PromotionStatus::Approved) {
            $this->record->approved_by = auth()->id();
            $this->record->approved_at = now();
        }

        $this->record->save();
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return match ($this->statusOnCreate) {
            PromotionStatus::Approved => $this->record->starts_at->isFuture()
                ? 'Published. It goes live on '.$this->record->starts_at->format('j M Y').'.'
                : 'Published. It is live on the site now.',
            PromotionStatus::AwaitingApproval => 'Submitted for approval.',
            PromotionStatus::Draft => 'Saved as a draft.',
        };
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    private function canApprove(): bool
    {
        return (bool) auth()->user()?->can_approve_promotions;
    }
}
