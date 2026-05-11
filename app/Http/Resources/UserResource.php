<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    //  Con el Resource al usuario le podremos agregar mas cosas o quitarle cosas 
    public function toArray(Request $request): array
    {
        // mandaremos los datos del usuario
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'last_name' => $this->last_name,
        ];
    }
}
