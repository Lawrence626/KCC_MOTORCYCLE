<?php

namespace App\Http\Controllers;

use App\Models\ReverseLogistics;
use Illuminate\Http\Request;

class ReverseLogisticsController extends Controller
{
    public function index(Request $request)
    {
        $query = ReverseLogistics::query();

        // Apply filters
        if ($request->has('search') && !empty($request->input('search'))) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('return_reason', 'like', "%{$search}%")
                  ->orWhere('condition', 'like', "%{$search}%")
                  ->orWhere('warehouse', 'like', "%{$search}%")
                  ->orWhere('source', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && !empty($request->input('status'))) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('reason') && !empty($request->input('reason'))) {
            $query->where('return_reason', $request->input('reason'));
        }

        if ($request->has('condition') && !empty($request->input('condition'))) {
            $query->where('condition', $request->input('condition'));
        }

        $records = $query->latest()->get();

        return response()->json([
            'data' => $records,
            'stats' => [
                'total' => $records->count(),
                'under_review' => $records->where('status', 'Under Review')->count(),
                'ready_for_restock' => $records->where('status', 'Ready for Restock')->count(),
                'pending_repair' => $records->where('status', 'Pending Repair')->count(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'product_name' => 'required|string|max:255',
            'sku' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'warehouse' => 'required|string|max:255',
            'return_reason' => 'required|string|max:255',
            'condition' => 'required|string|max:255',
            'source' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'reported_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $record = ReverseLogistics::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Reverse logistics record created successfully',
            'data' => $record,
        ]);
    }

    public function show($id)
    {
        $record = ReverseLogistics::findOrFail($id);
        return response()->json($record);
    }

    public function update(Request $request, $id)
    {
        $record = ReverseLogistics::findOrFail($id);

        $validated = $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'product_name' => 'required|string|max:255',
            'sku' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'warehouse' => 'required|string|max:255',
            'return_reason' => 'required|string|max:255',
            'condition' => 'required|string|max:255',
            'source' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'reported_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $record->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Reverse logistics record updated successfully',
            'data' => $record,
        ]);
    }

    public function destroy($id)
    {
        $record = ReverseLogistics::findOrFail($id);
        $record->delete();

        return response()->json([
            'success' => true,
            'message' => 'Reverse logistics record deleted successfully',
        ]);
    }
}
