<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'color',
    ];

    /**
     * Get knowledge base articles with this tag
     */
    public function knowledgeBase(): BelongsToMany
    {
        return $this->belongsToMany(KnowledgeBase::class, 'knowledge_base_tag');
    }
}



