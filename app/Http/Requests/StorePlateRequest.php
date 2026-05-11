<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlateRequest extends FormRequest
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
    { //las validaciones que van a ser requeridos 
        return [
            'name' => 'required',
            'price' => 'required',//$123,100 pesos (es un string , no va tener tantaas validaciones )
            'description' => 'required'
        ];
    }
}
