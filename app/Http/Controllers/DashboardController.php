<?php

namespace App\Http\Controllers;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\SearchHistory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        $totalCompaniesFound = SearchHistory::where('user_id', $userId)->sum('results_count');
        $totalLeads = Lead::forUser($userId)->count();

        $closedClients = Lead::forUser($userId)->where('status', LeadStatus::CLIENTE_FECHADO)->count();
        $conversionRate = $totalLeads > 0 ? round(($closedClients / $totalLeads) * 100, 1) : 0;

        // Montar mapa de status para o funil
        $leadsByStatus = [];
        foreach (LeadStatus::cases() as $status) {
            $leadsByStatus[$status->value] = Lead::forUser($userId)->where('status', $status)->count();
        }

        // Distribuição por categoria (top 5)
        $topCategories = Lead::forUser($userId)
            ->whereNotNull('category')
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Distribuição por cidade (top 5)
        $topCities = Lead::forUser($userId)
            ->whereNotNull('city')
            ->selectRaw('city, count(*) as total')
            ->groupBy('city')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $recentLeads = Lead::forUser($userId)->latest()->take(6)->get();

        return view('dashboard', compact(
            'totalCompaniesFound',
            'totalLeads',
            'closedClients',
            'conversionRate',
            'leadsByStatus',
            'topCategories',
            'topCities',
            'recentLeads'
        ));
    }
}
