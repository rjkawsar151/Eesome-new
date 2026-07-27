<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'like', '%'.$request->string('q').'%')->orWhere('sku', 'like', '%'.$request->string('q').'%')))
            ->orderBy('stock')->paginate(30)->withQueryString();
        $movements = InventoryMovement::with('product')->latest()->limit(30)->get();

        return view('admin.inventory.index', compact('products', 'movements'));
    }

    public function adjust(Request $request, Product $product)
    {
        $data = $request->validate(['quantity_delta' => 'required|integer|between:-100000,100000|not_in:0', 'reference' => 'required|string|max:100']);
        DB::transaction(function () use ($data, $product) {
            $locked = Product::lockForUpdate()->findOrFail($product->id);
            $before = $locked->stock;
            $after = $before + (int) $data['quantity_delta'];
            if ($after < 0) {
                throw new RuntimeException('Stock cannot be reduced below zero.');
            }
            $locked->update(['stock' => $after, 'is_sold_out' => $after === 0]);
            InventoryMovement::create([
                'product_id' => $locked->id, 'type' => 'adjustment',
                'quantity_delta' => $data['quantity_delta'], 'stock_before' => $before,
                'stock_after' => $after, 'reference' => $data['reference'],
                'created_by_user_id' => auth()->id(),
            ]);
        });

        return back()->with('success', 'Inventory adjusted.');
    }
}
