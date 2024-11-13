<?php

namespace App\Filament\Resources\GeneratedArticleResource\Pages;

use App\Filament\Resources\GeneratedArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGeneratedArticle extends EditRecord
{
    protected static string $resource = GeneratedArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
