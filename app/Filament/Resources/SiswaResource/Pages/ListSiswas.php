<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Models\Post;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
// use Filament\Forms\Components\Builder;
use App\Filament\Resources\SiswaResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use App\Filament\Resources\BlogPostResource\Widgets\StatsOverview;
use App\Filament\Resources\BlogPostResource\Widgets\PublishedOverview;

class ListSiswas extends ListRecords
// class ListPosts extends ListRecords
{
    protected static string $resource = SiswaResource::class;


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
        //   PublishedOverview::class,
        ];
    }
    public function getTabs(): array
    {
        return [

            'Published' => Tab::make()
            ->label('Published Article')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('published', 'Published'))
            // ->badge(Post::query()->where('published', 'Published')->count()),

                // 'all' => Tab::make()
                // ->label('All Posts')
                // ->badge(Post::all()->count()),
                // 'Published' => Tab::make()
                //     ->modifyQueryUsing(fn (Builder $query) => $query->where('published', 'Published'))
                //     ->badge(Post::query()->where('published', 'Published')->count()),
                // 'Reviewing' => Tab::make()
                //     ->modifyQueryUsing(fn (Builder $query) => $query->where('published', 'Reviewing'))
                //     ->badge(Post::query()->where('published', 'Reviewing')->count()),
                // 'Draft' => Tab::make()
                //     ->modifyQueryUsing(fn (Builder $query) => $query->where('published', 'Draft'))
                //     ->badge(Post::query()->where('published', 'Draft')->count()),
                // 'Declined' => Tab::make()
                //     ->modifyQueryUsing(fn (Builder $query) => $query->where('published', 'Declined'))
                //     ->badge(Post::query()->where('published', 'Declined')->count()),

                // 'Category' => Tab::make()
                //     ->modifyQueryUsing(fn (Builder $query) => $query->where('category.name', true))
                //     ->badge(Category::query()->where('category.name', true)->count()),
            ];
            // 'all' => Tab::make()
            // ->badge(Post::all()->count()),
            // 'Published' => Tab::make()
            //     ->modifyQueryUsing(fn (Builder $query) => $query->where('published', true))
            //     ->badge(Post::query()->where('published', true)->count()),
            // 'Unpublished' => Tab::make()
            //     ->modifyQueryUsing(fn (Builder $query) => $query->where('published', false))
            //     ->badge(Post::query()->where('published', false)->count())
    }
}
