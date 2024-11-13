<?php

namespace App\Livewire;

use Exception;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CommentCreate extends Component
{

    public $article;
    #[Validate('required|min:5|max:255')] 
    public $content;

    public function render()
    {
        return view('livewire.comment-create');
    }

    public function sendComment()
    {

        try {
            $this->validate();
            $this->article->comments()->create([
                'content' => $this->content,
                'user_id' => Auth::id()
            ]);
    
            $this->content = '';
            $this->dispatch('commentAdded');
            session()->flash('success', 'Commento Inviato correttamente!');
        } catch (Exception $e) {
            session()->flash('error', 'Qualcosa è andato storto, riprova più tardi!');
            Log::error($e->getMessage());
        }


    }

}
