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
        
        // Store in cache with user-specific key and global fallback
        if ($userId) {
            cache()->put("pos_scan_{$userId}", $scanData, 300);
        }
        cache()->put("pos_scan_latest", $scanData, 300);
        
        return response()->json([
            'success' => true,
            'message' => 'Scan received',
            'data' => $scanData
        ]);
    }

    /**
     * Check for new scans and cart updates from other devices
     */
    public function checkScan(Request $request)
    {
        $userId = Auth::id();
        $scanData = $userId ? cache()->get("pos_scan_{$userId}") : null;
        
        if (!$scanData) {
            $scanData = cache()->get("pos_scan_latest");
        }
        
        $cartData = $userId ? cache()->get("pos_cart_{$userId}") : null;
        if (!$cartData) {
            $cartData = cache()->get("pos_cart_latest");
        }
        
        return response()->json([
            'success' => true,
            'scan' => ($scanData && !empty($scanData['code'])) ? $scanData : null,
            'cart' => $cartData ?? null
        ]);
    }

    /**
     * Sync active cart state across devices (Desktop <-> Mobile)
     */
    public function syncCart(Request $request)
    {
        $userId = Auth::id() ?? 0;
        $cart = $request->input('cart', []);
        $services = $request->input('services', []);
        $discount = (float) $request->input('discount', 0);
        $extraCharge = (float) $request->input('extraCharge', 0);
        $clientId = (string) $request->input('clientId', '');
        $timestamp = (int) ($request->input('timestamp') ?? (time() * 1000));

        $cartPayload = [
            'cart' => is_array($cart) ? $cart : [],
            'services' => is_array($services) ? $services : [],
            'discount' => $discount,
            'extraCharge' => $extraCharge,
            'clientId' => $clientId,
            'timestamp' => $timestamp,
            'user_id' => $userId,
        ];

        if ($userId) {
            cache()->put("pos_cart_{$userId}", $cartPayload, 7200);
        }
        cache()->put("pos_cart_latest", $cartPayload, 7200);

        return response()->json([
            'success' => true,
            'message' => 'Cart synced',
            'data' => $cartPayload
        ]);
    }

    /**
     * Retrieve current active cart
     */
    public function getActiveCart(Request $request)
    {
        $userId = Auth::id();
        $cartData = $userId ? cache()->get("pos_cart_{$userId}") : null;
        if (!$cartData) {
            $cartData = cache()->get("pos_cart_latest");
        }

        return response()->json([
            'success' => true,
            'cart' => $cartData
        ]);
    }
}

