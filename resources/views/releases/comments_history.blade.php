@if(isset($comments) && $comments->count() > 0)
<div class="card card-custom card-stretch gutter-b">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title font-weight-bolder">Comments History</h3>
    </div>
    <div class="card-body">
        <div class="timeline timeline-3">
            <div class="timeline-items">
                @foreach($comments as $comment)
                    <div class="timeline-item">
                        <div class="timeline-media">
                            <i class="flaticon2-chat-1 text-primary"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="mr-2">
                                    <a href="#" class="text-dark-75 text-hover-primary font-weight-bold">{{ $comment->user->name ?? 'Unknown User' }}</a>
                                    <span class="text-muted ml-2">{{ $comment->created_at->format('d M Y, H:i') }}</span>
                                </div>
                            </div>
                            <p class="p-0">{{ $comment->comment }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif
