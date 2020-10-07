@extends('cliente.index2')
@section('contenido2')
    <template v-if="menu==0">
        <h1>Reservas</h1>
    </template>  
    <template v-if="menu==20">
       <frmreserva-component :usuario={{ Auth()->user()->id??0 }}></frmreserva-component>
    </template>
    <template v-if="menu==21">
        {{-- <frmpaquete-component></frmpaquete-component> --}}
    </template> 
    <template v-if="menu==23">
        {{-- <frmtipopaquete-component></frmtipopaquete-component> --}}
    </template> 
    <template v-if="menu==24">
        {{-- <frmsalon-component></frmsalon-component> --}}
    </template> 
    <template v-if="menu==25">
        {{-- <frmbitacora-component></frmbitacora-component> --}}
    </template> 
    <template v-if="menu==26">
       {{-- <frmitem-component></frmitem-component> --}}
    </template> 
    <template v-if="menu==27">
        {{-- <frmtipoitem-component></frmtipoitem-component> --}}
    </template> 
    <template v-if="menu==28">
        {{-- <frmempleado-component></frmempleado-component> --}}
        
    </template> 
    <template v-if="menu==29">
        {{-- <frmcliente-component></frmcliente-component> --}}
    </template> 
    <template v-if="menu==30">
        {{-- <h1>nota de servicio</h1> --}}
    </template>       

@endsection