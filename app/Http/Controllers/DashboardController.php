<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\KnowledgeBase;
use App\Models\Category;
use App\Services\CrmApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected CrmApiService $crmService;

    public function __construct(CrmApiService $crmService)
    {
        $this->crmService = $crmService;
    }

    /**
     * Get dashboard overview statistics
     */
    public function overview()
    {
        $stats = [
            'tickets' => [
                'total' => Ticket::count(),
                'open' => Ticket::where('status', 'open')->count(),
                'in_progress' => Ticket::where('status', 'in_progress')->count(),
                'resolved_today' => Ticket::where('status', 'resolved')
                    ->whereDate('resolved_at', today())
                    ->count(),
                'urgent' => Ticket::where('priority', 'urgent')
                    ->whereIn('status', ['open', 'in_progress'])
                    ->count(),
            ],
            'knowledge_base' => [
                'total_articles' => KnowledgeBase::published()->count(),
                'total_views' => KnowledgeBase::sum('views'),
                'avg_helpfulness' => KnowledgeBase::published()
                    ->selectRaw('AVG((helpful_count / NULLIF(helpful_count + not_helpful_count, 0)) * 100) as avg')
                    ->value('avg') ?? 0,
            ],
            'performance' => [
                'avg_response_time' => $this->getAverageResponseTime(),
                'avg_resolution_time' => $this->getAverageResolutionTime(),
                'customer_satisfaction' => $this->getCustomerSatisfaction(),
            ],
        ];

        return response()->json($stats);
    }

    /**
     * Get ticket trends
     */
    public function ticketTrends(Request $request)
    {
        $days = $request->get('days', 30);

        $trends = Ticket::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($trends);
    }

    /**
     * Get ticket distribution by category
     */
    public function ticketsByCategory()
    {
        $distribution = Ticket::select('category_id', DB::raw('COUNT(*) as count'))
            ->with('category:id,name')
            ->groupBy('category_id')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category?->name ?? 'Uncategorized',
                    'count' => $item->count,
                ];
            });

        return response()->json($distribution);
    }

    /**
     * Get ticket distribution by priority
     */
    public function ticketsByPriority()
    {
        $distribution = Ticket::select('priority', DB::raw('COUNT(*) as count'))
            ->groupBy('priority')
            ->get();

        return response()->json($distribution);
    }

    /**
     * Get recent tickets
     */
    public function recentTickets(Request $request)
    {
        $limit = $request->get('limit', 10);

        $tickets = Ticket::with(['user', 'category', 'assignedAgent'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($tickets);
    }

    /**
     * Get top performing knowledge base articles
     */
    public function topArticles(Request $request)
    {
        $limit = $request->get('limit', 10);

        $articles = KnowledgeBase::published()
            ->with(['category'])
            ->orderBy('views', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($articles);
    }

    /**
     * Get agent performance metrics
     */
    public function agentPerformance()
    {
        $agents = Ticket::select('assigned_to', DB::raw('COUNT(*) as total_tickets'))
            ->whereNotNull('assigned_to')
            ->with('assignedAgent:id,name,email')
            ->groupBy('assigned_to')
            ->get()
            ->map(function ($item) {
                $resolved = Ticket::where('assigned_to', $item->assigned_to)
                    ->whereIn('status', ['resolved', 'closed'])
                    ->count();

                $avgResolutionTime = Ticket::where('assigned_to', $item->assigned_to)
                    ->whereNotNull('resolved_at')
                    ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_time')
                    ->value('avg_time');

                return [
                    'agent' => $item->assignedAgent,
                    'total_tickets' => $item->total_tickets,
                    'resolved_tickets' => $resolved,
                    'resolution_rate' => $item->total_tickets > 0 
                        ? round(($resolved / $item->total_tickets) * 100, 2) 
                        : 0,
                    'avg_resolution_time' => round($avgResolutionTime ?? 0, 2),
                ];
            });

        return response()->json($agents);
    }

    /**
     * Get CRM data integration status
     */
    public function crmStatus()
    {
        $synced = Ticket::whereNotNull('crm_ticket_id')->count();
        $total = Ticket::count();

        $status = [
            'total_tickets' => $total,
            'synced_tickets' => $synced,
            'sync_rate' => $total > 0 ? round(($synced / $total) * 100, 2) : 0,
            'last_sync' => Ticket::whereNotNull('crm_ticket_id')
                ->orderBy('updated_at', 'desc')
                ->value('updated_at'),
        ];

        return response()->json($status);
    }

    /**
     * Helper: Get average response time in hours
     */
    protected function getAverageResponseTime(): float
    {
        return Ticket::whereNotNull('first_response_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, first_response_at)) as avg_time')
            ->value('avg_time') ?? 0;
    }

    /**
     * Helper: Get average resolution time in hours
     */
    protected function getAverageResolutionTime(): float
    {
        return Ticket::whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_time')
            ->value('avg_time') ?? 0;
    }

    /**
     * Helper: Get customer satisfaction score
     */
    protected function getCustomerSatisfaction(): float
    {
        // Calculate based on knowledge base helpfulness
        $total = KnowledgeBase::sum(DB::raw('helpful_count + not_helpful_count'));
        $helpful = KnowledgeBase::sum('helpful_count');

        if ($total === 0) {
            return 0;
        }

        return round(($helpful / $total) * 100, 2);
    }
}


