<?php

namespace App\Filament\Resources\GeneratedArticleResource\Pages;

use App\Filament\Resources\GeneratedArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGeneratedArticles extends ListRecords
{
    protected static string $resource = GeneratedArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}
