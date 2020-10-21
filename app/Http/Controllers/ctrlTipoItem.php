<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\tipoitem;

use App\bitacora;
use DateTime;
session_start();

class ctrlTipoItem extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   if (!$request->ajax()) return redirect('/');
        $buscar = $request->buscar;
        $criterio = $request->criterio;
         
        if ($buscar==''){
            $tipoitem = tipoitem::orderBy('id', 'desc')->paginate(20);
        }
        else{
            $tipoitem = tipoitem::where($criterio, 'like', '%'. $buscar . '%')->orderBy('id', 'desc')->paginate(20);
        }
        return [
            'pagination' => [
                'total'        => $tipoitem->total(),
                'current_page' => $tipoitem->currentPage(),
                'per_page'     => $tipoitem->perPage(),
                'last_page'    => $tipoitem->lastPage(),
                'from'         => $tipoitem->firstItem(),
                'to'           => $tipoitem->lastItem(),
            ],
            'tipoitem' => $tipoitem
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
       $tipoitem= new tipoitem;
       $tipoitem->descripcion=$request->descripcion;
       $tipoitem->save();

        /*REGISTRA EL MOVIMIENTO EN LA BITACORA */
        $objdate = new DateTime();
        $fechaactual= $objdate->format('Y-m-d');
        $horaactual=$objdate->format('H:i:s');
            $bitacora = new bitacora();
            $bitacora->idEmpleado =  session('idemp');
            $bitacora->fecha = $fechaactual;
            $bitacora->hora = $horaactual;
            $bitacora->tabla = 'tipoitem';
            $bitacora->codigoTabla = $tipoitem->id;
            $bitacora->transaccion = 'crear';
            $bitacora->save();
    }
    public function actualizar(Request $request)
    {
        $tipoitem= tipoitem::findOrFail($request->id);
        $tipoitem->descripcion=$request->descripcion;
        $tipoitem->save();

        /*REGISTRA EL MOVIMIENTO EN LA BITACORA */
        $objdate = new DateTime();
        $fechaactual= $objdate->format('Y-m-d');
        $horaactual=$objdate->format('H:i:s');
            $bitacora = new bitacora();
            $bitacora->idEmpleado =  session('idemp');
            $bitacora->fecha = $fechaactual;
            $bitacora->hora = $horaactual;
            $bitacora->tabla = 'tipoitem';
            $bitacora->codigoTabla = $request->id;
            $bitacora->transaccion = 'actualizar';
            $bitacora->save();
    }
    public function eliminar($id)
    {
        $tipoitem=tipoitem::find($id);
        $tipoitem->delete();
        /*REGISTRA EL MOVIMIENTO EN LA BITACORA */
        $objdate = new DateTime();
        $fechaactual= $objdate->format('Y-m-d');
        $horaactual=$objdate->format('H:i:s');
            $bitacora = new bitacora();
            $bitacora->idEmpleado =  session('idemp');
            $bitacora->fecha = $fechaactual;
            $bitacora->hora = $horaactual;
            $bitacora->tabla = 'tipoitem';
            $bitacora->codigoTabla = $id;
            $bitacora->transaccion = 'eliminar';
            $bitacora->save();
    }

    public function selectTipoItem(Request $request){
        if (!$request->ajax()) return redirect('/');
        $tipoitem = tipoitem::where('id','>=','1')
        ->select('id','descripcion')->orderBy('descripcion', 'asc')->get();
        return ['tipoitem' => $tipoitem];
    }


 
}
