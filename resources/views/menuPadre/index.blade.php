@extends('admin_template')
@section('content')
 <div class="row">
      
        <!--/.col (left) -->
        <!-- right column -->
        <div class="col-md-12">
          <!-- Horizontal Form -->
         <div class="box">
            <div class="box-header">
              <h3 class="box-title">Lista de Menu Padre</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
             
              <div class="btn-group">
              <a href="{{ route('menuPadre.create') }}" class="btn btn-info" >Añadir Menu Padre</a>
            </div>
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Estado</th>
                  <th>Usuario</th>
                  <th>Fecha de Creación</th>
                  <th>Editar</th>
                  <th>Eliminar</th>
                </tr>
                </thead>
                <tbody>
                  @if($menuPadre->count())  
                  @foreach($menuPadre as $menu)  
                <tr>
                  <td>{{$menu->nombreMenu}}</td>
                  <td>{{$menu->estado}}</td>
                  <td>{{$menu->perfilUsuario}}</td>
                  <td>{{$menu->created_at}}</td>
                  <td><a class="btn btn-primary btn-sm" href="{{action('menuPadreController@edit', $menu->id)}}" ><span class="glyphicon glyphicon-pencil"></span></a></td>
                <td>
                  <form action="{{action('menuPadreController@destroy', $menu->id)}}" method="post">
                   {{csrf_field()}}
                   <input name="_method" type="hidden" value="DELETE">
 
                   <button class="btn btn-danger btn-sm" type="submit"><span class="glyphicon glyphicon-trash"></span></button>
                 </td>
                </tr>
                @endforeach 
                @else
                <tr>
                  <td colspan="8">No hay registro !!</td>
                </tr>
                @endif
               </tbody>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
          
          <!-- /.box -->
        </div>
        <!--/.col (right) -->
      </div>
@endsection