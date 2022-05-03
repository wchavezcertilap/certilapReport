<?php

namespace App\Http\Controllers;
use DB;
use App\DatosUsuarioLogin;
use App\UsuarioContratista;
use App\UsuarioPrincipal;
use App\FolioSso;
use App\empresaPrincipal;
use App\Periodo;
use App\Month;
use App\Contratista;
use App\Solicitud;
use App\Certificado;
use App\Cuadratura;
use App\TrabajadorVerificacion;
use App\tipoEmpresa;
use App\tipoServicio;
use App\categoriaServicio;
use App\direccion;
use App\gerencia;
use App\EstadoCargaMasiva;
use App\DocumentoRechazdo;
use App\CertificateHistory;
use App\LoginUsuario;
use App\DesactivaUsuario;
use Illuminate\Http\Request;


class UsuarioDesactivarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $idUsuario = session('user_id');
        if($idUsuario ==  ""){
            return view('sesion.index');
        }
        $usuarioAqua = session('user_aqua');
        $usuarioABBChile= session('user_ABB');
        $usuarioNOKactivo = session('usuario_nok');
        $certificacion = session('certificacion');
        $principalesTexto = "";
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
        $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
        $UsuarioPrincipal->load('usuarioDatos');
        $usuarioDesactivados = 0;
        $usuarioEliminados = 0;


        if($datosUsuarios->type == 1){

            $EmpresasP = empresaPrincipal::distinct()->orderBy('name', 'ASC')->get(['name','rut']);
            $Contratistas = Contratista::distinct()->orderBy('name', 'ASC')->get(['name','rut']);
        }

       
        return view('usuarioDesactiva.index',compact('EmpresasP','periodos','datosUsuarios','certificacion','usuarioAqua','usuarioABBChile','Contratistas','usuarioDesactivados','usuarioEliminados','usuarioNOKactivo'));
    
        //////// busqueda de datos //////
    }   

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $idUsuario = session('user_id');
        if($idUsuario ==  ""){
            return view('sesion.index');
        }
        $usuarioAqua = session('user_aqua');
        $usuarioABBChile= session('user_ABB');
        $usuarioNOKactivo = session('usuario_nok');
        $certificacion = session('certificacion');
        $principalesTexto = "";
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
        $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
        $UsuarioPrincipal->load('usuarioDatos');
        $usuarioDesactivados = 0;
        $usuarioEliminados = 0;

        if($datosUsuarios->type == 1){

            $EmpresasP = empresaPrincipal::distinct()->orderBy('name', 'ASC')->get(['name','rut']);
            $Contratistas = Contratista::distinct()->orderBy('name', 'ASC')->get(['name','rut']);
        }


        $input=$request->all();    
        $countPrincipal = 0;
        if(!empty($input["empresaPrincipal"])){
            $empresaPrincipal = $input["empresaPrincipal"];

            foreach ($empresaPrincipal as $value) {
                $rutPrincipal[] = $value;
            }

            $countPrincipal =count($rutPrincipal); 
        }

        $countContratista = 0;
        if(!empty($input["contratista"])){
            $empresaContratista = $input["contratista"];

            foreach ($empresaContratista as $value2) {
                $rutcontratistasR[] = $value2;
            }

            $countContratista =count($rutcontratistasR); 
        }
            $tipoBsuqueda = $input["tipoBsuqueda"];
            $fechaSeleccion = $input["fechaSeleccion"];
            $fechas = $porciones = explode("_", $fechaSeleccion);
            $fecha1 = $fechas[0];
            $fecha2 = $fechas[1];
            $periodosT = $fecha1 ."-".$fecha2;
            $fechasDesde = strtotime ( '+1 hour' ,strtotime($fecha1));     
            $fechasHasta = strtotime ( '+1 hour' ,strtotime($fecha2));
            $currtme    = time();

        $estadoUsuario = $input["estadoUsuario"];
    /// busquedad principal
        if ($tipoBsuqueda == 1 and $estadoUsuario == 1){

            $UsuariosPrincipal = UsuarioPrincipal::whereIn('mainCompanyRut',$rutPrincipal)->get(['systemUserId']);

            if(!empty($UsuariosPrincipal)){
                foreach ($UsuariosPrincipal AS $usuario){

                    if(!empty($usuario->systemUserId)){

                        $UsuariosPrincipales = UsuarioPrincipal::where('systemUserId',$usuario->systemUserId)->get(['systemUserId','mainCompanyRut']);
                        foreach ($UsuariosPrincipales AS $usuario2){


                            $datosUsuario = DatosUsuarioLogin::where("id",'=',$usuario2->systemUserId)->where("type",'=',3)->get(['id','name','username','email'])->toArray();

                            if(!empty($datosUsuario)){

                                $usuarioDesctivadoCon = DesactivaUsuario::where("user_id",'=',$datosUsuario[0]['id'])->get(['user_id'])->toArray();

                               // print_r($usuarioDesctivadoCon);
                                if(empty($usuarioDesctivadoCon[0]['user_id'])){
                                   
                                        $loginUsuario = LoginUsuario::where("user_id",$datosUsuario[0]['id'])
                                        ->whereBetween('login_date', [$fechasDesde,$fechasHasta])
                                        ->orderby('login_date','DESC')->take(1)->get(['login_date'])->toArray();



                                        if(!empty($loginUsuario)){

                                            $principalEmpre = empresaPrincipal::distinct()->where('rut',$usuario2->mainCompanyRut)->orderBy('name', 'ASC')->get(['name','rut'])->toArray();
                                            $nombrePrincipal = strtoupper($principalEmpre[0]['name']);

                                            $datosReporte["idUsuario"] = $datosUsuario[0]['id'];
                                            $datosReporte["nombre"] = strtoupper($datosUsuario[0]['name']);
                                            $datosReporte["usuario"] = strtoupper($datosUsuario[0]['username']);
                                            $datosReporte["email"] = strtoupper($datosUsuario[0]['email']);
                                            $datosReporte["fechaLogin"] = date("d/m/Y",$loginUsuario[0]['login_date']);
                                            $datosReporte["empresaPrincipal"] = strtoupper($nombrePrincipal);
                                            $USUARIOS[] = $datosReporte;
                                        }
                                       
                                }
                            }

                        }

                    }

                }
            }


            if(!empty($USUARIOS)){ 

                $lista='
                <div class="col-md-4">
                    <input type="checkbox" id="checkTodos"/> Marcar/Desmarcar Todos
                </div>
                 <div class="col-md-4">
                    <button type="button" class="btn btn-warning" id="desactivarUsuario">Desactivar</button>
                    <input type="hidden" value = "" name="desactivarUsuarioVal" id="desactivarUsuarioVal">  
                </div>
                 <div class="col-md-4">
                    <button type="button" class="btn btn-danger" id="eliminarUsuario">Eliminar</button>
                    <input type="hidden" value = "" name="eliminarUsuarioVal" id="eliminarUsuarioVal">
                </div>            
                <table id="datosTabla" class="table table-bordered table-striped">
                    <thead>
                    
                    <tr>
                      <th>Seleccione</th>
                      <th>Id</th>
                      <th>Nombre</th>
                      <th>Usuario</th>
                      <th>Correo</th>
                      <th>Fecha Login</th>
                      <th>Empresa principal</th>
                      <th>Estado</th>
                    </tr>
                    </thead>
                    <tbody>';
                    foreach ($USUARIOS as $rcertificacion) {

                        $lista.= "<tr>";
                        $lista.= "<td><input type='checkbox' value='".$rcertificacion["idUsuario"]."' id='idUsuario[]' name='idUsuario[]'></td>";
                        $lista.= "<td>".$rcertificacion["idUsuario"]."</td>";
                        $lista.= "<td>".$rcertificacion["nombre"]."</td>";
                        $lista.= "<td>".$rcertificacion["usuario"]."</td>";
                        $lista.= "<td>".$rcertificacion["email"]."</td>";
                        $lista.= "<td>".$rcertificacion["fechaLogin"]."</td>";
                         $lista.= "<td>".$rcertificacion["empresaPrincipal"]."</td>";
                        $lista.= "<td>Activo</td>";
                        $lista.= "</tr>";
                    }

                    $lista.= "</table>";
                   
                    return view('usuarioDesactiva.index',compact('EmpresasP','periodos','datosUsuarios','certificacion','usuarioAqua','usuarioABBChile','Contratistas','USUARIOS','lista','usuarioDesactivados','usuarioEliminados','usuarioNOKactivo')); 
            }
           
        }if ($tipoBsuqueda == 1 and $estadoUsuario == 2){

            $UsuariosPrincipal = UsuarioPrincipal::whereIn('mainCompanyRut',$rutPrincipal)->get(['systemUserId']);
            if(!empty($UsuariosPrincipal)){
                foreach ($UsuariosPrincipal AS $usuario){

                    if(!empty($usuario->systemUserId)){

                        $UsuariosPrincipales = UsuarioPrincipal::where('systemUserId',$usuario->systemUserId)->get(['systemUserId','mainCompanyRut']);

                        foreach ($UsuariosPrincipales AS $usuario2){

                            $datosUsuario =  DB::table('SystemUser')
                            ->join('xt_user_disabled','xt_user_disabled.user_id','=','SystemUser.id')
                            ->where("id",'=',$usuario2->systemUserId)
                            ->where("type",'=',3)
                            ->get(['SystemUser.id','SystemUser.name','SystemUser.username','SystemUser.email','xt_user_disabled.disabled_date'])->toArray();
                      
                            if(!empty($datosUsuario)){
                                $loginUsuario = LoginUsuario::where("user_id",'=',$datosUsuario[0]->id)
                                ->whereBetween('login_date', [$fechasDesde,$fechasHasta])
                                ->orderby('login_date','DESC')->take(1)->get(['login_date'])->toArray();

                                if(empty($loginUsuario)){
                                    $principalEmpre = empresaPrincipal::distinct()->where('rut',$usuario2->mainCompanyRut)->orderBy('name', 'ASC')->get(['name','rut'])->toArray();
                                    $nombrePrincipal = strtoupper($principalEmpre[0]['name']);
                                    $datosReporte["idUsuario"] = $datosUsuario[0]->id;
                                    $datosReporte["nombre"] = strtoupper($datosUsuario[0]->name);
                                    $datosReporte["usuario"] = strtoupper($datosUsuario[0]->username);
                                    $datosReporte["email"] = strtoupper($datosUsuario[0]->email);
                                    $datosReporte["fechaLogin"] = date("d/m/Y",$loginUsuario[0]['login_date']);
                                    $datosReporte["empresaPrincipal"] = strtoupper($nombrePrincipal);
                                    $USUARIOS[] = $datosReporte;
                                }
                               
                            }
                        }

                    }

                }
                if(!empty($USUARIOS)){ 

                    $lista='<div class="col-md-4">
                        <input type="checkbox" id="checkTodos"/> Marcar/Desmarcar Todos
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-warning" id="desactivarUsuario">Desactivar</button>
                        <input type="hidden" value = "" name="desactivarUsuarioVal" id="desactivarUsuarioVal">  
                    </div>
                     <div class="col-md-4">
                        <button type="button" class="btn btn-danger" id="eliminarUsuario">Eliminar</button>
                        <input type="hidden" value = "" name="eliminarUsuarioVal" id="eliminarUsuarioVal">
                    </div>                 
                    <table id="datosTabla" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                         <th>Seleccione</th>
                          <th>Id</th>
                          <th>Nombre</th>
                          <th>Usuario</th>
                          <th>Correo</th>
                          <th>Fecha Login</th>
                          <th>Estado</th>
                        </tr>
                        </thead>
                        <tbody>';
                        foreach ($USUARIOS as $rcertificacion) {

                            $lista.= "<tr>";
                            $lista.= "<td><input type='checkbox' value='".$rcertificacion["idUsuario"]."' id='idUsuario[]' name='idUsuario[]'></td>";
                            $lista.= "<td>".$rcertificacion["idUsuario"]."</td>";
                            $lista.= "<td>".$rcertificacion["nombre"]."</td>";
                            $lista.= "<td>".$rcertificacion["usuario"]."</td>";
                            $lista.= "<td>".$rcertificacion["email"]."</td>";
                            $lista.= "<td>".$rcertificacion["fechaLogin"]."</td>";
                            $lista.= "<td>Desactivado</td>";
                            $lista.= "</tr>";
                        }

                        $lista.= "</table>";
                }
                return view('usuarioDesactiva.index',compact('EmpresasP','periodos','datosUsuarios','certificacion','usuarioAqua','usuarioABBChile','Contratistas','USUARIOS','lista','usuarioDesactivados','usuarioEliminados','usuarioNOKactivo')); 
            }
        }
        if($tipoBsuqueda == 2 and $estadoUsuario == 1){

            $UsuariosContratista = UsuarioContratista::whereIn('companyRut',$rutcontratistasR)->get(['systemUserId']);

            if(!empty($UsuariosContratista)){
                foreach ($UsuariosContratista AS $usuario){

                    if(!empty($usuario->systemUserId)){

                        $UsuariosContratistas = UsuarioContratista::where('systemUserId',$usuario->systemUserId)->get(['systemUserId','companyRut']);

                        foreach ($UsuariosContratistas AS $usuario2){

                            $datosUsuario = DatosUsuarioLogin::where("id",'=',$usuario2->systemUserId)->where("type",'=',4)->get(['id','name','username','email'])->toArray();

                            if(!empty($datosUsuario)){
                                $usuarioDesctivadoCon = DesactivaUsuario::where("user_id",'=',$datosUsuario[0]['id'])->get(['user_id'])->toArray();
                               // print_r($usuarioDesctivadoCon);
                            }

                            if(empty($usuarioDesctivadoCon)){
                                if(!empty($datosUsuario)){
                                    $loginUsuario = LoginUsuario::where("user_id",'=',$datosUsuario[0]['id'])
                                    ->whereBetween('login_date', [$fechasDesde,$fechasHasta])
                                    ->orderby('login_date','DESC')->take(1)->get(['login_date'])->toArray();

                                    if(!empty($loginUsuario)){
                                        $contratistaEmp = Contratista::distinct()->where('rut',$usuario2->companyRut)->orderBy('name', 'ASC')->get(['name','rut'])->toArray();
                                        $nombreContratista = strtoupper($contratistaEmp[0]['name']);
                                        $datosReporte["idUsuario"] = $datosUsuario[0]['id'];
                                        $datosReporte["nombre"] = strtoupper($datosUsuario[0]['name']);
                                        $datosReporte["usuario"] = strtoupper($datosUsuario[0]['username']);
                                        $datosReporte["email"] = strtoupper($datosUsuario[0]['email']);
                                        $datosReporte["fechaLogin"] = date("d/m/Y",$loginUsuario[0]['login_date']);
                                        $datosReporte["contratista"] = $nombreContratista;
                                        $USUARIOS[] = $datosReporte;
                                    }
                                }
                            }
                        }
                    }
                }
                if(!empty($USUARIOS)){ 

                    $lista='
                    <div class="col-md-4">
                    <input type="checkbox" id="checkTodos"/> Marcar/Desmarcar Todos
                    </div>
                    <div class="col-md-4">
                    <button type="button" class="btn btn-warning" id="desactivarUsuario">Desactivar</button>
                    <input type="hidden" value = "" name="desactivarUsuarioVal" id="desactivarUsuarioVal">  
                    </div>
                    <div class="col-md-4">
                    <button type="button" class="btn btn-danger" id="eliminarUsuario">Eliminar</button>
                    <input type="hidden" value = "" name="eliminarUsuarioVal" id="eliminarUsuarioVal">
                    </div>            
                    <table id="datosTabla" class="table table-bordered table-striped">
                    <thead>

                    <tr>
                      <th>Seleccione</th>
                      <th>Id</th>
                      <th>Nombre</th>
                      <th>Usuario</th>
                      <th>Correo</th>
                      <th>Fecha Login</th>
                      <th>Contratista</th>
                      <th>Estado</th>
                    </tr>
                    </thead>
                    <tbody>';
                        foreach ($USUARIOS as $rcertificacion) {

                            $lista.= "<tr>";
                            $lista.= "<td><input type='checkbox' value='".$rcertificacion["idUsuario"]."' id='idUsuario[]' name='idUsuario[]'></td>";
                            $lista.= "<td>".$rcertificacion["idUsuario"]."</td>";
                            $lista.= "<td>".$rcertificacion["nombre"]."</td>";
                            $lista.= "<td>".$rcertificacion["usuario"]."</td>";
                            $lista.= "<td>".$rcertificacion["email"]."</td>";
                            $lista.= "<td>".$rcertificacion["fechaLogin"]."</td>";
                            $lista.= "<td>".$rcertificacion["contratista"]."</td>";
                            $lista.= "<td>Activo</td>";
                            $lista.= "</tr>";
                        }

                    $lista.= "</table>";
                }
                return view('usuarioDesactiva.index',compact('EmpresasP','periodos','datosUsuarios','certificacion','usuarioAqua','usuarioABBChile','Contratistas','USUARIOS','lista','usuarioDesactivados','usuarioEliminados','usuarioNOKactivo'));
            }
            
        }
        if($tipoBsuqueda == 2 and $estadoUsuario == 2){
            $UsuariosContratista = UsuarioContratista::whereIn('companyRut',$rutcontratistasR)->get(['systemUserId']);
            if(!empty($UsuariosContratista)){
                foreach ($UsuariosContratista AS $usuario){

                    if(!empty($usuario->systemUserId)){

                        $datosUsuario =  DB::table('SystemUser')
                        ->join('xt_user_disabled','xt_user_disabled.user_id','=','SystemUser.id')
                        ->where("id",'=',$usuario->systemUserId)
                        ->where("type",'=',4)
                        ->get(['SystemUser.id','SystemUser.name','SystemUser.username','SystemUser.email','xt_user_disabled.disabled_date'])->toArray();


                        if(!empty($datosUsuario)){
                            $loginUsuario = LoginUsuario::where("user_id",'=',$datosUsuario[0]->id)
                            ->whereBetween('login_date', [$fechasDesde,$fechasHasta])
                            ->orderby('login_date','DESC')->take(1)->get(['login_date'])->toArray();

                            if(empty($loginUsuario)){
                                $datosReporte["idUsuario"] = $datosUsuario[0]->id;
                                $datosReporte["nombre"] = strtoupper($datosUsuario[0]->name);
                                $datosReporte["usuario"] = strtoupper($datosUsuario[0]->username);
                                $datosReporte["email"] = strtoupper($datosUsuario[0]->email);
                                $datosReporte["fechaLogin"] = date("d/m/Y",$loginUsuario[0]['login_date']);
                                $USUARIOS[] = $datosReporte;
                            }  
                        }
                    }
                }

                if(!empty($USUARIOS)){ 

                    $lista='
                    <div class="col-md-4">
                        <input type="checkbox" id="checkTodos"/> Marcar/Desmarcar Todos
                    </div>
                     <div class="col-md-4">
                        <button type="button" class="btn btn-warning" id="desactivarUsuario">Desactivar</button>
                        <input type="hidden" value = "" name="desactivarUsuarioVal" id="desactivarUsuarioVal">  
                    </div>
                     <div class="col-md-4">
                        <button type="button" class="btn btn-danger" id="eliminarUsuario">Eliminar</button>
                        <input type="hidden" value = "" name="eliminarUsuarioVal" id="eliminarUsuarioVal">
                    </div>            
                    <table id="datosTabla" class="table table-bordered table-striped">
                        <thead>
                        
                        <tr>
                          <th>Seleccione</th>
                          <th>Id</th>
                          <th>Nombre</th>
                          <th>Usuario</th>
                          <th>Correo</th>
                          <th>Fecha Login</th>
                          <th>Estado</th>
                        </tr>
                        </thead>
                        <tbody>';
                        foreach ($USUARIOS as $rcertificacion) {

                            $lista.= "<tr>";
                            $lista.= "<td><input type='checkbox' value='".$rcertificacion["idUsuario"]."' id='idUsuario[]' name='idUsuario[]'></td>";
                            $lista.= "<td>".$rcertificacion["idUsuario"]."</td>";
                            $lista.= "<td>".$rcertificacion["nombre"]."</td>";
                            $lista.= "<td>".$rcertificacion["usuario"]."</td>";
                            $lista.= "<td>".$rcertificacion["email"]."</td>";
                            $lista.= "<td>".$rcertificacion["fechaLogin"]."</td>";
                            $lista.= "<td>Activo</td>";
                            $lista.= "</tr>";
                        }

                        $lista.= "</table>";
                }
                    return view('usuarioDesactiva.index',compact('EmpresasP','periodos','datosUsuarios','certificacion','usuarioAqua','usuarioABBChile','Contratistas','USUARIOS','lista','usuarioDesactivados','usuarioEliminados','usuarioNOKactivo'));
            }

        }

    }


    public function desactiva()
    {
        if(!empty($_POST['idUsuario'])){
            
            if(!empty($_POST['desactivarUsuarioVal'])){
             
                $desactiva = $_POST['desactivarUsuarioVal'];
                if($desactiva==1){
                     
                    $fechaDesactivacion = time();

                    foreach ($_POST['idUsuario'] as $id) {

                        DB::table('xt_user_disabled')->insert(
                        ['user_id' => $id, 
                         'disabled_date' => $fechaDesactivacion
                        ]);
                    }
                }
                $usuarioDesactivados = 1;
                $usuarioEliminados = 0;
            }
            if(!empty($_POST['eliminarUsuarioVal'])){

                $eliminarUsuarioVal = $_POST['eliminarUsuarioVal'];
                if($eliminarUsuarioVal==1){
                 
                    foreach ($_POST['idUsuario'] as $id) {

                        DB::table('SystemUserMainCompany')->where('systemUserId', '=', $id)->delete();
                        DB::table('SystemUserCompany')->where('systemUserId', '=', $id)->delete();
                        DB::table('xt_user_disabled')->where('user_id', '=', $id)->delete();
                        DB::table('xt_user_logins')->where('user_id', '=', $id)->delete();
                        DB::table('SystemUser')->where('id', '=', $id)->delete();
                    }
                }

                 $usuarioEliminados = 1;
                 $usuarioDesactivados = 0; 
            }

            $idUsuario = session('user_id');
            if($idUsuario ==  ""){
                return view('sesion.index');
            }
            $usuarioAqua = session('user_aqua');
            $usuarioABBChile= session('user_ABB');
            $certificacion = session('certificacion');
            $usuarioNOKactivo = session('usuario_nok');
            $principalesTexto = "";
            $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
            $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
            $UsuarioPrincipal->load('usuarioDatos');

            if($datosUsuarios->type == 1){

                $EmpresasP = empresaPrincipal::distinct()->orderBy('name', 'ASC')->get(['name','rut']);
                $Contratistas = Contratista::distinct()->orderBy('name', 'ASC')->get(['name','rut']);
            }

           
            return view('usuarioDesactiva.index',compact('EmpresasP','periodos','datosUsuarios','certificacion','usuarioAqua','usuarioABBChile','Contratistas','usuarioEliminados','usuarioDesactivados','usuarioNOKactivo'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


}
