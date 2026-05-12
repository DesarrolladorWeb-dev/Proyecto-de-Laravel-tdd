<?php

namespace Tests\Feature\Plate;

use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class showPlateTest extends TestCase
{
    use RefreshDatabase;
    protected Restaurant $restaurant;
    protected Plate $plate;
    protected User $user;

    public function test_an_authenticated_user_can_see_their_plates(): void
    {
        $response = $this->apiAs($this->user,'get',"{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => ['plate' => [
                'id',
                'restaurant_id',
                'name',
                'description',
                'price'
            ]],
            'message','status','errors'
        ]);
        $response->assertJsonFragment([
              'data' => ['plate' => [
                'id' => $this->plate->id,
                'restaurant_id' => $this->plate->restaurant_id,
                'name' => $this->plate->name,
                'description' => $this->plate->description,
                // realizamos un casting para que me combierta a string
                'price'=> (string)$this->plate->price,

            ]],
        ]);
    }

    //si no sta autenticado no puede ver ningun plato
    public function test_an_unauthenticated_user_cannot_see_any_plates(){
        $response = $this->getJson("{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}");

        $response->assertStatus(401);
    }

    //aunque este logueado no debe haber platillos no relacionados a el 
    public function test_an_authenticated_user_can_only_see_their_plates(): void
    {
        $user = User::factory()->create();
        $response = $this->apiAs($user,'get',"{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}");

        $response->assertStatus(403);

    }

    protected function setUp():void {
        parent::setUp();
        //Primero generamos un usuario usando el factory
        $this->user = User::factory()->create();
        //cree un restaurante con el usuario que acabamos de crear
        $this->restaurant = Restaurant::factory()->create(['user_id' => $this->user->id]);
        $this->plate =Plate::factory()->create(['restaurant_id' => $this->restaurant->id]);

    }   
}
