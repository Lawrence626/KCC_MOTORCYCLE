<?php

namespace App\Services;

class AppSettingService
{
    protected static array $allowedExtensions = ['png', 'jpg', 'jpeg', 'webp', 'svg', 'gif', 'bmp', 'ico', 'avif', 'jfif', 'tif', 'tiff', 'heic', 'heif'];

    /**
     * Get public directory path safely.
     */
    public static function getPublicPath(string $path = ''): string
    {
        $base = null;
        try {
            if (function_exists('public_path')) {
                $base = public_path();
            }
        } catch (\Throwable $e) {}

        if (!$base) {
            $base = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public';
        }

        return $path ? rtrim($base, '/\\') . DIRECTORY_SEPARATOR . ltrim($path, '/\\') : $base;
    }

    /**
     * Get the URL of the active logo (custom logo if uploaded, or default Logo.png).
     * Always returns a root-relative path (/images/...) to guarantee it works on localhost,
     * 127.0.0.1, custom domains, or ports without dependency on APP_URL.
     */
    public static function getLogoUrl(): string
    {
        $customPath = self::getCustomLogoFilePath();
        if ($customPath && file_exists($customPath)) {
            $filename = basename($customPath);
            return '/images/' . $filename . '?v=' . filemtime($customPath);
        }

        return '/images/Logo.png';
    }

    /**
     * Check if a custom logo is currently active.
     */
    public static function hasCustomLogo(): bool
    {
        $customPath = self::getCustomLogoFilePath();
        return $customPath !== null && file_exists($customPath);
    }

    /**
     * Detect file extension from binary contents or SVG markup.
     */
    protected static function detectExtensionFromData(string $data): ?string
    {
        if (str_starts_with($data, "\x89PNG\r\n\x1a\n")) {
            return 'png';
        }
        if (str_starts_with($data, "\xFF\xD8\xFF")) {
            return 'jpg';
        }
        if (str_starts_with($data, 'GIF87a') || str_starts_with($data, 'GIF89a')) {
            return 'gif';
        }
        if (str_starts_with($data, 'RIFF') && str_contains(substr($data, 8, 8), 'WEBP')) {
            return 'webp';
        }
        if (str_starts_with($data, 'BM')) {
            return 'bmp';
        }
        if (str_starts_with($data, "\x00\x00\x01\x00")) {
            return 'ico';
        }
        if (str_contains(substr($data, 4, 12), 'ftypavif')) {
            return 'avif';
        }
        $prefix = strtolower(substr(ltrim($data), 0, 500));
        if (str_contains($prefix, '<svg') || str_contains($prefix, '<?xml')) {
            return 'svg';
        }
        return null;
    }

    /**
     * Save an uploaded logo image file or raw binary/base64 data.
     */
    public static function saveLogo($uploadedFileOrData): bool
    {
        if (!$uploadedFileOrData) {
            return false;
        }

        // Delete any existing custom logos first
        self::resetLogo();

        $ext = 'png';
        $rawData = null;
        $sourcePath = null;

        if ($uploadedFileOrData instanceof \Illuminate\Http\UploadedFile) {
            $clientExt = strtolower(ltrim($uploadedFileOrData->getClientOriginalExtension() ?: ($uploadedFileOrData->extension() ?: ''), '.'));
            if ($clientExt && in_array($clientExt, self::$allowedExtensions, true)) {
                $ext = $clientExt === 'jpeg' ? 'jpg' : $clientExt;
            } elseif ($uploadedFileOrData->getClientMimeType() === 'image/svg+xml') {
                $ext = 'svg';
            }
            $sourcePath = $uploadedFileOrData->getRealPath() ?: $uploadedFileOrData->getPathname();
            if ($sourcePath && file_exists($sourcePath)) {
                $fileBytes = @file_get_contents($sourcePath);
                if ($fileBytes !== false) {
                    $detected = self::detectExtensionFromData($fileBytes);
                    if ($detected) {
                        $ext = $detected;
                    }
                    $rawData = $fileBytes;
                }
            }
        } elseif (is_string($uploadedFileOrData)) {
            // Check if base64 data URI (data:image/...;base64,...)
            if (preg_match('#^data:([^;]+);base64,(.+)$#si', $uploadedFileOrData, $matches)) {
                $mime = strtolower(trim($matches[1]));
                $base64Raw = str_replace(' ', '+', trim($matches[2]));
                $decoded = base64_decode($base64Raw);
                if ($decoded !== false) {
                    $rawData = $decoded;
                    $detected = self::detectExtensionFromData($rawData);
                    if ($detected) {
                        $ext = $detected;
                    } elseif (str_contains($mime, 'svg')) {
                        $ext = 'svg';
                    } elseif (str_contains($mime, 'webp')) {
                        $ext = 'webp';
                    } elseif (str_contains($mime, 'jpeg') || str_contains($mime, 'jpg')) {
                        $ext = 'jpg';
                    } elseif (str_contains($mime, 'gif')) {
                        $ext = 'gif';
                    }
                }
            } elseif (str_contains($uploadedFileOrData, '<svg')) {
                $ext = 'svg';
                $rawData = $uploadedFileOrData;
            } else {
                $cleanStr = str_replace(' ', '+', trim($uploadedFileOrData));
                $decoded = @base64_decode($cleanStr, true);
                if ($decoded !== false && strlen($decoded) > 10) {
                    $rawData = $decoded;
                    $detected = self::detectExtensionFromData($rawData);
                    if ($detected) {
                        $ext = $detected;
                    }
                } else {
                    $rawData = $uploadedFileOrData;
                }
            }
        }

        $targetDir = self::getPublicPath('images');
        if (!file_exists($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        $targetName = 'custom_logo.' . $ext;
        $targetPath = $targetDir . DIRECTORY_SEPARATOR . $targetName;

        // Write raw binary/text data
        if ($rawData !== null && !empty($rawData)) {
            $written = @file_put_contents($targetPath, $rawData) !== false;
            clearstatcache(true, $targetPath);
            return $written;
        }

        // Fallback: copy or move from source path
        if ($sourcePath && file_exists($sourcePath)) {
            try {
                if (@copy($sourcePath, $targetPath)) {
                    clearstatcache(true, $targetPath);
                    return true;
                }
            } catch (\Throwable $e) {}

            if ($uploadedFileOrData instanceof \Illuminate\Http\UploadedFile) {
                try {
                    $uploadedFileOrData->move($targetDir, $targetName);
                    clearstatcache(true, $targetPath);
                    return true;
                } catch (\Throwable $e) {}
            }
        }

        return false;
    }

    /**
     * Reset to the default KCC logo by deleting any custom logo file.
     */
    public static function resetLogo(): bool
    {
        $deleted = false;
        $imagesDir = self::getPublicPath('images');
        $files = @glob($imagesDir . DIRECTORY_SEPARATOR . 'custom_logo.*') ?: [];
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
                $deleted = true;
            }
        }
        clearstatcache();
        return $deleted;
    }

    /**
     * Helper to find existing custom logo path if any.
     */
    protected static function getCustomLogoFilePath(): ?string
    {
        $imagesDir = self::getPublicPath('images');
        $files = @glob($imagesDir . DIRECTORY_SEPARATOR . 'custom_logo.*') ?: [];
        foreach ($files as $file) {
            if (is_file($file)) {
                return $file;
            }
        }
        return null;
    }

    /**
     * Get active primary theme color. Defaults to #6EC1D1.
     */
    public static function getThemeColor(): string
    {
        $file = storage_path('app/settings.json');
        if (file_exists($file)) {
            $data = @json_decode(@file_get_contents($file), true);
            if (!empty($data['theme_color'])) {
                return $data['theme_color'];
            }
        }
        return '#6EC1D1';
    }

    /**
     * Set active primary theme color.
     */
    public static function setThemeColor(string $color): bool
    {
        $color = trim($color);
        if (!str_starts_with($color, '#')) {
            $color = '#' . $color;
        }
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color) && !preg_match('/^#[0-9A-Fa-f]{3}$/', $color)) {
            return false;
        }
        $file = storage_path('app/settings.json');
        $dir = dirname($file);
        if (!file_exists($dir)) {
            @mkdir($dir, 0755, true);
        }
        $data = file_exists($file) ? (@json_decode(@file_get_contents($file), true) ?: []) : [];
        $data['theme_color'] = strtoupper($color);
        return @file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT)) !== false;
    }
}

if (!function_exists('app_logo_url')) {
    function app_logo_url(): string
    {
        return \App\Services\AppSettingService::getLogoUrl();
    }
}
