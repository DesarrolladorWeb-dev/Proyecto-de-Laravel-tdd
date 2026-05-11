<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteRestaurantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp():void {
        parent::setUp();
        $this->seed(UserSeeder::class); //generar el seeder 
        // para que la variable sea global 
        $this->restaurant = Restaurant::factory()->create([
            'user_id' => 1, // siempre sea el numero 1  RestaurantFactory
          
        ]);
    
    }

    
    public function test_an_authenticated_user_must_delete_their_restaurants(): void
    {
          
            $response = $this->apiAs(User::find(1), 'delete', "{$this->apiBase}/restaurants/{$this->restaurant->id}");
            // contar la cantidad de elementos que quiero que se vea 
            $response->assertStatus(200);
            $response->assertJsonFragment([
                'message' => 'OK'
            ]);
            //como eliminamos no deberia haber
            $this->assertDatabaseCount('restaurants', 0);
      
    }
    
    public function test_a_unauthenticated_user_cannot_edit_a_restaurant(): void
    {

    // $this->withoutExceptionHandling();
    // teniendo 
    // haciendo
        $response = $this->deleteJson("{$this->apiBase}/restaurants/{$this->restaurant->id}");
        // $response->dd();
    // esperando 
        $response->assertStatus(401);
    
    }
        
    public function test_an_authenticated_user_must_delete_only_their_restaurants(): void
    {
    // $this->withoutExceptionHandling();

            $user = User::factory()->create();
            // el error es 403 porque el no se genero el usuario
            $response = $this->apiAs($user, 'delete', "{$this->apiBase}/restaurants/{$this->restaurant->id}");
            // contar la cantidad de elementos que quiero que se vea 
            $response->assertStatus(403);
            // data.restaurants esto es un array y deberia tener 10
    }
}
