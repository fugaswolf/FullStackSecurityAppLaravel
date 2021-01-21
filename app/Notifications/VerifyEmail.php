<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\URL;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\VerifyEmail as Notification;

class VerifyEmail extends Notification
{
    //default verificationUrl overriden
    //verificatie email builder voor de API
    protected function verificationUrl($notifiable)
    {
        $appUrl = config('app.client_url', config('app.url'));


        $url = URL::temporarySignedRoute(
            'verification.verify', 
            //lifetime of the link is 60 minutes
            Carbon::now()->addMinutes(60), 
            ['user' => $notifiable->id]
        );

        //replace the url with the app url (CLIENT URL)
        return str_replace(url('/api'), $appUrl, $url);
        
    }
}
