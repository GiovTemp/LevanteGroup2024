<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;

class TagPolicy
{
    /**
     * Determina se l'utente può visualizzare qualsiasi tag.
     */
    public function viewAny(?User $user): bool
    {
        // Tutti possono visualizzare i tag
        return true;
    }

    /**
     * Determina se l'utente può visualizzare un tag specifico.
     */
    public function view(?User $user, Tag $tag): bool
    {
        // Tutti possono visualizzare un tag
        return true;
    }

    /**
     * Determina se l'utente può creare tag.
     */
    public function create(User $user): bool
    {
        // Solo gli amministratori possono creare tag
        return $user->is_admin;
    }

    /**
     * Determina se l'utente può aggiornare un tag.
     */
    public function update(User $user, Tag $tag): bool
    {
        // Solo gli amministratori possono aggiornare tag
        return $user->is_admin;
    }

    /**
     * Determina se l'utente può eliminare un tag.
     */
    public function delete(User $user, Tag $tag): bool
    {
        // Solo gli amministratori possono eliminare tag
        return $user->is_admin;
    }

    /**
     * Determina se l'utente può ripristinare un tag.
     */
    public function restore(User $user, Tag $tag): bool
    {
        // Solo gli amministratori possono ripristinare tag
        return $user->is_admin;
    }

    /**
     * Determina se l'utente può eliminare definitivamente un tag.
     */
    public function forceDelete(User $user, Tag $tag): bool
    {
        // Solo gli amministratori possono eliminare definitivamente tag
        return $user->is_admin;
    }
}
