
@extends('admin_template')
@section('content')

<section class="content-header">
  <h1>
    Reportes y Estadisticas
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Reporte Documentos</li>
  </ol>
</section>
</br>
           
  <div class="row">
    <div class="col-md-12">     
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title">Reportes Documentos.</h3>
        </div>
      </div>
    </div>
  </div>      
  @include('messages')
  <form method="POST" action="{{ route('documentoReporte.store') }}" role="form" class="form-horizontal" id="formDocumentos">
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
                        <select class="form-control js-example-basic-multiple" name="empresaPrincipal[]" id="empresaPrincipal" >
                          <option>Seleccione Empresa Principal</option>
                          <option value ="1">Todas</option>
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

                <div class="form-group">
                   <label for="folio" class="col-sm-2 control-label">Folios</label>
                   <div class="col-sm-4">
                        <select class="form-control js-example-basic-multiple" name="folio" id="folio" >
                          <option>Seleccione folio</option>
                        
                      </select>
                  </div>
               
                  <label for="proyecto" class="col-sm-2 control-label">Proyectos</label>
                   <div class="col-sm-4">
                        <select class="form-control js-example-basic-multiple" name="proyecto" id="proyecto">
                          <option>Seleccione proyectos</option>
                         
                      </select>
                  </div>
                </div>

                <div class="form-group">
                   <label for="folio" class="col-sm-2 control-label">Tipo de Informe</label>
                   <div class="col-sm-4">
                        <select class="form-control" name="tipoInforme" id="tipoInforme" >
                          <option value="0">Seleccione Informe</option>
                          <option value="1">Sin Documentos</option>
                          <option value="2">Vencidos</option>
                          <option value="3">Aprobados</option>
                          <option value="4">Rechazados</option>
                          <option value="5">Ingresados</option>
                          <option value="6">Pendiente de Revisión</option>
                      </select>
                  </div>
            
                  <label for="fechas" class="col-sm-2 control-label">Rango de Fecha</label>
                  <div class="col-sm-3 input-group">
                     <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <input type="text" class="form-control" id="fechaSeleccion" name="fechaSeleccion" value="" autocomplete="off">
                 </div>
                </div>
              </div> 

              <div class="box-footer">
                
                <button type="button" id="botonEnviar" class="btn btn-info pull-right">buscar</button>
              </div>
        </div>     
      </div>
    </div>
</form>

@isset ($activaLista)
@if ($activaLista >= 1)
<div class="row">
    <div class="col-md-12">     
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title">Resultados</h3>
        </div>
        <div class="box-body">
          <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="info-box">
              <span class="info-box-icon bg-yellow"><i class="fa fa-group"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Trajadores de las Empresas</span>
                <span class="info-box-number">{{$totalTB}}</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
        <!-- /.col -->
          <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="info-box">
              <span class="info-box-icon bg-green"><i class="fa fa-file"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">total documentos</span>
                <span class="info-box-number">{{$totalDoc}}</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
        </div>
      <div class="box-body">
                {!! $listaCuerpo !!}
                </tbody>
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


@isset ($totalDoc)
@if ($totalDoc > 1)
<div class="box box-danger">
  <input type="hidden" id="totalDocRechazados" value="{{$totalDocRechazados}}">
  <input type="hidden" id="totalDocAprobados" value="{{$totalDocAprobados}}">
  <input type="hidden" id="totalDocVencidos" value="{{$totalDocVencidos}}">
  <input type="hidden" id="totalDocRevision" value="{{$totalDocRevision}}">
   <input type="hidden" id="totalDocAprobadosObs" value="{{$totalDocAprobadosObs}}">
  <input type="hidden" id="totalDoc" value="{{$totalDoc}}">
            <div class="box-header with-border">
              <h3 class="box-title">Grafica de documentos</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <center><div id="leyenda"></div></br></center></br>
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
<link rel="stylesheet" href="{{ asset("/AdminLTE-2.3.11/plugins/datatables/Buttons-1.6.1/css/buttons.bootstrap.min.css") }}"/> 
<script src="{{ asset("/AdminLTE-2.3.11/plugins/datatables/datatables.min.js") }}""></script>




<!-- SlimScroll -->
<script src="{{ asset("/AdminLTE-2.3.11/plugins/slimScroll/jquery.slimscroll.min.js") }}""></script>
<!-- FastClick -->
<script src="{{ asset("/AdminLTE-2.3.11/plugins/fastclick/fastclick.js") }}""></script>


<script type="text/javascript">
$(function () {

  var table = $('table.display').DataTable({  
        pageLength: 50,   
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
        title: 'Reporte Documentos',   
        filename:  'Reporte_Documentos'
      },
      {
        extend:    'pdfHtml5',
        text:      '<i class="fa fa-fw fa-file-pdf-o"></i> ',
        titleAttr: 'Exportar a PDF',
        className: 'btn btn-danger',
        orientation: 'landscape',
        pageSize: 'LEGAL',
        title: 'Reporte Documentos',   
        filename:  'Reporte_Documentos'
      },
      {
        extend:    'print',
        text:      '<i class="fa fa-print"></i> ',
        titleAttr: 'Imprimir',
        className: 'btn btn-info',
        title: 'Reporte Documentos',   
        filename:  'Reporte_Documentos'
      },
    ]         
  })

  toastr.options = {
  "debug": false,
  "positionClass": "toast-top-center",
  "onclick": null,
  "fadeIn": 300,
  "fadeOut": 1000,
  "timeOut": 5000,
  "extendedTimeOut": 1000
  }


  moment.locale('es') 
    //Date range picker
  $('#fechaSeleccion').daterangepicker({
    autoUpdateInput: false,
    "locale": {
      "applyLabel": "Aplicar",
      "cancelLabel": "Cancelar",
    }
  })


  $('#fechaSeleccion').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('DD-MM-YYYY')+' '+picker.endDate.format('DD-MM-YYYY'));
  });

  $('#fechaSeleccion').on('cancel.daterangepicker', function(ev, picker) {
      $(this).val('');
  });

    //Initialize Select2 Elements
  $(".js-example-basic-multiple").select2();


  $("#empresaPrincipal").change(function(){
    var rutPrincipal = $(this).val();
    $.get('porContratista/'+rutPrincipal, function(data){

        var contratista = '<option value="">Seleccione Contratista</option>'
          for (var i=0; i<data.length;i++)
            contratista+='<option value="'+data[i].rut+'">'+data[i].name.toUpperCase()+'</option>';
        
          $("#empresaContratista").html(contratista);
    });

    $.get('porFolio/'+rutPrincipal, function(data){

        var folio = '<option value="">Seleccione Folio</option>'
          for (var x=0; x<data.length;x++)
            folio+='<option value="'+data[x].id+'">'+data[x].id+'</option>';
        
          $("#folio").html(folio);
    });

     $.get('porProyecto/'+rutPrincipal, function(data){

        var proyecto = '<option value="">Seleccione Proyecto</option>'
          for (var a=0; a<data.length;a++)
            proyecto+='<option value="'+data[a].sso_project+'">'+data[a].sso_project.toUpperCase()+'</option>';
        
          $("#proyecto").html(proyecto);
    });
  });

  $("#botonEnviar").click(function(){

    var empresaPrincipal = $("#empresaPrincipal").val();
 
    var tipoInforme = $("#tipoInforme").val();
    var fechaSeleccion = $("#fechaSeleccion").val();

    if(empresaPrincipal == "Seleccione Empresa Principal"){
        $("#empresaPrincipal").css({ "border":"1px solid red"});
        toastr.error("","<br>Debe seleccionar Empresa Principal<br><br>").css({"width"  : "30%" , "text-align" : "center"});
      }else if(tipoInforme == 0){

         $("#tipoInforme").css({ "border":"1px solid red"});
        toastr.error("","<br>Debe seleccionar Tipo de Informe<br><br>").css({"width"  : "30%" , "text-align" : "center"});
      }else if(fechaSeleccion == "" || fechaSeleccion == null){

         $("#fechaSeleccion").css({ "border":"1px solid red"});
        toastr.error("","<br>Debe seleccionar un rango de fechas<br><br>").css({"width"  : "30%" , "text-align" : "center"});
      }else{

         $("#formDocumentos").submit();

      }

  })





    /* ChartJS
     * -------
     * Here we will create a few charts using ChartJS
     */



    //-------------
    //- PIE CHART -
    //-------------
    // Get context with jQuery - using jQuery's .get() method.
    var totalDocRechazados = $('#totalDocRechazados').val();
    var totalDocAprobados = $('#totalDocAprobados').val();
    var totalDocVencidos = $('#totalDocVencidos').val();
    var totalDocRevision = $('#totalDocRevision').val();
    var totalDocAprobadosObs = $('#totalDocAprobadosObs').val();
    var totalDoc = $('#totalDoc').val();
    var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
    var pieChart       = new Chart(pieChartCanvas)
    var PieData        = [
      {
        value    : totalDocRevision,
        color    : '#f56954',
        highlight: '#f56954',
        label    : 'Por Revisión'
      },
      {
        value    : totalDocAprobados,
        color    : '#00a65a',
        highlight: '#00a65a',
        label    : 'Aprobados'
      },
      {
        value    : totalDocVencidos,
        color    : '#f39c12',
        highlight: '#f39c12',
        label    : 'Expirados'
      },
      {
        value    : totalDocRechazados,
        color    : '#3c8dbc',
        highlight: '#3c8dbc',
        label    : 'Rechazados'
      },
      {
        value    : totalDocAprobadosObs,
        color    : '#27EC92',
        highlight: '#27EC92',
        label    : 'Aprobados Obs'
      },
      {
        value    : totalDoc,
        color    : '#27ECBF',
        highlight: '#27ECBF',
        label    : 'total Documentos'
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
      legendTemplate       : '<table class="table"><tr><% for (var i=0; i<segments.length; i++){%><td style="background-color:<%=segments[i].fillColor%>"><%if(segments[i].label){%><%=segments[i].label%><%}%>: <%if(segments[i].value){%><%=segments[i].value%><%}%> </td><%}%></tr></table>'
    }
    //Create pie or douhnut chart
    // You can switch between pie and douhnut using the method below.
    var pie = pieChart.Doughnut(PieData, pieOptions);
    document.getElementById('leyenda').innerHTML = pie.generateLegend();



 });


</script>
    
@endsection