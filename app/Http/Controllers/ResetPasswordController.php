<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;


// use App\Http\Controllers\Controller;
// use Illuminate\Auth\Events\PasswordReset;
// use Illuminate\Auth\Notifications\ResetPassword;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Password;
// use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function send(Request $request) {


        $request->validate(['email' => 'required|email|exists:users,email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        $sent = $status === Password::RESET_LINK_SENT;
        // si fue enviado este status , entonces enviamos OK
        // luego lo mismo si fue enviado que sea un 200 y si no se envia el 500
        // esto es para el test de resetPassword
        return jsonResponse($sent ?  'OK' : 'Error' ,$sent ?  200 : 500);
        
    }

    public function resetPassword(Request $request) 
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|confirmed',
        ]);


        $status  = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),

            function ($user, $password) use ($request) {

                $user->forceFill([
                    'password' => Hash::make($password),
                    
                ])->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );
        $message = match($status){
            Password::INVALID_USER => 'Invalid email',
            Password::INVALID_TOKEN => 'Invalid token',
            default => 'OK',
        };
       
        // dd($reset);
        return jsonResponse(message: $message , status: $status === Password::PASSWORD_RESET ?  200 : 500);



    }
}