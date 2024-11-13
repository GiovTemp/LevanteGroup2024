<!-- resources/views/livewire/article-filter.blade.php -->

<div>
    <div class="row">
        <!-- Sidebar per i Filtri -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5>Filtri</h5>
                    <!-- Filtro per Titolo -->
                    <div class="mb-3">
                        <label for="search" class="form-label">Titolo</label>
                        <input type="text" wire:model.blur="search" id="search" class="form-control" placeholder="Cerca per titolo">
                    </div>
                    <!-- Filtro per Tag -->
                    <div class="mb-3">
                        <label class="form-label">Tag</label>
                        <div>
                            @foreach($availableTags as $tag)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model.live="selectedTags" value="{{ $tag->id }}" id="tag-{{ $tag->id }}">
                                    <label class="form-check-label" for="tag-{{ $tag->id }}">
                                        {{ $tag->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- Filtro per Categorie -->
                    <div class="mb-3">
                        <label class="form-label">Categorie</label>
                        <div>
                            @foreach($availableCategories as $category)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model.live="selectedCategories" value="{{ $category->id }}" id="category-{{ $category->id }}">
                                    <label class="form-check-label" for="category-{{ $category->id }}">
                                        {{ $category->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- Pulsante per Resettare i Filtri -->
                    <button wire:click="resetFilters" class="btn btn-secondary">Reset Filtri</button>
                </div>
            </div>
        </div>

        <!-- Sezione Principale per gli Articoli -->
        <div class="col-md-9">
            @if($articles->isEmpty())
                <div class="alert alert-info text-center" role="alert">
                    Nessun articolo trovato.
                </div>
            @else
                <div class="row">
                    @foreach ($articles as $article)
                        <div class="col-md-4 mb-4">
                            <x-article.card :article="$article" />
                        </div>
                    @endforeach
                </div>
                <div class="row">
                    <div class="col-12">
                        {{ $articles->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
