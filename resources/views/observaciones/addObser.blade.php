
@extends('admin_template')
@section('content')

<section class="content-header">
  <h1>
     Agregar Observacion al documento
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Agregar Observacion</li>
  </ol>
</section>
</br>
         
  <div class="row">
    <div class="col-md-12">     
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title">Registrar Observación para: <strong>{{ucwords(mb_strtolower($documentoTex,'UTF-8'))}}</strong></h3>
        </div>
      </div>
    </div>
  </div>      
  @include('messages')
<form method="POST" action="{{ route('observacionesDoc.store') }}" role="form" class="form-horizontal" id="formDocumentos">
  <div class="row">
    <div class="col-md-12">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title"></h3>
        </div>
        {{ csrf_field() }}
          <div class="box-body">
            <div class="form-group">
              <label for="observacion" class="col-sm-2 control-label">Observación</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="observacion"  name='observacion' placeholder="Ingrese Observación">
                  <input type="hidden" name="idDoc" id="idDoc" value={{$idDoc}}>
                </div>
            </div> 
            <div class="form-group">
              <label for="afectaTra" class="col-sm-2 control-label">Afecta trabajador</label>
                <div class="col-sm-4">
                  <select class="form-control" name="AfectaTra" id="AfectaTra">
                    <option value="0">Seleccione</option>
                    <option value="1">Si</option>
                    <option value="2">No</option>
                  </select>
                </div>
            </div>
          </div> 
          <div class="box-footer">
                <button type="button" id="botonEnviar" class="btn btn-info pull-right">Guardar</button>
          </div>
      </div>     
    </div>
  </div>
</form> 
@isset($observaciones)
@if (count($observaciones))
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
                  <th>Observaciones</th>
                  <th>Afecta Trabajador</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
                </thead>
                 <tbody>
                @foreach($observaciones as $dato)
                @isset($dato["observacion"])
                <tr>
                  <td> 
                   {{$dato["observacion"]}}
                  
                 </td>
                  @if ($dato["trabajador"] == 1)
                  <td> 
                   Si
                 </td>
                  @else
                   <td> 
                  No
                 </td>
                  @endif
                  </td>
                  @if ($dato["status"] == 1)
                  <td> 
                   Activo
                 </td>
                  @else
                   <td> 
                  Desactivado
                 </td>
                  @endif
                  <td>
                     <button class="btn btn-info editarObs" title="Editar Observación" id="{{$dato['id'].'_'.$dato['observacion'].'_'.$dato['trabajador']}}"><i class="fa fa-fw fa-pencil-square-o"></i></button>
                    <a href="{{ route('observacionesDoc.destroy',$dato['id'])}}" class="btn btn-danger" title="Desactivar"><i class="fa fa-fw fa-trash"></i></a>
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
        <div class="box-body">
          <div class="callout callout-info">
            <p>No hay resultado.</p>
          </div>
        </div>
      </div>
    </div>        
</div>
@endif
@endisset 
</section>
<div class="modal modal-success" id="modal_editar">
  <form  method="POST" action="{{ route('observacionesDoc.store2') }}" role="form" class="form-horizontal" id="formObservacionEdit">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="cerrarModal">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Editar Observación</h4>
        </div>
        {{ csrf_field() }}
        <div class="modal-body">
         <div class="form-group">
            <label for="observacion" class="col-sm-2 control-label">Observación</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="observacionEdit"  name='observacionEdit' value="">
              <input type="hidden" name="idObserEdit" id="idObserEdit" value="">
            </div>
          </div>
          <div class="form-group">
            <label for="afectaTra" class="col-sm-2 control-label">Afecta trabajador</label>
            <div class="col-sm-4">
              <select class="form-control" name="trabajadorEdit" id="trabajadorEdit">
                <option value="0">Seleccione</option>
                <option value="1">Si</option>
                <option value="2">No</option> 
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="estado" class="col-sm-2 control-label">Estado</label>
            <div class="col-sm-4">
              <select class="form-control" name="estadoEdit" id="estadoEdit">
                <option value="0">Seleccione</option>
                <option value="1">Activo</option>
                <option value="2">Desactivado</option> 
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="cerrarModal2" class="btn btn-outline pull-left">cerrar</button>
          <button type="button" id="editarObser" class="btn btn-outline">Editar</button>
        </div>
      </div>
    </div>
  </form>        
</div>
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
        pageLength: {{ $cantObser }},      
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
    let error = 0;
    if($("#observacion").val() == ""){
        error = 1;

        $("#observacion").css({"border":"1px solid red  !important"});
          toastr.error("","<br>Debe ingresar Observación <br><br>").css({"width"  : "30%" , "text-align" : "center"});
    }
    if($("#AfectaTra").val() == 0){
        error = 1;

        $("#AfectaTra").css({"border":"1px solid red  !important"});
          toastr.error("","<br>Debe Seleccionar una opción <br><br>").css({"width"  : "30%" , "text-align" : "center"});
    }
    if(error == 0){
      $("#formDocumentos").submit();
    }
  })



  $("#cerrarModal").click(function(){

       $('#modal_editar').hide();
    
  })

  $("#cerrarModal2").click(function(){

       $('#modal_editar').hide();
    
  })

  $("#editarObser").click(function(){
    let error = 0;
    if($("#observacionEdit").val() == ""){
        error = 1;

        $("#observacionEdit").css({"border":"1px solid red  !important"});
          toastr.error("","<br>Debe ingresar Observación <br><br>").css({"width"  : "30%" , "text-align" : "center"});
    }
    if($("#trabajadorEdit").val() == 0){
        error = 1;

        $("#trabajadorEdit").css({"border":"1px solid red  !important"});
          toastr.error("","<br>Debe Seleccionar una opción <br><br>").css({"width"  : "30%" , "text-align" : "center"});
    }
    if(error == 0){
      $("#formObservacionEdit").submit();
    }
  })


  $(".editarObs").click(function() {
    let id_Obser = $(this).attr('id');
    $('#modal_editar').show();
    let res = id_Obser.split("_");
    let idObse = res[0];
    let observion = res[1];
    let trabador = res[2];
    $('#observacionEdit').val(observion);
    $('#idObserEdit').val(idObse);
    $('#trabajadorEdit').val(trabador);
    $('#estadoEdit').val(1);
  });


  

 });


</script>
    
@endsection