<?php

namespace App\Http\Requests;

use App\Rules\CheckPasswordRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // ponemos la regla creada para las contraseñas antiguas no tengan escritas una contraseña antigua que no es
            'old_password'      => ['required','min:8', new CheckPasswordRule],
            'password' => 'required|min:8|confirmed',
        ];
    }
}
