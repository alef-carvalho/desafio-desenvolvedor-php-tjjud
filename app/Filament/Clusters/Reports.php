<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Pages\SubNavigationPosition;

class Reports extends Cluster
{
    protected static ?string $title = "Relatórios";
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
}
