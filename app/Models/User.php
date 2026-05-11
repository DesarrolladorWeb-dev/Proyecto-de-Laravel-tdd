<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Tymon\JWTAuth\Contracts\JWTSubject;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'last_name',
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        // 'created_at',
        // 'updated_at',
        // 'email_verified_at'
    ];

    public function restaurants() {
        return $this->hasMany(Restaurant::class);
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // funcion para las notificacionesque tiene que llamarse asi
    public function sendPasswordResetNotification($token)
    {
        // enviamos la noti a este dominio
        $url = "http://front.app/reset-password?token=".$token . '&email='. $this->email;
        $this->notify(new ResetPasswordNotification($url));
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }


    public function getJWTCustomClaims()
    {
        // para que aparesca en el JWT
        return  ['mensaje' => 'hola mundo'];
    }
}
