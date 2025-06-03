<?php

namespace App\Filament\Clusters\Reports\Pages;

use App\Filament\Clusters\Reports;
use App\Filament\Clusters\Reports\Widgets\BookStatsWidget;
use App\Filament\Clusters\Reports\Widgets\TopAuthorsWidget;
use Filament\Pages\Page;

class AuthorsReport extends Page {

    protected static ?string $cluster = Reports::class;

    protected static ?string $title = "Relatório de Autores";
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Autores';

    protected static string $view = 'filament.pages.report.author-report';

    public function getHeaderWidgets(): array
    {
        return [
            TopAuthorsWidget::class,
        ];
    }

}
