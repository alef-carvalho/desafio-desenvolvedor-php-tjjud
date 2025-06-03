<?php

namespace App\Filament\Clusters\Reports\Widgets;

use App\Models\Author;
use App\Models\Book;
use App\Models\Subject;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookStatsWidget extends BaseWidget
{

    protected function getStats(): array
    {
        return [
            Stat::make('Livros', Book::count())
                ->icon('heroicon-o-book-open'),
            Stat::make('Autores', Author::count())
                ->icon('heroicon-o-user-group'),
            Stat::make('Assuntos', Subject::count())
                ->icon('heroicon-o-tag'),
        ];
    }
}
