<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShowRestaurantTest extends TestCase
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

    public function test_an_authenticated_user_must_see_one_of_their_restaurants(): void
    {
            
            $response = $this->apiAs(User::find(1), 'get', "{$this->apiBase}/restaurants/{$this->restaurant->id}");
            // contar la cantidad de elementos que quiero que se vea 
            $response->assertStatus(200);
            // data.restaurants esto es un array y deberia tener 10
            $response->assertJsonStructure([
                'data' => ['restaurant' => ['id', 'name', 'slug','description']],
                'message','status','errors'
            ]);
            $response->assertJsonFragment([
                'data' => ['restaurant' => [
                    'id' => $this->restaurant->id, 
                    'name' => $this->restaurant->name,
                    'description' => $this->restaurant->description, 
                    'slug' => $this->restaurant->slug
                ]],
            ]);
    }

    
    public function test_a_unauthenticated_user_cannot_see_any_restauran(): void
    {
        
        // $this->withoutExceptionHandling();
        // teniendo 
        // haciendo
        $response = $this->getJson("{$this->apiBase}/restaurants/{$this->restaurant->id}");
        // $response->dd();
        // esperando 
        $response->assertStatus(401);
        
    }
    public function _an_authenticated_user_must_see_only_their_restaurants(): void
    {
            $user = User::factory()->create();
            $response = $this->apiAs($user, 'get', "{$this->apiBase}/restaurants/{$this->restaurant->id}");
            // contar la cantidad de elementos que quiero que se vea 
            $response->assertStatus(403);
           
    }
}

