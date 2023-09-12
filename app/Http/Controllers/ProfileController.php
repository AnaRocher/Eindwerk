<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index() {
        // Pas de views aan zodat je de juiste item counts kunt tonen in de knoppen op de profiel pagina.
        return view('profile.index');
    }

    public function edit() {
        // Vul het email adres van de ingelogde gebruiker in het formulier in
       $email = Auth::user()->email;
       return view('profile.edit', ['email' => $email]);
    }

    public function updateEmail(Request $request) {
        // Valideer het formulier, zorg dat het terug ingevuld wordt, en toon de foutmeldingen
        // Emailadres is verplicht en moet uniek zijn (behalve voor het huidge id van de gebruiker).
        // https://laravel.com/docs/9.x/validation#rule-unique -> Forcing A Unique Rule To Ignore A Given ID
        // Update de gegevens van de ingelogde gebruiker
        $request->validate([
            'email' => 'required|email|unique:users,email,'.Auth::id()
        ]);

        $user = User::find(Auth::id());
        $user->email = $request->email;
        $user->save();

        // Return het formulier terug

        // BONUS: Stuur een e-mail naar de gebruiker met de melding dat zijn e-mailadres gewijzigd is.

        return redirect()->route('profile.edit')->with('success', 'Email address updated successfully.');
    }

    public function updatePassword(Request $request) {
        // Valideer het formulier, zorg dat het terug ingevuld wordt, en toon de foutmeldingen
        // Wachtwoord is verplicht en moet confirmed zijn.
        // Update de gegevens van de ingelogde gebruiker met het nieuwe "hashed" password

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed'
        ]);

        $user = User::find(Auth::id());
        if (Hash::check($request->current_password, $user->password)){
            $user->password = Hash::make($request->password);
            $user->save();

            return redirect()->route('profile.edit')->with('success', 'Password updated successfully.');
        } else {
            return redirect()->route('profile.edit')->with('error', 'Current password is incorrect.');
        }

        // BONUS: Stuur een e-mail naar de gebruiker met de melding dat zijn wachtwoord gewijzigd is.
       
    }
}