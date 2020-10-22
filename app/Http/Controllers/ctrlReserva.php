<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\reserva;
use App\detallereserva;
use DB;

use App\detallenotapaquete;
use App\notaservicio;
use App\paqueteitem;
use App\paquete;
use App\salon;
use App\bitacora;
use DateTime;
session_start();

class ctrlReserva extends Controller 
{
             

    public function index(Request $request)
    {
        $idclientelogin=0;
               if (Auth()->user()) 
               {
                $idclientelogin=Auth()->user()->id;
               }
               else 
               {
                $idclientelogin=session('idcli');
               }
        // if (!$request->ajax()) return redirect ('/');
               
        $buscar = $request->buscar;
        $criterio = $request->criterio;
        if ($buscar==''){
            $reserva = reserva::join('cliente','reserva.idCliente','=', 'cliente.id')
                                    ->join('salon','reserva.idSalon','=','salon.id')
            ->select('reserva.id','reserva.fecha','reserva.fechaInicio','reserva.fechaFin','reserva.pago','cliente.nombre as nombrecli','salon.nombre as nombresalon','reserva.estado as estadoreser')
            ->where('cliente.id','=', $idclientelogin)
            ->where('reserva.estado','<>','anulado' )
            ->orderBy('reserva.id','desc')->paginate(20);
        }
        else{$reserva = reserva::join('cliente','reserva.idCliente','=', 'cliente.id')
                                     ->join('salon','reserva.idSalon','=','salon.id')
            ->select('reserva.id','reserva.fecha','reserva.fechaInicio','reserva.fechaFin','reserva.pago','cliente.nombre as nombrecli','salon.nombre as nombresalon','reserva.estado as estadoreser')
            ->where('cliente.'.$criterio, 'like', '%'. $buscar . '%' )
            ->where('cliente.id','=', $idclientelogin)
            ->where('reserva.estado','<>','anulado' ) 
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


    public function indextodos(Request $request)
    {
        // if (!$request->ajax()) return redirect ('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        if ($buscar==''){
            $reserva = reserva::join('cliente','reserva.idCliente','=', 'cliente.id')
                                    ->join('salon','reserva.idSalon','=','salon.id')
            ->select('reserva.id','reserva.fecha','reserva.fechaInicio','reserva.fechaFin','reserva.pago','cliente.nombre as nombrecli','salon.nombre as nombresalon','reserva.estado as estadoreser')
            ->where('reserva.estado','<>','anulado' )
            ->orderBy('reserva.id','desc')->paginate(20);
        }
        else{$reserva = reserva::join('cliente','reserva.idCliente','=', 'cliente.id')
                                     ->join('salon','reserva.idSalon','=','salon.id')
            ->select('reserva.id','reserva.fecha','reserva.fechaInicio','reserva.fechaFin','reserva.pago','cliente.nombre as nombrecli','salon.nombre as nombresalon','reserva.estado as estadoreser')
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
        $idclientelogin=0;
               if (Auth()->user()) 
               {
                $idclientelogin=Auth()->user()->id;
               }
               else 
               {
                $idclientelogin=session('idcli');
               }
        // if (!$request->ajax()) return redirect('/');
        $idpaquete=0;
        DB::beginTransaction();
        try{     
        $tabla = new reserva();
        $tabla->idCliente = $idclientelogin;
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
            /*OBTENEMOS EL ID DEL PAQUETE PARA PONERLO EN OCUUPADO */
            $obpaqueteitem=paqueteitem::findOrFail($det['id']);
            $idpaquete=$obpaqueteitem->idPaquete;
        }
        DB::commit();
        }
        catch(Exception $ex)
        {
            DB::rollBack();
        }

        /*ponemos ocupado el salon */
            $salon= salon::findOrFail($request->idSalon);
            $salon->estado='ocupado';
            $salon->save();
        /*ponemos ocupado el paquete */
            $paquete= paquete::findOrFail($idpaquete);
            $paquete->estado='ocupado';
            $paquete->save();
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

    public function anularReserva($id)
    {
        $reserva = reserva::findOrFail($id);
        $reserva->estado = 'anulado';
        $reserva->save();  

                /*REGISTRA EL MOVIMIENTO EN LA BITACORA */
                if (session('idemp')!='')
                {

                $objdate = new DateTime();
                $fechaactual= $objdate->format('Y-m-d');
                $horaactual=$objdate->format('H:i:s');
                $bitacora = new bitacora();
                $bitacora->idEmpleado =  session('idemp');
                $bitacora->fecha = $fechaactual;
                $bitacora->hora = $horaactual;
                $bitacora->tabla = 'reserva';
                $bitacora->codigoTabla = $id;
                $bitacora->transaccion = 'anular';
                $bitacora->save();
                }

                /*OBTENEMOS EL ID DEL SALON PARA ACTIVARLO NUEVAMENTE */
                $obreserva = reserva::select("id","idSalon")
                ->where("id","=",$id)
                ->get();
                $idsalon=$obreserva[0]->idSalon;
                $idreserva=$obreserva[0]->id;
                /*ponemos activo el salon */
                $salon= salon::findOrFail($idsalon);
                $salon->estado='activo';
                $salon->save();
                /*OBTENEMOS EL ID DEL PAQUETE PARA ACTIVARLO NUEVAMENTE */
                $detallereserva=detallereserva::select('id','idPaqueteitem')
                ->where("idReserva","=",$idreserva)
                ->get();
                $idpaqueteitem=$detallereserva[0]->idPaqueteitem;

                $paqueteitem=paqueteitem::select('id','idPaquete')
                ->where("id","=",$idpaqueteitem)
                ->get();
                $idpaquete=$paqueteitem[0]->idPaquete;

                /*ponemos activo el paquete */
                $paquete= paquete::findOrFail($idpaquete);
                $paquete->estado='activo';
                $paquete->save();
    }



   #se entrega la reserva al cliente 
    public function entregarReserva($id)
    {
        $reserva = reserva::findOrFail($id);
        $reserva->estado = 'entregado';
        $reserva->save();  

        /*REGISTRA EL MOVIMIENTO EN LA BITACORA */
            if (session('idemp')!='')
            {

            $objdate = new DateTime();
            $fechaactual= $objdate->format('Y-m-d');
            $horaactual=$objdate->format('H:i:s');
            $bitacora = new bitacora();
            $bitacora->idEmpleado =  session('idemp');
            $bitacora->fecha = $fechaactual;
            $bitacora->hora = $horaactual;
            $bitacora->tabla = 'reserva';
            $bitacora->codigoTabla = $id;
            $bitacora->transaccion = 'entregar';
            $bitacora->save();
            }


       /*AL ENTREGAR LA RESERVA AL CLIENTE, ESTA SE CONVIERTE EN NOTA DE SERVICIO */
       DB::beginTransaction();
        try{  
        /*insertamos la notaservicio */   
        $tabla = new notaservicio();
        $tabla->idCliente = $reserva->idCliente;
        $tabla->idEmpleado = session('idemp');
        $tabla->idSalon = $reserva->idSalon;
        $tabla->fecha = $reserva->fecha;
        $tabla->fechaInicio = $reserva->fechaInicio;
        $tabla->fechaFin = $reserva->fechaFin;
        $tabla->montoTotal = $reserva->pago;
        $tabla->estado = 'entregado';
        $tabla->save();

        /*insertamos el detallenotapaquete */
        /*obtenemos los detalles de reserva */
        $objdetallereserva = detallereserva::select("id","idReserva","idPaqueteitem")
        ->where("idReserva","=",$id)
        ->get();
        //recorremos todos los detalles de reserva
        foreach($objdetallereserva as $det)
        {
            $detalle = new detallenotapaquete();
            $detalle->idNotaservicio=$tabla->id;
            $detalle->idPaqueteitem=$det['idPaqueteitem'];
            
            $detalle->save();
            /*OBTENEMOS EL ID DEL PAQUETE PARA PONERLO EN OCUUPADO */
            // $obpaqueteitem=paqueteitem::findOrFail($det['id']);
            // $idpaquete=$obpaqueteitem->idPaquete;    
        }
        DB::commit();
        }
        catch(Exception $ex)
        {
            DB::rollBack();
        }
        /*FIN DEL CODIGO QUE INSERTA A NOTASERVICIO LA RESERVA */     
    }

    #el cliente entrega el paquete y el salon de la reserva 
    public function recibirPaqueteReserva($id)
    {
        $reserva = reserva::findOrFail($id);
        $reserva->estado = 'terminado';
        $reserva->save(); 
        
        /*REGISTRA EL MOVIMIENTO EN LA BITACORA */
        if (session('idemp')!='')
        {

        $objdate = new DateTime();
        $fechaactual= $objdate->format('Y-m-d');
        $horaactual=$objdate->format('H:i:s');
        $bitacora = new bitacora();
        $bitacora->idEmpleado =  session('idemp');
        $bitacora->fecha = $fechaactual;
        $bitacora->hora = $horaactual;
        $bitacora->tabla = 'reserva';
        $bitacora->codigoTabla = $id;
        $bitacora->transaccion = 'recibir';
        $bitacora->save();
        }

                /*OBTENEMOS EL ID DEL SALON PARA ACTIVARLO NUEVAMENTE */
                $obreserva = reserva::select("id","idSalon")
                ->where("id","=",$id)
                ->get();
                $idsalon=$obreserva[0]->idSalon;
                $idreserva=$obreserva[0]->id;
                /*ponemos activo el salon */
                $salon= salon::findOrFail($idsalon);
                $salon->estado='activo';
                $salon->save();
                /*OBTENEMOS EL ID DEL PAQUETE PARA ACTIVARLO NUEVAMENTE */
                $detallereserva=detallereserva::select('id','idPaqueteitem')
                ->where("idReserva","=",$idreserva)
                ->get();
                $idpaqueteitem=$detallereserva[0]->idPaqueteitem;

                $paqueteitem=paqueteitem::select('id','idPaquete')
                ->where("id","=",$idpaqueteitem)
                ->get();
                $idpaquete=$paqueteitem[0]->idPaquete;

                /*ponemos activo el paquete */
                $paquete= paquete::findOrFail($idpaquete);
                $paquete->estado='activo';
                $paquete->save();
    }
 
}
