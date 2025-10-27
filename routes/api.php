<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// WorkOS Webhook Route (No auth - verified by signature)
Route::post('/webhooks/workos', [App\Http\Controllers\Auth\WorkOsWebhookController::class, 'handle'])
    ->name('webhooks.workos');

// Public Knowledge Base Routes (No authentication required)
Route::prefix('knowledge-base')->group(function () {
    Route::get('/', [KnowledgeBaseController::class, 'index']);
    Route::get('/popular', [KnowledgeBaseController::class, 'popular']);
    Route::get('/featured', [KnowledgeBaseController::class, 'featured']);
    Route::get('/categories', [KnowledgeBaseController::class, 'categories']);
    Route::get('/tags', [KnowledgeBaseController::class, 'tags']);
    Route::get('/search', [KnowledgeBaseController::class, 'aiSearch']);
    Route::get('/{slug}', [KnowledgeBaseController::class, 'show']);
    Route::post('/{id}/helpful', [KnowledgeBaseController::class, 'markHelpful']);
    Route::post('/{id}/not-helpful', [KnowledgeBaseController::class, 'markNotHelpful']);
});

// Protected Routes (Require authentication)

    
    // Ticket Routes
    Route::prefix('tickets')->group(function () {
        Route::get('/', [TicketController::class, 'index']);
        Route::post('/', [TicketController::class, 'store']);
        Route::get('/statistics', [TicketController::class, 'statistics']);
        Route::get('/{id}', [TicketController::class, 'show']);
        Route::put('/{id}', [TicketController::class, 'update']);
        Route::delete('/{id}', [TicketController::class, 'destroy']);
        Route::post('/{id}/comments', [TicketController::class, 'addComment']);
        Route::post('/{id}/attachments', [TicketController::class, 'uploadAttachment']);
        Route::post('/{id}/assign', [TicketController::class, 'assign']);
    });

    // Knowledge Base Management (Admin only - add middleware as needed)
    Route::prefix('admin/knowledge-base')->group(function () {
        Route::post('/', [KnowledgeBaseController::class, 'store']);
        Route::put('/{id}', [KnowledgeBaseController::class, 'update']);
        Route::delete('/{id}', [KnowledgeBaseController::class, 'destroy']);
    });

    // Dashboard Routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/overview', [DashboardController::class, 'overview']);
        Route::get('/ticket-trends', [DashboardController::class, 'ticketTrends']);
        Route::get('/tickets-by-category', [DashboardController::class, 'ticketsByCategory']);
        Route::get('/tickets-by-priority', [DashboardController::class, 'ticketsByPriority']);
        Route::get('/recent-tickets', [DashboardController::class, 'recentTickets']);
        Route::get('/top-articles', [DashboardController::class, 'topArticles']);
        Route::get('/agent-performance', [DashboardController::class, 'agentPerformance']);
        Route::get('/crm-status', [DashboardController::class, 'crmStatus']);
    });



