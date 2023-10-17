<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Notifications\UserRegisteredNotification;


class AuthController extends Controller
{
    public function login() {
        return view('auth.login');
    }

    public function handleLogin(Request $request) {
        // Valideer het formulier
        // Elk veld is verplicht
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'          
        ]);

        // Write the login logic to login.
        // Once logged in redirect the visitor to the intended "profile" route (see below)
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            return redirect()->intended(route('profile'));
        }        

        // If your data is wrong send back to the form with
        // a message for the email field that the data is not correct.
        return back()->withErrors(['email' => 'We kunnen u niet aanmelden met deze inloggegevens']);
    }

    public function register() {        
        return view('auth.register');
    }

    public function handleRegister(Request $request) {
        // Valideer het formulier.
        // Elk veld is verplicht / Wachtwoord en confirmatie moeten overeen komen / Email adres moet uniek zijn
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|confirmed'
        ]);

        // Bewaar een nieuwe gebruiker in de databank met een beveiligd wachtwoord.
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        // BONUS: Verstuur een email naar de gebruiker waarin staat dat er een nieuwe account geregistreerd is voor de gebruiker.

        return redirect()->route('login');
    }

    public function logout() {
        // Gebruiker moet uitloggen
        Auth::logout();

        return back();
    }
}