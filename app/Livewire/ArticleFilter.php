<?php

namespace App\Livewire;

use Livewire\Attributes\On;


use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Article;
use App\Models\Tag;
use App\Models\Category;

class ArticleFilter extends Component
{
    use WithPagination;


    public string $search = '';
    public array $selectedTags = [];
    public array $selectedCategories = [];

    public int $perPage = 10;

    public $availableTags;
    public $availableCategories;
    public array $initialArticles = [];

    #[On('resetFilters')]
    public function resetFilters()
    {
        $this->reset('search', 'selectedTags', 'selectedCategories');
    }

    public function mount($articles)
    {
        $this->availableTags = Tag::all();
        $this->availableCategories = Category::all();
        $this->initialArticles = $articles->pluck('id')->toArray();
    }

    #[On('updating:search', 'updating:selectedTags', 'updating:selectedCategories')]
    public function updatingFilters()
    {
        dd('updating');
        $this->resetPage();
    }

    public function render()
    {
        $query = Article::query();

        if (!empty($this->initialArticles)) {
            $query->whereIn('id', $this->initialArticles);
        }

        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        if (!empty($this->selectedTags)) {
            $query->whereHas('tags', function($q) {
                $q->whereIn('tags.id', $this->selectedTags);
            });
        }

        if (!empty($this->selectedCategories)) {
            $query->whereIn('category_id', $this->selectedCategories);
        }

        $articles = $query->with(['category', 'tags'])->paginate($this->perPage);

        return view('livewire.article-filter', [
            'articles' => $articles,
        ]);
    }
}
