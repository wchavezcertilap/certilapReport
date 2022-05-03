
@extends('admin_template')
@section('content')

<section class="content-header">
  <h1>
    Reportes y Estadisticas
  </h1>
  <ol class="breadcrumb">
    <li><a href="https://certilapreports.certilapchile.cl/public/inicio/{{base64_encode($datosUsuarios->id)}}"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Folios SSO</li>
  </ol>
</section>
</br>
           
  <div class="row">
    <div class="col-md-12">     
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title">Documentos SSO</h3>
        </div>
      </div>
    </div>
  </div>      

<!-- reporte trabajadores -->
@isset($totalDocu)
@if (count($totalDocu))
<div class="row">
    <div class="col-md-12">
      <!-- DONUT CHART globales -->
      <div class="box box-danger">
        <div class="box-header with-border">
          <h3 class="box-title">Total Documentos</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          <input type="hidden" id="totalRechazados" value="{{$totalRechazados}}">
          <input type="hidden" id="totalAprobados" value="{{$totalAprobados}}">
          <input type="hidden" id="totalVencidos" value="{{$totalVencidos}}">
          <input type="hidden" id="totalRevision" value="{{$totalRevision}}">
          <input type="hidden" id="totalAprobadosObs" value="{{$totalAprobadosObs}}">
          <input type="hidden" id="totalDocu" value="{{$totalDocu}}">
          <center><div id="leyenda"></div></br></center></br>
          <canvas id="pieChartTotal" style="height:250px"></canvas>

        </div>
        <!-- /.box-body -->
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
<link rel="stylesheet" href="{{ asset("/AdminLTE-2.3.11/plugins/datatables/Buttons-1.6.1/css/buttons.bootstrap.min.css") }}"/> 
<script src="{{ asset("/AdminLTE-2.3.11/plugins/datatables/datatables.min.js") }}""></script>




<!-- SlimScroll -->
<script src="{{ asset("/AdminLTE-2.3.11/plugins/slimScroll/jquery.slimscroll.min.js") }}""></script>
<!-- FastClick -->
<script src="{{ asset("/AdminLTE-2.3.11/plugins/fastclick/fastclick.js") }}""></script>

</script>
<script type="text/javascript">
$(function () {

  //-------------
    //- PIE CHART - DOCUMENTOS
    //-------------
    // Get context with jQuery - using jQuery's .get() method.
    var totalRechazados = $('#totalRechazados').val();
    var totalAprobados = $('#totalAprobados').val();
    var totalVencidos = $('#totalVencidos').val();
    var totalRevision = $('#totalRevision').val();
    var totalAprobadosObs = $('#totalAprobadosObs').val();
    var totalDocu = $('#totalDocu').val();
    var pieChartCanvas = $('#pieChartTotal').get(0).getContext('2d')
    var pieChart       = new Chart(pieChartCanvas)
    var PieData        = [
      {
        value    : totalRevision,
        color    : '#f56954',
        highlight: '#f56954',
        label    : 'Por Revisión'
      },
      {
        value    : totalAprobados,
        color    : '#00a65a',
        highlight: '#00a65a',
        label    : 'Aprobados'
      },
      {
        value    : totalVencidos,
        color    : '#f39c12',
        highlight: '#f39c12',
        label    : 'Expirados'
      },
      {
        value    : totalRechazados,
        color    : '#3c8dbc',
        highlight: '#3c8dbc',
        label    : 'Rechazados'
      },
      {
        value    : totalAprobadosObs,
        color    : '#27EC92',
        highlight: '#27EC92',
        label    : 'Aprobados Obs'
      },
      {
        value    : totalDocu,
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