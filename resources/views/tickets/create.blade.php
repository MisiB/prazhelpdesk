@extends('layouts.app')

@section('title', 'Create Ticket')

@section('content')
    <div style="max-width: 800px; margin: 0 auto;">
        <div class="card">
            <div class="card-body">
                <div class="breadcrumbs text-sm">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/tickets">Tickets</a></li>
                        <li>Create Ticket</li>
                    </ul>
                </div>
            </div>
        </div>



        <div class="card">
            <h3 class="card-header">Create Support Ticket</h3>
            <div class="card-body">
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

                @if($errors->any())
                    <div style="padding: 1rem; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 0.25rem; color: #721c24; margin-bottom: 1rem;">
                        <ul style="margin: 0; padding-left: 1.5rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('tickets.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label">Subject *</label>
                        <input type="text" name="title" class="form-input" required
                            value="{{ old('title') }}"
                            placeholder="Brief description of your issue">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">Category *</label>
                            <select name="issuegroup_id" class="form-select" required>
                                <option value="">Select category</option>
                                @foreach ($settings->issuegroups as $category)
                                    <option value="{{ $category->id }}" {{ old('issuegroup_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Type *</label>
                            <select name="issuetype_id" class="form-select" required>
                                <option value="">Select type</option>
                                @foreach ($settings->issuetypes as $type)
                                    <option value="{{ $type->id }}" {{ old('issuetype_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description *</label>
                        <textarea name="description" class="form-textarea" required 
                            placeholder="Provide detailed information about your issue">{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Priority *</label>
                        <select name="priority" class="form-select" required>
                            <option value="Medium" {{ old('priority', 'Medium') == 'Medium' ? 'selected' : '' }}>Medium</option>
                            <option value="Low" {{ old('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                            <option value="High" {{ old('priority') == 'High' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">Organisation name *</label>
                            <input type="text" name="name" class="form-input" 
                                placeholder="Your full name">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-input" readonly
                                value="{{ old('email', auth()->user()->email) }}"
                                placeholder="your.email@example.com">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-input"
                                value="{{ old('phone') }}"
                                placeholder="Your phone number">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Praz Number</label>
                            <input type="text" name="regnumber" class="form-input"
                                value="{{ old('regnumber') }}"
                                placeholder="Registration or reference number">
                        </div>
                    </div>

                

                    <div class="form-group">
                        <label class="form-label">Attachments</label>
                        <input type="file" name="attachments[]" class="form-input" multiple
                            accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif">
                        <small style="color: #6b7280; font-size: 0.875rem;">
                            You can upload multiple files. Max 10MB per file.
                        </small>
                    </div>

                    <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                        <a href="/tickets" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Ticket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>

@endsection
