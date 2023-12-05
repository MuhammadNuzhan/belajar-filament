<?php

namespace App\Filament\Resources\IssueResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use App\Filament\Resources\IssueResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIssue extends CreateRecord
{
    protected static string $resource = IssueResource::class;

    protected function getFormActions(): array
    {
        return array_merge(parent::getFormActions(), [
            Actions\Action::make('clear')
                ->action(function () {
                    $this->form->fill();

                    Notification::make()
                    ->title('The form has been cleared')
                    ->success()
                    ->send();
                })
            ],
        );
    }
}
