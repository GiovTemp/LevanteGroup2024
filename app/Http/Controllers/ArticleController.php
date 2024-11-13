<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\User;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ArticleController extends Controller
{

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        if($article->is_published){
            views($article)->cooldown(1)->record();
        }


        return view('articles.show',compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        //
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cacheKey = 'articles.index.page_' . request('page', 1);
        $cacheTags = ['articles'];

        $articles = Cache::tags($cacheTags)->remember($cacheKey,env('CACHE_DURATION', 60), function () use ($cacheKey) {
            Log::info("Cache miss for key: {$cacheKey}");
            return Article::with(['category', 'tags'])
                        ->where('is_published', true)
                        ->where('published_at', '<=', now())
                        ->paginate(16);
        });

        if (Cache::has($cacheKey)) {
            Log::info("Cache hit for key: {$cacheKey}");
        }

        return view('articles.index', compact('articles'));
    }

    /**
     * Display a listing of articles by category.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\View\View
     */
    public function byCategory(Category $category)
    {
        views($category)->cooldown(1)->record();
        $cacheKey = 'articles.byCategory.' . $category->slug . '.page_' . request('page', 1);
        $cacheTags = ['articles', 'category:' . $category->id];

        $articles = Cache::tags($cacheTags)->remember($cacheKey, env('CACHE_DURATION', 60), function () use ($category,$cacheKey) {
            Log::info("Cache miss for key: {$cacheKey}");
            return $category->public_articles()->with(['category', 'tags'])->paginate(16);
        });

        if (Cache::has($cacheKey)) {
            Log::info("Cache hit for key: {$cacheKey}");
        }

        $search = $category->name;

        return view('articles.index', compact('articles', 'search'));
    }

    /**
     * Display a listing of articles by tag.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\View\View
     */
    public function byTag(Tag $tag)
    {
        views($tag)->cooldown(1)->record();
        $cacheKey = 'articles.byTag.' . $tag->slug . '.page_' . request('page', 1);
        $cacheTags = ['articles', 'tag:' . $tag->id];

        $articles = Cache::tags($cacheTags)->remember($cacheKey, env('CACHE_DURATION', 60), function () use ($tag,$cacheKey) {
            Log::info("Cache miss for key: {$cacheKey}");
            return $tag->public_articles()->with(['category', 'tags'])->paginate(16);
        });

        if (Cache::has($cacheKey)) {
            Log::info("Cache hit for key: {$cacheKey}");
        }

        $search = $tag->name;

        return view('articles.index', compact('articles', 'search'));
    }

    /**
     * Display a listing of the articles by the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View  
     */
    public function byUser(User $user)
    {
        views($user)->cooldown(1)->record();
        $cacheKey = 'articles.byUser.' . $user->id . '.page_' . request('page', 1);
        $cacheTags = ['articles', 'user:' . $user->id];

        $articles = Cache::tags($cacheTags)->remember($cacheKey, env('CACHE_DURATION', 60), function () use ($user,$cacheKey) {
            Log::info("Cache miss for key: {$cacheKey}");
            return $user->public_articles()->with(['category', 'tags'])->paginate(16);
        });

        if (Cache::has($cacheKey)) {
            Log::info("Cache hit for key: {$cacheKey}");
        }
        $search = $user->name;

        return view('articles.index', compact('articles', 'search'));
    }
}
