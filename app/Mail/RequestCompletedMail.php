<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\UserRequest;
use App\Models\User;

class RequestCompletedMail extends Mailable
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
        return $this->from($this->user->email, $this->user->first_name . ' ' . $this->user->last_name)
                    ->to($this->profiler->email)
                    ->subject('Request Marked as Completed')
                    ->view('emails.requestCompleted')
                    ->with([
                        'user' => $this->user,
                        'request' => $this->request,
                        'profiler' => $this->profiler
                    ]);
    }
}
