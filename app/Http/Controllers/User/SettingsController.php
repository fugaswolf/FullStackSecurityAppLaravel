<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Rules\MatchOldPassword;
use App\Rules\CheckSamePassword;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class SettingsController extends Controller
{

    //protected $users;


    //method to update the profile
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $this->validate($request, [
            'tagline' => ['required'],
            'name' => ['required'],
            'about' => ['required', 'string', 'min:20'],
            'formatted_address' => ['required'],
            'location.latitude' => ['required', 'numeric', 'min:-90', 'max:90'],
            'location.longitude' => ['required', 'numeric', 'min:-180', 'max:180']
        ]);

        $location = new Point($request->location['latitude'], $request->location['longitude']);
        

        $user->update([
            'name' => $request->name,
            'formatted_address' => $request->formatted_address,
            'location' => $location,
            'available_to_hire' => $request->available_to_hire,
            'about' => $request->about,
            'tagline' => $request->tagline,
        ]);

        return new UserResource($user);

    }


    //method to update the password
    public function updatePassword(Request $request)
    {
     

        //current password
        $this->validate($request, [
            'current_password' => ['required', new MatchOldPassword],
            'password' => ['required', 'string', 'min:7', 'confirmed', 'regex:%^[ -~]+$%', 'pwned:150', new CheckSamePassword],
        ]);
        
        //new password
        $request->user()->update([
            'password' => bcrypt($request->password)
        ]);
        
        //password confirmation
        return response()->json(['message' => 'Password updated'], 200);

    }
}
