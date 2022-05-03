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
use App\EstadoDocumento;
use App\trabajadorSSO;
use App\Documento;
use App\CargoSSO;
use Illuminate\Http\Request;

class DocumentoReporteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function porContratista($id){
        
        return Contratista::distinct()->where('mainCompanyRut','=',$id)->orderBy('name', 'ASC')->get(['name','rut']);
        
    }

    public function porFolio($id){
        
        return FolioSso::distinct()->where('sso_mcomp_rut','=',$id)->orderBy('id', 'ASC')->get(['id']);
    }


    public function porProyecto($id){
        
        return FolioSso::distinct()->where('sso_mcomp_rut','=',$id)->whereNotNull('sso_project')->orderBy('sso_project', 'ASC')->get(['sso_project']);
    }


    public function index(Request $request)
    {
        $certificacion = session('certificacion');
        $usuarioAqua = session('user_aqua');
        $usuarioABBChile= session('user_ABB');
        $usuarioClaroChile= session('user_Claro');
        $usuarioNOKactivo = session('usuario_nok');
        $idUsuario = session('user_id');
        if($idUsuario ==  ""){
            return view('sesion.index');
        }
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
        $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
        $UsuarioPrincipal->load('usuarioDatos');


        foreach ($UsuarioPrincipal as $rut) {

            $rutprincipal[]=$rut['mainCompanyRut'];
            
        }

            if($datosUsuarios->type == 3){

                $EmpresasP = FolioSso::distinct()->whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['sso_mcomp_name','sso_mcomp_rut']);

            }
            if($datosUsuarios->type == 2 or $datosUsuarios->type ==1){

              $EmpresasP = FolioSso::distinct()->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['sso_mcomp_name','sso_mcomp_rut']);

            }


        return view('documentoReporte.index',compact('datosUsuarios','EmpresasP','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile')); 
 
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
        $certificacion = session('certificacion');
        $usuarioAqua = session('user_aqua');
        $usuarioABBChile= session('user_ABB');
        $usuarioClaroChile= session('user_Claro');
        $usuarioNOKactivo = session('usuario_nok');
        $idUsuario = session('user_id');
        if($idUsuario ==  ""){
            return view('sesion.index');
        }
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
        $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
        $UsuarioPrincipal->load('usuarioDatos');


        foreach ($UsuarioPrincipal as $rut) {

            $rutprincipal[]=$rut['mainCompanyRut'];
            
        }

            if($datosUsuarios->type == 3){

                $EmpresasP = FolioSso::distinct()->whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['sso_mcomp_name','sso_mcomp_rut']);

            }
            if($datosUsuarios->type == 2 or $datosUsuarios->type ==1){

              $EmpresasP = FolioSso::distinct()->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['sso_mcomp_name','sso_mcomp_rut']);

            }
        $input=$request->all();
        //print_r($input);
      
        $empresasPrincipales = $input["empresaPrincipal"];
        if(!empty($input["empresaContratista"])){
            $empresaContratista = $input["empresaContratista"];
            foreach ($empresaContratista as $value2) {

                $rutcontratistasR[] = $value2;
            }
            $cantidadCon = count($rutcontratistasR);
        }else{
            $cantidadCon = 0;
        }

        if(!empty($input["folio"])){
            $folio = $input["folio"];
        }else{
            $folio= "";
        }
        if(!empty($input["proyecto"])){
            $proyecto = $input["proyecto"];
        }else{
            $proyecto="";
        }
 
        if($datosUsuarios->type == 3){

            if($empresasPrincipales[0] == 1){

                if($cantidadCon > 0 and $folio!="" and $proyecto!=""){

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status', '1')->whereIn('sso_comp_rut',$rutcontratistasR)
                    ->where('id', $folio)->where('sso_project', $proyecto)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);

                }elseif ($cantidadCon > 0 and $folio!="" and $proyecto=="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status', '1')->whereIn('sso_comp_rut',$rutcontratistasR)
                    ->where('id', $input["folio"])
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);
                }elseif ($cantidadCon > 0 and $folio=="" and $proyecto=="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status', '1')->whereIn('sso_comp_rut',$rutcontratistasR)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);
                }elseif ($cantidadCon == 0 and $folio!="" and $proyecto!="") {

                    $folios = FolioSso::where('id',$folio)->where('sso_project', $proyecto)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);
                }elseif ($cantidadCon == 0 and $folio!="" and $proyecto=="") {

                    $folios = FolioSso::where('id',$folio)->where('sso_status', '1')
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);
                }elseif ($cantidadCon == 0 and $folio=="" and $proyecto!="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status', '1')->where('sso_project', $proyecto)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);
                }
                else{
                    $folios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status', '1')
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);
                }

            }else{

                if($cantidadCon > 0 and $folio!="" and $proyecto!=""){

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('sso_status', '1')->whereIn('sso_comp_rut',$rutcontratistasR)
                    ->where('id', $folio)->where('id', $proyecto)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);

                }elseif ($cantidadCon > 0 and $folio!="" and $proyecto=="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('sso_status', '1')->whereIn('sso_comp_rut',$rutcontratistasR)
                    ->where('id', $input["folio"])
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);
                }elseif ($cantidadCon > 0 and $folio=="" and $proyecto=="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('sso_status', '1')->whereIn('sso_comp_rut',$rutcontratistasR)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);
                }elseif ($cantidadCon == 0 and $folio!="" and $proyecto!="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('id',$folio)->where('sso_project', $proyecto)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);
                }elseif ($cantidadCon == 0 and $folio!="" and $proyecto=="") {

                    $folios = FolioSso::where('id',$folio)->where('sso_status', '1')
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);
            
                }elseif ($cantidadCon == 0 and $folio=="" and $proyecto!="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('sso_project',$proyecto)->where('sso_status', '1')
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);
            
                }elseif ($cantidadCon == 0 and $folio=="" and $proyecto="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('sso_status', '1')->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);
                }else{
                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('sso_status', '1')->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);
                }
            }
        }
        if($datosUsuarios->type == 2 or $datosUsuarios->type ==1){

            if($empresasPrincipales[0] == 1){

                if($cantidadCon > 0 and $folio!="" and $proyecto!=""){

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status', '1')->whereIn('sso_comp_rut',$rutcontratistasR)
                    ->where('id', $folio)->where('sso_project', $proyecto)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);

                }elseif ($cantidadCon > 0 and $folio!="" and $proyecto=="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status', '1')->whereIn('sso_comp_rut',$rutcontratistasR)
                    ->where('id', $input["folio"])
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);
                }elseif ($cantidadCon > 0 and $folio=="" and $proyecto=="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status', '1')->whereIn('sso_comp_rut',$rutcontratistasR)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);
                }elseif ($cantidadCon == 0 and $folio!="" and $proyecto!="") {

                    $folios = FolioSso::where('id',$folio)->where('sso_project', $proyecto)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);
                }elseif ($cantidadCon == 0 and $folio!="" and $proyecto=="") {

                    $folios = FolioSso::where('id',$folio)->where('sso_status', '1')
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);
                }elseif ($cantidadCon == 0 and $folio=="" and $proyecto!="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status', '1')->where('sso_project', $proyecto)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);
                }
                else{
                    $folios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status', '1')
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);
                }

            }else{

                if($cantidadCon > 0 and $folio!="" and $proyecto!=""){

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('sso_status', '1')->whereIn('sso_comp_rut',$rutcontratistasR)
                    ->where('id', $folio)->where('id', $proyecto)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);

                }elseif ($cantidadCon > 0 and $folio!="" and $proyecto=="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('sso_status', '1')->whereIn('sso_comp_rut',$rutcontratistasR)
                    ->where('id', $input["folio"])
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);
                }elseif ($cantidadCon > 0 and $folio=="" and $proyecto=="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('sso_status', '1')->whereIn('sso_comp_rut',$rutcontratistasR)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);
                }elseif ($cantidadCon == 0 and $folio!="" and $proyecto!="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('id',$folio)->where('sso_project', $proyecto)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);
                }elseif ($cantidadCon == 0 and $folio!="" and $proyecto=="") {

                    $folios = FolioSso::where('id',$folio)->where('sso_status', '1')
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);
            
                }elseif ($cantidadCon == 0 and $folio=="" and $proyecto!="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('sso_project',$proyecto)->where('sso_status', '1')
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);
            
                }elseif ($cantidadCon == 0 and $folio=="" and $proyecto="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('sso_status', '1')->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);
                }else{
                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('sso_status', '1')->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project']);
                }
            }

        }   
        $tipoInforme = $input["tipoInforme"];
        $fechaSeleccion = $input["fechaSeleccion"];
        
        if (!empty($folios)) {
           
            $fechas = explode(" ", $fechaSeleccion);
            $fecha1 = trim($fechas[0]);
            $fecha2 = trim($fechas[1]);
            $fechasDesde = strtotime(str_replace('/', '-', $fecha1));
            $fechasHasta = strtotime(str_replace('/', '-', $fecha2));
        
            foreach ($folios as  $folio) {
                $folioID[]= $folio["id"];
            }
         
            $totalDocuementosEmpresa = 0;
            $totalTrabajadoresEmpresa = 0;
        
            $datosTrabajadores = trabajadorSSO::where('worker_status','1')->whereIn('sso_id',$folioID)->count();
            $totalTrabajadoresEmpresa = number_format($datosTrabajadores);
     
            $totalTB = 0;
            $totalDoc = 0;
            $totalDocRechazados = 0;
            $totalDocAprobados = 0;
            $totalDocVencidos = 0;
            $totalDocRevision = 0;
            $totalDocAprobadosObs = 0;

            foreach ($folios as  $folio) {
                

                if($tipoInforme != 1){
                $documentos = EstadoDocumento::where('upld_sso_id',$folio->id)->where('upld_status', '1')->whereBetween('upld_upddat', [$fechasDesde,$fechasHasta])->orderBy('upld_workerid', 'ASC')->get()->toArray();
                
                }
               
               
                $cantidadAprobados = 0;
                $cantidadRechazados = 0;
                $cantidadVencidos = 0;
                $cantidadAprobadoObse = 0;
                $cantidadPorRevision = 0;

       
                if($tipoInforme == 5){
                    $nombreDoc="";
                    foreach ($documentos as $value) {
                            unset($datosReporte);
                            ////////////////// fecha para determinar si esta expirado /////
                            $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
                            $fecha2 = $value["upld_vence_date"];
                            $fechaUpdate = $value["upld_upddat"];
                            
                            
                            if ($value["upld_workerid"] > 0){
                            
                                $nombreDoc = Documento::where('id',$value["upld_docid"])->get();
                                //$trabajador = trabajadorSSO::where('worker_status','1')->where('id',$value["upld_workerid"])->where('sso_id',$value["upld_sso_id"])->get()->toArray();

                                $trabajador = DB::table('xt_ssov2_header_worker')
                                ->join('xt_ssov2_cargos', 'xt_ssov2_cargos.id', '=', 'xt_ssov2_header_worker.worker_cargoid')
                                ->where(['xt_ssov2_header_worker.id' => $value["upld_workerid"]])
                                ->where(['xt_ssov2_header_worker.worker_status' => 1 ])
                                ->where(['xt_ssov2_header_worker.sso_id' => $value["upld_sso_id"]])
                                ->get(['xt_ssov2_header_worker.worker_name1',
                                        'xt_ssov2_header_worker.worker_name2',
                                        'xt_ssov2_header_worker.worker_name3',
                                        'xt_ssov2_header_worker.worker_rut',
                                        'xt_ssov2_header_worker.worker_cargoid',
                                        'xt_ssov2_header_worker.worker_syscargoname',
                                        'xt_ssov2_cargos.cargo_name'])->toArray();

                                if(!empty($trabajador[0]->worker_name1)){
                                    if($value["upld_docaprob"] == 0 and $value["upld_docaprob_uid"] == 0 and $value["upld_rechazado"]==0 and $value["upld_venced"]== 0 and $value["upld_aprobComen"] == 0){
                                           
                                            $estadoDocumento ="Por Revisión";
                                    }
                                    elseif(($value["upld_docaprob"] == 1 or $fecha_actual <= $fechaUpdate) and $fecha_actual < $fecha2 AND $value["upld_aprobComen"]== 0 and $value["upld_rechazado"] == 0){
                                        
                                            $estadoDocumento ="Aprobado";
                                    
                                    }elseif (($value["upld_venced"]== 1  or $fecha_actual > $fecha2)and $value["upld_rechazado"] == 0 and $fecha2!= 0){
                                            $estadoDocumento ="Vencido";
                                    }elseif ($value["upld_aprobComen"] == 1 and $value["upld_comments"]!="" and $value["upld_rechazado"] == 0){
                                            
                                            $estadoDocumento ="Aprobado Obs";
                                    }elseif ($value["upld_rechazado"] == 1) {
                                            
                                            $estadoDocumento ="Rechazado";
                                    }
                                    
                                    $datosReporte["folio"] = $folio["id"];
                                    $datosReporte["empresaPrincipal"] = strtoupper($folio["sso_mcomp_name"]);
                                    $datosReporte["rutEmpresaPrincipal"] = strtoupper($folio["sso_mcomp_rut"]."-".$folio["sso_mcomp_dv"]);
                                    $datosReporte["empresaContratista"] = strtoupper($folio["sso_comp_name"]);
                                    $datosReporte["rutEmpresaContratista"] = strtoupper($folio["sso_comp_rut"]."-".$folio["sso_comp_dv"]);
                                    if($folio["sso_subcomp_active"] == 1){
                                    $datosReporte["empresaSubContratista"] = strtoupper($folio["sso_subcomp_name"]);
                                    $datosReporte["rutEmpresaSubContratista"] = strtoupper($folio["sso_subcomp_rut"]."-".$folio["sso_subcomp_dv"]);
                                    }else{
                                    $datosReporte["empresaSubContratista"] = "";
                                    $datosReporte["rutEmpresaSubContratista"] = "";   
                                    } 
                                    $datosReporte["proyecto"] = strtoupper($folio["sso_project"]);
                                    $datosReporte["nombreTrabajador"] = strtoupper($trabajador[0]->worker_name1);
                                    $datosReporte["apellido1Trabajador"] = strtoupper($trabajador[0]->worker_name2);
                                    $datosReporte["apellido2Trabajador"] = strtoupper($trabajador[0]->worker_name3);
                                    $datosReporte["rutTrabajador"] = strtoupper($trabajador[0]->worker_rut);
                                    if($trabajador[0]->worker_syscargoname == ""){
                                        $datosReporte["cargoTrabajador"] = strtoupper($trabajador[0]->cargo_name);
                                    }else{
                                        $datosReporte["cargoTrabajador"] = strtoupper($trabajador[0]->worker_syscargoname);
                                    }
                                    $datosReporte["documentoTrabajador"] = strtoupper($nombreDoc[0]->doc_name);
                                    $datosReporte["estadoDocumento"] = $estadoDocumento;
                                    $datosReporte["fechaCarga"] = date("d-m-Y H:i:00",$value['upld_upddat']);
                                    if($value['upld_vence_date']>0){
                                            $datosReporte["fechaVence"] = date("d-m-Y H:i:00",$value['upld_vence_date']);  
                                    }else{
                                            $datosReporte["fechaVence"] = "";        
                                    }
                                    
                                }
                            }
                            if($value["upld_workerid"] == 0){
                            
                                $nombreDoc = Documento::where('id',$value["upld_docid"])->get();
                                if ($value["upld_docaprob"] == 0 and $value["upld_docaprob_uid"] == 0 and $value["upld_rechazado"]==0 and $value["upld_venced"]== 0 and $value["upld_aprobComen"] == 0){
                                           
                                            $estadoDocumento ="Por Revisión";
                                    }
                                    elseif(($value["upld_docaprob"] == 1 or $fecha_actual <= $fechaUpdate) and $fecha_actual < $fecha2 AND $value["upld_aprobComen"]== 0 and $value["upld_rechazado"] == 0){
                                    
                                            $estadoDocumento ="Aprobado";
                                        
                                    }elseif (($value["upld_venced"]== 1  or $fecha_actual > $fecha2)and $value["upld_rechazado"] == 0 and $fecha2!= 0){
                                          
                                            $estadoDocumento ="Vencido";
                                    }elseif ($value["upld_aprobComen"] == 1 and $value["upld_comments"]!="" and $value["upld_rechazado"] == 0){
                                             
                                            $estadoDocumento ="Aprobado Obs";
                                    }elseif ($value["upld_rechazado"] == 1) {
                                            
                                            $estadoDocumento ="Rechazado";
                                    }

                                $datosReporte["folio"] = $folio["id"];
                                $datosReporte["empresaPrincipal"] = strtoupper($folio["sso_mcomp_name"]);
                                $datosReporte["rutEmpresaPrincipal"] = strtoupper($folio["sso_mcomp_rut"]."-".$folio["sso_mcomp_dv"]);
                                $datosReporte["empresaContratista"] = strtoupper($folio["sso_comp_name"]);
                                $datosReporte["rutEmpresaContratista"] = strtoupper($folio["sso_comp_rut"]."-".$folio["sso_comp_dv"]);
                                if($folio["sso_subcomp_active"] == 1){
                                $datosReporte["empresaSubContratista"] = strtoupper($folio["sso_subcomp_name"]);
                                $datosReporte["rutEmpresaSubContratista"] = strtoupper($folio["sso_subcomp_rut"]."-".$folio["sso_subcomp_dv"]);
                                }else{
                                $datosReporte["empresaSubContratista"] = "";
                                $datosReporte["rutEmpresaSubContratista"] = ""; 
                                }  
                                $datosReporte["proyecto"] = strtoupper($folio["sso_project"]);
                                $datosReporte["nombreTrabajador"] = "";
                                $datosReporte["apellido1Trabajador"] = "";
                                $datosReporte["apellido2Trabajador"] = "";
                                $datosReporte["rutTrabajador"] = "";
                                $datosReporte["cargoTrabajador"] =  "";
                                $datosReporte["documentoTrabajador"] = strtoupper($nombreDoc[0]["doc_name"]);
                                $datosReporte["estadoDocumento"] = $estadoDocumento;
                                $datosReporte["fechaCarga"] = date("d-m-Y H:i:00",$value['upld_upddat']);
                                if($value['upld_vence_date']>0){
                                            $datosReporte["fechaVence"] = date("d-m-Y H:i:00",$value['upld_vence_date']);  
                                }else{
                                            $datosReporte["fechaVence"] = "";        
                                }
                            }

                            if(!empty($datosReporte)){
                             $listaDatosReporte[] =$datosReporte;
                            }
                    }
                }
     
                
                // vencidos ////
                if($tipoInforme == 2){
                   
                    $cantidadAprobados = 0;
                    $cantidadRechazados = 0;
                    $cantidadVencidos = 0;
                    $cantidadPorRevision = 0;
                  
                    $estadoDocumento ="";
                    
                    foreach ($documentos as $value) {
                            unset($datosReporte);
                            ////////////////// fecha para determinar si esta expirado /////
                            $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
                            $fecha2 = $value["upld_vence_date"];
                            $fechaUpdate = $value["upld_upddat"];
                            //////////////////////
                        
                            
                            if (($value["upld_venced"]== 1  or $fecha_actual > $fecha2)and $value["upld_rechazado"] == 0 and $fecha2!= 0)
                            {
                               
                                $estadoDocumento ="Vencido";
                            
                                /// NOMBRE DOCUMENTOS
                                $datosReporte= array();
                                $nombreDoc = Documento::where('id',$value["upld_docid"])->get();
                               
                                    if ($value["upld_workerid"] > 0){
                                       
                                        //$trabajador = trabajadorSSO::where('worker_status','1')->where('id',$value["upld_workerid"])->where('sso_id',$value["upld_sso_id"])->get()->toArray();
                                        $trabajador = DB::table('xt_ssov2_header_worker')
                                        ->join('xt_ssov2_cargos', 'xt_ssov2_cargos.id', '=', 'xt_ssov2_header_worker.worker_cargoid')
                                        ->where(['xt_ssov2_header_worker.id' => $value["upld_workerid"]])
                                        ->where(['xt_ssov2_header_worker.worker_status' => 1 ])
                                        ->where(['xt_ssov2_header_worker.sso_id' => $value["upld_sso_id"]])
                                        ->get(['xt_ssov2_header_worker.worker_name1',
                                                'xt_ssov2_header_worker.worker_name2',
                                                'xt_ssov2_header_worker.worker_name3',
                                                'xt_ssov2_header_worker.worker_rut',
                                                'xt_ssov2_header_worker.worker_cargoid',
                                                'xt_ssov2_header_worker.worker_syscargoname',
                                                'xt_ssov2_cargos.cargo_name'])->toArray();
                                        

                                        if(!empty($trabajador[0]->worker_name1)){
                                            
                                            $datosReporte["folio"] = $folio["id"];
                                            $datosReporte["empresaPrincipal"] = strtoupper($folio["sso_mcomp_name"]);
                                            $datosReporte["rutEmpresaPrincipal"] = strtoupper($folio["sso_mcomp_rut"]."-".$folio["sso_mcomp_dv"]);
                                            $datosReporte["empresaContratista"] = strtoupper($folio["sso_comp_name"]);
                                            $datosReporte["rutEmpresaContratista"] = strtoupper($folio["sso_comp_rut"]."-".$folio["sso_comp_dv"]);
                                            if($folio["sso_subcomp_active"] == 1){
                                            $datosReporte["empresaSubContratista"] = strtoupper($folio["sso_subcomp_name"]);
                                            $datosReporte["rutEmpresaSubContratista"] = strtoupper($folio["sso_subcomp_rut"]."-".$folio["sso_subcomp_dv"]);
                                            }else{
                                            $datosReporte["empresaSubContratista"] = "";
                                            $datosReporte["rutEmpresaSubContratista"] = "";   
                                            } 
                                            $datosReporte["proyecto"] = strtoupper($folio["sso_project"]);
                                            $datosReporte["nombreTrabajador"] = strtoupper($trabajador[0]->worker_name1);
                                            $datosReporte["apellido1Trabajador"] = strtoupper($trabajador[0]->worker_name2);
                                            $datosReporte["apellido2Trabajador"] = strtoupper($trabajador[0]->worker_name3);
                                            $datosReporte["rutTrabajador"] = strtoupper($trabajador[0]->worker_rut);
                                            if($trabajador[0]->worker_syscargoname == ""){
                                                $datosReporte["cargoTrabajador"] = strtoupper($trabajador[0]->cargo_name); 
                                            }else{
                                                $datosReporte["cargoTrabajador"] = strtoupper($trabajador[0]->worker_syscargoname);
                                            }
                                            $datosReporte["documentoTrabajador"] = strtoupper($nombreDoc[0]->doc_name);
                                            $datosReporte["estadoDocumento"] = $estadoDocumento; 
                                            $datosReporte["fechaCarga"] = date("d-m-Y H:i:00",$value['upld_upddat']);
                                            if($value['upld_vence_date']>0){
                                                $datosReporte["fechaVence"] = date("d-m-Y H:i:00",$value['upld_vence_date']);  
                                            }else{
                                                $datosReporte["fechaVence"] = "";        
                                            }    
                                        }
                                    }
                                    if($value["upld_workerid"] == 0){
                                            
                                            $datosReporte["folio"] = $folio["id"];
                                            $datosReporte["empresaPrincipal"] = strtoupper($folio["sso_mcomp_name"]);
                                            $datosReporte["rutEmpresaPrincipal"] = strtoupper($folio["sso_mcomp_rut"]."-".$folio["sso_mcomp_dv"]);
                                            $datosReporte["empresaContratista"] = strtoupper($folio["sso_comp_name"]);
                                            $datosReporte["rutEmpresaContratista"] = strtoupper($folio["sso_comp_rut"]."-".$folio["sso_comp_dv"]);
                                            if($folio["sso_subcomp_active"] == 1){
                                            $datosReporte["empresaSubContratista"] = strtoupper($folio["sso_subcomp_name"]);
                                            $datosReporte["rutEmpresaSubContratista"] = strtoupper($folio["sso_subcomp_rut"]."-".$folio["sso_subcomp_dv"]);
                                            }else{
                                            $datosReporte["empresaSubContratista"] = "";
                                            $datosReporte["rutEmpresaSubContratista"] = ""; 
                                            }
                                            $datosReporte["proyecto"] = strtoupper($folio["sso_project"]); 
                                            $datosReporte["nombreTrabajador"] = "";
                                            $datosReporte["apellido1Trabajador"] = "";
                                            $datosReporte["apellido2Trabajador"] = "";
                                            $datosReporte["rutTrabajador"] = "";
                                            $datosReporte["cargoTrabajador"] =  "";
                                            $datosReporte["documentoTrabajador"] = strtoupper($nombreDoc[0]->doc_name);
                                            $datosReporte["estadoDocumento"] = $estadoDocumento;
                                            $datosReporte["fechaCarga"] = date("d-m-Y H:i:00",$value['upld_upddat']);
                                            if($value['upld_vence_date']>0){
                                                $datosReporte["fechaVence"] = date("d-m-Y H:i:00",$value['upld_vence_date']);  
                                            }else{
                                                $datosReporte["fechaVence"] = "";        
                                            }    
                                            
                                    }

                                    if(!empty($datosReporte)){
                                        $listaDatosReporte[] =$datosReporte;
                                    } 
                            }
                    }
                    
                }
                /// aprobados ///
                if($tipoInforme == 3){
                    
                    $cantidadAprobados = 0;
                    $cantidadRechazados = 0;
                    $cantidadVencidos = 0;
                    $cantidadPorRevision = 0;
                    $estadoDocumento ="";
                    
                    foreach ($documentos as $value) {
                            unset($datosReporte);
                            ////////////////// fecha para determinar si esta expirado /////
                            $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
                            $fecha2 = $value["upld_vence_date"];
                            $fechaUpdate = $value["upld_upddat"];
                            //////////////////////
                        
                            
                            if (($value["upld_docaprob"] == 1 or $fecha_actual <= $fechaUpdate) and $fecha_actual < $fecha2 AND $value["upld_aprobComen"]== 0 and $value["upld_rechazado"] == 0)
                            {
                               
                                $estadoDocumento ="Aprobado";
                            
                                /// NOMBRE DOCUMENTOS
                                $datosReporte= array();
                                $nombreDoc = Documento::where('id',$value["upld_docid"])->get();
                               
                                    if ($value["upld_workerid"] > 0){
                                       
                                        //$trabajador = trabajadorSSO::where('worker_status','1')->where('id',$value["upld_workerid"])->where('sso_id',$value["upld_sso_id"])->get()->toArray();

                                        $trabajador = DB::table('xt_ssov2_header_worker')
                                        ->join('xt_ssov2_cargos', 'xt_ssov2_cargos.id', '=', 'xt_ssov2_header_worker.worker_cargoid')
                                        ->where(['xt_ssov2_header_worker.id' => $value["upld_workerid"]])
                                        ->where(['xt_ssov2_header_worker.worker_status' => 1 ])
                                        ->where(['xt_ssov2_header_worker.sso_id' => $value["upld_sso_id"]])
                                        ->get(['xt_ssov2_header_worker.worker_name1',
                                                'xt_ssov2_header_worker.worker_name2',
                                                'xt_ssov2_header_worker.worker_name3',
                                                'xt_ssov2_header_worker.worker_rut',
                                                'xt_ssov2_header_worker.worker_cargoid',
                                                'xt_ssov2_header_worker.worker_syscargoname',
                                                'xt_ssov2_cargos.cargo_name'])->toArray();
                                        

                                        if(!empty($trabajador[0]->worker_name1)) {
                                            
                                            $datosReporte["folio"] = $folio["id"];
                                            $datosReporte["empresaPrincipal"] = strtoupper($folio["sso_mcomp_name"]);
                                            $datosReporte["rutEmpresaPrincipal"] = strtoupper($folio["sso_mcomp_rut"]."-".$folio["sso_mcomp_dv"]);
                                            $datosReporte["empresaContratista"] = strtoupper($folio["sso_comp_name"]);
                                            $datosReporte["rutEmpresaContratista"] = strtoupper($folio["sso_comp_rut"]."-".$folio["sso_comp_dv"]);
                                            if($folio["sso_subcomp_active"] == 1){
                                            $datosReporte["empresaSubContratista"] = strtoupper($folio["sso_subcomp_name"]);
                                            $datosReporte["rutEmpresaSubContratista"] = strtoupper($folio["sso_subcomp_rut"]."-".$folio["sso_subcomp_dv"]);
                                            }else{
                                            $datosReporte["empresaSubContratista"] = "";
                                            $datosReporte["rutEmpresaSubContratista"] = "";   
                                            } 
                                            $datosReporte["proyecto"] = strtoupper($folio["sso_project"]);
                                            $datosReporte["nombreTrabajador"] = strtoupper($trabajador[0]->worker_name1);
                                            $datosReporte["apellido1Trabajador"] = strtoupper($trabajador[0]->worker_name2);
                                            $datosReporte["apellido2Trabajador"] = strtoupper($trabajador[0]->worker_name3);
                                            $datosReporte["rutTrabajador"] = strtoupper($trabajador[0]->worker_rut);
                                            if($trabajador[0]->worker_syscargoname == ""){
                                                $datosReporte["cargoTrabajador"] = strtoupper($trabajador[0]->cargo_name);
                                            }else{
                                                $datosReporte["cargoTrabajador"] = strtoupper($trabajador[0]->worker_syscargoname);
                                            }
                                            
                                            $datosReporte["documentoTrabajador"] = strtoupper($nombreDoc[0]->doc_name);
                                            $datosReporte["estadoDocumento"] = $estadoDocumento; 
                                            $datosReporte["fechaCarga"] = date("d-m-Y H:i:00",$value['upld_upddat']);
                                            if($value['upld_vence_date']>0){
                                                $datosReporte["fechaVence"] = date("d-m-Y H:i:00",$value['upld_vence_date']);  
                                            }else{
                                                $datosReporte["fechaVence"] = "";        
                                            }    
                                        }
                                    }
                                    if($value["upld_workerid"] == 0){
                                             
                                            $datosReporte["folio"] = $folio["id"];
                                            $datosReporte["empresaPrincipal"] = strtoupper($folio["sso_mcomp_name"]);
                                            $datosReporte["rutEmpresaPrincipal"] = strtoupper($folio["sso_mcomp_rut"]."-".$folio["sso_mcomp_dv"]);
                                            $datosReporte["empresaContratista"] = strtoupper($folio["sso_comp_name"]);
                                            $datosReporte["rutEmpresaContratista"] = strtoupper($folio["sso_comp_rut"]."-".$folio["sso_comp_dv"]);
                                            if($folio["sso_subcomp_active"] == 1){
                                            $datosReporte["empresaSubContratista"] = strtoupper($folio["sso_subcomp_name"]);
                                            $datosReporte["rutEmpresaSubContratista"] = strtoupper($folio["sso_subcomp_rut"]."-".$folio["sso_subcomp_dv"]);
                                            }else{
                                            $datosReporte["empresaSubContratista"] = "";
                                            $datosReporte["rutEmpresaSubContratista"] = ""; 
                                            } 
                                            $datosReporte["proyecto"] = strtoupper($folio["sso_project"]); 
                                            $datosReporte["nombreTrabajador"] = "";
                                            $datosReporte["apellido1Trabajador"] = "";
                                            $datosReporte["apellido2Trabajador"] = "";
                                            $datosReporte["rutTrabajador"] = "";
                                            $datosReporte["cargoTrabajador"] =  "";
                                            $datosReporte["documentoTrabajador"] = strtoupper($nombreDoc[0]["doc_name"]);
                                            $datosReporte["estadoDocumento"] = $estadoDocumento;
                                            $datosReporte["fechaCarga"] = date("d-m-Y H:i:00",$value['upld_upddat']);
                                            if($value['upld_vence_date']>0){
                                                $datosReporte["fechaVence"] = date("d-m-Y H:i:00",$value['upld_vence_date']);  
                                            }else{
                                                $datosReporte["fechaVence"] = "";        
                                            }    
                                    }
                                
                                    if(!empty($datosReporte)){
                                     $listaDatosReporte[] =$datosReporte;
                                    }
                                    
                            }
                    }
                }
                /// rechazados ///
                if($tipoInforme == 4){
                    
                    $cantidadAprobados = 0;
                    $cantidadRechazados = 0;
                    $cantidadVencidos = 0;
                    $cantidadPorRevision = 0;
                    $estadoDocumento ="";
                    $datosTrabajadores = trabajadorSSO::where('worker_status','1')->where('sso_id',$folio["id"])->count();
                   
                    foreach ($documentos as $value) {
                            unset($datosReporte);
                            ////////////////// fecha para determinar si esta expirado /////
                            $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
                            $fecha2 = $value["upld_vence_date"];
                            $fechaUpdate = $value["upld_upddat"];
                            //////////////////////
                        
                            
                            if ($value["upld_rechazado"] == 1) 
                            {
                                
                                $estadoDocumento ="Rechazado";
                            
                                /// NOMBRE DOCUMENTOS
                                $datosReporte= array();
                                $nombreDoc = Documento::where('id',$value["upld_docid"])->get();
                             
                                    if ($value["upld_workerid"] > 0){
                                       
                                       // $trabajador = trabajadorSSO::where('worker_status','1')->where('id',$value["upld_workerid"])->where('sso_id',$value["upld_sso_id"])->get()->toArray();

                                        $trabajador = DB::table('xt_ssov2_header_worker')
                                        ->join('xt_ssov2_cargos', 'xt_ssov2_cargos.id', '=', 'xt_ssov2_header_worker.worker_cargoid')
                                        ->where(['xt_ssov2_header_worker.id' => $value["upld_workerid"]])
                                        ->where(['xt_ssov2_header_worker.worker_status' => 1 ])
                                        ->where(['xt_ssov2_header_worker.sso_id' => $value["upld_sso_id"]])
                                        ->get(['xt_ssov2_header_worker.worker_name1',
                                                'xt_ssov2_header_worker.worker_name2',
                                                'xt_ssov2_header_worker.worker_name3',
                                                'xt_ssov2_header_worker.worker_rut',
                                                'xt_ssov2_header_worker.worker_cargoid',
                                                'xt_ssov2_header_worker.worker_syscargoname',
                                                'xt_ssov2_cargos.cargo_name'])->toArray();
                                      

                                        if(!empty($trabajador[0]->worker_name1)) {

                                            $datosReporte["folio"] = $folio["id"];
                                            $datosReporte["empresaPrincipal"] = strtoupper($folio["sso_mcomp_name"]);
                                            $datosReporte["rutEmpresaPrincipal"] = strtoupper($folio["sso_mcomp_rut"]."-".$folio["sso_mcomp_dv"]);
                                            $datosReporte["empresaContratista"] = strtoupper($folio["sso_comp_name"]);
                                            $datosReporte["rutEmpresaContratista"] = strtoupper($folio["sso_comp_rut"]."-".$folio["sso_comp_dv"]);
                                            if($folio["sso_subcomp_active"] == 1){
                                            $datosReporte["empresaSubContratista"] = strtoupper($folio["sso_subcomp_name"]);
                                            $datosReporte["rutEmpresaSubContratista"] = strtoupper($folio["sso_subcomp_rut"]."-".$folio["sso_subcomp_dv"]);
                                            }else{
                                            $datosReporte["empresaSubContratista"] = "";
                                            $datosReporte["rutEmpresaSubContratista"] = "";   
                                            } 
                                            $datosReporte["proyecto"] = strtoupper($folio["sso_project"]);
                                            $datosReporte["nombreTrabajador"] = strtoupper($trabajador[0]->worker_name1);
                                            $datosReporte["apellido1Trabajador"] = strtoupper($trabajador[0]->worker_name2);
                                            $datosReporte["apellido2Trabajador"] = strtoupper($trabajador[0]->worker_name3);
                                            $datosReporte["rutTrabajador"] = strtoupper($trabajador[0]->worker_rut);
                                            if($trabajador[0]->worker_syscargoname == ""){
                                                $datosReporte["cargoTrabajador"] = strtoupper($trabajador[0]->cargo_name);
                                            }else{
                                                $datosReporte["cargoTrabajador"] = strtoupper($trabajador[0]->worker_syscargoname);
                                            }
                                            $datosReporte["documentoTrabajador"] = strtoupper($nombreDoc[0]->doc_name);
                                            $datosReporte["estadoDocumento"] = $estadoDocumento; 
                                            $datosReporte["fechaCarga"] = date("d-m-Y H:i:00",$value['upld_upddat']);
                                            if($value['upld_vence_date']>0){
                                                $datosReporte["fechaVence"] = date("d-m-Y H:i:00",$value['upld_vence_date']);  
                                            }else{
                                                $datosReporte["fechaVence"] = "";        
                                            }    
                                        }
                                    }
                                    if($value["upld_workerid"] == 0){
                                              
                                            $datosReporte["folio"] = $folio["id"];
                                            $datosReporte["empresaPrincipal"] = strtoupper($folio["sso_mcomp_name"]);
                                            $datosReporte["rutEmpresaPrincipal"] = strtoupper($folio["sso_mcomp_rut"]."-".$folio["sso_mcomp_dv"]);
                                            $datosReporte["empresaContratista"] = strtoupper($folio["sso_comp_name"]);
                                            $datosReporte["rutEmpresaContratista"] = strtoupper($folio["sso_comp_rut"]."-".$folio["sso_comp_dv"]);
                                            if($folio["sso_subcomp_active"] == 1){
                                            $datosReporte["empresaSubContratista"] = strtoupper($folio["sso_subcomp_name"]);
                                            $datosReporte["rutEmpresaSubContratista"] = strtoupper($folio["sso_subcomp_rut"]."-".$folio["sso_subcomp_dv"]);
                                            }else{
                                            $datosReporte["empresaSubContratista"] = "";
                                            $datosReporte["rutEmpresaSubContratista"] = ""; 
                                            }  
                                            $datosReporte["proyecto"] = strtoupper($folio["sso_project"]);
                                            $datosReporte["nombreTrabajador"] = "";
                                            $datosReporte["apellido1Trabajador"] = "";
                                            $datosReporte["apellido2Trabajador"] = "";
                                            $datosReporte["rutTrabajador"] = "";
                                            $datosReporte["cargoTrabajador"] =  "";
                                            $datosReporte["documentoTrabajador"] = strtoupper($nombreDoc[0]["doc_name"]);
                                            $datosReporte["estadoDocumento"] = $estadoDocumento;
                                            $datosReporte["fechaCarga"] = date("d-m-Y H:i:00",$value['upld_upddat']);
                                            if($value['upld_vence_date']>0){
                                                $datosReporte["fechaVence"] = date("d-m-Y H:i:00",$value['upld_vence_date']);  
                                            }else{
                                                $datosReporte["fechaVence"] = "";        
                                            }    
                                    }

                                    if(!empty($datosReporte)){
                                     $listaDatosReporte[] =$datosReporte;
                                    }
                            }

                            
                    }
                }
                // sin documentos //
                if($tipoInforme == 1){
                   
                    $cantidadAprobados = 0;
                    $cantidadRechazados = 0;
                    $cantidadVencidos = 0;
                    $cantidadPorRevision = 0;
                    $cantidadSinDoc = 0;
                    
                    $documentosV = EstadoDocumento::where('upld_sso_id',$folio["id"])->where('upld_status', '1')->whereBetween('upld_upddat', [$fechasDesde,$fechasHasta])->orderBy('upld_workerid', 'ASC')->get(['id','upld_workerid'])->toArray();
                      
                        if(empty($documentosV)){
                               // echo $folio["id"]."<br>";
                                //$datosTrabajadores = trabajadorSSO::where('worker_status','1')->where('sso_id',$folio["id"])->get(['worker_name1','worker_name2','worker_name3','worker_rut','worker_syscargoname'])->toArray();
                                 $trabajador = DB::table('xt_ssov2_header_worker')
                                ->join('xt_ssov2_cargos', 'xt_ssov2_cargos.id', '=', 'xt_ssov2_header_worker.worker_cargoid')
                                ->where(['xt_ssov2_header_worker.id' => $value["upld_workerid"]])
                                ->where(['xt_ssov2_header_worker.worker_status' => 1 ])
                                ->where(['xt_ssov2_header_worker.sso_id' => $value["upld_sso_id"]])
                                ->get(['xt_ssov2_header_worker.worker_name1',
                                        'xt_ssov2_header_worker.worker_name2',
                                        'xt_ssov2_header_worker.worker_name3',
                                        'xt_ssov2_header_worker.worker_rut',
                                        'xt_ssov2_header_worker.worker_cargoid',
                                        'xt_ssov2_header_worker.worker_syscargoname',
                                        'xt_ssov2_cargos.cargo_name'])->toArray();
                                
                                if(!empty($datosTrabajadores)){
                                    foreach ($datosTrabajadores as $trabajador) {
                                    
                                        
                                        $datosReporte["folio"] = $folio["id"];
                                        $datosReporte["empresaPrincipal"] = strtoupper($folio["sso_mcomp_name"]);
                                        $datosReporte["rutEmpresaPrincipal"] = strtoupper($folio["sso_mcomp_rut"]."-".$folio["sso_mcomp_dv"]);
                                        $datosReporte["empresaContratista"] = strtoupper($folio["sso_comp_name"]);
                                        $datosReporte["rutEmpresaContratista"] = strtoupper($folio["sso_comp_rut"]."-".$folio["sso_comp_dv"]);
                                        if($folio["sso_subcomp_active"] == 1){
                                        $datosReporte["empresaSubContratista"] = strtoupper($folio["sso_subcomp_name"]);
                                        $datosReporte["rutEmpresaSubContratista"] = strtoupper($folio["sso_subcomp_rut"]."-".$folio["sso_subcomp_dv"]);
                                        }else{
                                        $datosReporte["empresaSubContratista"] = "";
                                        $datosReporte["rutEmpresaSubContratista"] = "";   
                                        } 
                                        $datosReporte["proyecto"] = strtoupper($folio["sso_project"]);
                                        $datosReporte["nombreTrabajador"] = strtoupper($trabajador->worker_name1);
                                        $datosReporte["apellido1Trabajador"] = strtoupper($trabajador->worker_name2);
                                        $datosReporte["apellido2Trabajador"] = strtoupper($trabajador->worker_name3);
                                        $datosReporte["rutTrabajador"] = strtoupper($trabajador->worker_rut);
                                        if($trabajador[0]->worker_syscargoname == ""){
                                            $datosReporte["cargoTrabajador"] = strtoupper($trabajador[0]->cargo_name);
                                        }else{
                                            $datosReporte["cargoTrabajador"] = strtoupper($trabajador[0]->worker_syscargoname);
                                        }
                                    
                                        $datosReporte["documentoTrabajador"] ="";
                                        $datosReporte["estadoDocumento"] = "N/A"; 
                                        $datosReporte["fechaCarga"] = "";
                                        $datosReporte["fechaVence"] = "";
                                    }
                                }else{
                                        $datosReporte["folio"] = $folio["id"];
                                        $datosReporte["empresaPrincipal"] = strtoupper($folio["sso_mcomp_name"]);
                                        $datosReporte["rutEmpresaPrincipal"] = strtoupper($folio["sso_mcomp_rut"]."-".$folio["sso_mcomp_dv"]);
                                        $datosReporte["empresaContratista"] = strtoupper($folio["sso_comp_name"]);
                                        $datosReporte["rutEmpresaContratista"] = strtoupper($folio["sso_comp_rut"]."-".$folio["sso_comp_dv"]);
                                        if($folio["sso_subcomp_active"] == 1){
                                        $datosReporte["empresaSubContratista"] = strtoupper($folio["sso_subcomp_name"]);
                                        $datosReporte["rutEmpresaSubContratista"] = strtoupper($folio["sso_subcomp_rut"]."-".$folio["sso_subcomp_dv"]);
                                        }else{
                                        $datosReporte["empresaSubContratista"] = "";
                                        $datosReporte["rutEmpresaSubContratista"] = "";   
                                        } 
                                        $datosReporte["proyecto"] = strtoupper($folio["sso_project"]);
                                        $datosReporte["nombreTrabajador"] = "";
                                        $datosReporte["apellido1Trabajador"] = "";
                                        $datosReporte["apellido2Trabajador"] = "";
                                        $datosReporte["rutTrabajador"] = "";
                                        $datosReporte["cargoTrabajador"] = "";
                                        $datosReporte["documentoTrabajador"] ="";
                                        $datosReporte["estadoDocumento"] = "N/A"; 
                                        $datosReporte["fechaCarga"] = "";
                                        $datosReporte["fechaVence"] = "";
                                        

                                }
                               
                                if(!empty($datosReporte)){
                                    $listaDatosReporte[] =$datosReporte;
                                }
                            
                        }
                    
                }
                if($tipoInforme == 6){
                      
                    foreach ($documentos as $value) {
                        unset($datosReporte);
                      
                        if($value['upld_ispayed'] == 0 and $value['upld_venced'] == 0 and $value['upld_rechazado'] == 0 and $value['upld_docaprob'] == 0 and $value['upld_aprobComen'] == 0 and $value['upld_status'] == 1){

                            $estadoDocumento = "Por Revisión";
                            $nombreDoc = Documento::where('id',$value["upld_docid"])->get();
                     
                            if ($value["upld_workerid"] > 0){
                               
                                //$trabajador = trabajadorSSO::where('worker_status','1')->where('id',$value["upld_workerid"])->where('sso_id',$value["upld_sso_id"])->get()->toArray();
                              

                                $trabajador = DB::table('xt_ssov2_header_worker')
                                ->join('xt_ssov2_cargos', 'xt_ssov2_cargos.id', '=', 'xt_ssov2_header_worker.worker_cargoid')
                                ->where(['xt_ssov2_header_worker.id' => $value["upld_workerid"]])
                                ->where(['xt_ssov2_header_worker.worker_status' => 1 ])
                                ->where(['xt_ssov2_header_worker.sso_id' => $value["upld_sso_id"]])
                                ->get(['xt_ssov2_header_worker.worker_name1',
                                        'xt_ssov2_header_worker.worker_name2',
                                        'xt_ssov2_header_worker.worker_name3',
                                        'xt_ssov2_header_worker.worker_rut',
                                        'xt_ssov2_header_worker.worker_cargoid',
                                        'xt_ssov2_header_worker.worker_syscargoname',
                                        'xt_ssov2_cargos.cargo_name'])->toArray();
                                
                                if(!empty($trabajador[0]->worker_name1)){

                                    $datosReporte["folio"] = $folio["id"];
                                    $datosReporte["empresaPrincipal"] = strtoupper($folio["sso_mcomp_name"]);
                                    $datosReporte["rutEmpresaPrincipal"] = strtoupper($folio["sso_mcomp_rut"]."-".$folio["sso_mcomp_dv"]);
                                    $datosReporte["empresaContratista"] = strtoupper($folio["sso_comp_name"]);
                                    $datosReporte["rutEmpresaContratista"] = strtoupper($folio["sso_comp_rut"]."-".$folio["sso_comp_dv"]);
                                    if($folio["sso_subcomp_active"] == 1){
                                    $datosReporte["empresaSubContratista"] = strtoupper($folio["sso_subcomp_name"]);
                                    $datosReporte["rutEmpresaSubContratista"] = strtoupper($folio["sso_subcomp_rut"]."-".$folio["sso_subcomp_dv"]);
                                    }else{
                                    $datosReporte["empresaSubContratista"] = "";
                                    $datosReporte["rutEmpresaSubContratista"] = "";   
                                    } 
                                    $datosReporte["proyecto"] = strtoupper($folio["sso_project"]);
                                    $datosReporte["nombreTrabajador"] = strtoupper($trabajador[0]->worker_name1);
                                    $datosReporte["apellido1Trabajador"] = strtoupper($trabajador[0]->worker_name2);
                                    $datosReporte["apellido2Trabajador"] = strtoupper($trabajador[0]->worker_name3);
                                    $datosReporte["rutTrabajador"] = strtoupper($trabajador[0]->worker_rut);
                                    if($trabajador[0]->worker_syscargoname == ""){
                                        $datosReporte["cargoTrabajador"] = strtoupper($trabajador[0]->cargo_name);
                                    }else{
                                        $datosReporte["cargoTrabajador"] = strtoupper($trabajador[0]->worker_syscargoname);
                                    }
                                    $datosReporte["documentoTrabajador"] = strtoupper($nombreDoc[0]->doc_name);
                                    $datosReporte["estadoDocumento"] = $estadoDocumento; 
                                    $datosReporte["fechaCarga"] = date("d-m-Y H:i:00",$value['upld_upddat']);
                                    if($value['upld_vence_date']>0){
                                                $datosReporte["fechaVence"] = date("d-m-Y H:i:00",$value['upld_vence_date']);  
                                            }else{
                                                $datosReporte["fechaVence"] = "";        
                                            }    
                                }
                            }
                            if($value["upld_workerid"] == 0){
                                      
                                    $datosReporte["folio"] = $folio["id"];
                                    $datosReporte["empresaPrincipal"] = strtoupper($folio["sso_mcomp_name"]);
                                    $datosReporte["rutEmpresaPrincipal"] = strtoupper($folio["sso_mcomp_rut"]."-".$folio["sso_mcomp_dv"]);
                                    $datosReporte["empresaContratista"] = strtoupper($folio["sso_comp_name"]);
                                    $datosReporte["rutEmpresaContratista"] = strtoupper($folio["sso_comp_rut"]."-".$folio["sso_comp_dv"]);
                                    if($folio["sso_subcomp_active"] == 1){
                                    $datosReporte["empresaSubContratista"] = strtoupper($folio["sso_subcomp_name"]);
                                    $datosReporte["rutEmpresaSubContratista"] = strtoupper($folio["sso_subcomp_rut"]."-".$folio["sso_subcomp_dv"]);
                                    }else{
                                    $datosReporte["empresaSubContratista"] = "";
                                    $datosReporte["rutEmpresaSubContratista"] = ""; 
                                    }  
                                    $datosReporte["proyecto"] = strtoupper($folio["sso_project"]);
                                    $datosReporte["nombreTrabajador"] = "";
                                    $datosReporte["apellido1Trabajador"] = "";
                                    $datosReporte["apellido2Trabajador"] = "";
                                    $datosReporte["rutTrabajador"] = "";
                                    $datosReporte["cargoTrabajador"] =  "";
                                    $datosReporte["documentoTrabajador"] = strtoupper($nombreDoc[0]["doc_name"]);
                                    $datosReporte["estadoDocumento"] = $estadoDocumento;
                                    $datosReporte["fechaCarga"] = date("d-m-Y H:i:00",$value['upld_upddat']);
                                    if($value['upld_vence_date']>0){
                                                $datosReporte["fechaVence"] = date("d-m-Y H:i:00",$value['upld_vence_date']);  
                                            }else{
                                                $datosReporte["fechaVence"] = "";        
                                            }    
                            }

                            if(!empty($datosReporte)){
                             $listaDatosReporte[] =$datosReporte;
                            }
                        }   
                    }
                }

                if($tipoInforme != 1){
                    if(!empty($listaDatosReporte)){
                        $totalDocuementosEmpresa = count($listaDatosReporte); 
                    }else{
                        $totalDocuementosEmpresa= 0;
                    }
                   
                }
                $totalTB = $totalTrabajadoresEmpresa;
                $totalDoc = $totalDocuementosEmpresa;  
            }
       


            if(!empty($listaDatosReporte)){
                $activaLista=1;
                $listaCuerpo ='<table id="datosTabla" class="table table-bordered table-striped display">
                <thead>
                <tr>
                  <th>Folio</th>
                  <th>Empresa Principal</th>
                  <th>RUT</th>
                  <th>Empresa Contratista</th>
                  <th>RUT</th>
                  <th>Empresa Sub Contratista</th>
                  <th>RUT</th>
                  <th>Proyecto</th>
                  <th>Nombre</th>
                  <th>Apellido</th>
                  <th>Apellido</th>
                  <th>RUT</th>
                  <th>Cargo</th>
                  <th>Documento</th>
                  <th>Estado</th>
                  <th>Fecha Registro</th>
                  <th>Fecha Vence</th>
                </tr>
                </thead>
                 <tbody>';

                foreach($listaDatosReporte as $datos){

                    $listaCuerpo.= "<tr>";
                    $listaCuerpo.= "<td>".$datos["folio"]."</td>";
                    $listaCuerpo.= "<td>".$datos["empresaPrincipal"]."</td>";
                    $listaCuerpo.= "<td>".$datos["rutEmpresaPrincipal"]."</td>";
                    $listaCuerpo.= "<td>".$datos["empresaContratista"]."</td>";
                    $listaCuerpo.= "<td>".$datos["rutEmpresaContratista"]."</td>";
                    $listaCuerpo.= "<td>".$datos["empresaSubContratista"]."</td>";
                    $listaCuerpo.= "<td>".$datos["rutEmpresaSubContratista"]."</td>";
                    $listaCuerpo.= "<td>".$datos["proyecto"]."</td>";
                    $listaCuerpo.= "<td>".$datos["nombreTrabajador"]."</td>";
                    $listaCuerpo.= "<td>".$datos["apellido1Trabajador"]."</td>";
                    $listaCuerpo.= "<td>".$datos["apellido2Trabajador"]."</td>";
                    $listaCuerpo.= "<td>".$datos["rutTrabajador"]."</td>";
                    $listaCuerpo.= "<td>".$datos["cargoTrabajador"]."</td>";
                    $listaCuerpo.= "<td>".$datos["documentoTrabajador"]."</td>";
                    $listaCuerpo.= "<td>".strtoupper($datos["estadoDocumento"])."</td>";
                    $listaCuerpo.= "<td>".$datos["fechaCarga"]."</td>";
                    $listaCuerpo.= "<td>".$datos["fechaVence"]."</td>";
                    $listaCuerpo.= "</tr>";

                    switch ($datos["estadoDocumento"]) {
                     case 'Por Revisión':
                           $cantidadPorRevision +=1;
                          break;
                     case 'Aprobado':
                          $cantidadAprobados +=1; 
                          break;
                     case 'Vencido':
                          $cantidadVencidos +=1; ;
                          break;
                     case 'Aprobado Obs':
                          $cantidadAprobadoObse +=1;
                          break;
                     case 'Rechazado':
                          $cantidadRechazados +=1; ;
                          break;
                     case 'N/A':
                          $cantidadSinDoc +=1; ;
                          break;
                     }
                }
            }else{
                $listaCuerpo = 0;
                $activaLista = 0;
            }

            $totalDocRechazados = $totalDocRechazados + $cantidadRechazados;
            $totalDocAprobados = $cantidadAprobados + $totalDocAprobados; 
            $totalDocVencidos = $totalDocVencidos + $cantidadVencidos;
            $totalDocRevision = $totalDocRevision + $cantidadPorRevision;
            $totalDocAprobadosObs = $totalDocAprobadosObs + $cantidadAprobadoObse;
        }else{
            $listaCuerpo = 0;
            $activaLista = 0;
        }    
            return view('documentoReporte.index',compact('listaCuerpo','totalDoc','totalDocRechazados','totalDocAprobados','totalDocVencidos','totalDocRevision','totalTB','totalDocAprobadosObs','EmpresasP','empresasPrincipales','datosUsuarios','certificacion','usuarioAqua','activaLista','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile')); 
      
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
