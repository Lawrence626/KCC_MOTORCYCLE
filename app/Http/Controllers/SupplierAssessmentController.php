<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class SupplierAssessmentController extends Controller
{
    public function index()
    {
        $productSupplierNames = Product::whereNotNull('supplier_name')
            ->pluck('supplier_name')
            ->filter()
            ->unique()
            ->values();

        $actualSupplierNames = Supplier::pluck('name');
        $allSupplierNames = $productSupplierNames->merge($actualSupplierNames)->unique()->values();

        $products = Product::whereNotNull('supplier_name')
            ->orderBy('supplier_name')
            ->orderByDesc('stock_quantity')
            ->get()
            ->groupBy('supplier_name');

        $suppliersByName = Supplier::whereIn('name', $allSupplierNames)->get()->keyBy('name');

        $supplierSummaries = $allSupplierNames->map(function ($supplierName) use ($products, $suppliersByName) {
            $supplier = $suppliersByName->get($supplierName);
            $group = $products->get($supplierName, collect());
            $totalValue = $group->sum(fn (Product $product) => $product->stock_quantity * $product->unit_price);
            $avgPrice = $group->count() ? $group->avg('unit_price') : 0;
            $minPrice = $group->count() ? $group->min('unit_price') : 0;
            $maxPrice = $group->count() ? $group->max('unit_price') : 0;
            $priceRange = $maxPrice - $minPrice;
            $priceStdDeviation = $group->count() ? sqrt($group->avg(fn (Product $product) => pow($product->unit_price - $avgPrice, 2))) : 0;
            $lastRestock = $group->max('last_restock_date');

            return (object) [
                'id' => $supplier?->id,
                'name' => $supplierName,
                'contact_person' => $supplier?->contact_person,
                'email' => $supplier?->email,
                'phone' => $supplier?->phone,
                'status' => $supplier?->status ?? 'active',
                'notes' => $supplier?->notes,
                'product_count' => $group->count(),
                'total_value' => $totalValue,
                'avg_price' => $avgPrice,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'price_range' => $priceRange,
                'price_std_deviation' => $priceStdDeviation,
                'last_restock_date' => $lastRestock ? Carbon::parse($lastRestock)->toDateString() : null,
                'products' => $group->map(fn (Product $product) => [
                    'id' => $product->id,
                    'name' => $product->product_name ?: $product->name,
                    'sku' => $product->sku,
                    'price' => $product->unit_price,
                    'stock_quantity' => $product->stock_quantity,
                    'category' => $product->category,
                    'last_restock_date' => optional($product->last_restock_date)->toDateString(),
                ])->values(),
                'has_record' => $supplier !== null,
            ];
        });

        $totalSuppliers = $allSupplierNames->count();
        $totalProducts = Product::whereNotNull('supplier_name')->count();
        $totalStockValue = Product::whereNotNull('supplier_name')
            ->get()
            ->sum(fn (Product $product) => $product->stock_quantity * $product->unit_price);
        $activeSuppliers = $supplierSummaries->filter(fn ($summary) => $summary->status === 'active')->count();

        return view('supplier_assessment.supplier_asses', [
            'supplierSummaries' => $supplierSummaries,
            'quickStats' => [
                'totalSuppliers' => $totalSuppliers,
                'activeSuppliers' => $activeSuppliers,
                'trackedProducts' => $totalProducts,
                'stockValue' => $totalStockValue,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('suppliers', 'name')],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'notes' => ['nullable', 'string'],
        ]);

        Supplier::create($data);

        return redirect()->route('supplier.assessment')->with('success', 'Supplier added successfully.');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('suppliers', 'name')->ignore($supplier->id)],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'notes' => ['nullable', 'string'],
        ]);

        $oldName = $supplier->name;
        $supplier->update($data);

        if ($oldName !== $supplier->name) {
            Product::where('supplier_name', $oldName)->update(['supplier_name' => $supplier->name]);
        }

        return redirect()->route('supplier.assessment')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        Product::where('supplier_name', $supplier->name)->update(['supplier_name' => null]);
        $supplier->delete();

        return redirect()->route('supplier.assessment')->with('success', 'Supplier deleted and linked products cleared.');
    }
}
