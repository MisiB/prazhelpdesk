@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div style="text-align: center; padding: 3rem 0;">
    <h1 style="font-size: 3rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem;">
        Welcome to AI Support Portal
    </h1>
    <p style="font-size: 1.25rem; color: #64748b; margin-bottom: 2rem; max-width: 600px; margin-left: auto; margin-right: auto;">
        Search the knowledge base if you can't find your answer create a support ticket
    </p>
    

    
    <!-- Quick Actions -->
    <div class="grid lg:grid-cols-2 gap-4 p-4">
        <div class="card" style="text-align: left; transition: transform 0.2s; cursor: pointer;" onclick="window.location.href='/knowledge-base'">
            <div style="font-size: 2rem; margin-bottom: 1rem;">📚</div>
            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">Browse Knowledge Base</h3>
            <p style="color: #64748b;">Find answers in our comprehensive knowledge base</p>
        </div>
        <div class="card" style="text-align: left; transition: transform 0.2s; cursor: pointer;" onclick="window.location.href='/tickets'">
            <div style="font-size: 2rem; margin-bottom: 1rem;">📋</div>
            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">Workshops ,Trainings and Conferences</h3>
            <p style="color: #64748b;">View and manage your workshops and conferences</p>
        </div>
        
        <div class="card" style="text-align: left; transition: transform 0.2s; cursor: pointer;" onclick="window.location.href='/tickets/create'">
            <div style="font-size: 2rem; margin-bottom: 1rem;">🎫</div>
            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">Create Ticket</h3>
            <p style="color: #64748b;">Can't find an answer? Submit a support ticket</p>
        </div>
        
        <div class="card" style="text-align: left; transition: transform 0.2s; cursor: pointer;" onclick="window.location.href='/tickets'">
            <div style="font-size: 2rem; margin-bottom: 1rem;">📋</div>
            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">My Tickets</h3>
            <p style="color: #64748b;">View and manage your support tickets</p>
        </div>
       
    </div>
    
 
</div>

@endsection


