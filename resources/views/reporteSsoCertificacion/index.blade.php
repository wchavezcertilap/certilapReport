
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
          <h3 class="box-title">Reportes trabjadores SSO / Certificación Laboral.</h3>
        </div>
      </div>
    </div>
  </div>      
  @include('messages')
  <form method="POST" action="{{ route('reporteTrabajadoresSsoAcre.store') }}" role="form" class="form-horizontal" id="formDocumentos">

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
                <div class="box-body" id="periodos">
                  <div class="form-group">
                    <label for="peridoIncical" class="col-sm-2 control-label">Perido Incial</label>
                     <div class="col-sm-4">
                      <select class="form-control" name="peridoInicio" id="peridoInicio" style="width: 100%;">
                            <option value='0'>Seleccione Periodo</option>
                            @foreach($periodos as $periodo)
                            <option value="{{$periodo->id}}">{{mb_strtoupper($periodo->mes[0]->name)."-".mb_strtoupper($periodo->year)}}</option>
                            @endforeach
                        </select>
                    </div>
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

@isset($lista)
@if ($lista==0)
<div class="row" id="midiv2">
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

<link rel="stylesheet" href="{{ asset("/AdminLTE-2.3.11/plugins/toaster/toastr.min.css") }}">
<script src="{{ asset("/AdminLTE-2.3.11/plugins/toaster/toastr.min.js") }}""></script>

<!-- SlimScroll -->
<script src="{{ asset("/AdminLTE-2.3.11/plugins/slimScroll/jquery.slimscroll.min.js") }}""></script>
<!-- FastClick -->
<script src="{{ asset("/AdminLTE-2.3.11/plugins/fastclick/fastclick.js") }}""></script>

</script>
<script type="text/javascript">
$(function () {

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
  });


 $("#botonEnviar").click(function(){

    var empresaPrincipal = $("#empresaPrincipal").val();
    var peridoInicio = $("#peridoInicio").val();
    var valida = 0;
    if(empresaPrincipal == "Seleccione Empresa Principal" || empresaPrincipal == null ){
        $("#empresaPrincipal").css({"border":"1px solid red  !important"});
        toastr.error("","<br>Debe seleccionar Empresa Principal<br><br>").css({"width"  : "30%" , "text-align" : "center"});
        valida = 1;
    }
    if(peridoInicio == "" || peridoInicio == null ){
        $("#peridoInicio").css({"border":"1px solid red  !important"});
        toastr.error("","<br>Debe seleccionar un Periodo<br><br>").css({"width"  : "30%" , "text-align" : "center"});
        valida = 1;
    }
    if(valida == 0){
        $('#loader').show();
        $("#formDocumentos").submit();
      }

  })

   
 });

$(document).ready(function() {
  setTimeout(function() {
    // Declaramos la capa  mediante una clase para ocultarlo
        $("#midiv2").fadeOut(1500);
    // Transcurridos 5 segundos aparecera la capa midiv2
    },5000);
});

</script>    
@endsection