<?php

namespace App\Filament\Clusters\Reports\Pages;

use App\Filament\Clusters\Reports;
use App\Filament\Clusters\Reports\Widgets\BookStatsWidget;
use Filament\Pages\Page;

class BookReport extends Page {

    protected static ?string $cluster = Reports::class;

    protected static ?string $title = "Relatório de Livros";
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $navigationLabel = 'Livros';

    protected static string $view = 'filament.pages.report.book-report';

    public function getHeaderWidgets(): array
    {
        return [
            BookStatsWidget::class,
        ];
    }

}
