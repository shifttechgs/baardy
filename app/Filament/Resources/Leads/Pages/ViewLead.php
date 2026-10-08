<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\LeadLostReason;
use App\LeadStage;
use App\Models\Lead;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Where a lead is worked. The header holds, left to right:
 *
 *   More (...)       edit contact details, assign, reopen, delete
 *   Call, WhatsApp   one click to the person (WhatsApp opens with a greeting)
 *   Add note         what was said, for whoever picks it up next
 *   Close as lost    with a reason, while the lead is open
 *   next step        the one primary button, moving the lead one stage on:
 *                    Mark contacted -> Application started -> Mark approved
 *
 * Every move is written to the timeline by Lead::moveTo().
 */
class ViewLead extends ViewRecord
{
    protected static string $resource = LeadResource::class;

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->name;
    }

    public function getSubheading(): string|Htmlable|null
    {
        /** @var Lead $lead */
        $lead = $this->getRecord();

        return "{$lead->reference} · {$lead->interest} · {$lead->branch}";
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('editDetails')
                    ->label('Edit contact details')
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->url(fn (Lead $record): string => LeadResource::getUrl('edit', ['record' => $record])),

                Action::make('assign')
                    ->label('Assign')
                    ->icon(Heroicon::OutlinedUser)
                    ->modalIcon(Heroicon::OutlinedUserPlus)
                    ->modalIconColor('primary')
                    ->modalHeading('Assign this lead')
                    ->modalDescription('Choose who follows this lead up. They are named on the lead and in the timeline.')
                    ->modalSubmitActionLabel('Assign')
                    ->schema([
                        Select::make('assigned_to')
                            ->label('Handled by')
                            ->options(fn (): array => User::query()->where('is_admin', true)->orderBy('name')->pluck('name', 'id')->all())
                            ->default(fn (Lead $record): ?int => $record->assigned_to ?? auth()->id())
                            ->placeholder('Nobody'),
                    ])
                    ->action(function (Lead $record, array $data): void {
                        $record->assignTo(filled($data['assigned_to']) ? User::find($data['assigned_to']) : null, auth()->user());

                        Notification::make()->title('Assigned')->success()->send();
                    }),

                Action::make('reopen')
                    ->label('Reopen')
                    ->icon(Heroicon::OutlinedArrowUturnLeft)
                    ->visible(fn (Lead $record): bool => ! $record->stage->isOpen())
                    ->action(function (Lead $record): void {
                        $record->moveTo(LeadStage::Contacted, auth()->user());
                        $this->afterMove('Reopened');
                    }),

                Action::make('closeAsLost')
                    ->label('Close as lost')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->visible(fn (Lead $record): bool => $record->stage->isOpen())
                    ->modalHeading('Close this lead?')
                    ->modalIcon(Heroicon::OutlinedXCircle)
                    ->modalIconColor('warning')
                    ->modalDescription('It leaves the open list. You can reopen it later.')
                    ->modalSubmitActionLabel('Close as lost')
                    ->schema([
                        Select::make('reason')
                            ->label('Why?')
                            ->options(LeadLostReason::class)
                            ->required(),
                        Textarea::make('note')
                            ->label('Note')
                            ->rows(3)
                            ->maxLength(2000),
                    ])
                    ->action(function (Lead $record, array $data): void {
                        $reason = $data['reason'] instanceof LeadLostReason ? $data['reason'] : LeadLostReason::from($data['reason']);

                        $record->moveTo(LeadStage::Lost, auth()->user(), $reason, $data['note'] ?? null);
                        $this->afterMove('Closed as lost');
                    }),

                DeleteAction::make()
                    ->modalDescription('The lead and its timeline are removed for good. Use this for spam, or when the person asks for their details to be deleted.'),
            ])
                ->icon(Heroicon::EllipsisHorizontal)
                ->color('gray')
                ->button()
                ->label('More'),

            Action::make('call')
                ->label('Call')
                ->icon(Heroicon::OutlinedPhone)
                ->color('gray')
                ->url(fn (Lead $record): string => $record->telUrl()),

            Action::make('whatsapp')
                ->label('WhatsApp')
                ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                ->color('gray')
                ->url(fn (Lead $record): string => $record->whatsappUrl(auth()->user()))
                ->openUrlInNewTab(),

            Action::make('addNote')
                ->label('Add note')
                ->icon(Heroicon::OutlinedPencil)
                ->color('gray')
                ->modalIcon(Heroicon::OutlinedPencilSquare)
                ->modalIconColor('primary')
                ->modalHeading('Add a note')
                ->modalDescription('Everyone on the team sees it on the timeline of this lead.')
                ->modalSubmitActionLabel('Add note')
                ->schema([
                    Textarea::make('body')
                        ->hiddenLabel()
                        ->placeholder('What was said, what happens next.')
                        ->rows(4)
                        ->required()
                        ->maxLength(2000),
                ])
                ->action(function (Lead $record, array $data): void {
                    $record->addNote($data['body'], auth()->user());

                    Notification::make()->title('Note added')->success()->send();
                }),

            Action::make('advance')
                ->label(fn (Lead $record): string => match ($record->stage) {
                    LeadStage::New => 'Mark contacted',
                    LeadStage::Contacted => 'Application started',
                    default => 'Mark approved',
                })
                ->icon(fn (Lead $record): Heroicon => match ($record->stage) {
                    LeadStage::New => Heroicon::OutlinedPhone,
                    default => Heroicon::OutlinedDocumentText,
                })
                ->visible(fn (Lead $record): bool => in_array($record->stage, [LeadStage::New, LeadStage::Contacted], true))
                ->action(function (Lead $record): void {
                    $next = $record->stage === LeadStage::New ? LeadStage::Contacted : LeadStage::Application;

                    $record->moveTo($next, auth()->user());
                    $this->afterMove('Moved to '.$next->getLabel());
                }),

            Action::make('approve')
                ->label('Mark approved')
                ->icon(Heroicon::OutlinedCheckBadge)
                ->visible(fn (Lead $record): bool => $record->stage === LeadStage::Application)
                ->requiresConfirmation()
                ->modalIcon(Heroicon::OutlinedCheckBadge)
                ->modalIconColor('success')
                ->modalHeading('Mark this loan as approved?')
                ->modalDescription('The lead closes as won and counts towards the funnel\'s conversions.')
                ->modalSubmitActionLabel('Mark approved')
                ->action(function (Lead $record): void {
                    $record->moveTo(LeadStage::Approved, auth()->user());
                    $this->afterMove('Moved to '.LeadStage::Approved->getLabel());
                }),
        ];
    }

    private function afterMove(string $message): void
    {
        Notification::make()->title($message)->success()->send();

        $this->dispatch('refresh-sidebar');
    }
}
