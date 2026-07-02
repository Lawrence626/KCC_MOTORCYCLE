<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    /**
     * Handle QR code scan from mobile device
     */
    public function handleScan(Request $request)
    {
        $code = $request->input('code');
        
        // Store the scan in cache for the current user's session
        // This allows the terminal to poll for new scans
        $scanData = [
            'code' => $code,
            'timestamp' => now()->toISOString(),
            'user_id' => Auth::id(),
        ];
        
        // Store in cache with user-specific key
        cache()->put("pos_scan_{$scanData['user_id']}", $scanData, 60);
        
        return response()->json([
            'success' => true,
            'message' => 'Scan received'
        ]);
    }

    /**
     * Check for new scans from mobile device
     */
    public function checkScan(Request $request)
    {
        $userId = Auth::id();
        $scanData = cache()->get("pos_scan_{$userId}");
        
        if ($scanData) {
            return response()->json([
                'success' => true,
                'scan' => $scanData
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No new scans'
        ]);
    }
}
