<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\UserRequest;
use App\Models\User;
use Carbon\Carbon;

class RequestCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $request;
    public $profiler;
    public $formattedDate;

    public function __construct($user, $request, $profiler)
    {
        $this->user = $user;
        $this->request = $request;
        $this->profiler = $profiler;
        $this->formattedDate = Carbon::parse($request->Updated_Time)->format('M d, Y, h:i A'); // ✅ Format completion date
    }

    public function build()
    {
        return $this->from($this->user->email, $this->user->first_name . ' ' . $this->user->last_name)
                    ->to($this->profiler->email)
                    ->subject('Request Completed for Applicant ' . $this->request->First_Name . ' ' . $this->request->Last_Name) // ✅ Updated subject
                    ->view('emails.requestCompleted')
                    ->with([
                        'user' => $this->user,
                        'request' => $this->request,
                        'profiler' => $this->profiler,
                    ]);
    }
}
