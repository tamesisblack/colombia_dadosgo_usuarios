<?php

namespace App\Http\Controllers;

use App\Mail\SetEmailData;
use App\Mail\SendMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Redirect;

class SendEmailController extends Controller
{
    public function __construct()
    {
    }

    function send(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required'
        ]);
        
        $data = $request->all();

        Mail::to(env('MAIL_TO_ADDRESS'))->send(new SendMail($data));
        
        return back()->with('success_contact', 'Thanks for contacting us!');
    }

    function sendMail(Request $request)
    {

        $data = $request->all();
        $subject = $data['subject'];
        $message = $data['message'];
        $recipients = $data['recipients'];

        Mail::to($recipients)->send(new SetEmailData($subject, $message));

        return "email sent successfully!";
    }
}

?>