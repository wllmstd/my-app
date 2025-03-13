<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\UserRequest;
use App\Models\User;

class RequestRevisionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userRequest;
    public $profiler;
    public $requester;
    public $feedback; 
    public $formattedDate;

    public function __construct(UserRequest $userRequest, $feedback)
    {
        if (!$userRequest) {
            throw new \Exception("🚨 UserRequest is NULL in RequestRevisionMail constructor.");
        }
    
        $this->userRequest = $userRequest;
        $this->profiler = User::find($userRequest->accepted_by);
        $this->requester = User::find($userRequest->Users_ID);
        $this->feedback = $feedback;
        $this->formattedDate = \Carbon\Carbon::parse($userRequest->Date_Created)->format('M d, Y, h:i A'); //Format Date
    }
    

    public function build()
    {
        return $this->from($this->requester->email, $this->requester->first_name . ' ' . $this->requester->last_name) //From Requester (TA)
                    ->to($this->profiler->email) // Send to Support (Profiler)
                    // ->subject('Revision Requested for Request #' . $this->userRequest->Request_ID)
                    ->subject('Revision Requested for ' . $this->userRequest->First_Name . ' ' . $this->userRequest->Last_Name)
                    ->view('emails.requestRevision')
                    ->with([
                        'request' => $this->userRequest,
                        'profiler' => $this->profiler,
                        'requester' => $this->requester,
                        'feedback' => $this->feedback, 
                    ]);
    }
}
