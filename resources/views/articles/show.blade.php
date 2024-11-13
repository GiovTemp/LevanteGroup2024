<x-layout>
    <x-slot name="title">{{ $article->meta_title ?? $article->title }}</x-slot>
    <x-slot name="meta">
        <x-article.meta :article="$article"/>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <img src="{{ Storage::url($article->image) }}" class="card-img-top rounded-top" alt="{{ $article->title }}">
                <div class="card-body">
                    <h1 class="card-title">{{ $article->title }}</h1>
                    <h4 class="card-subtitle text-muted mb-3">{{ $article->subtitle }}</h4>
                    <div class="mb-3">
                        <span class="badge bg-primary">Category: {{ $article->category->name }}</span>
                        @foreach ($article->tags as $tag)
                            <span class="badge bg-secondary">{{ $tag->name }}</span>
                        @endforeach
                        @if ($article->ai_generated)
                            <span class="badge bg-warning text-dark">AI Generated</span>
                        @endif
                    </div>
                    <p class="text-muted mb-1">
                        <small>Published on: {{ $article->published_at }}</small>
                    </p>
                    <p class="text-muted mb-3">
                        <small>Author: {{ $article->user->name }}</small>
                    </p>
                    <div class="content">
                        {!! $article->content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @auth
        <div class="row">
            <div class="col-12">
                <livewire:comment-create :article="$article" />
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info" role="alert">
                    <a href="{{ route('login') }}" class="alert-link">Login</a> to leave a comment.
                </div>
            </div>
        </div>
    @endauth

    <div class="row">
        <div class="col-12">
            <livewire:comment-list :article="$article" />
        </div>
    </div>



</x-layout>

