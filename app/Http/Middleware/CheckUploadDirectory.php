<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUploadDirectory
{
    public function handle(Request $request, Closure $next)
    {
        $directories = [
            public_path('uploads'),
            public_path('uploads/product_images'),
            public_path('uploads/profile_pictures'),
        ];
        
        foreach ($directories as $directory) {
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }
        }
        
        return $next($request);
    }
}