<x-layout>

    <div class="row">
        <div class="col-12">
            <h1>Risultati per la ricerca : {{$query}}</h1>
        </div>
    </div>

    @if(count($articles) == 0)
        <div class="row">
            <div class="col-12">
                <h2>Nessun articolo trovato</h2>
            </div>
        </div>
    @else
  
        <livewire:article-filter :articles="$articles" />
    
    
    @endif

</x-layout>