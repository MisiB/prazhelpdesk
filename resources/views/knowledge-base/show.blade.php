@extends('layouts.app')

@php
    $articleData = is_array($article->data ?? null) ? (object)($article->data) : ($article->data ?? $article);
@endphp

@section('title', $articleData->title ?? 'Article')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <!-- Breadcrumbs -->
    <div class="card">
        <div class="card-body">
            <div class="breadcrumbs text-sm">
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="/knowledge-base">Knowledge Base</a></li>
                    <li>{{ $articleData->title ?? 'Article' }}</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Article Header -->
    <div class="card">
        <div class="card-body">
            <!-- Featured Badge -->
            @if(isset($articleData->is_featured) && $articleData->is_featured)
            <div style="margin-bottom: 1rem;">
                <span style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: white; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <svg style="width: 1.125rem; height: 1.125rem;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    Featured Article
                </span>
            </div>
            @endif

            <!-- Title -->
            <h1 style="font-size: 2.25rem; font-weight: 700; line-height: 1.2; margin-bottom: 1.5rem; color: #1e293b;">
                {{ $articleData->title ?? 'Untitled Article' }}
            </h1>

            <!-- Excerpt -->
            @if(isset($articleData->excerpt) && $articleData->excerpt)
            <p style="font-size: 1.125rem; color: #64748b; line-height: 1.6; margin-bottom: 1.5rem; font-weight: 400;">
                {{ $articleData->excerpt }}
            </p>
            @endif

            <!-- Metadata -->
            <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e2e8f0; align-items: center;">
                <!-- Author -->
                @if(isset($articleData->author))
                @php
                    $author = is_array($articleData->author) ? (object)$articleData->author : $articleData->author;
                @endphp
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 2.5rem; height: 2.5rem; border-radius: 50%; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1rem;">
                        {{ strtoupper(substr($author->name ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <p style="font-weight: 600; margin: 0; color: #1e293b;">{{ $author->name ?? 'Unknown' }} {{ $author->surname ?? '' }}</p>
                        <p style="font-size: 0.875rem; color: #64748b; margin: 0;">Author</p>
                    </div>
                </div>
                @endif

                <!-- Published Date -->
                @if(isset($articleData->published_at))
                <div style="display: flex; align-items: center; gap: 0.5rem; color: #64748b;">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span style="font-size: 0.875rem;">{{ Carbon\Carbon::parse($articleData->published_at)->format('F d, Y') }}</span>
                </div>
                @endif

                <!-- Views Count -->
                @if(isset($articleData->views_count))
                <div style="display: flex; align-items: center; gap: 0.5rem; color: #64748b;">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <span style="font-size: 0.875rem;">{{ $articleData->views_count }} views</span>
                </div>
                @endif

                <!-- Category -->
                @if(isset($articleData->category) && $articleData->category)
                @php
                    $category = is_array($articleData->category) ? (object)$articleData->category : $articleData->category;
                @endphp
                <span class="badge badge-info">{{ $category->name ?? $articleData->category }}</span>
                @endif
            </div>

            <!-- Tags -->
            @if(isset($articleData->tags) && is_array($articleData->tags) && count($articleData->tags) > 0)
            <div style="margin-top: 1rem; display: flex; flex-wrap: wrap; gap: 0.5rem;">
                @foreach($articleData->tags as $tag)
                @php
                    $tagData = is_array($tag) ? (object)$tag : $tag;
                @endphp
                <span style="padding: 0.25rem 0.75rem; background-color: #f1f5f9; color: #64748b; border-radius: 9999px; font-size: 0.875rem; font-weight: 500;">
                    #{{ $tagData->name ?? $tag }}
                </span>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <!-- External URL Notice -->
    @if(isset($articleData->external_url) && $articleData->external_url)
    <div style="padding: 1rem 1.5rem; background: linear-gradient(135deg, #dbeafe 0%, #e0e7ff 100%); border-left: 4px solid #3b82f6; border-radius: 0.5rem; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: start; gap: 1rem;">
            <svg style="width: 1.5rem; height: 1.5rem; color: #3b82f6; flex-shrink: 0; margin-top: 0.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div style="flex: 1;">
                <p style="font-weight: 600; color: #1e40af; margin: 0 0 0.5rem 0;">External Resource Available</p>
                <p style="color: #1e40af; margin: 0 0 0.75rem 0; font-size: 0.875rem;">This article has additional content available on an external platform.</p>
                <a href="{{ $articleData->external_url }}" target="_blank" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                    View External Content
                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Article Content -->
    <div class="card">
        <div class="card-body">
            <div style="line-height: 1.8; color: #334155; font-size: 1.0625rem;">
                @if(isset($articleData->content))
                    {!! nl2br(e($articleData->content)) !!}
                @else
                    <p style="color: #94a3b8; text-align: center; padding: 3rem 0;">No content available for this article.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div style="margin-top: 2rem; display: flex; justify-content: space-between; align-items: center;">
        <a href="/knowledge-base" class="btn btn-secondary">
            <svg style="width: 1rem; height: 1rem; display: inline; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Knowledge Base
        </a>
    </div>
</div>

<style>
    @media (max-width: 768px) {
        .card-body h1 {
            font-size: 1.75rem !important;
        }
        
        .card-body > div[style*="display: flex"] {
            flex-direction: column !important;
            align-items: flex-start !important;
        }
    }
</style>
@endsection
