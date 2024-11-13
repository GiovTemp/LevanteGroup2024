<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    /**
     * Determina se l'utente può visualizzare qualsiasi articolo.
     */
    public function viewAny(?User $user): bool
    {
        // Tutti possono visualizzare gli articoli pubblici
        return true;
    }

    /**
     * Determina se l'utente può visualizzare un articolo specifico.
     */
    public function view(?User $user, Article $article): bool
    {
        // Tutti possono visualizzare gli articoli pubblici
        return $article->is_published || ($user && ($user->id === $article->user_id || $user->is_admin));
    }

    /**
     * Determina se l'utente può creare articoli.
     */
    public function create(User $user): bool
    {
        // Solo gli utenti autenticati possono creare articoli
        return ($user !== null && $user->is_admin);
    }

    /**
     * Determina se l'utente può aggiornare l'articolo.
     */
    public function update(User $user, Article $article): bool
    {
        // Solo l'autore o un amministratore possono aggiornare l'articolo
        return $user->id === $article->user_id || $user->is_admin;
    }

    /**
     * Determina se l'utente può eliminare l'articolo.
     */
    public function delete(User $user, Article $article): bool
    {
        // Solo l'autore o un amministratore possono eliminare l'articolo
        return $user->id === $article->user_id || $user->is_admin;
    }

    /**
     * Determina se l'utente può ripristinare l'articolo.
     */
    public function restore(User $user, Article $article): bool
    {
        // Solo un amministratore può ripristinare gli articoli
        return $user->is_admin;
    }

    /**
     * Determina se l'utente può eliminare definitivamente l'articolo.
     */
    public function forceDelete(User $user, Article $article): bool
    {
        // Solo un amministratore può eliminare definitivamente gli articoli
        return $user->is_admin;
    }
}
