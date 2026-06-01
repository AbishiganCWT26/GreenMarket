<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$view = view('contactus')->withErrors(new \Illuminate\Support\MessageBag())->render();
file_put_contents('rendered_contact.html', $view);
echo "Rendered successfully.\n";
