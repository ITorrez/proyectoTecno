<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">       
            <li class="nav-title">

              <?php
              $nombreCliente="";
               if (Auth()->user()) 
               {
                $nombreCliente=Auth()->user()->nombre;
               }
               else {
                $nombreCliente=session('nombrecli');
               } 
                
              ?>

                Panel <br>
                @if ($nombreCliente!="" )
                <label>Usuario: {{ $nombreCliente }}</label>
                @endif
            </li>
            {{-- <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-bag"></i> Cliente</a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-bag"></i> Categorías</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-bag"></i> Artículos</a>
                    </li>
                </ul>
            </li> --}}
            @if ($nombreCliente=="")
            <li class="nav-item nav-dropdown">
                <a target="_blank" class="nav-link nav-dropdown-toggle" href="{{ route('login') }}"><i class="icon-wallet"></i> Iniciar </a>
                {{-- <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <a class="nav-link" href="register"><i class="icon-wallet"></i> Registro</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}"><i class="icon-notebook"></i>Login</a>
                    </li>
                </ul> --}}
            </li>
            @endif


             @if ($nombreCliente!="")
             <li class="nav-item nav-dropdown">
                <a  class="nav-link nav-dropdown-toggle" href="#"><i class="icon-wallet"></i> Servicios </a>
                <ul class="nav-dropdown-items">
                    <li @click="menu=20" class="nav-item">
                        
                        <a onclick="cerratVentana()" target="_blank"   class="nav-link" href="reservas"><i class="icon-wallet"></i> Reserva</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}"><i class="icon-notebook"></i>Logaut</a>
                    </li> --}}
                </ul>
            </li> 
            
            
            <li class="nav-item nav-dropdown">
                
                <a class="nav-link" href="/cliente" onclick="event.preventDefault();
                                       document.getElementById('logout-form').submit();"
                                       ><i class="icon-logout"></i> Cerrar sesión
                </a>
                
                {{-- <ul class="nav-dropdown-items">
                    <li @click="menu=23" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-basket-loaded"></i> Paquetes</a>
                    </li>
                    <li @click="menu=3" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-notebook"></i> Tipo de Paquetes</a>
                    </li>
                </ul> --}}
            </li>

            @endif
            
            @if ($nombreCliente=="")
            <li class="nav-item nav-dropdown">    
                <a target="_blank" class="nav-link nav-dropdown-toggle" href="{{ route('register') }}"><i class="icon-wallet"></i>Registro </a>
                {{-- <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <a class="nav-link" href="register"><i class="icon-wallet"></i> Registro</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}"><i class="icon-notebook"></i>Login</a>
                    </li>
                </ul> --}}
            </li>
            @endif

            {{-- @if(Auth()->cliente())
            <label>Usuario: {{ Auth()->cliente()->nombre }}</label>
            @endif --}}
      


            

            {{-- <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-basket"></i> Salon y Ambiente</a>
                <ul class="nav-dropdown-items">
                    <li @click="menu=4" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-basket-loaded"></i> Salones</a>
                    </li>
                    <li @click="menu=5" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-notebook"></i> Bitacora</a>
                    </li>
                </ul>
            </li>


            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-basket"></i> Inventario</a>
                <ul class="nav-dropdown-items">
                    <li @click="menu=6" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-basket-loaded"></i> Items</a>
                    </li>
                    <li @click="menu=7" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-notebook"></i> Tipo Items</a>
                    </li>
                </ul>
            </li>


            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-people"></i> Control de Usuarios</a>
                <ul class="nav-dropdown-items">
                    <li @click="menu=8"  class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-user"></i> Empleados</a>
                    </li>
                    <li @click="menu=9" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-user-following"></i> Clientes</a>
                    </li>
                </ul>
            </li>



            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-pie-chart"></i> Reportes</a>
                <ul class="nav-dropdown-items">
                    <li @click="menu=10" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-chart"></i> Reporte Ingresos</a>
                    </li>
                    <li @click="menu=11" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-chart"></i> Reporte Ventas</a>
                    </li>
                </ul>
            </li> --}}


           
        </ul>
    </nav>

    <form id="logout-form" method="POST" action="{{ route('cliente.logout') }}" style="display: none;">
        @csrf
    </form>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
    {{-- <a href="reservas">Salirff</a> --}}
</div>
<script>
    function cerratVentana(){
        window.close();
        //alert('dfbxfd');
    }
</script>