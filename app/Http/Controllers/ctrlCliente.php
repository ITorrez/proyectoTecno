<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\cliente;
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
    }
    public function eliminar($id)
    {
        $cliente=cliente::find($id);
        $cliente->delete();
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
      if (Auth::attempt($data)) 
      {
          $con='Ok';
      }
      $usuario=$request->get('usuario');
      $query=cliente::where('usuario','=',$usuario)->get();
      if ($query->count()!=0) 
      {
        $passwordbd=$query[0]->password;
        $passwordForm=$request->get('password');
        if ($passwordbd==$passwordForm) 
        {
           return view('home');
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
    
}
