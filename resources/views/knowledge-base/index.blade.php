@extends('layouts.app')

@section('title', 'Knowledge Base')

@section('content')
<div style="max-width: 1400px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">Knowledge Base</h1>
        <p style="color: #64748b; font-size: 1rem;">Browse articles and find answers to common questions</p>
    </div>
    
    <!-- Search -->
    <div class="card">
        <form id="searchForm">
            <div style="position: relative;">
                <svg style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 1.25rem; height: 1.25rem; color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input 
                    type="text" 
                    id="searchInput"
                    placeholder="Search for articles..." 
                    class="form-input"
                    style="padding-left: 3rem; padding-right: 120px;"
                >
                <button type="submit" class="btn btn-primary" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%);">
                    Search
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($articles->data as $article)
            <div class="card">
                <div class="card-header">
                    <h3>{{ $article->title }}</h3>
                </div>
                <div class="card-body">
                    <h3>{{ $article->title }}</h3>
                    <p>{{ $article->excerpt }}</p>
                </div>
                <div class="card-footer">
                    <a href="{{ route('knowledge-base.show', $article->slug) }}" class="btn btn-primary">Read More</a>
                </div>
            </div>
        @endforeach
    </div>
   
</div>

<style>
    .category-btn {
        transition: all 0.2s;
    }
    
    .category-btn.active {
        background-color: #3b82f6 !important;
        color: white !important;
    }
    
    .category-btn:not(.active) {
        background-color: #f1f5f9 !important;
        color: #64748b !important;
    }
    
    .category-btn:not(.active):hover {
        background-color: #e2e8f0 !important;
    }
    
    @media (max-width: 768px) {
        #searchInput {
            padding-right: 8px !important;
        }
        
        #searchForm button[type="submit"] {
            position: static !important;
            transform: none !important;
            width: 100%;
            margin-top: 0.5rem;
        }
        
        #searchForm > div {
            display: flex;
            flex-direction: column;
        }
    }
</style>



@endsection
