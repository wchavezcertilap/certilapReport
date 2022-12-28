<?php

namespace App\Http\Controllers;
use Excel;
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
use App\DocConfigGlobal;
use App\trabajadorFactura;
use App\PagosSso;
use App\TipoPagoSso;
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
        
        return FolioSso::distinct()->where('sso_mcomp_rut','=',$id)->where('sso_status','=',1)->orderBy('id', 'ASC')->get(['id']);
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
        $todosFolio = 0;
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
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);

                }elseif ($cantidadCon > 0 and $folio!="" and $proyecto=="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status', '1')->whereIn('sso_comp_rut',$rutcontratistasR)
                    ->where('id', $input["folio"])
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);
                }elseif ($cantidadCon > 0 and $folio=="" and $proyecto=="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status', '1')->whereIn('sso_comp_rut',$rutcontratistasR)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);
                }elseif ($cantidadCon == 0 and $folio!="" and $proyecto!="") {

                    $folios = FolioSso::where('id',$folio)->where('sso_project', $proyecto)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);
                }elseif ($cantidadCon == 0 and $folio!="" and $proyecto=="") {

                    $folios = FolioSso::where('id',$folio)->where('sso_status', '1')
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);
                }elseif ($cantidadCon == 0 and $folio=="" and $proyecto!="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status', '1')->where('sso_project', $proyecto)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);
                }
                else{
                    $folios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status', '1')
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);
                }

            }else{

                if($cantidadCon > 0 and $folio!="" and $proyecto!=""){

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('sso_status', '1')->whereIn('sso_comp_rut',$rutcontratistasR)
                    ->where('id', $folio)->where('id', $proyecto)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);

                }elseif ($cantidadCon > 0 and $folio!="" and $proyecto=="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('sso_status', '1')->whereIn('sso_comp_rut',$rutcontratistasR)
                    ->where('id', $input["folio"])
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);
                }elseif ($cantidadCon > 0 and $folio=="" and $proyecto=="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('sso_status', '1')->whereIn('sso_comp_rut',$rutcontratistasR)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);
                }elseif ($cantidadCon == 0 and $folio!="" and $proyecto!="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('id',$folio)->where('sso_project', $proyecto)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);
                }elseif ($cantidadCon == 0 and $folio!="" and $proyecto=="") {

                    $folios = FolioSso::where('id',$folio)->where('sso_status', '1')
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);
            
                }elseif ($cantidadCon == 0 and $folio=="" and $proyecto!="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('sso_project',$proyecto)->where('sso_status', '1')
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);
            
                }elseif ($cantidadCon == 0 and $folio=="" and $proyecto="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('sso_status', '1')->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);
                }else{
                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('sso_status', '1')->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);
                }
            }
        }
        if($datosUsuarios->type == 2 or $datosUsuarios->type ==1){

            if($empresasPrincipales[0] == 1){

                if($cantidadCon > 0 and $folio!="" and $proyecto!=""){

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status', '1')->whereIn('sso_comp_rut',$rutcontratistasR)
                    ->where('id', $folio)->where('sso_project', $proyecto)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);

                }elseif ($cantidadCon > 0 and $folio!="" and $proyecto=="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status', '1')->whereIn('sso_comp_rut',$rutcontratistasR)
                    ->where('id', $input["folio"])
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);
                }elseif ($cantidadCon > 0 and $folio=="" and $proyecto=="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status', '1')->whereIn('sso_comp_rut',$rutcontratistasR)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);
                }elseif ($cantidadCon == 0 and $folio!="" and $proyecto!="") {

                    $folios = FolioSso::where('id',$folio)->where('sso_project', $proyecto)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);
                }elseif ($cantidadCon == 0 and $folio!="" and $proyecto=="") {

                    $folios = FolioSso::where('id',$folio)->where('sso_status', '1')
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);
                }elseif ($cantidadCon == 0 and $folio=="" and $proyecto!="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status', '1')->where('sso_project', $proyecto)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);
                }
                else{
                    $folios = FolioSso::where('sso_status', '1')
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);
                    $todosFolio = 1;
                }

            }else{

                if($cantidadCon > 0 and $folio!="" and $proyecto!=""){

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('sso_status', '1')->whereIn('sso_comp_rut',$rutcontratistasR)
                    ->where('id', $folio)->where('id', $proyecto)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);

                }elseif ($cantidadCon > 0 and $folio!="" and $proyecto=="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('sso_status', '1')->whereIn('sso_comp_rut',$rutcontratistasR)
                    ->where('id', $input["folio"])
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);
                }elseif ($cantidadCon > 0 and $folio=="" and $proyecto=="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('sso_status', '1')->whereIn('sso_comp_rut',$rutcontratistasR)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);
                }elseif ($cantidadCon == 0 and $folio!="" and $proyecto!="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('id',$folio)->where('sso_project', $proyecto)
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);
                }elseif ($cantidadCon == 0 and $folio!="" and $proyecto=="") {

                    $folios = FolioSso::where('id',$folio)->where('sso_status', '1')
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);
            
                }elseif ($cantidadCon == 0 and $folio=="" and $proyecto!="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('sso_project',$proyecto)->where('sso_status', '1')
                    ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);
            
                }elseif ($cantidadCon == 0 and $folio=="" and $proyecto="") {

                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('sso_status', '1')->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);
                }else{
                    $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('sso_status', '1')->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv','sso_project','sso_cycle_aprobdays','sso_cycle_cargadays','sso_cfgid']);
                }
            }

        }   
        $tipoInforme = $input["tipoInforme"];
        $fechaSeleccion = $input["fechaSeleccion"];



        if($tipoInforme == 8){

            $idFolios = FolioSso::where('sso_mcomp_rut',78921690)
            ->where('sso_project','LIKE',"%PERSONALIZADO-%")
            ->where('sso_status',1)
            ->orderBy('id', 'ASC')->orderBy('sso_project', 'ASC')->get(['id','sso_mcomp_rut','sso_mcomp_name','sso_mcomp_dv','sso_comp_rut','sso_comp_name','sso_comp_dv','sso_subcomp_active','sso_subcomp_rut','sso_subcomp_dv','sso_subcomp_name','sso_cfgid','sso_project','sso_crtdat','sso_sub_project','sso_service_cat','sso_worker_cat'])->toArray();


            $documentosGlobalesObligatoriosLabel = DB::table('xt_ssov2_configs_glbdocs')
                ->join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_configs_glbdocs.glb_docid')
                ->where(['xt_ssov2_configs_glbdocs.cfg_id' => 493])
                ->where(['xt_ssov2_doctypes.doc_status' => 1 ])
                ->where(['xt_ssov2_doctypes.doc_type' => 0])
                ->orderBy('xt_ssov2_configs_glbdocs.glb_obligact', 'DESC')
                ->select('xt_ssov2_doctypes.id','xt_ssov2_doctypes.doc_name','xt_ssov2_configs_glbdocs.glb_obligact')->get()->toArray();

            foreach ($idFolios as $folio) {

                $_DATA["STATS"]["DATA"][$folio["id"]]["folio"] = $folio["id"];
                $_DATA["STATS"]["DATA"][$folio["id"]]["PROYECTO"] = mb_strtoupper($folio["sso_project"], "UTF-8");
                $_DATA["STATS"]["DATA"][$folio["id"]]["COMPANY"]     = mb_strtoupper($folio["sso_comp_name"], "UTF-8");
                $_DATA["STATS"]["DATA"][$folio["id"]]["COMPANYRUT"]     = mb_strtoupper($folio["sso_comp_rut"].'-'.$folio["sso_comp_dv"], "UTF-8");
                if($folio["sso_subcomp_active"] == 1){
                    $_DATA["STATS"]["DATA"][$folio["id"]]["SUBCOMPANY"]     = mb_strtoupper($folio["sso_subcomp_name"], "UTF-8");
                    $_DATA["STATS"]["DATA"][$folio["id"]]["SUBCOMPANYRUT"]     = mb_strtoupper($folio["sso_subcomp_rut"].'-'.$folio["sso_subcomp_dv"], "UTF-8");

                }else{
                    $_DATA["STATS"]["DATA"][$folio["id"]]["SUBCOMPANY"]     = "";
                    $_DATA["STATS"]["DATA"][$folio["id"]]["SUBCOMPANYRUT"]     = ""; 
                }
                
       
                $documentosGlobalesSubidos = EstadoDocumento::where('upld_sso_id',$folio['id'])->where('upld_status', 1)->where('upld_type',0)
                ->where('upld_docaprob',1)
                ->orderBy('upld_docid', 'DESC')
                ->get(['id', 'upld_catid', 'upld_docid', 'upld_docaprob', 'upld_venced', 'upld_vence_date', 'upld_rechazado', 'upld_comments','upld_aprobComen','upld_upddat','upld_docaprob_uid'])->toArray();

                unset($_DOCSUPLOADES);
                foreach($documentosGlobalesSubidos AS $docsuploadedrow){
                    $_DOCSUPLOADES[$docsuploadedrow["upld_docid"]] = $docsuploadedrow;
                }      
                //// DOC GLOBLAES OBLIGATORIOS ////
                $documentosGlobalesObligatoriosF = DB::table('xt_ssov2_configs_glbdocs')
                ->join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_configs_glbdocs.glb_docid')
                ->where(['xt_ssov2_configs_glbdocs.cfg_id' => $folio['sso_cfgid']])
                ->where(['xt_ssov2_doctypes.doc_status' => 1 ])
                ->where(['xt_ssov2_doctypes.doc_type' => 0])
                ->orderBy('xt_ssov2_configs_glbdocs.glb_obligact', 'DESC')
                ->select('xt_ssov2_doctypes.id','xt_ssov2_doctypes.doc_name','xt_ssov2_configs_glbdocs.glb_obligact')->get()->toArray();

    
                $_NEW_GLOBAL_DOCS_TOTAL = count($documentosGlobalesObligatoriosF);
                $_NEW_GLOBAL_DOCS_APROB = 0;
                $_NEW_GLOBAL_DOCS_PERC  = 0;
                $R = 0;
                for($w = 0; $w < count($documentosGlobalesObligatoriosF) && $documentosGlobalesObligatoriosF != false; $w++)
                {
                    $grow    = $documentosGlobalesObligatoriosF[$w];
                    if(isset($_DOCSUPLOADES[$grow->id])){
                        $upldata = $_DOCSUPLOADES;
                    
                        if($grow->glb_obligact==1){
                            $R++;
                        }   

                        ////////////////// fecha para determinar si esta expirado /////
                        $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
                        $fecha2 = $upldata[$grow->id]["upld_vence_date"];
                        $fechaUpdate = $upldata[$grow->id]["upld_upddat"];
                        //////////////////////

            
                        
                        if (($upldata[$grow->id]["upld_aprobComen"] == 1 or $fecha_actual <= $fechaUpdate) and $fecha_actual < $fecha2){
                            $docstr = 100;
                            $_NEW_GLOBAL_DOCS_APROB++;  
                        }
                        elseif (($upldata[$grow->id]["upld_docaprob"] == 1 or $fecha_actual <= $fechaUpdate) and $fecha_actual < $fecha2){
                            $docstr = 100;
                            $_NEW_GLOBAL_DOCS_APROB++;  
                        }else{
                           $docstr = 0; 

                        }
                        
                   

                        $_DATA["STATS"]["GDOCS"][$folio["id"]][$grow->id]["NAME"] = mb_strtoupper(str_replace('.', '',$grow->doc_name), "UTF-8");
                        $_DATA["STATS"]["GDOCS"][$folio["id"]][$grow->id]["STAT"] = mb_strtoupper($docstr, "UTF-8");

                    }else{
                        $_DATA["STATS"]["GDOCS"][$folio["id"]][$grow->id]["NAME"] = mb_strtoupper(str_replace('.', '',$grow->doc_name), "UTF-8");
                        $_DATA["STATS"]["GDOCS"][$folio["id"]][$grow->id]["STAT"] = 0;
                    }

                }
            }


            $tablah='<table border="2">
                        <thead><tr>
                                <th style="background-color:#e3e3e3" colspan='.($_NEW_GLOBAL_DOCS_TOTAL + 7).'>Resumen de Estado</th>
                        </tr></thead>';
            $tablah.='<tr>
                          <td style="background-color:#76489b"><font color="#FFFFFF">N° FOLIO</font></td>
                            <td style="background-color:#76489b"><font color="#FFFFFF">PROYECTO</font></td>
                            <td style="background-color:#76489b"><font color="#FFFFFF">CONTRATISTA</font></td>
                            <td style="background-color:#76489b"><font color="#FFFFFF">RUT CONTRATISTA</font></td>
                            <td style="background-color:#76489b"><font color="#FFFFFF">SUB CONTRATISTA</font></td>
                            <td style="background-color:#76489b"><font color="#FFFFFF">RUT SUB CONTRATISTA</font></td>';

            foreach($documentosGlobalesObligatoriosLabel as $name)
            {
              
                $tablah.='<td style="background-color:#76489b"><font color="#FFFFFF">'.mb_strtoupper(str_replace('.', '',$name->doc_name)).'</font></td>'; 
            }
            $tablah.='<td style="background-color:#76489b"><font color="#FFFFFF">% TOTAL</font></td>';
            $tablah.='</tr>';
            $tabla='<tbody>';
            foreach (array_keys($_DATA["STATS"]["DATA"]) as $ssoid) {

                $datos = $_DATA["STATS"]["DATA"][$ssoid];
               
                $tabla.='<tr>';
                $tabla.='<td>'.$ssoid.'</td>';
                $tabla.='<td>'.$datos['PROYECTO'].'</td>';
                $tabla.='<td>'.$datos['COMPANY'].'</td>';
                $tabla.='<td>'.$datos['COMPANYRUT'].'</td>';
                $tabla.='<td>'.$datos['SUBCOMPANY'].'</td>';
                $tabla.='<td>'.$datos['SUBCOMPANYRUT'].'</td>';
                $_GLOBAL_DOCS_APROB = 0;
                foreach(array_keys( $_DATA["STATS"]["GDOCS"][$ssoid]) as $docid)
                {
                  
                    $docname = $_DATA["STATS"]["GDOCS"][$ssoid][$docid];
                    $tabla.='<td>'.mb_strtoupper($docname['STAT'], "UTF-8").'</td>'; 
                    if($docname['STAT'] == 100){
                        $_GLOBAL_DOCS_APROB++;  
                    }
                }
                $tabla.='<td>'.round((($_GLOBAL_DOCS_APROB*100)/$_NEW_GLOBAL_DOCS_TOTAL)).'</td>';
                $tabla.='</tr>';

                $TABLAVISTA[] = $tabla;

            }
            $tabla.='</tbody></table>';
            
            $tablav = $tablah.$tabla;

            Excel::create('Reporte SSO Personalizado', function($excel) use($tablav) {
                $excel->sheet('Datos Empresa', function($sheet) use($tablav) { 
                    $sheet->loadView('documentoReporte.excelTabla',compact('tablav'));
                });  
            
            })->export('xls');
              
        }///FIN8

        if($tipoInforme == 10){

            foreach ($folios as $folio) {

               
                $chkvencedate = mktime(23, 59, 59, date("m"), date("d"), date("Y")) + (30 * 86400);   
                $vencedate = mktime(23, 59, 59, date("m"), date("d"), date("Y"));
              
        
                $documentosporVencer = EstadoDocumento::join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_header_uploads.upld_docid')
                ->where('upld_sso_id',$folio['id'])->where('upld_status', 1)
                ->where('upld_docaprob',1)
                ->where('upld_rechazado',0)
                ->where('upld_vence_date','>',0)
                ->whereBetween('upld_vence_date', [$vencedate,$chkvencedate])
                ->orderBy('upld_vence_date', 'ASC')
                ->get(['xt_ssov2_header_uploads.id', 'upld_catid', 'upld_docid', 'upld_docaprob', 'upld_venced', 'upld_vence_date', 'upld_rechazado', 'upld_comments','upld_aprobComen','upld_upddat','upld_workerid','doc_name'])->toArray();
 
               
               

                if(isset($documentosporVencer[0]['id'])){

                    $_DATA["folio"] = $folio["id"];
                    $_DATA["PROYECTO"] = mb_strtoupper($folio["sso_project"], "UTF-8");
                    $_DATA["COMPANY"]     = mb_strtoupper($folio["sso_comp_name"], "UTF-8");
                    $_DATA["COMPANYRUT"]     = mb_strtoupper($folio["sso_comp_rut"].'-'.$folio["sso_comp_dv"], "UTF-8");
                    if($folio["sso_subcomp_active"] == 1){
                        $_DATA["SUBCOMPANY"]     = mb_strtoupper($folio["sso_subcomp_name"], "UTF-8");
                        $_DATA["SUBCOMPANYRUT"]     = mb_strtoupper($folio["sso_subcomp_rut"].'-'.$folio["sso_subcomp_dv"], "UTF-8");

                    }else{
                        $_DATA["SUBCOMPANY"]     = "";
                        $_DATA["SUBCOMPANYRUT"]     = ""; 
                    }
                
                    foreach ($documentosporVencer as $docVencer) {

                        $_DATA["documento"] = mb_strtoupper($docVencer['doc_name'], "UTF-8");
                        $_DATA["fechaVencimiento"] = $docVencer['upld_vence_date'];

                        if($docVencer['upld_workerid'] > 0){
                            $_DATA["tipoDoc"] = 'TRABAJADOR';
                             $trabajador = DB::table('xt_ssov2_header_worker')
                                    ->join('xt_ssov2_cargos', 'xt_ssov2_cargos.id', '=', 'xt_ssov2_header_worker.worker_cargoid')
                                    ->where(['xt_ssov2_header_worker.id' => $docVencer["upld_workerid"]])
                                    ->where(['xt_ssov2_header_worker.worker_status' => 1 ])
                                    ->get(['xt_ssov2_header_worker.worker_name1',
                                            'xt_ssov2_header_worker.worker_name2',
                                            'xt_ssov2_header_worker.worker_name3',
                                            'xt_ssov2_header_worker.worker_rut',
                                            'xt_ssov2_header_worker.worker_cargoid',
                                            'xt_ssov2_header_worker.worker_syscargoname',
                                            'xt_ssov2_cargos.cargo_name'])->toArray();
                
                            if(isset($trabajador[0]->worker_rut)){

                                $_DATA["nombreTrabajador"] = mb_strtoupper($trabajador[0]->worker_name1.' '.$trabajador[0]->worker_name2.' '.$trabajador[0]->worker_name3, "UTF-8");
                                $_DATA["rutTrabajador"] = $trabajador[0]->worker_rut;

                            } 
                        }else{
                            $_DATA["tipoDoc"] = 'GLOBAL';
                            $_DATA["nombreTrabajador"] = '';
                            $_DATA["rutTrabajador"] = '';
                        } 

                        
                    }
                     $data[] = $_DATA;
                   
                }

            }
           
            $tablah='<table border="2">
            <tr>
                <td style="background-color:#e3e3e3"><font color="#FFFFFF">N° FOLIO</font></td>
                <td style="background-color:#e3e3e3"><font color="#FFFFFF">PROYECTO</font></td>
                <td style="background-color:#e3e3e3"><font color="#FFFFFF">CONTRATISTA</font></td>
                <td style="background-color:#e3e3e3"><font color="#FFFFFF">RUT CONTRATISTA</font></td>
                <td style="background-color:#e3e3e3"><font color="#FFFFFF">SUB CONTRATISTA</font></td>
                <td style="background-color:#e3e3e3"><font color="#FFFFFF">RUT SUB CONTRATISTA</font></td>
                <td style="background-color:#e3e3e3"><font color="#FFFFFF">TIPO DOCUMENTO</font></td>
                <td style="background-color:#e3e3e3"><font color="#FFFFFF">DOCUMENTO</font></td>
                <td style="background-color:#e3e3e3"><font color="#FFFFFF">FECHA VENCIMIENTO</font></td>
                <td style="background-color:#e3e3e3"><font color="#FFFFFF">TRABAJADOR</font></td>
                <td style="background-color:#e3e3e3"><font color="#FFFFFF">RUT TRABAJADOR</font></td>
            </tr>';
            $tabla='<tbody>';
            foreach ($data as  $datos) {
                $tabla.='<tr>';
                $tabla.='<td>'.$datos['folio'].'</td>';
                $tabla.='<td>'.$datos['PROYECTO'].'</td>';
                $tabla.='<td>'.$datos['COMPANY'].'</td>';
                $tabla.='<td>'.$datos['COMPANYRUT'].'</td>';
                $tabla.='<td>'.$datos['SUBCOMPANY'].'</td>';
                $tabla.='<td>'.$datos['SUBCOMPANYRUT'].'</td>';
                $tabla.='<td>'.$datos['tipoDoc'].'</td>';
                $tabla.='<td>'.$datos['documento'].'</td>';
                $tabla.='<td>'.date("d-m-Y H:i:00",$datos['fechaVencimiento']).'</td>';
                $tabla.='<td>'.$datos['nombreTrabajador'].'</td>';
                $tabla.='<td>'.$datos['rutTrabajador'].'</td>';
                $tabla.='</tr>';
            }
            $tabla.='</tbody></table>';
            $tablav = $tablah.$tabla;
            Excel::create('SSO Documentos por Vencer', function($excel) use($tablav) {
                $excel->sheet('Datos Empresa', function($sheet) use($tablav) { 
                    $sheet->loadView('documentoReporte.excelTabla',compact('tablav'));
                });  
            
            })->export('xls');


            
              
        }
        
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
            if($todosFolio== 1){
                $datosTrabajadores = trabajadorSSO::where('worker_status','1')->count();
                $totalTrabajadoresEmpresa = number_format($datosTrabajadores);
            }else{
                 $datosTrabajadores = trabajadorSSO::where('worker_status','1')->whereIn('sso_id',$folioID)->count();
                $totalTrabajadoresEmpresa = number_format($datosTrabajadores);
            }
           
     
            $totalTB = 0;
            $totalDoc = 0;
            $totalDocRechazados = 0;
            $totalDocAprobados = 0;
            $totalDocVencidos = 0;
            $totalDocRevision = 0;
            $totalDocAprobadosObs = 0;

            foreach ($folios as  $folio) {
                

                if($tipoInforme != 1 and $tipoInforme != 6 and $tipoInforme != 9){
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
                                    $datosReporte["idDoc"] = $value["id"]; 
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
                                    $datosReporte["ciclo"] = $folio["sso_cycle_aprobdays"]."X".$folio["sso_cycle_cargadays"];
                                    $datosReporte['tipoPago'] = "";
                                    $datosReporte['fechaAprobacion'] = "";
                                    $datosReporte['fechaTransaccion'] = "";
                                    $datosReporte['idPago'] = "";    
                                    
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
                                $datosReporte["idDoc"] = $value["id"];         
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
                                $datosReporte["ciclo"] = $folio["sso_cycle_aprobdays"]."X".$folio["sso_cycle_cargadays"];
                                $datosReporte['tipoPago'] = "";
                                $datosReporte['fechaAprobacion'] = "";
                                $datosReporte['fechaTransaccion'] = "";
                                $datosReporte['idPago'] = "";    
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
                                            $datosReporte["idDoc"] = $value["id"]; 
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
                                            $datosReporte["ciclo"] = $folio["sso_cycle_aprobdays"]."X".$folio["sso_cycle_cargadays"];
                                            $datosReporte['tipoPago'] = "";
                                            $datosReporte['fechaAprobacion'] = "";
                                            $datosReporte['fechaTransaccion'] = "";
                                            $datosReporte['idPago'] = "";        
                                        }
                                    }
                                    if($value["upld_workerid"] == 0){
                                            $datosReporte["idDoc"] = $value["id"]; 
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
                                            $datosReporte["ciclo"] = $folio["sso_cycle_aprobdays"]."X".$folio["sso_cycle_cargadays"];  
                                            $datosReporte['tipoPago'] = "";
                                            $datosReporte['fechaAprobacion'] = "";
                                            $datosReporte['fechaTransaccion'] = "";
                                            $datosReporte['idPago'] = "";    
                                            
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
                                            $datosReporte["idDoc"] = $value["id"]; 
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
                                            $datosReporte["ciclo"] = $folio["sso_cycle_aprobdays"]."X".$folio["sso_cycle_cargadays"]; 
                                            $datosReporte['tipoPago'] = "";
                                            $datosReporte['fechaAprobacion'] = "";
                                            $datosReporte['fechaTransaccion'] = "";
                                            $datosReporte['idPago'] = "";       
                                        }
                                    }
                                    if($value["upld_workerid"] == 0){
                                            $datosReporte["idDoc"] = $value["id"];  
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
                                            $datosReporte["ciclo"] = $folio["sso_cycle_aprobdays"]."X".$folio["sso_cycle_cargadays"];   
                                            $datosReporte['tipoPago'] = "";
                                            $datosReporte['fechaAprobacion'] = "";
                                            $datosReporte['fechaTransaccion'] = "";
                                            $datosReporte['idPago'] = "";    
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
                                            $datosReporte["idDoc"] = $value["id"];     
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
                                            $datosReporte["ciclo"] = $folio["sso_cycle_aprobdays"]."X".$folio["sso_cycle_cargadays"];
                                            $datosReporte['tipoPago'] = "";
                                            $datosReporte['fechaAprobacion'] = "";
                                            $datosReporte['fechaTransaccion'] = "";
                                            $datosReporte['idPago'] = "";        
                                        }
                                    }
                                    if($value["upld_workerid"] == 0){
                                            $datosReporte["idDoc"] = $value["id"];   
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
                                            $datosReporte["ciclo"] = $folio["sso_cycle_aprobdays"]."X".$folio["sso_cycle_cargadays"]; 
                                            $datosReporte['tipoPago'] = "";
                                            $datosReporte['fechaAprobacion'] = "";
                                            $datosReporte['fechaTransaccion'] = "";
                                            $datosReporte['idPago'] = "";    

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
                                    
                                        $datosReporte["idDoc"] = $value["id"]; 
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
                                        $datosReporte["ciclo"] = $folio["sso_cycle_aprobdays"]."X".$folio["sso_cycle_cargadays"];
                                        $datosReporte['tipoPago'] = "";
                                        $datosReporte['fechaAprobacion'] = "";
                                        $datosReporte['fechaTransaccion'] = "";
                                        $datosReporte['idPago'] = "";    
                                    }
                                }else{
                                        $datosReporte["idDoc"] = $value["id"]; 
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
                                        $datosReporte["ciclo"] = $folio["sso_cycle_aprobdays"]."X".$folio["sso_cycle_cargadays"];
                                        $datosReporte['tipoPago'] = "";
                                        $datosReporte['fechaAprobacion'] = "";
                                        $datosReporte['fechaTransaccion'] = "";
                                        $datosReporte['idPago'] = "";    
                                        

                                }
                               
                                if(!empty($datosReporte)){
                                    $listaDatosReporte[] =$datosReporte;
                                }
                            
                        }
                    
                }
                if($tipoInforme == 6){
                    $EmpresasPago = TipoPagoSso::where('mrut',$folio->sso_mcomp_rut)->get(['fixed_paytype'])->toArray();
                    if(isset($EmpresasPago[0]['fixed_paytype'])){

                            $pagoDirecto = PagosSso::where('sso_id',$folio->id)->whereBetween('req_upddat', array($fechasDesde,  $fechasHasta))->where('req_payment_type','DEPWEB')
                            ->where('req_payment_type','DIRECT');
                          
                            $pagoWebpay = PagosSso::where('sso_id',$folio->id)->whereBetween('req_upddat', array($fechasDesde,  $fechasHasta))->where('req_payment_type','DEPWEB')
                            ->where('req_payment_subtype','WEBPAY')
                            ->where('req_status',2)
                            ->where('req_tbk_status',1);

                            $pagos = PagosSso::where('sso_id',$folio->id)->whereBetween('req_deposit_transdate', array($fechasDesde,  $fechasHasta))->where('req_payment_type','DEPWEB')
                            ->where('req_payment_subtype','DEPOSITO')
                            ->where('req_status',2)
                            ->where('req_deposit_file_approved',1)
                            ->unionAll($pagoDirecto,$pagoWebpay)
                            ->get()->toArray();
                     

                            if(!empty($pagos[0]['id'])){

                                foreach ($pagos as $pg){
                                    $trabajadorF = trabajadorFactura::where('invc_id',$pg['id'])->get()->toArray();
                                    
                                        foreach ($trabajadorF as $idtra){

                                            $trabajador = trabajadorSSO::distinct()
                                            ->join('xt_ssov2_cargos', 'xt_ssov2_cargos.id', '=', 'xt_ssov2_header_worker.worker_cargoid')
                                            ->join('xt_ssov2_configs_cargos', function ($join) use($folio) {
                                            $join->on('xt_ssov2_configs_cargos.cargo_id','=','xt_ssov2_cargos.id')
                                            ->where ('xt_ssov2_configs_cargos.cfg_id', '=', intval($folio->sso_cfgid));
                                            })
                                            ->join('xt_ssov2_configs_cargos_cats', 'xt_ssov2_configs_cargos_cats.cfg_id', '=', 'xt_ssov2_configs_cargos.cfg_id')
                                            ->join('xt_ssov2_doccats', 'xt_ssov2_doccats.id', '=', 'xt_ssov2_configs_cargos_cats.cat_id')
                                            ->join('xt_ssov2_configs_cargos_cats_docs', function ($join) {
                                            $join->on('xt_ssov2_configs_cargos_cats_docs.cfg_id', '=', 'xt_ssov2_configs_cargos.cfg_id')
                                            ->on('xt_ssov2_configs_cargos_cats_docs.cargo_id','=','xt_ssov2_configs_cargos_cats.cargo_id')
                                            ->on('xt_ssov2_configs_cargos_cats_docs.cat_id','=','xt_ssov2_configs_cargos_cats.cat_id');
                                            })
                                            ->join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_configs_cargos_cats_docs.doc_id')
                                            ->join('xt_ssov2_header_uploads', function ($join) {
                                            $join->on('xt_ssov2_header_uploads.upld_workerid', '=', 'xt_ssov2_header_worker.id')
                                            ->on('xt_ssov2_header_uploads.upld_sso_id','=','xt_ssov2_header_worker.sso_id')
                                            ->on('xt_ssov2_header_uploads.upld_docid','=','xt_ssov2_doctypes.id');
                                            })
                                            ->where('xt_ssov2_header_worker.sso_id', intval($folio->id))
                                            ->where('xt_ssov2_header_worker.id', intval($idtra['worker_id']))
                                            ->where('xt_ssov2_header_worker.worker_status', 1 )
                                            ->where('xt_ssov2_doccats.cat_status', 1)
                                            ->where('xt_ssov2_doctypes.doc_status', 1)
                                            ->where('xt_ssov2_doctypes.doc_type', 1)
                                            ->where('xt_ssov2_header_uploads.upld_status', 1)
                                            ->where('xt_ssov2_header_uploads.upld_venced', 0)
                                            ->where('xt_ssov2_header_uploads.upld_rechazado', 0)
                                            ->where('xt_ssov2_header_uploads.upld_docaprob', 0)
                                            ->whereBetween('xt_ssov2_header_uploads.upld_upddat', [$fechasDesde,$fechasHasta])
                                            ->get([
                                                'xt_ssov2_header_worker.worker_name',
                                                'xt_ssov2_header_worker.worker_name1',
                                                'xt_ssov2_header_worker.worker_name2',
                                                'xt_ssov2_header_worker.worker_name3',
                                                'xt_ssov2_header_worker.worker_name',
                                                'xt_ssov2_header_worker.worker_rut',
                                                'xt_ssov2_header_worker.worker_cargoid',
                                                'xt_ssov2_header_worker.worker_syscargoname',
                                                'xt_ssov2_cargos.cargo_name',
                                                'xt_ssov2_doctypes.doc_name',
                                                'xt_ssov2_header_uploads.upld_venced',
                                                'xt_ssov2_header_uploads.upld_vence_date',
                                                'xt_ssov2_header_uploads.upld_docaprob',
                                                'xt_ssov2_header_uploads.upld_docaprob_date',
                                                'xt_ssov2_header_uploads.id',
                                                'xt_ssov2_header_uploads.upld_workerid',
                                                'xt_ssov2_header_uploads.upld_crtdat',
                                                'xt_ssov2_header_uploads.upld_comments',
                                                'xt_ssov2_header_uploads.upld_docid',
                                                'xt_ssov2_header_uploads.upld_rechazado',
                                                'xt_ssov2_header_uploads.upld_upddat',
                                                'xt_ssov2_header_uploads.upld_sso_id'])->toArray();

                                          

                                            if(!empty($trabajador[0]['id'])){
                                                foreach ($trabajador as $value) {
                                                
                                                    $estadoDocumento = "Por Revisión";
                                    
                                                    $datosReporte["idDoc"] = $value["id"];
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
                                                    $datosReporte["nombreTrabajador"] = strtoupper($value['worker_name1']);
                                                    $datosReporte["apellido1Trabajador"] = strtoupper($value['worker_name2']);
                                                    $datosReporte["apellido2Trabajador"] = strtoupper($value['worker_name3']);
                                                    $datosReporte["rutTrabajador"] = strtoupper($value['worker_rut']);
                                                    if($value['worker_syscargoname'] == ""){
                                                        $datosReporte["cargoTrabajador"] = strtoupper($value['cargo_name']);
                                                    }else{
                                                        $datosReporte["cargoTrabajador"] = strtoupper($value['worker_syscargoname']);
                                                    }
                                                    $datosReporte["documentoTrabajador"] = strtoupper($value['doc_name']);
                                                    $datosReporte["estadoDocumento"] = $estadoDocumento; 
                                                    $datosReporte["fechaCarga"] = date("d-m-Y H:i:00",$value['upld_crtdat']);
                                                    if($value['upld_vence_date']>0){
                                                        $datosReporte["fechaVence"] = date("d-m-Y H:i:00",$value['upld_vence_date']);  
                                                    }else{
                                                        $datosReporte["fechaVence"] = "";        
                                                    }
                                                    $datosReporte["ciclo"] = $folio["sso_cycle_aprobdays"]."X".$folio["sso_cycle_cargadays"];
                                                    if($pg['req_payment_type']=='DIRECT'){
                                                        
                                                        $datosReporte['tipoPago'] ='Pago Directo';
                                                    }else{
                                                        $datosReporte['tipoPago'] = ucwords(mb_strtolower($pg['req_payment_subtype'],'UTF-8'));
                                                    }
                                                    
                                                    if($pg['req_payment_subtype']=='DEPOSITO'){
                                                        $datosReporte['fechaAprobacion'] = date('d/m/Y',$pg['req_deposit_transdate']);
                                                    }else{
                                                        $datosReporte['fechaAprobacion'] = date('d/m/Y',$pg['req_upddat']);    
                                                    }
                                                    
                                                    $datosReporte['fechaTransaccion'] = date('d/m/Y',$pg['req_crtdat']);
                                                    $datosReporte['idPago'] = $pg['id'];
                                                    if(isset($datosReporte)){
                                                        $listaDatosReporteTr[] = $datosReporte;
                                                    }   
                                                }
                                                
                                                 
                                            }
                                                
                                                 
                                        }

                                }
                               
                            }
                        
                    }else{

                        $trabajador = trabajadorSSO::distinct()
                        ->join('xt_ssov2_cargos', 'xt_ssov2_cargos.id', '=', 'xt_ssov2_header_worker.worker_cargoid')
                        ->join('xt_ssov2_configs_cargos', function ($join) use($folio) {
                            $join->on('xt_ssov2_configs_cargos.cargo_id','=','xt_ssov2_cargos.id')
                            ->where ('xt_ssov2_configs_cargos.cfg_id', '=', intval($folio->sso_cfgid));
                        })
                        ->join('xt_ssov2_configs_cargos_cats', 'xt_ssov2_configs_cargos_cats.cfg_id', '=', 'xt_ssov2_configs_cargos.cfg_id')
                        ->join('xt_ssov2_doccats', 'xt_ssov2_doccats.id', '=', 'xt_ssov2_configs_cargos_cats.cat_id')
                        ->join('xt_ssov2_configs_cargos_cats_docs', function ($join) {
                        $join->on('xt_ssov2_configs_cargos_cats_docs.cfg_id', '=', 'xt_ssov2_configs_cargos.cfg_id')
                            ->on('xt_ssov2_configs_cargos_cats_docs.cargo_id','=','xt_ssov2_configs_cargos_cats.cargo_id')
                             ->on('xt_ssov2_configs_cargos_cats_docs.cat_id','=','xt_ssov2_configs_cargos_cats.cat_id');
                        })
                        ->join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_configs_cargos_cats_docs.doc_id')
                        ->join('xt_ssov2_header_uploads', function ($join) {
                            $join->on('xt_ssov2_header_uploads.upld_workerid', '=', 'xt_ssov2_header_worker.id')
                            ->on('xt_ssov2_header_uploads.upld_sso_id','=','xt_ssov2_header_worker.sso_id')
                            ->on('xt_ssov2_header_uploads.upld_docid','=','xt_ssov2_doctypes.id');
                        })
                        ->where('xt_ssov2_header_worker.sso_id', intval($folio->id))
                        ->where('xt_ssov2_header_worker.worker_status', 1 )
                        ->where('xt_ssov2_doccats.cat_status', 1)
                        ->where('xt_ssov2_doctypes.doc_status', 1)
                        ->where('xt_ssov2_doctypes.doc_type', 1)
                        ->where('xt_ssov2_header_uploads.upld_status', 1)
                        ->where('xt_ssov2_header_uploads.upld_venced', 0)
                        ->where('xt_ssov2_header_uploads.upld_rechazado', 0)
                        ->where('xt_ssov2_header_uploads.upld_docaprob', 0)
                        ->whereBetween('xt_ssov2_header_uploads.upld_upddat', [$fechasDesde,$fechasHasta])
                        ->get(['xt_ssov2_header_uploads.upld_workerid',
                                'xt_ssov2_header_worker.worker_name1',
                                'xt_ssov2_header_worker.worker_name2',
                                'xt_ssov2_header_worker.worker_name3',
                                'xt_ssov2_header_worker.worker_rut',
                                'xt_ssov2_header_worker.worker_cargoid',
                                'xt_ssov2_header_worker.worker_syscargoname',
                                'xt_ssov2_cargos.cargo_name',
                                'xt_ssov2_doctypes.doc_name',
                                'xt_ssov2_header_uploads.upld_venced',
                                'xt_ssov2_header_uploads.upld_vence_date',
                                'xt_ssov2_header_uploads.upld_docaprob',
                                'xt_ssov2_header_uploads.upld_docaprob_date',
                                'xt_ssov2_header_uploads.id',
                                'xt_ssov2_header_uploads.upld_workerid',
                                'xt_ssov2_header_uploads.upld_crtdat',
                                'xt_ssov2_header_uploads.upld_comments',
                                'xt_ssov2_header_uploads.upld_docid',
                                'xt_ssov2_header_uploads.upld_rechazado',
                                'xt_ssov2_header_uploads.upld_upddat',
                                'xt_ssov2_header_uploads.upld_sso_id'])->toArray();

                        foreach ($trabajador as $value) {
                            unset($datosReporteSP);
                            $estadoDocumento = "Por Revisión";
                            
                            $datosReporteSP["idDoc"] = $value["id"];
                            $datosReporteSP["folio"] = $folio["id"];
                            $datosReporteSP["empresaPrincipal"] = strtoupper($folio["sso_mcomp_name"]);
                            $datosReporteSP["rutEmpresaPrincipal"] = strtoupper($folio["sso_mcomp_rut"]."-".$folio["sso_mcomp_dv"]);
                            $datosReporteSP["empresaContratista"] = strtoupper($folio["sso_comp_name"]);
                            $datosReporteSP["rutEmpresaContratista"] = strtoupper($folio["sso_comp_rut"]."-".$folio["sso_comp_dv"]);
                            if($folio["sso_subcomp_active"] == 1){
                            $datosReporteSP["empresaSubContratista"] = strtoupper($folio["sso_subcomp_name"]);
                            $datosReporteSP["rutEmpresaSubContratista"] = strtoupper($folio["sso_subcomp_rut"]."-".$folio["sso_subcomp_dv"]);
                            }else{
                            $datosReporteSP["empresaSubContratista"] = "";
                            $datosReporteSP["rutEmpresaSubContratista"] = "";   
                            } 
                            $datosReporteSP["proyecto"] = strtoupper($folio["sso_project"]);
                            $datosReporteSP["nombreTrabajador"] = strtoupper($value['worker_name1']);
                            $datosReporteSP["apellido1Trabajador"] = strtoupper($value['worker_name2']);
                            $datosReporteSP["apellido2Trabajador"] = strtoupper($value['worker_name3']);
                            $datosReporteSP["rutTrabajador"] = strtoupper($value['worker_rut']);
                            if($value['worker_syscargoname'] == ""){
                                $datosReporteSP["cargoTrabajador"] = strtoupper($value['cargo_name']);
                            }else{
                                $datosReporteSP["cargoTrabajador"] = strtoupper($value['worker_syscargoname']);
                            }
                            $datosReporteSP["documentoTrabajador"] = strtoupper($value['doc_name']);
                            $datosReporteSP["estadoDocumento"] = $estadoDocumento; 
                            $datosReporteSP["fechaCarga"] = date("d-m-Y H:i:00",$value['upld_crtdat']);
                            if($value['upld_vence_date']>0){
                                $datosReporteSP["fechaVence"] = date("d-m-Y H:i:00",$value['upld_vence_date']);  
                            }else{
                                $datosReporteSP["fechaVence"] = "";        
                            }
                            $datosReporteSP["ciclo"] = $folio["sso_cycle_aprobdays"]."X".$folio["sso_cycle_cargadays"];
                            $datosReporteSP['tipoPago'] = "";
                            $datosReporteSP['fechaAprobacion'] = "";
                            $datosReporteSP['fechaTransaccion'] = "";   
                            $datosReporteSP['idPago'] = ""; 
                                
                            if(!empty($datosReporteSP)){
                             $listaDatosReporteTrSP[] =$datosReporteSP;
                            }
                        }

                        $globalesDoc = DocConfigGlobal::distinct()
                        ->join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_configs_glbdocs.glb_docid')
                                    ->join('xt_ssov2_header_uploads', function ($join) use($folio) {
                                        $join->on('xt_ssov2_header_uploads.upld_docid','=','xt_ssov2_doctypes.id')
                                        ->where ('xt_ssov2_header_uploads.upld_sso_id', '=', intval($folio->id));
                                    })
                        ->where('xt_ssov2_configs_glbdocs.cfg_id', intval($folio->sso_cfgid))
                        ->where('xt_ssov2_doctypes.doc_status', 1)
                        ->where('xt_ssov2_doctypes.doc_type', 0)
                        ->where('xt_ssov2_header_uploads.upld_status', 1)
                        ->where('xt_ssov2_doctypes.doc_type', 0)
                        ->where('xt_ssov2_header_uploads.upld_venced', 0)
                        ->where('xt_ssov2_header_uploads.upld_rechazado', 0)
                        ->where('xt_ssov2_header_uploads.upld_docaprob', 0)
                        ->where('xt_ssov2_header_uploads.upld_aprobComen', 0)
                        ->whereBetween('xt_ssov2_header_uploads.upld_upddat', [$fechasDesde,$fechasHasta])
                        ->get(['xt_ssov2_header_uploads.upld_workerid',
                                            'xt_ssov2_doctypes.doc_name',
                                            'xt_ssov2_header_uploads.upld_venced',
                                            'xt_ssov2_header_uploads.upld_vence_date',
                                            'xt_ssov2_header_uploads.upld_docaprob',
                                            'xt_ssov2_header_uploads.upld_docaprob_date',
                                            'xt_ssov2_header_uploads.id',
                                            'xt_ssov2_header_uploads.upld_crtdat',
                                            'xt_ssov2_header_uploads.upld_docid',
                                            'xt_ssov2_header_uploads.upld_upddat',
                                            'xt_ssov2_header_uploads.upld_sso_id'])->toArray();

                        foreach ($globalesDoc as $docg) {
                            unset($datosReporteGlo);
                            $estadoDocumento = "Por Revisión";
                                
                            $datosReporteGlo["idDoc"] = $docg["id"];      
                            $datosReporteGlo["folio"] = $folio["id"];
                            $datosReporteGlo["empresaPrincipal"] = strtoupper($folio["sso_mcomp_name"]);
                            $datosReporteGlo["rutEmpresaPrincipal"] = strtoupper($folio["sso_mcomp_rut"]."-".$folio["sso_mcomp_dv"]);
                            $datosReporteGlo["empresaContratista"] = strtoupper($folio["sso_comp_name"]);
                            $datosReporteGlo["rutEmpresaContratista"] = strtoupper($folio["sso_comp_rut"]."-".$folio["sso_comp_dv"]);
                            if($folio["sso_subcomp_active"] == 1){
                            $datosReporteGlo["empresaSubContratista"] = strtoupper($folio["sso_subcomp_name"]);
                            $datosReporteGlo["rutEmpresaSubContratista"] = strtoupper($folio["sso_subcomp_rut"]."-".$folio["sso_subcomp_dv"]);
                            }else{
                            $datosReporteGlo["empresaSubContratista"] = "";
                            $datosReporteGlo["rutEmpresaSubContratista"] = ""; 
                            }  
                            $datosReporteGlo["proyecto"] = strtoupper($folio["sso_project"]);
                            $datosReporteGlo["nombreTrabajador"] = "";
                            $datosReporteGlo["apellido1Trabajador"] = "";
                            $datosReporteGlo["apellido2Trabajador"] = "";
                            $datosReporteGlo["rutTrabajador"] = "";
                            $datosReporteGlo["cargoTrabajador"] =  "";
                            $datosReporteGlo["documentoTrabajador"] = strtoupper($docg["doc_name"]);
                            $datosReporteGlo["estadoDocumento"] = $estadoDocumento;
                            $datosReporteGlo["fechaCarga"] = date("d-m-Y H:i:00",$docg['upld_upddat']);
                            if($docg['upld_vence_date']>0){
                                $datosReporteGlo["fechaVence"] = date("d-m-Y H:i:00",$docg['upld_vence_date']);  
                            }else{
                                $datosReporteGlo["fechaVence"] = "";        
                            }
                            $datosReporteGlo["ciclo"] = $folio["sso_cycle_aprobdays"]."X".$folio["sso_cycle_cargadays"];
                            $datosReporteGlo['tipoPago'] = "";
                            $datosReporteGlo['fechaAprobacion'] = "";
                            $datosReporteGlo['fechaTransaccion'] = "";
                            $datosReporteGlo['idPago'] = "";       
                        

                            if(!empty($datosReporteGlo)){
                             $listaDatosReporteGlo[] =$datosReporteGlo;
                            }       
                        }

                    }//else
                  
                    /*if (isset($listaDatosReporteTr[0]["idDoc"]) || isset($listaDatosReporteTrSP[0]["idDoc"]) || isset($listaDatosReporteGlo[0]["idDoc"])) {
                        $listaDatosReporte =array_merge($listaDatosReporteTr,$listaDatosReporteTrSP,$listaDatosReporteGlo);
                    }
                    if (isset($listaDatosReporteTrSP[0]["idDoc"]) || isset($listaDatosReporteGlo[0]["idDoc"])) {
                        $listaDatosReporte =array_merge($listaDatosReporteTrSP,$listaDatosReporteGlo);
                    }
                    if (isset($listaDatosReporteTr[0]["idDoc"]) || isset($listaDatosReporteGlo[0]["idDoc"])) {
                        $listaDatosReporte =array_merge($listaDatosReporteTr,$listaDatosReporteGlo);
                    }
                    if (isset($listaDatosReporteGlo[0]["idDoc"])) {
                        $listaDatosReporte =array_merge($listaDatosReporteGlo);
                    }*/
                    
                    

                }//fin 6

                if($tipoInforme == 9){

                    $documentosFlota = DB::select('select t1.id as idDoc, t1.upld_crtdat, t1.upld_upddat, t1.upld_docaprob, t1.upld_docaprob_date, t1.upld_venced, t1.upld_vence_date, t1.upld_rechazado, t1.upld_comments,t1.upld_aprobComen, t1.upld_flotaid, t2.modelo, t3.marca, t2.anio, t2.placa,  t4.doc_name  
                        from xt_ssov2_header_uploads t1
                        inner join xt_ssov2_header_flota t2 on t2.id = t1.upld_flotaid
                        inner join marcaVehiculos t3 on t3.idmarca = t2.marca
                        inner join xt_ssov2_doctypes t4 on t4.id = t1.upld_docid
                        where t1.upld_flotaid != 0 and t1.upld_status = 1 and t2.estatus = 1 and upld_upddat Between '.$fechasDesde.' and '.$fechasHasta.' and t1.upld_sso_id = '.$folio["id"].'');

                   

                    if(!empty($documentosFlota[0]->idDoc)){

                        foreach ($documentosFlota as $docFlota) {

                            $datosReporteF["idDoc"] = $docFlota->idDoc;     
                            $datosReporteF["folio"] = $folio["id"];
                            $datosReporteF["empresaPrincipal"] = strtoupper($folio["sso_mcomp_name"]);
                            $datosReporteF["rutEmpresaPrincipal"] = strtoupper($folio["sso_mcomp_rut"]."-".$folio["sso_mcomp_dv"]);
                            $datosReporteF["empresaContratista"] = strtoupper($folio["sso_comp_name"]);
                            $datosReporteF["rutEmpresaContratista"] = strtoupper($folio["sso_comp_rut"]."-".$folio["sso_comp_dv"]);
                            if($folio["sso_subcomp_active"] == 1){
                            $datosReporteF["empresaSubContratista"] = strtoupper($folio["sso_subcomp_name"]);
                            $datosReporteF["rutEmpresaSubContratista"] = strtoupper($folio["sso_subcomp_rut"]."-".$folio["sso_subcomp_dv"]);
                            }else{
                            $datosReporteF["empresaSubContratista"] = "";
                            $datosReporteF["rutEmpresaSubContratista"] = "";   
                            } 
                            $datosReporteF["proyecto"] = strtoupper($folio["sso_project"]);
                            $datosReporteF["marca"] = strtoupper($docFlota->marca);
                            $datosReporteF["modelo"] = strtoupper($docFlota->modelo);
                            $datosReporteF["placa"] = strtoupper($docFlota->placa);
                            $datosReporteF["anio"] = strtoupper($docFlota->anio);
                            $datosReporteF["documento"] = mb_strtoupper(str_replace('..', '',$docFlota->doc_name));
                            if($docFlota->upld_docaprob == 1){
                                $estadoDocumento = 'APROBADO';
                                if($docFlota->upld_aprobComen){
                                    $estadoDocumento = 'APROBADO CON OBS';

                                }
                            }elseif($docFlota->upld_venced == 1){
                                $estadoDocumento = 'VENCIDO';
                            }elseif ($docFlota->upld_rechazado == 1){
                                $estadoDocumento = 'RECHAZADO';
                            }else{
                                $estadoDocumento = 'POR REVISION';
                            }

                            $datosReporteF["estadoDocumento"] = $estadoDocumento; 
                            $datosReporteF["fechaCarga"] = date("d-m-Y H:i:00",$docFlota->upld_upddat);
                            if($docFlota->upld_vence_date > 0){
                                $datosReporteF["fechaVence"] = date("d-m-Y H:i:00",$docFlota->upld_vence_date);  
                            }else{
                                $datosReporteF["fechaVence"] = "";        
                            }
                            $datosReporteF["ciclo"] = $folio["sso_cycle_aprobdays"]."X".$folio["sso_cycle_cargadays"];

                            if(!empty($datosReporteF)){
                                $listaDatosReporte[] =$datosReporteF;
                            } 
                           
                        }

                        if(!empty($listaDatosReporte)){
                            $activaLista=1;
                            $tablav ='<table id="datosTabla" class="table table-bordered table-striped display">
                            <thead>
                            <tr>
                              <th>Doc id</th>
                              <th>Folio</th>
                              <th>Empresa Principal</th>
                              <th>RUT</th>
                              <th>Empresa Contratista</th>
                              <th>RUT</th>
                              <th>Empresa Sub Contratista</th>
                              <th>RUT</th>
                              <th>Proyecto</th>
                              <th>Marca</th>
                              <th>Modelo</th>
                              <th>Placa</th>
                              <th>Año</th>
                              <th>Documento</th>
                              <th>Estado</th>
                              <th>Fecha Registro</th>
                              <th>Fecha Vence</th>
                              <th>Ciclo</th>
                            </tr>
                            </thead>
                             <tbody>';

                            foreach($listaDatosReporte as $datos){

                                $tablav.= "<tr>";
                                $tablav.= "<td>".$datos["idDoc"]."</td>";
                                $tablav.= "<td>".$datos["folio"]."</td>";
                                $tablav.= "<td>".$datos["empresaPrincipal"]."</td>";
                                $tablav.= "<td>".$datos["rutEmpresaPrincipal"]."</td>";
                                $tablav.= "<td>".$datos["empresaContratista"]."</td>";
                                $tablav.= "<td>".$datos["rutEmpresaContratista"]."</td>";
                                $tablav.= "<td>".$datos["empresaSubContratista"]."</td>";
                                $tablav.= "<td>".$datos["rutEmpresaSubContratista"]."</td>";
                                $tablav.= "<td>".$datos["proyecto"]."</td>";
                                $tablav.= "<td>".$datos["marca"]."</td>";
                                $tablav.= "<td>".$datos["modelo"]."</td>";
                                $tablav.= "<td>".$datos["placa"]."</td>";
                                $tablav.= "<td>".$datos["anio"]."</td>";
                                $tablav.= "<td>".$datos["documento"]."</td>";
                                $tablav.= "<td>".$datos["estadoDocumento"]."</td>";
                                $tablav.= "<td>".$datos["fechaCarga"]."</td>";
                                $tablav.= "<td>".$datos["fechaVence"]."</td>";
                                $tablav.= "<td>".$datos["ciclo"]."</td>";
                                $tablav.= "</tr>";

                                switch ($datos["estadoDocumento"]) {
                                 case 'POR REVISION':
                                       $cantidadPorRevision +=1;
                                      break;
                                 case 'APROBADO':
                                      $cantidadAprobados +=1; 
                                      break;
                                 case 'VENCIDO':
                                      $cantidadVencidos +=1; ;
                                      break;
                                 case 'APROBADO CON OBS':
                                      $cantidadAprobadoObse +=1;
                                      break;
                                 case 'RECHAZADO':
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
                    }
                }
                /// FIN 9 
               
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

            if($tipoInforme == 9){
            
                Excel::create('Reporte Flota', function($excel) use ($tablav) {
                    $excel->sheet('Documentos Flota', function($sheet) use($tablav) {    
                        $sheet->loadView('documentoReporte.excelTabla',compact('tablav'));
                    });
                })->export('xlsx');

            }
            //exit();
           
            if(!empty($listaDatosReporteTr) and !empty($listaDatosReporteTrSP) and !empty($listaDatosReporteGlo)){
                $listaDatosReporte =array_merge($listaDatosReporteTr,$listaDatosReporteTrSP,$listaDatosReporteGlo);  
                
            }if(empty($listaDatosReporteTr) and !empty($listaDatosReporteTrSP) and !empty($listaDatosReporteGlo)){
                $listaDatosReporte =array_merge($listaDatosReporteTrSP,$listaDatosReporteGlo);  
                
            }if(empty($listaDatosReporteTr) and empty($listaDatosReporteTrSP) and !empty($listaDatosReporteGlo)){
                $listaDatosReporte = $listaDatosReporteGlo;  
                
            }if(!empty($listaDatosReporteTr) and empty($listaDatosReporteTrSP) and empty($listaDatosReporteGlo)){
                $listaDatosReporte = $listaDatosReporteTr;  
                
            }if(!empty($listaDatosReporteTr) and !empty($listaDatosReporteTrSP) and empty($listaDatosReporteGlo)){
                $listaDatosReporte = array_merge($listaDatosReporteTr,$listaDatosReporteTrSP); 
                
            }if(!empty($listaDatosReporteTr) and empty($listaDatosReporteTrSP) and !empty($listaDatosReporteGlo)){
                $listaDatosReporte = array_merge($listaDatosReporteTr,$listaDatosReporteGlo); 
                
            }if(empty($listaDatosReporteTr) and !empty($listaDatosReporteTrSP) and empty($listaDatosReporteGlo)){
                $listaDatosReporte = $listaDatosReporteTrSP; 
                
            }
       

            if(!empty($listaDatosReporte)){
                $activaLista=1;
                $listaCuerpo ='<table id="datosTabla" class="table table-bordered table-striped display">
                <thead>
                <tr>
                  <th>Doc id</th>
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
                  <th>Ciclo</th>
                  <th>Tipo Pago</th>
                  <th>Fecha Aprobacion</th>
                  <th>Fecha Transaccion</th>
                  <th>N SSO</th>
                </tr>
                </thead>
                 <tbody>';

                foreach($listaDatosReporte as $datos){

                    $listaCuerpo.= "<tr>";
                    $listaCuerpo.= "<td>".$datos["idDoc"]."</td>";
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
                    $listaCuerpo.= "<td>".$datos["ciclo"]."</td>";
                    $listaCuerpo.= "<td>".$datos["tipoPago"]."</td>";
                    $listaCuerpo.= "<td>".$datos["fechaAprobacion"]."</td>";
                    $listaCuerpo.= "<td>".$datos["fechaTransaccion"]."</td>";
                    $listaCuerpo.= "<td>".$datos["idPago"]."</td>";
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
