<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
// use UserSeeder;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp():void {
        parent::setUp();
        $this->seed(UserSeeder::class); //generar el seeder 
    }

    /**
     *Para que PHPUnit reconozca un método como una prueba, el nombre del método debe comenzar con test o debes usar la anotación @test en el comentario del método.
     */

    public function test_an_existing_user_can_login(): void
    {

        // $this->withoutExceptionHandLing();
        // teniendo
        $credentials = ['email' => 'example@example.com', 'password' => 'password'];
        // haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);
        // $response->dump();

        // Esperando
        // dd($response->json());
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['token']]);

    }
    public function test_a_non_existing_user_cannot_login(): void
    {
        // temniendo
        $credentials = ['email' => 'example@noexisting.com', 'password' => 'password'];
        // haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        // Esperando
        
        $response->assertStatus(401);
        $response->assertJsonFragment(['status' => 401 , 'message'=>'Unauthorized']);

    }

    
    public function test_email_must_be_required(): void
    {
        // temniendo
        $credentials = ['password' => 'password'];
        // haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        // $response->dd();
        // Esperando
        
        $response->assertStatus(422);
        // validamos la estructura de nuestro Json usando bootstrap - app 
        $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['email']]);
        $response->assertJsonFragment(['errors' => ['email' => ['The email field is required.']]]);

    }
    public function test_email_must_be_valid_email(): void
    {
        // temniendo
        $credentials = ['email' => 'example.com', 'password' => 'password'];
        // haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        // $response->dd();
        // Esperando
        
        $response->assertStatus(422);
        // validamos la estructura de nuestro Json usando bootstrap - app 
        $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['email']]);
        // esta es la respuesta que nos lanzara en el Insomnia
        $response->assertJsonFragment(['errors' => ['email' => ['The email field must be a valid email address.']]]);

    }
    public function test_email_must_be_a_string(): void
    {
        // temniendo
        $credentials = ['email' => 111111111 , 'password' => 'password'];
        // haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        // $response->dd();
        // Esperando
        
        $response->assertStatus(422);
        // validamos la estructura de nuestro Json usando bootstrap - app 
        $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['email']]);
        // esta es la respuesta que nos lanzara en el Insomnia
  
    }
    public function test_password_must_be_required(): void
    {
        // temniendo
        $credentials = ['email' => 'example@noexisting.com'];
        // haciendo
        $response = $this->postJson('/api/v1/login', $credentials);

        // Esperando
        
        $response->assertStatus(422);
        // validamos la estructura de nuestro Json usando bootstrap - app 
        $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['password']]);
        // esta es la respuesta que nos lanzara en el Insomnia


    }
    public function test_password_must_have_at_lease_8_character(): void
    {
        // temniendo
        $credentials = ['email' => 'example@noexisting.com', 'password' => 'abcd'];
        // haciendo
        $response = $this->postJson('/api/v1/login', $credentials);
        // $response->dd();

        // Esperando
        
        $response->assertStatus(422);
        // validamos la estructura de nuestro Json usando bootstrap - app 
        $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['password']]);
        // esta es la respuesta que nos lanzara en el Insomnia


    }
}
