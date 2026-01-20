<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    protected $signature = 'mail:test {email}';
    protected $description = 'Test email configuration';

    public function handle()
    {
        $email = $this->argument('email');

        try {
            Mail::raw('Test email from Laravel', function($message) use ($email) {
                $message->to($email)->subject('Test Email');
            });

            $this->info('✅ Email sent successfully to: ' . $email);
        } catch (\Exception $e) {
            $this->error('❌ Email failed: ' . $e->getMessage());
        }

        return 0;
    }
}
