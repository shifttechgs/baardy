<?php

namespace App\Filament\Resources\JobApplications\Pages;

use App\Filament\Resources\JobApplications\JobApplicationResource;
use App\Models\JobApplication;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

/**
 * One applicant, CV first. The header is three controls: a Contact menu (call,
 * WhatsApp, email), a More menu (delete), and the one primary button, Mark
 * reviewed, while the application is still waiting. Viewing and downloading
 * the CV sit on the CV card itself.
 */
class ViewJobApplication extends ViewRecord
{
    protected static string $resource = JobApplicationResource::class;

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->name;
    }

    public function getSubheading(): string|Htmlable|null
    {
        /** @var JobApplication $application */
        $application = $this->getRecord();

        return ($application->vacancy?->title ?? 'General application').' · received '.$application->created_at->diffForHumans();
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('call')
                    ->label('Call')
                    ->icon(Heroicon::OutlinedPhone)
                    ->url(fn (JobApplication $record): string => $record->telUrl()),

                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                    ->url(fn (JobApplication $record): string => $record->whatsappUrl())
                    ->openUrlInNewTab(),

                Action::make('email')
                    ->label('Email')
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->visible(fn (JobApplication $record): bool => filled($record->email))
                    ->url(fn (JobApplication $record): string => 'mailto:'.$record->email),
            ])
                ->icon(Heroicon::OutlinedPhone)
                ->color('gray')
                ->button()
                ->label('Contact'),

            ActionGroup::make([
                DeleteAction::make()
                    ->modalDescription('The application and its CV are removed for good.'),
            ])
                ->icon(Heroicon::EllipsisHorizontal)
                ->color('gray')
                ->button()
                ->label('More'),

            JobApplicationResource::markReviewedAction(),
        ];
    }
}
