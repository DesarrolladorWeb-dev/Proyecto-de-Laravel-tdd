<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserRegisterTest extends TestCase
{
    use RefreshDatabase; //se encarba de que cada prueba se vacie nuestra base de datos


    /**
     * A basic feature test example.
     */
    public function test_a_user_can_register(): void
    {

        // cuando tenemos errores 500 
        //$this->withoutExceptionHandling();
    // teniendo 
    $data = [
        'email' => 'email@email.com' , 
        'password' => 'password',
        'name' => 'example',  
        'last_name' => 'example example' ,
    ];
    // haciendo
        $response = $this->postJson("{$this->apiBase}/users",$data);
    // dd(User::all());
    // $response->dd();

    // esperando 
        $response->assertStatus(200); //created
        $response->assertJsonStructure(['message', 'data', 'errors', 'status']);
        $response->assertJsonFragment([
            'message' => 'OK', 'data' => [
            'user' =>  [
            'id' => 1,
            'email' => 'email@email.com' , 
            'name' => 'example',  
            'last_name' => 'example example',
        ]], 'status' => 200]);


        $this->assertDatabaseCount('users',1); //solo un solo registro nos aseguranmos
        // vera si en la base de datos la tabla users tiene todos estos elementos
        $this->assertDatabaseHas('users', [
            'email' => 'email@email.com' , 
            'name' => 'example',  
            'last_name' => 'example example' ,
        ]);
    }
    public function  test_a_registered_user_can_login(): void
    {
        // temniendo
        $data = [
            'email' => 'email@email.com' , 
            'password' => 'password',
            'name' => 'example',  
            'last_name' => 'example example' ,
        ];
        // haciendo
            $this->postJson("{$this->apiBase}/users",$data);
            $response = $this->postJson("{$this->apiBase}/login",[ 'email' => 'email@email.com' , 
            'password' => 'password',]);
        // Esperando
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['token']]);

        



    }

    


    public function test_email_must_be_required(): void
    {
        //$this->withoutExceptionHandling();

        // temniendo
        $data = [
            'email' => '' , 
            'password' => 'password',
            'name' => 'example',  
            'last_name' => 'example example' ,
        ];
        // haciendo
        $response = $this->postJson("{$this->apiBase}/users",$data);

        // $response->dd();
        // Esperando
        
        $response->assertStatus(422);
        // validamos la estructura de nuestro Json usando bootstrap - app 
        $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['email']]);
        $response->assertJsonFragment(['errors' => ['email' => ['The email field is required.']]]);

    }
    public function test_email_must_be_valid_email(): void
    {
        // cuando tenemos errores 500 
        //$this->withoutExceptionHandling();
    // teniendo 
    $data = [
        'email' => 'ddddddd' , 
        'password' => 'password',
        'name' => 'example',  
        'last_name' => 'example example' ,
    ];
    // haciendo
        $response = $this->postJson("{$this->apiBase}/users",$data);

        // $response->dd();
        // Esperando
        
        $response->assertStatus(422);
        // validamos la estructura de nuestro Json usando bootstrap - app 
        $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['email']]);
        // esta es la respuesta que nos lanzara en el Insomnia
        $response->assertJsonFragment(['errors' => ['email' => ['The email field must be a valid email address.']]]);

    }

    public function test_email_must_be_unique(): void
    {
        // generamos el usuario en base de datos y un email existente en base dedatos
        // para que aparesca el error 
            User::factory()->create(['email' => 'email@email.com']);
        //$this->withoutExceptionHandling();
    // teniendo 
    $data = [
        'email' => 'email@email.com' , 
        'password' => 'password',
        'name' => 'example',  
        'last_name' => 'example example' ,
    ];
    // haciendo
        $response = $this->postJson("{$this->apiBase}/users",$data);

        // $response->dd();
        // Esperando
        
        $response->assertStatus(422);
        // validamos la estructura de nuestro Json usando bootstrap - app 
        $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['email']]);
        // esta es la respuesta que nos lanzara en el Insomnia
        $response->assertJsonFragment(['errors' => ['email' => ['The email has already been taken.']]]);

    }


    public function test_password_must_be_required(): void
    {
        //$this->withoutExceptionHandling();

        // temniendo
        $data = [
            'email' => 'email@email.com' , 
            'password' => '',
            'name' => 'example',  
            'last_name' => 'example example' ,
        ];
        // haciendo
            $response = $this->postJson("{$this->apiBase}/users",$data);
    
        // Esperando
        
        $response->assertStatus(422);
        // validamos la estructura de nuestro Json usando bootstrap - app 
        $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['password']]);
        // esta es la respuesta que nos lanzara en el Insomnia


    }
    public function test_password_must_have_at_lease_8_character(): void
    {
        // temniendo
        $data = [
            'email' => 'email@email.com' , 
            'password' => 'abcd',
            'name' => 'example',  
            'last_name' => 'example example' ,
        ];
        // haciendo
            $response = $this->postJson("{$this->apiBase}/users",$data);
    
        // $response->dd();

        // Esperando
        
        $response->assertStatus(422);
        // validamos la estructura de nuestro Json usando bootstrap - app 
        $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['password']]);
        // esta es la respuesta que nos lanzara en el Insomnia


    }
    public function test_name_must_be_required(): void
    {
        // temniendo
        $data = [
            'email' => 'email@email.com' , 
            'password' => 'password',
            'name' => '',  
            'last_name' => 'example example' ,
        ];
        // haciendo
            $response = $this->postJson("{$this->apiBase}/users",$data);
    
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
            'email' => 'email@email.com' , 
            'password' => 'password',
            'name' => 'e',  
            'last_name' => 'example example' ,
        ];
        // haciendo
            $response = $this->postJson("{$this->apiBase}/users",$data);
    
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
            'email' => 'email@email.com' , 
            'password' => 'password',
            'name' => 'example',  
            'last_name' => 'e' ,
        ];
        // haciendo
            $response = $this->postJson("{$this->apiBase}/users",$data);
    
        // $response->dd();

        // Esperando
        
        $response->assertStatus(422);
        // validamos la estructura de nuestro Json usando bootstrap - app 
        $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['last_name']]);
        // esta es la respuesta que nos lanzara en el Insomnia


    }
   
    //COPIA -----------------
    public function  test_last_name_must_have_at_lease_2_characters(): void
    {
        // temniendo
        $data = [
            'email' => 'email@email.com' , 
            'password' => 'password',
            'name' => 'example',  
            'last_name' => 'l' ,
        ];
        // haciendo
            $response = $this->postJson("{$this->apiBase}/users",$data);
    
        // $response->dd();

        // Esperando
        
        $response->assertStatus(422);
        // validamos la estructura de nuestro Json usando bootstrap - app 
        $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['last_name']]);
        // esta es la respuesta que nos lanzara en el Insomnia


    }
    //---------------------


}
