
@extends('admin_template')
@section('content')

<section class="content-header">
  <h1>
    Formulario de carga
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Productividad Certificación</li>
  </ol>
</section>
</br>
@isset($block)
@if ($block == 0)         
  <div class="row">
    <div class="col-md-12">     
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title">Registrar datos</h3>
        </div>
      </div>
    </div>
  </div>      
  @include('messages')
  <form method="POST" action="{{ route('productividadCert.store') }}" role="form" class="form-horizontal" id="formDocumentos">
  

    <div class="row">
      <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"></h3>
            </div>
      
               {{ csrf_field() }}
              <div class="box-body">
                <table class="table">
                  <thead>
                    <tr>
                      <th scope="col">Fecha: {{ date('d-m-Y')}}</th>
                      <th colspan="2">Primer Ciclo</th>
                      <th colspan="2">Segundo Ciclo</th>
                    </tr>
                  </thead>
                  <thead>
                    <tr>
                      <th scope="col">Certificador</th>
                      <th scope="col">N° Centro de Costo</th>
                      <th scope="col">N° Trabajadores</th>
                      <th scope="col">N° Centro de Costo</th>
                      <th scope="col">N° Trabajadores</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                      $i = 0;
                      @endphp
                    @foreach($UsuarioCertilap as $datos)
                     @isset($datos["name"])
                     
                    <tr>
                      <td>{{ucwords(mb_strtolower($datos["name"],'UTF-8'))}}<input type="hidden"  name="usuario_{{$i}}" value = "{{$datos['id']}}"></td>
                      <td><input type="number" min="0" class="form-control" name="centro_{{$datos['id']}}" id="centro_{{$datos['id']}}"></td>
                      <td><input type="number" min="0" class="form-control" name="trabajadores_{{$datos['id']}}" id="trabajadores_{{$datos['id']}}"></td>
                      <td><input type="number" min="0" class="form-control" name="centro2_{{$datos['id']}}" id="centro2_{{$datos['id']}}"></td>
                      <td><input type="number" min="0" class="form-control" name="trabajadores2_{{$datos['id']}}" id="trabajadores2_{{$datos['id']}}"></td>
                    </tr>
                  </tbody>
                  @php
                  $i ++;
                  @endphp
                  @endisset
                  @endforeach
                </table>

          
               
              </div> 

              <div class="box-footer">
                
                <button type="button" id="botonEnviar" class="btn btn-info pull-right">Guardar</button>
              </div>
        </div>     
      </div>
    </div>
</form>
@endif
@endisset 

@isset($datosVista)
@if (count($datosVista))
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
                  <th>Certificador</th>
                  <th>N° Centro de Costo Primer Ciclo</th>
                  <th>N° Trabajadores Primer Ciclo</th>
                  <th>N° Centro de Costo Segundo Ciclo</th>
                  <th>N° Trabajadores Segundo Ciclo</th>
                  <th>Fecha</th>
                </tr>
                </thead>
                 <tbody>
                @foreach($datosVista as $dato)
                @isset($dato["usuario"])
                <tr>
                  <td> 
                   {{$dato["usuario"]}}
                 </td>
                  <td> 
                   {{$dato["numCentro"]}}
                 </td>
                 <td> 
                   {{$dato["numTrab"]}}
                 </td>
                 <td> 
                   {{$dato["numCentro2"]}}
                 </td>
                 <td> 
                   {{$dato["numtraba2"]}}
                 </td>
                  <td> 
                   {{$dato["fecha"]}}
                 </td>
                </tr> 
                @endisset
                @endforeach
                </table>
               
      </div>
    </div>
  </div>
</div>
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

            <p>No hay resultado para la busquedad solicitada.</p>
          </div>
        
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>        <!-- /.col -->
</div>
@endif
@endisset 




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
<link rel="stylesheet" href="{{ asset("/AdminLTE-2.3.11/plugins/datatables/datatableButtons-1.6.1/css/buttons.bootstrap.min.css") }}"/> 
<script src="{{ asset("/AdminLTE-2.3.11/plugins/datatables/datatables.min.js") }}""></script>




<!-- SlimScroll -->
<script src="{{ asset("/AdminLTE-2.3.11/plugins/slimScroll/jquery.slimscroll.min.js") }}""></script>
<!-- FastClick -->
<script src="{{ asset("/AdminLTE-2.3.11/plugins/fastclick/fastclick.js") }}""></script>

</script>
<script type="text/javascript">
$(function () {

 

  var table = $('table.display').DataTable({  
  pageLength: 20,        
        language: {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar: ",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast":"Último",
                    "sNext":"Siguiente",
                    "sPrevious": "Anterior"
           },
           "sProcessing":"Procesando...",
            },
        //para usar los botones   
        responsive: "true",
        dom: 'Bfrtilp',       
        buttons:[ 
      {
        extend:    'excelHtml5',
        text:      '<i class="fa fa-fw fa-file-excel-o"></i> ',
        titleAttr: 'Exportar a Excel',
        className: 'btn btn-success',
        title: 'Reporte Productividad',   
        filename:  'Reporte Productividad'
      },
      {
        extend:    'pdfHtml5',
        text:      '<i class="fa fa-fw fa-file-pdf-o"></i> ',
        titleAttr: 'Exportar a PDF',
        className: 'btn btn-danger',
        title: 'Reporte Productividad',   
        filename:  'Reporte Productividad'
      },
      {
        extend:    'print',
        text:      '<i class="fa fa-print"></i> ',
        titleAttr: 'Imprimir',
        className: 'btn btn-info',
        title: 'Reporte Productividad',   
        filename:  'Reporte Productividad'
      },
    ]         
    });

   
  toastr.options = {
  "debug": false,
  "positionClass": "toast-top-center",
  "onclick": null,
  "fadeIn": 300,
  "fadeOut": 1000,
  "timeOut": 5000,
  "extendedTimeOut": 1000
  }
   
   
    
  $("#botonEnviar").click(function(){

    let array = {!! json_encode($UsuarioCertilap) !!};
    let error = 0;
    $.each(array, function(i, item) {
     

     // console.log(centro_+item.id);

      if($("#centro_"+item.id).val() == ""){
        error = 1;

        $("#centro_"+item.id).css({"border":"1px solid red  !important"});
          toastr.error("","<br>Debe ingresar un valor de Centro de Costo <br><br>").css({"width"  : "30%" , "text-align" : "center"});
      }

      if($("#trabajadores_"+item.id).val() == ""){
        error = 1;

        $("#trabajadores_"+item.id).css({"border":"1px solid red  !important"});
          toastr.error("","<br>Debe ingresar un valor  de Trabajadores<br><br>").css({"width"  : "30%" , "text-align" : "center"});
      }

      if($("#centro2_"+item.id).val() == ""){
        error = 1;

        $("#centro2_"+item.id).css({"border":"1px solid red  !important"});
          toastr.error("","<br>Debe ingresar un valor de Centro de Costo <br><br>").css({"width"  : "30%" , "text-align" : "center"});
      }

      if($("#trabajadores2_"+item.id).val() == ""){
        error = 1;

        $("#trabajadores2_"+item.id).css({"border":"1px solid red  !important"});
          toastr.error("","<br>Debe ingresar un valor de Trabajadores<br><br>").css({"width"  : "30%" , "text-align" : "center"});
      }

       
    });

    if(error == 0){
      $("#formDocumentos").submit();
    }

   
    

  })
 });


</script>
    
@endsection