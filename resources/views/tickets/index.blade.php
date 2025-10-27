@extends('layouts.app')

@section('title', 'My Tickets')

@section('content')
<div>
    <div class="card">
        <div class="card-body">
            <div class="breadcrumbs text-sm">
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a>My Tickets</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; font-weight: 700;">My Tickets</h1>
        <a href="/tickets/create" class="btn btn-primary">+ Create New Ticket</a>
    </div>

    @if(session('success'))
        <div style="padding: 1rem; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 0.25rem; color: #155724; margin-bottom: 1rem;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="padding: 1rem; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 0.25rem; color: #721c24; margin-bottom: 1rem;">
            {{ session('error') }}
        </div>
    @endif
    
    <!-- Filters -->
    <div class="card">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div>
                <label class="form-label">Status</label>
                <select id="filterStatus" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="open">Open</option>
                    <option value="in_progress">In Progress</option>
                    <option value="waiting_customer">Waiting on Customer</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                </select>
            </div>
            
            <div>
                <label class="form-label">Priority</label>
                <select id="filterPriority" class="form-select">
                    <option value="">All Priorities</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
            
            <div>
                <label class="form-label">Search</label>
                <input type="text" id="searchTickets" class="form-input" placeholder="Search tickets...">
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <!-- Tickets List -->
    <div class="overflow-x-auto">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tickets->data as $ticket)
                    <tr>
                        <td>{{ $ticket->ticket_number }}</td>
                        <td>{{ $ticket->title }}</td>
                        <td>{{ $ticket->status }}</td>
                        <td>{{ $ticket->priority }}</td>
                        <td>{{ Carbon\Carbon::parse($ticket->created_at)->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="/tickets/{{ $ticket->id }}/show" class="btn btn-sm btn-primary">View ticket</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="alert alert-error">No tickets found</div>
                            <a href="/tickets/create" class="btn btn-primary">+ Create New Ticket</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </div>
    </div>
</div>


@endsection

