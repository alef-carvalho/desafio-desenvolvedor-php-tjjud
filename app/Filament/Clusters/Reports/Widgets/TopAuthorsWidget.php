<?php

namespace App\Filament\Clusters\Reports\Widgets;

use App\Models\AuthorReport;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;

class TopAuthorsWidget extends BaseWidget
{
    protected static ?string $heading = "Top 10 Autores";
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AuthorReport::query()
                    ->orderBy("books_count", "desc")
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('author_name')->label("Autor"),
                TextColumn::make('books_count')->label("Quantidade"),
            ])
            ->searchable(false);
    }

    public function getTableRecordKey(Model $record): string
    {
        return $record->author_id;
    }
}
