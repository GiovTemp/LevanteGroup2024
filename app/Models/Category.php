<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;

class Category extends Model implements Viewable
{
    //
    use InteractsWithViews;
    
    protected $fillable = ['name', 'slug'];

    public $timestamps = false;

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function public_articles()
    {
        return $this->hasMany(Article::class)
                ->where('is_published', true)
                ->where('published_at', '<=', now());
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
