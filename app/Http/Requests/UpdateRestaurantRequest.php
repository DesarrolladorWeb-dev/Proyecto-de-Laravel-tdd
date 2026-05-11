<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRestaurantRequest extends FormRequest
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
            'name' => 'required',
            // de esta manera ignora el mismo que estamos actualizando 
            // de esta manera ignora el restaurante que estoy actualizando  en este momento $this->restaurant->id 
            'slug' => 'required | unique:restaurants,slug'.$this->restaurant->id ,
            'description' => 'required',
        ];
    }

    protected function prepareForValidation() {

        // igualamo nuestro slug al estado inicial
        $slug = $this->restaurant->slug;
        //solo cuando cambia el nombre se va a regenerar el slug
        if($this->get('name') !== $this->restaurant->name){
            // de esta el slug va a ser unico - lo crea
           $slug = str( $this->get('name').' '.uniqid())->slug();
       
        }
        $this->merge([
            'slug' => $slug
        ]);
    }
}