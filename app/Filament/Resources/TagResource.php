<?php

namespace App\Filament\Resources;

use App\Models\Tag;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TagResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TagResource\RelationManagers;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                ->label('Name')
                ->required()
                ->maxLength(255)
                ->debounce(500) 
                ->afterStateUpdated(fn (string $state, callable $set) => $set('slug', Str::slug($state))), // Genera lo slug automaticamente

                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->unique(Tag::class, 'slug', ignoreRecord: true),
                ]);
        }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

            TextColumn::make('id')
                ->label('ID')
                ->sortable(), // Rende la colonna ID ordinabile

            TextColumn::make('name')
                ->label('Name')
                ->sortable()
                ->searchable(),
            
            TextColumn::make('slug')
                ->label('Slug'),
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
            'index' => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'edit' => Pages\EditTag::route('/{record}/edit'),
        ];
    }
}
