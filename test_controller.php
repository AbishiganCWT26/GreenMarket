<?php
require __DIR__.'/vendor/autoload.php';

$controllerClass = 'App\Http\Controllers\Admin\ComplaintController';

if (class_exists($controllerClass)) {
    echo "✅ Controller class exists!\n";
} else {
    echo "❌ Controller class NOT found!\n";

    // Check the file
    $controllerFile = __DIR__.'/app/Http/Controllers/Admin/ComplaintController.php';
    if (file_exists($controllerFile)) {
        echo "✅ Controller file exists at: $controllerFile\n";

        // Check the content
        $content = file_get_contents($controllerFile);
        if (strpos($content, 'namespace App\Http\Controllers\Admin') !== false) {
            echo "✅ Namespace is correct\n";
        } else {
            echo "❌ Namespace is WRONG\n";
        }

        if (strpos($content, 'class ComplaintController') !== false) {
            echo "✅ Class name is correct\n";
        } else {
            echo "❌ Class name is WRONG\n";
        }
    } else {
        echo "❌ Controller file NOT found!\n";
    }
}
