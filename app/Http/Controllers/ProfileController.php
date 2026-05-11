<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function update(UpdateUserRequest $request)
    {
        // usar el $request->all() me trae todos los datos de la peticion UpdateUserRequest
        // except email es para que el email en el test no tome en cuenta el email al momento de compara en el test
        // validate nos traera todo los que esta aqui en la funcion de rule  dentro del file UpdateUserRequest - y no nos dara la pasword com vemos 
        auth()->user()->update($request->validated());
        // para que me traigue los datos mas actualizados
        // usamos el resorce 
        $user = UserResource::make(auth()->user()->fresh());
        return jsonResponse(compact('user'));
    }
}
