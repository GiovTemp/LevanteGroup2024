<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;

class HomeController extends Controller
{
    public function home()
    {
        return view('home');
    }

    public function searchArticle(Request $request)
    {
        $query = $request->input('query');
        $page = $request->input('page', 1);
        
        // Genera una chiave di cache unica basata sulla query e sulla pagina
        $cacheKey = 'articles.search.' . md5($query) . '.page_' . $page;
        $cacheTags = ['articles', 'search'];

        // Determina la durata della cache
        $cacheDuration = env('CACHE_DURATION', 60);


        if (Cache::tags($cacheTags)->has($cacheKey)) {
            Log::info("Cache hit for key: {$cacheKey}");
        } else {
            Log::info("Cache miss for key: {$cacheKey}");
        }

        // Recupera gli articoli dalla cache o esegue la ricerca se la cache non esiste
        $articles = Cache::tags($cacheTags)->remember($cacheKey, $cacheDuration, function () use ($query) {
            Log::info("Executing search query: {$query}");
            return Article::search($query)->paginate(16);
        });
        

        return view('articles.search', compact('articles', 'query'));
    }
}
