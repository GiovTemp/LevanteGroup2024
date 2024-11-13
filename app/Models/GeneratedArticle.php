<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GeneratedArticle extends Model
{
    protected $fillable = [
        'title','meta_title','meta_description','meta_keywords',
        'content', 'slug', 'image' , 'subtitle', 
        'ai_generated', 'published_at','category_id',
    ];

    
    protected static function booted()
    {

        static::created(function (GeneratedArticle $article) {
            // Verifica se l'immagine si trova ancora nella directory temporanea
            if ($article->image && str_contains($article->image, 'generated_articles/temp')) {
                // Nuovo percorso in cui spostare l'immagine
                $newPath = 'genereted_articles/' . $article->id . '/' . basename($article->image);

                // Sposta l'immagine nella directory definitiva
                Storage::disk('public')->move($article->image, $newPath);

                // Aggiorna il percorso dell'immagine nel record e salva di nuovo
                $article->image = $newPath;
                $article->save();
            }
        });
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function tags(){
        return $this->belongsToMany(Tag::class);
    }
}
