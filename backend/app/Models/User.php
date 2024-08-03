<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;

use App\Models\GenProfesional;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // esquema de conexion
    protected $connection = 'GESTION';

    // nombre de la tabla
    protected $table = 'INTRANET.INT_USUARIOS';

    // definiendo que esta sera la clave primaria.
    protected $primaryKey = 'usuario_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
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
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // 'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getNameAttribute()
    {
        // tomara el valor de este campo.
        return $this->attributes['nombre'];
    }

    public static function logearUsuario($credentials)
    {
        // buscar si existe el correo del usuario.
        $user = User::where('login', $credentials['login'])->first();

        if ($user && $user->password == $credentials['password']) {
            // logear al usuario.
            Auth::login($user);
            // sanctum token
            $token = $user->createToken('auth_token')->plainTextToken;

            // api response user and token
            return ['user' => $user, 'token' => $token];
        } else {
            return null;
        }
    }

    // relaciones
    public function codigoProfesional()
    {
        return $this->hasOne(GenProfesional::class, 'rut_prof', 'rut')->select('rut_prof','cod_prof')->where('vigencia', 'S');
    }
}
