
@extends('admin_template')
@section('content')

<section class="content-header">
  <h1>
    Configuraciones SSO
  </h1>
  <ol class="breadcrumb">
    <li><a href="https://certilapreports.certilapchile.cl/public/inicio/{{base64_encode($datosUsuarios->id)}}"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Cambio de Ciclo</li>
  </ol>
</section>
</br>
           
  <div class="row">
    <div class="col-md-12">     
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title">Cambio de Ciclos.</h3>
        </div>
      </div>
    </div>
  </div>      
  @include('messages')
  <form method="POST" action="{{ route('cambiarCiclo.store') }}" role="form" class="form-horizontal" id="formCiclos">

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
               
                  <label for="fechas" class="col-sm-2 control-label">Fecha de inicio del ciclo</label>
                    <div class="input-group date col-xs-3">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                        <input type="text" class="form-control pull-right" id="fecha" name="fecha" value="" autocomplete="off">
                    </div>
                </div>
              </div> 

              <div class="box-body">
                <div class="form-group">
                 <label for="empresaSub" class="col-sm-2 control-label">Dias de Verificación</label>
                   <div class="col-sm-2">
                        <input type="number" min="1" class="form-control" placeholder="Dias de Verificación" name="diasVerif" id="diasVerif">
                  </div>
               
                  <label for="empresaSub" class="col-sm-4 control-label">Dias de Carga</label>
                   <div class="col-sm-2">
                        <input type="number" min="1" class="form-control" name="diasCarga" id="diasCarga" placeholder="Dias de Carga">
                  </div>
                </div>
              </div> 

              <div class="box-footer">
                
                <button type="button" id="botonEnviar" class="btn btn-info pull-right">Registrar</button>
              </div>
        </div>     
      </div>
    </div>
</form>
@isset($actualizado)
 <input type="hidden"  class="form-control" name="actualizado" id="actualizado" value="{{$actualizado}}">
 <div class="modal modal-success" id="modal_success">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="cerrarModal">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Confirmar</h4>
              </div>
              <div class="modal-body">
                <p>Se actulizo Satisfactoriamente</p>
              </div>
              <div class="modal-footer">
                <button type="button" id="cerrarModal2" class="btn btn-outline pull-left">cerrar</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
@endif
<!-- /.row -->
</section>
    <!-- /.content -->

<link rel="stylesheet" href="{{ asset("/AdminLTE-2.3.11/plugins/select2/select2.min.css") }}">
<script src="{{ asset("/AdminLTE-2.3.11/plugins/select2/select2.min.js") }}""></script>

<script src="{{ asset("/AdminLTE-2.3.11/plugins/moment/moment.js") }}""></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/locales/bootstrap-datepicker.es.min.js"></script>
<link rel="stylesheet" href="{{ asset("/AdminLTE-2.3.11/plugins/toaster/toastr.min.css") }}">
<script src="{{ asset("/AdminLTE-2.3.11/plugins/toaster/toastr.min.js") }}""></script>

<!-- SlimScroll -->
<script src="{{ asset("/AdminLTE-2.3.11/plugins/slimScroll/jquery.slimscroll.min.js") }}""></script>
<!-- FastClick -->
<script src="{{ asset("/AdminLTE-2.3.11/plugins/fastclick/fastclick.js") }}""></script>

</script>
<script type="text/javascript">
$(function () {

  // moment.locale('es'); 
    //Date range picker
  $('#fecha').datepicker({
    format: 'dd-mm-yyyy',
    language: 'es',
    autoUpdateInput: false
    
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

    //Initialize Select2 Elements
  $(".js-example-basic-multiple").select2();

 $("#botonEnviar").click(function(){

    var empresaPrincipal = $("#empresaPrincipal").val();
    var fecha = $("#fecha").val();
    var diasVerif = $("#diasVerif").val();
    var diasCarga = $("#diasCarga").val();
    if(empresaPrincipal == "Seleccione Empresa Principal" || empresaPrincipal == null){
        $("#empresaPrincipal").css({"border":"1px solid red  !important"});
        toastr.error("","<br>Debe seleccionar Empresa Principal<br><br>").css({"width"  : "30%" , "text-align" : "center"});
      }
    else if(fecha == "" || fecha == null){
        $("#fecha").css({"border":"1px solid red  !important"});
        toastr.error("","<br>Debe seleccionar una fecha<br><br>").css({"width"  : "30%" , "text-align" : "center"});
    }
    else if(diasVerif == "" || diasVerif == null){
        $("#diasVerif").css({"border":"1px solid red  !important"});
        toastr.error("","<br>Debe colocar los dias de Verificación<br><br>").css({"width"  : "30%" , "text-align" : "center"});
    }
    else if(diasCarga == "" || diasCarga == null){
        $("#diasCarga").css({"border":"1px solid red  !important"});
        toastr.error("","<br>Debe colocar los dias de carga<br><br>").css({"width"  : "30%" , "text-align" : "center"});
    }else{

         $("#formCiclos").submit();
      }

  })

  var actualizado = $("#actualizado").val();
  if(actualizado == 1){
     $('#modal_success').show();
  }

  $("#cerrarModal").click(function(){

      $('#modal_success').hide();
    
  })

  $("#cerrarModal2").click(function(){

       $('#modal_success').hide();
    
  })
    
   
 });


</script>
    
@endsection