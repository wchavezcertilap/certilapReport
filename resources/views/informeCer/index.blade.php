
@extends('admin_template')
@section('content')

<section class="content-header">
  <h1>
    Reportes y Estadisticas
  </h1>
  <ol class="breadcrumb">
    <li><a href="https://certilapreports.certilapchile.cl/public/inicio/{{base64_encode($datosUsuarios->id)}}"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Informe Certificación</li>
  </ol>
</section>
</br>
           
  <div class="row">
    <div class="col-md-12">     
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title">Informe de Certificación Laboral por perido</h3>
        </div>
      </div>
    </div>
  </div>      
  @include('messages')
  <form method="POST" action="{{ route('informeCer.store') }}" role="form" class="form-horizontal" id="formReporteCompleto">

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
                          <option value="1">Mensual</option>
                          <option value="2">Semestral</option>
                         
                      </select>
                  </div>
                </div> 
              </div>  

              <div class="box-body" id="periodo">
                <div class="form-group">
                  <label for="peridoIncical" class="col-sm-2 control-label">Perido Incial</label>
                   <div class="col-sm-4">
                    <select class="form-control" name="peridoUnico" id="peridoUnico" style="width: 100%;">
                          <option value='0'>Seleccione Periodo</option>
                          @foreach($periodos as $periodo)
                          <option value="{{$periodo->id}}">{{mb_strtoupper($periodo->mes[0]->name)."-".mb_strtoupper($periodo->year)}}</option>
                          @endforeach
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

             

              <div class="box-body">
                <div class="form-group">
                  <label for="empresaPrincipal" class="col-sm-2 control-label">Empresa Principal</label>
                   <div class="col-sm-4">
                    <select class="form-control js-example-basic-multiple empresaPrincipal" name="empresaPrincipal" id="empresaPrincipal" >
                          <option>Seleccione Empresa Principal</option>
                          @foreach($EmpresasP as $empresa)

                          <option value="{{$empresa->rut}}">{{mb_strtoupper($empresa->name)}}</option>
                          @endforeach
                      </select>
                  </div>
               
                  <!-- <label for="empresaSub" class="col-sm-2 control-label">Empresa Contratistas</label>
                   <div class="col-sm-4">
                        <select class="form-control js-example-basic-multiple" name="empresaContratista[]" id="empresaContratista"  multiple="multiple">
                          <option>Seleccione Empresa Contratista</option>
                         
                      </select>
                  </div> -->
                </div>
              </div>  
               
              <div class="box-footer">
                
                <button type="button" id="botonEnviar" class="btn btn-info pull-right">buscar</button>
              </div>
        </div>     
      </div>
    </div>
</form>



<!-- reporte certificacion estados por periodo -->

@isset($nodatos)

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



<!-- SlimScroll -->
<script src="{{ asset("/AdminLTE-2.3.11/plugins/slimScroll/jquery.slimscroll.min.js") }}""></script>
<!-- FastClick -->
<script src="{{ asset("/AdminLTE-2.3.11/plugins/fastclick/fastclick.js") }}""></script>

</script>
<script type="text/javascript">
$(function () {

    $("#periodos").hide();
    $("#periodo").hide();

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
      $("#periodo").show();
      $("#periodos").hide();
    }
    if(tipoBsuqueda == 2){
      $("#periodos").show();
      $("#periodo").hide();

    }
    
  });


  $("#empresaPrincipal").change(function(){
    var rutPrincipal = $(this).val();
    $.get('porContratista/'+rutPrincipal, function(data){

        var contratista = '<option value="">Seleccione Contratista</option>'
          for (var i=0; i<data.length;i++)
            contratista+='<option value="'+data[i].rut+'">'+data[i].name.toUpperCase()+'</option>';
        
          $("#empresaContratista").html(contratista);
    });
  });

   



 $("#botonEnviar").click(function(){

    const tipoBsuqueda = $("#tipoBsuqueda").val();
    const empresaPrincipal = $("#empresaPrincipal").val();


    const valida = 0;
    const peridoUnico = $("#peridoUnico").val();
   
    const peridoInicio = $("#peridoInicio").val();
    const peridoFinal = $("#peridoFinal").val();
    if(tipoBsuqueda == 1){
      if(peridoUnico == 0){
        $("#peridoUnico").css({"border":"1px solid red  !important"});
        toastr.error("","<br>Debe seleccionar Periodo<br><br>").css({"width"  : "30%" , "text-align" : "center"});
        valida = 1;
      }

      if(empresaPrincipal == "Seleccione Empresa Principal" || empresaPrincipal == null){
        $("#empresaPrincipal").css({"border":"1px solid red  !important"});
        toastr.error("","<br>Debe seleccionar Empresa Principal<br><br>").css({"width"  : "30%" , "text-align" : "center"});
        valida = 1;
      }
    }
    if(tipoBsuqueda == 2){
      if(peridoInicio == 0){
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
      if(empresaPrincipal == "Seleccione Empresa Principal" || empresaPrincipal == null){
        $("#empresaPrincipal").css({"border":"1px solid red  !important"});
        toastr.error("","<br>Debe seleccionar Empresa Principal<br><br>").css({"width"  : "30%" , "text-align" : "center"});
        valida = 1;
      }
    }
      
      if(valida==0){
        $("#formReporteCompleto").submit();
      }
  })

    
    
   
 });


</script>
    
@endsection