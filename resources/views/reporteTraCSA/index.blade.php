
@extends('admin_template')
@section('content')

<section class="content-header">
  <h1>
    Reportes y Estadisticas
  </h1>
  <ol class="breadcrumb">
    <li><a href="https://certilapreports.certilapchile.cl/public/inicio/{{base64_encode($datosUsuarios->id)}}"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Reporte trabajadores Acreditación, Verficación y Control Acceso</li>
  </ol>
</section>
</br>
           
  <div class="row">
    <div class="col-md-12">     
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title">Reporte Cruzado entre Acreditación, Verficación y Control de Acceso</h3>
        </div>
      </div>
    </div>
  </div>      
  @include('messages')
  <form method="POST" action="{{ route('reporteCSA.store') }}" role="form" class="form-horizontal" id="formDocumentos">

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
                          <option value="2">Fecha de Acreditación</option>
                         
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
                  </div> 
              </div> 

         

              <div class="box-footer">
                
                <button type="button" id="botonEnviar" class="btn btn-info pull-right">buscar</button>
              </div>
        </div>     
      </div>
    </div>
</form>

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
    }
    if(valida == 0){

        $("#formDocumentos").submit();
      }

  })
 });

</script>
    
@endsection