<?php

namespace App\Providers;

use App\Models\Tag;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Category;
use App\Policies\TagPolicy;
use App\Policies\ArticlePolicy;
use App\Policies\CommentPolicy;
use App\Policies\CategoryPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * La mappatura delle policy per l'applicazione.
     *
     * @var array
     */
    protected $policies = [
        Comment::class => CommentPolicy::class,
        Article::class => ArticlePolicy::class,
        Category::class => CategoryPolicy::class,
        Tag::class      => TagPolicy::class,
    ];

    /**
     * Registra eventuali servizi di autenticazione/autorizzazione.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

    }
}
