
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Leave a Comment</h5>
                        @session('success')
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endsession
                        <form wire:submit="sendComment">
                            <div class="mb-3">
                                <label for="comment" class="form-label">Your Comment</label>
                                <textarea wire:model.blur="content" rows="4" class="form-control rounded-3" placeholder="Share your thoughts..." required></textarea>
                                @error('content') <span class="text-danger">{{ $message }}</span> @enderror    
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Post Comment</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


