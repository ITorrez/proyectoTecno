<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\paquete;
use App\paqueteitem;
use App\item;
use DB;

use App\bitacora;
use DateTime;
session_start();

class ctrlPaquete extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect ('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        if ($buscar==''){
            $paquete = paquete::join('tipopaquete','paquete.idTipoPaquete','=', 'tipopaquete.id')
            ->select('paquete.id','paquete.idTipoPaquete','tipopaquete.nombre','paquete.acontecimiento','paquete.precio',)
            ->orderBy('paquete.id','desc')->paginate(20);
        }
        else{$paquete = paquete::join('tipopaquete','paquete.idTipoPaquete','=', 'tipopaquete.id')
            ->select('paquete.id','paquete.idTipoPaquete','tipopaquete.nombre','paquete.acontecimiento','paquete.precio',)
            ->where('paquete.'.$criterio, 'like', '%'. $buscar . '%' ) 
            ->orderBy('paquete.id','desc')->paginate(20);
        }
        return [
            'pagination' => [
                'total'        => $paquete->total(),
                'current_page' => $paquete->currentPage(),
                'per_page'     => $paquete->perPage(),
                'last_page'    => $paquete->lastPage(),
                'from'         => $paquete->firstItem(),
                'to'           => $paquete->lastItem(),
            ],
            'paquete' =>$paquete
        ];
    }

   public function getBy($id){
       $tabla=paquete::findOrFail($id);

       $tabla['detalle']=paqueteitem::join('item','paqueteitem.idItem','item.id')
        ->select('paqueteitem.id','paqueteitem.idItem','item.nombre','paqueteitem.cantidad'
        ,'paqueteitem.precio')
        ->where('paqueteitem.idPaquete','=',$id)
        ->get();

        return $tabla;
   }

   public function todos(){
    //if (!$request->ajax()) return redirect('/');
    $lista = paquete::select('id','acontecimiento')
    ->where("estado","=","activo")
    ->get();
    return $lista;
    }
   public function getByDetalle($id){
    
    $tabla=paqueteitem::join('item','paqueteitem.idItem','item.id')
     ->select('paqueteitem.id','paqueteitem.idItem','item.nombre',
     'paqueteitem.cantidad','paqueteitem.precio'
     ,DB::raw('paqueteitem.cantidad*paqueteitem.precio as subTotal'))
     ->where('paqueteitem.idPaquete','=',$id)
     ->get();

     return $tabla;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
    {
        // if (!$request->ajax()) return redirect('/');
        DB::beginTransaction();
        try{     
        $paquete = new paquete();
        $paquete->idTipoPaquete = $request->idTipoPaquete;
        $paquete->acontecimiento = $request->acontecimiento;
        $paquete->precio = $request->precio;

        $paquete->estado = 'activo';

        $paquete->save();
        $detalles = $request->detalle;
        foreach($detalles as $det)
        {
            $detalle = new paqueteitem();
            $detalle->idPaquete=$paquete->id;
            $detalle->idItem=$det['idItem'];
            $detalle->cantidad=$det['cantidad'];
            $detalle->precio=$det['precio'];
            $detalle->save();
            /*ACTUALIZAR EL STOCK DEL ITEM*/
            
            $item=new item();
            $item = item::findOrFail($det['idItem']);
            $item->stock = $det['stock']-$det['cantidad'];
            $item->save();



        }
        DB::commit();
        }
        catch(Exception $ex)
        {
            DB::rollBack();
        }

         /*REGISTRA EL MOVIMIENTO EN LA BITACORA */
         $objdate = new DateTime();
         $fechaactual= $objdate->format('Y-m-d');
         $horaactual=$objdate->format('H:i:s');
            $bitacora = new bitacora();
            $bitacora->idEmpleado =  session('idemp');
            $bitacora->fecha = $fechaactual;
            $bitacora->hora = $horaactual;
            $bitacora->tabla = 'paquete';
            $bitacora->codigoTabla = $paquete->id;
            $bitacora->transaccion = 'crear';
            $bitacora->save();
    }
    public function actualizar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $paquete = paquete::findOrFail($request->id);
        $paquete->idTipoPaquete = $request->idTipoPaquete;
        $paquete->acontecimiento = $request->acontecimiento;
        $paquete->precio = $request->precio;
        $paquete->save();
    }

    public function eliminar($id)
    {
        $paqueteitem = paqueteitem::where('idPaquete','=', $id)->delete();
        // $eliminar=paqueteitem::destroy($paqueteitem);
        // $paqueteitem->delete();
        // Users:where('created_at', $date)->delete();
        // $paquete=paquete::find($id);
        $paquete=paquete::where('id',$id)->delete();
        /*REGISTRA EL MOVIMIENTO EN LA BITACORA */
        $objdate = new DateTime();
        $fechaactual= $objdate->format('Y-m-d');
        $horaactual=$objdate->format('H:i:s');
           $bitacora = new bitacora();
           $bitacora->idEmpleado =  session('idemp');
           $bitacora->fecha = $fechaactual;
           $bitacora->hora = $horaactual;
           $bitacora->tabla = 'paquete';
           $bitacora->codigoTabla = $id;
           $bitacora->transaccion = 'eliminar';
           $bitacora->save();
    }
    
}
