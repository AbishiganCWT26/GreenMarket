<?php

namespace App\Helpers;

class LogoHelper
{
    public static function getLogoPath()
    {
        $logoPaths = [
            public_path('assets/images/Logo-4.svg'),
            public_path('assets/images/Logo Green Market.png'),
            public_path('assets/images/Logo Green Market.png'),
        ];

        foreach ($logoPaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        // Return a default logo path or null
        return null;
    }

    public static function getLogoUrl()
    {
        $logoFiles = [
            'Logo-4.svg',
            'Logo Green Market.png',
            'Logo Green Market.png',
        ];

        foreach ($logoFiles as $file) {
            $url = asset('assets/images/' . $file);
            // You could add additional checks here if needed
            return $url;
        }

        return asset('assets/images/logo-default.png');
    }
}
