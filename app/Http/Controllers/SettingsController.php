<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AppSettingService;

class SettingsController extends Controller
{
    /**
     * Display the General Settings page.
     */
    public function index()
    {
        return view('settings.general', [
            'isCustomLogo' => AppSettingService::hasCustomLogo(),
            'currentLogoUrl' => AppSettingService::getLogoUrl(),
            'currentThemeColor' => AppSettingService::getThemeColor(),
        ]);
    }

    /**
     * Handle custom logo upload.
     */
    public function updateLogo(Request $request)
    {
        try {
            $uploaded = null;

            // 1. Check direct input or base64
            if ($request->filled('logo_base64')) {
                $uploaded = $request->input('logo_base64');
            } elseif ($request->filled('logo') && !is_object($request->input('logo'))) {
                $uploaded = $request->input('logo');
            }

            // 2. Check JSON payload / raw body
            if (!$uploaded) {
                $rawContent = $request->getContent();
                if (!empty($rawContent)) {
                    $json = @json_decode($rawContent, true);
                    if (is_array($json) && !empty($json['logo_base64'])) {
                        $uploaded = $json['logo_base64'];
                    } elseif (is_array($json) && !empty($json['logo'])) {
                        $uploaded = $json['logo'];
                    } elseif (str_starts_with(trim($rawContent), 'data:image/') || str_contains($rawContent, '<svg')) {
                        $uploaded = $rawContent;
                    }
                }
            }

            // 3. Check uploaded file
            if (!$uploaded && $request->hasFile('logo')) {
                $file = $request->file('logo');
                if ($file && $file->isValid()) {
                    $uploaded = $file;
                } elseif ($file) {
                    $uploaded = $file;
                }
            }

            if (!$uploaded) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select an image file to upload.',
                ], 422);
            }

            $saved = AppSettingService::saveLogo($uploaded);

            if (!$saved) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save the uploaded logo file.',
                ], 500);
            }

            $newLogoUrl = AppSettingService::getLogoUrl();

            return response()->json([
                'success' => true,
                'logo_url' => $newLogoUrl,
                'message' => 'System logo updated successfully! The sidebar logo has been updated automatically.',
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Logo upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error uploading logo: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reset the logo to default.
     */
    public function resetLogo(Request $request)
    {
        try {
            AppSettingService::resetLogo();
            $defaultLogoUrl = AppSettingService::getLogoUrl();

            return response()->json([
                'success' => true,
                'logo_url' => $defaultLogoUrl,
                'message' => 'Logo reset to default KCC logo successfully!',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error resetting logo: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update primary theme color.
     */
    public function updateThemeColor(Request $request)
    {
        try {
            $color = $request->input('color');
            if (!$color && $request->isJson()) {
                $color = $request->json('color');
            }
            if (!$color || !is_string($color)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please provide a valid hex color code.',
                ], 422);
            }

            $saved = AppSettingService::setThemeColor($color);
            if (!$saved) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save theme color.',
                ], 422);
            }

            return response()->json([
                'success' => true,
                'color' => AppSettingService::getThemeColor(),
                'message' => 'Theme color updated successfully!',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating theme color: ' . $e->getMessage(),
            ], 500);
        }
    }
}
