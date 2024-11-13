<?php

namespace App\Filament\Resources\CommentResource\Pages;

use App\Filament\Resources\CommentResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;

class ViewComment extends ViewRecord
{
    protected static string $resource = CommentResource::class;

    protected function getActions(): array
    {
        return [
            Action::make('Accetta')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(function () {
                    $this->record->update([
                        'is_accepted' => true,
                        'is_rejected' => false,
                    ]);
                    $this->notify('success', 'Commento accettato con successo.');
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record->getKey()]));
                })
                ->requiresConfirmation()
                ->visible(fn () => is_null($this->record->is_accepted) || !$this->record->is_accepted),
            Action::make('Rifiuta')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->action(function () {
                    $this->record->update([
                        'is_accepted' => false,
                        'is_rejected' => true,
                    ]);
                    $this->notify('success', 'Commento rifiutato con successo.');
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record->getKey()]));
                })
                ->requiresConfirmation()
                ->visible(fn () => is_null($this->record->is_accepted) || $this->record->is_accepted),
            Action::make('Riporta in revisione')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('warning')
                ->action(function () {
                    $this->record->update([
                        'is_accepted' => null,
                        'is_rejected' => false,
                    ]);
                    $this->notify('success', 'Commento riportato in revisione.');
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record->getKey()]));
                })
                ->requiresConfirmation()
                ->visible(fn () => !is_null($this->record->is_accepted)),
            Action::make('Elimina')
                ->action('delete')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation(),
        ];
    }
}
