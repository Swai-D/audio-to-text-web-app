<?php

namespace App\Helpers;

class AssetHelper
{
    /**
     * Get the correct asset URL based on environment
     */
    public static function viteAsset(string $path): string
    {
        if (app()->environment(['local', 'development'])) {
            return asset($path);
        }

        // For production, return the compiled asset path
        $manifestPath = public_path('build/manifest.json');
        
        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
            
            if (isset($manifest[$path])) {
                return asset('build/' . $manifest[$path]['file']);
            }
        }

        // Fallback to original path
        return asset($path);
    }

    /**
     * Get CSS asset URL
     */
    public static function viteCss(string $path): string
    {
        return self::viteAsset($path);
    }

    /**
     * Get JavaScript asset URL
     */
    public static function viteJs(string $path): string
    {
        return self::viteAsset($path);
    }

    /**
     * Get the current asset filename for production
     */
    public static function getAssetFilename(string $path): string
    {
        $manifestPath = public_path('build/manifest.json');
        
        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
            
            if (isset($manifest[$path])) {
                return $manifest[$path]['file'];
            }
        }

        return basename($path);
    }
}
