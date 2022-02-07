<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Support\Facades\URL;

//use Illuminate\Foundation\Auth\VerifiesEmails;

class VerificationController extends Controller
{

    use VerifiesEmails;
    /**
     * Where to redirect users after verification.
     *
     * @var string
     */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }


    public function verify(Request $request)
    {
        $user = User::findOrFail($request->input('user'));

        //check if url is a valid signature url
        if (! URL::hasValidSignature($request) ) {
            return response()->json([
                'errors' => ['message' => 'Invalid verification link']
            ], 422);
        }

        //check if user has already verified account
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'errors' => ['message' => 'Email has already verified']
            ], 422);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json([
            'message' => 'Email Succesfully Verified'
        ], 200);

    }


public function resend(Request $request)
{
    $this->validate($request,[
        'email'=>['email','required']
    ]);

    $user=User::where('email',$request->email)->first();
    if(! $user)
    {
        return response()->json(["errors"=>[
            "email"=>"No user couldbe found with this user address"
        ]],422);
    }
    //Check if the user has verified account
    if($user->hasVerifiedEmail())
    {
        return response()->json(["errors"=>[
            "message"=>"Email address already verified"
        ]],422);
    }
    $user->sendEmailVerificationNotification();

    return response()->json(['status'=>"verification link resent"]);

}

}
