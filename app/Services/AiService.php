<?php

namespace App\Services;

use App\Models\KnowledgeBase;
use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiService
{
    /**
     * Search knowledge base using AI-powered semantic search
     * This is a basic implementation. For production, integrate with OpenAI, Claude, or similar
     */
    public function searchKnowledgeBase(string $query, int $limit = 5): array
    {
        // Normalize query
        $normalizedQuery = strtolower(trim($query));
        $keywords = $this->extractKeywords($normalizedQuery);

        // Search in knowledge base
        $results = KnowledgeBase::published()
            ->where(function ($q) use ($normalizedQuery, $keywords) {
                $q->where('title', 'LIKE', "%{$normalizedQuery}%")
                  ->orWhere('content', 'LIKE', "%{$normalizedQuery}%")
                  ->orWhere('excerpt', 'LIKE', "%{$normalizedQuery}%");
                
                // Search for individual keywords
                foreach ($keywords as $keyword) {
                    $q->orWhere('title', 'LIKE', "%{$keyword}%")
                      ->orWhere('content', 'LIKE', "%{$keyword}%");
                }
            })
            ->with(['category', 'tags', 'author'])
            ->get();

        // Calculate relevance scores
        $scoredResults = $results->map(function ($article) use ($normalizedQuery, $keywords) {
            $score = $this->calculateRelevanceScore($article, $normalizedQuery, $keywords);
            return [
                'article' => $article,
                'score' => $score,
                'excerpt' => $this->generateHighlightedExcerpt($article->content, $keywords),
            ];
        });

        // Sort by score and return top results
        return $scoredResults->sortByDesc('score')
            ->take($limit)
            ->values()
            ->toArray();
    }

    /**
     * Calculate relevance score for an article
     */
    protected function calculateRelevanceScore($article, string $query, array $keywords): float
    {
        $score = 0;

        // Title match (highest weight)
        if (stripos($article->title, $query) !== false) {
            $score += 10;
        }

        // Keyword matches in title
        foreach ($keywords as $keyword) {
            if (stripos($article->title, $keyword) !== false) {
                $score += 5;
            }
        }

        // Content matches
        $contentLower = strtolower($article->content);
        $titleLower = strtolower($article->title);
        
        foreach ($keywords as $keyword) {
            $score += substr_count($contentLower, $keyword) * 2;
            $score += substr_count($titleLower, $keyword) * 3;
        }

        // Boost for helpfulness
        $score += ($article->helpful_count - $article->not_helpful_count) * 0.5;

        // Boost for popularity
        $score += $article->views * 0.01;

        // Boost for featured articles
        if ($article->is_featured) {
            $score += 5;
        }

        return $score;
    }

    /**
     * Extract keywords from query
     */
    protected function extractKeywords(string $query): array
    {
        // Remove common stop words
        $stopWords = ['the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 
                      'of', 'with', 'by', 'from', 'as', 'is', 'was', 'are', 'were', 'how', 
                      'what', 'when', 'where', 'why', 'can', 'do', 'does', 'i', 'my'];

        $words = explode(' ', $query);
        $keywords = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 2 && !in_array($word, $stopWords);
        });

        return array_values($keywords);
    }

    /**
     * Generate highlighted excerpt
     */
    protected function generateHighlightedExcerpt(string $content, array $keywords, int $length = 200): string
    {
        // Find the best position to extract excerpt (where most keywords appear)
        $contentLower = strtolower($content);
        $bestPosition = 0;
        $maxScore = 0;

        for ($i = 0; $i < strlen($content) - $length; $i += 50) {
            $section = substr($contentLower, $i, $length);
            $score = 0;
            
            foreach ($keywords as $keyword) {
                $score += substr_count($section, $keyword);
            }

            if ($score > $maxScore) {
                $maxScore = $score;
                $bestPosition = $i;
            }
        }

        $excerpt = substr($content, $bestPosition, $length);
        
        // Clean up excerpt
        $excerpt = strip_tags($excerpt);
        $excerpt = preg_replace('/\s+/', ' ', $excerpt);
        $excerpt = trim($excerpt);

        // Add ellipsis
        if ($bestPosition > 0) {
            $excerpt = '...' . $excerpt;
        }
        if ($bestPosition + $length < strlen($content)) {
            $excerpt .= '...';
        }

        return $excerpt;
    }

    /**
     * Generate AI suggestions for a ticket
     */
    public function generateTicketSuggestions(Ticket $ticket): array
    {
        $suggestions = [];
        $confidenceScore = 0;

        // Search for related knowledge base articles
        $relatedArticles = $this->searchKnowledgeBase($ticket->subject . ' ' . $ticket->description, 3);
        
        if (!empty($relatedArticles)) {
            $suggestions['related_articles'] = array_map(function($item) {
                return [
                    'id' => $item['article']->id,
                    'title' => $item['article']->title,
                    'score' => $item['score'],
                    'excerpt' => $item['excerpt'],
                ];
            }, $relatedArticles);
            
            $confidenceScore += min($relatedArticles[0]['score'] ?? 0, 50);
        }

        // Find similar resolved tickets
        $similarTickets = $this->findSimilarTickets($ticket);
        
        if (!empty($similarTickets)) {
            $suggestions['similar_tickets'] = $similarTickets;
            $confidenceScore += 20;
        }

        // Suggest category based on content
        $suggestedCategory = $this->suggestCategory($ticket);
        
        if ($suggestedCategory) {
            $suggestions['suggested_category'] = $suggestedCategory;
            $confidenceScore += 15;
        }

        // Suggest priority based on keywords
        $suggestedPriority = $this->suggestPriority($ticket);
        
        if ($suggestedPriority && $suggestedPriority !== $ticket->priority) {
            $suggestions['suggested_priority'] = $suggestedPriority;
            $confidenceScore += 10;
        }

        // Generate auto-response suggestion
        $autoResponse = $this->generateAutoResponse($ticket, $relatedArticles);
        
        if ($autoResponse) {
            $suggestions['auto_response'] = $autoResponse;
            $confidenceScore += 20;
        }

        return [
            'suggestions' => $suggestions,
            'confidence_score' => min($confidenceScore, 100),
        ];
    }

    /**
     * Find similar resolved tickets
     */
    protected function findSimilarTickets(Ticket $ticket, int $limit = 3): array
    {
        $keywords = $this->extractKeywords(strtolower($ticket->subject . ' ' . $ticket->description));
        
        $similar = Ticket::closed()
            ->where('id', '!=', $ticket->id)
            ->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('subject', 'LIKE', "%{$keyword}%")
                      ->orWhere('description', 'LIKE', "%{$keyword}%");
                }
            })
            ->with(['assignedAgent'])
            ->limit($limit)
            ->get();

        return $similar->map(function ($t) {
            return [
                'id' => $t->id,
                'ticket_number' => $t->ticket_number,
                'subject' => $t->subject,
                'resolution_time' => $t->resolution_time,
                'assigned_agent' => $t->assignedAgent?->name,
            ];
        })->toArray();
    }

    /**
     * Suggest category based on ticket content
     */
    protected function suggestCategory(Ticket $ticket)
    {
        // Simple keyword-based categorization
        // In production, use ML model or more sophisticated NLP
        
        $contentLower = strtolower($ticket->subject . ' ' . $ticket->description);
        
        $categoryKeywords = [
            'billing' => ['invoice', 'payment', 'bill', 'charge', 'refund', 'price', 'cost'],
            'technical' => ['error', 'bug', 'crash', 'issue', 'problem', 'not working', 'broken'],
            'account' => ['password', 'login', 'access', 'account', 'username', 'email'],
            'feature' => ['feature', 'request', 'suggestion', 'enhancement', 'improvement'],
            'general' => ['question', 'how to', 'help', 'support', 'information'],
        ];

        $categoryScores = [];
        
        foreach ($categoryKeywords as $categoryName => $keywords) {
            $score = 0;
            foreach ($keywords as $keyword) {
                if (stripos($contentLower, $keyword) !== false) {
                    $score += substr_count($contentLower, $keyword);
                }
            }
            if ($score > 0) {
                $categoryScores[$categoryName] = $score;
            }
        }

        if (empty($categoryScores)) {
            return null;
        }

        $suggestedCategoryName = array_search(max($categoryScores), $categoryScores);
        
        // Find actual category
        $category = \App\Models\Category::where('name', 'LIKE', "%{$suggestedCategoryName}%")->first();
        
        return $category ? [
            'id' => $category->id,
            'name' => $category->name,
            'confidence' => min(max($categoryScores) * 10, 100),
        ] : null;
    }

    /**
     * Suggest priority based on content
     */
    protected function suggestPriority(Ticket $ticket): ?string
    {
        $contentLower = strtolower($ticket->subject . ' ' . $ticket->description);
        
        $urgentKeywords = ['urgent', 'emergency', 'critical', 'asap', 'immediately', 'down', 'broken', 'not working'];
        $highKeywords = ['important', 'soon', 'quickly', 'priority', 'needed'];
        
        foreach ($urgentKeywords as $keyword) {
            if (stripos($contentLower, $keyword) !== false) {
                return 'urgent';
            }
        }
        
        foreach ($highKeywords as $keyword) {
            if (stripos($contentLower, $keyword) !== false) {
                return 'high';
            }
        }
        
        return null;
    }

    /**
     * Generate auto-response based on knowledge base articles
     */
    protected function generateAutoResponse(Ticket $ticket, array $relatedArticles): ?string
    {
        if (empty($relatedArticles) || $relatedArticles[0]['score'] < 10) {
            return null;
        }

        $topArticle = $relatedArticles[0]['article'];
        
        $response = "Thank you for contacting support. I found a relevant article that might help:\n\n";
        $response .= "**{$topArticle->title}**\n\n";
        $response .= $relatedArticles[0]['excerpt'] . "\n\n";
        $response .= "You can read the full article here: [View Article]\n\n";
        $response .= "If this doesn't resolve your issue, our support team will assist you shortly.";
        
        return $response;
    }

    /**
     * Analyze ticket sentiment
     */
    public function analyzeSentiment(string $text): array
    {
        // Basic sentiment analysis
        // In production, integrate with sentiment analysis API
        
        $positiveWords = ['thank', 'thanks', 'please', 'appreciate', 'good', 'great', 'excellent', 'happy'];
        $negativeWords = ['angry', 'frustrated', 'terrible', 'awful', 'worst', 'hate', 'disappointed', 'annoyed'];
        
        $textLower = strtolower($text);
        $positiveCount = 0;
        $negativeCount = 0;
        
        foreach ($positiveWords as $word) {
            $positiveCount += substr_count($textLower, $word);
        }
        
        foreach ($negativeWords as $word) {
            $negativeCount += substr_count($textLower, $word);
        }
        
        $total = $positiveCount + $negativeCount;
        
        if ($total === 0) {
            return ['sentiment' => 'neutral', 'score' => 0];
        }
        
        $score = (($positiveCount - $negativeCount) / $total) * 100;
        
        if ($score > 20) {
            $sentiment = 'positive';
        } elseif ($score < -20) {
            $sentiment = 'negative';
        } else {
            $sentiment = 'neutral';
        }
        
        return [
            'sentiment' => $sentiment,
            'score' => $score,
            'positive_indicators' => $positiveCount,
            'negative_indicators' => $negativeCount,
        ];
    }
}

