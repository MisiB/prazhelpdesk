<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'subject',
        'description',
        'status',
        'priority',
        'category_id',
        'user_id',
        'assigned_to',
        'crm_customer_id',
        'crm_ticket_id',
        'ai_suggestions',
        'ai_confidence_score',
        'first_response_at',
        'resolved_at',
    ];

    protected $casts = [
        'ai_suggestions' => 'array',
        'ai_confidence_score' => 'float',
        'first_response_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = self::generateTicketNumber();
            }
        });
    }

    /**
     * Generate a unique ticket number
     */
    public static function generateTicketNumber(): string
    {
        do {
            $number = 'TKT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (self::where('ticket_number', $number)->exists());

        return $number;
    }

    /**
     * Get the user (customer) that owns the ticket
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the assigned agent
     */
    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get comments for the ticket
     */
    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    /**
     * Get attachments for the ticket
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    /**
     * Scope for open tickets
     */
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress', 'waiting_customer']);
    }

    /**
     * Scope for closed tickets
     */
    public function scopeClosed($query)
    {
        return $query->whereIn('status', ['resolved', 'closed']);
    }

    /**
     * Scope for tickets by priority
     */
    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for urgent tickets
     */
    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    /**
     * Scope for assigned tickets
     */
    public function scopeAssignedTo($query, string $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Check if ticket is open
     */
    public function isOpen(): bool
    {
        return in_array($this->status, ['open', 'in_progress', 'waiting_customer']);
    }

    /**
     * Check if ticket is closed
     */
    public function isClosed(): bool
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

    /**
     * Mark ticket as resolved
     */
    public function markResolved()
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);
    }

    /**
     * Assign ticket to an agent
     */
    public function assignTo(string $agentId)
    {
        $this->update([
            'assigned_to' => $agentId,
            'status' => 'in_progress',
        ]);
    }

    /**
     * Get response time in hours
     */
    public function getResponseTimeAttribute(): ?float
    {
        if (!$this->first_response_at) {
            return null;
        }
        return $this->created_at->diffInHours($this->first_response_at, true);
    }

    /**
     * Get resolution time in hours
     */
    public function getResolutionTimeAttribute(): ?float
    {
        if (!$this->resolved_at) {
            return null;
        }
        return $this->created_at->diffInHours($this->resolved_at, true);
    }
}



