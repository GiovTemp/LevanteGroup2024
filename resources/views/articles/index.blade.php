<x-layout>

    @if(empty($search))
        <div class="row">
            <div class="col-12">
                <h1>Tutti gli articoli</h1>
            </div>
        </div>
    @else
    <div class="row">
        <div class="col-12">
            <h1>Risultati per {{$search}}</h1>
        </div>
    </div>
    @endif


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