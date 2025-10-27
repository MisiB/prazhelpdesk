<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Services\AiService;
use App\Services\CrmApiService;
use Illuminate\Http\Request;
use App\interfaces\ihttpService;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    protected ihttpService $httpService;

    public function __construct(ihttpService $httpService)
    {
        $this->httpService = $httpService;
    }

    public function index()
    {
        $email = auth()->user()->email;
        $tickets = $this->httpService->gettickets($email);
     
        return view('tickets.index', compact('tickets'));
    }
    public function create()
    {
       $settings = $this->httpService->getsettings();
      
        return view('tickets.create', compact('settings'));
    }

    public function show($id)
    {
        $ticket = $this->httpService->getticket($id);
        
       
        return view('tickets.show', compact('ticket','id'));
    }

    public function createTicket(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'issuegroup_id' => 'required|integer',
            'issuetype_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'regnumber' => 'required|string|max:255',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max per file
        ]);

        try {
            // Handle file attachments if present
            if ($request->hasFile('attachments')) {
                $attachments = [];
                foreach ($request->file('attachments') as $file) {
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'data' => base64_encode(file_get_contents($file->getRealPath())),
                        'mime_type' => $file->getMimeType(),
                    ];
                }
                $validated['attachments'] = $attachments;
            }
         
            // Create ticket via API
            $ticket = $this->httpService->createTicket($validated);
     

            if ($ticket->success) {
                return redirect()->route('tickets.index')
                    ->with('success', $ticket->message);
            } else {
                return back()
                    ->withInput()
                    ->with('error', $ticket->message);
            }
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    
    public function updateTicket($id, Request $request)
    {
        $ticket = $this->httpService->updateTicket($id, $request->all());
        return response()->json($ticket);
    }
    
    public function deleteTicket($id)
    {
        $this->httpService->deleteTicket($id);
        return response()->json(['message' => 'Ticket deleted successfully']);
    }

    public function addComment($id, Request $request)
    {
      $request->validate([
        "comment" => 'required|string'
      ]);
      $comment = $this->httpService->addcomment(["issue_id" => $id,"user_email"=>auth()->user()->email, "comment" => $request->comment,'is_internal' => false]);
      if ($comment->success) {
        return redirect()->route('tickets.show', $id)
            ->with('success', $comment->message);
      } else {
        return redirect()->route('tickets.show', $request->issue_id)
            ->with('error', $comment->message);
      }
    }

    public function updateComment(Request $request, $id, $commentId)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'comment' => 'required|string',
        ]);

        try {
            // Update comment via API
            $result = $this->httpService->updatecomment($commentId,['comment' => $validated['comment'],'user_email'=>auth()->user()->email,'is_internal' => false]);

            if (isset($result->success) && $result->success) {
                return redirect()->route('tickets.show', $id)
                    ->with('success', 'Comment updated successfully!');
            } else {
                return back()
                    ->with('error', $result->message ?? 'Failed to update comment. Please try again.');
            }
        } catch (\Exception $e) {
            return back()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function deleteComment($ticketId, $commentId)
    {
        try {
            $result = $this->httpService->deletecomment( $commentId);

            if (isset($result->success) && $result->success) {
                return redirect()->route('tickets.show', $ticketId)
                    ->with('success', 'Comment deleted successfully!');
            } else {
                return back()
                    ->with('error', $result->message ?? 'Failed to delete comment. Please try again.');
            }
        } catch (\Exception $e) {
            return back()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function closeTicket($id)
    {
        try {
            $result = $this->httpService->closeTicket($id);

            if (isset($result->success) && $result->success) {
                return redirect()->route('tickets.show', $id)
                    ->with('success', 'Ticket closed successfully!');
            } else {
                return back()
                    ->with('error', $result->message ?? 'Failed to close ticket. Please try again.');
            }
        } catch (\Exception $e) {
            return back()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}

