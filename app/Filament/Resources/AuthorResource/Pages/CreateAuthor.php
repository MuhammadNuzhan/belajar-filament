<?php

namespace App\Filament\Resources\AuthorResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use App\Filament\Resources\AuthorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAuthor extends CreateRecord
{
    protected static string $resource = AuthorResource::class;

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
