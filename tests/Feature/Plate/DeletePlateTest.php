<?php

namespace Tests\Feature\Plate;

use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class deletePlateTest extends TestCase
{
    use RefreshDatabase;
    protected Restaurant $restaurant;
    protected Plate $plate;
    protected User $user;

    public function test_an_authenticated_user_can_delete_their_plates(): void
    {
        $response = $this->apiAs($this->user,'delete',"{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}");

        $response->assertStatus(200);
        
        $response->assertJsonFragment([
              'message' => 'OK',
        ]);
        $this->assertDatabaseMissing('plates' ,['id' => $this->plate->id] );
    }

    //si no sta autenticado no puede ver ningun plato
    public function test_an_unauthenticated_user_cannot_delete_any_plates(){
        $response = $this->deleteJson("{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}");

        $response->assertStatus(401);
    }

    //el usaurio no deberia eliminarlo porque no le pertenece ni el restaurante y platillo 
    public function test_an_authenticated_user_can_only_delete_their_plates(): void
    {
        $user = User::factory()->create();
        $response = $this->apiAs($user,'delete',"{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}");

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
