<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\notaservicio;
use App\detallenotapaquete;
use DB;

use App\paqueteitem;
use App\paquete;
use App\salon;
use App\bitacora;
use DateTime;
session_start();

class ctrlNotaServicio extends Controller
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
            $notaserv = notaservicio::join('cliente','notaservicio.idCliente','=', 'cliente.id')
                                    ->join('empleado','notaservicio.idEmpleado','=','empleado.id')
                                    ->join('salon','notaservicio.idSalon','=','salon.id')
            ->select('notaservicio.id','notaservicio.fecha','notaservicio.fechaInicio','notaservicio.fechaFin','notaservicio.montoTotal','cliente.nombre as nombrecli','empleado.nombre as nombreemp','salon.nombre as nombresalon','notaservicio.estado as estadoserv')
            ->orderBy('notaservicio.id','desc')->paginate(20);
        }
        else{$notaserv = notaservicio::join('cliente','notaservicio.idCliente','=', 'cliente.id')
                                     ->join('empleado','notaservicio.idEmpleado','=','empleado.id')
                                     ->join('salon','notaservicio.idSalon','=','salon.id')
            ->select('notaservicio.id','notaservicio.fecha','notaservicio.fechaInicio','notaservicio.fechaFin','notaservicio.montoTotal','cliente.nombre as nombrecli','empleado.nombre as nombreemp','salon.nombre as nombresalon','notaservicio.estado as estadoserv')
            ->where('cliente.'.$criterio, 'like', '%'. $buscar . '%' ) 
            ->orderBy('notaservicio.id','desc')->paginate(20);
        }
        return [
            'pagination' => [
                'total'        => $notaserv->total(),
                'current_page' => $notaserv->currentPage(),
                'per_page'     => $notaserv->perPage(),
                'last_page'    => $notaserv->lastPage(),
                'from'         => $notaserv->firstItem(),
                'to'           => $notaserv->lastItem(),
            ],
            'notaserv' =>$notaserv
        ];
    }

   public function getBy($id){
       $tabla=notaservicio::findOrFail($id);

       $tabla['precioSalon']=notaservicio::join('salon','notaservicio.idSalon','salon.id')
       
       ->where('notaservicio.id',$id)
       ->sum('salon.precio');
    //    ->select('salon.precio as precioSalon')
    //    ->first();
       
       $tabla['detalle']=detallenotapaquete::join('paqueteitem as detItem',
       'detallenotapaquetes.idPaqueteitem','detItem.id')
       ->join('item','detItem.idItem','item.id')

    //    ->join('notaservicio','detallenotapaquete.idNotaservicio','notaservicio.id')
    //    ->join('salon','notaservicio.idSalon','salon.id')

        ->select('detallenotapaquetes.id','detallenotapaquetes.idPaqueteitem',
        'detItem.cantidad'
        ,'detItem.precio'
        ,'item.nombre'

        // ,'salon.precio as precioSalon'

        ,DB::raw('detItem.precio*detItem.cantidad as subTotal'))
        ->where('detallenotapaquetes.idNotaservicio',$id)
        ->get();

        return $tabla;
   }

   public function todos(){
    //if (!$request->ajax()) return redirect('/');
    $lista = paquete::select('id','acontecimiento')
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
        $idpaquete=0;
        DB::beginTransaction();
        try{     
        $tabla = new notaservicio();
        $tabla->idCliente = $request->idCliente;
        $tabla->idEmpleado = session('idemp');
        $tabla->idSalon = $request->idSalon;
        $tabla->fecha = $request->fecha;
        $tabla->fechaInicio = $request->fechaInicio;
        $tabla->fechaFin = $request->fechaFin;
        $tabla->montoTotal = $request->montoTotal;
        $tabla->estado = $request->estado;
        $tabla->save();
        $detalles = $request->detalle;
        foreach($detalles as $det)
        {
            $detalle = new detallenotapaquete();
            $detalle->idNotaservicio=$tabla->id;
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


                /*REGISTRA EL MOVIMIENTO EN LA BITACORA */
                $objdate = new DateTime();
                $fechaactual= $objdate->format('Y-m-d');
                $horaactual=$objdate->format('H:i:s');
                $bitacora = new bitacora();
                $bitacora->idEmpleado =  session('idemp');
                $bitacora->fecha = $fechaactual;
                $bitacora->hora = $horaactual;
                $bitacora->tabla = 'notaservicio';
                $bitacora->codigoTabla = $tabla->id;
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
        $paquete=paquete::find($id);
        $paquete->delete();
    }

    public function recibirNotaServivio($id)
    {
        $notaservicio = notaservicio::findOrFail($id);
        $notaservicio->estado = 'terminado';
        $notaservicio->save();  

        /*REGISTRA EL MOVIMIENTO EN LA BITACORA */
        $objdate = new DateTime();
        $fechaactual= $objdate->format('Y-m-d');
        $horaactual=$objdate->format('H:i:s');
        $bitacora = new bitacora();
        $bitacora->idEmpleado =  session('idemp');
        $bitacora->fecha = $fechaactual;
        $bitacora->hora = $horaactual;
        $bitacora->tabla = 'notaservicio';
        $bitacora->codigoTabla = $id;
        $bitacora->transaccion = 'recibir';
        $bitacora->save();


        /*OBTENEMOS EL ID DEL SALON PARA ACTIVARLO NUEVAMENTE */
        $objnotaser = notaservicio::select("id","idSalon")
        ->where("id","=",$id)
        ->get();
       // dd($objnotaser);
        $idsalon=$objnotaser[0]->idSalon;
        $idnotaservicio=$objnotaser[0]->id;
        /*ponemos activo el salon */
        $salon= salon::findOrFail($idsalon);
        $salon->estado='activo';
        $salon->save();
        /*OBTENEMOS EL ID DEL PAQUETE PARA ACTIVARLO NUEVAMENTE */
        $detallenotaserv=detallenotapaquete::select('id','idPaqueteitem')
        ->where("idNotaservicio","=",$idnotaservicio)
        ->get();
        $idpaqueteitem=$detallenotaserv[0]->idPaqueteitem;

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
