<?php

namespace App\Observers;

use App\Models\Article;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ArticleObserver
{
    /**
     * Handle the Article "updated" event.
     *
     * @param  \App\Models\Article  $article
     * @return void
     */
    public function updated(Article $article)
    {
        if ($article->shouldBeSearchable()) {
            $article->searchable();
        } else {
            $article->unsearchable();
        }

        // Invalida la cache generale degli articoli
        Cache::tags('articles')->flush();

        // Invalida la cache specifica della categoria
        if ($article->category) {
            Cache::tags(['articles', 'category:' . $article->category->id])->flush();
        }

        // Invalida la cache specifica dei tag
        foreach ($article->tags as $tag) {
            Cache::tags(['articles', 'tag:' . $tag->id])->flush();
        }

        // Invalida la cache specifica dell'utente
        if ($article->user) {
            Cache::tags(['articles', 'user:' . $article->user->id])->flush();
        }
    }

    /**
     * Handle the Article "created" event.
     *
     * @param  \App\Models\Article  $article
     * @return void
     */
    public function created(Article $article)
    {
        if ($article->shouldBeSearchable()) {
            $article->searchable();
        }

        // Invalida la cache generale degli articoli
        Cache::tags('articles')->flush();

        // Invalida la cache specifica della categoria
        if ($article->category) {
            Cache::tags(['articles', 'category:' . $article->category->id])->flush();
        }

        // Invalida la cache specifica dei tag
        foreach ($article->tags as $tag) {
            Cache::tags(['articles', 'tag:' . $tag->id])->flush();
        }

        // Invalida la cache specifica dell'utente
        if ($article->user) {
            Cache::tags(['articles', 'user:' . $article->user->id])->flush();
        }
    }

    /**
     * Handle the Article "deleted" event.
     *
     * @param  \App\Models\Article  $article
     * @return void
     */
    public function deleted(Article $article)
    {
        $article->unsearchable();

        // Invalida la cache generale degli articoli
        Cache::tags('articles')->flush();

        // Invalida la cache specifica della categoria
        if ($article->category) {
            Cache::tags(['articles', 'category:' . $article->category->id])->flush();
        }

        // Invalida la cache specifica dei tag
        foreach ($article->tags as $tag) {
            Cache::tags(['articles', 'tag:' . $tag->id])->flush();
        }

        // Invalida la cache specifica dell'utente
        if ($article->user) {
            Cache::tags(['articles', 'user:' . $article->user->id])->flush();
        }

        // Definisci il percorso della cartella dell'articolo
        $directory = 'articles/' . $article->id;

        // Controlla se la directory esiste prima di tentare di eliminarla
        if (Storage::disk('public')->exists($directory)) {
            // Elimina l'intera directory
            Storage::disk('public')->deleteDirectory($directory);

            // Opzionale: Log per confermare l'eliminazione
            Log::info("Directory eliminata: {$directory}");
        } else {
            Log::warning("Tentativo di eliminare una directory inesistente: {$directory}");
        }
    }
}
