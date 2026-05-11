<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UpdatePasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp():void {
        parent::setUp();
        $this->seed(UserSeeder::class); //generar el seeder 
    }

    public function test_an_authenticated_user_can_update_their_password(): void
    {

        // cuando tenemos errores 500 
        // $this->withoutExceptionHandling();
    // teniendo 
    $data = [
        'old_password'      => 'password',
        'password' => 'newpassword',
        'password_confirmation' => 'newpassword',
    ];
    // haciendo
    $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/password", $data);
    #esperando
    // $response->dd();
    $response->assertStatus(200); 
    $response->assertJsonStructure(['message', 'data', 'errors', 'status']);
    $user = User::find(1);
    // que la contraseña que ponemos sea igual que lo que el usuario tiene
    $this->assertTrue(Hash::check('newpassword', $user->password)); 
    }

    public function test_old_password_must_be_validated(): void
    {

        // cuando tenemos errores 500 
        // $this->withoutExceptionHandling();
    // teniendo 
    $data = [
        'old_password'      => 'contrasenaincorrecta',
        'password' => 'newpassword',
        'password_confirmation' => 'newpassword',
    ];
    // haciendo
    $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/password", $data);
    // $response->dd();
    
    
    #esperando
    $response->assertStatus(422); 
    $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['old_password']]);
        $response->assertJsonFragment(['errors' => ['old_password' => [
            "The password does not match."
        ]]]);
    }



    public function test_old_password_must_be_required(): void
    {
        // temniendo
        $data = [
            'old_password'      => '',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];
        // haciendo
            $response = $this->apiAs(User::find(1),'put',"{$this->apiBase}/password",$data);
    
        // $response->dd();

        // Esperando
        
        $response->assertStatus(422);
        // validamos la estructura de nuestro Json usando bootstrap - app 
        $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['old_password']]);
        // esta es la respuesta que nos lanzara en el Insomnia


    }

    public function test_password_must_be_required(): void
    {
        // temniendo
        $data = [
            'old_password'      => 'password',
            'password'           => '',
            'password_confirmation' => 'newpassword',
        ];
        // haciendo
            $response = $this->apiAs(User::find(1),'put',"{$this->apiBase}/password",$data);
    
        // $response->dd();

        // Esperando
        
        $response->assertStatus(422);
        // validamos la estructura de nuestro Json usando bootstrap - app 
        $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['password']]);
        // esta es la respuesta que nos lanzara en el Insomnia


    }

        public function test_password_must_be_confirmed(): void
    {
        // temniendo
        $data = [
            'old_password'      => 'password',
            'password'           => 'newpassword',
            'password_confirmation' => '',
        ];
        // haciendo
            $response = $this->apiAs(User::find(1),'put',"{$this->apiBase}/password",$data);
    
        // $response->dd();

        // Esperando
        
        $response->assertStatus(422);
        // validamos la estructura de nuestro Json usando bootstrap - app 
        $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['password']]);
        $response->assertJsonFragment(['errors' => ['password' => [
            "The password field confirmation does not match."
        ]]]);
        // esta es la respuesta que nos lanzara en el Insomnia


    }
}
