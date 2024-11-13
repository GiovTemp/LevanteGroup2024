<div>
    <div class="card shadow-sm border-0" style="width: 18rem;">
        <img src="{{Storage::url($article->image) }}" class="card-img-top rounded-top" alt="{{ $article->title }}">
        <div class="card-body">
            <h5 class="card-title d-flex align-items-center">
                {{ $article->title }}
                @if ($article->ai_generated)
                    <span class="badge bg-warning ms-2 text-dark">AI Generated</span>
                @endif
            </h5>
            <h6 class="card-subtitle mb-2 text-muted">{{ $article->subtitle }}</h6>
            <p class="text-muted mb-1">
                <small>Published on: {{ $article->published_at}}</small>
            </p>

            <p class="text-muted mb-1">
                <small>Author: <a href="{{route('articles.byUser',$article->user)}}"> {{ $article->user->name}} </a> </small>
            </p>
            <p class="text-muted mb-2">
                <small>Category: <a href="{{route('articles.byCategory',$article->category)}}"><span class="text-primary">{{ $article->category->name }}</span></a> </small>
            </p>
            <p class="card-text">{{ Str::limit($article->excerpt, 100) }}</p>

            <div class="mb-3">
                @foreach ($article->tags as $tag)
                    <a href="{{route('articles.byTag',$tag)}}"><span class="badge bg-secondary">{{ $tag->name }}</span></a>
                @endforeach
            </div>

            <a href="{{ route('articles.show', $article) }}" class="btn btn-primary w-100">Read More</a>
        </div>
    </div>
</div>