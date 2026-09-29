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
        $code = trim((string) $request->input('code', ''));
        if (empty($code)) {
            return response()->json([
                'success' => false,
                'message' => 'Empty scan code'
            ], 422);
        }

        $userId = Auth::id() ?? 0;
        
        $scanData = [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'code' => $code,
            'timestamp' => now()->toISOString(),
            'user_id' => $userId,
        ];
        
        // Store in cache with user-specific key and global latest key
        if ($userId) {
            cache()->put("pos_scan_{$userId}", $scanData, 120);
        }
        cache()->put("pos_scan_latest", $scanData, 120);
        
        return response()->json([
            'success' => true,
            'message' => 'Scan received',
            'data' => $scanData
        ]);
    }

    /**
     * Check for new scans from mobile device
     */
    public function checkScan(Request $request)
    {
        $userId = Auth::id();
        $scanData = $userId ? cache()->get("pos_scan_{$userId}") : null;
        
        if (!$scanData) {
            $scanData = cache()->get("pos_scan_latest");
        }
        
        if ($scanData && !empty($scanData['code'])) {
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
