<?php

namespace App\Observers;

use App\Models\Tag;
use Illuminate\Support\Facades\Cache;

class TagObserver
{
    /**
     * Handle the Tag "updated" event.
     *
     * @param  \App\Models\Tag  $tag
     * @return void
     */
    public function updated(Tag $tag)
    {
        // Invalida la cache specifica del tag
        Cache::tags(['articles', 'tag:' . $tag->id])->flush();
    }

    /**
     * Handle the Tag "deleted" event.
     *
     * @param  \App\Models\Tag  $tag
     * @return void
     */
    public function deleted(Tag $tag)
    {
        // Invalida la cache specifica del tag
        Cache::tags(['articles', 'tag:' . $tag->id])->flush();
    }
}
