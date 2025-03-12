<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\UserRequest;
use App\Models\User;

class FileUploadedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $request;
    public $profiler;
    public $uploadedFiles;

    public function __construct($user, $request, $profiler, $uploadedFiles)
    {
        $this->user = $user;
        $this->request = $request;
        $this->profiler = $profiler;
        $this->uploadedFiles = $uploadedFiles;
    }

    public function build()
    {
        return $this->from($this->profiler->email, $this->profiler->first_name . ' ' . $this->profiler->last_name)
                    ->to($this->user->email)
                    ->subject('Your Requested File Has Been Uploaded')
                    ->view('emails.fileUploaded')
                    ->with([
                        'user' => $this->user,
                        'request' => $this->request,
                        'profiler' => $this->profiler,
                        'uploadedFiles' => $this->uploadedFiles
                    ]);
    }
}
