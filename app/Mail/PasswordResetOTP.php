<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetOTP extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->subject('GreenMarket - Password Reset OTP')
                    ->view('emails.password-reset-otp')
                    ->with([
                        'otp' => $this->otp,
                        'expiry_minutes' => 10
                    ]);
    }
}
