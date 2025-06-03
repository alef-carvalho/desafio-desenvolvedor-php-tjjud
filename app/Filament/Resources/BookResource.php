<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Filament\Resources\BookResource\RelationManagers;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookResource extends Resource
{
    protected static ?string $pluralLabel = 'Livros';
    protected static ?string $modelLabel = 'Livro';
    protected static ?string $navigationLabel = 'Livros';
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->label("Nome")
                    ->placeholder("Nome do livro...")
                    ->maxLength(40)
                    ->minLength(3)
                    ->unique(ignoreRecord: true),
                TextInput::make('edition')
                    ->required()
                    ->label("Edição")
                    ->placeholder("Número da edição...")
                    ->numeric()
                    ->minValue(1),
                TextInput::make('publisher')
                    ->required()
                    ->label("Editora")
                    ->placeholder("Nome da editora...")
                    ->maxLength(40)
                    ->minLength(3),
                TextInput::make('published_year')
                    ->required()
                    ->label("Ano de Publicação")
                    ->placeholder("Ex. 2020")
                    ->numeric()
                    ->length(4),
                Select::make('authors')
                    ->label('Autores')
                    ->multiple()
                    ->relationship('authors', 'name')
                    ->preload(),
                Select::make('subjects')
                    ->label('Assuntos')
                    ->multiple()
                    ->relationship('subjects', 'description')
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label("Nome")->searchable(),
                TextColumn::make('edition')->label("Edição")->searchable(),
                TextColumn::make('publisher')->label("Editora")->searchable(),
                TextColumn::make('published_year')->label("Ano")->searchable(),
                TextColumn::make('authors.name')->label('Autores'),
                TextColumn::make('subjects.description')->label('Assuntos'),
                TextColumn::make('created_at')->since()->label("Data de cadastro"),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])->defaultSort('title');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/cadastrar'),
            'edit' => Pages\EditBook::route('/{record}/editar'),
        ];
    }
}
