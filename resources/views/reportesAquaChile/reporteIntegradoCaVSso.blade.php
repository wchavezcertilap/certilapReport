
@extends('admin_template')
@section('content')

<section class="content-header">
  <h1>
    Reportes Aqua Chile
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i>Reportes Aqua Chile</a></li>
    <li class="active">Reporte Integrado</li>
  </ol>
</section>
</br>
           
  <div class="row">
    <div class="col-md-12">     
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title">Reportes Integrado Control de Acceso, Verificación Laboral y Acreditación</h3>
        </div>
      </div>
    </div>
  </div>      
  @include('messages')
  <form method="POST" action="{{ route('reporteAquaIntegrado.store') }}" role="form" class="form-horizontal" id="formDocumentos">

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
                <div class="form-group">
                  <label for="periodo" class="col-sm-2 control-label">Periodo</label>
                   <div class="col-sm-4">
                        <select class="form-control" name="periodo" id="periodo" >
                         <option value="0">Seleccione Periodo Inicial</option>
                          @foreach($periodos as $periodo)
                          <option value="{{$periodo->id}}">{{mb_strtoupper($periodo->mes[0]->name)."-".mb_strtoupper($periodo->year)}}</option>
                          @endforeach
                        </select>
                  </div>

                    <label for="estdoCertificacion" class="col-sm-2 control-label">Estado Certificación</label>
                   <div class="col-sm-4">
                        <select class="form-control" name="estadoCertificacion" id="estadoCertificacion" >
                          <option value="0">Seleccione Estado de Certificación</option>
                          <option value="1">Ingresado</option>
                          <option value="2">Solicitado</option>
                          <option value="8">Completo</option>
                          <option value="6">Documentado</option>
                          <option value="9">En Proceso</option>
                          <option value="3">Aprobado</option>
                          <option value="4">No Aprobado</option>
                          <option value="5">Certificado</option>
                          <option value="10">No Conforme</option>
                        <!--   <option value="7">Histórico</option>
                          <option value="11">Inactivo</option> -->
                        </select>
                  </div>
                </div>

                <div class="form-group">
                    <label for="fechas" class="col-sm-2 control-label">Fecha de Cerificación</label>
                    <div class="col-sm-3 input-group">
                       <div class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control" id="fechaSeleccion" name="fechaSeleccion" autocomplete="off">
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

@isset($todoTrabajadores)
@if (count($todoTrabajadores) > 1)
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
                  <th>RUT</th>
                  <th>Nombres</th>
                  <th>Apellidos</th>
                  <th>RUT Principal</th>
                  <th>Empresa Principal</th>
                  <th>RUT Contratista</th>
                  <th>Empresa Contratista</th>
                  <th>Estado Certificación</th>
                  <th>Fecha Certificación</th>
                  <th>Estado Acredditación</th>
                  <th>Folio Acredditación</th>
                  <th>Fecha Ingreso</th>
                  <th>Lugar Ingreso</th>
                </tr>
                </thead>
                 <tbody>
                @foreach($todoTrabajadores as $datos)
                @isset($datos["RUT"])
                <tr>
                  <td>
                   {{$datos["RUT"]}}-{{$datos["DV"]}}
                 </td>
                 <td>
                   {{ucwords(strtolower($datos["NOMBRES"]))}}
                 </td>
                  <td>
                   {{ucwords(strtolower($datos["APELLIDOS"]))}}
                 </td>
                 <td>
                   {{$datos["MAINRUT"]}}
                 </td>
                 <td>
                   {{ucwords(strtolower($datos["MAINNOMBRE"]))}}
                 </td>
                 <td>
                   {{$datos["COMRUT"]}}
                 </td>
                  <td>
                   {{ucwords(strtolower($datos["COMNOMBRE"]))}}
                 </td>
                  <td>
                   {{$datos["ESTADOC"]}}
                 </td>
                 <td>
                 <!--  fecha certificacion -->
                 </td>
                 <td>
                  {{$datos["CERTICACIONSSO"]}}
                </td>
                 <td>
                   {{$datos["SSOID"]}}
                 </td>
                 <td>
                   {{$datos["HORA"]}}
                 </td>
                  <td>
                   {{ucwords(strtolower($datos["LUGAR"]))}}
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

            <p>No hay resultado para la busquedad solicitada.</p>
          </div>
        
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>        <!-- /.col -->
</div>
@endif


@isset ($totalDoc)
@if ($totalDoc > 0)
<div class="box box-danger">
  <input type="hidden" id="totalDocRechazados" value="{{$totalDocRechazados}}">
  <input type="hidden" id="totalDocAprobados" value="{{$totalDocAprobados}}">
  <input type="hidden" id="totalDocVencidos" value="{{$totalDocVencidos}}">
  <input type="hidden" id="totalDocRevision" value="{{$totalDocRevision}}">
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
        title: 'Reporte Integrado',   
        filename:  'Reporte_Integrado'
      },
      {
        extend:    'pdfHtml5',
        text:      '<i class="fa fa-fw fa-file-pdf-o"></i> ',
        titleAttr: 'Exportar a PDF',
        className: 'btn btn-danger',
        title: 'Reporte Integrado',   
        filename:  'Reporte_Integrado'
      },
      {
        extend:    'print',
        text:      '<i class="fa fa-print"></i> ',
        titleAttr: 'Imprimir',
        className: 'btn btn-info',
        title: 'Reporte Integrado',   
        filename:  'Reporte_Integrado'
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


  moment.locale('es') 
    //Date range picker
  $('#fechaSeleccion').daterangepicker({
    autoUpdateInput: false,
    "locale": {
      "applyLabel": "Aplicar",
      "cancelLabel": "Cancelar",
    }
  })

  $('input[name="fechaSeleccion"]').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
  });

  $('input[name="fechaSeleccion"]').on('cancel.daterangepicker', function(ev, picker) {
      $(this).val('');
  });
    //Initialize Select2 Elements
  $(".js-example-basic-multiple").select2();


  $("#empresaPrincipal").change(function(){
    var rutPrincipal = $(this).val();
    console.log( rutPrincipal);

    $.get('porContratistaAqua/'+rutPrincipal, function(data){

        var contratista = '<option value="">Seleccione Contratista</option>'
          for (var i=0; i<data.length;i++)
            contratista+='<option value="'+data[i].rut+'">'+data[i].name.toUpperCase()+'</option>';
        
          $("#empresaContratista").html(contratista);
    });
  });

  $("#botonEnviar").click(function(){

    var empresaPrincipal = $("#empresaPrincipal").val();
    var periodo = $("#periodo").val();
    var estadoCertificacion = $("#estadoCertificacion").val();
    var fechaSeleccion =$("#fechaSeleccion").val();
    console.log(fechaSeleccion);



    if(empresaPrincipal == "Seleccione Empresa Principal" || empresaPrincipal == null){
        $("#empresaPrincipal").css({"border":"1px solid red  !important"});
        toastr.error("","<br>Debe seleccionar Empresa Principal<br><br>").css({"width"  : "30%" , "text-align" : "center"});
      }else if(periodo == 0 && (fechaSeleccion==null || fechaSeleccion=="") ){

        $("#periodo").css({ "border":"1px solid red"});
        $("#fechaSeleccion").css({ "border":"1px solid red"});

        toastr.error("","<br>Debe seleccionar el periodo, o fecha de Certificación<br><br>").css({"width"  : "30%" , "text-align" : "center"});

      }else if(estadoCertificacion == 0){

        $("#estadoCertificacion").css({ "border":"1px solid red"});
        toastr.error("","<br>Debe seleccionar Estado de Certificación<br><br>").css({"width"  : "30%" , "text-align" : "center"});
      }else{

         $("#formDocumentos").submit();
      }

  })


 });


</script>
    
@endsection