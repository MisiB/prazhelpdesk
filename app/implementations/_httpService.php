<?php

namespace App\implementations;

use App\interfaces\ihttpService;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class _httpService implements ihttpService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.helpdesk.url');
        $this->apiKey = config('services.helpdesk.api_key', '');
        $this->timeout = config('services.helpdesk.timeout', 30);
    }

    protected function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->withHeaders(['X-API-Key' => $this->apiKey])
            ->acceptJson()
            ->timeout($this->timeout)
            ->retry(2, 500);
    }

    protected function publicClient(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->timeout($this->timeout)
            ->retry(2, 500);
    }

    // ──────────────────────────────────────────
    // Public Endpoints (no API key needed)
    // ──────────────────────────────────────────

    public function getsettings()
    {
        $response = $this->publicClient()->get('/helpdesk/settings');
        return $response->object();
    }

    public function createTicket($ticket)
    {
        $response = $this->publicClient()->post('/helpdesk/tickets', $ticket);
        return $response->object();
    }

    // ──────────────────────────────────────────
    // Protected Endpoints (require API key)
    // ──────────────────────────────────────────

    public function gettickets($email)
    {
        $response = $this->client()->get('/helpdesk/tickets/' . $email);
        return $response->object();
    }

    public function getticket($id)
    {
        $response = $this->client()->get('/helpdesk/tickets/' . $id . '/show');
        return $response->object();
    }

    public function updateTicket($id, $ticket)
    {
        $response = $this->client()->put('/helpdesk/tickets/' . $id, $ticket);
        return $response->object();
    }

    public function deleteTicket($id)
    {
        $response = $this->client()->delete('/helpdesk/tickets/' . $id);
        return $response->object();
    }

    public function closeTicket($ticketId)
    {
        $response = $this->client()->put('/helpdesk/tickets/' . $ticketId . '/close');
        return $response->object();
    }

    public function addcomment($comment)
    {
        $response = $this->client()->post('/helpdesk/comments', $comment);
        return $response->object();
    }

    public function updatecomment($commentid, $comment)
    {
        $response = $this->client()->put('/helpdesk/comments/' . $commentid, $comment);
        return $response->object();
    }

    public function deletecomment($commentid)
    {
        $response = $this->client()->delete('/helpdesk/comments/' . $commentid);
        return $response->object();
    }

    // ──────────────────────────────────────────
    // Knowledge Base (public, no API key needed)
    // ──────────────────────────────────────────

    public function searchKnowledgeBase($query)
    {
        try {
            $response = $this->publicClient()->get('/knowledge-base/search', [
                'query' => $query,
            ]);

            if ($response->successful()) {
                return $response->object();
            }

            Log::error('Knowledge Base Search API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'query' => $query,
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Knowledge Base Search Exception: ' . $e->getMessage(), [
                'query' => $query,
            ]);
            throw $e;
        }
    }

    public function getKnowledgeBaseArticles($params = [])
    {
        if (isset($params['featured']) && $params['featured']) {
            $endpoint = '/knowledge-base/featured';
            unset($params['featured']);
        } elseif (isset($params['popular']) && $params['popular']) {
            $endpoint = '/knowledge-base/popular';
            unset($params['popular']);
        } else {
            $endpoint = '/knowledge-base';
        }

        try {
            $response = $this->publicClient()->get($endpoint, $params);

            if ($response->successful()) {
                return $response->object();
            }

            Log::error('Knowledge Base Articles API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'endpoint' => $endpoint,
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Knowledge Base Articles Exception: ' . $e->getMessage(), [
                'endpoint' => $endpoint,
                'params' => $params,
            ]);
            throw $e;
        }
    }

    public function getKnowledgeBaseArticle($id)
    {
        try {
            $response = $this->publicClient()->get('/knowledge-base/' . $id);

            if ($response->successful()) {
                return $response->object();
            }

            Log::error('Knowledge Base Article API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'id' => $id,
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Knowledge Base Article Exception: ' . $e->getMessage(), [
                'id' => $id,
            ]);
            throw $e;
        }
    }

    public function getKnowledgeBaseCategories()
    {
        try {
            $response = $this->publicClient()->get('/knowledge-base/categories');

            if ($response->successful()) {
                return $response->object();
            }

            Log::error('Knowledge Base Categories API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Knowledge Base Categories Exception: ' . $e->getMessage());
            throw $e;
        }
    }
}
