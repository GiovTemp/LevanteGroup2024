<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Article;
use App\Models\Comment;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\BelongsToSelect;
use App\Filament\Resources\CommentResource\Pages;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Commenti';

    protected static ?string $navigationGroup = 'Gestione Contenuti';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Poiché non modifichiamo i commenti, possiamo mostrare solo le informazioni
                Forms\Components\Section::make([
                    Textarea::make('content')
                        ->label('Contenuto del Commento')
                        ->disabled(),
                    Select::make('user_id')
                        ->label('Utente')
                        ->relationship('user', 'name')
                        ->disabled(),

                    Select::make('article_id')
                        ->label('Articolo')
                        ->relationship('article', 'title')
                        ->disabled(),
                    
                    Placeholder::make('status')
                        ->label('Stato')
                        ->content(function (Comment $record) {
                            if (is_null($record->is_accepted)) {
                                return 'In Attesa';
                            }
                            return $record->is_accepted ? 'Accettato' : 'Rifiutato';
                        }),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('user.name')->label('Utente')->searchable(),
                TextColumn::make('article.title')->label('Articolo')->limit(30)->tooltip(fn ($record) => $record->article->title),
                TextColumn::make('content')->label('Commento')->limit(50)->tooltip(fn ($record) => $record->content),
                TextColumn::make('status')
                ->label('Stato')
                ->badge()
                ->getStateUsing(function (Comment $record) {
                    if (is_null($record->is_accepted)) {
                        return 'In Attesa';
                    }
                    return $record->is_accepted ? 'Accettato' : 'Rifiutato';
                })
                ->colors([
                    'warning' => 'In Attesa',
                    'success' => 'Accettato',
                    'danger'  => 'Rifiutato',
                ]),
            
                TextColumn::make('created_at')->label('Creato il')->sortable()->dateTime('d/m/Y'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Stato')
                    ->options([
                        'pending'  => 'In Attesa',
                        'accepted' => 'Accettato',
                        'rejected' => 'Rifiutato',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'accepted') {
                            $query->where('is_accepted', true);
                        } elseif ($data['value'] === 'rejected') {
                            $query->where('is_accepted', false);
                        } elseif ($data['value'] === 'pending') {
                            $query->whereNull('is_accepted');
                        }
                    })
                    ->default('pending'),
                Filter::make('created_at')
                    ->label('Intervallo di Date')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Da'),
                        Forms\Components\DatePicker::make('created_until')->label('A'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['created_from']) {
                            $query->whereDate('created_at', '>=', $data['created_from']);
                        }
                        if ($data['created_until']) {
                            $query->whereDate('created_at', '<=', $data['created_until']);
                        }
                    }),

                SelectFilter::make('user_id')
                    ->label('Utente')
                    ->relationship('user', 'name')
                    ->searchable(),
                
                // Filtro per Articolo
                SelectFilter::make('article_id')
                ->label('Articolo')
                ->relationship('article', 'title')
                ->searchable(), 
        
            ])
            ->actions([
                Action::make('Accetta')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (Comment $record) {
                        $record->update([
                            'is_accepted' => true,
                        ]);
                    })
                    ->requiresConfirmation()
                    ->visible(fn (Comment $record) => is_null($record->is_accepted) || !$record->is_accepted),

                Action::make('Rifiuta')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(function (Comment $record) {
                        $record->update([
                            'is_accepted' => false,

                        ]);
                    })
                    ->requiresConfirmation()
                    ->visible(fn (Comment $record) => is_null($record->is_accepted) || $record->is_accepted),

                Action::make('Riporta in revisione')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->action(function (Comment $record) {
                        $record->update([
                            'is_accepted' => null,
                            'is_rejected' => false,
                        ]);
                    })
                    ->requiresConfirmation()
                    ->visible(fn (Comment $record) => !is_null($record->is_accepted)),
                Tables\Actions\ViewAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('Accetta Selezionati')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                $record->update([
                                    'is_accepted' => true,

                                ]);
                            }
                        })
                        ->requiresConfirmation()
                        ->color('success')
                        ->icon('heroicon-o-check-circle'),
                    Tables\Actions\BulkAction::make('Rifiuta Selezionati')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                $record->update([
                                    'is_accepted' => false,

                                ]);
                            }
                        })
                        ->requiresConfirmation()
                        ->color('danger')
                        ->icon('heroicon-o-x-circle'),

                    Tables\Actions\BulkAction::make('Riporta in revisione Selezionati')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                $record->update([
                                    'is_accepted' => null,
                                ]);
                            }
                        })
                        ->requiresConfirmation()
                        ->color('warning')
                        ->icon('heroicon-o-arrow-uturn-left'),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Definisci eventuali relazioni se necessario
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            // Rimuovi le pagine di creazione e modifica
            // 'create' => Pages\CreateComment::route('/create'),
            // 'edit' => Pages\EditComment::route('/{record}/edit'),
            'view' => Pages\ViewComment::route('/{record}'),
        ];
    }


}
