<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\tipopaquete;

use App\bitacora;
use DateTime;
session_start();

class ctrlTipoPaquete extends Controller
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
            $tipopaquete = tipopaquete::orderBy('id', 'desc')->paginate(20);
        }
        else{
            $tipopaquete = tipopaquete::where($criterio, 'like', '%'. $buscar . '%')->orderBy('id', 'desc')->paginate(20);
        }
        return [
            'pagination' => [
                'total'        => $tipopaquete->total(),
                'current_page' => $tipopaquete->currentPage(),
                'per_page'     => $tipopaquete->perPage(),
                'last_page'    => $tipopaquete->lastPage(),
                'from'         => $tipopaquete->firstItem(),
                'to'           => $tipopaquete->lastItem(),
            ],
            'tipopaquete' => $tipopaquete
        ];
        
    }

   
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function todos(){
        //if (!$request->ajax()) return redirect('/');
        $tipopaquete = tipopaquete::select("id","nombre")->get();
        return ['data' => $tipopaquete];
    }

    public function guardar(Request $request)
    {
        $tipopaquete= new tipopaquete;
        $tipopaquete->nombre=$request->nombre;
        $tipopaquete->descripcion=$request->descripcion;
        $tipopaquete->save();

        /*REGISTRA EL MOVIMIENTO EN LA BITACORA */
        $objdate = new DateTime();
        $fechaactual= $objdate->format('Y-m-d');
        $horaactual=$objdate->format('H:i:s');
           $bitacora = new bitacora();
           $bitacora->idEmpleado =  session('idemp');
           $bitacora->fecha = $fechaactual;
           $bitacora->hora = $horaactual;
           $bitacora->tabla = 'tipopaquete';
           $bitacora->codigoTabla = $tipopaquete->id;
           $bitacora->transaccion = 'crear';
           $bitacora->save();
    }
    
    public function actualizar(Request $request)
    {
        $tipopaquete= tipopaquete::findOrFail($request->id);
        $tipopaquete->nombre=$request->nombre;
        $tipopaquete->descripcion=$request->descripcion;
        $tipopaquete->save();

         /*REGISTRA EL MOVIMIENTO EN LA BITACORA */
         $objdate = new DateTime();
         $fechaactual= $objdate->format('Y-m-d');
         $horaactual=$objdate->format('H:i:s');
            $bitacora = new bitacora();
            $bitacora->idEmpleado =  session('idemp');
            $bitacora->fecha = $fechaactual;
            $bitacora->hora = $horaactual;
            $bitacora->tabla = 'tipopaquete';
            $bitacora->codigoTabla = $request->id;
            $bitacora->transaccion = 'actualizar';
            $bitacora->save();
    }
    public function eliminar($id)
    {
        $tipopaquete=tipopaquete::find($id);
        $tipopaquete->delete();

        /*REGISTRA EL MOVIMIENTO EN LA BITACORA */
        $objdate = new DateTime();
        $fechaactual= $objdate->format('Y-m-d');
        $horaactual=$objdate->format('H:i:s');
           $bitacora = new bitacora();
           $bitacora->idEmpleado =  session('idemp');
           $bitacora->fecha = $fechaactual;
           $bitacora->hora = $horaactual;
           $bitacora->tabla = 'tipopaquete';
           $bitacora->codigoTabla = $id;
           $bitacora->transaccion = 'eliminar';
           $bitacora->save();
    }

 }

    
