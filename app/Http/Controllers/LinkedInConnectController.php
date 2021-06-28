<?php

namespace App\Http\Controllers;

use App\Models\Social;
use Illuminate\Http\Request;
use Exception;
use Socialite;
use GuzzleHttp\Client;

class LinkedInConnectController extends Controller
{
    public function connect()
    {

        return Socialite::driver('linkedin')->redirect();
        
    }

    public function callback(Request $request)
    {
        $user = Socialite::driver('linkedin')->user();
        $request->user()->social()->updateOrCreate([
            'platform' => Social::PLATFORM_LINKEDIN,
        ], [
            'token' => $user->token,
            'secret' => $request->code,
        ]);

        session()->flash('status', 'Your linkedin account has been linked!');

        return redirect()->route('todo.index');
    }
  
}
