@extends('layouts.app')

@section('title', 'Ticket Details')

@section('content')
@php
    $ticketData = $ticket;
@endphp

<div style="max-width: 1200px; margin: 0 auto;">
    <!-- Breadcrumbs -->
    <div class="card">
        <div class="card-body">
            <div class="breadcrumbs text-sm">
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="/tickets">Tickets</a></li>
                    <li>Ticket #{{ $ticketData->ticket_number ?? $id }}</li>
                </ul>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div style="padding: 1rem; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 0.25rem; color: #155724; margin-bottom: 1rem;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="padding: 1rem; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 0.25rem; color: #721c24; margin-bottom: 1rem;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Ticket Header -->
    <div class="card">
        <div class="card-body">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.5rem;">
                <div>
                    <h1 style="font-size: 1.875rem; font-weight: 700; margin-bottom: 0.5rem;">
                        {{ $ticketData->title ?? 'N/A' }}
                    </h1>
                    <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                        <span style="font-size: 0.875rem; color: #6b7280;">
                            Ticket #{{ $ticketData->ticket_number ?? $id }}
                        </span>
                        <span style="padding: 0.25rem 0.75rem; background-color: #3b82f6; color: white; border-radius: 9999px; font-size: 0.875rem; font-weight: 500;">
                            {{ $ticketData->status ?? 'N/A' }}
                        </span>
                        @php
                            $priority = $ticketData->priority ?? 'Medium';
                            $priorityColor = $priority === 'High' ? '#ef4444' : ($priority === 'Medium' ? '#f59e0b' : '#10b981');
                        @endphp
                        <span style="padding: 0.25rem 0.75rem; background-color: {{ $priorityColor }}; color: white; border-radius: 9999px; font-size: 0.875rem; font-weight: 500;">
                            {{ $priority }} Priority
                        </span>
                    </div>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <a href="/tickets" class="btn btn-secondary">Back to Tickets</a>
                </div>
            </div>

            <!-- Ticket Info Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                <div>
                    <label style="font-size: 0.875rem; color: #6b7280; font-weight: 500;">Category</label>
                    <p style="margin-top: 0.25rem; font-weight: 500;">
                        {{ is_object($ticketData->issuegroup ?? null) ? $ticketData->issuegroup->name : (is_array($ticketData->issuegroup ?? null) ? $ticketData->issuegroup['name'] ?? 'N/A' : 'N/A') }}
                    </p>
                </div>
                <div>
                    <label style="font-size: 0.875rem; color: #6b7280; font-weight: 500;">Type</label>
                    <p style="margin-top: 0.25rem; font-weight: 500;">
                        {{ is_object($ticketData->issuetype ?? null) ? $ticketData->issuetype->name : (is_array($ticketData->issuetype ?? null) ? $ticketData->issuetype['name'] ?? 'N/A' : 'N/A') }}
                    </p>
                </div>
                <div>
                    <label style="font-size: 0.875rem; color: #6b7280; font-weight: 500;">Created</label>
                    <p style="margin-top: 0.25rem; font-weight: 500;">{{ Carbon\Carbon::parse($ticketData->created_at ?? now())->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <label style="font-size: 0.875rem; color: #6b7280; font-weight: 500;">Last Updated</label>
                    <p style="margin-top: 0.25rem; font-weight: 500;">{{ Carbon\Carbon::parse($ticketData->updated_at ?? now())->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <!-- Main Content -->
        <div>
            <!-- Description -->
            <div class="card">
                <h3 class="card-header">Description</h3>
                <div class="card-body">
                    <div style="white-space: pre-wrap; line-height: 1.6;">{{ $ticketData->description ?? 'N/A' }}</div>
                </div>
            </div>

            <!-- Attachments -->
            @if(isset($ticketData->attachments) && (is_array($ticketData->attachments) ? count($ticketData->attachments) > 0 : (is_countable($ticketData->attachments) && count($ticketData->attachments) > 0)))
            <div class="card">
                <h3 class="card-header">Attachments</h3>
                <div class="card-body">
                    <div style="display: grid; gap: 0.75rem;">
                        @foreach($ticketData->attachments as $attachment)
                        @php
                            $attachmentData = is_array($attachment) ? (object)$attachment : $attachment;
                        @endphp
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; background-color: #f9fafb; border-radius: 0.375rem; border: 1px solid #e5e7eb;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <svg style="width: 1.5rem; height: 1.5rem; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                <div>
                                    <p style="font-weight: 500;">{{ $attachmentData->name ?? 'Attachment' }}</p>
                                    @if(isset($attachmentData->size))
                                    <p style="font-size: 0.875rem; color: #6b7280;">{{ number_format($attachmentData->size / 1024, 2) }} KB</p>
                                    @endif
                                </div>
                            </div>
                            @if(isset($attachmentData->url))
                            <a href="{{ $attachmentData->url }}" target="_blank" class="btn btn-sm btn-primary" download>Download</a>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Comments/Activity -->
            <div class="card">
                <h3 class="card-header">Comments & Activity</h3>
                <div class="card-body">
                    @php
                     
                        $hasComments = count($ticketData->comments) > 0;
                    @endphp

                    @if($hasComments)
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            @foreach($ticketData->comments as $comment)
                            @php
                                $commentData = is_array($comment) ? (object)$comment : $comment;
                                $commentUser = $ticketData->assignedto;
                                $commentText=$commentData->comment;
                                $userName=$commentData->user_email ?? 'Support Team';
                                $showcomment = $commentData->is_internal ? false : true;
                                
                                // Check if this comment belongs to the current user
                                $isOwnComment = false;
                                $commentEmail = $commentData->user_email ?? $commentData->email ?? null;
                                if ($commentEmail && auth()->check() && strtolower($commentEmail) === strtolower(auth()->user()->email)) {
                                    $isOwnComment = true;
                                }
                                
                                $commentId = $commentData->id ?? null;
                            @endphp
                            
                            @if(trim($commentText) !== '' && $showcomment)
                            <div class="comment-item" id="comment-{{ $commentId }}" style="background-color: #ffffff; border: 1px solid #e5e7eb; border-left: 4px solid #3b82f6; padding: 1rem; border-radius: 0.5rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div style="width: 2.5rem; height: 2.5rem; border-radius: 50%; background-color: #3b82f6; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1rem;">
                                            {{ strtoupper(substr($userName, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p style="font-weight: 600; margin: 0; color: #111827;">
                                                {{ $userName }}
                                                @if($isOwnComment)
                                                <span style="font-size: 0.75rem; color: #6b7280; font-weight: 400;">(You)</span>
                                                @endif
                                            </p>
                                            <p style="font-size: 0.875rem; color: #6b7280; margin: 0.125rem 0 0 0;">
                                                {{ isset($commentData->created_at) ? Carbon\Carbon::parse($commentData->created_at)->format('M d, Y \a\t H:i') : 'Just now' }}
                                            </p>
                                        </div>
                                    </div>
                                    @if($isOwnComment && $commentId)
                                    <div style="display: flex; gap: 0.5rem;">
                                        <button onclick="editComment({{ $commentId }})" class="btn btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.875rem; background-color: #f3f4f6; color: #374151; border: 1px solid #d1d5db;">
                                            <svg style="width: 1rem; height: 1rem; display: inline;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </button>
                                        <form action="/tickets/{{ $id }}/comment/{{ $commentId }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this comment?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.875rem; background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca;">
                                                <svg style="width: 1rem; height: 1rem; display: inline;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                                </div>
                                <div class="comment-text" style="line-height: 1.625; color: #374151; padding-left: 3.25rem;">
                                    {!! nl2br(e($commentText)) !!}
                                </div>
                                <div class="comment-edit-form" style="display: none; padding-left: 3.25rem; margin-top: 0.75rem;">
                                    <form action="/tickets/{{ $id }}/comment/{{ $commentId }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <textarea name="comment" class="form-textarea" rows="4" style="width: 100%; margin-bottom: 0.5rem;">{{ $commentText }}</textarea>
                                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                            <button type="button" onclick="cancelEdit({{ $commentId }})" class="btn btn-secondary btn-sm">Cancel</button>
                                            <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: 3rem 2rem; color: #6b7280; background-color: #f9fafb; border-radius: 0.5rem;">
                            <svg style="width: 4rem; height: 4rem; margin: 0 auto 1rem; color: #d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <p style="margin: 0; font-weight: 600; font-size: 1.125rem; color: #374151;">No comments yet</p>
                            <p style="margin: 0.5rem 0 0 0; font-size: 0.875rem;">Be the first to add a comment below.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Add Comment Form -->
            @if(($ticketData->status ?? '') !== 'Closed' && ($ticketData->status ?? '') !== 'closed')
            <div class="card">
                <h3 class="card-header">Add Comment</h3>
                <div class="card-body">
                    <form action="/tickets/{{ $id }}/comment" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Your Comment *</label>
                            <textarea name="comment" class="form-textarea" required rows="4" 
                                placeholder="Add your comment or reply..."></textarea>
                        </div>
                     
                        <div style="display: flex; justify-content: flex-end;">
                            <button type="submit" class="btn btn-primary">Add Comment</button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Requester Information -->
            <div class="card">
                <h3 class="card-header">Requester Information</h3>
                <div class="card-body">
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div>
                            <label style="font-size: 0.875rem; color: #6b7280; font-weight: 500;">Name</label>
                            <p style="margin-top: 0.25rem; font-weight: 500;">{{ $ticketData->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label style="font-size: 0.875rem; color: #6b7280; font-weight: 500;">Email</label>
                            <p style="margin-top: 0.25rem;">
                                <a href="mailto:{{ $ticketData->email ?? '' }}" style="color: #3b82f6;">
                                    {{ $ticketData->email ?? 'N/A' }}
                                </a>
                            </p>
                        </div>
                        @if(isset($ticketData->phone) && $ticketData->phone)
                        <div>
                            <label style="font-size: 0.875rem; color: #6b7280; font-weight: 500;">Phone</label>
                            <p style="margin-top: 0.25rem;">
                                <a href="tel:{{ $ticketData->phone }}" style="color: #3b82f6;">
                                    {{ $ticketData->phone }}
                                </a>
                            </p>
                        </div>
                        @endif
                        @if(isset($ticketData->regnumber) && $ticketData->regnumber)
                        <div>
                            <label style="font-size: 0.875rem; color: #6b7280; font-weight: 500;">Registration Number</label>
                            <p style="margin-top: 0.25rem; font-weight: 500;">{{ $ticketData->regnumber }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Ticket Actions -->
            @if(($ticketData->status ?? '') !== 'Closed' && ($ticketData->status ?? '') !== 'closed')
            <div class="card">
                <h3 class="card-header">Actions</h3>
                <div class="card-body">
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <form action="/tickets/{{ $id }}/close" method="POST" onsubmit="return confirm('Are you sure you want to close this ticket?');">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-secondary" style="width: 100%;">Close Ticket</button>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            <!-- Ticket Timeline -->
            @if(isset($ticketData->timeline) && (is_array($ticketData->timeline) ? count($ticketData->timeline) > 0 : (is_countable($ticketData->timeline) && count($ticketData->timeline) > 0)))
            <div class="card">
                <h3 class="card-header">Timeline</h3>
                <div class="card-body">
                    <div style="position: relative;">
                        @foreach($ticketData->timeline as $event)
                        @php
                            $eventData = is_array($event) ? (object)$event : $event;
                        @endphp
                        <div style="display: flex; gap: 0.75rem; margin-bottom: 1rem; position: relative;">
                            <div style="display: flex; flex-direction: column; align-items: center;">
                                <div style="width: 0.5rem; height: 0.5rem; background-color: #3b82f6; border-radius: 50%; margin-top: 0.375rem;"></div>
                                @if(!$loop->last)
                                <div style="width: 2px; height: 100%; background-color: #e5e7eb; margin-top: 0.25rem; margin-bottom: 0.25rem;"></div>
                                @endif
                            </div>
                            <div style="flex: 1; padding-bottom: 1rem;">
                                <p style="font-weight: 500; font-size: 0.875rem;">{{ $eventData->action ?? 'Event' }}</p>
                                <p style="font-size: 0.75rem; color: #6b7280;">
                                    {{ Carbon\Carbon::parse($eventData->created_at ?? now())->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function editComment(commentId) {
    const commentDiv = document.getElementById('comment-' + commentId);
    if (!commentDiv) return;
    
    const textDiv = commentDiv.querySelector('.comment-text');
    const editForm = commentDiv.querySelector('.comment-edit-form');
    
    if (textDiv && editForm) {
        textDiv.style.display = 'none';
        editForm.style.display = 'block';
    }
}

function cancelEdit(commentId) {
    const commentDiv = document.getElementById('comment-' + commentId);
    if (!commentDiv) return;
    
    const textDiv = commentDiv.querySelector('.comment-text');
    const editForm = commentDiv.querySelector('.comment-edit-form');
    
    if (textDiv && editForm) {
        textDiv.style.display = 'block';
        editForm.style.display = 'none';
    }
}
</script>
@endsection
