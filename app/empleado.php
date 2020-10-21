<?php

namespace App;

//use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class empleado extends Authenticatable
{
    use Notifiable;
    protected $table = "empleado";
    protected $fillable = [
        'nombre',
        'apellido',
        'ci',
        'telefono',
        'usuario',
        'password'
    ];
    public $timestamps=false;
    public function bitacora(){
        return $this->hasMany('App\bitacora');
    }

    public function notaservicio(){
        return $this->hasMany('App\notaservicio');
    }
}
