<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBase;
use App\Models\Category;
use App\Models\Tag;
use App\Services\AiService;
use App\interfaces\ihttpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class KnowledgeBaseController extends Controller
{
    protected $httpService;

    public function __construct(ihttpService $httpService)
    {
        $this->httpService = $httpService;
    }

    /**
     * Display a listing of knowledge base articles
     */
    public function index(Request $request)
    {
        try {
            $response = $this->httpService->getKnowledgeBaseArticles();
            
            // The API returns data in a nested structure
            $articles = $response->data ?? [];
            
            
            return view('knowledge-base.index', compact('articles'));
        } catch (\Exception $e) {
            \Log::error('Knowledge Base Articles Error: ' . $e->getMessage());
            return response()->json(['data' => []], 500);
        }
    }
    public function show($id)
    {
        try {
            $response = $this->httpService->getKnowledgeBaseArticle($id);
            
            // Handle the nested response structure
            if (isset($response->success) && $response->success) {
                $article = $response;
            } else {
                $article = $response;
            }
            
            return view('knowledge-base.show', compact('article'));
        } catch (\Exception $e) {
            \Log::error('Knowledge Base Article Error: ' . $e->getMessage(), ['id' => $id]);
            abort(404, 'Article not found');
        }
    }

    /**
     * Display popular articles
     */
    public function popular()
    {
        try {
            $response = $this->httpService->getKnowledgeBaseArticles(['popular' => true]);
            
            if (isset($response->success) && $response->success) {
                $articles = $response->data->data ?? [];
            } else {
                $articles = [];
            }
            
            return view('knowledge-base.index', compact('articles'));
        } catch (\Exception $e) {
            \Log::error('Knowledge Base Popular Articles Error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    /**
     * Display featured articles
     */
    public function featured()
    {
        try {
            $response = $this->httpService->getKnowledgeBaseArticles(['featured' => true]);
            
            if (isset($response->success) && $response->success) {
                $articles = $response->data->data ?? [];
            } else {
                $articles = [];
            }
            
            return response()->json($articles);
        } catch (\Exception $e) {
            \Log::error('Knowledge Base Featured Articles Error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    /**
     * AI-powered search
     */
    public function aiSearch(Request $request)
    {
        $query = $request->input('query');
        
        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Search query is required',
                'results' => []
            ], 400);
        }
        
        try {
            $response = $this->httpService->searchKnowledgeBase($query);
            
            // Check if the result is valid
            if (!$response) {
                return response()->json([
                    'success' => false,
                    'message' => 'No results found',
                    'results' => []
                ]);
            }
            
            // Handle nested response structure
            if (isset($response->success) && $response->success) {
                // If the API returns a successful response with results
                return response()->json($response);
            } else {
                // If the API returns an unsuccessful response
                return response()->json([
                    'success' => false,
                    'message' => $response->message ?? 'No results found',
                    'results' => []
                ]);
            }
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Knowledge Base Search Error: ' . $e->getMessage(), [
                'query' => $query,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while searching. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
                'results' => []
            ], 500);
        }
    }


    /**
     * Display the specified article
     */
   
    /**
     * Update the specified article
     */
    public function update(Request $request, $id)
    {
        $article = KnowledgeBase::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'excerpt' => 'nullable|string',
            'category_id' => 'sometimes|exists:categories,id',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'meta_description' => 'nullable|string|max:160',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        // Update slug if title changed
        if (isset($validated['title']) && $validated['title'] !== $article->title) {
            $validated['slug'] = Str::slug($validated['title']);
            
            // Ensure unique slug
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (KnowledgeBase::where('slug', $validated['slug'])->where('id', '!=', $id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $tags = $validated['tags'] ?? null;
        unset($validated['tags']);

        $article->update($validated);

        // Update tags if provided
        if ($tags !== null) {
            $article->tags()->sync($tags);
        }

        return response()->json($article->fresh(['category', 'author', 'tags']));
    }

    /**
     * Mark article as helpful
     */
    public function markHelpful($id)
    {
        $article = KnowledgeBase::findOrFail($id);
        $article->markHelpful();

        return response()->json([
            'message' => 'Thank you for your feedback',
            'helpful_count' => $article->helpful_count,
        ]);
    }

    /**
     * Mark article as not helpful
     */
    public function markNotHelpful($id)
    {
        $article = KnowledgeBase::findOrFail($id);
        $article->markNotHelpful();

        return response()->json([
            'message' => 'Thank you for your feedback',
            'not_helpful_count' => $article->not_helpful_count,
        ]);
    }

    /**
     * Delete an article
     */
    public function destroy($id)
    {
        $article = KnowledgeBase::findOrFail($id);
        $article->delete();

        return response()->json(['message' => 'Article deleted successfully']);
    }

    /**
     * Get categories tree
     */
    public function categories()
    {
        try {
            // Use httpService to fetch categories from external API
            $response = $this->httpService->getKnowledgeBaseCategories();
            
            // Handle nested response structure
            if (isset($response->success) && $response->success) {
                $categories = $response->data ?? [];
            } else {
                $categories = [];
            }
            
            return response()->json($categories);
        } catch (\Exception $e) {
            \Log::error('Knowledge Base Categories Error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    /**
     * Get all tags
     */
    public function tags()
    {
        $tags = Tag::orderBy('name')->get();
        return response()->json($tags);
    }
}




