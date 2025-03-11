<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\UserRequest;

class RequestAcceptedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $request;
    public $profiler;

    public function __construct($user, $request, $profiler)
    {
        $this->user = $user;
        $this->request = $request;
        $this->profiler = $profiler;
    }

    public function build()
    {
        return $this->from($this->profiler->email, $this->profiler->first_name . ' ' . $this->profiler->last_name) // ✅ Fix sender name
                    ->to($this->user->email) // ✅ Send to TA (Requester)
                    ->subject('Your Request Has Been Accepted')
                    ->view('emails.requestAccepted')
                    ->with([
                        'user' => $this->user, 
                        'request' => $this->request, 
                        'profiler' => $this->profiler, 
                    ]);
    }
    
    
}