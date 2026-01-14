<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->string('subject');
            $table->longText('description');
            $table->enum('status', ['open', 'in_progress', 'waiting_customer', 'resolved', 'closed'])->default('open');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->uuid('user_id'); // Customer
            $table->uuid('assigned_to')->nullable(); // Agent
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->integer('crm_customer_id')->nullable(); // Reference to prazcrmadmin customer
            $table->integer('crm_ticket_id')->nullable(); // Synced ticket ID from prazcrmadmin
            $table->json('ai_suggestions')->nullable(); // AI-generated suggestions
            $table->float('ai_confidence_score')->nullable();
            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};



