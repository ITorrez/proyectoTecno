<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\reserva;
use App\detallereserva;
use DB;

class ctrlReserva extends Controller 
{
    
    public function index(Request $request)
    {
        // if (!$request->ajax()) return redirect ('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        if ($buscar==''){
            $reserva = reserva::join('cliente','reserva.idCliente','=', 'cliente.id')
                                    ->join('salon','reserva.idSalon','=','salon.id')
            ->select('reserva.id','reserva.fecha','reserva.fechaInicio','reserva.fechaFin','reserva.pago','cliente.nombre as nombrecli','salon.nombre as nombresalon')
            ->where('cliente.id','=', Auth()->user()->id)
            ->orderBy('reserva.id','desc')->paginate(20);
        }
        else{$reserva = reserva::join('cliente','reserva.idCliente','=', 'cliente.id')
                                     ->join('salon','reserva.idSalon','=','salon.id')
            ->select('reserva.id','reserva.fecha','reserva.fechaInicio','reserva.fechaFin','reserva.pago','cliente.nombre as nombrecli','salon.nombre as nombresalon')
            ->where('cliente.'.$criterio, 'like', '%'. $buscar . '%' ) 
            ->orderBy('reserva.id','desc')->paginate(20);
        }
        return [
            'pagination' => [
                'total'        => $reserva->total(),
                'current_page' => $reserva->currentPage(),
                'per_page'     => $reserva->perPage(),
                'last_page'    => $reserva->lastPage(),
                'from'         => $reserva->firstItem(),
                'to'           => $reserva->lastItem(),
            ],
            'reserv' =>$reserva
        ];
    }



    public function guardar(Request $request)
    {
        // if (!$request->ajax()) return redirect('/');
        DB::beginTransaction();
        try{     
        $tabla = new reserva();
        $tabla->idCliente = Auth()->user()->id;
        // $tabla->idEmpleado = $request->idEmpleado;
        $tabla->idSalon = $request->idSalon;
        $tabla->fecha = $request->fecha;
        $tabla->fechaInicio = $request->fechaInicio;
        $tabla->fechaFin = $request->fechaFin;
        $tabla->pago = $request->pago;
        $tabla->estado = $request->estado;
        $tabla->save();
        $detalles = $request->detalle;
        foreach($detalles as $det)
        {
            $detalle = new detallereserva();
            $detalle->idReserva=$tabla->id;
            $detalle->idPaqueteitem=$det['id'];
            // $detalle->cantidad=$det['cantidad'];
            // $detalle->subTotal=$det['subTotal'];
            $detalle->save();
        }
        DB::commit();
        }
        catch(Exception $ex)
        {
            DB::rollBack();
        }
    }



    public function getBy($id){
        $tabla=reserva::findOrFail($id);
 
        $tabla['precioSalon']=reserva::join('salon','reserva.idSalon','salon.id')
        
        ->where('reserva.id',$id)
        ->sum('salon.precio');
     //    ->select('salon.precio as precioSalon')
     //    ->first();
        
        $tabla['detalle']=detallereserva::join('paqueteitem as detItem',
        'detallereserva.idPaqueteitem','detItem.id')
        ->join('item','detItem.idItem','item.id')
 
     //    ->join('notaservicio','detallenotapaquete.idNotaservicio','notaservicio.id')
     //    ->join('salon','notaservicio.idSalon','salon.id')
 
         ->select('detallereserva.id','detallereserva.idPaqueteitem',
         'detItem.cantidad'
         ,'detItem.precio'
         ,'item.nombre'
 
         // ,'salon.precio as precioSalon'
 
         ,DB::raw('detItem.precio*detItem.cantidad as subTotal'))
         ->where('detallereserva.idReserva',$id)
         ->get();
 
         return $tabla;
    }
 
}
