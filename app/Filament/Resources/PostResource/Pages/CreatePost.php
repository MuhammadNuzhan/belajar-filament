<?php

namespace App\Filament\Resources\PostResource\Pages;

use Filament\Actions;
use App\Filament\Resources\PostResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

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
