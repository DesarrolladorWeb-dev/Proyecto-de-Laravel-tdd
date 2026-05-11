<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateRestaurantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp():void {
        parent::setUp();
        $this->seed(UserSeeder::class); //generar el seeder 
    }
    /**
     * A basic feature test example.
     */
    public function test_a_user_can_create_a_restaurant(): void
    {

        // cuando tenemos errores 500 
        //$this->withoutExceptionHandling();
    // teniendo 
    $data = [
        'name' => 'New restaurant' , 
        'description' => 'New restaurant description',

    ];
    // haciendo
        $response = $this->apiAs(User::find(1), 'post' , "{$this->apiBase}/restaurants",$data);
    // dd(User::all());
    // $response->dd();
    // dd($response);

    // esperando 
        $response->assertStatus(200); //created
        $response->assertJsonStructure(['message', 'data' => [
            'restaurant' => ['id', 'name', 'slug','description']
        ], 'errors', 'status']);
    


        $this->assertDatabaseCount('restaurants',1); //solo un solo registro nos aseguranmos

        // con el slug solo preguntaremos que debe contener algo asi "new-restaurant" en la tabla
        $restaurant = Restaurant::first();
        $this->assertStringContainsString('new-restaurant' , $restaurant->slug);
        $this->assertDatabaseHas('restaurants', [
           'id' => 1,
           'user_id' => 1,
            'name' => 'New restaurant' , 
            'description' => 'New restaurant description',
        ]);
    }


public function test_name_must_be_required(): void
{
    $data = [
        'name' => '' , 
        'description' => 'New restaurant description',

    ];
    // haciendo
        $response = $this->apiAs(User::find(1), 'post' , "{$this->apiBase}/restaurants",$data);
    // dd(User::all());

    // Esperando
    
    $response->assertStatus(422);
    // validamos la estructura de nuestro Json usando bootstrap - app 
    $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['name']]);
    // esta es la respuesta que nos lanzara en el Insomnia

}

public function test_description_must_be_required(): void
{
    $data = [
        'name' => 'New restaurant' , 
        'description' => '',

    ];
    // haciendo
        $response = $this->apiAs(User::find(1), 'post' , "{$this->apiBase}/restaurants",$data);
    // dd(User::all());

    // Esperando
    
    $response->assertStatus(422);
    // validamos la estructura de nuestro Json usando bootstrap - app 
    $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['description']]);
    // esta es la respuesta que nos lanzara en el Insomnia


}

}//clase
