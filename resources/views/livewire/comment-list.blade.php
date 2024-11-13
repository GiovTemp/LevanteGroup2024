<!-- resources/views/livewire/comment-list.blade.php -->

<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi"></button>
        </div>
    @endif

    @if($comments->isEmpty())
        <div class="alert alert-info text-center" role="alert">
            Nessun commento ancora. Sii il primo a condividere i tuoi pensieri!
        </div>
    @else
        @foreach ($comments as $comment)
            <div class="card mb-3 shadow-sm
                @if(is_null($comment->is_accepted))
                    bg-light text-muted
                @elseif(!$comment->is_accepted)
                    border-danger
                @endif
                " style="
                @if(is_null($comment->is_accepted))
                    opacity: 0.7;
                @endif
                ">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">{{ $comment->user->name }}</h6>
                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                        </div>
                        @if ($comment->user_id == auth()->id())
                            <div>
                                @if ($comment->is_accepted)
                                    <span class="badge bg-success">Approvato</span>
                                @elseif (is_null($comment->is_accepted))
                                    <span class="badge bg-warning text-dark">In attesa</span>
                                @else
                                    <span class="badge bg-danger">Rifiutato</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    @if($editingCommentId === $comment->id)
                        <div class="mt-3">
                            <textarea wire:model="content" class="form-control" rows="3"></textarea>
                            @error('content') <span class="text-danger">{{ $message }}</span> @enderror
                            <div class="mt-2">
                                <button wire:click="updateComment" class="btn btn-success btn-sm">Aggiorna</button>
                                <button wire:click="$set('editingCommentId', null)" class="btn btn-secondary btn-sm">Annulla</button>
                            </div>
                        </div>
                    @else
                        <p class="mt-3">{{ $comment->content }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <button wire:click="replyToComment({{ $comment->id }})" class="btn btn-outline-primary btn-sm">Rispondi</button>
                            </div>
                            @can('update', $comment)
                                <div class="d-flex">
                                    <button wire:click="editComment({{ $comment->id }})" class="btn btn-outline-primary btn-sm me-2">Modifica</button>
                                    <button wire:click="deleteComment({{ $comment->id }})" class="btn btn-outline-danger btn-sm">Elimina</button>
                                </div>
                            @endcan
                        </div>

                        <!-- Se si sta rispondendo a questo commento, mostra il form di risposta -->
                        @if($replyingToCommentId === $comment->id)
                            <div class="mt-3">
                                <textarea wire:model="content" class="form-control" rows="2" placeholder="Scrivi la tua risposta..."></textarea>
                                @error('content') <span class="text-danger">{{ $message }}</span> @enderror
                                <div class="mt-2">
                                    <button wire:click="submitReply" class="btn btn-primary btn-sm">Invia</button>
                                    <button wire:click="cancelReply" class="btn btn-secondary btn-sm">Annulla</button>
                                </div>
                            </div>
                        @endif

                        <!-- Mostra le risposte -->
                        @if($comment->replies->isNotEmpty())
                            <div class="mt-3 ms-4">
                                @foreach ($comment->replies as $reply)
                                    <div class="card mb-2 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h6 class="mb-0">{{ $reply->user->name }}</h6>
                                                    <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                                                </div>
                                                @if ($reply->user_id == auth()->id())
                                                    <div>
                                                        @if ($reply->is_accepted)
                                                            <span class="badge bg-success">Approvato</span>
                                                        @elseif (is_null($reply->is_accepted))
                                                            <span class="badge bg-warning text-dark">In attesa</span>
                                                            
                                                        @else
                                                            <span class="badge bg-danger">Rifiutato</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>

                                            @if($editingCommentId === $reply->id)
                                                <div class="mt-3">
                                                    <textarea wire:model="content" class="form-control" rows="3"></textarea>
                                                    @error('content') <span class="text-danger">{{ $message }}</span> @enderror
                                                    <div class="mt-2">
                                                        <button wire:click="updateComment" class="btn btn-success btn-sm">Aggiorna</button>
                                                        <button wire:click="$set('editingCommentId', null)" class="btn btn-secondary btn-sm">Annulla</button>
                                                    </div>
                                                </div>
                                            @else
                                                <p class="mt-3">{{ $reply->content }}</p>
                                                @can('update', $reply)
                                                    <div class="d-flex">
                                                        <button wire:click="editComment({{ $reply->id }})" class="btn btn-outline-primary btn-sm me-2">Modifica</button>
                                                        <button wire:click="deleteComment({{ $reply->id }})" class="btn btn-outline-danger btn-sm">Elimina</button>
                                                    </div>
                                                @endcan
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        @endforeach

        @if($comments->count() >= $perPage)
            <div class="text-center">
                <button wire:click="loadMore" class="btn btn-primary">Carica altri commenti</button>
            </div>
        @endif
    @endif
</div>

<script>
    document.addEventListener('livewire:load', function () {
        let loading = false;
        window.onscroll = function() {
            if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 200 && !loading) {
                loading = true;
                @this.call('loadMore').then(() => {
                    loading = false;
                });
            }
        };
    });
</script>
