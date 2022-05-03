
@extends('admin_template')

@section('content')

<section class="content-header">
  <h1>
    Estadísticas siniestralidad, accidentabilidad, tasa frecuencia y gravedad
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="#">Estadisticas</a></li>
    <li class="active"> Ingresar datos</li>
  </ol>
</section>

  {{ csrf_field() }}
  
           
  <div class="row">
    <div class="col-md-12">     
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title">Inicio</h3>
        </div>
      </div>
    </div>
  </div>      

  <div class="row">
    <div class="col-md-6">     
        <div class="box-body">
           
        </div> 
    </div>


     <div class="col-md-6">     
        <div class="box-body">
            
        </div> 
    </div>
  </div>    


  <div class="row">
    
  </div>         




  <div class="row">
    <div class="col-md-6">
        <div class="box-footer">
          <button type="submit" center class="btn btn-block btn-success btn-lg">Guardar</button>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box-footer">
          <button type="submit" center class="btn btn-block btn-info btn-lg">Volver</button>
        </div>
    </div>
  </div>  
</form>

<!-- /.row -->
</section>
    <!-- /.content -->

<link rel="stylesheet" href="{{ asset("/AdminLTE-2.3.11/plugins/select2/select2.min.css") }}">
<script src="{{ asset("/AdminLTE-2.3.11/plugins/select2/select2.min.js") }}""></script>

<script type="text/javascript">


</script>
    
@endsection