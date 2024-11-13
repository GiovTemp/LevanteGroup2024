<?php

namespace App\Filament\Resources\ArgumentResource\Pages;

use Filament\Actions;
use App\Models\Argument;
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Wizard\Step;
use App\Filament\Resources\ArgumentResource;
use Filament\Forms\Components\MarkdownEditor;

class ListArguments extends ListRecords
{
    protected static string $resource = ArgumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
    
            Action::make('create')
                ->label('Crea Argomento')
                ->icon('heroicon-o-plus-circle')
                ->steps([
                    Step::make('Titolo')
                        ->description('Dai un titolo al tuo argomento')
                        ->schema([
                            TextInput::make('title')
                                ->label('Titolo')
                                ->maxLength(255)
                                ->required()
                        ]),
    
                    Step::make('Contenuto')
                        ->description('Spiega cosa dovrebbe trattare gli articoli generati da questo argomento')
                        ->schema([
                            MarkdownEditor::make('content')
                                ->label('Descrizione')
                                ->required(),
                        ]),
    
                    Step::make('Numero di generazioni')
                        ->description('Decidi quanti articoli generare per questo argomento')
                        ->schema([
                            TextInput::make('to_generate')
                                ->label('Numero di generazioni (1-5)')
                                ->required()
                                ->numeric()
                                ->maxValue(5)
                                ->minValue(1)
                                ->default(1)
                        ]),
                ])
                ->action(function (array $data) {
                    // Crea il nuovo record nell'argomento
                    Argument::create([
                        'title' => $data['title'],
                        'content' => $data['content'],
                        'to_generate' => $data['to_generate'],
                    ]);
    
                    // Opzionale: Invia una notifica di successo
                    Notification::make()
                        ->title('Argomento creato con successo')
                        ->success()
                        ->send();
                })
                ->modalHeading('Crea un Nuovo Argomento')
                ->modalWidth('xxl'),
        ];
    }
    
}
