<?php

namespace App\Livewire;

use App\Models\Comment;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;

class CommentList extends Component
{
    public $article;
    public $comments;

    #[Validate('required|min:5|max:255')] 
    public $content;
    
    public $editingCommentId = null;
    public $replyingToCommentId = null;
    public $perPage = 10;

    protected $listeners = ['load-more' => 'loadMore'];


    public function mount($article)
    {
        $this->article = $article;
        $this->loadComments();
    }

    #[On('commentAdded')]
    public function loadComments()
    {
        $this->comments = $this->article->comments()
            ->where(function ($query) {
                $query->where('is_accepted', true)
                      ->orWhere('user_id', Auth::id());
            })
            ->whereNull('reply_to') // Carica solo i commenti principali
            ->with(['replies.user']) // Carica le risposte e i relativi utenti
            ->latest()
            ->take($this->perPage)
            ->get();
    }

    public function loadMore()
    {
        $this->perPage += 10;
        $this->loadComments();
    }

    public function editComment($commentId)
    {

        $this->editingCommentId = $commentId;
        $comment = Comment::findOrFail($commentId);
        $this->content = $comment->content;
    }

    public function updateComment()
    {
        $this->validate();

        $comment = Comment::findOrFail($this->editingCommentId);
        $this->authorize('update', $comment);

        $comment->content = $this->content;
        $comment->save();

        $this->reset(['editingCommentId', 'content']);
        $this->loadComments();
        session()->flash('message', 'Commento aggiornato con successo.');
    }

    public function deleteComment($commentId)
    {
        $comment = Comment::findOrFail($commentId);
        $this->authorize('delete', $comment);

        $comment->delete();

        $this->loadComments();
        session()->flash('message', 'Commento eliminato con successo.');
    }

    public function replyToComment($commentId)
    {
        $this->replyingToCommentId = $commentId;
    }

    public function cancelReply()
    {
        $this->replyingToCommentId = null;
        $this->reset('content');
    }

    public function submitReply()
    {
        $this->validate([
            'content' => 'required|min:5|max:255',
        ]);

        Comment::create([
            'content' => $this->content,
            'user_id' => Auth::id(),
            'article_id' => $this->article->id,
            'reply_to' => $this->replyingToCommentId,
        ]);

        $this->reset(['replyingToCommentId', 'content']);
        $this->loadComments();
        session()->flash('message', 'Risposta inviata con successo.');
    }

    public function render()
    {
        return view('livewire.comment-list');
    }
}
