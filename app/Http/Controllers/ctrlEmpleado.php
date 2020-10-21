<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
/*Incluido para el login del empleado */
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Session;

use App\bitacora;
use App\empleado;
use DateTime;
session_start();
class ctrlEmpleado extends Controller
{
    //incluido para el login del empleado
    use AuthenticatesUsers;
    //protected $loginView = 'empleado.login';
    protected $guard='empleado';


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!$request->ajax()) return redirect('/');
        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if($buscar==''){
            $empleado = empleado::orderBy('id', 'desc')-> paginate(20);
        }
        else{
            $empleado = empleado::where($criterio, 'like', '%'.$buscar . '%')->orderBy('id', 'desc')->paginate(20);
        }
        return [
            'pagination' => [
                'total'          =>$empleado->total(),
                'current_page' => $empleado->currentPage(),
                'per_page'     => $empleado->perPage(),
                'last_page'    => $empleado->lastPage(),
                'from'         => $empleado->firstItem(),
                'to'           => $empleado->lastItem(),
            ],
            'empleado' => $empleado
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
    {
       $empleado= new empleado;
       $empleado->nombre=$request->nombre;
       $empleado->apellido=$request->apellido;
       $empleado->ci=$request->ci;
       $empleado->telefono=$request->telefono;
       $empleado->usuario=$request->usuario;
       $empleado->password=$request->password;
       $empleado->save();
       /*REGISTRA EL MOVIMIENTO EN LA BITACORA */
                $objdate = new DateTime();
                $fechaactual= $objdate->format('Y-m-d');
                $horaactual=$objdate->format('H:i:s');
            $bitacora = new bitacora();
            $bitacora->idEmpleado =  session('idemp');
            $bitacora->fecha = $fechaactual;
            $bitacora->hora = $horaactual;
            $bitacora->tabla = 'empleado';
            $bitacora->codigoTabla = $empleado->id;
            $bitacora->transaccion = 'crear';
            $bitacora->save();
    }

    public function actualizar(Request $request)
    {
       $empleado= empleado::findOrFail($request->id);
       $empleado->nombre=$request->nombre;
       $empleado->apellido=$request->apellido;
       $empleado->ci=$request->ci;
       $empleado->telefono=$request->telefono;
       $empleado->usuario=$request->usuario;
       $empleado->password=$request->password;
       $empleado->save();

       /*REGISTRA EL MOVIMIENTO EN LA BITACORA */
                $objdate = new DateTime();
                $fechaactual= $objdate->format('Y-m-d');
                $horaactual=$objdate->format('H:i:s');
            $bitacora = new bitacora();
            $bitacora->idEmpleado =  session('idemp');
            $bitacora->fecha = $fechaactual;
            $bitacora->hora = $horaactual;
            $bitacora->tabla = 'empleado';
            $bitacora->codigoTabla = $request->id;
            $bitacora->transaccion = 'actualizar';
            $bitacora->save();
    }

    public function eliminar($id)
    {
        $empleado=empleado::find($id);
        $empleado->delete();

        /*REGISTRA EL MOVIMIENTO EN LA BITACORA */
                $objdate = new DateTime();
                $fechaactual= $objdate->format('Y-m-d');
                $horaactual=$objdate->format('H:i:s');
            $bitacora = new bitacora();
            $bitacora->idEmpleado =  session('idemp');
            $bitacora->fecha = $fechaactual;
            $bitacora->hora = $horaactual;
            $bitacora->tabla = 'empleado';
            $bitacora->codigoTabla = $id;
            $bitacora->transaccion = 'eliminar';
            $bitacora->save();
    }
    public function selectEmpleado(Request $request){
        if (!$request->ajax()) return redirect('/');
        $empleado = empleado::where('id','>=','1')
        ->select('id','nombre')->orderBy('nombre', 'asc')->get();
        return ['empleado' => $empleado];
    }

    public function todos(){
        //if (!$request->ajax()) return redirect('/');
        $empleado = empleado::select("id","nombre")->get();
        return ['data' => $empleado];
    }

    public function ShowLoginForm()
    {
        return view('empleado.login');
    }

    public function authenticated()
    {
        return view('eeeee');
    }

    public function loginempleado(Request $request)
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
    // if (Auth::attempt($data)) 
    // {
    //     $con='Ok';
    // }
    $usuario=$request->get('usuario');
    $query=empleado::where('usuario','=',$usuario)->get();
        if ($query->count()!=0) 
        {
        $passwordbd=$query[0]->password;
        $idempleado=$query[0]->id;
        $nombreemp=$query[0]->nombre;
        $passwordForm=$request->get('password');
            if ($passwordbd==$passwordForm) 
            {
                //return view('home');
                //return view('../../contenido/contenido');
                session(['nombreemp' => $nombreemp]);
                session(['idemp' => $idempleado]);
                return redirect('/sistema');
            }
            else 
            {
                return back()->withErrors(['password'=>'Contraseña no valida'])->withInput([request('password')]);
            }
        }
        else {
        return back()->withErrors(['usuario'=>'usuario no valido'])->withInput([request('usuario')]);
        }
  
    }

    public function logoutempleado()
    {
       // Auth::logout(); 
        Session::flush(); 
        return redirect('/empleado/login');
    }

    
}
