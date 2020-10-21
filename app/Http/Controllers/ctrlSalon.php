<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\salon;

use App\bitacora;
use DateTime;
session_start();
class ctrlSalon extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {if (!$request->ajax()) return redirect('/');
        $buscar = $request->buscar;
        $criterio = $request->criterio;
         
        if ($buscar==''){
            $salon = salon::orderBy('id', 'desc')->paginate(20);
        }
        else{
            $salon = salon::where($criterio, 'like', '%'. $buscar . '%')->orderBy('id', 'desc')->paginate(20);
        }
        return [
            'pagination' => [
                'total'        => $salon->total(),
                'current_page' => $salon->currentPage(),
                'per_page'     => $salon->perPage(),
                'last_page'    => $salon->lastPage(),
                'from'         => $salon->firstItem(),
                'to'           => $salon->lastItem(),
            ],
            'salon' => $salon
        ];
        
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
    {
        $salon= new salon;
       // $salon->foto=$request->foto;
        $salon->nombre=$request->nombre;
        $salon->descripcion=$request->descripcion;
        $salon->ubicacion=$request->ubicacion;
        $salon->estado=$request->estado;
        $salon->precio=$request->precio;

            if($request->foto==null)
            {//si no tiene imagen entonces se le asigna una imagen por defecto
                $salon->foto ='defecto.png';
            }else
            {
                    /*guardando la super imagen */
                    $explode=explode(',',$request->foto);
                    $decoded=\base64_decode($explode[1]);
                    if(str_contains($explode[0],'jpeg')){
                        $extension='jpg';
                    }else{
                        $extension='png';
                    }
                    $fileName = \Str::random().'.'.$extension;
                    $path= 'img'.'/'.$fileName;
                    \file_put_contents($path,$decoded);
                    /*terminando de guardar la superImagen */
                    $salon->foto=$fileName;   
            }


        $salon->save();

        /*REGISTRA EL MOVIMIENTO EN LA BITACORA */
        $objdate = new DateTime();
        $fechaactual= $objdate->format('Y-m-d');
        $horaactual=$objdate->format('H:i:s');
           $bitacora = new bitacora();
           $bitacora->idEmpleado =  session('idemp');
           $bitacora->fecha = $fechaactual;
           $bitacora->hora = $horaactual;
           $bitacora->tabla = 'salon';
           $bitacora->codigoTabla = $salon->id;
           $bitacora->transaccion = 'crear';
           $bitacora->save();
    }

    public function actualizar(Request $request)
    {
        $salon= salon::findOrFail($request->id);
        $salon->foto=$request->foto;
        $salon->nombre=$request->nombre;
        $salon->descripcion=$request->descripcion;
        $salon->ubicacion=$request->ubicacion;
        $salon->estado=$request->estado;
        $salon->precio=$request->precio;
        if($request->foto==null)
            {//si no tiene imagen entonces se deja como estava
               
            }else
            {
                    /*guardando la super imagen */
                    $explode=explode(',',$request->foto);
                    $decoded=\base64_decode($explode[1]);
                    if(str_contains($explode[0],'jpeg')){
                        $extension='jpg';
                    }else{
                        $extension='png';
                    }
                    $fileName = \Str::random().'.'.$extension;
                    $path= 'img'.'/'.$fileName;
                    \file_put_contents($path,$decoded);
                    /*terminando de guardar la superImagen */
                    $salon->foto=$fileName;   
            }
        $salon->save();

        /*REGISTRA EL MOVIMIENTO EN LA BITACORA */
        $objdate = new DateTime();
        $fechaactual= $objdate->format('Y-m-d');
        $horaactual=$objdate->format('H:i:s');
           $bitacora = new bitacora();
           $bitacora->idEmpleado =  session('idemp');
           $bitacora->fecha = $fechaactual;
           $bitacora->hora = $horaactual;
           $bitacora->tabla = 'salon';
           $bitacora->codigoTabla = $request->id;
           $bitacora->transaccion = 'actualizar';
           $bitacora->save();
    }
    public function eliminar($id)
    {
        $salon=salon::find($id);
        $salon->delete();

         /*REGISTRA EL MOVIMIENTO EN LA BITACORA */
         $objdate = new DateTime();
         $fechaactual= $objdate->format('Y-m-d');
         $horaactual=$objdate->format('H:i:s');
            $bitacora = new bitacora();
            $bitacora->idEmpleado =  session('idemp');
            $bitacora->fecha = $fechaactual;
            $bitacora->hora = $horaactual;
            $bitacora->tabla = 'salon';
            $bitacora->codigoTabla = $id;
            $bitacora->transaccion = 'eliminar';
            $bitacora->save();
    }
    public function todos(){
        //if (!$request->ajax()) return redirect('/');
        $salon = salon::select("id","nombre","precio")->where("estado","=","activo")->get();
        return ['data' => $salon];
    }
    public function mostrar($id){
        return ['data' => salon::findOrFail($id)];
    }
    
}
