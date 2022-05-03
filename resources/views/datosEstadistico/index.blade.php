
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
<form method="post" action="{{ route('datosEstadistico.create') }}"  role="form" class="form-horizontal">
  {{ csrf_field() }}
  
           
  <div class="row">
    <div class="col-md-12">     
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title">Ingresar datos de Estadísticas siniestralidad, accidentabilidad, tasa frecuencia y gravedad.</h3>
        </div>
      </div>
    </div>
  </div>      

  <div class="row">
    <div class="col-md-6">     
        <div class="box-body">
          <div class="form-group">
            <label for="mes" class="col-md-1 control-label">Mes:</label>
              <div class="input-group col-md-5">
                <div class="input-group-addon"> 
                  <i class="fa fa-calendar"></i>
                </div>
                <select class="form-control" name="mes" id="mes" style="width: 100%;">
                        <option>Seleccione Mes</option>
                        @foreach($meses as $mes)
                        <option value="{{$mes}}">{{mb_strtoupper($mes)}}</option>
                        @endforeach
                </select>
              </div>
          </div>  
        </div> 
    </div>


     <div class="col-md-6">     
        <div class="box-body">
          <div class="form-group">
            <label for="año" class="col-md-1 control-label">Año:</label>
              <div class="input-group col-md-5">
                <div class="input-group-addon"> 
                  <i class="fa fa-calendar-minus-o"></i>
                </div>
                 <select class="form-control" name="anio" id="anio" style="width: 100%;">
                          <option>Seleccione Año</option>
                          @foreach($year as $ano)
                          <option value="{{$ano}}">{{mb_strtoupper($ano)}}</option>
                          @endforeach
                  </select>
              </div>
          </div>  
        </div> 
    </div>
  </div>    


  <div class="row">
    <div class="col-md-6">    
      <div class="box-body">
        <div class="form-group">
          <label for="nAccidente" class="col-sm-4 control-label">Nº de Accidentes:</label>
            <div class="input-group col-md-2">
                <div class="input-group-addon"> 
                  <i class="fa fa-fw fa-wheelchair"></i>
                </div>
                    <input type="number" min="0" class="form-control" name="field_1" id="field_1">
              </div>  
          </div>
         </div> 

        <div class="box-body">
          <div class="form-group">
            <label for="nAccidente" class="col-sm-4 control-label">Nº enfermedades profesionales:</label>
              <div class="input-group col-md-2">
                  <div class="input-group-addon"> 
                    <i class="fa fa-fw fa-heartbeat"></i>
                  </div>
                     <input type="number" min="0" class="form-control" name="field_2" id="field_2">
                </div>  
            </div>
         </div> 

         <div class="box-body">
          <div class="form-group">
            <label for="totalPerdidosIncapacidadTem" class="col-sm-4 control-label">Total de días perdidos por incapacidades temporales:</label>
              <div class="input-group col-md-2">
                  <div class="input-group-addon"> 
                    <i class="fa  fa-calendar-times-o"></i>
                  </div>
                     <input type="number" min="0" class="form-control" name="field_3" id="field_3"> 
                </div>  
            </div>
         </div> 

         <div class="box-body">
          <div class="form-group">
            <label for="totalDiasPerdidos" class="col-sm-4 control-label">Total de días perdidos:</label>
              <div class="input-group col-md-2">
                  <div class="input-group-addon"> 
                    <i class="fa fa-fw fa-hospital-o"></i>
                  </div>
                    <input type="number" min="0" class="form-control" name="field_4" id="field_4"> 
                </div>  
            </div>
         </div> 

        <div class="box-body">
          <div class="form-group">
            <label for="tasaFrecuencia" class="col-sm-4 control-label">Tasa de frecuencia:</label>
              <div class="input-group col-md-2">
                  <div class="input-group-addon"> 
                    <i class="fa fa-fw fa-line-chart"></i>
                  </div>
                     <input type="number" min="0" class="form-control" name="field_5" id="field_5"> 
                </div>  
            </div>
        </div> 


        <div class="box-body">
          <div class="form-group">
            <label for="tasaSiniestralidadTemporal" class="col-sm-4 control-label">Tasa de siniestralidad por incapacidades temporales:</label>
              <div class="input-group col-md-2">
                  <div class="input-group-addon"> 
                    <i class="fa fa-fw fa-line-chart"></i>
                  </div>
                     <input type="number" min="0" class="form-control" name="field_6" id="field_6"> 
                </div>  
            </div>
        </div> 


        <div class="box-body">
          <div class="form-group">
             <label for="tasaSiniestralidadTotal" class="col-sm-4 control-label">Tasa de siniestralidad total:</label>
              <div class="input-group col-md-2">
                  <div class="input-group-addon"> 
                    <i class="fa fa-fw fa-line-chart"></i>
                  </div>
                    <input type="number" min="0" class="form-control" name="field_7" id="field_7"> 
                </div>  
            </div>
        </div> 

        <div class="box-body">
          <div class="form-group">
             <label for="nuneroPensionadoAccidenteEnfermedades" class="col-sm-4 control-label">Nº de pensionados por accidentes y enfermedades:</label>
              <div class="input-group col-md-2">
                  <div class="input-group-addon"> 
                    <i class="fa fa-fw fa-wheelchair"></i>
                  </div>
                    <input type="number" min="0" class="form-control" name="field_8" id="field_8"> 
                </div>  
            </div>
        </div> 

        <div class="box-body">
          <div class="form-group">
             <label for="accidentesFatales" class="col-sm-4 control-label">Nº de accidentes fatales:</label>
              <div class="input-group col-md-2">
                  <div class="input-group-addon"> 
                    <i class="fa fa-fw fa-hospital-o"></i>
                  </div>
                    <input type="number" min="0" class="form-control" name="field_9" id="field_9"> 
                </div>  
            </div>
        </div> 

        <div class="box-body">
          <div class="form-group">
             <label for="cantidadMujeres" class="col-sm-4 control-label">Cantidad mujeres:</label>
              <div class="input-group col-md-2">
                  <div class="input-group-addon"> 
                    <i class="fa fa-fw fa-female"></i>
                  </div>
                    <input type="number" min="0" class="form-control" name="field_10" id="field_10"> 
                </div>  
            </div>
        </div> 

        <div class="box-body">
          <div class="form-group">
             <label for="indiceAccidentabilidad" class="col-sm-4 control-label">Índice de accidentabilidad:</label>
              <div class="input-group col-md-2">
                  <div class="input-group-addon"> 
                    <i class="fa fa-fw fa-line-chart"></i>
                  </div>
                    <input type="number" min="0" class="form-control" name="field_11" id="field_11"> 
                </div>  
            </div>
        </div> 

        <div class="box-body">
          <div class="form-group">
             <label for="cotizacionAdicional" class="col-sm-4 control-label">Cotización adicional:</label>
              <div class="input-group col-md-2">
                  <div class="input-group-addon"> 
                    <i class="fa fa-fw fa-calculator"></i>
                  </div>
                    <input type="number" min="0" class="form-control" name="field_12" id="field_12"> 
                </div>  
            </div>
        </div> 
  </div>         


<!-- lado derecho -->

    <div class="col-md-6">    
      <div class="box-body">
        <div class="form-group">
         <label for="diasPerdidosAccidentes" class="col-sm-4 control-label">Días perdidos por Accidentes de trabajo:</label>
            <div class="input-group col-md-2">
                <div class="input-group-addon"> 
                  <i class="fa fa-calendar-times-o"></i>
                </div>
                   <input type="number" min="0" class="form-control" name="field_13" id="field_13"> 
              </div>  
          </div>
         </div> 

        <div class="box-body">
          <div class="form-group">
            <label for="diasPerdidosAccidentes" class="col-sm-4 control-label">Días perdidos por enfermedad profesional:</label>
              <div class="input-group col-md-2">
                  <div class="input-group-addon"> 
                    <i class="fa fa-calendar-times-o"></i>
                  </div>
                     <input type="number" min="0" class="form-control" name="field_14" id="field_14"> 
                </div>  
            </div>
         </div> 

         <div class="box-body">
          <div class="form-group">
            <label for="totalDiaPerdidosCasoFatal" class="col-sm-4 control-label">Total de días perdidos cargo por casos fatales:</label>
              <div class="input-group col-md-2">
                  <div class="input-group-addon"> 
                    <i class="fa fa-calendar-times-o"></i>
                  </div>
                     <input type="number" min="0" class="form-control" name="field_15" id="field_15"> 
                </div>  
            </div>
         </div> 

         <div class="box-body">
          <div class="form-group">
            <label for="numeroTrabajadoresPromedio" class="col-sm-4 control-label">Número de trabajadores promedio:</label>
              <div class="input-group col-md-2">
                  <div class="input-group-addon"> 
                    <i class="fa fa-fw fa-group"></i>
                  </div>
                    <input type="number" min="0" class="form-control" name="field_16" id="field_16"> 
                </div>  
            </div>
         </div> 

        <div class="box-body">
          <div class="form-group">
            <label for="tasaGravedad" class="col-sm-4 control-label">Tasa de gravedad:</label>
              <div class="input-group col-md-2">
                  <div class="input-group-addon"> 
                    <i class="fa fa-fw fa-line-chart"></i>
                  </div>
                     <input type="number" min="0" class="form-control" name="field_17" id="field_17"> 
                </div>  
            </div>
        </div> 


        <div class="box-body">
          <div class="form-group">
            <label for="tasaSiniestralidadInvalidez" class="col-sm-4 control-label">Tasa de siniestralidad por invalideces y muertes:</label>
              <div class="input-group col-md-2">
                  <div class="input-group-addon"> 
                    <i class="fa fa-fw fa-line-chart"></i>
                  </div>
                     <input type="number" min="0" class="form-control" name="field_18" id="field_18"> 
                </div>  
            </div>
        </div> 


        <div class="box-body">
          <div class="form-group">
             <label for="tasaAccidentabilidad" class="col-sm-4 control-label">Tasa de accidentabilidad (%):</label>
              <div class="input-group col-md-2">
                  <div class="input-group-addon"> 
                    <i class="fa fa-fw fa-line-chart"></i>
                  </div>
                    <input type="number" min="0" class="form-control" name="field_19" id="field_19"> 
                </div>  
            </div>
        </div> 

        <div class="box-body">
          <div class="form-group">
             <label for="numeroIndenizadoAccidentes" class="col-sm-4 control-label">Nº de indemnizados por accidentes y enfermedades:</label>
              <div class="input-group col-md-2">
                  <div class="input-group-addon"> 
                    <i class="fa fa-fw fa-wheelchair"></i>
                  </div>
                    <input type="number" min="0" class="form-control" name="field_20" id="field_20"> 
                </div>  
            </div>
        </div> 

        <div class="box-body">
          <div class="form-group">
             <label for="horasHombres" class="col-sm-4 control-label">Horas hombre (**):</label>
              <div class="input-group col-md-2">
                  <div class="input-group-addon"> 
                    <i class="fa fa-fw fa-users"></i>
                  </div>
                    <input type="number" min="0" class="form-control" name="field_21" id="field_21"> 
                </div>  
            </div>
        </div> 

        <div class="box-body">
          <div class="form-group">
             <label for="cantidadHombre" class="col-sm-4 control-label">Cantidad hombres:</label>
              <div class="input-group col-md-2">
                  <div class="input-group-addon"> 
                    <i class="fa fa-fw fa-male"></i>
                  </div>
                    <input type="number" min="0" class="form-control" name="field_22" id="field_22"> 
                </div>  
            </div>
        </div> 

        <div class="box-body">
          <div class="form-group">
             <label for="cotizacionBasica" class="col-sm-4 control-label">Cotización básica:</label>
              <div class="input-group col-md-2">
                  <div class="input-group-addon"> 
                    <i class="fa fa-fw fa-calculator"></i>
                  </div>
                    <input type="number" min="0" class="form-control" name="field_23" id="field_23"> 
                </div>  
            </div>
        </div> 

        <div class="box-body">
          <div class="form-group">
             <label for="cotizacionTotal" class="col-sm-4 control-label">Cotización total:</label>
              <div class="input-group col-md-2">
                  <div class="input-group-addon"> 
                    <i class="fa fa-fw fa-calculator"></i>
                  </div>
                    <input type="number" min="0" class="form-control" name="field_24" id="field_24"> 
                </div>  
            </div>
        </div> 
    </div>
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