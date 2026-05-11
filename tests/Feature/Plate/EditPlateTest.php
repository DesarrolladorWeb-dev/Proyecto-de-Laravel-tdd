<?php

namespace Tests\Feature\Plate;

use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EditPlateTest extends TestCase
{
    use RefreshDatabase;
    protected Restaurant $restaurant;
    protected Plate $plate;

    public function test_an_authenticated_user_can_edit_a_plate(): void
    {
    //    $this->withoutExceptionHandling();//miramos exactamente los errores
        //TENIENDO
        $data = [
            'name' => 'NEW Name test' ,
            'description' => 'NEW Description test',
            'price' => 'NEW $123',
            
        ];
        //HACIENDO
        $response = 
        $this->apiAs(User::find(1), 'put', "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}", $data);


        //ESPERANDO
        $response->assertStatus(200);
        //esperamos que tenga esta estructura 
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
                        'id' => $this->plate->id,
                        'restaurant_id' => $this->restaurant->id,
                ]
            ]
        ] );

        //Missin: ya no deberia estar la version anterior como estaba antes 
        $this->assertDatabaseMissing('plates',[
            'name' => 'Name test' ,
            'description' => 'Description test',
            'price' => '$123',
            
        ]);

    }

    public function test_plate_name_is_resquired(){
        //TENIENDO
        $data = [
            'name' => '' ,
            'description' => 'New New Description test',
            'price' => 'New New $123',
            
        ];
        //HACIENDO
        $response = 
        $this->apiAs(User::find(1), 'put', "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['name']]);

    }
    public function test_plate_description_is_resquired(){
        //TENIENDO
        $data = [
            'name' => 'New Name test' ,
            'description' => '',
            'price' => 'New $123',
            
        ];
        //HACIENDO
        $response = 
        $this->apiAs(User::find(1), 'put', "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['description']]);

    }
       public function test_plate_price_is_resquired(){
        //TENIENDO
        $data = [
            'name' => 'New Name test' ,
            'description' => 'New Description test',
            'price' => '',
            
        ];
        //HACIENDO
        $response = 
        $this->apiAs(User::find(1), 'put', "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['price']]);

    }

    public function test_a_unautheticated_user_cannot_update_a_plate():void {
        $data = [
            'name' => 'NEW Name test' ,
            'description' => 'NEW Description test',
            'price' => 'NEW $123',
            
        ];
        //HACIENDO
        $response = 
        $this->putJson("{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}", $data);
           //ESPERANDO
        $response->assertStatus(401);
    }

    public function test_a_autheticated_user_can_only_update_their_plates():void {
        $data = [
            'name' => 'New Name test' ,
            'description' => 'New Description test',
            'price' => 'New $123',
            
        ];
        //creo un usuario 
        $user = User::factory()->create();
        
        //HACIENDO
        $response = 
        $this->apiAs($user,'put',"{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}", $data);
           //ESPERANDO
        //este 403 que no puede modificar nada de este restaurante (solo se queda en el Gate del Store del PlateController)
        $response->assertStatus(403);
    }


    protected function setUp(): void
    {
        parent::setUp();
       
        $this->seed(UserSeeder::class);

       
        $this->restaurant = Restaurant::factory()->create([
                'user_id' => 1
        ]);
        // vamos a generar un nuevo platillo 
        $this->plate = Plate::factory()->create([
                'restaurant_id' => 1
        ]);
    }
}
