<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\cliente;
use App\bitacora;
use DateTime;
session_start();
class ctrlCliente extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // if (!$request->ajax()) return redirect('/');
        $buscar = $request->buscar;
        $criterio = $request->criterio;
         
        if ($buscar==''){
            $cliente = cliente::orderBy('id', 'desc')->paginate(5);
        }
        else{
            $cliente = cliente::where($criterio, 'like', '%'.$buscar.'%')->orderBy('id', 'desc')->paginate(5);
        }
        return [
            'pagination' => [
                'total'        => $cliente->total(),
                'current_page' => $cliente->currentPage(),
                'per_page'     => $cliente->perPage(),
                'last_page'    => $cliente->lastPage(),
                'from'         => $cliente->firstItem(),
                'to'           => $cliente->lastItem(),
            ],
            'cliente' => $cliente
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
    {
        $cliente = new cliente;
        $cliente->nombre=$request->nombre;
        $cliente->apellido=$request->apellido;
        $cliente->ci=$request->ci;
        $cliente->direccion=$request->direccion;
        $cliente->telefono=$request->telefono;
        $cliente->usuario=$request->usuario;
        $cliente->password=$request->password;
        $cliente->save();

         /*REGISTRA EL MOVIMIENTO EN LA BITACORA */
         $objdate = new DateTime();
         $fechaactual= $objdate->format('Y-m-d');
         $horaactual=$objdate->format('H:i:s');
            $bitacora = new bitacora();
            $bitacora->idEmpleado =  session('idemp');
            $bitacora->fecha = $fechaactual;
            $bitacora->hora = $horaactual;
            $bitacora->tabla = 'cliente';
            $bitacora->codigoTabla = $cliente->id;
            $bitacora->transaccion = 'crear';
            $bitacora->save();
    }

    public function actualizar(Request $request)
    {
        $cliente= cliente::findOrFail($request->id);
        $cliente->nombre=$request->nombre;
        $cliente->apellido=$request->apellido;
        $cliente->ci=$request->ci;
        $cliente->direccion=$request->direccion;
        $cliente->telefono=$request->telefono;
        $cliente->usuario=$request->usuario;
        $cliente->password=$request->password;
        $cliente->save();

        /*REGISTRA EL MOVIMIENTO EN LA BITACORA */
        $objdate = new DateTime();
        $fechaactual= $objdate->format('Y-m-d');
        $horaactual=$objdate->format('H:i:s');
           $bitacora = new bitacora();
           $bitacora->idEmpleado =  session('idemp');
           $bitacora->fecha = $fechaactual;
           $bitacora->hora = $horaactual;
           $bitacora->tabla = 'cliente';
           $bitacora->codigoTabla = $request->id;
           $bitacora->transaccion = 'actualizar';
           $bitacora->save();
    }
    public function eliminar($id)
    {
        $cliente=cliente::find($id);
        $cliente->delete();

        /*REGISTRA EL MOVIMIENTO EN LA BITACORA */
        $objdate = new DateTime();
        $fechaactual= $objdate->format('Y-m-d');
        $horaactual=$objdate->format('H:i:s');
           $bitacora = new bitacora();
           $bitacora->idEmpleado =  session('idemp');
           $bitacora->fecha = $fechaactual;
           $bitacora->hora = $horaactual;
           $bitacora->tabla = 'cliente';
           $bitacora->codigoTabla = $id;
           $bitacora->transaccion = 'eliminar';
           $bitacora->save();
    }
    public function todos(){
        //if (!$request->ajax()) return redirect('/');
        $cliente = cliente::select("id","nombre")->get();
        return ['data' => $cliente];
    }

    public function login(Request $request)
    {
        //PARA VERIFICAR SI LOS DATOS SE ENVIAN
      //return dd($request->all()); 
      $data=request()->validate([
          'usuario'=>'required',
          'password'=>'required'
      ],
      [
          'usuario.required'=>'Ingrese Usuario',
          'password.required'=>'Ingrese Password',
      ]);
    //   if (Auth::attempt($data)) 
    //   {
    //       $con='Ok';
    //   }
      $usuario=$request->get('usuario');
      $query=cliente::where('usuario','=',$usuario)->get();
      if ($query->count()!=0) 
      {
        $passwordbd=$query[0]->password;
        $idcli=$query[0]->id;
        $nombrecli=$query[0]->nombre;
        $passwordForm=$request->get('password');
        if ($passwordbd==$passwordForm) 
        {
           //return view('home');
                //return view('../../contenido/contenido');
                session(['nombrecli' => $nombrecli]);
                session(['idcli' => $idcli]);
                return redirect('/reservas');
        }
        else 
        {
            return back()->withErrors(['password'=>'Contraseña no valida'])->withInput([request('password')]);
        }
      }
      else {
        return back()->withErrors(['usuario'=>'usuario no valida'])->withInput([request('usuario')]);
      }
    }

    public function logoutcliente()
    {
        Auth::logout(); 
        Session::flush(); 
        return redirect('/clientes');
    }
    
}
