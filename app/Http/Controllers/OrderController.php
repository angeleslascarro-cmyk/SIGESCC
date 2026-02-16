<?php

namespace App\Http\Controllers;

use App\Models\{Order, OrderItem, Neighbor, Product};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['neighbor','user'])
            ->orderByDesc('id')
            ->get();

        return Inertia::render('Orders/Index', ['orders' => $orders]);
    }

    public function create()
    {
        $neighbors = Neighbor::query()
            ->select(['id','full_name','cedula','credit_limit_bs','credit_limit_usd'])
            ->withSum([
                'orders as pending_total_bs' => function ($q) {
                    $q->where('status', 'PENDING')->where('currency', 'BS');
                }
            ], 'total')
            ->withSum([
                'orders as pending_total_usd' => function ($q) {
                    $q->where('status', 'PENDING')->where('currency', 'USD');
                }
            ], 'total')
            ->orderBy('full_name')
            ->get()
            ->map(function ($n) {
                $pendingBs  = (float) ($n->pending_total_bs ?? 0);
                $pendingUsd = (float) ($n->pending_total_usd ?? 0);

                $limitBs  = (float) ($n->credit_limit_bs ?? 0);
                $limitUsd = (float) ($n->credit_limit_usd ?? 0);

                $n->available_bs  = max(0, $limitBs - $pendingBs);
                $n->available_usd = max(0, $limitUsd - $pendingUsd);

                return $n;
            });

        return Inertia::render('Orders/Create', [
            'neighbors' => $neighbors,
            'products' => Product::where('active', true)
                ->orderBy('name')
                ->get(['id','name','price_bs','price_usd','stock']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'neighbor_id' => ['required','exists:neighbors,id'],
            'currency' => ['required','in:BS,USD'],
            'status' => ['required','in:PAID,PENDING'],
            'items' => ['required','array','min:1'],
            'items.*.product_id' => ['required','exists:products,id'],
            'items.*.qty' => ['required','integer','min:1'],
        ]);

        return DB::transaction(function () use ($data, $request) {
            $neighbor = Neighbor::select(['id','credit_limit_bs','credit_limit_usd'])
                ->findOrFail($data['neighbor_id']);

            $productIds = collect($data['items'])->pluck('product_id')->unique()->values();
            $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            $total = 0;
            $itemsToCreate = [];

            foreach ($data['items'] as $item) {
                $p = $products[$item['product_id']] ?? null;

                if (!$p) {
                    throw ValidationException::withMessages([
                        'items' => 'Producto no encontrado.',
                    ]);
                }

                if ((int)$item['qty'] > (int)$p->stock) {
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente para {$p->name} (disponible: {$p->stock}).",
                    ]);
                }

                $unit = $data['currency'] === 'BS' ? (float)$p->price_bs : (float)$p->price_usd;
                $subtotal = $unit * (int)$item['qty'];
                $total += $subtotal;

                $itemsToCreate[] = [
                    'product_id' => $p->id,
                    'qty' => (int)$item['qty'],
                    'unit_price' => $unit,
                    'subtotal' => $subtotal,
                ];
            }

         
            if ($data['status'] === 'PENDING') {
                $currency = $data['currency'];

                $currentDebt = (float) Order::where('neighbor_id', $neighbor->id)
                    ->where('status', 'PENDING')
                    ->where('currency', $currency)
                    ->sum('total');

                $limit = $currency === 'BS'
                    ? (float) ($neighbor->credit_limit_bs ?? 0)
                    : (float) ($neighbor->credit_limit_usd ?? 0);

                if (($currentDebt + $total) > $limit) {
                    throw ValidationException::withMessages([
                        'neighbor_id' => "Límite de crédito excedido ({$currency}). Deuda actual: {$currentDebt}. Límite: {$limit}.",
                    ]);
                }
            }

            $order = Order::create([
                'neighbor_id' => $neighbor->id,
                'user_id' => $request->user()->id,
                'currency' => $data['currency'],
                'status' => $data['status'],
                'total' => $total,
            ]);

            foreach ($itemsToCreate as $it) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $it['product_id'],
                    'qty' => $it['qty'],
                    'unit_price' => $it['unit_price'],
                    'subtotal' => $it['subtotal'],
                ]);

                $products[$it['product_id']]->decrement('stock', $it['qty']);
            }

            return redirect()->route('orders.index')->with('success','Venta registrada.');
        });
    }
}
