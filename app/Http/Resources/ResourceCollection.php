<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\ResourceCollection as Base;
use Illuminate\Http\Request;


class ResourceCollection extends Base
{
    
    public function toArray(Request $request): array
    {

        // las collecciones nos ayuda agregar a lo que esta en RestaurantResource
        return [
            static::$wrap => $this->collection,
            'total' => $this->total(),
            'count' => $this->count(),
            'per_page' => $this->perPage(),
            'current_page' => $this->currentPage(),
            'total_pages' => $this->lastPage(),


    
        ];
    }
}
