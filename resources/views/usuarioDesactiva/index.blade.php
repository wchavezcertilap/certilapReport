
@extends('admin_template')
@section('content')

<section class="content-header">
  <h1>
    Usuarios
  </h1>
  <ol class="breadcrumb">
    <li><a href="https://certilapreports.certilapchile.cl/public/inicio/{{base64_encode($datosUsuarios->id)}}"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Desactivar | Eliminar usuarios</li>
  </ol>
</section>
</br>
           
  <div class="row">
    <div class="col-md-12">     
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title">Desactivar | Eliminar usuarios</h3>
        </div>
      </div>
    </div>
  </div>      
  @include('messages')
  <form method="POST" action="{{ route('usuarioDesactiva.store') }}" role="form" class="form-horizontal" id="formUsuario">

    <div class="row">
      <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Criterios de Busqueda</h3>
            </div>
      
               {{ csrf_field() }}

              <div class="box-body">
                <div class="form-group">
                  <label for="tipoBsuqueda" class="col-sm-2 control-label">Tipo de usuarios</label>
                   <div class="col-sm-4">
                    <select class="form-control" name="tipoBsuqueda" id="tipoBsuqueda">
                          <option value="0">Seleccione tipo de usuario</option>
                          <option value="1">Empresa Principal</option>
                          <option value="2">Empresa Contratista</option>
                         
                      </select>
                  </div>

                  <label for="estadoUsuario" class="col-sm-2 control-label">Estado usuario</label>
                   <div class="col-sm-4">
                    <select class="form-control" name="estadoUsuario" id="estadoUsuario">
                          <option value="0">Seleccione estado de usuario</option>
                          <option value="1">Activo</option>
                          <option value="2">Inactivo</option>
                         
                      </select>
                  </div>
                </div> 
              </div> 

              <div class="box-body">
                <div class="form-group">
                  <label for="empresaPrincipal" class="col-sm-2 control-label">Empresa Principal</label>
                   <div class="col-sm-4">
                    <select class="form-control js-example-basic-multiple empresaPrincipal" name="empresaPrincipal[]" id="empresaPrincipal"  multiple="multiple">
                          <option value="1" selected="selected">Seleccione Empresa Principal</option>
                          
                          @foreach($EmpresasP as $empresa)

                          <option value="{{$empresa->rut}}">{{mb_strtoupper($empresa->name)}}</option>
                          @endforeach
                      </select>
                  </div>
                </div> 
              </div> 

               <div class="box-body">
                <div class="form-group">
                    <label for="contratista" class="col-sm-2 control-label">Contratistas</label>
                      <div class="col-sm-4">
                      <select class="form-control js-example-basic-multiple" name="contratista[]" id="contratista"  multiple="multiple" size="100%">
                          <option value="1" selected="selected">Seleccione Empresa Contratista</option>
                          @foreach($Contratistas as $contratista)

                          <option value="{{$contratista->rut}}">{{mb_strtoupper($contratista->name)}}</option>
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
              <div class="box-footer">
                
                <button type="button" id="botonEnviar" class="btn btn-info pull-right">buscar</button>
              </div>
        </div>     
      </div>
    </div>
</form>
<!-- reporte completo detallado sin f30 -->
@isset($USUARIOS)
@if ($USUARIOS > 0)
<div class="row">
    <div class="col-md-12">     
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title">Resultados</h3>
        </div>
      <div class="box-body">
        <form method="POST" action="{{ route('usuarioDesactiva.desactiva') }}" class="form-horizontal" id="desactivar" autocomplete="off">
      {{ csrf_field() }} 
      
           {!! $lista !!}  
        </form>  
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
 
  $('#empresaPrincipal').attr("disabled", true); 
  $('#contratista').attr("disabled", true); 

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

  $("#checkTodos").change(function () {
      $("input:checkbox").prop('checked', $(this).prop("checked"));
  });

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
        title: 'Reporte Certificacion',   
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


  $("#tipoBsuqueda").change(function(){
    var tipoBsuqueda = $(this).val();
    if(tipoBsuqueda == 1){
      $('#empresaPrincipal').attr("disabled", false); 
      $('#contratista').attr("disabled", true); 
     
    }
    if(tipoBsuqueda == 2){
       $('#contratista').attr("disabled", false); 
      $('#empresaPrincipal').attr("disabled", true); 
    }
    
  });


  $("#botonEnviar").click(function(){

    var empresaPrincipal = $("#empresaPrincipal").val();
    var fechaSeleccion = $("#fechaSeleccion").val();
    var contratista = $("#contratista").val();
    var estadoUsuario = $("#estadoUsuario").val();
    var error = 0;
    if(tipoBsuqueda == 0){
       $("#tipoBsuqueda").css({"border":"1px solid red  !important"});
        toastr.error("","<br>Debe seleccionar Tipo de Usuario<br><br>").css({"width"  : "30%" , "text-align" : "center"});
      var error = 1;
    }if(estadoUsuario == 0){
       $("#estadoUsuario").css({"border":"1px solid red  !important"});
        toastr.error("","<br>Debe seleccionar el estado del usuario<br><br>").css({"width"  : "30%" , "text-align" : "center"});
      var error = 1;
    }
    if(tipoBsuqueda == 1){

      if(empresaPrincipal == "Seleccione Empresa Principal" || empresaPrincipal == null || empresaPrincipal == 1){
        $("#empresaPrincipal").css({"border":"1px solid red  !important"});
        toastr.error("","<br>Debe seleccionar Empresa Principal<br><br>").css({"width"  : "30%" , "text-align" : "center"});
        var error = 1;
      }

      if(fechaSeleccion == "" || fechaSeleccion == null ){
        $("#fechaSeleccion").css({"border":"1px solid red  !important"});
        toastr.error("","<br>Debe seleccionar Fecha<br><br>").css({"width"  : "30%" , "text-align" : "center"});
        var error = 1;
      }
    }if(tipoBsuqueda == 2){

      if(contratista == "Seleccione Empresa Contratista" || contratista == null || contratista == 1){
        $("#contratista").css({"border":"1px solid red  !important"});
        toastr.error("","<br>Debe seleccionar Empresa Contratista<br><br>").css({"width"  : "30%" , "text-align" : "center"});
        var error = 1;
      }
      if(fechaSeleccion == "" || fechaSeleccion == null ){
        $("#fechaSeleccion").css({"border":"1px solid red  !important"});
        toastr.error("","<br>Debe seleccionar Fecha<br><br>").css({"width"  : "30%" , "text-align" : "center"});
        var error = 1;
      }
    }

        if(error == 0){
          $("#formUsuario").submit();
        }
       
    

  });

  $("#desactivarUsuario").click(function(){
    var desactivarUsuarioVal = $("#desactivarUsuarioVal").val('1');  
    $("#desactivar").submit();
      
  }); 

  $("#eliminarUsuario").click(function(){
     var eliminarUsuarioVal = $("#eliminarUsuarioVal").val('1');  
    $("#desactivar").submit();
      
  });

  var mensajeDesactivados = '{{$usuarioDesactivados}}';
    if(mensajeDesactivados == 1){
        toastr.info("","<br>Usuarios Desactivados<br><br>").css({"width"  : "30%" , "text-align" : "center"});
    }
  var mensajeEliminados = '{{$usuarioEliminados}}';
    if(mensajeEliminados == 1){
        toastr.info("","<br>Usuarios Eliminados<br><br>").css({"width"  : "30%" , "text-align" : "center"});
    }
})


</script>
    
@endsection