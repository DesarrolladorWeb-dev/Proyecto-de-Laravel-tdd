<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaginateRestaurantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp():void {
        parent::setUp();
        $this->seed(UserSeeder::class); //generar el seeder 
        // para que la variable sea global 
        $this->restaurants = Restaurant::factory()->count(150)->create([
            'user_id' => 1, // siempre sea el numero 1  RestaurantFactory
          
        ]);
    
    }

    public function test_a_user_must_see_their_restaurants(): void
    {
    $this->withoutExceptionHandling();

            
            $response = $this->apiAs(User::find(1), 'get', "{$this->apiBase}/restaurants");
            // $response->dd();
            // contar la cantidad de elementos que quiero que se vea 
            $response->assertStatus(200);
            $response->assertJsonCount(15,'data.restaurants');
            $response->assertJsonStructure([
                'data' => [
                    'restaurants',
                    'total',
                    'current_page',
                    'per_page',
                    'total_pages',
                    'count'    
                ],
                'message', 'status','errors'
            ]);

            $response->assertJsonPath('data.total',150);
            $response->assertJsonPath('data.current_page',1);
            $response->assertJsonPath('data.per_page',15);
            $response->assertJsonPath('data.total_pages',10);
            $response->assertJsonPath('data.count',15);
    }

}
