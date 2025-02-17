<?php
namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class VerifyEmail extends Mailable
{
    use SerializesModels;

    public $user;
    public $verificationCode;

    // Constructor to pass the data
    public function __construct(User $user, $verificationCode)
    {
        $this->user = $user;
        $this->verificationCode = $verificationCode;  // Store the verification code
    }

    public function build()
    {
        return $this->view('emails.verify-email')
                    ->with([
                        'verificationCode' => $this->verificationCode,
                        'user' => $this->user,
                    ])
                    ->subject('Email Verification');
    }
}
