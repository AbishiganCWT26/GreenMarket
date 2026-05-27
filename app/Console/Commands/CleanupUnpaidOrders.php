<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupUnpaidOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete temporary delivery order items older than 24 hours and send reminder SMS';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. Delete items older than 24 hours (Hard Delete)
        $deletedCount = DB::table('temporary_delivery_order_items')
            ->where('created_at', '<', now()->subHours(24))
            ->delete();

        if ($deletedCount > 0) {
            $this->info("Deleted $deletedCount expired temporary order items.");
            Log::info("CleanupUnpaidOrders: Deleted $deletedCount expired temporary order items.");
        }

        // 2. Send fallback SMS for items with ~1.5 hours left
        // Target items older than 22.5 hours but not yet 24 hours
        $toNotify = DB::table('temporary_delivery_order_items')
            ->where('sms_sent', false)
            ->where('created_at', '<=', now()->subMinutes(1350)) // 22.5 hours = 1350 minutes
            ->where('created_at', '>', now()->subHours(24))
            ->get();

        $smsCount = 0;
        $processedOrders = [];

        foreach ($toNotify as $item) {
            if (in_array($item->order_id, $processedOrders)) {
                continue;
            }

            $buyerPhone = DB::table('buyers')->where('id', $item->buyer_id)->value('primary_mobile');
            
            if ($buyerPhone) {
                $message = "Please pay your unpaid order ($item->order_number) with in 1 hour";
                
                if ($this->sendSMS($buyerPhone, $message)) {
                    DB::table('temporary_delivery_order_items')
                        ->where('order_id', $item->order_id)
                        ->update(['sms_sent' => true]);
                    
                    $processedOrders[] = $item->order_id;
                    $smsCount++;
                }
            }
        }

        if ($smsCount > 0) {
            $this->info("Sent $smsCount reminder SMS notifications.");
            Log::info("CleanupUnpaidOrders: Sent $smsCount reminder SMS notifications.");
        }

        return Command::SUCCESS;
    }

    /**
     * Send SMS using TextIt API
     */
    private function sendSMS($phone, $message)
    {
        try {
            $user = env('SMS_USER', 'number');
            $password = env('SMS_PASSWORD', '0000');
            $text = urlencode($message);
            $baseurl = env('SMS_API_URL', 'https://textit.biz/sendmsg');
            $url = "$baseurl/?id=$user&pw=$password&to=$phone&text=$text";
            
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => 30,
            ]);
            $result = curl_exec($ch);
            curl_close($ch);
            
            return $result !== false;
        } catch (\Exception $e) {
            Log::error('CleanupUnpaidOrders SMS failed: ' . $e->getMessage());
            return false;
        }
    }
}
