<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class CrmApiService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.prazcrmadmin.url');
        $this->apiKey = config('services.prazcrmadmin.api_key');
        $this->timeout = config('services.prazcrmadmin.timeout', 30);
    }

    /**
     * Make a GET request to the CRM API
     */
    protected function get(string $endpoint, array $params = [])
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get($this->baseUrl . $endpoint, $params);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('CRM API GET request failed', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (Exception $e) {
            Log::error('CRM API GET request exception', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Make a POST request to the CRM API
     */
    protected function post(string $endpoint, array $data = [])
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->post($this->baseUrl . $endpoint, $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('CRM API POST request failed', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (Exception $e) {
            Log::error('CRM API POST request exception', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Make a PUT request to the CRM API
     */
    protected function put(string $endpoint, array $data = [])
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->put($this->baseUrl . $endpoint, $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('CRM API PUT request failed', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (Exception $e) {
            Log::error('CRM API PUT request exception', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get customer details from CRM
     */
    public function getCustomer(int $customerId)
    {
        return $this->get("/api/customers/{$customerId}");
    }

    /**
     * Get all customers from CRM
     */
    public function getCustomers(array $filters = [])
    {
        return $this->get('/api/customers', $filters);
    }

    /**
     * Create a new ticket in CRM
     */
    public function createTicket(array $ticketData)
    {
        return $this->post('/api/tickets', $ticketData);
    }

    /**
     * Update a ticket in CRM
     */
    public function updateTicket(int $ticketId, array $ticketData)
    {
        return $this->put("/api/tickets/{$ticketId}", $ticketData);
    }

    /**
     * Get ticket details from CRM
     */
    public function getTicket(int $ticketId)
    {
        return $this->get("/api/tickets/{$ticketId}");
    }

    /**
     * Get all tickets from CRM
     */
    public function getTickets(array $filters = [])
    {
        return $this->get('/api/tickets', $filters);
    }

    /**
     * Sync customer data from CRM
     */
    public function syncCustomer(int $customerId)
    {
        $customerData = $this->getCustomer($customerId);
        
        if (!$customerData) {
            return null;
        }

        // Process and return customer data
        // You can add logic here to sync with local database if needed
        return $customerData;
    }

    /**
     * Sync ticket with CRM
     */
    public function syncTicket(int $localTicketId, int $crmTicketId = null)
    {
        $ticket = \App\Models\Ticket::find($localTicketId);
        
        if (!$ticket) {
            return null;
        }

        $ticketData = [
            'subject' => $ticket->subject,
            'description' => $ticket->description,
            'status' => $ticket->status,
            'priority' => $ticket->priority,
            'customer_id' => $ticket->crm_customer_id,
        ];

        if ($crmTicketId) {
            // Update existing ticket
            $response = $this->updateTicket($crmTicketId, $ticketData);
        } else {
            // Create new ticket
            $response = $this->createTicket($ticketData);
            
            if ($response && isset($response['id'])) {
                $ticket->update(['crm_ticket_id' => $response['id']]);
            }
        }

        return $response;
    }

    /**
     * Get customer support history
     */
    public function getCustomerSupportHistory(int $customerId)
    {
        return $this->get("/api/customers/{$customerId}/support-history");
    }

    /**
     * Get customer orders
     */
    public function getCustomerOrders(int $customerId)
    {
        return $this->get("/api/customers/{$customerId}/orders");
    }

    /**
     * Search knowledge base in CRM
     */
    public function searchCrmKnowledgeBase(string $query)
    {
        return $this->get('/api/knowledge-base/search', ['q' => $query]);
    }
}

