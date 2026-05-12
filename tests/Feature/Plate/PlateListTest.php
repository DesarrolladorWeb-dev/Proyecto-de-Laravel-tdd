<?php

namespace Tests\Feature\Plate;

use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PlateListTest extends TestCase
{
    use RefreshDatabase;
    protected $plates;
    protected $restaurant;


    public function test_an_authenticated_user_must_see_their_plates(): void
    {
        // para ver el handlin de los errores - se puede saber mas el porque del errir 
            // $this->withExceptionHandling();
            $response = $this->apiAs(User::find(1), 'get', "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates");
            // $response->dd(); //vemos que me trae (created-at,id,restauran , updated etc)
            // contar la cantidad de elementos que quiero que se vea 
            $response->assertStatus(200);
            // data.plate esto es un array y deberia tener 15
            $response->assertJsonCount(15,'data.plates');
    
            $response->assertJsonStructure([
                'data'=>[
                    'plates'=> [
                        '*'=>['id','restaurant_id','name','description','price']
                    ],
                ]
            ]);
            // creamos el foreach para pasar por todos 
            foreach (range(0,14) as $platePosition) {
                //verificar que restaurant_id tega este valor restaurant_id
                $response->assertJsonPath("data.plates.{$platePosition}.restaurant_id",$this->restaurant->id);
            }

    } public function test_a_unauthenticated_user_cannot_see_the_list_of_plates(): void
    {

    // $this->withoutExceptionHandling();
    // teniendo 
    // haciendo
        $response = $this->getJson("{$this->apiBase}/restaurants/{$this->restaurant->id}/plates");
        // $response->dd();
    // esperando 
        $response->assertStatus(401);
    
    }
// Para que pueda ver solo el plato que le corresponda solo de el 
      public function test_an_authenticated_user_must_see_only_their_plates(): void
    {
            $user = User::factory()->create();
            $response = $this->apiAs($user, 'get', "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates");
            // contar la cantidad de elementos que quiero que se vea 
            $response->assertStatus(403);
        
    }

     public function test_a_user_must_see_their_paginated_plates(): void
    {
            // $this->withoutExceptionHandling();
            $response = $this->apiAs(User::find(1), 'get', "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates");
            // $response->dd();
            // contar la cantidad de elementos que quiero que se vea 
            $response->assertStatus(200);
            $response->assertJsonCount(15,'data.plates');
            $response->assertJsonStructure([
                'data' => [
                    'plates',
                    'total',
                    'current_page',
                    'per_page',
                    'total_pages',
                    'count'    
                ],
                'message', 'status','errors'
            ]);

            $response->assertJsonPath('data.total',15);
            $response->assertJsonPath('data.current_page',1);
            $response->assertJsonPath('data.per_page',15);
            $response->assertJsonPath('data.total_pages',1);
            $response->assertJsonPath('data.count',15);
    }



    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->restaurant = Restaurant::factory()->create(
            [
                'user_id' => 1

            ]
        );
        $this->plates = Plate::factory()->count(15)->create([
            // aqui automaticamente solo agarra el id
            'restaurant_id' => $this->restaurant
        ]);
    }

}
