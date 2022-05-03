
@extends('admin_template')
@section('content')

<section class="content-header">
  <h1>
    Reportes y Estadisticas
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Historial SSO ABB</li>
  </ol>
</section>
</br>
           
  <div class="row">
    <div class="col-md-12">     
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title">Historial SSO ABB</h3>
        </div>
      </div>
    </div>
  </div>      
  @include('messages')

@isset($listaDatos)
@if ($listaDatos > 0)
<div class="row">
    <div class="col-md-12">     
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title">Resultados</h3>
        </div>
      <div class="box-body">
           <table id="datosTabla" class="table table-bordered table-striped display">
                <thead>
                <tr>
                  <th>N° Folio</th>
                  <th>Empresa Principal</th>
                  <th>RUT</th>
                  <th>Empresa Contratista</th>
                  <th>RUT</th>
                  <th>Empresa Sub Contratista</th>
                  <th>RUT</th>
                  <th>Fecha Inicio</th>
                  <th>Fecha Vencimiento</th>
                  <th>Descargar</th>
                </tr>
                </thead>
                 <tbody>
                   @foreach($listaDatos as $datos)
                @isset($datos["folio"])
                <tr>
                   <td>
                   {{$datos["folio"]}}
                 </td>
                  <td>
                   {{$datos["principal"]}}
                 </td>
                 <td>
                   {{$datos["rutPrincipal"]}}
                 </td>
                  <td>
                   {{$datos["contratista"]}}
                 </td>
                 <td>
                   {{$datos["rutContratista"]}}
                 </td>
                 <td>
                   {{$datos["subContratista"]}}
                 </td>
                 <td>
                   {{$datos["rutsubContratista"]}}
                 </td>
                  <td>
                   {{$datos["fechaIncio"]}}
                 </td>
                 <td>
                   {{$datos["fechaFin"]}}
                 </td>
                 <!--https://sistema.certilapchile.cl/index.php?aa=pdf&cn=certificate&di= -->
                 <td>
                   <a href="descargar/{{$datos['archivo']}}" class="btn btn-success btn-lg active" role="button" aria-pressed="true" target="_blank"><i class="fa fa-file-excel-o" aria-hidden="true"></i></a>
                 </td>
                </tr>  
              </tbody>
                @endisset
                @endforeach
                </table>
      </div>
    </div>
  </div>
</div>
 @endisset
@else
<div class="row">
    <div class="col-md-12">
      <div class="box box-default">
        <div class="box-header with-border">
          <i class="fa fa-bullhorn"></i>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
        
          <div class="callout callout-info">
            <h4>No hay resultado</h4>

            <p>No hay folios historicos.</p>
          </div>
        
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>        <!-- /.col -->
</div>
@endif
 

<!-- /.row -->
</section>
    <!-- /.content -->

<link rel="stylesheet" href="{{ asset("/AdminLTE-2.3.11/plugins/select2/select2.min.css") }}">
<script src="{{ asset("/AdminLTE-2.3.11/plugins/select2/select2.min.js") }}""></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.css">

<script src="{{ asset("/AdminLTE-2.3.11/plugins/moment/moment.js") }}""></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/locale/es.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.js"></script>

<link rel="stylesheet" href="{{ asset("/AdminLTE-2.3.11/plugins/toaster/toastr.min.css") }}">
<script src="{{ asset("/AdminLTE-2.3.11/plugins/toaster/toastr.min.js") }}""></script>

<script src="{{ asset("/AdminLTE-2.3.11/plugins/chartjs/Chart.js") }}""></script>


<!-- DataTables -->
<link rel="stylesheet" href="{{ asset("/AdminLTE-2.3.11/plugins/datatables/DataTables-1.10.20/css/dataTables.bootstrap.min.css") }}"/>
<link rel="stylesheet" href="{{ asset("/AdminLTE-2.3.11/plugins/datatables/Buttons-1.6.1/css/buttons.bootstrap.min.css") }}"/> 
<script src="{{ asset("/AdminLTE-2.3.11/plugins/datatables/datatables.min.js") }}""></script>




<!-- SlimScroll -->
<script src="{{ asset("/AdminLTE-2.3.11/plugins/slimScroll/jquery.slimscroll.min.js") }}""></script>
<!-- FastClick -->
<script src="{{ asset("/AdminLTE-2.3.11/plugins/fastclick/fastclick.js") }}""></script>


<script type="text/javascript">
$(function () {


 });


</script>
    
@endsection