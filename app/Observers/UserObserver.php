<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserObserver
{
    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        // Invalida la cache specifica dell'utente
        Cache::tags(['articles', 'user:' . $user->id])->flush();
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        // Invalida la cache specifica dell'utente
        Cache::tags(['articles', 'user:' . $user->id])->flush();
    }
}
