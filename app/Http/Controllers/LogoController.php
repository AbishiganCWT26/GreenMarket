<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogoController extends Controller
{
    /* the logo is in oval rectangular shape */
    public function getLogoPath()
    {
        $svgPath = public_path('assets/images/Logo-4.svg');
        $pngPath = public_path('assets/images/Logo-4.png');

        if (file_exists($svgPath)) {
            return $svgPath;
        }

        if (file_exists($pngPath)) {
            return $pngPath;
        }

        return public_path('assets/images/logo-default.png');
    }

    public function getLogoBase64()
    {
        $path = $this->getLogoPath();

        if (!file_exists($path)) {
            return '';
        }

        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);

        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}
