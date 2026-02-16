<?php

namespace App\Http\Controllers;

use App\Models\Neighbor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class NeighborController extends Controller
{
    public function index()
    {
        $neighbors = Neighbor::query()
            ->select(['id','full_name','cedula','phone','credit_limit_bs','credit_limit_usd'])
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
            ->orderByDesc('id')
            ->get()
            ->map(function ($n) {
                $pendingBs  = (float) ($n->pending_total_bs ?? 0);
                $pendingUsd = (float) ($n->pending_total_usd ?? 0);

                $limitBs  = (float) ($n->credit_limit_bs ?? 0);
                $limitUsd = (float) ($n->credit_limit_usd ?? 0);

                // Disponible = Límite - Pendiente
                $n->available_bs  = max(0, $limitBs - $pendingBs);
                $n->available_usd = max(0, $limitUsd - $pendingUsd);

                return $n;
            });

        return Inertia::render('Neighbors/Index', [
            'neighbors' => $neighbors,
        ]);
    }

    public function create()
    {
        return Inertia::render('Neighbors/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required','string','max:255'],
            'cedula' => ['required','string','max:50', 'unique:neighbors,cedula'],
            'phone' => ['nullable','string','max:50'],
            'address' => ['nullable','string','max:255'],
            'credit_limit_bs' => ['required','numeric','min:0'],
            'credit_limit_usd' => ['required','numeric','min:0'],
        ]);

        Neighbor::create($data);

        return redirect()
            ->route('neighbors.index')
            ->with('success','Vecino creado.');
    }

    public function edit(Neighbor $neighbor)
    {
        return Inertia::render('Neighbors/Edit', [
            'neighbor' => $neighbor
        ]);
    }

    public function update(Request $request, Neighbor $neighbor)
    {
        $data = $request->validate([
            'full_name' => ['required','string','max:255'],
            'cedula' => ['required','string','max:50', Rule::unique('neighbors','cedula')->ignore($neighbor->id)],
            'phone' => ['nullable','string','max:50'],
            'address' => ['nullable','string','max:255'],
            'credit_limit_bs' => ['required','numeric','min:0'],
            'credit_limit_usd' => ['required','numeric','min:0'],
        ]);

        $neighbor->update($data);

        return redirect()
            ->route('neighbors.index')
            ->with('success','Vecino actualizado.');
    }

    public function destroy(Neighbor $neighbor)
    {
        $neighbor->delete();

        return redirect()
            ->route('neighbors.index')
            ->with('success','Vecino eliminado.');
    }
}
