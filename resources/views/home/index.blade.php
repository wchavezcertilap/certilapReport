
@extends('admin_template')

@section('content')
<input type="hidden" id="certificacion" name="certificacion" value="{{$certificacion}}">
<input type="hidden" id="graficaCertificacion" name="graficaCertificacion" value="{{$graficaCertificacion}}">
<input type="hidden" id="ssograficos" name="ssograficos" value="{{$ssograficos}}">


<section class="content-header">
  <h1>
    Reportes y Estadisticas
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Reportes y Estadisticas</li>
  </ol>
</section>

  {{ csrf_field() }}
  
@if ($ssograficos == 1)            
  <div class="row">
    <div class="col-md-12">     
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title">Estadisticas SSO.</h3>
          
        </div>
      </div>
    </div>
  </div> 
    
@isset($totalFolios)
  <div class="row">
     <div class="col-md-12"> 
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3>{{$totalFolios}}</h3>
              <input type="hidden" name="totalFolios" value="{{$totalFolios}}">
              <p>Total Folios Activos</p>
            </div>
            <div class="icon">
              <i class="fa fa-folder"></i>
            </div>
              <a href="ssoFolios/{{base64_encode($datosUsuarios->id)}}" class="small-box-footer">
                Mas Info <i class="fa fa-arrow-circle-right"></i>
              </a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3>{{$totalDocuementos}}</h3>
              <input type="hidden" name="totalDocuementos" value="{{$totalDocuementos}}">
              <p>Total Documentos</p>
            </div>
            <div class="icon">
              <i class="fa fa-file"></i>
            </div>
             <a href="ssoDocumentos/{{base64_encode($datosUsuarios->id)}}" class="small-box-footer">
                Mas Info <i class="fa fa-arrow-circle-right"></i>
              </a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3>{{$totalTrabajadores}}</h3>
              <input type="hidden" name="totalTrabajadores" value="{{$totalTrabajadores}}">
              <p>Total Trabajadores</p>
            </div>
            <div class="icon">
              <i class="ion ion-person-add"></i>
            </div>
            <a href="ssoTrabajadores/{{base64_encode($datosUsuarios->id)}}" class="small-box-footer">
              Mas Info <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <h3>{{$totalEmpresasPriSSO}}</h3>
               <input type="hidden" name="totalEmpresasPriSSO" value="{{$totalEmpresasPriSSO}}">
              <p>Total Empresas</p>
            </div>
            <div class="icon">
              <i class="fa fa-industry"></i>
            </div>
              <a href="ssoEmpresas/{{base64_encode($datosUsuarios->id)}}" class="small-box-footer">
              Mas Info <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
      </div>
  </div>

  <div class="row">
     <div class="col-md-12">     
       <!-- BAR CHART FOLIIOS CREADO POR MES-->
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Folios Y Trabajadores creados por mes año {{$anio}}</h3>

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


  <div class="row">
    <div class="col-md-6">
      <!-- DONUT CHART globales -->
      <div class="box box-danger">
        <div class="box-header with-border">
          <h3 class="box-title">Documentos globales del mes de {{$mes}}-{{$anio}}</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          <input type="hidden" id="totalDocRechazados" value="{{$totalDocRechazados}}">
          <input type="hidden" id="totalDocAprobados" value="{{$totalDocAprobados}}">
          <input type="hidden" id="totalDocVencidos" value="{{$totalDocVencidos}}">
          <input type="hidden" id="totalDocRevision" value="{{$totalDocRevision}}">
          <input type="hidden" id="totalDoc" value="{{$totalDoc}}">
          <center><div id="leyendaGlobal"></div></br></center>
          <canvas id="pieChart" style="height:250px"></canvas>
        </div>
        <!-- /.box-body -->
      </div>
    </div>

    <div class="col-md-6">
      <!-- DONUT CHART globales -->
      <div class="box box-danger">
        <div class="box-header with-border">
          <h3 class="box-title">Documentos Trabajadores Mes de {{$mes}}-{{$anio}}</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          <input type="hidden" id="totalDocRechazadosTrabajadores" value="{{$totalDocRechazadosTrabajadores}}">
          <input type="hidden" id="totalDocAprobadosTrabajadores" value="{{$totalDocAprobadosTrabajadores}}">
          <input type="hidden" id="totalDocVencidosTrabajadores" value="{{$totalDocVencidosTrabajadores}}">
          <input type="hidden" id="totalDocRevisionTrabajadores" value="{{$totalDocRevisionTrabajadores}}">
          <input type="hidden" id="totalDocTrabajadores" value="{{$totalDocTrabajadores}}">
          <center><div id="leyendaTrabajador"></div></br></center>
          <canvas id="pieChartworker" style="height:250px"></canvas>
        </div>
        <!-- /.box-body -->
      </div>
    </div>
  </div>         
@endisset
@endif
<!-- /.row -->
<div class="row">
    <div class="col-md-12">     
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title">Estadisticas Certificación Laboral.</h3>
          
        </div>
      </div>
    </div>
  </div>
  <div class="row">
     <div class="col-md-12"> 
        <div class="col-lg-6 col-xs-9">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3>{{$cantidadContratistas}}</h3>
              <input type="hidden" name="totalFolios" value="{{$totalFolios}}">
              <p>Total Empresas Contratistas</p>
            </div>
            <div class="icon">
              <i class="fa fa-industry"></i>
            </div>
           <!--  <a href="#" class="small-box-footer">
              More info <i class="fa fa-arrow-circle-right"></i>
            </a> -->
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-6 col-xs-9">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3>{{$cantidadSubContratistas}}</h3>
              <input type="hidden" name="totalDocuementos" value="{{$totalDocuementos}}">
              <p>total Sub Contratistas</p>
            </div>
            <div class="icon">
              <i class="fa fa-suitcase"></i>
            </div>
           <!--  <a href="#" class="small-box-footer">
              More info <i class="fa fa-arrow-circle-right"></i>
            </a> -->
          </div>
        </div>
      </div>
  </div>

  <div class="row">
     <div class="col-md-12">     
       <!-- BAR CHART FOLIIOS CREADO POR MES-->
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Empresa Certificadas y trabajadores Ingresados en el {{$anio}}</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="chart">
                <canvas id="barChartC" style="height:230px"></canvas>
              </div>
            </div>
            <!-- /.box-body -->
          </div>
    </div>
  </div> 

  <div class="row">
    <div class="col-md-12">
      <!-- DONUT CHART globales -->
      <div class="box box-danger">
        <div class="box-header with-border">
          <h3 class="box-title">Historico de Estados de Certificación</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          <input type="hidden" id="estadoIngresado" value="{{$estadoIngresado}}">
          <input type="hidden" id="estadoSolicitado" value="{{$estadoSolicitado}}">
          <input type="hidden" id="estadoAprobado" value="{{$estadoAprobado}}">
          <input type="hidden" id="estadoNoAprobado" value="{{$estadoNoAprobado}}">
          <input type="hidden" id="estadoCertificado" value="{{$estadoCertificado}}">
          <input type="hidden" id="estadoDocumentado" value="{{$estadoDocumentado}}">
          <input type="hidden" id="estadoHistorico" value="{{$estadoHistorico}}">
          <input type="hidden" id="estadoCompleto" value="{{$estadoCompleto}}">
          <input type="hidden" id="estadoProceso" value="{{$estadoProceso}}">
          <input type="hidden" id="estadoNoConforme" value="{{$estadoNoConforme}}">
          <input type="hidden" id="estadoInactivo" value="{{$estadoInactivo}}">
          <canvas id="pieChartworkerCertificacion" style="height:250px"></canvas>
        </div>
        <!-- /.box-body -->
      </div>
    </div>
  </div>   
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
<link rel="stylesheet" href="{{ asset("/AdminLTE-2.3.11/plugins/datatables/datatable/Buttons-1.6.1/css/buttons.bootstrap.min.css") }}"/> 
<script src="{{ asset("/AdminLTE-2.3.11/plugins/datatables/datatables.min.js") }}""></script>




<!-- SlimScroll -->
<script src="{{ asset("/AdminLTE-2.3.11/plugins/slimScroll/jquery.slimscroll.min.js") }}""></script>
<!-- FastClick -->
<script src="{{ asset("/AdminLTE-2.3.11/plugins/fastclick/fastclick.js") }}""></script>

<script type="text/javascript">
$(function () {

  var ssograficos = $("#ssograficos").val();

  if(ssograficos==1){
    var areaChartData = {
      labels  : {!! json_encode($etiquetaMes) !!},
      datasets: [
        {
          label               : 'FOLIOS',
          fillColor           : 'rgba(210, 214, 222, 1)',
          strokeColor         : 'rgba(210, 214, 222, 1)',
          pointColor          : 'rgba(210, 214, 222, 1)',
          pointStrokeColor    : '#c1c7d1',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(220,220,220,1)',
          data                : {!! json_encode($valoresMes) !!}
        },
        {
          label               : 'TRABAJADORES',
          fillColor           : 'rgba(60,141,188,0.9)',
          strokeColor         : 'rgba(60,141,188,0.8)',
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : {!! json_encode($valoresTrabajador) !!}
        }
      ]
    }
    var barChartCanvas                   = $('#barChart').get(0).getContext('2d')
    var barChart                         = new Chart(barChartCanvas)
    var barChartData                     = areaChartData
    barChartData.datasets[1].fillColor   = '#00a65a'
    barChartData.datasets[1].strokeColor = '#00a65a'
    barChartData.datasets[1].pointColor  = '#00a65a'
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

    //-------------
    //- PIE CHART - GLOBALES
    //-------------
    // Get context with jQuery - using jQuery's .get() method.
    var totalDocRechazados = $('#totalDocRechazados').val();
    var totalDocAprobados = $('#totalDocAprobados').val();
    var totalDocVencidos = $('#totalDocVencidos').val();
    var totalDocRevision = $('#totalDocRevision').val();
    var totalDocRevision = $('#totalDocRevision').val();
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
      legendTemplate       : '<table class="table" style="width:80%"><tr><% for (var i=0; i<segments.length; i++){%><td style="background-color:<%=segments[i].fillColor%>"><%if(segments[i].label){%><%=segments[i].label%><%}%>: <%if(segments[i].value){%><%=segments[i].value%><%}%> </td><%}%></tr></table>'
    }
    //Create pie or douhnut chart
    // You can switch between pie and douhnut using the method below.
    var pie = pieChart.Doughnut(PieData, pieOptions);
    document.getElementById('leyendaGlobal').innerHTML = pie.generateLegend();



    //-------------
    //- PIE CHART - TRABAJDORES
    //-------------
    // Get context with jQuery - using jQuery's .get() method.
    var totalDocRechazadosTrabajadores = $('#totalDocRechazadosTrabajadores').val();
    var totalDocAprobadosTrabajadores = $('#totalDocAprobadosTrabajadores').val();
    var totalDocVencidosTrabajadores = $('#totalDocVencidosTrabajadores').val();
    var totalDocRevisionTrabajadores = $('#totalDocRevisionTrabajadores').val();
    var totalDocTrabajadores = $('#totalDocTrabajadores').val();
    var pieChartCanvas = $('#pieChartworker').get(0).getContext('2d')
    var pieChart       = new Chart(pieChartCanvas)
    var PieData        = [
      {
        value    : totalDocRevisionTrabajadores,
        color    : '#f56954',
        highlight: '#f56954',
        label    : 'Por Revisión'
      },
      {
        value    : totalDocAprobadosTrabajadores,
        color    : '#00a65a',
        highlight: '#00a65a',
        label    : 'Aprobados'
      },
      {
        value    : totalDocVencidosTrabajadores,
        color    : '#f39c12',
        highlight: '#f39c12',
        label    : 'Expirados'
      },
      {
        value    : totalDocRechazadosTrabajadores,
        color    : '#3c8dbc',
        highlight: '#3c8dbc',
        label    : 'Rechazados'
      },
      {
        value    : totalDocTrabajadores,
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
      legendTemplate       : '<table class="table" style="width:80%"><tr><% for (var i=0; i<segments.length; i++){%><td style="background-color:<%=segments[i].fillColor%>"><%if(segments[i].label){%><%=segments[i].label%><%}%>: <%if(segments[i].value){%><%=segments[i].value%><%}%> </td><%}%></tr></table>'
    }
    //Create pie or douhnut chart
    // You can switch between pie and douhnut using the method below.
    var pie = pieChart.Doughnut(PieData, pieOptions);
    document.getElementById('leyendaTrabajador').innerHTML = pie.generateLegend();
  }
  var graficaCertificacion = $("#graficaCertificacion").val();
  if(graficaCertificacion==1){
    var areaChartDataC = {
      labels  : {!! json_encode($etiquetaMesCertificacion) !!},
      datasets: [
        {
          label               : 'Empresas',
          fillColor           : 'rgba(0,116,190)',
          strokeColor         : 'rgba(0,116,190)',
          pointColor          : 'rgba(0,116,190)',
          pointStrokeColor    : '#c1c7d1',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(220,220,220,1)',
          data                : {!! json_encode($valoresMesCerticacion) !!}
        },
        {
          label               : 'TRABAJADORES',
          fillColor           : 'rgba(60,141,188,0.9)',
          strokeColor         : 'rgba(60,141,188,0.8)',
          pointColor          : '#3bba5f',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : {!! json_encode($valoresTrabajadoresCerticacion) !!}
        }
      ]
    }
      var barChartCanvasC                   = $('#barChartC').get(0).getContext('2d')
      var barChartC                         = new Chart(barChartCanvasC)
      var barChartDataC                     = areaChartDataC
      barChartDataC.datasets[1].fillColor   = '#00a65a'
      barChartDataC.datasets[1].strokeColor = '#00a65a'
      barChartDataC.datasets[1].pointColor  = '#00a65a'
      var barChartOptionsC                  = {
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

    barChartOptionsC.datasetFill = false
    barChartC.Bar(barChartDataC, barChartOptionsC)


    ///// estado de documentos ///

    //-------------
    //- PIE CHART - TRABAJDORES
    //-------------
    // Get context with jQuery - using jQuery's .get() method.
    var estadoIngresado = $('#estadoIngresado').val();
    var estadoSolicitado = $('#estadoSolicitado').val();
    var estadoAprobado = $('#estadoAprobado').val();
    var estadoNoAprobado = $('#estadoNoAprobado').val();
    var estadoCertificado = $('#estadoCertificado').val();

    var estadoDocumentado = $('#estadoDocumentado').val();
    var estadoHistorico = $('#estadoHistorico').val();
    var estadoCompleto = $('#estadoCompleto').val();
    var estadoProceso = $('#estadoProceso').val();
    var estadoNoConforme = $('#estadoNoConforme').val();

    var estadoInactivo = $('#estadoInactivo').val();
    

    var pieChartCanvas = $('#pieChartworkerCertificacion').get(0).getContext('2d')
    var pieChart       = new Chart(pieChartCanvas)
    var PieData        = [
      {
        value    : estadoIngresado,
        color    : '#2EFE2E',
        highlight: '#2EFE2E',
        label    : 'Estado Ingresado'
      },
      {
        value    : estadoSolicitado,
        color    : '#045FB4',
        highlight: '#045FB4',
        label    : 'Estado Solicitado'
      },
      {
        value    : estadoAprobado,
        color    : '#82FA58',
        highlight: '#82FA58',
        label    : 'Estado Aprobado'
      },
      {
        value    : estadoNoAprobado,
        color    : '#DF3A01',
        highlight: '#DF3A01',
        label    : 'Estado No Aprobado'
      },
      {
        value    : estadoCertificado,
        color    : '#088A08',
        highlight: '#088A08',
        label    : 'Estado Certificado'
      },
      {
        value    : estadoDocumentado,
        color    : '#0B2161',
        highlight: '#0B2161',
        label    : 'Estado Documentado'
      },
      {
        value    : estadoHistorico,
        color    : '#6E6E6E',
        highlight: '#6E6E6E',
        label    : 'Estado Historico'
      },
      {
        value    : estadoCompleto,
        color    : '#21610B',
        highlight: '#21610B',
        label    : 'Estado Completo'
      },
      {
        value    : estadoProceso,
        color    : '#2E9AFE',
        highlight: '#2E9AFE',
        label    : 'Estado Proceso'
      },
      {
        value    : estadoNoConforme,
        color    : '#FF0000',
        highlight: '#FF0000',
        label    : 'Estado No Conforme'
      },
      {
        value    : estadoInactivo,
        color    : '#A4A4A4',
        highlight: '#A4A4A4',
        label    : 'Estado Inactivo'
      },

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

    
  }
})
</script>
    
@endsection