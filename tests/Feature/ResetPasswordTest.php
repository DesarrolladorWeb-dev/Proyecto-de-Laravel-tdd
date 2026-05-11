<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Notifications\Notification;
use Tests\TestCase;

use Illuminate\Support\Facades\Notification;

class ResetPasswordTest extends TestCase
{
       use RefreshDatabase;
        protected $token = '';
        protected $email = '';


protected function setUp():void {
    parent::setUp();
    $this->seed(UserSeeder::class); //generar el seeder 
}

/**
 *Para que PHPUnit reconozca un método como una prueba, el nombre del método debe comenzar con test o debes usar la anotación @test en el comentario del método.
 */

public function test_an_existing_user_ca_reset_their_password(): void
{

    $this->sendResetPassword();
 


    // dd($this->email); //aqui esta nuestro token y lo podremos validar con otra enpoint 
    $response = $this->putJson("{$this->apiBase}/reset-password?token={$this->token}", [
        'email' => $this->email,
        'password'  => 'newpassword' , 
        'password_confirmation' => 'newpassword'
        
    ]);
    
    $response->assertStatus(200);
    $response->assertJsonStructure(['message', 'data', 'errors', 'status']);

    $user = User::find(1);

    $this->assertTrue(Hash::check('newpassword', $user->password)); 
}

public function sendResetPassword(){
       // $this->withoutExceptionHandLing();
    // Notification es para correos emails ... 
    Notification::fake(); //no enviamos nada se quedara en un limbo la cual solo podremos testear
    // teniendo
    $data = ['email' => 'example@example.com'];
    // haciendo
    $response = $this->postJson("{$this->apiBase}/reset-password", $data);
    // $response->dump();

    // Esperando
    // dd($response->json());
    $response->assertStatus(200);
    $response->assertJsonFragment(['message' => 'OK']);
    $user = User::find(1);
    // el primer argumento es un array es un array de noticables de nuestros modelos User.php
    // tiene el modelo User el NOtifiable (tienes funciones para notificacion), el otro argumento
    // es la clase de notificacion a enviar
    // si esta asumiendo que se envio esta notificacion
    Notification::assertSentTo([$user], function (ResetPasswordNotification $notification) {
        // veremos la url que lleva el token
        // dd($notification->url); //para ver si se hizo o no la notificacion
        $url = $notification->url;
        // parseamos la url 
        $parts = parse_url($url);
        parse_str($parts['query'], $query);
        $this->token = $query['token']; //solo obtenemos el tocken
        $this->email = $query['email']; //solo obtenemos el tocken
        // dd($query);
        // solo quiero esta parte
        return str_contains($url, 'http://front.app/reset-password?token=');

    });
}


public function test_email_must_be_required(): void
{
    // teniendo
    $data = ['email' => ''];
    // haciendo
    $response = $this->postJson("{$this->apiBase}/reset-password", $data);

    // $response->dd();
    // Esperando
    
    $response->assertStatus(422);
    // validamos la estructura de nuestro Json usando bootstrap - app 
    $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['email']]);
    $response->assertJsonFragment(['errors' => ['email' => ['The email field is required.']]]);

}
public function test_email_must_be_valid_email(): void
{
    // teniendo
    $data = ['email' => 'notemail'];
    // haciendo
    $response = $this->postJson("{$this->apiBase}/reset-password", $data);

    // $response->dd();
    // Esperando
    
    $response->assertStatus(422);
    // validamos la estructura de nuestro Json usando bootstrap - app 
    $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['email']]);
    // esta es la respuesta que nos lanzara en el Insomnia
    $response->assertJsonFragment(['errors' => ['email' => ['The email field must be a valid email address.']]]);

}

public function test_email_must_be_an_existing_email(): void
{
    // teniendo
    $data = ['email' => 'notexisting@example.com'];
    // haciendo
    $response = $this->postJson("{$this->apiBase}/reset-password", $data);

    // $response->dd();
    // Esperando
    
    $response->assertStatus(422);
    // validamos la estructura de nuestro Json usando bootstrap - app 
    $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['email']]);
    // esta es la respuesta que nos lanzara en el Insomnia
    $response->assertJsonFragment(['errors' => ['email' => ['The selected email is invalid.']]]);

}


public function test_email_must_be_associated_with_the_token(): void
{
    $this->sendResetPassword();
 


    // dd($this->email); //aqui esta nuestro token y lo podremos validar con otra enpoint 
    $response = $this->putJson("{$this->apiBase}/reset-password?token={$this->token}", [
        'email' => 'fake@email.com',
        'password'  => 'newpassword' , 
        'password_confirmation' => 'newpassword'
        
    ]);
    // $response->dd();
    $response->assertStatus(500);
    // validamos la estructura de nuestro Json usando bootstrap - app 
    $response->assertJsonStructure(['message', 'data' , 'status']);


    $response->assertJsonFragment([
        'message' => "Invalid email"
     ]);
}



public function test_password_must_be_required(): void
{
    $this->sendResetPassword();
 


    // dd($this->email); //aqui esta nuestro token y lo podremos validar con otra enpoint 
    $response = $this->putJson("{$this->apiBase}/reset-password?token={$this->token}", [
        'email' => $this->email,
        'password'  => '' , 
        'password_confirmation' => 'newpassword'
        
    ]);
    $response->assertStatus(422);
    // validamos la estructura de nuestro Json usando bootstrap - app 
    $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['password']]);



}

    public function test_password_must_be_confirmed(): void
{
    $this->sendResetPassword();
 


    // dd($this->email); //aqui esta nuestro token y lo podremos validar con otra enpoint 
    $response = $this->putJson("{$this->apiBase}/reset-password?token={$this->token}", [
        'email' => $this->email,
        'password'  => 'newpassword' , 
        'password_confirmation' => ''
        
    ]);
    $response->assertStatus(422);
    // validamos la estructura de nuestro Json usando bootstrap - app 
    $response->assertJsonStructure(['message', 'data' , 'status','errors' => ['password']]);


    $response->assertJsonFragment(['errors' => ['password' => [
        "The password field confirmation does not match."
    ]]]);
    // esta es la respuesta que nos lanzara en el Insomnia


}

public function test_token_must_be_a_valid_token(): void
{
    $this->sendResetPassword();
 


    // dd($this->email); //aqui esta nuestro token y lo podremos validar con otra enpoint 
    $response = $this->putJson("{$this->apiBase}/reset-password?token={$this->token}adadadadadadad", [
        'email' => $this->email,
        'password'  => 'newpassword' , 
        'password_confirmation' => 'newpassword'
        
    ]);
    // $response->dd();
    $response->assertStatus(500);
    // validamos la estructura de nuestro Json usando bootstrap - app 
    $response->assertJsonStructure(['message', 'data' , 'status']);


    $response->assertJsonFragment([
        'message' => "Invalid token"
     ]);
    // esta es la respuesta que nos lanzara en el Insomnia


}
}
