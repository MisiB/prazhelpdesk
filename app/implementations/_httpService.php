<?php

namespace App\implementations;

use App\interfaces\ihttpService;
use Illuminate\Support\Facades\Http;
class _httpService implements ihttpService
{
    /**
     * Create a new class instance.
     */
    protected string $baseUrl;
    public function __construct()
    {
        $this->baseUrl = config('services.prazcrmadmin.url');
       
    }

    public function getsettings()
    {
        $response = Http::get($this->baseUrl . '/helpdesk/settings');
        return $response->object();
    }

    public function gettickets($email)
    {
        $response = Http::get($this->baseUrl . '/helpdesk/tickets/' . $email);
        return $response->object();
    }

    public function getticket($id)
    {
        $response = Http::get($this->baseUrl . '/helpdesk/tickets/'. $id."/show");
        return $response->object();
    }

    public function createTicket($ticket)
    {
        $response = Http::post($this->baseUrl . '/helpdesk/tickets', $ticket);
      
        return $response->object();
    }

    public function updateTicket($id, $ticket)
    {
        $response = Http::put($this->baseUrl . '/helpdesk/tickets/' . $id, $ticket);
        return $response->object();
    }

    public function deleteTicket($id)
    {
        $response = Http::delete($this->baseUrl . '/helpdesk/tickets/' . $id);
        return $response->object();
    }

    public function addcomment($comment)
    {
        $response = Http::post($this->baseUrl . '/helpdesk/comments', $comment);
        return $response->object();
    }
    public function updatecomment($commentid, $comment)
    {
        $response = Http::put($this->baseUrl . '/helpdesk/comments/' . $commentid, $comment);
        return $response->object();
    }
    public function deletecomment($commentid)
    {
        $response = Http::delete($this->baseUrl . '/helpdesk/comments/' . $commentid);
        return $response->object();
    }

    public function closeTicket($ticketId)
    {
        $response = Http::put($this->baseUrl . '/helpdesk/tickets/' . $ticketId . '/close');
        return $response->object();
    }
    public function searchKnowledgeBase($query)
    {
        try {
            $response = Http::timeout(30)->get($this->baseUrl . '/knowledge-base/search', [
                'query' => $query
            ]);
            
            if ($response->successful()) {
                return $response->object();
            }
            
            \Log::error('Knowledge Base Search API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'query' => $query
            ]);
            
            return null;
        } catch (\Exception $e) {
            \Log::error('Knowledge Base Search Exception: ' . $e->getMessage(), [
                'query' => $query
            ]);
            throw $e;
        }
    }
    
    public function getKnowledgeBaseArticles($params = [])
    {
        // Determine the endpoint based on parameters
        if (isset($params['featured']) && $params['featured']) {
            $endpoint = '/knowledge-base/featured';
            unset($params['featured']); // Remove from query params
        } elseif (isset($params['popular']) && $params['popular']) {
            $endpoint = '/knowledge-base/popular';
            unset($params['popular']); // Remove from query params
        } else {
            $endpoint = '/knowledge-base';
        }
        
        try {
            $response = Http::timeout(30)->get($this->baseUrl . $endpoint, $params);
            
            if ($response->successful()) {
                return $response->object();
            }
            
            \Log::error('Knowledge Base Articles API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'endpoint' => $endpoint
            ]);
            
            return null;
        } catch (\Exception $e) {
            \Log::error('Knowledge Base Articles Exception: ' . $e->getMessage(), [
                'endpoint' => $endpoint,
                'params' => $params
            ]);
            throw $e;
        }
    }
    
    public function getKnowledgeBaseArticle($id)
    {
        try {
            $response = Http::timeout(30)->get($this->baseUrl . '/knowledge-base/' . $id);
            
            if ($response->successful()) {
                return $response->object();
            }
            
            \Log::error('Knowledge Base Article API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'id' => $id
            ]);
            
            return null;
        } catch (\Exception $e) {
            \Log::error('Knowledge Base Article Exception: ' . $e->getMessage(), [
                'id' => $id
            ]);
            throw $e;
        }
    }
    
    public function getKnowledgeBaseCategories()
    {
        try {
            $response = Http::timeout(30)->get($this->baseUrl . '/knowledge-base/categories');
            
            if ($response->successful()) {
                return $response->object();
            }
            
            \Log::error('Knowledge Base Categories API Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return null;
        } catch (\Exception $e) {
            \Log::error('Knowledge Base Categories Exception: ' . $e->getMessage());
            throw $e;
        }
    }
}
