<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        return Inertia::render('Products/Index', [
            'products' => Product::orderByDesc('id')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Products/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'price_bs' => ['required','numeric','min:0'],
            'price_usd' => ['required','numeric','min:0'],
            'stock' => ['required','integer','min:0'],
            'active' => ['required','boolean'],
        ]);

        Product::create($data);
        return redirect()->route('products.index')->with('success','Producto creado.');
    }

    public function edit(Product $product)
    {
        return Inertia::render('Products/Edit', ['product' => $product]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'price_bs' => ['required','numeric','min:0'],
            'price_usd' => ['required','numeric','min:0'],
            'stock' => ['required','integer','min:0'],
            'active' => ['required','boolean'],
        ]);

        $product->update($data);
        return redirect()->route('products.index')->with('success','Producto actualizado.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success','Producto eliminado.');
    }
}
