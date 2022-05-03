
@extends('admin_template')
@section('content')

<section class="content-header">
  <h1>
    Reportes y Estadisticas
  </h1>
  <ol class="breadcrumb">
    <li><a href="https://certilapreports.certilapchile.cl/public/inicio/{{base64_encode($datosUsuarios->id)}}"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Reporte Certificación</li>
  </ol>
</section>
</br>
           
  <div class="row">
    <div class="col-md-12">     
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title">Reportes Certificación Empresas</h3>
        </div>
      </div>
    </div>
  </div>      
  @include('messages')
  <form method="POST" action="{{ route('reporteCertificacion.store') }}" role="form" class="form-horizontal" id="formDocumentos">

    <div class="row">
      <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Criterios de Busqueda</h3>
            </div>
      
               {{ csrf_field() }}

              <div class="box-body">
                <div class="form-group">
                  <label for="tipoBsuqueda" class="col-sm-2 control-label">Tipo de busqueda</label>
                   <div class="col-sm-4">
                    <select class="form-control" name="tipoBsuqueda" id="tipoBsuqueda">
                          <option value="0">Seleccione tipo de busqueda</option>
                          <option value="1">Periodo</option>
                          <option value="2">Fecha de Certificación</option>
                         
                      </select>
                  </div>
                </div> 
              </div> 

              <div class="box-body" id="periodos">
                <div class="form-group">
                  <label for="peridoIncical" class="col-sm-2 control-label">Perido Incial</label>
                   <div class="col-sm-4">
                    <select class="form-control" name="peridoInicio" id="peridoInicio" style="width: 100%;">
                          <option value='0'>Seleccione Periodo Inicial</option>
                          @foreach($periodos as $periodo)
                          <option value="{{$periodo->id}}">{{mb_strtoupper($periodo->mes[0]->name)."-".mb_strtoupper($periodo->year)}}</option>
                          @endforeach
                      </select>
                  </div>
               

               
                  <label for="peridoFinal" class="col-sm-2 control-label">Perido Final</label>
                   <div class="col-sm-4">
                    <select class="form-control" name="peridoFinal" id="peridoFinal" style="width: 100%;">
                          <option value='0'>Seleccione Periodo Final</option>
                          @foreach($periodos as $periodo)
                          <option value="{{$periodo->id}}">{{mb_strtoupper($periodo->mes[0]->name)."-".mb_strtoupper($periodo->year)}}</option>
                          @endforeach
                      </select>
                  </div>
                </div> 
              </div> 

              <div class="box-body" id="rangoFecha">
                <div class="form-group">
                    <label for="fechas" class="col-sm-2 control-label">Rango de Fecha</label>
                      <div class="col-sm-3 input-group">
                         <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                          </div>
                          <input type="text" class="form-control" id="fechaSeleccion" name="fechaSeleccion" value="" autocomplete="off">
                     </div>
                </div> 
              </div> 

              <div class="box-body">
                <div class="form-group">
                  <label for="empresaPrincipal" class="col-sm-2 control-label">Empresa Principal</label>
                   <div class="col-sm-4">
                    <select class="form-control js-example-basic-multiple empresaPrincipal" name="empresaPrincipal[]" id="empresaPrincipal"  multiple="multiple">
                          <option>Seleccione Empresa Principal</option>
                          <option value="1" selected="selected">Todas</option>
                          @foreach($EmpresasP as $empresa)

                          <option value="{{$empresa->rut}}">{{mb_strtoupper($empresa->name)}}</option>
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
              </div>  
                <div class="box-body">
                  <div class="form-group">
                      <label for="centro" class="col-sm-2 control-label">Centro de Costo</label>
                        <div class="col-sm-4">
                          <select class="form-control" name="centroCosto" id="centroCosto">
                           <option value="0">Seleccione Centro de Costo</option>
                        
                          </select> 
                        </div>
                 
                      <label for="tipoReporte" class="col-sm-2 control-label">Tipo de Reportes</label>
                        <div class="col-sm-4">
                          <select class="form-control" name="tipoReporte" id="tipoReporte">
                            <option value="0">Seleccione tipo de reporte</option>
                            <option value="7">Resumen de Certificación</option>
                            <option value="1">Detallado</option>
                            <option value="2">Estados por periodo</option>
                            <option value="3" disabled>Historial</option>
                            <option value="4" disabled>Tiempos</option>
                            <option value="5" disabled>Listados de personas insatisfactorias</option>
                            <option value="6" disabled>Certificación Indenizaciones</option>
                          </select> 
                        </div>
                  </div> 
              </div> 

              <div class="box-body">
                  <div class="form-group">
                      <label for="f30" class="col-sm-2 control-label">Incluir F30-1</label>
                        <div class="col-sm-4">
                          <select class="form-control" name="f30" id="f30">
                            <option  value="0">Seleccione</option>
                            <option value="1">Si</option>
                            <option value="2" selected="selected">No</option>
                          </select> 
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
<!-- reporte completo detallado sin f30 -->
@isset($cantidadDatos)
@if ($cantidadDatos > 0)
<div class="row">
    <div class="col-md-12">     
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title">Resultados</h3>
        </div>
      <div class="box-body">
           {!! $lista !!}    
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


<!-- reporte certificacion estados por periodo -->

@isset($contratistaPeriodo)
@if (count($contratistaPeriodo))
<div class="row">
    <div class="col-md-12">     
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title">Resultados</h3>
        </div>
      <div class="box-body">
              <table id="datosTabla" class="table table-bordered table-striped">
                {!! $listaTitulos !!}
               <tbody>
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

@isset ($cantidadDatos)
@if ($cantidadDatos > 0)
 <div class="row">
     <div class="col-md-12">     
       <!-- BAR CHART FOLIIOS CREADO POR MES-->
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Estado de Certificación</h3>
              <input type="hidden" name="cantidadDatos" id="cantidadDatos" value="{{$cantidadDatos}}">
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="chart">
                <canvas id="barChart" style="height:230px"></canvas>
              </div>
            </div>
            <!-- /.box-body -->
          </div>
    </div>
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

</script>
<script type="text/javascript">
$(function () {

  var cantidadDatos =  $("#cantidadDatos").val();
 
  if(cantidadDatos > 0){

    var areaChartData = {
      labels  : {!! json_encode($etiquetasEstados) !!},
      datasets: [
        {
          label               : 'Estado de Certificacion',
          fillColor           : 'rgba(60,141,188,0.9)',
          strokeColor         : 'rgba(60,141,188,0.8)',
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(220,220,220,1)',
          data                : {!! json_encode($valoresEstados) !!}
        }
      ]
    }
    var barChartCanvas                   = $('#barChart').get(0).getContext('2d')
    var barChart                         = new Chart(barChartCanvas)
    var barChartData                     = areaChartData
    barChartData.datasets.fillColor   = '#00a65a'
    barChartData.datasets.strokeColor = '#00a65a'
    barChartData.datasets.pointColor  = '#00a65a'
    var barChartOptions                  = {
      //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
      scaleBeginAtZero        : true,
      //Boolean - Whether grid lines are shown across the chart
      scaleShowGridLines      : true,
      //String - Colour of the grid lines
      scaleGridLineColor      : 'rgba(0,0,0,.05)',
      //Number - Width of the grid lines
      scaleGridLineWidth      : 1,
      //Boolean - Whether to show horizontal lines (except X axis)
      scaleShowHorizontalLines: true,
      //Boolean - Whether to show vertical lines (except Y axis)
      scaleShowVerticalLines  : true,
      //Boolean - If there is a stroke on each bar
      barShowStroke           : true,
      //Number - Pixel width of the bar stroke
      barStrokeWidth          : 2,
      //Number - Spacing between each of the X value sets
      barValueSpacing         : 5,
      //Number - Spacing between data sets within X values
      barDatasetSpacing       : 1,
      //String - A legend template
      legendTemplate          : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].fillColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
      //Boolean - whether to make the chart responsive
      responsive              : true,
      maintainAspectRatio     : true
    }

    barChartOptions.datasetFill = false
    barChart.Bar(barChartData, barChartOptions)

  }


  

  $("#periodos").hide();
  $("#rangoFecha").hide();

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
    maxDate: 90,
    autoUpdateInput: false,
    "locale": {
      "applyLabel": "Aplicar",
      "cancelLabel": "Cancelar",
    }
  })

  $('#fechaSeleccion').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('DD-MM-YYYY')+'_'+picker.endDate.format('DD-MM-YYYY'));
  });

  $('#fechaSeleccion').on('cancel.daterangepicker', function(ev, picker) {
      $(this).val('');
  });

  var table = $('#datosTabla').DataTable({  
        pageLength: 500,   
        scrollX: true, 
        orderCellsTop: true,  
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
        responsive: true,
        dom: 'Bfrtilp',

        
        buttons:[ 
      {
        extend:    'excelHtml5',
        text:      '<i class="fa fa-fw fa-file-excel-o"></i> ',
        titleAttr: 'Exportar a Excel',
        className: 'btn btn-success',
        title: 'Reporte Certificacion, periodos o fecha de certificacion: {{$periodosT}}, Empresa Principal: {{$principalesTexto}} ',   
        filename:  'Reporte_certificacion'
      },
      {
        extend:    'pdfHtml5',
        text:      '<i class="fa fa-fw fa-file-pdf-o"></i> ',
        titleAttr: 'Exportar a PDF',
        className: 'btn btn-danger',
        orientation: 'landscape',
        pageSize: 'LEGAL',
        title: 'Reporte Certificacion',   
        filename:  'Reporte_certificacion'
      },
      {
        extend:    'print',
        text:      '<i class="fa fa-print"></i> ',
        titleAttr: 'Imprimir',
        className: 'btn btn-info',
        title: 'Reporte Certificacion',   
        filename:  'Reporte_certificacion'
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
    $.get('porContratista/'+rutPrincipal, function(data){

        var contratista = '<option value="">Seleccione Contratista</option>'
          for (var i=0; i<data.length;i++)
            contratista+='<option value="'+data[i].rut+'">'+data[i].name.toUpperCase()+'</option>';
        
          $("#empresaContratista").html(contratista);
    });
  });

   $("#empresaContratista").change(function(){
    var rutContratista = $(this).val();
    var rutPrincipal = $("#empresaPrincipal").val();
    var peridoInicio = $("#peridoInicio").val();
    if(peridoInicio == ""){

      peridoInicio = 0;

    }
    var peridoFinal = $("#peridoFinal").val();

    if(peridoFinal == 0){

      peridoFinal = 0;

    }
    var fechaSeleccion = $("#fechaSeleccion").val();

     if(fechaSeleccion == 0){

      fechaSeleccion = 0;

    }

    $.get('porCentroCosto/'+rutContratista+'/'+rutPrincipal+'/'+peridoInicio+'/'+peridoFinal+'/'+fechaSeleccion, function(data){

        var centroCosto = '<option value="">Seleccione Centro de Costo</option>'
          for (var i=0; i<data.length;i++)
            centroCosto+='<option value="'+data[i].id+'">'+data[i].center.toUpperCase()+'</option>';
        
          $("#centroCosto").html(centroCosto);
    });
  });

  $("#tipoBsuqueda").change(function(){
    var tipoBsuqueda = $(this).val();
    if(tipoBsuqueda == 1){
      $("#periodos").show();
      $("#rangoFecha").hide();
    }
    if(tipoBsuqueda == 2){
      $("#rangoFecha").show();
      $("#periodos").hide();

    }
    
  });


$("#botonEnviar").click(function(){

    var empresaPrincipal = $("#empresaPrincipal").val();
    var tipoBsuqueda = $("#tipoBsuqueda").val();
    var tipoReporte = $("#tipoReporte").val();
    var f30 = $("#f30").val();
    var valida = 0;
    if(tipoBsuqueda == 0){
       $("#tipoBsuqueda").css({"border":"1px solid red  !important"});
        toastr.error("","<br>Debe seleccionar Tipo de Busqueda<br><br>").css({"width"  : "30%" , "text-align" : "center"});
        valida = 1;
     
    }
    if(tipoBsuqueda == 1){

      var peridoInicio = $("#peridoInicio").val();
      var peridoFinal = $("#peridoFinal").val();
      if(peridoInicio== 0){
        $("#peridoInicio").css({"border":"1px solid red  !important"});
        toastr.error("","<br>Debe seleccionar Periodo Inicial<br><br>").css({"width"  : "30%" , "text-align" : "center"});
         valida = 1;
      }
      if(peridoFinal== 0){
        $("#peridoFinal").css({"border":"1px solid red  !important"});
        toastr.error("","<br>Debe seleccionar Periodo Final<br><br>").css({"width"  : "30%" , "text-align" : "center"});
         valida = 1;
      }
      if(peridoInicio > peridoFinal){
           $("#peridoInicio").css({"border":"1px solid red  !important"});
        toastr.error("","<br>El Mes de Periodo Inicial no puede ser un Mes Mayor al Periodo Final<br><br>").css({"width"  : "30%" , "text-align" : "center"});
        valida = 1;

      }
      if(peridoFinal < peridoInicio){
           $("#peridoInicio").css({"border":"1px solid red  !important"});
        toastr.error("","<br>El Mes de Periodo Final no puede ser un Mes Menor al Periodo Inicial<br><br>").css({"width"  : "30%" , "text-align" : "center"});
        valida = 1;
      }
    }if(tipoBsuqueda == 2){

      var fechaSeleccion = $("#fechaSeleccion").val();
 
      if(fechaSeleccion == "" || fechaSeleccion == null ){
        $("#fechaSeleccion").css({"border":"1px solid red  !important"});
        toastr.error("","<br>Debe seleccionar Fecha<br><br>").css({"width"  : "30%" , "text-align" : "center"});
         valida = 1;
      }
      
    }if(empresaPrincipal == "Seleccione Empresa Principal" || empresaPrincipal == null){
        $("#empresaPrincipal").css({"border":"1px solid red  !important"});
        toastr.error("","<br>Debe seleccionar Empresa Principal<br><br>").css({"width"  : "30%" , "text-align" : "center"});
         valida = 1;
    }if(tipoReporte == 0){
        $("#tipoReporte").css({"border":"1px solid red  !important"});
        toastr.error("","<br>Debe seleccionar un tipo de reporte<br><br>").css({"width"  : "30%" , "text-align" : "center"});
         valida = 1;
    }if(f30 == 0){
        $("#f30").css({"border":"1px solid red  !important"});
        toastr.error("","<br>Debe seleccionar la opcion de F30-1<br><br>").css({"width"  : "30%" , "text-align" : "center"});
         valida = 1;
    }
    if(valida == 0){

        $("#formDocumentos").submit();
      }

  })

    
    
   
 });


</script>
    
@endsection