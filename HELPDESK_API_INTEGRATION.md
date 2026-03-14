# Helpdesk API Integration Guide

This guide is for Laravel applications that consume the PrazAdmin Helpdesk API.

---

## Base URL

```
https://prazadmin2025.test/api/helpdesk
```

> Replace `prazadmin2025.test` with the production domain when deploying.

---

## Authentication

Protected endpoints require a static **API key** sent via the `X-API-Key` header. Public endpoints (ticket creation, tracking) do not require authentication.

### Step 1: Obtain Your API Key

Request an API key from the PrazAdmin administrator. Each consuming application receives its own unique key.

### Step 2: Add to Your `.env`

```env
PRAZADMIN_API_URL=https://prazadmin2025.test/api/helpdesk
PRAZADMIN_API_KEY=your-api-key-here
```

### Step 3: Create a Config File

Create `config/prazadmin.php`:

```php
<?php

return [
    'api_url' => env('PRAZADMIN_API_URL', 'https://prazadmin2025.test/api/helpdesk'),
    'api_key' => env('PRAZADMIN_API_KEY'),
];
```

### Step 4: Create an API Service Class

Create `app/Services/PrazAdminHelpdeskService.php`:

```php
<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class PrazAdminHelpdeskService
{
    protected function client(): PendingRequest
    {
        return Http::baseUrl(config('prazadmin.api_url'))
            ->withHeaders(['X-API-Key' => config('prazadmin.api_key')])
            ->acceptJson()
            ->timeout(30);
    }

    protected function publicClient(): PendingRequest
    {
        return Http::baseUrl(config('prazadmin.api_url'))
            ->acceptJson()
            ->timeout(30);
    }
}
```

---

## Handling 401 Unauthorized

If your API key is missing or invalid, protected endpoints return:

```json
{
    "message": "API key is required."
}
```

or

```json
{
    "message": "Invalid API key."
}
```

**HTTP Status:** `401`

Handle this in your service:

```php
$response = $this->client()->get('/tickets/user@example.com');

if ($response->status() === 401) {
    Log::error('PrazAdmin API: Invalid or missing API key', [
        'response' => $response->json(),
    ]);
}
```

---

## API Endpoints

### Public Endpoints (No API Key Required)

These endpoints use the `publicClient()` method.

---

#### Get Helpdesk Settings

```
GET /settings
```

**Example:**

```php
public function getSettings(): array
{
    return $this->publicClient()->get('/settings')->json();
}
```

---

#### Create a Ticket

```
POST /tickets
```

**Body Parameters:**

| Field            | Type     | Required | Notes                                    |
|------------------|----------|----------|------------------------------------------|
| `issuegroup_id`  | integer  | Yes      | Must exist in `issuegroups` table        |
| `issuetype_id`   | integer  | Yes      | Must exist in `issuetypes` table         |
| `title`          | string   | Yes      | Max 255 characters                       |
| `description`    | string   | Yes      |                                          |
| `priority`       | string   | Yes      | `Low`, `Medium`, or `High`               |
| `name`           | string   | No       | Reporter name, max 255                   |
| `email`          | string   | No       | Reporter email                           |
| `phone`          | string   | No       | Max 50 characters                        |
| `regnumber`      | string   | No       | Registration number, max 100             |
| `department_id`  | integer  | No       | Must exist in `departments` table        |
| `attachments`    | array    | No       | Array of file objects (see below)        |

**Attachment Object:**

| Field       | Type   | Required          | Notes                                |
|-------------|--------|-------------------|--------------------------------------|
| `name`      | string | If attachments set | Filename, must end in `.pdf`, `.png`, `.jpg`, or `.jpeg` |
| `data`      | string | If attachments set | Base64 encoded file data             |
| `mime_type` | string | If attachments set | `application/pdf`, `image/png`, or `image/jpeg` |

**Example:**

```php
public function createTicket(array $data): array
{
    return $this->publicClient()->post('/tickets', [
        'issuegroup_id' => $data['issuegroup_id'],
        'issuetype_id' => $data['issuetype_id'],
        'title' => $data['title'],
        'description' => $data['description'],
        'priority' => $data['priority'],
        'name' => $data['name'] ?? null,
        'email' => $data['email'] ?? null,
        'phone' => $data['phone'] ?? null,
        'regnumber' => $data['regnumber'] ?? null,
        'attachments' => $data['attachments'] ?? [],
    ])->json();
}
```

**Success Response (201):**

```json
{
    "success": true,
    "message": "Issue created successfully.",
    "data": { ... }
}
```

**Validation Error (422):**

```json
{
    "success": false,
    "message": "Title is required"
}
```

---

#### Track Tickets by Email

```
POST /tickets/track
```

**Body Parameters:**

| Field   | Type   | Required | Notes            |
|---------|--------|----------|------------------|
| `email` | string | Yes      | Valid email      |

**Example:**

```php
public function trackByEmail(string $email): array
{
    return $this->publicClient()->post('/tickets/track', [
        'email' => $email,
    ])->json();
}
```

---

#### Get Ticket by Ticket Number

```
GET /tickets/number/{ticketNumber}
```

**Example:**

```php
public function getByTicketNumber(string $ticketNumber): array
{
    return $this->publicClient()->get("/tickets/number/{$ticketNumber}")->json();
}
```

**Not Found (404):**

```json
{
    "success": false,
    "message": "Ticket not found"
}
```

---

### Protected Endpoints (API Key Required)

These endpoints use the `client()` method which includes the `X-API-Key` header.

---

#### List Tickets by Email

```
GET /tickets/{email}
```

**Example:**

```php
public function listTickets(string $email): array
{
    return $this->client()->get("/tickets/{$email}")->json();
}
```

---

#### Get Ticket by ID

```
GET /tickets/{id}/show
```

**Example:**

```php
public function getTicket(int $id): array
{
    return $this->client()->get("/tickets/{$id}/show")->json();
}
```

---

#### Update Ticket Status

```
PATCH /tickets/{id}/status
```

**Body Parameters:**

| Field    | Type   | Required | Notes                                        |
|----------|--------|----------|----------------------------------------------|
| `status` | string | Yes      | `open`, `in_progress`, `resolved`, or `closed` |

**Example:**

```php
public function updateStatus(int $id, string $status): array
{
    return $this->client()->patch("/tickets/{$id}/status", [
        'status' => $status,
    ])->json();
}
```

**Success (200):**

```json
{
    "success": true,
    "message": "Issue status updated successfully."
}
```

---

#### Get Statistics

```
GET /statistics
```

**Example:**

```php
public function getStatistics(): array
{
    return $this->client()->get('/statistics')->json();
}
```

**Response (200):**

```json
{
    "success": true,
    "data": {
        "total": 150,
        "by_status": {
            "open": 45,
            "in_progress": 30,
            "resolved": 50,
            "closed": 25
        },
        "by_priority": {
            "low": 40,
            "medium": 70,
            "high": 40
        }
    }
}
```

---

#### Get Comments for a Ticket

```
GET /tickets/{issueId}/comments
```

**Example:**

```php
public function getComments(int $issueId): array
{
    return $this->client()->get("/tickets/{$issueId}/comments")->json();
}
```

**Response (200):**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "issue_id": 5,
            "user_email": "user@example.com",
            "comment": "Looking into this now.",
            "is_internal": false,
            "created_at": "2026-03-12T10:30:00+00:00",
            "updated_at": "2026-03-12T10:30:00+00:00",
            "created_at_human": "2 hours ago"
        }
    ]
}
```

---

#### Add a Comment

```
POST /comments
```

**Body Parameters:**

| Field         | Type    | Required | Notes                          |
|---------------|---------|----------|--------------------------------|
| `issue_id`    | integer | Yes      | Must exist in `issuelogs`      |
| `user_email`  | string  | Yes      | Valid email                    |
| `comment`     | string  | Yes      | Min 1 character                |
| `is_internal` | boolean | No       | Default `false`                |

**Example:**

```php
public function addComment(int $issueId, string $email, string $comment, bool $internal = false): array
{
    return $this->client()->post('/comments', [
        'issue_id' => $issueId,
        'user_email' => $email,
        'comment' => $comment,
        'is_internal' => $internal,
    ])->json();
}
```

**Success (201):**

```json
{
    "success": true,
    "message": "Comment added successfully."
}
```

---

#### Update a Comment

```
PUT /comments/{commentId}
```

**Body Parameters:**

| Field         | Type    | Required | Notes           |
|---------------|---------|----------|-----------------|
| `comment`     | string  | Yes      | Min 1 character |
| `user_email`  | string  | Yes      | Valid email     |
| `is_internal` | boolean | No       |                 |

**Example:**

```php
public function updateComment(int $commentId, string $email, string $comment, bool $internal = false): array
{
    return $this->client()->put("/comments/{$commentId}", [
        'user_email' => $email,
        'comment' => $comment,
        'is_internal' => $internal,
    ])->json();
}
```

**Success (200):**

```json
{
    "success": true,
    "message": "Comment updated successfully.",
    "data": {
        "id": 1,
        "comment": "Updated comment text",
        "is_internal": false,
        "updated_at": "2026-03-12T11:00:00+00:00"
    }
}
```

---

#### Delete a Comment

```
DELETE /comments/{commentId}
```

**Example:**

```php
public function deleteComment(int $commentId): array
{
    return $this->client()->delete("/comments/{$commentId}")->json();
}
```

**Success (200):**

```json
{
    "success": true,
    "message": "Comment deleted successfully."
}
```

---

## Complete Service Class

Here is the full `PrazAdminHelpdeskService` ready to copy into your application:

```php
<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PrazAdminHelpdeskService
{
    protected function client(): PendingRequest
    {
        return Http::baseUrl(config('prazadmin.api_url'))
            ->withHeaders(['X-API-Key' => config('prazadmin.api_key')])
            ->acceptJson()
            ->timeout(30)
            ->retry(2, 500);
    }

    protected function publicClient(): PendingRequest
    {
        return Http::baseUrl(config('prazadmin.api_url'))
            ->acceptJson()
            ->timeout(30)
            ->retry(2, 500);
    }

    // ──────────────────────────────────────────
    // Public Endpoints (no API key needed)
    // ──────────────────────────────────────────

    public function getSettings(): array
    {
        return $this->publicClient()->get('/settings')->json();
    }

    public function createTicket(array $data): array
    {
        return $this->publicClient()->post('/tickets', $data)->json();
    }

    public function trackByEmail(string $email): array
    {
        return $this->publicClient()->post('/tickets/track', [
            'email' => $email,
        ])->json();
    }

    public function getByTicketNumber(string $ticketNumber): array
    {
        return $this->publicClient()->get("/tickets/number/{$ticketNumber}")->json();
    }

    // ──────────────────────────────────────────
    // Protected Endpoints (require API key)
    // ──────────────────────────────────────────

    public function listTickets(string $email): array
    {
        return $this->client()->get("/tickets/{$email}")->json();
    }

    public function getTicket(int $id): array
    {
        return $this->client()->get("/tickets/{$id}/show")->json();
    }

    public function updateStatus(int $id, string $status): array
    {
        return $this->client()->patch("/tickets/{$id}/status", [
            'status' => $status,
        ])->json();
    }

    public function getStatistics(): array
    {
        return $this->client()->get('/statistics')->json();
    }

    public function getComments(int $issueId): array
    {
        return $this->client()->get("/tickets/{$issueId}/comments")->json();
    }

    public function addComment(int $issueId, string $email, string $comment, bool $internal = false): array
    {
        return $this->client()->post('/comments', [
            'issue_id' => $issueId,
            'user_email' => $email,
            'comment' => $comment,
            'is_internal' => $internal,
        ])->json();
    }

    public function updateComment(int $commentId, string $email, string $comment, bool $internal = false): array
    {
        return $this->client()->put("/comments/{$commentId}", [
            'user_email' => $email,
            'comment' => $comment,
            'is_internal' => $internal,
        ])->json();
    }

    public function deleteComment(int $commentId): array
    {
        return $this->client()->delete("/comments/{$commentId}")->json();
    }
}
```

---

## Quick Start Checklist

1. Add `PRAZADMIN_API_URL` and `PRAZADMIN_API_KEY` to your `.env`
2. Create `config/prazadmin.php` with the config values
3. Copy `PrazAdminHelpdeskService.php` into `app/Services/`
4. Inject or resolve the service where needed:
   ```php
   $helpdesk = app(PrazAdminHelpdeskService::class);
   $tickets = $helpdesk->listTickets('user@example.com');
   ```

---

## Error Codes Summary

| Code | Meaning                              |
|------|--------------------------------------|
| 200  | Success                              |
| 201  | Created successfully                 |
| 401  | Missing or invalid API key           |
| 404  | Resource not found                   |
| 422  | Validation error                     |
| 429  | Rate limited (too many requests)     |
| 500  | Server error                         |

---

## For PrazAdmin Administrators

Generate a new API key:

```bash
php artisan tinker --execute="echo bin2hex(random_bytes(32));"
```

Add to PrazAdmin's `.env`:

```env
API_KEY_HELPDESK_PORTAL=generated-key-here
```

Run `php artisan config:clear` after updating.
