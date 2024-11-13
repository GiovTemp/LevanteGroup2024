<?php

// app/Console/Commands/GenerateSitemap.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Genera la sitemap del sito';

    public function handle()
    {
        $sitemap = Sitemap::create();

        // Aggiungi la homepage
        $sitemap->add(
            Url::create(route('home'))
                ->setPriority(1.0)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
        );

        // Aggiungi pagine statiche con priorità minore
        $sitemap->add(
            Url::create(route('login'))
                ->setPriority(0.5)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
        );

        $sitemap->add(
            Url::create(route('register'))
                ->setPriority(0.5)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
        );

        // Aggiungi tutte le categorie
        Category::all()->each(function (Category $category) use ($sitemap) {
            $sitemap->add(
                Url::create(route('articles.byCategory', $category->slug))
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.7)
            );
        });

        // Aggiungi tutti gli articoli pubblicati
        Article::where('is_published', true)
               ->where('published_at', '<', now())
               ->get()
               ->each(function (Article $article) use ($sitemap) {
                    $sitemap->add(
                        Url::create(route('articles.show', $article->slug))
                            ->setLastModificationDate($article->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.8)
                    );
               });

        // Salva la sitemap
        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generata con successo!');
        Log::info('Sitemap generata con successo!');
    }
}
