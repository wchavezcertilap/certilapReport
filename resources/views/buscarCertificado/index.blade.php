
@extends('admin_template')
@section('content')

<section class="content-header">
  <h1>
    Certificación laboral
  </h1>
  <ol class="breadcrumb">
    <li><a href="https://certilapreports.certilapchile.cl/public/inicio/{{base64_encode($datosUsuarios->id)}}"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Buscar certificado</li>
  </ol>
</section>
</br>
           
  <div class="row">
    <div class="col-md-12">     
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title">Buscar certificado.</h3>
        </div>
      </div>
    </div>
  </div>      
  @include('messages')
  <form method="POST" action="{{ route('buscarCertificado.store') }}" role="form" class="form-horizontal" id="formCertificado">

    <div class="row">
      <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Criterios de Busqueda</h3>
            </div>
      
               {{ csrf_field() }}
              

              <div class="box-body">
                <div class="form-group">
                 <label for="empresaPrincipal" class="col-sm-2 control-label">N° de Certificado (sin número de serie)</label>
                   <div class="col-sm-4">
                      <input type="number" min="1" class="form-control pull-right" id="certificado" name="certificado" value="">
                  </div>
                </div>
              </div> 

              <div class="box-footer">
                
                <button type="button" id="botonEnviar" class="btn btn-info pull-right">Buscar</button>
              </div>
        </div>     
      </div>
    </div>
</form>
@isset($cantidaDatos)
@if ($cantidaDatos > 0)
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
                  <th>N° Certificado</th>
                  <th>Empresa Principal</th>
                  <th>RUT</th>
                  <th>Empresa Contratista</th>
                  <th>RUT</th>
                  <th>Empresa Sub Contratista</th>
                  <th>RUT</th>
                  <th>Centro de Costo</th>
                  <th>Descargar</th>
                </tr>
                </thead>
                 <tbody>
                   @foreach($datosVista as $datos)
                @isset($datos["numeroCertificado"])
                <tr>
                  <td>
                   {{$datos["numeroCertificado"]}}
                 </td>
                 <td>
                   {{$datos["empresaPrincipal"]}}
                 </td>
                  <td>
                   {{$datos["empresaPrincipalRut"]}}
                 </td>
                 <td>
                   {{$datos["empresaContratista"]}}
                 </td>
                 <td>
                   {{$datos["empresaContratistaRut"]}}
                 </td>
                 <td>
                   {{$datos["empresaSubContratista"]}}
                 </td>
                  <td>
                   {{$datos["empresaSubContratistaRut"]}}
                 </td>
                 <td>
                   {{$datos["centroCosto"]}}
                 </td>
                 <td>
                   <a href="https://sistema.certilapchile.cl/index.php?aa=pdf&cn=certificate&di={{$datos['idCompany']}}" class="btn btn-danger btn-lg active" role="button" aria-pressed="true" target="_blank"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<script src="{{ asset("/AdminLTE-2.3.11/plugins/moment/moment.js") }}""></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/locale/es.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<link rel="stylesheet" href="{{ asset("/AdminLTE-2.3.11/plugins/toaster/toastr.min.css") }}">
<script src="{{ asset("/AdminLTE-2.3.11/plugins/toaster/toastr.min.js") }}""></script>

<!-- SlimScroll -->
<script src="{{ asset("/AdminLTE-2.3.11/plugins/slimScroll/jquery.slimscroll.min.js") }}""></script>
<!-- FastClick -->
<script src="{{ asset("/AdminLTE-2.3.11/plugins/fastclick/fastclick.js") }}""></script>

</script>
<script type="text/javascript">
$(function () {

   moment.locale('es') 
    //Date range picker
  $('#fecha').datepicker({
    language: 'es',
    autoUpdateInput: false
    
  })



 ;


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

    var certificado = $("#certificado").val();
    
    
    if(certificado == "" || certificado == null){
        $("#fecha").css({"border":"1px solid red  !important"});
        toastr.error("","<br>Debe ingresar el N° de certificado<br><br>").css({"width"  : "30%" , "text-align" : "center"});
    }else{

         $("#formCertificado").submit();
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