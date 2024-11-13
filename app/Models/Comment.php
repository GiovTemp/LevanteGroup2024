<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //
    protected $fillable = ['content', 'user_id','is_accepted','article_id','reply_to'];

    /**
     * Il metodo "booted" del modello.
     * Questo metodo viene chiamato una volta quando il modello viene inizializzato.
    */
    protected static function booted()
    {
        static::updating(function ($comment) {
            // Verifica se il contenuto del commento è stato modificato
            if ($comment->isDirty('content')) {
                // Resetta lo stato di approvazione
                $comment->is_accepted = null;
            }
        });
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function article(){
        return $this->belongsTo(Article::class);
    }

    // Relazione con il commento padre
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'reply_to');
    }

    // Relazione con i commenti figli
    public function replies()
    {
        return $this->hasMany(Comment::class, 'reply_to')->with('user', 'replies');
    }

    public function getStatusAttribute(): string
    {
        if (is_null($this->is_accepted)) {
            return 'In Attesa';
        }
        return $this->is_accepted ? 'Accettato' : 'Rifiutato';
    }

    /**
     * Accessor per l'ordinamento dello stato.
     */
    public function getStatusOrderAttribute(): int
    {
        if (is_null($this->is_accepted)) {
            return 0; // In Attesa
        }
        return $this->is_accepted ? 1 : 2; // Accettato: 1, Rifiutato: 2
    }

}
