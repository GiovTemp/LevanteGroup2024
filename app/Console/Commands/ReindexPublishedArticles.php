<?php
// app/Console/Commands/ReindexPublishedArticles.php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReindexPublishedArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:reindex-published';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reindex articles that have become published as of today';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today();

        // Trova articoli che ora dovrebbero essere indicizzati
        $articlesToIndex = Article::where('is_published', true)
            ->where('published_at', '<=', $today)
            ->get();

        if ($articlesToIndex->isEmpty()) {
            $this->info('Nessun articolo da indicizzare.');
            return 0;
        }

        // Indicizza gli articoli trovati
        foreach ($articlesToIndex as $article) {
            $article->searchable();
            $this->info("Indicizzato l'articolo ID: {$article->id} - {$article->title}");
        }

        $this->info('Reindicizzazione completata.');
        Log::info('Reindicizzazione completata.');
        return 0;
    }
}
