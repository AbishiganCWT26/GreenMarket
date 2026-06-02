<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    Illuminate\Support\Facades\DB::beginTransaction();
    
    Illuminate\Support\Facades\DB::table('invoices')->insert([
        'invoice_number' => 'TEST-TX-123',
        'order_id' => 1,
        'invoice_path' => 'test-tx.pdf',
        'generated_at' => now(),
        'updated_at' => now()
    ]);
    
    Illuminate\Support\Facades\DB::commit();
    echo "Transaction Insert successful\n";
} catch (\Exception $e) {
    Illuminate\Support\Facades\DB::rollBack();
    echo "Transaction Insert failed: " . $e->getMessage() . "\n";
}
