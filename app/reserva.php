<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class reserva extends Model
{
    protected $table = "reserva";
    protected $fillable =[
        'idCliente',
        'idSalon',
        
        'fecha',
        'pago',
        'estado',
        'fechaInicio',
        'fechaFin'
        
    ];
    public $timestamps=false;
    public function cliente(){
        return $this->belongsTo('App\cliente');
     } 

    public function salon(){
        return $this->belongsTo('App\salon');
     } 
 
    public function paqueteitem(){
        return $this->belongsTo('App\paqueteitem');
     } 
}
