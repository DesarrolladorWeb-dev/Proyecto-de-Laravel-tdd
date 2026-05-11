<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login( Request $request){
        // primero valida
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|min:8'
        ]);

        //luego guarda en credentials 
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return jsonResponse(status:401, message:'Unauthorized');
        }

        // responde
        return jsonResponse(data: [
            'token' => $token,
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
