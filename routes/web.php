<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\KnowledgeBaseController;
Route::get('/', function () {
    return view('home');
});

Route::get('/aiasstant', function () {
    return view('aiasstant');
});

        Route::get('/knowledge-base', [KnowledgeBaseController::class, 'index'])->name('knowledge-base.index');
        Route::get('/knowledge-base/{id}', [KnowledgeBaseController::class, 'show'])->name('knowledge-base.show');
Route::middleware('auth')->group(function () {
Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
Route::get('/tickets/{id}/show', [TicketController::class, 'show'])->name('tickets.show');
Route::post('/tickets', [TicketController::class, 'createTicket'])->name('tickets.store');
Route::post('/tickets/{id}/comment', [TicketController::class, 'addComment'])->name('tickets.comment');
Route::put('/tickets/{ticketId}/comment/{commentId}', [TicketController::class, 'updateComment'])->name('tickets.comment.update');
Route::delete('/tickets/{ticketId}/comment/{commentId}', [TicketController::class, 'deleteComment'])->name('tickets.comment.delete');
Route::put('/tickets/{id}/close', [TicketController::class, 'closeTicket'])->name('tickets.close');
Route::put('/tickets/{id}', [TicketController::class, 'update'])->name('tickets.update');
Route::delete('/tickets/{id}', [TicketController::class, 'destroy'])->name('tickets.destroy');
});
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    // WorkOS Authentication Routes
    Route::get('/auth/workos', [AuthController::class, 'redirectToWorkOS'])->name('auth.workos.redirect');
});

// WorkOS Callback (accessible even if authenticated)
Route::get('/auth/callback', [AuthController::class, 'callback'])->name('auth.callback');

// Logout Route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');