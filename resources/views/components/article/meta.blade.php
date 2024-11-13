<meta name="description" content="{{ $article->meta_description }}">
<meta name="keywords" content="{{ $article->meta_keywords }}">

        <!-- Open Graph -->
<meta property="og:title" content="{{ $article->meta_title ?? $article->title }}">
<meta property="og:description" content="{{ $article->meta_description ?? Str::limit(strip_tags($article->content), 150) }}">
<meta property="og:type" content="article">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="{{ $article->image ?  Storage::url($article->image) : asset('images/default-og-image.jpg') }}">

<!-- Twitter Cards -->
<meta name="twitter:title" content="{{ $article->meta_title ?? $article->title }}">
<meta name="twitter:description" content="{{ $article->meta_description ?? Str::limit(strip_tags($article->content), 150) }}">
<meta name="twitter:image" content="{{ $article->image ? Storage::url($article->image) : asset('images/default-twitter-image.jpg') }}">