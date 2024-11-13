<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    /**
     * Determina se l'utente può visualizzare qualsiasi commento.
     */
    public function viewAny(?User $user): bool
    {
        // Chiunque può visualizzare i commenti accettati.
        return true;
    }

    /**
     * Determina se l'utente può visualizzare un commento specifico.
     */
    public function view(?User $user, Comment $comment): bool
    {
        // Permetti la visualizzazione se il commento è accettato o se l'utente è l'autore.
        return $comment->is_accepted || ($user && $user->id === $comment->user_id);
    }

    /**
     * Determina se l'utente può creare un commento.
     */
    public function create(User $user): bool
    {
        // Solo gli utenti autenticati possono creare commenti.
        return $user !== null;
    }

    /**
     * Determina se l'utente può aggiornare un commento.
     */
    public function update(User $user, Comment $comment): bool
    {
        // L'utente può aggiornare se è l'autore
        return $user->id === $comment->user_id ;
    }

    /**
     * Determina se l'utente può eliminare un commento.
     */
    public function delete(User $user, Comment $comment): bool
    {
        // L'utente può eliminare se è l'autore 
        return $user->id === $comment->user_id ;
    }

    /**
     * Determina se l'utente può ripristinare un commento.
     */
    public function restore(User $user, Comment $comment): bool
    {
        // Solo un amministratore può ripristinare commenti.
        return $user->is_admin;
    }

    /**
     * Determina se l'utente può eliminare definitivamente un commento.
     */
    public function forceDelete(User $user, Comment $comment): bool
    {
        // Solo un amministratore può eliminare definitivamente commenti.
        return $user->is_admin;
    }
}
