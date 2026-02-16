<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Neighbor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) $request->get('year', now()->year);

        $totals = Order::selectRaw('currency, SUM(total) as total')
            ->whereYear('created_at', $year)
            ->groupBy('currency')
            ->pluck('total', 'currency');

        $pending = Order::selectRaw('currency, SUM(total) as total')
            ->where('status', 'PENDING')
            ->whereYear('created_at', $year)
            ->groupBy('currency')
            ->pluck('total', 'currency');

               $topDebtors = Neighbor::query()
            ->select(['id', 'full_name', 'cedula'])
            ->withSum(['orders as pending_bs' => function ($q) {
                $q->where('status', 'PENDING')->where('currency', 'BS');
            }], 'total')
            ->withSum(['orders as pending_usd' => function ($q) {
                $q->where('status', 'PENDING')->where('currency', 'USD');
            }], 'total')
            ->orderByRaw('(COALESCE(pending_bs,0) + COALESCE(pending_usd,0)) DESC')
            ->limit(5)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'full_name' => $n->full_name,
                    'cedula' => $n->cedula,
                    'pending_bs' => (float)($n->pending_bs ?? 0),
                    'pending_usd' => (float)($n->pending_usd ?? 0),
                ];
            });

        return Inertia::render('Dashboard', [
            'year' => $year,
            'kpis' => [
                'sales_bs' => (float)($totals['BS'] ?? 0),
                'sales_usd' => (float)($totals['USD'] ?? 0),
                'pending_bs' => (float)($pending['BS'] ?? 0),
                'pending_usd' => (float)($pending['USD'] ?? 0),
            ],
            'topDebtors' => $topDebtors,
        ]);
    }
}
