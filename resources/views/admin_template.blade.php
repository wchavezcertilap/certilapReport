<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Certilap Reportes</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="{{ asset("/AdminLTE-2.3.11/bootstrap/css/bootstrap.min.css") }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset("/AdminLTE-2.3.11/dist/css/AdminLTE.min.css") }}">
  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect.
  -->
  <link rel="stylesheet" href="{{ asset("/AdminLTE-2.3.11/dist/css/skins/skin-blue.min.css") }}">

<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 2.2.3 -->
<script src="{{ asset("/AdminLTE-2.3.11/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ asset("/AdminLTE-2.3.11/plugins/jQueryUI/jquery-ui.min.js") }}"></script>
<!-- Bootstrap 3.3.6 -->
<script src="{{ asset("/AdminLTE-2.3.11/bootstrap/js/bootstrap.min.js") }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset("/AdminLTE-2.3.11/dist/js/app.min.js") }}"></script>
<script type="text/javascript">
  
  function close_window() {
  if (confirm("Desea salir de la reporteria?")) {
    close();
  }
}
</script>

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- Main Header -->
  <header class="main-header">

    <!-- Logo -->
    <a class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><img src="{{ asset("/img/logo.jpg") }}"></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><img src="{{ asset("/img/certilapLogo.jpg") }}"></span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- Messages: style can be found in dropdown.less-->
          <li class="dropdown messages-menu">
            <!-- Menu toggle button -->
             <!--   <a href="#" class="dropdown-toggle" data-toggle="dropdown">
           <i class="fa fa-envelope-o"></i>
              <span class="label label-success">4</span> -->
            </a>
            <ul class="dropdown-menu">
             <!--  <li class="header">You have 4 messages</li>
              <li> -->
                <!-- inner menu: contains the messages -->
                <ul class="menu">
                  <li><!-- start message -->
                    <a href="#">
                      <div class="pull-left">
                        <!-- User Image -->
                        <img src="{{ asset("/AdminLTE-2.3.11/dist/img/user2-160x160.png") }}" class="img-circle" alt="User Image">
                      </div>
                      <!-- Message title and timestamp -->
                      <h4>
                        Support Team
                        <small><i class="fa fa-clock-o"></i> 5 mins</small>
                      </h4>
                      <!-- The message -->
                      <p>Why not buy a new awesome theme?</p>
                    </a>
                  </li>
                  <!-- end message -->
                </ul>
                <!-- /.menu -->
              </li>
              <li class="footer"><a href="#">See All Messages</a></li>
            </ul>
          </li>
          <!-- /.messages-menu -->

          <!-- Notifications Menu -->
          <li class="dropdown notifications-menu">
            <!-- Menu toggle button -->
            <!-- <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
              <span class="label label-warning">10</span>
            </a> -->
            <ul class="dropdown-menu">
             <!--  <li class="header">You have 10 notifications</li>
              <li> -->
                <!-- Inner Menu: contains the notifications -->
                <ul class="menu">
                  <li><!-- start notification -->
                    <a href="#">
                      <i class="fa fa-users text-aqua"></i> 5 new members joined today
                    </a>
                  </li>
                  <!-- end notification -->
                </ul>
              </li>
              <li class="footer"><a href="#">View all</a></li>
            </ul>
          </li>
          <!-- Tasks Menu -->
          <li class="dropdown tasks-menu">
            <!-- Menu Toggle Button -->
           <!--  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-flag-o"></i>
              <span class="label label-danger">9</span>
            </a> -->
            <ul class="dropdown-menu">
            <!--   <li class="header">You have 9 tasks</li>
              <li> -->
                <!-- Inner menu: contains the tasks -->
                <ul class="menu">
                  <li><!-- Task item -->
                    <a href="#">
                      <!-- Task title and progress text -->
                      <h3>
                        Design some buttons
                        <small class="pull-right">20%</small>
                      </h3>
                      <!-- The progress bar -->
                      <div class="progress xs">
                        <!-- Change the css width attribute to simulate progress -->
                        <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                          <span class="sr-only">20% Complete</span>
                        </div>
                      </div>
                    </a>
                  </li>
                  <!-- end task item -->
                </ul>
              </li>
              <li class="footer">
                <a href="#">View all tasks</a>
              </li>
            </ul>
          </li>
          <!-- User Account Menu -->
          <li class="dropdown user user-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <!-- The user image in the navbar-->
              <img src="{{ asset("/AdminLTE-2.3.11/dist/img/user2-160x160.png") }}" class="user-image" alt="User Image">
              <!-- hidden-xs hides the username on small devices so only the image appears. -->
              <span class="hidden-xs">{{$datosUsuarios->name}}</span>
              @php
               session(['datosUsuarios-nombre'=> $datosUsuarios->name]);
              @endphp
            </a>
            <ul class="dropdown-menu">
              <!-- The user image in the menu -->
              <li class="user-header">
                <img src="{{ asset("/AdminLTE-2.3.11/dist/img/user2-160x160.png") }}" class="img-circle" alt="User Image">

                <p>
                  {{$datosUsuarios->name}}
                  <small>Member since Nov. 2012</small>
                </p>
              </li>
              <!-- Menu Body -->
              <li class="user-body">
                <div class="row">
                  <div class="col-xs-4 text-center">
                    <a href="#">Followers</a>
                  </div>
                  <div class="col-xs-4 text-center">
                    <a href="#">Sales</a>
                  </div>
                  <div class="col-xs-4 text-center">
                    <a href="#">Friends</a>
                  </div>
                </div>
                <!-- /.row -->
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="#" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="#" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
          <li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

      <!-- Sidebar user panel (optional) -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="{{ asset("/AdminLTE-2.3.11/dist/img/user2-160x160.png") }}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>{{$datosUsuarios->name}}</p>
          <!-- Status -->
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>

      <!-- search form (Optional) -->
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>
      <!-- /.search form -->

      <!-- Sidebar Menu -->
      <ul class="sidebar-menu">
        <li class="header">Menu</li>
         @if ($datosUsuarios->type == 1 )
          <li class="treeview">
            <a href="#"><i class="fa fa-users"></i> <span>Usuarios</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="{{ route('usuarioDesactiva.index') }}">Desactivar / Eliminar</br>Usuarios</a></li>
              
            </ul>
          </li>
        @endif
        @if ($datosUsuarios->type == 1 || $datosUsuarios->type == 2)
      
        <li class="treeview">
          <a href="#"><i class="fa fa-link"></i> <span>Configuraciones</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
          
            <li><a href="{{ route('cambiarCiclo.index') }}">Cambiar ciclos SSO</a></li>
            <li><a href="{{ route('habilitarFolio.index') }}">Habilitar Folio</a></li>
            <li><a href="{{ route('productividadCert.index') }}">Productividad Certificación</a></li>
            <li><a href="{{ route('observacionesDoc.index') }}">Observacioes Doc</a></li>
          </ul>
        </li>
        <!-- facturacion -->
        <li class="treeview">
          <a href="#"><i class="fa fa-money"></i> <span>Facturación</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
             <li><a href="{{ route('trabajadoresPagadosPre.index') }}">Trabajadores Pagados</br>SSO</a></li>
             <li><a href="{{ route('reporteFactCert.index') }}">Reporte Certificación/Facturación</a></li>
          </ul>
        </li> 

       
        @endif
       
      
     
  
        @if( $datosUsuarios->type == 1 || $datosUsuarios->type == 2 || $datosUsuarios->type == 3)
        @if($certificacion == 0)
          <li class="treeview">
          <a href="#"><i class="fa fa-link"></i> <span>Reporte SSO</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
           
            <li><a href="{{ route('documentoReporte.index') }}">Estado de Documentos </a></li>
            
             <!--  <li><a href="{{ route('reporteAcreditacion.index') }}">Reporte Acreditación</a></li> -->
            <li><a href="{{ route('reporteAquaAcreditado.index') }}">% de Acreditación global</br>por trabajador</a></li>
            <li><a href="{{ route('reporteTrabajadoresSsoAcre.index') }}">Reporte Trabajdores SSO</br>VS Certificación Laboral</a></li>
            <li><a href="{{ route('porcentajeCumplimientoSSO.index') }}">% Cumplimiento SSO</a></li>
            @if($usuarioAqua == 1 || $datosUsuarios->type == 1 || $datosUsuarios->type == 2 )
              <li><a href="{{ route('reporteCumplimientoAqua.index') }}">Reporte Cumplimento </br>AquaChile</a></li>
              <li><a href="{{ route('reporteCSA.index') }}">Reporte Cruzado Acreditación</br>Verificación y Control de Acceso</a></li>
            @endif
            @if($usuarioABBChile == 1 || $datosUsuarios->type == 1 || $datosUsuarios->type == 2)
             <li><a href="{{ route('historialSso.index') }}">Folios Historicos</a></li>
            @endif
             <li><a href="{{ route('reporteSSOClaro.index') }}">Reporte SSO acreditación</a></li>
          </ul>
        </li>
        @endif
        @if($usuarioNOKactivo == 0)
         <li class="treeview">
          <a href="#"><i class="fa fa-link"></i> <span>Reporte Certificación Laboral</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <!--  <li><a href="{{ route('documentoReporte.index') }}">Estado de Documentos </a></li>
             <li><a href="{{ route('reporteAcreditacion.index') }}">Reporte Acreditación</a></li> -->
            <li><a href="{{ route('reporteCertificacion.index') }}">Reporte Certificación</a></li>
            <li><a href="{{ route('reporteCertificacionGrafica.index') }}">Reporte Certificación Graficos</a></li>
            <li><a href="{{ route('reporteRotacion.index') }}">Reporte Rotación</a></li>
            <li><a href="{{ route('reporteCompleto.index') }}">Reporte Completo</a></li>
            @if($usuarioAqua == 1 || $datosUsuarios->type == 1 || $datosUsuarios->type == 2)
            <li><a href="{{ route('reporteFTE.index') }}">Reporte FTE</a></li>
            @endif
            @if($datosUsuarios->type == 1 || $datosUsuarios->type == 2 )
            <li><a href="{{ route('reporteExtranjero.index') }}">Reporte Extranjeros</a></li>
            @endif
             <li><a href="{{ route('reporteObsCert.index') }}">Reporte Observaciones</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#"><i class="fa fa-certificate"></i> <span>Certificados</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
           <ul class="treeview-menu">
            <li><a href="{{ route('buscarCertificado.index') }}">Buscar Certificado</br>por número</a></li>
            <li><a href="{{ route('certificadoMasivo.index') }}">Buscar Certificados</br>por Empresa</a></li>
          </ul>
        </li>
        @endif
        <!-- <li class="treeview">
          <a href="#"><i class="fa fa-link"></i> <span>Reporte Certificación</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
             <li><a href="{{ route('reporteCompleto.index') }}">Reporte Completo</a></li>
              <li><a href="{{ route('menuHijo.create') }}">Reporte Empresa</a></li> 
          </ul>
        </li> -->
<!-- 
        <li class="treeview">
          <a href="#"><i class="fa fa-link"></i> <span>Reporte Integrados</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
             <li><a href="{{ route('reporteCompleto.index') }}">Reporte 1</a></li>
              
          </ul>
        </li> -->

<!--         <li class="treeview">
          <a href="#"><i class="fa fa-fw fa-medkit"></i> <span>siniestralidad</br>accidentabilidad</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
        </li> -->
      



       @endif
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    @yield('content')
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
      Sistema de Gestión de trabajadores
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2019 <a href="http://certilapchile.cl/">Certilap Chile</a>.</strong> Derechos Reservados.
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
      <li class="active"><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
      <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
      <!-- Home tab content -->
        <div class="tab-pane active" id="control-sidebar-home-tab">
        <h3 class="control-sidebar-heading">Cerrar Reporteria</h3>
        <ul class="control-sidebar-menu">
          <li>
            <a href="javascript:close_window();">
              <i class="menu-icon fa fa-desktop bg-red"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">salir</h4>

                
              </div>
            </a>
          </li>
        </ul>
        <!-- /.control-sidebar-menu -->

        <!-- <h3 class="control-sidebar-heading">Tasks Progress</h3>
        <ul class="control-sidebar-menu">
          <li>
            <a href="javascript:;">
              <h4 class="control-sidebar-subheading">
                Custom Template Design
                <span class="pull-right-container">
                  <span class="label label-danger pull-right">70%</span>
                </span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
              </div>
            </a>
          </li>
        </ul> -->
        <!-- /.control-sidebar-menu -->

      </div>
      <!-- /.tab-pane -->
      <!-- Stats tab content -->
      <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
      <!-- /.tab-pane -->
      <!-- Settings tab content -->
      <div class="tab-pane" id="control-sidebar-settings-tab">
        <form method="post">
          <h3 class="control-sidebar-heading">General Settings</h3>

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Report panel usage
              <input type="checkbox" class="pull-right" checked>
            </label>

            <p>
              Some information about this general settings option
            </p>
          </div>
          <!-- /.form-group -->
        </form>
      </div>
      <!-- /.tab-pane -->
    </div>
  </aside>
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->



<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->

     
</body>
</html>

