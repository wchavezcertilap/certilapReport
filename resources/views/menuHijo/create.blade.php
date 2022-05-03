@extends('admin_template')
@section('content')
 <div class="row">
      
        <!--/.col (left) -->
        <!-- right column -->
        <div class="col-md-12">
          <!-- Horizontal Form -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Crear Menu Hijo</h3>
            </div>
             @include('messages')


            <!-- /.box-header -->
            <!-- form start -->
            <form method="POST" action="{{ route('menuHijo.store') }}"  role="form" class="form-horizontal">
              {{ csrf_field() }}
              <div class="box-body">
                <div class="form-group">
                  <label for="permisoUsuario" class="col-sm-2 control-label">Menu Padre</label>

                  <div class="col-sm-10">
                    
                  <select class="form-control input-lg" name="idMenuPadre">
                   <option disabled selected>Seleccione menu Padre</option>
                    @foreach($menuPadre as $menuP)
                    <option value="{{$menuP->id}}">{{$menuP->nombreMenu}}</option>
                    @endforeach
                  </select>
                  </div>
                </div>

                <div class="form-group">
                  <label for="nombreMenuHijo" class="col-sm-2 control-label">Nombre Menu Hijo</label>

                  <div class="col-sm-10">
                    <input type="text" class="form-control input-lg"" id="nombreMenuHijo" name="nombreMenuHijo" placeholder="Nombre del menu Hijo" >
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
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <div class="col-sm-6">
                 <button type="submit" class="btn btn-success btn-lg pull-right">Guardar</button>
                  </div>
                  <div class="col-sm-6">
                 <button type="submit" class="btn btn-default btn-lg">volver</button>
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