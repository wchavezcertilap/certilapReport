
@extends('admin_template')
@section('content')
 <style>
    .example-modal .modal {
      position: relative;
      top: auto;
      bottom: auto;
      right: auto;
      left: auto;
      display: block;
      z-index: 1;
    }

    .example-modal .modal {
      background: transparent !important;
    }
  </style>
<section class="content-header">
  <h1>
    Formulario de Observaciones de documentos
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Observaciones de documentos</li>
  </ol>
</section>
</br>       
  <div class="row">
    <div class="col-md-12">     
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title">Registrar datos</h3>
        </div>
      </div>
    </div>
  </div>      
  @include('messages')
  
  

    <div class="row">
      <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"></h3>
            </div>
      
               {{ csrf_field() }}
              <div class="box-body">
                <table class="table">
                  <thead>
                    <tr>
                      <th scope="col">Documento</th>
                      <th scope="col">Agregar observacion</th>
                       <th scope="col">Ver observaciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($documentos as $datos)
                    @isset($datos["name"])
                    <tr>
                      <td>{{$datos["name"]}}</td>
                      <td><a href="{{ route('observacionesDoc.edit',$datos['id'])}}" class="btn btn-success" title="Agregar Observacion"/><i class="fa fa-fw fa-plus"></i></a>
                      <td><button class="btn btn-info botonObser" id="{{$datos['id']}}" title="Ver Observaciones"/><i class="fa fa-fw fa-list"></i></button>
                          
                    </tr>
                  </tbody>
                  @endisset
                  @endforeach
                </table>
              </div>
        </div>     
      </div>
    </div>



 <div class="modal modal-info" id="modal_observaciones">
  
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="cerrarModal">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Ver Observaciones</h4>
              </div>
              <div class="modal-body" id="tablaObsd">
                
              </div>
              <div class="modal-footer">
                <button type="button" id="cerrarModal2" class="btn btn-outline pull-left">cerrar</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div> <!-- /.modal-dialog -->

</div>



<!-- /.row -->
</section>
    <!-- /.content -->

<link rel="stylesheet" href="{{ asset("/AdminLTE-2.3.11/plugins/select2/select2.min.css") }}">
<script src="{{ asset("/AdminLTE-2.3.11/plugins/select2/select2.min.js") }}""></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.css">

<link rel="stylesheet" href="{{ asset("/AdminLTE-2.3.11/plugins/toaster/toastr.min.css") }}">
<script src="{{ asset("/AdminLTE-2.3.11/plugins/toaster/toastr.min.js") }}""></script>

</script>
<script type="text/javascript">
$(function () {


  $(".botonObser").click(function(){
    let id = $(this).attr('id');

    
    $.get('obsDoc/'+id, function(data){

      if(data.length > 0){
        $('#modal_observaciones').show();
        $('#tablaObs').show();
        let observacion ='<table class="table" id="tablaObs"><thead><tr><th>Observaciones</th><th>Afecta Trabajador</th><th>Estado</th></tr></thead><tbody>';
        for (var i=0; i<data.length;i++){
            observacion+='<tr><td>'+data[i].observacion+'</td><td>'+data[i].trabajador.toUpperCase()+'</td><td>'+data[i].status+'</td></tr>';
        }
        observacion+='<tbody></table>';
        $("#tablaObsd").html(observacion);
          
      }else{
        let observacion ='<table class="table" id="tablaObs"><thead><tr><th>Sin Observaciones</th><th></table>';
        $('#modal_observaciones').show();
        $("#tablaObsd").html(observacion);
        
        
       
      }

       

        
    });
   
    

  })

$("#cerrarModal2").click(function(){

      $('#modal_observaciones').hide();
      $("#idDoc").val('');
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


 $("#cerrarModal").click(function(){

       $('#modal_observaciones').hide();
    
  })

  $("#cerrarModal2").click(function(){

       $('#modal_observaciones').hide();
    
  })



});


</script>
    
@endsection