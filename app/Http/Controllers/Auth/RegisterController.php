<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Contracts\IUser;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Ubient\PwnedPasswords\Rules\Pwned;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;


    protected $users;

    protected function registered(Request $request, User $user)
    {
        return response()->json($user, 200);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            //de gebruiker kan een gebruikersnaam en wachtwoord ingeven.
            'username' => ['required', 'string', 'max:15', 'alpha_dash', 'unique:users,username'],
            'name' => ['required', 'string', 'max:255'],
            //de gebruiker moet bij registratie een email adres opgeven
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            //het wachtwoord wordt enkel aanvaard als het minstens 7 karakters bevat: 'min:7'
            //alle 'printable' ASCII karakters worden aanvaard in het wachtwoord: 'regex:%^[ -~]+$%'
            // Een wachtwoord wordt beschouwd als vaak gebruikt indien  de HIBP API hiervoor aangeeft dat het meer dan 300 keer voorkwam in eerdere inbraken: 'pwned:300'
            'password' => ['required', 'string', 'min:7', 'confirmed', 'regex:%^[ -~]+$%', 'pwned:150'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        //dd($data);
        return User::create([
            'username' => $data['username'],
            'name' => $data['name'],
            'email' => $data['email'],
            //password wordt gehasht met bcrypt (default bij Laravel)
            'password' => Hash::make($data['password']),
        ]);
    }
}
