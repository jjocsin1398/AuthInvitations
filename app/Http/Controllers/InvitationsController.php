<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvitationRequest;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InvitationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invitations = Invitation::where('registered_at', null)->orderBy('created_at', 'desc')->get();
        return view('invitations.index', compact('invitations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreInvitationRequest $request)
    {

        $invitation = new Invitation($request->all());
        $invitation->generateInvitationToken();
        $invitation->save();

        //Email Sending

        $to_name = "NEW USER";
        $to_email = $invitation->email;
        $inv_link = $invitation->getLink();
    
        $data = array('name'=>$to_name, 'body' =>$inv_link );
        Mail::send('email.mail', $data, function($message) use ($to_name, $to_email) {
        $message->to($to_email, $to_name)
        ->subject('Requesting Invitation');
        $message->from('laravelassessment@gmail.com',"Requesting Invitation");
        });    
        
        //end of sending email

        return redirect()->route('requestInvitation')
            ->with('success', 'Invitation to register successfully requested. Please wait for registration link.');

    }
}
