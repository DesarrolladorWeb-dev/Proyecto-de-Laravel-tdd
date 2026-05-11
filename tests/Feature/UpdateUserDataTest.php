<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UpdateUserDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp():void {
        parent::setUp();
        $this->seed(UserSeeder::class); //generar el seeder 
    }


    public function test_an_authenticated_user_can_modify_their_data(): void
    {

        // cuando tenemos errores 500 
        // $this->withoutExceptionHandling();
    // teniendo 
    $data = [
        'name'      => 'newname',
        'last_name' => 'new lastname',
    ];
    // haciendo
    $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/profile", $data);
    #esperando
    // $response->dd();
    $response->assertStatus(200); //created
    $response->assertJsonStructure(['message', 'data', 'errors', 'status']);
        $response->assertJsonFragment([
            'message' => 'OK', 'data' => [
            'user' =>  [
            'id' => 1,
            'email' => 'example@example.com' , 
            'name' => 'newname',  
            'last_name' => 'new lastname' ,
        ]], 'status' => 200]);


        // vera si en la base de datos la tabla users tiene todos estos elementos
        $this->assertDatabaseMissing('users', [
            'email'     => 'example@example.com',
            'name'      => 'User',
            'last_name' => 'Test',
        ]);
    }
    //  Para que no pueda modificar su email
    public function test_an_authenticated_user_cannnot_modify_their_email(): void
    {

        // cuando tenemos errores 500 
        // $this->withoutExceptionHandling();
    // teniendo
    $data = [
        'email' => 'newemail@example.com', //hago que falle - lo que voy a esperar es que este correo no halla cambiado
        'name'      => 'newname',
        'last_name' => 'new lastname',
    ];
    // haciendo
    $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/profile", $data);
    #esperando
    // $response->dd();
    $response->assertStatus(200); //created
    $response->assertJsonStructure(['message', 'data', 'errors', 'status']);
        $response->assertJsonFragment([
            'message' => 'OK', 'data' => [
            'user' =>  [
            'id' => 1,
            'email' => 'example@example.com' ,  //esperamos que asi nos llegue 
            'name' => 'newname',  
            'last_name' => 'new lastname' ,
        ]], 'status' => 200]);


        // vera si en la base de datos la tabla users tiene todos estos elementos
        $this->assertDatabaseHas('users', [ //y esperamos que la tabla users tenga esto
            'email'     => 'example@example.com', //pero el correo debe mantenerse igual
            'name'      => 'newname',
            'last_name' => 'new lastname',
        ]);
    }
    // Para que no pueda modificar su contraseña 
    public function test_an_authenticated_user_cannnot_modify_their_password(): void
    {

        // cuando tenemos errores 500 
        // $this->withoutExceptionHandling();
    // teniendo
    $data = [
        'password' => 'newpassword', //un password que no es 
        'name'      => 'newname',
        'last_name' => 'new lastname',
    ];
    // haciendo
    $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/profile", $data);
    #esperando
    // $response->dd();
    $response->assertStatus(200); //created
    $response->assertJsonStructure(['message', 'data', 'errors', 'status']);
    $user = User::find(1);
    // validemos que la nueva contraseña no sea la correcta 
    // lo que hace el hash check compara la cadena de texto con el $user->password
    // SOlo si no es correcta pasara el test
    $this->assertFalse(Hash::check('newpassword', $user->password)); 


     
    }


    public function test_name_must_be_required(): void
    {
        // temniendo
        $data = [
  
            'name' => '',  
            'last_name' => 'example example' ,
        ];
        // haciendo
            $response = $this->apiAs(User::find(1),'put',"{$this->apiBase}/profile",$data);
    
        // $response->dd();

        // Esperando
        
        $response->assertStatus(422);
        // validamos la estructura de nuestro Json usando bootstrap - app 
        $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['name']]);
        // esta es la respuesta que nos lanzara en el Insomnia


    }
    public function test_name_must_have_at_lease_2_characters(): void
    {
        // temniendo
        $data = [

            'name' => 'e',  
            'last_name' => 'example example' ,
        ];
        // haciendo
            $response = $this->apiAs(User::find(1),'put',"{$this->apiBase}/profile",$data);
    
        // $response->dd();

        // Esperando
        
        $response->assertStatus(422);
        // validamos la estructura de nuestro Json usando bootstrap - app 
        $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['name']]);
        // esta es la respuesta que nos lanzara en el Insomnia


    }

    public function test_last_name_must_be_required(): void
    {
        // temniendo
        $data = [

            'name' => 'example',  
            'last_name' => 'e' ,
        ];
        // haciendo
            $response = $this->apiAs(User::find(1),'put',"{$this->apiBase}/profile",$data);
    
        // $response->dd();

        // Esperando
        
        $response->assertStatus(422);
        // validamos la estructura de nuestro Json usando bootstrap - app 
        $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['last_name']]);
        // esta es la respuesta que nos lanzara en el Insomnia


    }
    public function  test_last_name_must_have_at_lease_2_characters(): void
    {
        // temniendo
        $data = [

            'name' => 'example',  
            'last_name' => 'l' ,
        ];
        // haciendo
            $response = $this->apiAs(User::find(1),'put',"{$this->apiBase}/profile",$data);
    
        // $response->dd();

        // Esperando
        
        $response->assertStatus(422);
        // validamos la estructura de nuestro Json usando bootstrap - app 
        $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['last_name']]);
        // esta es la respuesta que nos lanzara en el Insomnia


    }

}
