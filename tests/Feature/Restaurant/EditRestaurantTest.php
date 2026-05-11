<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EditRestaurantTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp():void {
        parent::setUp();
        $this->seed(UserSeeder::class); //generar el seeder 
        // para que la variable sea global 
        $this->restaurant = Restaurant::factory()->create([
            'user_id' => 1, // siempre sea el numero 1  RestaurantFactory
            // de que siempre seran iguales
            'name' => 'Restaurant' , 
            'slug' => 'restaurant' , 
            'description' => 'Restaurant description',
        ]);
    
    }

    /**
     * A basic feature test example.
     */
    public function test_an_authenticated_user_can_edit_a_restaurant(): void
    {

        // cuando tenemos errores 500 
        // $this->withoutExceptionHandling();
    // teniendo 
    $data = [
        'name' => 'New restaurant' , 
        'description' => 'New restaurant description',

    ];
    // haciendo
        $response = $this->apiAs(User::find(1), 'put' ,"{$this->apiBase}/restaurants/{$this->restaurant->id}",$data);
    // dd(User::all());
    // $response->dd();
    // dd($response);

    // esperando 
        $response->assertStatus(200); //created
    //Estamos esperando esta estructura del JSON - esta bien 
        $response->assertJsonStructure(['message', 'data' => [
            'restaurant' => ['id', 'name', 'slug','description']
        ], 'errors', 'status']);
    

        //QUE halla solo un restaurante - esto es correctp
        $this->assertDatabaseCount('restaurants',1); //solo un solo registro nos aseguranmos

        // con el slug solo preguntaremos que debe contener algo asi "new-restaurant" en la tabla
        $restaurant = Restaurant::first();

        //que el slug cambie si cambiamos el nombre del restaurante
        $this->assertStringContainsString('new-restaurant' , $restaurant->slug);

        // lo se significa Missing es quiero que no encuentre esto en base de datos
        $this->assertDatabaseMissing('restaurants', [
            'name' => 'Restaurant' , 
            'description' => 'Restaurant description',
        ]);
    }

// SOLO SE PODRA CAMBIAR EL SLUG SI CAMBIAMOS EL NOMBRE
public function test_slug_must_not_change_if_the_name_is_the_same(): void
{

    // cuando tenemos errores 500 
    // $this->withoutExceptionHandling();
// teniendo 
$data = [
    'name' => 'Restaurant' , 
    'description' => 'New restaurant description',

];
// haciendo
    $response = $this->apiAs(User::find(1), 'put' ,"{$this->apiBase}/restaurants/{$this->restaurant->id}",$data);
// dd(User::all());
// $response->dd();
// dd($response);

// esperando 
    $response->assertStatus(200); //created
    $response->assertJsonStructure(['message', 'data' => [
        'restaurant' => ['id', 'name', 'slug','description']
    ], 'errors', 'status']);



    $this->assertDatabaseCount('restaurants',1); //solo un solo registro nos aseguranmos

    $restaurant = Restaurant::find(1);//buscamos el primer restaurante
    // ambos restaurantes deben ser iguales , el antiguo no debe cambiar su slug
    $this->assertTrue($restaurant->slug === $this->restaurant->slug);
    //todo esto significa que el slug no a cambiado si todo esta bien
    

    // lo se significa Missing es quiero que no encuentre esto en base de datos
    $this->assertDatabaseMissing('restaurants', [
        'name' => 'Restaurant' , 
        'description' => 'Restaurant description',
    ]);
}

public function test_a_unauthenticated_user_cannot_edit_a_restaurant(): void
{

    // cuando tenemos errores 500 
    // $this->withoutExceptionHandling();
// teniendo 
$data = [
    'name' => 'New restaurant' , 
    'description' => 'New restaurant description',

];
// haciendo
    $response = $this->putJson("{$this->apiBase}/restaurants/{$this->restaurant->id}",$data);


// esperando 
    $response->assertStatus(401); //created
 
}

// PARA QUE SOLO EDITEN LOS DUEÑOS DEL POST
public function test_a_user_should_only_update_their_restaurants(): void
{

    // cuando tenemos errores 500 
    // $this->withoutExceptionHandling();
// teniendo 
// sabemos que al crear el factori esta creando un nuevo usuario 
$restaurant = Restaurant::factory()->create();
$data = [
    'name' => 'New restaurant' , 
    'description' => 'New restaurant description',

];
// haciendo
    $response = $this->apiAs(User::find(1),'put',"{$this->apiBase}/restaurants/{$restaurant->id}",$data);
// dd(User::all());
// $response->dd();
// dd($response);

// esperando 
    $response->assertStatus(403); //created
 
}
public function test_name_must_be_required(): void
{
    $data = [
        'name' => '' , 
        'description' => 'New restaurant description',

    ];
    // haciendo
        $response = $this->apiAs(User::find(1), 'put' , "{$this->apiBase}/restaurants/{$this->restaurant->id}",$data);
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
        $response = $this->apiAs(User::find(1), 'put' , "{$this->apiBase}/restaurants/{$this->restaurant->id}",$data);
    // dd(User::all());

    // Esperando
    
    $response->assertStatus(422);
    // validamos la estructura de nuestro Json usando bootstrap - app 
    $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['description']]);
    // esta es la respuesta que nos lanzara en el Insomnia


}



}//clase
