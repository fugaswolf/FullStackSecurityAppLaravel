<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use App\Providers\RouteServiceProvider;
//use Illuminate\Foundation\Auth\VerifiesEmails;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller #CUSTOM CONTROLLER BY ALVI ISTAMALOV#
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    //verificatie email sturen naar de user's email
    public function verify(Request $request, User $user)
    {
        // is de url een valid signed url?
        if(! URL::hasValidSignature($request)){
            return response()->json(["errors" => [
                "message" => "Invalid verification link or signature"
            ]], 422);
        }

        // heeft de user zijn email al gevalideerd?
        if($user->hasVerifiedEmail()){
            return response()->json(["errors" => [
                "message" => "Email address is already verified"
            ]], 422);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json(['message' => 'Email is successfully verified'], 200);

    }


    //verificatie email opnieuw sturen
    public function resend(Request $request)
    {
        // heeft de request de user's email adres?
        $this->validate($request, [
            'email' => ['email', 'required']
        ]);
        
    
        $user = User::where('email', $request->email)->first();
        
        if(! $user){
            return response()->json(["errors" => [
                "email" => "No user has been found with this email address"
            ]], 422);
        }

        if($user->hasVerifiedEmail()){
            return response()->json(["errors" => [
                "message" => "Email address is already verified"
            ]], 422);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['status' => "Verification link resent"]);

    }




    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }
}
