<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check user 6 and their buyer data
$user = DB::table('users')->where('id', 6)->first();
if ($user) {
    echo "=== User #6 ===\n";
    echo "Username: " . $user->username . "\n";
    echo "Role: " . $user->role . "\n";
    echo "Email: " . $user->email . "\n\n";

    $buyer = DB::table('buyers')->where('user_id', 6)->first();
    if ($buyer) {
        echo "=== Buyer Data ===\n";
        echo "District: " . ($buyer->district ?? 'NULL') . "\n";
        echo "Business Name: " . ($buyer->business_name ?? 'NULL') . "\n";
        echo "Business Type: " . ($buyer->business_type ?? 'NULL') . "\n";
        echo "Primary Mobile: " . ($buyer->primary_mobile ?? 'NULL') . "\n";
        echo "NIC: " . ($buyer->nic_no ?? 'NULL') . "\n";
        echo "Residential Address: " . ($buyer->residential_address ?? 'NULL') . "\n";
    } else {
        echo "No buyer record found for user 6\n";
    }
} else {
    echo "User #6 not found!\n";
}
