<?php

namespace App\Http\Controllers;

use App\Models\Neighbor;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $year = (int)($request->get('year', now()->year));

        $monthly = Order::selectRaw('MONTH(created_at) as month, currency, SUM(total) as total')
            ->whereYear('created_at', $year)
            ->groupBy('month','currency')
            ->orderBy('month')
            ->get();

       
        $debtors = Neighbor::query()
            ->select(['id','full_name','cedula','phone','credit_limit_bs','credit_limit_usd'])
            ->withSum(['orders as pending_bs' => function ($q) {
                $q->where('status','PENDING')->where('currency','BS');
            }], 'total')
            ->withSum(['orders as pending_usd' => function ($q) {
                $q->where('status','PENDING')->where('currency','USD');
            }], 'total')
            // Solo vecinos con deuda en alguna moneda
            ->havingRaw('(COALESCE(pending_bs,0) + COALESCE(pending_usd,0)) > 0')
            // Orden por deuda total (solo para ranking)
            ->orderByDesc(DB::raw('COALESCE(pending_bs,0) + COALESCE(pending_usd,0)'))
            ->get();

        return Inertia::render('Reports/Index', [
            'year' => $year,
            'monthly' => $monthly,
            'debtors' => $debtors,
        ]);
    }

    public function debtorsCsv()
    {
        $rows = Neighbor::query()
            ->select(['full_name','cedula','phone','credit_limit_bs','credit_limit_usd'])
            ->withSum(['orders as pending_bs' => function ($q) {
                $q->where('status','PENDING')->where('currency','BS');
            }], 'total')
            ->withSum(['orders as pending_usd' => function ($q) {
                $q->where('status','PENDING')->where('currency','USD');
            }], 'total')
            ->havingRaw('(COALESCE(pending_bs,0) + COALESCE(pending_usd,0)) > 0')
            ->orderByDesc(DB::raw('COALESCE(pending_bs,0) + COALESCE(pending_usd,0)'))
            ->get();

        $filename = 'deudores_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');


            fputcsv($out, [
                'cedula','nombre','telefono',
                'pendiente_bs','pendiente_usd',
                'limite_bs','limite_usd'
            ]);

            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->cedula,
                    $r->full_name,
                    $r->phone,
                    number_format((float)($r->pending_bs ?? 0), 2, '.', ''),
                    number_format((float)($r->pending_usd ?? 0), 2, '.', ''),
                    number_format((float)($r->credit_limit_bs ?? 0), 2, '.', ''),
                    number_format((float)($r->credit_limit_usd ?? 0), 2, '.', ''),
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function monthlyCsv(Request $request): StreamedResponse
    {
        $year = (int) $request->get('year', now()->year);
        $month = $request->get('month'); // opcional (1-12)

        $q = Order::selectRaw('MONTH(created_at) as month, currency, SUM(total) as total')
            ->whereYear('created_at', $year);

        if ($month !== null && $month !== '') {
            $q->whereMonth('created_at', (int)$month);
        }

        $rows = $q->groupBy('month','currency')
            ->orderBy('month')
            ->get();

        $filename = 'ventas_mensuales_' . $year
            . ($month ? ('_' . str_pad((int)$month, 2, '0', STR_PAD_LEFT)) : '')
            . '_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($rows, $year, $month) {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['year','month','currency','total']);

            foreach ($rows as $r) {
                fputcsv($out, [
                    $year,
                    (int)$r->month,
                    $r->currency,
                    number_format((float)$r->total, 2, '.', ''),
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
