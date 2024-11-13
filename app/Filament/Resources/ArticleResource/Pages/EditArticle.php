<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ArticleResource;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),

                        // Azione personalizzata per l'anteprima
            Action::make('preview')
                ->label('Anteprima') // Etichetta del pulsante
                ->url(fn () => route('articles.show', $this->record)) // URL della rotta di anteprima
                ->openUrlInNewTab() // Apre l'URL in una nuova scheda
                ->icon('heroicon-o-eye'), 
        
        ];
    }
}
