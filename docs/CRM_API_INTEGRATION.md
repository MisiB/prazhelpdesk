# PrazCRM Admin API Integration Guide

This guide will help you integrate the actual prazcrmadmin API endpoints into your support portal.

## Current Service Location

The CRM API integration service is located at:
- **File**: `app/Services/CrmApiService.php`
- **Config**: `config/services.php` (prazcrmadmin section)

## Step 1: Review Your API Documentation

Visit your API documentation at: `https://prazcrmadmin.test/docs/api`

Document the following information:

### Authentication
- [ ] Authentication method (Bearer token, API key, OAuth, etc.)
- [ ] Header format
- [ ] Token/key location

### Base URL
- [ ] API base URL (e.g., `https://prazcrmadmin.test/api/v1`)

### Available Endpoints

Create a list of endpoints you want to integrate:

#### Customer Endpoints
```
GET    /api/customers              - List all customers
GET    /api/customers/{id}         - Get customer details
POST   /api/customers              - Create customer
PUT    /api/customers/{id}         - Update customer
DELETE /api/customers/{id}         - Delete customer
```

#### Ticket/Support Endpoints
```
GET    /api/tickets                - List all tickets
GET    /api/tickets/{id}           - Get ticket details
POST   /api/tickets                - Create ticket
PUT    /api/tickets/{id}           - Update ticket
DELETE /api/tickets/{id}           - Delete ticket
```

#### Other Endpoints
```
(Add any other endpoints you need)
```

## Step 2: Update Configuration

Edit `.env` file:

```env
PRAZCRMADMIN_API_URL=https://prazcrmadmin.test
PRAZCRMADMIN_API_KEY=your_actual_api_key_here
PRAZCRMADMIN_API_TIMEOUT=30
```

## Step 3: Update CrmApiService

Based on your actual API documentation, update the methods in `app/Services/CrmApiService.php`.

### Template for Adding New Endpoints

```php
/**
 * Description of what this endpoint does
 * 
 * @param mixed $param Parameter description
 * @return array|null
 */
public function methodName($param)
{
    return $this->get('/api/your-endpoint', ['param' => $param]);
}
```

### Example: Adding a New Customer Endpoint

If your API has a "search customers" endpoint:

```php
/**
 * Search customers by query
 * 
 * @param string $query Search query
 * @return array|null
 */
public function searchCustomers(string $query)
{
    return $this->get('/api/customers/search', ['q' => $query]);
}
```

## Step 4: Test the Integration

Create a test script or use Laravel Tinker:

```bash
php artisan tinker
```

```php
// Test the service
$crm = app(\App\Services\CrmApiService::class);

// Test getting customers
$customers = $crm->getCustomers();
dd($customers);

// Test getting a specific customer
$customer = $crm->getCustomer(1);
dd($customer);
```

## Step 5: Handle Response Formats

Your API might return data in a specific format. Update the service methods to handle this:

```php
public function getCustomers(array $filters = [])
{
    $response = $this->get('/api/customers', $filters);
    
    // If your API wraps responses in a 'data' key:
    return $response['data'] ?? $response;
}
```

## Common API Patterns

### Pagination

If your API uses pagination:

```php
public function getCustomers(int $page = 1, int $perPage = 15, array $filters = [])
{
    return $this->get('/api/customers', array_merge($filters, [
        'page' => $page,
        'per_page' => $perPage,
    ]));
}
```

### Filtering

If your API supports filtering:

```php
public function getTickets(array $filters = [])
{
    $params = [];
    
    if (isset($filters['status'])) {
        $params['status'] = $filters['status'];
    }
    
    if (isset($filters['priority'])) {
        $params['priority'] = $filters['priority'];
    }
    
    return $this->get('/api/tickets', $params);
}
```

### Nested Resources

For nested resources like customer orders:

```php
public function getCustomerOrders(int $customerId, array $filters = [])
{
    return $this->get("/api/customers/{$customerId}/orders", $filters);
}
```

## Step 6: Add DELETE Method (if needed)

The service currently has GET, POST, and PUT. If you need DELETE:

```php
/**
 * Make a DELETE request to the CRM API
 */
protected function delete(string $endpoint)
{
    try {
        $response = Http::timeout($this->timeout)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])
            ->delete($this->baseUrl . $endpoint);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('CRM API DELETE request failed', [
            'endpoint' => $endpoint,
            'status' => $response->status(),
            'response' => $response->body(),
        ]);

        return null;
    } catch (Exception $e) {
        Log::error('CRM API DELETE request exception', [
            'endpoint' => $endpoint,
            'error' => $e->getMessage(),
        ]);
        return null;
    }
}

// Usage
public function deleteCustomer(int $customerId)
{
    return $this->delete("/api/customers/{$customerId}");
}
```

## Step 7: Error Handling

Enhance error handling for specific cases:

```php
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

        // Handle specific error codes
        if ($response->status() === 401) {
            Log::error('CRM API authentication failed - check API key');
        } elseif ($response->status() === 404) {
            Log::warning('CRM API resource not found', ['endpoint' => $endpoint]);
        } elseif ($response->status() === 429) {
            Log::warning('CRM API rate limit exceeded');
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
```

## Testing Checklist

- [ ] Test authentication (verify API key works)
- [ ] Test GET requests (fetch data)
- [ ] Test POST requests (create resources)
- [ ] Test PUT requests (update resources)
- [ ] Test error handling (invalid data, 404s, etc.)
- [ ] Test timeout handling
- [ ] Check log files for errors

## Need Help?

1. Check Laravel logs: `storage/logs/laravel.log`
2. Enable debug mode: Set `APP_DEBUG=true` in `.env`
3. Use `dd()` to inspect responses
4. Check API documentation for exact endpoint formats

## Next Steps

Once you've updated the service with your actual API endpoints:

1. Update the documentation in this file
2. Create tests for the integration
3. Set up monitoring for API failures
4. Configure rate limiting if needed
5. Set up webhook listeners if your API supports them












