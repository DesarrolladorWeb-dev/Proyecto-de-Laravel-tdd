<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;

class CheckPasswordRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //$value : valor de la contraseña actual 
        // $attribute : el nombre
        // que si no hace match nos dara error 
        if(!Hash::check($value, auth()->user()->password))
        {   
            // levantamos el error aqui 
            $fail("The password does not match.");

        }

    }
}
