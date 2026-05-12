<?php

namespace App\Http\Controllers;

use App\Models\Plate;
use App\Http\Requests\StorePlateRequest;
use App\Http\Requests\UpdatePlateRequest;
use App\Http\Resources\PlateCollection;
use App\Http\Resources\PlateResource;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Gate;

class PlateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Restaurant $restaurant)
    {
        Gate::authorize('viewPlates',$restaurant);
        // dd($restaurant); //vemos que esta vacio lo que contiene restaurnat porque no se lo estamos mandando desde api.php 
        $plates = $restaurant->plates()->paginate();
        return jsonResponse(  new PlateCollection($plates));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePlateRequest $request,Restaurant $restaurant)
    {
        // solo el dueño de este restaurante puede crear un platillo
        Gate::authorize('view',$restaurant);

        //la creacion de nuestro platillo 
        $plate = $restaurant->plates()->create($request->validated());

        // Aqui si se le pasa el array y no una colecccion 
        return jsonResponse(['plate' =>  PlateResource::make($plate)]);
    }

    /**
     * Display the specified resource.
     */
    public function show( Restaurant $restaurant, Plate $plate)
    {
        Gate::authorize('view',$restaurant);
        return jsonResponse(['plate' =>  PlateResource::make($plate)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlateRequest $request, Restaurant $restaurant, Plate $plate)
    {
         // solo el dueño de este restaurante puede crear un platillo
        Gate::authorize('view',$restaurant);

        //la actualizacion del platillo - (este update regrsa un booleano - por eso usamos el fresh)
        $plate->update($request->validated());

        // Aqui si se le pasa el array y no una colecccion - Realizamos un fresh (entra a la base de datos y me trae lo ultimo)
        return jsonResponse(['plate' =>  PlateResource::make($plate->fresh())]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant, Plate $plate)
    {
        Gate::authorize('view',$restaurant);

        $plate->delete();
        return jsonResponse(['plate' => PlateResource::make($plate)]);
    }
}
