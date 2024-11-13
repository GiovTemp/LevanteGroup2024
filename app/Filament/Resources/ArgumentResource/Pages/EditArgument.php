<?php

namespace App\Filament\Resources\ArgumentResource\Pages;

use App\Filament\Resources\ArgumentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArgument extends EditRecord
{
    protected static string $resource = ArgumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
