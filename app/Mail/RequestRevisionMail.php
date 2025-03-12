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
    public $feedback; // ✅ Add feedback

    public function __construct(UserRequest $userRequest, $feedback)
    {
        if (!$userRequest) {
            throw new \Exception("🚨 UserRequest is NULL in RequestRevisionMail constructor.");
        }

        $this->userRequest = $userRequest;
        $this->profiler = User::find($userRequest->accepted_by);
        $this->requester = User::find($userRequest->Users_ID);
        $this->feedback = $userRequest->feedback; // ✅ Store feedback
    }

    public function build()
    {
        return $this->subject('Revision Requested for Request #' . $this->userRequest->Request_ID)
                    ->view('emails.requestRevision')
                    ->with([
                        'request' => $this->userRequest,
                        'profiler' => $this->profiler,
                        'requester' => $this->requester,
                        'feedback' => $this->feedback, // ✅ Pass feedback
                    ]);
    }
}
