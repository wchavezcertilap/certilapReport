
@extends('admin_template')
@section('content')

<section class="content-header">
  <h1>
    Reportes y Estadisticas
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Facturación</li>
  </ol>
</section>
</br>
           
  <div class="row">
    <div class="col-md-12">     
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title">Trabajadores pagados en SSO</h3>
        </div>
      </div>
    </div>
  </div>      
  @include('messages')
  <form method="POST" action="{{ route('trabajadoresPagadosPre.store') }}" role="form" class="form-horizontal" id="formTrabajador">
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
                          @foreach($empresasPrinpalesPagoD as $empresa)

                          <option value="{{$empresa['rutprincipal']}}">{{mb_strtoupper($empresa['nombrePrincipal'])}}</option>
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


<script type="text/javascript">
$(function () {

  var table = $('#datosTabla').DataTable({
      pageLength: 100,        
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
        title: 'Reporte Facturacion Trabajadores SSO',   
        filename:  'Reporte_Facturacion_Trabajadores_SSO'
      },
      {
        extend:    'pdfHtml5',
        text:      '<i class="fa fa-fw fa-file-pdf-o"></i> ',
        titleAttr: 'Exportar a PDF',
        className: 'btn btn-danger',
        orientation: 'landscape',
        pageSize: 'LEGAL',
        title: 'Reporte Facturacion Trabajadores SSO',   
        filename:  'Reporte_Facturacion_Trabajadores_SSO'
      },
      {
        extend:    'print',
        text:      '<i class="fa fa-print"></i> ',
        titleAttr: 'Imprimir',
        className: 'btn btn-info',
        title: 'Reporte Facturacion Trabajadores SSO',   
        filename:  'Reporte_Facturacion_Trabajadores_SSO'
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
      }else if(fechaSeleccion == "" || fechaSeleccion == null){

         $("#fechaSeleccion").css({ "border":"1px solid red"});
        toastr.error("","<br>Debe seleccionar un rango de fechas<br><br>").css({"width"  : "30%" , "text-align" : "center"});
      }else{

         $("#formTrabajador").submit();

      }

  })

 });


</script>
    
@endsection