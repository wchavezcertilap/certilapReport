@extends('admin_template')
@section('content')
 <div class="row">
      
        <!--/.col (left) -->
        <!-- right column -->
        <div class="col-md-12">
          <!-- Horizontal Form -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Crear Menu Padre</h3>
            </div>
             @include('messages')


            <!-- /.box-header -->
            <!-- form start -->
            <form method="POST" action="{{ route('menuPadre.store') }}"  role="form" class="form-horizontal">
              {{ csrf_field() }}
              <div class="box-body">
                <div class="form-group">
                  <label for="nombreMenu" class="col-sm-2 control-label">Nombre Menu</label>

                  <div class="col-sm-10">
                    <input type="text" class="form-control input-lg"" id="nombreMenu" name="nombreMenu" placeholder="Nombre del menu" >
                  </div>
                </div>
                <div class="form-group">
                  <label for="estado" class="col-sm-2 control-label">Estado</label>

                  <div class="col-sm-10">
                    
                  <select class="form-control input-lg" name="estado" id="estado">
                    <option value="">Seleccione Estado</option>
                    <option value="A">Activo</option>
                    <option value="D">Desactivado</option>
                  </select>
                  </div>
                </div>


                <div class="form-group">
                  <label for="permisoUsuario" class="col-sm-2 control-label">Usuarios Permitidos</label>

                  <div class="col-sm-10">
                    
                  <select multiple class="form-control input-lg" name="perfilUsuario[]">
                   <option disabled selected>Seleccione Usuario</option>
                    @foreach($tipoUsuario as $usuario)
                    <option value="{{$usuario->id}}">{{$usuario->nombreTipoUsuario}}</option>
                    @endforeach
                  </select>
                  </div>
                </div>
                
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <div class="col-sm-6">
                 <button type="submit" class="btn btn-success btn-lg pull-right">Guardar</button>
                  </div>
                  <div class="col-sm-6">
                     <a href="{{ route('menuPadre.index') }}" class="btn btn-default btn-lg" >Lista menu</a>
                 
                  </div>
                </div>
                
               
              </div>
              <!-- /.box-footer -->
            </form>
          </div>
          
          <!-- /.box -->
        </div>
        <!--/.col (right) -->
      </div>
@endsection