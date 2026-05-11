<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function store(CreateUserRequest $request) {

        // dd($request->all());
        // de esta menera creamos y recibimos toda la info del usuario 
       $user =  User::create($request->all());
    //    usamos el resource y enviamos como parte de respuesta del json para que el test lo lea correctemente 
       return jsonResponse(data:['user' => UserResource::make($user)]);
    }
}
