<?php

namespace App\Filament\Resources\IssueResource\Pages;

use App\Models\Issue;
use Filament\Actions;
use App\Filament\Resources\IssueResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords\Tab;

class ListIssues extends ListRecords
{
    protected static string $resource = IssueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            // 'all' => Tab::make()
            // ->label('All Posts')
            // ->badge(Issue::all()->count()),
            'Future Issues' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Future Issue'))
                ->badge(Issue::query()->where('status', 'Future Issue')->count()),
            'Back Issues' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Back Issue'))
                ->badge(Issue::query()->where('status', 'Back Issue')->count()),

            // 'Category' => Tab::make()
            //     ->modifyQueryUsing(fn (Builder $query) => $query->where('category.name', true))
            //     ->badge(Category::query()->where('category.name', true)->count()),
        ];
    }

}
