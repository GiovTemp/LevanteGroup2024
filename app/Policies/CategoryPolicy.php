<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Comment;
use App\Models\Category;

class CategoryPolicy
{
    /**
     * Determina se l'utente può visualizzare qualsiasi categoria.
     */
    public function viewAny(?User $user): bool
    {
        // Tutti possono visualizzare le categorie
        return true;
    }

    /**
     * Determina se l'utente può visualizzare una categoria specifica.
     */
    public function view(?User $user, Category $category): bool
    {
        // Tutti possono visualizzare una categoria
        return true;
    }

    /**
     * Determina se l'utente può creare categorie.
     */
    public function create(User $user): bool
    {
        // Solo gli amministratori possono creare categorie
        return $user->is_admin;
    }

    /**
     * Determina se l'utente può aggiornare una categoria.
     */
    public function update(User $user, Category $category): bool
    {
        // Solo gli amministratori possono aggiornare categorie
        return $user->is_admin;
    }

    /**
     * Determina se l'utente può eliminare una categoria.
     */
    public function delete(User $user, Category $category): bool
    {
        // Solo gli amministratori possono eliminare categorie
        return $user->is_admin;
    }

    /**
     * Determina se l'utente può ripristinare una categoria.
     */
    public function restore(User $user, Category $category): bool
    {
        // Solo gli amministratori possono ripristinare categorie
        return $user->is_admin;
    }

    /**
     * Determina se l'utente può eliminare definitivamente una categoria.
     */
    public function forceDelete(User $user, Category $category): bool
    {
        // Solo gli amministratori possono eliminare definitivamente categorie
        return $user->is_admin;
    }

    public function createReply(User $user, Comment $comment)
    {
        return true; // Permetti a tutti gli utenti autenticati di rispondere
    }
}
