<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\CategoryResource;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

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
