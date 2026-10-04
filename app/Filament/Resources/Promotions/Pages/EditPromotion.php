<?php

namespace App\Filament\Resources\Promotions\Pages;

use App\Filament\Resources\Promotions\PromotionResource;
use App\Models\Promotion;
use App\PromotionStatus;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

/**
 * Everything happens from the page header, top right, in view from the start:
 *
 *   primary               approvers: "Save and publish" -- or "Approve and
 *                         publish", with a confirmation, when reviewing a
 *                         submission; it records who approved it and when.
 *                         Everyone else: "Save and submit for approval".
 *   Save                  saves without changing the status
 *   ... menu              View on site (while live), Return to draft (takes
 *                         it off the site), Delete
 *
 * Saving changes to an approved promotion's content un-approves it (see
 * Promotion::booted()); the editor is told so, rather than finding out when
 * the promotion quietly disappears from the site.
 */
class EditPromotion extends EditRecord
{
    protected static string $resource = PromotionResource::class;

    /** Set while a save is immediately followed by re-publishing or submitting. */
    protected bool $statusFollowsSave = false;

    /**
     * @return array<Action|ActionGroup>
     */
    protected function getHeaderActions(): array
    {
        $isReviewing = fn (Promotion $record): bool => $record->status === PromotionStatus::AwaitingApproval;

        return [
            ActionGroup::make([
                Action::make('viewOnSite')
                    ->label('View on site')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (Promotion $record): string => route('promotions.show', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (Promotion $record): bool => $record->isLive()),

                Action::make('returnToDraft')
                    ->label('Return to draft')
                    ->icon(Heroicon::OutlinedArrowUturnLeft)
                    ->requiresConfirmation()
                    ->modalDescription('It comes off the site until it is published again.')
                    ->visible(fn (Promotion $record): bool => $record->status !== PromotionStatus::Draft)
                    ->action(function (Promotion $record): void {
                        $record->status = PromotionStatus::Draft;
                        $record->approved_by = null;
                        $record->approved_at = null;
                        $record->save();

                        Notification::make()->title('Returned to draft')->send();
                    }),

                DeleteAction::make(),
            ])
                ->icon(Heroicon::EllipsisHorizontal)
                ->color('gray')
                ->button()
                ->label('More'),

            $this->getCancelFormAction()->link(),

            $this->getSaveFormAction()
                ->label('Save')
                ->color('gray')
                ->submit(null)
                ->action(fn () => $this->save())
                ->keyBindings($this->canApprove() ? null : ['mod+s']),

            $this->canApprove()
                ? Action::make('saveAndPublish')
                    ->label(fn (Promotion $record): string => $isReviewing($record) ? 'Approve and publish' : 'Save and publish')
                    ->requiresConfirmation($isReviewing)
                    ->modalHeading('Approve this promotion?')
                    ->modalDescription('It will appear on the site between its start and end dates, exactly as it is now. Check the terms before approving.')
                    ->modalSubmitActionLabel('Approve and publish')
                    ->keyBindings(['mod+s'])
                    ->action(fn () => $this->saveAndPublish())
                : Action::make('saveAndSubmit')
                    ->label('Save and submit for approval')
                    ->action(fn () => $this->saveAndSubmit()),
        ];
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [];
    }

    public function saveAndPublish(): void
    {
        abort_unless($this->canApprove(), 403);

        $this->statusFollowsSave = true;
        $this->save(shouldSendSavedNotification: false);

        $this->record->status = PromotionStatus::Approved;
        $this->record->approved_by = auth()->id();
        $this->record->approved_at = now();
        $this->record->save();

        Notification::make()->title('Saved and published')->success()->send();
    }

    public function saveAndSubmit(): void
    {
        $this->statusFollowsSave = true;
        $this->save(shouldSendSavedNotification: false);

        $this->record->status = PromotionStatus::AwaitingApproval;
        $this->record->save();

        Notification::make()->title('Saved and submitted for approval')->success()->send();
    }

    protected function afterSave(): void
    {
        if (! $this->statusFollowsSave && $this->record->wasChanged('status') && $this->record->status === PromotionStatus::Draft) {
            Notification::make()
                ->title('Changes need approval again')
                ->body('This promotion was approved, so editing it has taken it off the site until it is re-approved.')
                ->warning()
                ->persistent()
                ->send();
        }
    }

    private function canApprove(): bool
    {
        return (bool) auth()->user()?->can_approve_promotions;
    }
}
