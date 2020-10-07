@extends('cliente.index2')
@section('contenido2')

  <template>
    <frmreserva-component :usuario={{ Auth()->user()->id??0 }}></frmreserva-component>
  </template>
@endsection