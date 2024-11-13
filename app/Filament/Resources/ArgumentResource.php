<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Argument;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\ArgumentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ArgumentResource\RelationManagers;

class ArgumentResource extends Resource
{
    protected static ?string $model = Argument::class;

    protected static ?string $navigationIcon = 'heroicon-c-sparkles';

    protected static ?string $navigationGroup = 'AI';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('Titolo')
                    ->maxLength(255)
                    ->required(),
                MarkdownEditor::make('content')
                    ->label('Descrivi di cosa dovrebbe parlare, quali argomenti dovrebbe trattare,il tono che dovrebbe avere,ecc...')
                    ->required(),
                TextInput::make('to_generate')
                    ->label('Numero di generazioni (1-5)')
                    ->required()
                    ->numeric()
                    ->maxValue(5)
                    ->minValue(1)
                    ->default(1)
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                
            TextColumn::make('id')
                ->label('ID')
                ->sortable(), // Rende la colonna ID ordinabile

            TextColumn::make('title')
                ->label('Title')
                ->sortable()
                ->searchable(),
            
            TextColumn::make('to_generate')
                ->label('Artcoli da generare')
                ->sortable(),

                ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArguments::route('/'),
            'create' => Pages\CreateArgument::route('/create'),
            'edit' => Pages\EditArgument::route('/{record}/edit'),
        ];
    }
}
