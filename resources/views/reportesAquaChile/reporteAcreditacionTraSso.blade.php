
@extends('admin_template')
@section('content')

<section class="content-header">
  <h1>
    Reportes y Estadisticas
  </h1>
  <ol class="breadcrumb">
    <li><a href="https://certilapreports.certilapchile.cl/public/inicio/{{base64_encode($datosUsuarios->id)}}"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Reportes SSO</li>
  </ol>
</section>
</br>
           
  <div class="row">
    <div class="col-md-12">     
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title">Reportes acreditación trabajadores.</h3>
        </div>
      </div>
    </div>
  </div>      
  @include('messages')
  <form method="POST" action="{{ route('reporteAquaAcreditado.store') }}" role="form" class="form-horizontal" id="formDocumentos">

    <div class="row">
      <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Criterios de Busqueda</h3>
            </div>
      
               {{ csrf_field() }}
              <div class="box-body">
                <div class="form-group">
                  <label for="empresaPrincipal" class="col-sm-2 control-label">Empresa Principal</label>
                   <div class="col-sm-4">
                    <select class="form-control js-example-basic-multiple empresaPrincipal" name="empresaPrincipal[]" id="empresaPrincipal"  multiple="multiple">
                          <option>Seleccione Empresa Principal</option>
                          @foreach($EmpresasP as $empresa)

                          <option value="{{$empresa->sso_mcomp_rut}}">{{mb_strtoupper($empresa->sso_mcomp_name)}}</option>
                          @endforeach
                      </select>
                  </div>
               
                  <label for="empresaSub" class="col-sm-2 control-label">Empresa Contratistas</label>
                   <div class="col-sm-4">
                        <select class="form-control js-example-basic-multiple" name="empresaContratista[]" id="empresaContratista"  multiple="multiple">
                          <option>Seleccione Empresa Contratista</option>
                         
                      </select>
                  </div>
                </div>

              <!--   <div class="form-group">
                   <label for="folio" class="col-sm-2 control-label">Folios</label>
                   <div class="col-sm-4">
                        <select class="form-control" name="folio" id="folio" >
                          <option>Seleccione folio</option>
                        
                      </select>
                  </div>
                </div> -->
              </div> 

              <div class="box-footer">
                
                <button type="button" id="botonEnviar" class="btn btn-info pull-right">buscar</button>
              </div>
        </div>     
      </div>
    </div>
</form>

@isset($WORK)
@if (count($WORK))
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
                  <th>Folio</th>
                  <th>RUT Principal</th>
                  <th>Empresa Principal</th>
                  <th>RUT Contratista</th>
                  <th>Empresa Contratista</th>
                  <th>RUT Sub Contratista</th>
                  <th>Sub Contratista</th>
                  <th>RUT Trabajador</th>
                  <th>Nombre</th>
                  <th>Apellido</th>
                  <th>Porcentaje</th>
                  @if($estado == 1)
                  <th>Estado</th>
                  @endif
                </tr>
                </thead>
                 <tbody>
                @foreach($WORK as $work)
                @isset($work["folio"])
                <tr>
                  <td> 
                   {{$work["folio"]}}
                 </td>
                  <td>
                   {{$work["rutPrincipal"]}}
                 </td>
                 <td>
                   {{$work["nombrePrincipal"]}}
                 </td>
                 <td>
                   {{$work["rutContratista"]}}
                 </td>
                  <td>
                   {{$work["nombreContratista"]}}
                 </td>
                  <td>
                   {{$work["rutSubContra"]}}
                 </td>
                 <td>
                   {{$work["nombreSubContra"]}}
                 </td>
                  <td>
                   {{$work["rutTrabajador"]}}
                 </td>
                 <td>
                   {{$work["nombreTrabajador"]}}
                 </td>
                 <td>
                   {{$work["apellidoTrabajador"]}}
                 </td>
                 <td>
                   {{$work["porcentajeTrabajador"]}}
                 </td>
                    @isset($work["estado"])
                    <td>
                      {{$work["estado"]}}
                    </td>
                   @endisset
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

@isset ($totalTrabajadores)
@if ($totalTrabajadores > 0)
<div class="box box-danger">
  <input type="hidden" id="totalTrabajadores" value="{{$totalTrabajadores}}">
  <input type="hidden" id="totalAcreditados" value="{{$totalAcreditados}}">
  <input type="hidden" id="totalNoAcreditados" value="{{$totalNoAcreditados}}">
            <div class="box-header with-border">
              <h3 class="box-title">Grafica de documentos</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <canvas id="pieChart" style="height:250px"></canvas>
            </div>
            <!-- /.box-body -->
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
         title: 'Reporte % Acreditiacion Global por Trabajador',   
        filename:  'Reporte_porcentaje_acreditacion_global_trabajadores'
      },
      {
        extend:    'pdfHtml5',
        text:      '<i class="fa fa-fw fa-file-pdf-o"></i> ',
        titleAttr: 'Exportar a PDF',
        className: 'btn btn-danger',
        orientation: 'landscape',
        pageSize: 'LEGAL',
        title: 'Reporte % Acreditiacion Global por Trabajador',   
        filename:  'Reporte_porcentaje_acreditacion_global_trabajadores'
      },
      {
        extend:    'print',
        text:      '<i class="fa fa-print"></i> ',
        titleAttr: 'Imprimir',
        className: 'btn btn-info',
        title: 'Reporte % Acreditiacion Global por Trabajador',   
        filename:  'Reporte_porcentaje_acreditacion_global_trabajadores'
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

    //Initialize Select2 Elements
  $(".js-example-basic-multiple").select2();


  $("#empresaPrincipal").change(function(){
    var rutPrincipal = $(this).val();
    $.get('porContratistaAquaSSO/'+rutPrincipal, function(data){

        var contratista = '<option value="">Seleccione Contratista</option>'
          for (var i=0; i<data.length;i++)
            contratista+='<option value="'+data[i].sso_comp_rut+'">'+data[i].sso_comp_name.toUpperCase()+'</option>';
        
          $("#empresaContratista").html(contratista);
    });

    $.get('porFolio/'+rutPrincipal, function(data){

        var folio = '<option value="">Seleccione Folio</option>'
          for (var x=0; x<data.length;x++)
            folio+='<option value="'+data[x].id+'">'+data[x].id+'</option>';
        
          $("#folio").html(folio);
    });
  });


 $("#botonEnviar").click(function(){

    var empresaPrincipal = $("#empresaPrincipal").val();
  
    if(empresaPrincipal == "Seleccione Empresa Principal" || empresaPrincipal == null){
        $("#empresaPrincipal").css({"border":"1px solid red  !important"});
        toastr.error("","<br>Debe seleccionar Empresa Principal<br><br>").css({"width"  : "30%" , "text-align" : "center"});
      }else{

         $("#formDocumentos").submit();
      }

  })

    //-------------
    //- PIE CHART - ACREDITADOS
    //-------------
    // Get context with jQuery - using jQuery's .get() method.
    var totalTrabajadores = $('#totalTrabajadores').val();
    var totalAcreditados = $('#totalAcreditados').val();
    var totalNoAcreditados = $('#totalNoAcreditados').val();
    var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
    var pieChart       = new Chart(pieChartCanvas)
    var PieData        = [
      {
        value    : totalTrabajadores,
        color    : '#f56954',
        highlight: '#f56954',
        label    : 'Total Trabajadores'
      },
      {
        value    : totalAcreditados,
        color    : '#00a65a',
        highlight: '#00a65a',
        label    : 'Total Trabajadores Acreditados'
      },
      {
        value    : totalNoAcreditados,
        color    : '#f39c12',
        highlight: '#f39c12',
        label    : 'Total Trabajadores No Acreditados'
      }
    ]


    var pieOptions     = {
      
      //Boolean - Whether we should show a stroke on each segment
      segmentShowStroke    : true,
      //String - The colour of each segment stroke
      segmentStrokeColor   : '#fff',
      //Number - The width of each segment stroke
      segmentStrokeWidth   : 2,
      //Number - The percentage of the chart that we cut out of the middle
      percentageInnerCutout: 50, // This is 0 for Pie charts
      //Number - Amount of animation steps
      animationSteps       : 100,
      //String - Animation easing effect
      animationEasing      : 'easeOutBounce',
      //Boolean - Whether we animate the rotation of the Doughnut
      animateRotate        : true,
      //Boolean - Whether we animate scaling the Doughnut from the centre
      animateScale         : false,
      //Boolean - whether to make the chart responsive to window resizing
      responsive           : true,
      // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
      maintainAspectRatio  : true,
      //String - A legend template
      legendTemplate       : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<segments.length; i++){%><li><span style="background-color:<%=segments[i].fillColor%>"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>'
    }
    //Create pie or douhnut chart
    // You can switch between pie and douhnut using the method below.
    pieChart.Doughnut(PieData, pieOptions);
    
   
 });


</script>
    
@endsection