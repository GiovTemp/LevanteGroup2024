<?php

namespace App\Models;

use Carbon\Carbon;
use Laravel\Scout\Searchable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;


class Article extends Model implements Viewable
{

    use InteractsWithViews , Searchable;

    protected $fillable = [
        'title','meta_title','meta_description','meta_keywords',
        'content', 'slug', 'image' ,'user_id', 'subtitle', 
        'ai_generated', 'is_published', 'published_at','category_id'
    ];

    


    
    protected static function booted()
    {
        static::creating(function ($article) {
            // Imposta l'ID dell'utente autenticato al momento della creazione del record
            $article->user_id = Auth::id();
        });

        static::created(function (Article $article) {
            // Verifica se l'immagine si trova ancora nella directory temporanea
            if ($article->image && str_contains($article->image, 'articles/temp')) {
                // Nuovo percorso in cui spostare l'immagine
                $newPath = 'articles/' . $article->id . '/' . basename($article->image);

                // Sposta l'immagine nella directory definitiva
                Storage::disk('public')->move($article->image, $newPath);

                // Aggiorna il percorso dell'immagine nel record e salva di nuovo
                $article->image = $newPath;
                $article->save();
            }
        });
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function tags(){
        return $this->belongsToMany(Tag::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);    
    }

    /**
     * Determina se il modello deve essere indicizzato.
     *
     * @return bool
     */
    public function shouldBeSearchable()
    {
        return $this->is_published && $this->published_at <= Carbon::today()->toDateString();
    }


    public function toSearchableArray()
    {
        $array= [
            'id'=>$this->id,
            'title'=>$this->title,
            'subtitle'=>$this->subtitle,
            'content'=>$this->content,
            'category'=>$this->category,
            'tags'=>$this->tags,
            'author'=>$this->user->name,
        ];

        return $array;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
