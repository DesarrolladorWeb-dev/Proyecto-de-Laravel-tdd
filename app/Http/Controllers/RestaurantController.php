<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantRequest;
use App\Http\Resources\RestaurantCollection;
use App\Http\Resources\RestaurantResource;
use Illuminate\Support\Facades\Gate;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $restaurants = auth()->user()->restaurants()->paginate();
     //para que me  la respuesta como JSON
        return jsonResponse( new RestaurantCollection($restaurants));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRestaurantRequest $request)
    {
        // para decirle que usuario lo creo 
        // dd($request);
        $restaurant = auth()->user()->restaurants()->create($request->validated());
        // dd($restaurant);
        return jsonResponse(data:[
            'restaurant' => RestaurantResource::make($restaurant),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Restaurant $restaurant)
    {
        Gate::authorize('view', $restaurant);

        return jsonResponse([
            'restaurant' =>  RestaurantResource::make($restaurant),
        ]);
    
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRestaurantRequest $request, Restaurant $restaurant)
    {
        // para que se actualize solo si le pertenece el post 
        // Gate esta en RestaurantPolicy.php 
        Gate::authorize('update', $restaurant);
        $restaurant->update($request->validated());
        return jsonResponse(data:[
            'restaurant' => RestaurantResource::make($restaurant),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant)
    {
    Gate::authorize('delete',$restaurant);
    // Haremos la parte de la eliminacion 
    $restaurant->delete();
    return jsonResponse();
    }
}
