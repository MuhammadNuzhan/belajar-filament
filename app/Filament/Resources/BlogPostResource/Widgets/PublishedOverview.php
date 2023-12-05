<?php

namespace App\Filament\Resources\BlogPostResource\Widgets;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class PublishedOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [

            Stat::make('Published Article', Post::query()->where('published', 'Published')->count())
            ->description('Increare published article')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('success')
            ->chart([2, 7, 3, 5, 3, 8, 8, 10])
            ->extraAttributes([
                'class' => 'cursor-pointer',
            ]),

        ];
    }
}
