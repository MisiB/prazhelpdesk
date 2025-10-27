@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div>
    <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 2rem;">Support Dashboard</h1>
    
    <!-- Statistics Overview -->
    <div id="statsOverview" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="spinner"></div>
    </div>
    
    <!-- Charts Row -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card">
            <div class="card-header">Ticket Trends (Last 30 Days)</div>
            <canvas id="ticketTrendsChart" style="max-height: 300px;"></canvas>
        </div>
        
        <div class="card">
            <div class="card-header">Tickets by Priority</div>
            <canvas id="priorityChart" style="max-height: 300px;"></canvas>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem;">
        <div class="card">
            <div class="card-header">Recent Tickets</div>
            <div id="recentTickets">
                <div class="spinner"></div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">Top Articles</div>
            <div id="topArticles">
                <div class="spinner"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Load dashboard overview
    async function loadOverview() {
        try {
            const response = await fetch('/api/dashboard/overview', {
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('token')
                }
            });
            const data = await response.json();
            
            const container = document.getElementById('statsOverview');
            container.innerHTML = `
                <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <div style="font-size: 2rem; font-weight: 700;">${data.tickets.total}</div>
                    <div style="opacity: 0.9;">Total Tickets</div>
                </div>
                
                <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                    <div style="font-size: 2rem; font-weight: 700;">${data.tickets.open}</div>
                    <div style="opacity: 0.9;">Open Tickets</div>
                </div>
                
                <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                    <div style="font-size: 2rem; font-weight: 700;">${data.knowledge_base.total_articles}</div>
                    <div style="opacity: 0.9;">KB Articles</div>
                </div>
                
                <div class="card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
                    <div style="font-size: 2rem; font-weight: 700;">${Math.round(data.performance.avg_response_time)}h</div>
                    <div style="opacity: 0.9;">Avg Response Time</div>
                </div>
            `;
        } catch (error) {
            console.error('Error loading overview:', error);
        }
    }
    
    // Load recent tickets
    async function loadRecentTickets() {
        try {
            const response = await fetch('/api/dashboard/recent-tickets?limit=5', {
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('token')
                }
            });
            const tickets = await response.json();
            
            const container = document.getElementById('recentTickets');
            
            if (tickets && tickets.length > 0) {
                container.innerHTML = tickets.map(ticket => `
                    <div style="padding: 1rem; border-bottom: 1px solid #e2e8f0; cursor: pointer;" 
                         onclick="window.location.href='/tickets/${ticket.id}'">
                        <div style="font-weight: 600; margin-bottom: 0.25rem;">${ticket.subject}</div>
                        <div style="font-size: 0.875rem; color: #64748b;">${ticket.ticket_number}</div>
                    </div>
                `).join('');
            } else {
                container.innerHTML = '<p style="color: #64748b; padding: 1rem;">No recent tickets</p>';
            }
        } catch (error) {
            console.error('Error loading recent tickets:', error);
        }
    }
    
    // Load top articles
    async function loadTopArticles() {
        try {
            const response = await fetch('/api/dashboard/top-articles?limit=5');
            const articles = await response.json();
            
            const container = document.getElementById('topArticles');
            
            if (articles && articles.length > 0) {
                container.innerHTML = articles.map(article => `
                    <div style="padding: 1rem; border-bottom: 1px solid #e2e8f0; cursor: pointer;" 
                         onclick="window.location.href='/knowledge-base/${article.slug}'">
                        <div style="font-weight: 600; margin-bottom: 0.25rem;">${article.title}</div>
                        <div style="font-size: 0.875rem; color: #64748b;">👁️ ${article.views} views</div>
                    </div>
                `).join('');
            } else {
                container.innerHTML = '<p style="color: #64748b; padding: 1rem;">No articles yet</p>';
            }
        } catch (error) {
            console.error('Error loading top articles:', error);
        }
    }
    
    // Load on page load
    loadOverview();
    loadRecentTickets();
    loadTopArticles();
</script>
@endpush
@endsection







