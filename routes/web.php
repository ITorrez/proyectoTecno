<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/sistema', function () {
    return view('contenido/contenido');
});

Route::get('/clientes', function () {
    return view('layouts/app');
});

Route::get('/reservas', function () {
    return view('cliente/reserva/index');
});

Auth::routes();


/*ruta de tipoitem*/ 
Route::get('/tipoitem', 'ctrlTipoItem@index');
Route::post('/tipoitem/registrar', 'ctrlTipoItem@guardar');
Route::put('/tipoitem/actualizar', 'ctrlTipoItem@actualizar'); 
Route::delete('/tipoitem/eliminar_{id}', 'ctrlTipoItem@eliminar');
Route::get('/tipoitem/selectTipoItem', 'ctrlTipoItem@selectTipoItem');

//Item
Route::get('/item', 'ctrlItem@index');
Route::post('/item/registrar', 'ctrlItem@guardar');
Route::put('/item/actualizar', 'ctrlItem@actualizar');
Route::delete('/item/eliminar_{id}', 'ctrlItem@eliminar');

Route::get('/item/todos', 'ctrlItem@todos');

/*ruta de tipopaquete*/
Route::get('/tipopaquete', 'ctrlTipoPaquete@index');
Route::post('/tipopaquete/guardar', 'ctrlTipoPaquete@guardar');
Route::put('/tipopaquete/actualizar', 'ctrlTipoPaquete@actualizar');
Route::delete('/tipopaquete/eliminar_{id}', 'ctrlTipoPaquete@eliminar');
Route::get('/tipopaquete/todos', 'ctrlTipoPaquete@todos');


/*ruta de salon*/
Route::get('/salon', 'ctrlSalon@index');
Route::post('/salon/guardar', 'ctrlSalon@guardar');
Route::put('/salon/actualizar', 'ctrlSalon@actualizar');
Route::delete('/salon/eliminar_{id}', 'ctrlSalon@eliminar');
Route::get('/salon/mostrar_{id}', 'ctrlSalon@mostrar');
Route::get('/salon/todos', 'ctrlSalon@todos');

/*Rutas de Bitacora*/
Route::get('/bitacora', 'ctrlBitacora@index');
Route::post('/bitacora/guardar', 'ctrlBitacora@guardar');
Route::put('/bitacora/actualizar', 'ctrlBitacora@actualizar');
Route::delete('/bitacora/eliminar_{id}', 'ctrlBitacora@eliminar'); 

/*Rutas de Paquete*/
Route::get('/paquete', 'ctrlPaquete@index');
Route::get('/paquete/get_{id}', 'ctrlPaquete@getBy');
Route::get('/paquetedetalle/get_{id}', 'ctrlPaquete@getByDetalle');
Route::post('/paquete/guardar', 'ctrlPaquete@guardar');
Route::put('/paquete/actualizar', 'ctrlPaquete@actualizar');
Route::delete('/paquete/eliminar_{id}', 'ctrlPaquete@eliminar');
Route::get('/paquete/todos', 'ctrlPaquete@todos');

/*NOTA SERVICIO */
/*estados:
  entregado    es cuando se entrega todo el paquete y salon al cliente
  terminado    es cuando el cliente nos devuelve el paquete y salon*/
Route::get('/notaservicio/get_{id}', 'ctrlNotaServicio@getBy');
Route::post('/notaservicio/guardar','ctrlNotaServicio@guardar');
Route::get('/notaservicio','ctrlNotaServicio@index');
Route::put('/notaservicio/recibirnotaservicio_{id}', 'ctrlNotaServicio@recibirNotaServivio');

/*RESERVA */
/*estados:
  reservado  es cuando el cliente hizo su reserva, el cleinte o el empleado pueden anular
  entregado  es cuando se entrega todo el paquete y salon al cliente
  terminado  es cuando el cliente nos devuelve el paquete y salon
  anulado    es cuando anulan la reserva ya sea por el cliente o por el empleado*/
Route::get('/reserva/get_{id}', 'ctrlReserva@getBy');
Route::get('/reserva','ctrlReserva@index');
/*enlista todas las reservas de todos los clientes */
Route::get('/reservatodos','ctrlReserva@indextodos');

Route::post('/reserva/guardar','ctrlReserva@guardar');
Route::put('/reserva/anular_{id}','ctrlReserva@anularReserva');
Route::put('/reserva/entregar_{id}','ctrlReserva@entregarReserva');
Route::put('/reserva/recibirreserva_{id}','ctrlReserva@recibirPaqueteReserva');

/*Cliente*/
Route::get('/cliente','ctrlCliente@index');
Route::post('/cliente/guardar','ctrlCliente@guardar');
Route::put('/cliente/actualizar','ctrlCliente@actualizar');
Route::delete('/cliente/eliminar_{id}','ctrlCliente@eliminar');
Route::get('/cliente/todos', 'ctrlCliente@todos');
/*RUTA PARA EL LOGIN DEL CLIENTE */
Route::post('/', 'ctrlCliente@login')->name('cliente.login');

/*controlador Home*/
Route::get('/home', 'HomeController@index')->name('home');
/*Empleado*/
Route::get('/empleado', 'ctrlEmpleado@index');
Route::post('/empleado/guardar', 'ctrlEmpleado@guardar');
Route::put('/empleado/actualizar', 'ctrlEmpleado@actualizar');
Route::delete('/empleado/eliminar_{id}', 'ctrlEmpleado@eliminar');
Route::get('/empleado/selectEmpleado', 'ctrlEmpleado@selectEmpleado');
Route::get('/empleado/todos', 'ctrlEmpleado@todos');


