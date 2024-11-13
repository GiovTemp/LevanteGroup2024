<?php

namespace App\Observers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryObserver
{
    /**
     * Handle the Category "updated" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function updated(Category $category)
    {
        // Invalida la cache specifica della categoria
        Cache::tags(['articles', 'category:' . $category->id])->flush();
    }

    /**
     * Handle the Category "deleted" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function deleted(Category $category)
    {
        // Invalida la cache specifica della categoria
        Cache::tags(['articles', 'category:' . $category->id])->flush();
    }
}
