<?php

namespace Tests\Feature\Plate;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreatePlateTest extends TestCase
{
    use RefreshDatabase;
    protected Restaurant $restaurant;


    public function test_an_authenticated_user_can_create_a_plate(): void
    {
    //    $this->withoutExceptionHandling();//miramos exactamente los errores
        //TENIENDO
        $data = [
            'name' => 'Name test' ,
            'description' => 'Description test',
            'price' => '$123',
            
        ];
        //HACIENDO
        $response = 
        $this->apiAs(User::find(1), 'post', "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates", $data);

// $response->dd();

        //ESPERANDO
        $response->assertStatus(200);
        //esperamos tener una estructura de JSON

        $response->assertJsonStructure([
                'data' => ['plate' => [
                    'id','restaurant_id','name', 'description','price'
                ]],
                'message','status','errors'
        ]);
        //tambien quiero que la parte de data del Json - hague match con esto 
        $response->assertJsonFragment(['data'=> [
            
                'plate' => [
                        ...$data,
                        'id' => 1,
                        'restaurant_id' => $this->restaurant->id,
                ]
            ]
        ] );

        //Obligamos que esta informacion($data) esta en base de datos 
        $this->assertDatabaseHas('plates',$data);

    }
    //esto es para la validacion de campo name

    public function test_plate_name_is_resquired(){
        //TENIENDO
        $data = [
            'name' => '' ,
            'description' => 'Description test',
            'price' => '$123',
            
        ];
        //HACIENDO
        $response = 
        $this->apiAs(User::find(1), 'post', "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['name']]);

    }
        //esto es para la validacion de campo description 
    public function test_plate_description_is_resquired(){
        //TENIENDO
        $data = [
            'name' => 'Name test' ,
            'description' => '',
            'price' => '$123',
            
        ];
        //HACIENDO
        $response = 
        $this->apiAs(User::find(1), 'post', "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['description']]);

    }
        //esto es para la validacion de campo price
       public function test_plate_price_is_resquired(){
        //TENIENDO
        $data = [
            'name' => 'Name test' ,
            'description' => 'Description test',
            'price' => '',
            
        ];
        //HACIENDO
        $response = 
        $this->apiAs(User::find(1), 'post', "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['price']]);

    }

    //Que un usuario que no este autenticado no pueda generar nada 
    public function test_a_unautheticated_user_cannot_update_a_plate():void {
        $data = [
            'name' => 'Name test' ,
            'description' => 'Description test',
            'price' => '$123',
            
        ];
        //HACIENDO
        $response = 
        $this->postJson("{$this->apiBase}/restaurants/{$this->restaurant->id}/plates", $data);
           //ESPERANDO
        $response->assertStatus(401);
    }
//Que si este logueaado pero que no sea dueño del post del platillo que no pueda modificarlo
    public function test_a_unautheticated_user_cannot_update_a_plate():void {
        $data = [
            'name' => 'Name test' ,
            'description' => 'Description test',
            'price' => '$123',
            
        ];
        //HACIENDO
        $response = 
        $this->postJson("{$this->apiBase}/restaurants/{$this->restaurant->id}/plates", $data);
           //ESPERANDO
        $response->assertStatus(401);
    }


    protected function setUp(): void
    {
        parent::setUp();
        // necesitamos el usuario para crear el restaurante 
        $this->seed(UserSeeder::class);

        // necesitamso un usuario para poder generar nuestro restaurante 
        // que los platillos pertenescan a un restaurante
        $this->restaurant = Restaurant::factory()->create([
                'user_id' => 1
        ]);
    }
}
