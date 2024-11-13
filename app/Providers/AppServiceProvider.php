<?php

namespace App\Providers;

use App\Models\Tag;
use App\Models\User;
use App\Models\Article;
use App\Models\Category;
use App\Observers\TagObserver;
use App\Observers\UserObserver;
use App\Observers\ArticleObserver;
use App\Observers\CategoryObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Opcodes\LogViewer\Facades\LogViewer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        $this->startObserver();
        
        Paginator::useBootstrapFive();

        LogViewer::auth(function ($request) {
            return Auth::check() && Auth::user()->isAdmin();
        });
    }

    public function startObserver(): void
    {
        Article::observe(ArticleObserver::class);
        Category::observe(CategoryObserver::class);
        Tag::observe(TagObserver::class);
        User::observe(UserObserver::class);
    }
}
