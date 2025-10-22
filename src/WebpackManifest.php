<?php

namespace App;

class WebpackManifest
{
    private static ?array $manifest = null;
    private static string $manifestPath = __DIR__ . '/../public/manifest.json';

    /**
     * Get the versioned asset path
     *
     * @param string $name The original filename (e.g., 'base.css' or 'carousel.js')
     * @return string The versioned path (e.g., '/_css/base.abc123.css')
     */
    public static function asset(string $name): string
    {
        if (self::$manifest === null) {
            self::loadManifest();
        }

        // Check if the asset exists in manifest
        if (isset(self::$manifest[$name])) {
            return self::$manifest[$name];
        }

        // Fallback: try to find by basename
        foreach (self::$manifest as $key => $value) {
            if (basename($key) === $name) {
                return $value;
            }
        }

        // Last resort fallback (shouldn't happen in production)
        error_log("Asset not found in manifest: {$name}");
        
        // Guess the path based on extension
        $ext = pathinfo($name, PATHINFO_EXTENSION);
        $folder = $ext === 'css' ? '_css' : '_js';
        return "/{$folder}/{$name}";
    }

    private static function loadManifest(): void
    {
        if (file_exists(self::$manifestPath)) {
            self::$manifest = json_decode(
                file_get_contents(self::$manifestPath),
                true
            ) ?? [];
        } else {
            self::$manifest = [];
            error_log("Webpack manifest not found at: " . self::$manifestPath);
        }
    }
}
