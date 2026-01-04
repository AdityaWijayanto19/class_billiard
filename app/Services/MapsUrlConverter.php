<?php

namespace App\Services;

class MapsUrlConverter
{
    /**
     * Convert various Google Maps formats to embeddable iframe format
     *
     * Supports:
     * 1. Full iframe HTML: <iframe src="...google.com/maps/embed?pb=..."></iframe>
     * 2. Embed URLs: https://www.google.com/maps/embed?pb=...
     * 3. Google Short URLs: https://maps.app.goo.gl/xxxxx (cannot embed - show as link)
     * 4. Place URLs: https://www.google.com/maps/place/... (cannot embed - show as link)
     *
     * @param  string  $url
     * @return array [
     *               'isEmbeddable' => bool,
     *               'embedUrl' => string|null,
     *               'shareUrl' => string
     *               ]
     */
    public static function convert($url)
    {
        if (empty($url)) {
            return [
                'isEmbeddable' => false,
                'embedUrl' => null,
                'shareUrl' => '',
            ];
        }

        $url = trim($url);

        // Check if it's a full iframe HTML
        if (strpos($url, '<iframe') !== false) {
            // Extract src attribute from iframe
            if (preg_match('/src=(["\'])(.+?)\1/i', $url, $matches)) {
                $url = $matches[2]; // Get the URL from src
            } else {
                // Invalid iframe format
                return [
                    'isEmbeddable' => false,
                    'embedUrl' => null,
                    'shareUrl' => '',
                ];
            }
        }

        $shareUrl = $url;

        // Already in embed format - THIS is the only truly embeddable format
        if (strpos($url, 'google.com/maps/embed') !== false) {
            return [
                'isEmbeddable' => true,
                'embedUrl' => $url,
                'shareUrl' => $url,
            ];
        }

        // Google short URL (maps.app.goo.gl) - Google blocks iframe embedding
        if (strpos($url, 'maps.app.goo.gl') !== false) {
            return [
                'isEmbeddable' => false,
                'embedUrl' => null,
                'shareUrl' => $url,
            ];
        }

        // Regular Google Maps URL - Also blocked by Google X-Frame-Options
        if (strpos($url, 'google.com/maps') !== false) {
            return [
                'isEmbeddable' => false,
                'embedUrl' => null,
                'shareUrl' => $url,
            ];
        }

        // For any other URL, cannot embed
        return [
            'isEmbeddable' => false,
            'embedUrl' => null,
            'shareUrl' => $url,
        ];
    }
}
