<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReplacementController extends Controller
{
    public function index(Request $request)
    {
        try {
            $replacements = DB::table('replacements')
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $replacements
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching replacements: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching replacements'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'receipt_no' => 'required|string',
                'returned_item' => 'required|string',
                'reason' => 'required|string',
                'replacement_product' => 'required|string',
                'quantity' => 'required|integer|min:1'
            ]);

            $id = DB::table('replacements')->insertGetId([
                'receipt_no' => $validated['receipt_no'],
                'returned_item' => $validated['returned_item'],
                'reason' => $validated['reason'],
                'replacement_product' => $validated['replacement_product'],
                'quantity' => $validated['quantity'],
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('Replacement created successfully', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Replacement created successfully',
                'data' => ['id' => $id]
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating replacement: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating replacement: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,approved,completed'
            ]);

            DB::table('replacements')
                ->where('id', $id)
                ->update([
                    'status' => $validated['status'],
                    'updated_at' => now()
                ]);

            Log::info('Replacement updated successfully', ['id' => $id, 'status' => $validated['status']]);

            return response()->json([
                'success' => true,
                'message' => 'Replacement updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating replacement: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating replacement'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::table('replacements')->where('id', $id)->delete();

            Log::info('Replacement deleted successfully', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Replacement deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting replacement: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting replacement'
            ], 500);
        }
    }
}
