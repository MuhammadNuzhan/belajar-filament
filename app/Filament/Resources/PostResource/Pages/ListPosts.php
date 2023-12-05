<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Models\Post;
use App\Models\User;
use Filament\Actions;
use App\Models\Category;
use App\Filament\Resources\PostResource;
use Filament\Pages\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords\Tab;
use App\Filament\Resources\BlogPostResource\Widgets\StatsOverview;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
          StatsOverview::class,
        ];
    }
    // public function getTabs(): array
    // {
    //     return [
    //         'all' => Tab::make()
    //         ->badge(Post::all()->count()),
    //         'Published' => Tab::make()
    //             ->modifyQueryUsing(fn (Builder $query) => $query->where('published', true))
    //             ->badge(Post::query()->where('published', true)->count()),
    //         'Unpublished' => Tab::make()
    //             ->modifyQueryUsing(fn (Builder $query) => $query->where('published', false))
    //             ->badge(Post::query()->where('published', false)->count())
    //     ];
    public function getTabs(): array
    {
        return [
            'all' => Tab::make()
            ->label('All Article')
            ->badge(Post::all()->count()),
            // 'Published' => Tab::make()
            //     ->modifyQueryUsing(fn (Builder $query) => $query->where('published', 'Published'))
            //     ->badge(Post::query()->where('published', 'Published')->count()),
            'Reviewing' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('published', 'Reviewing'))
                ->badge(Post::query()->where('published', 'Reviewing')->count()),
            'Draft' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('published', 'Draft'))
                ->badge(Post::query()->where('published', 'Draft')->count()),
            'Declined' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('published', 'Declined'))
                ->badge(Post::query()->where('published', 'Declined')->count()),
            // 'Category' => Tab::make()
            //     ->modifyQueryUsing(fn (Builder $query) => $query->where('category.name', true))
            //     ->badge(Category::query()->where('category.name', true)->count()),
        ];
    }

}
