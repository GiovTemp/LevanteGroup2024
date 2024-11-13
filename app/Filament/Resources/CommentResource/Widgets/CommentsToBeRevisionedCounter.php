<?php

namespace App\Filament\Resources\CommentResource\Widgets;

use App\Models\Comment;
use Filament\Widgets\Widget;

class CommentsToBeRevisionedCounter extends Widget
{
    protected static string $view = 'filament.resources.comment-resource.widgets.comments-to-be-revisioned-counter';

    protected int | string | array $columnSpan = [
        'sm' => 2,
        'md' => 1,
        'xl' => 1,
    ];

    protected $count=0;

    public function mount()
    {
        $this->count=Comment::whereNull('is_accepted')->count();
    }
}
