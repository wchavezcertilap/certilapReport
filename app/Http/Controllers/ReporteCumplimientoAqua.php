<?php

namespace App\Http\Controllers;
use DB;
use Excel;
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
use App\CargoCateDoc;


use Illuminate\Http\Request;

class ReporteCumplimientoAqua extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$usuarioAqua = session('user_aqua');
       
        $idUsuario = session('user_id');
        if($idUsuario ==  ""){
            return view('sesion.index');
        }
        $usuarioAqua = session('user_aqua');
        $usuarioABBChile= session('user_ABB');
        $usuarioNOKactivo = session('usuario_nok');
        $certificacion = session('certificacion');
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
        $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
        $UsuarioPrincipal->load('usuarioDatos');
        $etiquetasEstadosCovid=0;
        $valoresCovid =0;

        foreach ($UsuarioPrincipal as $rut) {

            $rutprincipal[]=$rut['mainCompanyRut'];
            
        }

            if($datosUsuarios->type == 3){

                $EmpresasP = FolioSso::distinct()->whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['sso_mcomp_name','sso_mcomp_rut']);

                return view('reporteCumplimientoAqua.index',compact('datosUsuarios','EmpresasP','certificacion','etiquetasEstadosCovid','valoresCovid','usuarioAqua','usuarioABBChile','usuarioNOKactivo')); 

            }
            if($datosUsuarios->type ==2 || $datosUsuarios->type ==1 ){

                $rutprincipal = ['76452811','76794910','79872420','86247400','79800600','84449400','88274600','87782700','76495180','99595500','89604200','78754560','76125666','78512930'];
                $EmpresasP = FolioSso::distinct()->whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['sso_mcomp_name','sso_mcomp_rut']);

                return view('reporteCumplimientoAqua.index',compact('datosUsuarios','EmpresasP','certificacion','etiquetasEstadosCovid','valoresCovid','usuarioAqua','usuarioABBChile','usuarioNOKactivo')); 

            }
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
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
        $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
        $UsuarioPrincipal->load('usuarioDatos');


        foreach ($UsuarioPrincipal as $rut) {

            $rutprincipal[]=$rut['mainCompanyRut'];
            
        }

            if($datosUsuarios->type == 3){

                $EmpresasP = FolioSso::distinct()->whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv']);

               // return view('reporteCumplimientoAqua.index',compact('datosUsuarios','EmpresasP','certificacion')); 

            }
            if($datosUsuarios->type ==2 || $datosUsuarios->type ==1 ){

                $rutprincipal = ['76452811','76794910','79872420','86247400','79800600','84449400','88274600','87782700','76495180','99595500','89604200','78754560','76125666','78512930'];

                $EmpresasP = FolioSso::distinct()->whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv']);
                //return view('reporteCumplimientoAqua.index',compact('datosUsuarios','EmpresasP','certificacion')); 

            }
        
        //////// busqueda de datos //////
        $input=$request->all();  
        
        $empresaPrin = $input['empresaPrincipal'];
        $fechaSeleccion = $input["fechaSeleccion"];
        $fechas = explode(" ", $fechaSeleccion);
        $fecha1V = trim($fechas[0]);
        $fecha2V = trim($fechas[1]);
        $fechasDesde = strtotime(str_replace('/', '-', $fecha1V));
        $fechasHasta = strtotime(str_replace('/', '-', $fecha2V));
        if(!empty($input["empresaContratista"])){
           
            $empresasCon = $input["empresaContratista"];
            
        }

        if($empresaPrin[0] == 1){

            foreach ($EmpresasP as $folio) {

                $folios[] = $folio->id;

            }
          
            if(!empty($folios)){
                
                $documentos = EstadoDocumento::whereIn('upld_sso_id',$folios)->where('upld_status', '1')
                ->whereBetween('upld_crtdat', [$fechasDesde,$fechasHasta])
                ->join('xt_ssov2_header_worker', 'xt_ssov2_header_uploads.upld_workerid', '=', 'xt_ssov2_header_worker.id')
                ->where('xt_ssov2_header_worker.worker_status','1')
                ->where('xt_ssov2_header_worker.desvinculado','0')
                ->join('xt_ssov2_header', 'xt_ssov2_header_uploads.upld_sso_id', '=', 'xt_ssov2_header.id')
                ->where('xt_ssov2_header.sso_status','1')
                 ->join('xt_ssov2_doctypes', 'xt_ssov2_header_uploads.upld_docid', '=', 'xt_ssov2_doctypes.id')
                ->select('xt_ssov2_header_uploads.*', 'xt_ssov2_header_worker.*','xt_ssov2_header.sso_mcomp_rut','xt_ssov2_header.sso_mcomp_dv','xt_ssov2_header.sso_mcomp_name','xt_ssov2_header.sso_comp_name','xt_ssov2_header.sso_comp_dv','xt_ssov2_header.sso_comp_rut','xt_ssov2_header.sso_subcomp_active','xt_ssov2_header.sso_subcomp_rut','xt_ssov2_header.sso_subcomp_dv','xt_ssov2_header.sso_subcomp_name','xt_ssov2_doctypes.doc_name')
                ->get()->toArray();

                $documentosGlobales = EstadoDocumento::whereIn('upld_sso_id',$folios)->where('upld_status', '1')->where('upld_workerid', '0')
                ->whereBetween('upld_crtdat', [$fechasDesde,$fechasHasta])
                ->join('xt_ssov2_header', 'xt_ssov2_header_uploads.upld_sso_id', '=', 'xt_ssov2_header.id')
                ->where('xt_ssov2_header.sso_status','1')
                 ->join('xt_ssov2_doctypes', 'xt_ssov2_header_uploads.upld_docid', '=', 'xt_ssov2_doctypes.id')
                ->select('xt_ssov2_header_uploads.*','xt_ssov2_header.sso_mcomp_rut','xt_ssov2_header.sso_mcomp_dv','xt_ssov2_header.sso_mcomp_name','xt_ssov2_header.sso_comp_name','xt_ssov2_header.sso_comp_dv','xt_ssov2_header.sso_comp_rut','xt_ssov2_header.sso_subcomp_active','xt_ssov2_header.sso_subcomp_rut','xt_ssov2_header.sso_subcomp_dv','xt_ssov2_header.sso_subcomp_name','xt_ssov2_doctypes.doc_name')
                ->get()->toArray();

                
                
                /// ingresados //
                
                $cantidadAprobados = 0;
                $cantidadRechazados = 0;
                $cantidadVencidos = 0;
                $cantidadPorRevision = 0;
               
                $cantidadAprobadosCovid = 0;
                $cantidadRechazadosCovid = 0;
                $cantidadVencidosCovid = 0;
                $cantidadPorRevisionCovid = 0;
                $totalTrabajadoresEmpresaCovid = 0;

                $cantidadAprobadosGlobal = 0;
                $cantidadRechazadosGlobal = 0;
                $cantidadVencidosGlobal = 0;
                $cantidadPorRevisionGlobal = 0;
                $totalTrabajadoresEmpresa = 0;
                
                $estadoDocumento ="";

                $fecha_actual = strtotime(date("d-m-Y H:i:00",time())); 
                foreach ($documentos as $value) {
                    
                    ////////////////// fecha para determinar si esta expirado /////
                    $fecha2 = $value["upld_vence_date"];
                    $fechaUpdate = $value["upld_upddat"];
                        
                    $totalTrabajadoresEmpresa += 1;
                    if($value["upld_docid"] == 660 or $value["upld_docid"] == 661){
                       
                        if($value["upld_rechazado"] == 1) {
                            $cantidadRechazadosCovid +=1; 
                            $estadoDocumento ="Rechazado";
                            $datosReporteRechazadosCovid["folio"] = $value["upld_sso_id"];
                            $datosReporteRechazadosCovid["principal"] = ucwords(mb_strtolower($value["sso_mcomp_name"],'UTF-8'));
                            $datosReporteRechazadosCovid["rutPrincipal"] = $value["sso_mcomp_rut"]."-".$value["sso_mcomp_dv"];
                            $datosReporteRechazadosCovid["Contratista"] = ucwords(mb_strtolower($value["sso_comp_name"],'UTF-8'));
                            $datosReporteRechazadosCovid["rutContratista"] = $value["sso_comp_rut"]."-".$value["sso_comp_dv"];
                            if($value["sso_subcomp_active"] == 1){
                                $datosReporteRechazadosCovid["subContratista"] = ucwords(mb_strtolower($value["sso_subcomp_name"],'UTF-8'));
                                $datosReporteRechazadosCovid["subrutContratista"] = $value["sso_subcomp_rut"]."-".$value["sso_subcomp_dv"];
                            }else{
                                $datosReporteRechazadosCovid["subContratista"] = "";
                                $datosReporteRechazadosCovid["subrutContratista"] = "";
                            }
                            $datosReporteRechazadosCovid["nombreTrabajador"] = ucwords(mb_strtolower($value["worker_name1"],'UTF-8'));
                            $datosReporteRechazadosCovid["apellido1Trabajador"] = ucwords(mb_strtolower($value["worker_name2"],'UTF-8'));
                            $datosReporteRechazadosCovid["apellido2Trabajador"] = ucwords(mb_strtolower($value["worker_name3"],'UTF-8'));
                            $datosReporteRechazadosCovid["rutTrabajador"] = strtoupper($value["worker_rut"]);
                            $datosReporteRechazadosCovid["cargoTrabajador"] = ucwords(mb_strtolower($value["worker_syscargoname"],'UTF-8'));
                            $datosReporteRechazadosCovid["documentoTrabajador"] = ucwords(mb_strtolower($value["doc_name"],'UTF-8'));
                            $datosReporteRechazadosCovid["fechaCreacionDoc"] = date('d/m/Y', $value['upld_crtdat']);
                            $datosReporteRechazadosCovid["observacionDoc"] = ucwords(mb_strtolower($value['upld_comments'],'UTF-8'));
                            $datosReporteRechazadosCovid["estadoDocumento"] = ucwords(mb_strtolower($estadoDocumento,'UTF-8'));
                            $datosReporteRechazadosCovid["fechaVenceDoc"] = date('d/m/Y', $value['upld_vence_date']);
                            if(!empty($datosReporteRechazadosCovid)){
                            $listaDatosReporteRechazadosCovid[] =$datosReporteRechazadosCovid;
                            }
                        }elseif (($value["upld_docaprob"] == 1 or $fecha_actual <= $fechaUpdate) and $fecha_actual < $fecha2){
                                $cantidadAprobadosCovid +=1; 
                                $estadoDocumento ="Aprobado";
                        }elseif (($value["upld_venced"]== 1  or $fecha_actual > $fecha2)and $value["upld_rechazado"] == 0 and $fecha2!= 0){
                                $cantidadVencidosCovid +=1; 
                                $estadoDocumento ="Vencido";
                        }
                        elseif ($value["upld_docaprob"] == 0 and $value["upld_docaprob_uid"] == 0){
                                $cantidadPorRevisionCovid +=1; 
                                $estadoDocumento ="Por Revisión";
                        }

                    
                        $datosReporteCovid["folio"] = $value["upld_sso_id"];
                        $datosReporteCovid["principal"] = ucwords(mb_strtolower($value["sso_mcomp_name"],'UTF-8'));
                        $datosReporteCovid["rutPrincipal"] = $value["sso_mcomp_rut"]."-".$value["sso_mcomp_dv"];
                        $datosReporteCovid["Contratista"] = ucwords(mb_strtolower($value["sso_comp_name"],'UTF-8'));
                        $datosReporteCovid["rutContratista"] = $value["sso_comp_rut"]."-".$value["sso_comp_dv"];
                        if($value["sso_subcomp_active"] == 1){
                            $datosReporteCovid["subContratista"] = ucwords(mb_strtolower($value["sso_subcomp_name"],'UTF-8'));
                            $datosReporteCovid["subrutContratista"] = $value["sso_subcomp_rut"]."-".$value["sso_subcomp_dv"];
                        }else{
                            $datosReporteCovid["subContratista"] = "";
                            $datosReporteCovid["subrutContratista"] = "";
                        }
                        $datosReporteCovid["nombreTrabajador"] = ucwords(mb_strtolower($value["worker_name1"],'UTF-8'));
                        $datosReporteCovid["apellido1Trabajador"] = ucwords(mb_strtolower($value["worker_name2"],'UTF-8'));
                        $datosReporteCovid["apellido2Trabajador"] = ucwords(mb_strtolower($value["worker_name3"],'UTF-8'));
                        $datosReporteCovid["rutTrabajador"] = strtoupper($value["worker_rut"]);
                        $datosReporteCovid["cargoTrabajador"] = ucwords(mb_strtolower($value["worker_syscargoname"],'UTF-8'));
                        $datosReporteCovid["documentoTrabajador"] = ucwords(mb_strtolower($value["doc_name"],'UTF-8'));
                        $datosReporteCovid["fechaCreacionDoc"] = date('d/m/Y', $value['upld_crtdat']);
                        $datosReporteCovid["observacionDoc"] = ucwords(mb_strtolower($value['upld_comments'],'UTF-8'));
                        $datosReporteCovid["estadoDocumento"] = ucwords(mb_strtolower($estadoDocumento,'UTF-8'));
                        $datosReporteCovid["fechaVenceDoc"] = date('d/m/Y', $value['upld_vence_date']);
                        if(!empty($datosReporteCovid)){
                            $listaDatosReporteCovid[] =$datosReporteCovid;
                        }
                    }else{
                        if ($value["upld_rechazado"] == 1) {
                            $cantidadRechazados +=1;
                            $estadoDocumento ="Rechazado";
                            $datosReporteRechazados["folio"] = $value["upld_sso_id"];
                            $datosReporteRechazados["principal"] = ucwords(mb_strtolower($value["sso_mcomp_name"],'UTF-8'));
                            $datosReporteRechazados["rutPrincipal"] = $value["sso_mcomp_rut"]."-".$value["sso_mcomp_dv"];
                            $datosReporteRechazados["Contratista"] = ucwords(mb_strtolower($value["sso_comp_name"],'UTF-8'));
                            $datosReporteRechazados["rutContratista"] = $value["sso_comp_rut"]."-".$value["sso_comp_dv"];
                            if($value["sso_subcomp_active"] == 1){
                                $datosReporteRechazados["subContratista"] = ucwords(mb_strtolower($value["sso_subcomp_name"],'UTF-8'));
                                $datosReporteRechazados["subrutContratista"] = $value["sso_subcomp_rut"]."-".$value["sso_subcomp_dv"];
                            }else{
                                $datosReporteRechazados["subContratista"] = "";
                                $datosReporteRechazados["subrutContratista"] = "";
                            }
                            $datosReporteRechazados["nombreTrabajador"] = ucwords(mb_strtolower($value["worker_name1"],'UTF-8'));
                            $datosReporteRechazados["apellido1Trabajador"] = ucwords(mb_strtolower($value["worker_name2"],'UTF-8'));
                            $datosReporteRechazados["apellido2Trabajador"] = ucwords(mb_strtolower($value["worker_name3"],'UTF-8'));
                            $datosReporteRechazados["rutTrabajador"] = strtoupper($value["worker_rut"]);
                            $datosReporteRechazados["cargoTrabajador"] = ucwords(mb_strtolower($value["worker_syscargoname"],'UTF-8'));
                            $datosReporteRechazados["documentoTrabajador"] = ucwords(mb_strtolower($value["doc_name"],'UTF-8'));
                            $datosReporteRechazados["fechaCreacionDoc"] = date('d/m/Y', $value['upld_crtdat']);
                            $datosReporteRechazados["observacionDoc"] = ucwords(mb_strtolower($value['upld_comments'],'UTF-8'));
                            $datosReporteRechazados["estadoDocumento"] = ucwords(mb_strtolower($estadoDocumento,'UTF-8'));
                            $datosReporteRechazados["fechaVenceDoc"] = date('d/m/Y', $value['upld_vence_date']);
                            if(!empty($datosReporteRechazados)){
                            $listaDatosReporteRechazados[] =$datosReporteRechazados;
                            }

                        }elseif (($value["upld_docaprob"] == 1 or $fecha_actual <= $fechaUpdate) and $fecha_actual < $fecha2){
                                $cantidadAprobados +=1; 
                                $estadoDocumento ="Aprobado";
                        }elseif (($value["upld_venced"]== 1  or $fecha_actual > $fecha2)and $value["upld_rechazado"] == 0 and $fecha2!= 0){
                                $cantidadVencidos +=1; 
                                $estadoDocumento ="Vencido";
                        }
                        elseif ($value["upld_docaprob"] == 0 and $value["upld_docaprob_uid"] == 0){
                                $cantidadPorRevision +=1; 
                                $estadoDocumento ="Por Revisión";
                        }

                    
                        $datosReporte["folio"] = $value["upld_sso_id"];
                        $datosReporte["principal"] = ucwords(mb_strtolower($value["sso_mcomp_name"],'UTF-8'));
                        $datosReporte["rutPrincipal"] = $value["sso_mcomp_rut"]."-".$value["sso_mcomp_dv"];
                        $datosReporte["Contratista"] = ucwords(mb_strtolower($value["sso_comp_name"],'UTF-8'));
                        $datosReporte["rutContratista"] = $value["sso_comp_rut"]."-".$value["sso_comp_dv"];
                        if($value["sso_subcomp_active"] == 1){
                            $datosReporte["subContratista"] = ucwords(mb_strtolower($value["sso_subcomp_name"],'UTF-8'));
                            $datosReporte["subrutContratista"] = $value["sso_subcomp_rut"]."-".$value["sso_subcomp_dv"];
                        }else{
                            $datosReporte["subContratista"] = "";
                            $datosReporte["subrutContratista"] = "";
                        }
                        $datosReporte["nombreTrabajador"] = ucwords(mb_strtolower($value["worker_name1"],'UTF-8'));
                        $datosReporte["apellido1Trabajador"] = ucwords(mb_strtolower($value["worker_name2"],'UTF-8'));
                        $datosReporte["apellido2Trabajador"] = ucwords(mb_strtolower($value["worker_name3"],'UTF-8'));
                        $datosReporte["rutTrabajador"] = strtoupper($value["worker_rut"]);
                        $datosReporte["cargoTrabajador"] = ucwords(mb_strtolower($value["worker_syscargoname"],'UTF-8'));
                        $datosReporte["documentoTrabajador"] = ucwords(mb_strtolower($value["doc_name"],'UTF-8'));
                        $datosReporte["fechaCreacionDoc"] = date('d/m/Y', $value['upld_crtdat']);
                        $datosReporte["observacionDoc"] = ucwords(mb_strtolower($value['upld_comments'],'UTF-8'));
                        $datosReporte["estadoDocumento"] = ucwords(mb_strtolower($estadoDocumento,'UTF-8'));
                        $datosReporte["fechaVenceDoc"] = date('d/m/Y', $value['upld_vence_date']);
                        if(!empty($datosReporte)){
                            $listaDatosReporte[] =$datosReporte;
                        }
                    }
                }

                foreach ($documentosGlobales as $value) {
                    
                    ////////////////// fecha para determinar si esta expirado /////
                    $fecha2 = $value["upld_vence_date"];
                    $fechaUpdate = $value["upld_upddat"];
                 

                    if ($value["upld_rechazado"] == 1) {
                        $cantidadRechazadosGlobal +=1; 
                        $estadoDocumento ="Rechazado";
                        $datosReporteRechazadosGlobales["folio"] = $value["upld_sso_id"];
                        $datosReporteRechazadosGlobales["principal"] = ucwords(mb_strtolower($value["sso_mcomp_name"],'UTF-8'));
                        $datosReporteRechazadosGlobales["rutPrincipal"] = $value["sso_mcomp_rut"]."-".$value["sso_mcomp_dv"];
                        $datosReporteRechazadosGlobales["Contratista"] = ucwords(mb_strtolower($value["sso_comp_name"],'UTF-8'));
                        $datosReporteRechazadosGlobales["rutContratista"] = $value["sso_comp_rut"]."-".$value["sso_comp_dv"];
                        if($value["sso_subcomp_active"] == 1){
                            $datosReporteRechazadosGlobales["subContratista"] = ucwords(mb_strtolower($value["sso_subcomp_name"],'UTF-8'));
                            $datosReporteRechazadosGlobales["subrutContratista"] = $value["sso_subcomp_rut"]."-".$value["sso_subcomp_dv"];
                        }else{
                            $datosReporteRechazadosGlobales["subContratista"] = "";
                            $datosReporteRechazadosGlobales["subrutContratista"] = "";
                        }
                        $datosReporteRechazadosGlobales["nombreTrabajador"] = "";
                        $datosReporteRechazadosGlobales["apellido1Trabajador"] = "";
                        $datosReporteRechazadosGlobales["apellido2Trabajador"] = "";
                        $datosReporteRechazadosGlobales["rutTrabajador"] = "";
                        $datosReporteRechazadosGlobales["cargoTrabajador"] = "";
                        $datosReporteRechazadosGlobales["documentoTrabajador"] = ucwords(mb_strtolower($value["doc_name"],'UTF-8'));
                        $datosReporteRechazadosGlobales["fechaCreacionDoc"] = date('d/m/Y', $value['upld_crtdat']);
                        $datosReporteRechazadosGlobales["observacionDoc"] = ucwords(mb_strtolower($value['upld_comments'],'UTF-8'));
                        $datosReporteRechazadosGlobales["estadoDocumento"] = ucwords(mb_strtolower($estadoDocumento,'UTF-8'));
                        $datosReporteRechazadosGlobales["fechaVenceDoc"] = date('d/m/Y', $value['upld_vence_date']);
                        if(!empty($datosReporteRechazadosGlobales)){
                            $listaDatosReporteRechazadosGlobales[] =$datosReporteRechazadosGlobales;
                        }
                    }elseif (($value["upld_docaprob"] == 1 or $fecha_actual <= $fechaUpdate) and $fecha_actual < $fecha2){
                        $cantidadAprobadosGlobal +=1; 
                        $estadoDocumento ="Aprobado";
                    }elseif (($value["upld_venced"]== 1  or $fecha_actual > $fecha2)and $value["upld_rechazado"] == 0 and $fecha2!= 0){
                        $cantidadVencidosGlobal +=1; 
                        $estadoDocumento ="Vencido";
                    }
                    elseif ($value["upld_docaprob"] == 0 and $value["upld_docaprob_uid"] == 0){
                        $cantidadPorRevisionGlobal +=1; 
                        $estadoDocumento ="Por Revisión";
                    }

                    $datosReporteGlobal["folio"] =  $value["upld_sso_id"];
                    $datosReporteGlobal["principal"] = ucwords(mb_strtolower($value["sso_mcomp_name"],'UTF-8'));
                    $datosReporteGlobal["rutPrincipal"] = $value["sso_mcomp_rut"]."-".$value["sso_mcomp_dv"];
                    $datosReporteGlobal["Contratista"] = ucwords(mb_strtolower($value["sso_comp_name"],'UTF-8'));
                    $datosReporteGlobal["rutContratista"] = $value["sso_comp_rut"]."-".$value["sso_comp_dv"];
                    if($value["sso_subcomp_active"] == 1){
                        $datosReporteGlobal["Contratista"] = ucwords(mb_strtolower($value["sso_subcomp_name"],'UTF-8'));
                        $datosReporteGlobal["rutContratista"] = $value["sso_subcomp_rut"]."-".$value["sso_subcomp_dv"];
                    }else{
                        $datosReporteGlobal["Contratista"] = "";
                        $datosReporteGlobal["rutContratista"] = "";
                    }
                    $datosReporteGlobal["nombreTrabajador"] = "";
                    $datosReporteGlobal["apellido1Trabajador"] = "";
                    $datosReporteGlobal["apellido2Trabajador"] = "";
                    $datosReporteGlobal["rutTrabajador"] = "";
                    $datosReporteGlobal["cargoTrabajador"] =  "";
                    $datosReporteGlobal["documentoTrabajador"] = ucwords(mb_strtolower($value["doc_name"],'UTF-8'));
                    $datosReporteGlobal["fechaCreacionDoc"] = date('d/m/Y', $value['upld_crtdat']);
                    $datosReporteGlobal["observacionDoc"] = ucwords(mb_strtolower($value['upld_comments'],'UTF-8'));
                    $datosReporteGlobal["estadoDocumento"] = ucwords(mb_strtolower($estadoDocumento,'UTF-8'));
                    $datosReporteGlobal["fechaVenceDoc"] = date('d/m/Y', $value['upld_vence_date']);
                    if(!empty($datosReporteGlobal)){
                        $listaDatosReporteGlobal[] =$datosReporteGlobal;
                    }
                }
            }

            if(empty($listaDatosReporte)){
            $listaDatosReporte = ['','','','','','','','','','','','','','','','',''];
            } 

            if(empty($listaDatosReporteCovid)){
            $listaDatosReporteCovid = ['','','','','','','','','','','','','','','','',''];
            } 

            if(empty($listaDatosReporteGlobal)){
            $listaDatosReporteGlobal = ['','','','','','','','','','','','','','','','',''];
            }  

            if(empty($listaDatosReporteRechazadosCovid)){
            $listaDatosReporteRechazadosCovid = ['','','','','','','','','','','','','','','','',''];
            }
            if(empty($listaDatosReporteRechazados)){
            $listaDatosReporteRechazados = ['','','','','','','','','','','','','','','','',''];
            } 
             if(empty($listaDatosReporteRechazadosGlobales)){
            $listaDatosReporteRechazadosGlobales = ['','','','','','','','','','','','','','','','',''];
            }   




            Excel::create('Reporte Cumplimiento Aqua', function($excel) use($listaDatosReporteCovid,$cantidadAprobados,$cantidadRechazados,$cantidadVencidos,$cantidadPorRevision,$totalTrabajadoresEmpresa,$listaDatosReporte,$cantidadAprobadosCovid,$cantidadRechazadosCovid,$cantidadVencidosCovid,$cantidadPorRevisionCovid,$listaDatosReporteGlobal,$cantidadRechazadosGlobal,$cantidadAprobadosGlobal,$cantidadVencidosGlobal,$cantidadPorRevisionGlobal,$listaDatosReporteRechazadosCovid,$listaDatosReporteRechazados,$listaDatosReporteRechazadosGlobales,$fecha1V,$fecha2V) {
                    if($listaDatosReporteCovid!= 0){
                        $excel->sheet('Documentos Covid', function($sheet) use($listaDatosReporteCovid,$fecha1V,$fecha2V) {
                           
                            $sheet->cells('A5:Q5', function($cells) {
                                  $cells->setBorder('thin','thin','thin','thin');
                            });
                            $sheet->row(2, array('Empresa', 'Todas'));
                            $sheet->row(3, array('Fecha Desde', $fecha1V,'Fecha Hasta',$fecha2V));
                           
                            $sheet->row(5, array('folio','Empresa Principal','RUT Principal','Empresa Contratista','RUT Contratista','Empresa Sub Contratista','RUT Sub Contratista','Nombre','Apellido Paterno','Apellido Materno','RUT Trabajador','Cargo','Documento','Fecha de creación','Observación','Estado','Fecha de vencimiento'));
                                foreach ($listaDatosReporteCovid as $datosReporte) {
                                 $sheet->appendRow($datosReporte);
                                }

                        });

                        $excel->sheet('PorcentajeCovid', function($sheet) use($cantidadAprobadosCovid,$cantidadRechazadosCovid,$cantidadVencidosCovid,$cantidadPorRevisionCovid,$fecha1V,$fecha2V) {
                            $TotalGeneralCovid=$cantidadAprobadosCovid+$cantidadPorRevisionCovid+$cantidadRechazadosCovid+$cantidadVencidosCovid;
                            $sheet->row(2, array('Empresa', 'Todas'));
                            $sheet->row(3, array('Fecha Desde', $fecha1V,'Fecha Hasta',$fecha2V));
                            
                            $sheet->row(4, array('Estado', 'Total'));
                            $sheet->row(5, array('Aprobados', $cantidadAprobadosCovid));
                            $sheet->row(6, array('Por revisar', $cantidadPorRevisionCovid));
                            $sheet->row(7, array('Rechazado', $cantidadRechazadosCovid));
                            $sheet->row(8, array('Vencido', $cantidadVencidosCovid));
                            $sheet->row(9, array('Total general', $TotalGeneralCovid));

                            if($TotalGeneralCovid != 0){
                               
                                $sheet->row(12, array('Estado', 'Porcentaje de Acreditación'));
                                $sheet->row(13, array('Aprobados',  round(($cantidadAprobadosCovid*100)/$TotalGeneralCovid,0)));
                                $sheet->row(14, array('Por revisar', round(($cantidadPorRevisionCovid*100)/$TotalGeneralCovid,0)));
                                $sheet->row(15, array('Rechazado', round(($cantidadRechazadosCovid*100)/$TotalGeneralCovid,0)));
                                $sheet->row(16, array('Vencido', round(($cantidadVencidosCovid*100)/$TotalGeneralCovid,0)));
                            }else{
                                $sheet->row(12, array('Estado', 'Porcentaje de Acreditación'));
                                $sheet->row(13, array('Aprobados', 0));
                                $sheet->row(14, array('Por revisar', 0));
                                $sheet->row(15, array('Rechazado', 0));
                                $sheet->row(16, array('Vencido', 0));
                            }

                           // $xAxisTickValues2 = array(new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$2:$A$5', NULL, 4));
//  
                            $labels1 = [
                                new  \PHPExcel_Chart_DataSeriesValues('String','PorcentajeCovid!$A$2', null, 1), // 2011
                            ];

                            $categories1 = [
                                new \PHPExcel_Chart_DataSeriesValues('String','PorcentajeCovid!$A$13:$A$16', null, 4), // Q1 to Q4
                            ];

                            $values1 = [
                            new \PHPExcel_Chart_DataSeriesValues('Number','PorcentajeCovid!$B$13:$B$16', null, 4),
                            ];

                        
                            $series = new \PHPExcel_Chart_DataSeries(
                                \PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
                                null,  // plotGrouping
                                range(0, count($values1)-1),           // plotOrder
                                $labels1,                              // plotLabel
                                $categories1,                               // plotCategory
                                $values1                               // plotValues
                            );

                            //  Set up a layout object for the Pie chart
                            $layout1 = new \PHPExcel_Chart_Layout();
                            $layout1->setShowPercent(TRUE);
                        
                            //  Set the series in the plot area
                            $plotarea1 = new \PHPExcel_Chart_PlotArea($layout1, array($series));
                            //  Set the chart legend
                            $legend1 = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                       
                            $title1 = new \PHPExcel_Chart_Title('Porcentaje Covid');

                            //  Create the chart
                            $chart1 = new \PHPExcel_Chart(
                                'Porcentaje Documentos Covid', //name
                                $title1,        // title
                                $legend1,       // legend
                                $plotarea1,     // plotArea
                                true,           // plotVisibleOnly
                                0,              // displayBlanksAs
                                NULL,           // xAxisLabel
                                NULL            // yAxisLabel       - Pie charts don't have a Y-Axis
                            );

                            //  Set the position where the chart should appear in the worksheet
                            $chart1->setTopLeftPosition('E13');
                            $chart1->setBottomRightPosition('F20');
                            $sheet->addChart($chart1);

                        });

                        if(!empty($listaDatosReporteRechazadosCovid)){

                            $excel->sheet('Documentos Rechazados Covid', function($sheet) use($listaDatosReporteRechazadosCovid,$fecha1V,$fecha2V) {
                                
                                $sheet->cells('A5:Q5', function($cells) {
                                  $cells->setBorder('thin','thin','thin','thin');
                                });
                                $sheet->row(2, array('Empresa', 'Todas'));
                                $sheet->row(3, array('Fecha Desde', $fecha1V,'Fecha Hasta',$fecha2V));
                                
                                $sheet->row(5, array('folio','Empresa Principal','RUT Principal','Empresa Contratista','RUT Contratista','Empresa Sub Contratista','RUT Sub Contratista','Nombre','Apellido Paterno','Apellido Materno','RUT Trabajador','Cargo','Documento','Fecha de creación','Observación','Estado','Fecha de vencimiento'));
                                foreach ($listaDatosReporteRechazadosCovid as $datosReporte) {
                                 $sheet->appendRow($datosReporte);
                                }

                            });
                        }
                    }

                    if(!empty($listaDatosReporte)){
                        $excel->sheet('Otros Documentos', function($sheet) use($listaDatosReporte,$fecha1V,$fecha2V) {

                            $sheet->cells('A5:Q5', function($cells) {
                                  $cells->setBorder('thin','thin','thin','thin');
                                });
                            $sheet->row(2, array('Empresa', 'Todas'));
                            $sheet->row(3, array('Fecha Desde', $fecha1V,'Fecha Hasta',$fecha2V));
                            
                            $sheet->row(5, array('folio','Empresa Principal','RUT Principal','Empresa Contratista','RUT Contratista','Empresa Sub Contratista','RUT Sub Contratista','Nombre','Apellido Paterno','Apellido Materno','RUT Trabajador','Cargo','Documento','Fecha de creación','Observación','Estado','Fecha de vencimiento'));
                           foreach ($listaDatosReporte as $datosReporte) {
                             $sheet->appendRow($datosReporte);
                           }

                        });


                        $excel->sheet('Porcentaje_Otros_Doc', function($sheet) use($cantidadAprobados,$cantidadRechazados,$cantidadVencidos,$cantidadPorRevision,$totalTrabajadoresEmpresa,$fecha1V,$fecha2V) {
                            $nombreHoja = 'Porcentaje_Otros_Doc';
                            $TotalGeneral=$cantidadAprobados+$cantidadPorRevision+$cantidadRechazados+$cantidadVencidos;
                            $sheet->row(2, array('Empresa', 'Todas'));
                            $sheet->row(3, array('Fecha Desde', $fecha1V,'Fecha Hasta',$fecha2V));
                            
                            $sheet->row(4, array('Estado', 'Total'));
                            $sheet->row(5, array('Aprobados', $cantidadAprobados));
                            $sheet->row(6, array('Por revisar', $cantidadPorRevision));
                            $sheet->row(7, array('Rechazado', $cantidadRechazados));
                            $sheet->row(8, array('Vencido', $cantidadVencidos));
                            $sheet->row(9, array('Total general', $TotalGeneral));

                            if($TotalGeneral != 0){
                                $sheet->row(12, array('Estado', 'Porcentaje de Acreditación'));
                                $sheet->row(13, array('Aprobados', round(($cantidadAprobados*100)/$TotalGeneral,0)));
                                $sheet->row(14, array('Por revisar', round(($cantidadPorRevision*100)/$TotalGeneral,0)));
                                $sheet->row(15, array('Rechazado', round(($cantidadRechazados*100)/$TotalGeneral,0)));
                                $sheet->row(16, array('Vencido', round(($cantidadVencidos*100)/$TotalGeneral,0)));
                                 $labels2 = [
                                    new  \PHPExcel_Chart_DataSeriesValues('String', $nombreHoja. '!$A$12', null, 1), // 2011
                                ];

                                $categories2 = [
                                    new \PHPExcel_Chart_DataSeriesValues('String', $nombreHoja. '!$A$13:$A$16', null, 4), // Q1 to Q4
                                ];

                                 $values2 = [
                                    new \PHPExcel_Chart_DataSeriesValues('Number', $nombreHoja. '!$B$13:$B$16', null, 4),
                                ];

                        

                                   $series2 = new \PHPExcel_Chart_DataSeries(
                                        \PHPExcel_Chart_DataSeries::TYPE_PIECHART,       // plotType
                                        null,       // plotType
                                        range(0, count($values2)-1),           // plotOrder
                                        $labels2,                              // plotLabel
                                        $categories2,                               // plotCategory
                                        $values2                               // plotValues
                                    );

                                //  Set up a layout object for the Pie chart
                                $layout2 = new \PHPExcel_Chart_Layout();
                                $layout2->setShowPercent(TRUE);

                                //  Set the series in the plot area
                                $plotarea2 = new \PHPExcel_Chart_PlotArea($layout2, array($series2));
                                //  Set the chart legend
                                $legend2 = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                               
                                $title2 = new \PHPExcel_Chart_Title('Porcentaje Otros Documentos');


                                //  Create the chart
                                $chart3 = new \PHPExcel_Chart(
                                    'Porcentaje Otros Documentos',       // name
                                    $title2,        // title
                                    $legend2,       // legend
                                    $plotarea2,     // plotArea
                                    true,           // plotVisibleOnly
                                    0,              // displayBlanksAs
                                    NULL,           // xAxisLabel
                                    NULL            // yAxisLabel       - Pie charts don't have a Y-Axis
                                );

                                //  Set the position where the chart should appear in the worksheet
                                $chart3->setTopLeftPosition('F4');
                                $chart3->setBottomRightPosition('M16');
                                $sheet->addChart($chart3); 
                            }else{
                                $sheet->row(12, array('Estado', 'Porcentaje de Acreditación'));
                                $sheet->row(13, array('Aprobados', 0));
                                $sheet->row(14, array('Por revisar',0 ));
                                $sheet->row(15, array('Rechazado',0 ));
                                $sheet->row(16, array('Vencido', 0));
                            }

                        });

                        if(!empty($listaDatosReporteRechazados)){

                            $excel->sheet('Otros Documentos Rechazado', function($sheet) use($listaDatosReporteRechazados,$fecha1V,$fecha2V) {
                                $sheet->cells('A5:Q5', function($cells) {
                                  $cells->setBorder('thin','thin','thin','thin');
                                });
                                $sheet->row(2, array('Empresa', 'Todas'));
                                $sheet->row(3, array('Fecha Desde', $fecha1V,'Fecha Hasta',$fecha2V));
                                
                                $sheet->row(5, array('folio','Empresa Principal','RUT Principal','Empresa Contratista','RUT Contratista','Empresa Sub Contratista','RUT Sub Contratista','Nombre','Apellido Paterno','Apellido Materno','RUT Trabajador','Cargo','Documento','Fecha de creación','Observación','Estado','Fecha de vencimiento'));
                                foreach ($listaDatosReporteRechazados as $datosReporte) {
                                    $sheet->appendRow($datosReporte);
                                }

                            });

                        }
                    }

                    if(!empty($listaDatosReporteGlobal)){
                        $excel->sheet('Documentos Globales', function($sheet) use($listaDatosReporteGlobal,$fecha1V,$fecha2V) {
                            $sheet->cells('A5:Q5', function($cells) {
                                  $cells->setBorder('thin','thin','thin','thin');
                                });
                            $sheet->row(2, array('Empresa', 'Todas'));
                            $sheet->row(3, array('Fecha Desde', $fecha1V,'Fecha Hasta',$fecha2V));
                            
                            $sheet->row(5, array('folio','Empresa Principal','RUT Principal','Empresa Contratista','RUT Contratista','Empresa Sub Contratista','RUT Sub Contratista','Nombre','Apellido Paterno','Apellido Materno','RUT Trabajador','Cargo','Documento','Fecha de creación','Observación','Estado','Fecha de vencimiento'));
                            foreach ($listaDatosReporteGlobal as $datosReporte) {
                                $sheet->appendRow($datosReporte);
                            }
                            

                        });


                        $excel->sheet('Porcentaje_Doc_Global', function($sheet) use($cantidadAprobadosGlobal,$cantidadRechazadosGlobal,$cantidadVencidosGlobal,$cantidadPorRevisionGlobal,$fecha1V,$fecha2V) {
                            $nombreHoja = 'Porcentaje_Doc_Global';
                            $TotalGeneralGlobal=$cantidadAprobadosGlobal+$cantidadPorRevisionGlobal+$cantidadRechazadosGlobal+$cantidadVencidosGlobal;
                            $sheet->row(2, array('Empresa', 'Todas'));
                            $sheet->row(3, array('Fecha Desde', $fecha1V,'Fecha Hasta',$fecha2V));
                            
                            $sheet->row(4, array('Estado', 'Total'));
                            $sheet->row(5, array('Aprobados', $cantidadAprobadosGlobal));
                            $sheet->row(6, array('Por revisar', $cantidadPorRevisionGlobal));
                            $sheet->row(7, array('Rechazado', $cantidadRechazadosGlobal));
                            $sheet->row(8, array('Vencido', $cantidadVencidosGlobal));
                            $sheet->row(9, array('Total general', $TotalGeneralGlobal));

                            if($TotalGeneralGlobal!= 0){
                                $sheet->row(12, array('Estado', 'Porcentaje de Acreditación'));
                                $sheet->row(13, array('Aprobados', round(($cantidadAprobadosGlobal*100)/$TotalGeneralGlobal,0)));
                                $sheet->row(14, array('Por revisar', round(($cantidadPorRevisionGlobal*100)/$TotalGeneralGlobal,0)));
                                $sheet->row(15, array('Rechazado', round(($cantidadRechazadosGlobal*100)/$TotalGeneralGlobal,0)));
                                $sheet->row(16, array('Vencido', round(($cantidadVencidosGlobal*100)/$TotalGeneralGlobal,0)));

                                $labels3 = [
                                    new  \PHPExcel_Chart_DataSeriesValues('String', $nombreHoja. '!$A$12', null, 1), // 2011
                                ];

                                $categories3 = [
                                    new \PHPExcel_Chart_DataSeriesValues('String', $nombreHoja. '!$A$13:$A$16', null, 4), // Q1 to Q4
                                ];

                                 $values3 = [
                                    new \PHPExcel_Chart_DataSeriesValues('Number', $nombreHoja. '!$B$13:$B$16', null, 4),
                                ];

                        

                                   $series3 = new \PHPExcel_Chart_DataSeries(
                                        \PHPExcel_Chart_DataSeries::TYPE_PIECHART,       // plotType
                                        null,       // plotType
                                        range(0, count($values3)-1),           // plotOrder
                                        $labels3,                              // plotLabel
                                        $categories3,                               // plotCategory
                                        $values3                               // plotValues
                                    );

                                //  Set up a layout object for the Pie chart
                                $layout3 = new \PHPExcel_Chart_Layout();
                                $layout3->setShowPercent(TRUE);

                                //  Set the series in the plot area
                                $plotarea3 = new \PHPExcel_Chart_PlotArea($layout3, array($series3));
                                //  Set the chart legend
                                $legend3 = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                               
                                $title2 = new \PHPExcel_Chart_Title('Porcentaje Documentos Globales');


                                //  Create the chart
                                $chart4 = new \PHPExcel_Chart(
                                    'Porcentaje Documentos Globales',       // name
                                    $title3,        // title
                                    $legend3,       // legend
                                    $plotarea3,     // plotArea
                                    true,           // plotVisibleOnly
                                    0,              // displayBlanksAs
                                    NULL,           // xAxisLabel
                                    NULL            // yAxisLabel       - Pie charts don't have a Y-Axis
                                );

                                //  Set the position where the chart should appear in the worksheet
                                $chart4->setTopLeftPosition('F4');
                                $chart4->setBottomRightPosition('M16');
                                $sheet->addChart($chart4); 

                            }else{
                                $sheet->row(12, array('Estado', 'Porcentaje de Acreditación'));
                                $sheet->row(13, array('Aprobados', 0));
                                $sheet->row(14, array('Por revisar', 0));
                                $sheet->row(15, array('Rechazado', 0));
                                $sheet->row(16, array('Vencido', 0));
                            }   


                        });

                        if(!empty($listaDatosReporteRechazadosGlobales)){

                            $excel->sheet('Documentos Globales Rechazados', function($sheet) use($listaDatosReporteRechazadosGlobales,$fecha1V,$fecha2V) {
                                $sheet->cells('A5:Q5', function($cells) {
                                  $cells->setBorder('thin','thin','thin','thin');
                                });
                                $sheet->row(2, array('Empresa', 'Todas'));
                                $sheet->row(3, array('Fecha Desde', $fecha1V,'Fecha Hasta',$fecha2V));
                                
                                $sheet->row(5, array('folio','Empresa Principal','RUT Principal','Empresa Contratista','RUT Contratista','Empresa Sub Contratista','RUT Sub Contratista','Nombre','Apellido Paterno','Apellido Materno','RUT Trabajador','Cargo','Documento','Fecha de creación','Observación','Estado','Fecha de vencimiento'));
                                foreach ($listaDatosReporteRechazadosGlobales as $datosReporte) {
                                    $sheet->appendRow($datosReporte);
                                }

                            });

                        }
                    }
            })->export('xlsx');
        }else{

            if(!empty($empresasCon)){

                $EmpresasP = FolioSso::whereIn('sso_mcomp_rut',$empresaPrin)->whereIn('sso_comp_rut',$empresasCon)->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['id','sso_mcomp_name']);
                foreach ($EmpresasP as $folio) {

                    $folios[] = $folio->id;
                    $empresa[] = $folio->sso_mcomp_name;

                }

            }else{

                $EmpresasP = FolioSso::whereIn('sso_mcomp_rut',$empresaPrin)->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['id','sso_mcomp_name']);
                foreach ($EmpresasP as $folio) {

                    $folios[] = $folio->id;
                    $empresa[] = $folio->sso_mcomp_name;

                }
            }

            
            $empresas = array_unique($empresa);
            $empresasTexto = implode(",", $empresas);
           
           
            $documentos = EstadoDocumento::whereIn('upld_sso_id',$folios)->where('upld_status', '1')
            ->whereBetween('upld_crtdat', [$fechasDesde,$fechasHasta])
            ->join('xt_ssov2_header_worker', 'xt_ssov2_header_uploads.upld_workerid', '=', 'xt_ssov2_header_worker.id')
            ->where('xt_ssov2_header_worker.worker_status','1')
            ->where('xt_ssov2_header_worker.desvinculado','0')
            ->join('xt_ssov2_header', 'xt_ssov2_header_uploads.upld_sso_id', '=', 'xt_ssov2_header.id')
            ->where('xt_ssov2_header.sso_status','1')
            ->join('xt_ssov2_doctypes', 'xt_ssov2_header_uploads.upld_docid', '=', 'xt_ssov2_doctypes.id')
            ->select('xt_ssov2_header_uploads.*', 'xt_ssov2_header_worker.*','xt_ssov2_header.sso_mcomp_rut','xt_ssov2_header.sso_mcomp_dv','xt_ssov2_header.sso_mcomp_name','xt_ssov2_header.sso_comp_name','xt_ssov2_header.sso_comp_dv','xt_ssov2_header.sso_comp_rut','xt_ssov2_header.sso_subcomp_active','xt_ssov2_header.sso_subcomp_rut','xt_ssov2_header.sso_subcomp_dv','xt_ssov2_header.sso_subcomp_name','xt_ssov2_doctypes.doc_name')
            ->get()->toArray();


            $documentosGlobales = EstadoDocumento::whereIn('upld_sso_id',$folios)->where('upld_status', '1')->where('upld_workerid', '0')
            ->whereBetween('upld_crtdat', [$fechasDesde,$fechasHasta])
            ->join('xt_ssov2_header', 'xt_ssov2_header_uploads.upld_sso_id', '=', 'xt_ssov2_header.id')
            ->where('xt_ssov2_header.sso_status','1')
            ->join('xt_ssov2_doctypes', 'xt_ssov2_header_uploads.upld_docid', '=', 'xt_ssov2_doctypes.id')
            ->select('xt_ssov2_header_uploads.*','xt_ssov2_header.sso_mcomp_rut','xt_ssov2_header.sso_mcomp_dv','xt_ssov2_header.sso_mcomp_name','xt_ssov2_header.sso_comp_name','xt_ssov2_header.sso_comp_dv','xt_ssov2_header.sso_comp_rut','xt_ssov2_header.sso_subcomp_active','xt_ssov2_header.sso_subcomp_rut','xt_ssov2_header.sso_subcomp_dv','xt_ssov2_header.sso_subcomp_name','xt_ssov2_doctypes.doc_name')
            ->get()->toArray();
                
                /// ingresados //
                
                $cantidadAprobados = 0;
                $cantidadRechazados = 0;
                $cantidadVencidos = 0;
                $cantidadPorRevision = 0;
               
                $cantidadAprobadosCovid = 0;
                $cantidadRechazadosCovid = 0;
                $cantidadVencidosCovid = 0;
                $cantidadPorRevisionCovid = 0;
                $totalTrabajadoresEmpresaCovid = 0;

                $cantidadAprobadosGlobal = 0;
                $cantidadRechazadosGlobal = 0;
                $cantidadVencidosGlobal = 0;
                $cantidadPorRevisionGlobal = 0;
                $totalTrabajadoresEmpresa = 0;
                
                $estadoDocumento ="";

                $fecha_actual = strtotime(date("d-m-Y H:i:00",time())); 
                foreach ($documentos as $value) {
                    
                    ////////////////// fecha para determinar si esta expirado /////
                    $fecha2 = $value["upld_vence_date"];
                    $fechaUpdate = $value["upld_upddat"];
                
                    $totalTrabajadoresEmpresa += 1;
                    if($value["upld_docid"] == 660 or $value["upld_docid"] == 661){
                       
                        if($value["upld_rechazado"] == 1) {
                            $cantidadRechazadosCovid +=1; 
                            $estadoDocumento ="Rechazado";
                            $datosReporteRechazadosCovid["folio"] = $value["upld_sso_id"];
                            $datosReporteRechazadosCovid["principal"] = ucwords(mb_strtolower($value["sso_mcomp_name"],'UTF-8'));
                            $datosReporteRechazadosCovid["rutPrincipal"] = $value["sso_mcomp_rut"]."-".$value["sso_mcomp_dv"];
                            $datosReporteRechazadosCovid["Contratista"] = ucwords(mb_strtolower($value["sso_comp_name"],'UTF-8'));
                            $datosReporteRechazadosCovid["rutContratista"] = $value["sso_comp_rut"]."-".$value["sso_comp_dv"];
                            if($value["sso_subcomp_active"] == 1){
                                $datosReporteRechazadosCovid["subContratista"] = ucwords(mb_strtolower($value["sso_subcomp_name"],'UTF-8'));
                                $datosReporteRechazadosCovid["subrutContratista"] = $value["sso_subcomp_rut"]."-".$value["sso_subcomp_dv"];
                            }else{
                                $datosReporteRechazadosCovid["subContratista"] = "";
                                $datosReporteRechazadosCovid["subrutContratista"] = "";
                            }
                            $datosReporteRechazadosCovid["nombreTrabajador"] = ucwords(mb_strtolower($value["worker_name1"],'UTF-8'));
                            $datosReporteRechazadosCovid["apellido1Trabajador"] = ucwords(mb_strtolower($value["worker_name2"],'UTF-8'));
                            $datosReporteRechazadosCovid["apellido2Trabajador"] = ucwords(mb_strtolower($value["worker_name3"],'UTF-8'));
                            $datosReporteRechazadosCovid["rutTrabajador"] = strtoupper($value["worker_rut"]);
                            $datosReporteRechazadosCovid["cargoTrabajador"] = ucwords(mb_strtolower($value["worker_syscargoname"],'UTF-8'));
                            $datosReporteRechazadosCovid["documentoTrabajador"] = ucwords(mb_strtolower($value["doc_name"],'UTF-8'));
                            $datosReporteRechazadosCovid["fechaCreacionDoc"] = date('d/m/Y', $value['upld_crtdat']);
                            $datosReporteRechazadosCovid["observacionDoc"] = ucwords(mb_strtolower($value['upld_comments'],'UTF-8'));
                            $datosReporteRechazadosCovid["estadoDocumento"] = ucwords(mb_strtolower($estadoDocumento,'UTF-8'));
                            $datosReporteRechazadosCovid["fechaVenceDoc"] = date('d/m/Y', $value['upld_vence_date']);
                            if(!empty($datosReporteRechazadosCovid)){
                            $listaDatosReporteRechazadosCovid[] =$datosReporteRechazadosCovid;
                            }


                        }elseif (($value["upld_docaprob"] == 1 or $fecha_actual <= $fechaUpdate) and $fecha_actual < $fecha2){
                                $cantidadAprobadosCovid +=1; 
                                $estadoDocumento ="Aprobado";
                        }elseif (($value["upld_venced"]== 1  or $fecha_actual > $fecha2)and $value["upld_rechazado"] == 0 and $fecha2!= 0){
                                $cantidadVencidosCovid +=1; 
                                $estadoDocumento ="Vencido";
                        }
                        elseif ($value["upld_docaprob"] == 0 and $value["upld_docaprob_uid"] == 0){
                                $cantidadPorRevisionCovid +=1; 
                                $estadoDocumento ="Por Revisión";
                        }

                    
                        $datosReporteCovid["folio"] = $value["upld_sso_id"];
                        $datosReporteCovid["principal"] = ucwords(mb_strtolower($value["sso_mcomp_name"],'UTF-8'));
                        $datosReporteCovid["rutPrincipal"] = $value["sso_mcomp_rut"]."-".$value["sso_mcomp_dv"];
                        $datosReporteCovid["Contratista"] = ucwords(mb_strtolower($value["sso_comp_name"],'UTF-8'));
                        $datosReporteCovid["rutContratista"] = $value["sso_comp_rut"]."-".$value["sso_comp_dv"];
                        if($value["sso_subcomp_active"] == 1){
                            $datosReporteCovid["subContratista"] = ucwords(mb_strtolower($value["sso_subcomp_name"],'UTF-8'));
                            $datosReporteCovid["subrutContratista"] = $value["sso_subcomp_rut"]."-".$value["sso_subcomp_dv"];
                        }else{
                            $datosReporteCovid["subContratista"] = "";
                            $datosReporteCovid["subrutContratista"] = "";
                        }
                        $datosReporteCovid["nombreTrabajador"] = ucwords(mb_strtolower($value["worker_name1"],'UTF-8'));
                        $datosReporteCovid["apellido1Trabajador"] = ucwords(mb_strtolower($value["worker_name2"],'UTF-8'));
                        $datosReporteCovid["apellido2Trabajador"] = ucwords(mb_strtolower($value["worker_name3"],'UTF-8'));
                        $datosReporteCovid["rutTrabajador"] = strtoupper($value["worker_rut"]);
                        $datosReporteCovid["cargoTrabajador"] = ucwords(mb_strtolower($value["worker_syscargoname"],'UTF-8'));
                        $datosReporteCovid["documentoTrabajador"] = ucwords(mb_strtolower($value["doc_name"],'UTF-8'));
                        $datosReporteCovid["fechaCreacionDoc"] = date('d/m/Y', $value['upld_crtdat']);
                        $datosReporteCovid["observacionDoc"] = ucwords(mb_strtolower($value['upld_comments'],'UTF-8'));
                        $datosReporteCovid["estadoDocumento"] = ucwords(mb_strtolower($estadoDocumento,'UTF-8'));
                        $datosReporteCovid["fechaVenceDoc"] = date('d/m/Y', $value['upld_vence_date']);
                        if(!empty($datosReporteCovid)){
                            $listaDatosReporteCovid[] =$datosReporteCovid;
                        }
                    }else{
                        if ($value["upld_rechazado"] == 1) {
                            $cantidadRechazados +=1;
                            $estadoDocumento ="Rechazado";
                            $datosReporteRechazados["folio"] = $value["upld_sso_id"];
                            $datosReporteRechazados["principal"] = ucwords(mb_strtolower($value["sso_mcomp_name"],'UTF-8'));
                            $datosReporteRechazados["rutPrincipal"] = $value["sso_mcomp_rut"]."-".$value["sso_mcomp_dv"];
                            $datosReporteRechazados["Contratista"] = ucwords(mb_strtolower($value["sso_comp_name"],'UTF-8'));
                            $datosReporteRechazados["rutContratista"] = $value["sso_comp_rut"]."-".$value["sso_comp_dv"];
                            if($value["sso_subcomp_active"] == 1){
                                $datosReporteRechazados["subContratista"] = ucwords(mb_strtolower($value["sso_subcomp_name"],'UTF-8'));
                                $datosReporteRechazados["subrutContratista"] = $value["sso_subcomp_rut"]."-".$value["sso_subcomp_dv"];
                            }else{
                                $datosReporteRechazados["subContratista"] = "";
                                $datosReporteRechazados["subrutContratista"] = "";
                            }
                            $datosReporteRechazados["nombreTrabajador"] = ucwords(mb_strtolower($value["worker_name1"],'UTF-8'));
                            $datosReporteRechazados["apellido1Trabajador"] = ucwords(mb_strtolower($value["worker_name2"],'UTF-8'));
                            $datosReporteRechazados["apellido2Trabajador"] = ucwords(mb_strtolower($value["worker_name3"],'UTF-8'));
                            $datosReporteRechazados["rutTrabajador"] = strtoupper($value["worker_rut"]);
                            $datosReporteRechazados["cargoTrabajador"] = ucwords(mb_strtolower($value["worker_syscargoname"],'UTF-8'));
                            $datosReporteRechazados["documentoTrabajador"] = ucwords(mb_strtolower($value["doc_name"],'UTF-8'));
                            $datosReporteRechazados["fechaCreacionDoc"] = date('d/m/Y', $value['upld_crtdat']);
                            $datosReporteRechazados["observacionDoc"] = ucwords(mb_strtolower($value['upld_comments'],'UTF-8'));
                            $datosReporteRechazados["estadoDocumento"] = ucwords(mb_strtolower($estadoDocumento,'UTF-8'));
                            $datosReporteRechazados["fechaVenceDoc"] = date('d/m/Y', $value['upld_vence_date']);
                            if(!empty($datosReporteRechazados)){
                                $listaDatosReporteRechazados[] =$datosReporteRechazados;
                            }

                        }elseif (($value["upld_docaprob"] == 1 or $fecha_actual <= $fechaUpdate) and $fecha_actual < $fecha2){
                                $cantidadAprobados +=1; 
                                $estadoDocumento ="Aprobado";
                        }elseif (($value["upld_venced"]== 1  or $fecha_actual > $fecha2)and $value["upld_rechazado"] == 0 and $fecha2!= 0){
                                $cantidadVencidos +=1; 
                                $estadoDocumento ="Vencido";
                        }
                        elseif ($value["upld_docaprob"] == 0 and $value["upld_docaprob_uid"] == 0){
                                $cantidadPorRevision +=1; 
                                $estadoDocumento ="Por Revisión";
                        }

                    
                        $datosReporte["folio"] = $value["upld_sso_id"];
                        $datosReporte["principal"] = ucwords(mb_strtolower($value["sso_mcomp_name"],'UTF-8'));
                        $datosReporte["rutPrincipal"] = $value["sso_mcomp_rut"]."-".$value["sso_mcomp_dv"];
                        $datosReporte["Contratista"] = ucwords(mb_strtolower($value["sso_comp_name"],'UTF-8'));
                        $datosReporte["rutContratista"] = $value["sso_comp_rut"]."-".$value["sso_comp_dv"];
                        if($value["sso_subcomp_active"] == 1){
                            $datosReporte["subContratista"] = ucwords(mb_strtolower($value["sso_subcomp_name"],'UTF-8'));
                            $datosReporte["subrutContratista"] = $value["sso_subcomp_rut"]."-".$value["sso_subcomp_dv"];
                        }else{
                            $datosReporte["subContratista"] = "";
                            $datosReporte["subrutContratista"] = "";
                        }
                        $datosReporte["nombreTrabajador"] = ucwords(mb_strtolower($value["worker_name1"],'UTF-8'));
                        $datosReporte["apellido1Trabajador"] = ucwords(mb_strtolower($value["worker_name2"],'UTF-8'));
                        $datosReporte["apellido2Trabajador"] = ucwords(mb_strtolower($value["worker_name3"],'UTF-8'));
                        $datosReporte["rutTrabajador"] = strtoupper($value["worker_rut"]);
                        $datosReporte["cargoTrabajador"] = ucwords(mb_strtolower($value["worker_syscargoname"],'UTF-8'));
                        $datosReporte["documentoTrabajador"] = ucwords(mb_strtolower($value["doc_name"],'UTF-8'));
                        $datosReporte["fechaCreacionDoc"] = date('d/m/Y', $value['upld_crtdat']);
                        $datosReporte["observacionDoc"] = ucwords(mb_strtolower($value['upld_comments'],'UTF-8'));
                        $datosReporte["estadoDocumento"] = ucwords(mb_strtolower($estadoDocumento,'UTF-8'));
                        $datosReporte["fechaVenceDoc"] = date('d/m/Y', $value['upld_vence_date']);
                        if(!empty($datosReporte)){
                            $listaDatosReporte[] =$datosReporte;
                        }
                    }
                        
                }

                foreach ($documentosGlobales as $value) {

                    $fecha2 = $value["upld_vence_date"];
                    $fechaUpdate = $value["upld_upddat"];

                    if ($value["upld_rechazado"] == 1) {
                        $cantidadRechazadosGlobal +=1; 
                        $estadoDocumento ="Rechazado";
                        $datosReporteRechazadosGlobales["folio"] = $value["upld_sso_id"];
                        $datosReporteRechazadosGlobales["principal"] = ucwords(mb_strtolower($value["sso_mcomp_name"],'UTF-8'));
                        $datosReporteRechazadosGlobales["rutPrincipal"] = $value["sso_mcomp_rut"]."-".$value["sso_mcomp_dv"];
                        $datosReporteRechazadosGlobales["Contratista"] = ucwords(mb_strtolower($value["sso_comp_name"],'UTF-8'));
                        $datosReporteRechazadosGlobales["rutContratista"] = $value["sso_comp_rut"]."-".$value["sso_comp_dv"];
                        if($value["sso_subcomp_active"] == 1){
                            $datosReporteRechazadosGlobales["subContratista"] = ucwords(mb_strtolower($value["sso_subcomp_name"],'UTF-8'));
                            $datosReporteRechazadosGlobales["subrutContratista"] = $value["sso_subcomp_rut"]."-".$value["sso_subcomp_dv"];
                        }else{
                            $datosReporteRechazadosGlobales["subContratista"] = "";
                            $datosReporteRechazadosGlobales["subrutContratista"] = "";
                        }
                        $datosReporteRechazadosGlobales["nombreTrabajador"] = "";
                        $datosReporteRechazadosGlobales["apellido1Trabajador"] = "";
                        $datosReporteRechazadosGlobales["apellido2Trabajador"] = "";
                        $datosReporteRechazadosGlobales["rutTrabajador"] = "";
                        $datosReporteRechazadosGlobales["cargoTrabajador"] = "";
                        $datosReporteRechazadosGlobales["documentoTrabajador"] = ucwords(mb_strtolower($value["doc_name"],'UTF-8'));
                        $datosReporteRechazadosGlobales["fechaCreacionDoc"] = date('d/m/Y', $value['upld_crtdat']);
                        $datosReporteRechazadosGlobales["observacionDoc"] = ucwords(mb_strtolower($value['upld_comments'],'UTF-8'));
                        $datosReporteRechazadosGlobales["estadoDocumento"] = ucwords(mb_strtolower($estadoDocumento,'UTF-8'));
                        $datosReporteRechazadosGlobales["fechaVenceDoc"] = date('d/m/Y', $value['upld_vence_date']);
                        if(!empty($datosReporteRechazadosGlobales)){
                            $listaDatosReporteRechazadosGlobales[] =$datosReporteRechazadosGlobales;
                        }
                    }elseif (($value["upld_docaprob"] == 1 or $fecha_actual <= $fechaUpdate) and $fecha_actual < $fecha2){
                        $cantidadAprobadosGlobal +=1; 
                        $estadoDocumento ="Aprobado";
                    }elseif (($value["upld_venced"]== 1  or $fecha_actual > $fecha2)and $value["upld_rechazado"] == 0 and $fecha2!= 0){
                        $cantidadVencidosGlobal +=1; 
                        $estadoDocumento ="Vencido";
                    }
                    elseif ($value["upld_docaprob"] == 0 and $value["upld_docaprob_uid"] == 0){
                        $cantidadPorRevisionGlobal +=1; 
                        $estadoDocumento ="Por Revisión";
                    }

                    $datosReporteGlobal["folio"] =  $value["upld_sso_id"];
                    $datosReporteGlobal["principal"] = ucwords(mb_strtolower($value["sso_mcomp_name"],'UTF-8'));
                    $datosReporteGlobal["rutPrincipal"] = $value["sso_mcomp_rut"]."-".$value["sso_mcomp_dv"];
                    $datosReporteGlobal["Contratista"] = ucwords(mb_strtolower($value["sso_comp_name"],'UTF-8'));
                    $datosReporteGlobal["rutContratista"] = $value["sso_comp_rut"]."-".$value["sso_comp_dv"];
                    if($value["sso_subcomp_active"] == 1){
                        $datosReporteGlobal["Contratista"] = ucwords(mb_strtolower($value["sso_subcomp_name"],'UTF-8'));
                        $datosReporteGlobal["rutContratista"] = $value["sso_subcomp_rut"]."-".$value["sso_subcomp_dv"];
                    }else{
                        $datosReporteGlobal["Contratista"] = "";
                        $datosReporteGlobal["rutContratista"] = "";
                    }
                    $datosReporteGlobal["nombreTrabajador"] = "";
                    $datosReporteGlobal["apellido1Trabajador"] = "";
                    $datosReporteGlobal["apellido2Trabajador"] = "";
                    $datosReporteGlobal["rutTrabajador"] = "";
                    $datosReporteGlobal["cargoTrabajador"] =  "";
                    $datosReporteGlobal["documentoTrabajador"] = ucwords(mb_strtolower($value["doc_name"],'UTF-8'));
                    $datosReporteGlobal["fechaCreacionDoc"] = date('d/m/Y', $value['upld_crtdat']);
                    $datosReporteGlobal["observacionDoc"] = ucwords(mb_strtolower($value['upld_comments'],'UTF-8'));
                    $datosReporteGlobal["estadoDocumento"] = ucwords(mb_strtolower($estadoDocumento,'UTF-8'));
                    $datosReporteGlobal["fechaVenceDoc"] = date('d/m/Y', $value['upld_vence_date']);
                    if(!empty($datosReporteGlobal)){
                        $listaDatosReporteGlobal[] =$datosReporteGlobal;
                    }
                    
                }

            
            if(empty($listaDatosReporte)){
            $listaDatosReporte = ['','','','','','','','','','','','','','','','',''];
            } 

            if(empty($listaDatosReporteCovid)){
            $listaDatosReporteCovid = ['','','','','','','','','','','','','','','','',''];
            } 

            if(empty($listaDatosReporteGlobal)){
            $listaDatosReporteGlobal = ['','','','','','','','','','','','','','','','',''];
            }  

            if(empty($listaDatosReporteRechazadosCovid)){
            $listaDatosReporteRechazadosCovid = ['','','','','','','','','','','','','','','','',''];
            }
            if(empty($listaDatosReporteRechazados)){
            $listaDatosReporteRechazados = ['','','','','','','','','','','','','','','','',''];
            } 
             if(empty($listaDatosReporteRechazadosGlobales)){
            $listaDatosReporteRechazadosGlobales = ['','','','','','','','','','','','','','','','',''];
            }   




            Excel::create('Reporte Cumplimiento Aqua', function($excel) use($listaDatosReporteCovid,$cantidadAprobados,$cantidadRechazados,$cantidadVencidos,$cantidadPorRevision,$totalTrabajadoresEmpresa,$listaDatosReporte,$cantidadAprobadosCovid,$cantidadRechazadosCovid,$cantidadVencidosCovid,$cantidadPorRevisionCovid,$listaDatosReporteGlobal,$cantidadRechazadosGlobal,$cantidadAprobadosGlobal,$cantidadVencidosGlobal,$cantidadPorRevisionGlobal,$listaDatosReporteRechazadosCovid,$listaDatosReporteRechazados,$listaDatosReporteRechazadosGlobales,$empresasTexto,$fecha1V,$fecha2V) {
                
                    if($listaDatosReporteCovid!= 0){

                        
                        $excel->sheet('Documentos Covid', function($sheet) use($listaDatosReporteCovid,$empresasTexto,$fecha1V,$fecha2V) {
                            $sheet->cells('A5:Q5', function($cells) {
                                  $cells->setBorder('thin','thin','thin','thin');
                                });
                            $sheet->row(2, array('Empresas',ucwords(mb_strtolower($empresasTexto,'UTF-8'))));
                            $sheet->row(3, array('Fecha Desde', $fecha1V,'Fecha Hasta',$fecha2V));
                              
                            $sheet->row(5, array('folio','Empresa Principal','RUT Principal','Empresa Contratista','RUT Contratista','Empresa Sub Contratista','RUT Sub Contratista','Nombre','Apellido Paterno','Apellido Materno','RUT Trabajador','Cargo','Documento','Fecha de creación','Observación','Estado','Fecha de vencimiento'));
                                foreach ($listaDatosReporteCovid as $datosReporte) {
                                 $sheet->appendRow($datosReporte);
                                }

                        });

                        $excel->sheet('Porcentaje_Covid', function($sheet) use($cantidadAprobadosCovid,$cantidadRechazadosCovid,$cantidadVencidosCovid,$cantidadPorRevisionCovid,$empresasTexto,$fecha1V,$fecha2V) {

                            $TotalGeneralCovid=$cantidadAprobadosCovid+$cantidadPorRevisionCovid+$cantidadRechazadosCovid+$cantidadVencidosCovid;
                            if($TotalGeneralCovid != 0){
                                $sheet->row(2, array('Empresa',ucwords(mb_strtolower($empresasTexto,'UTF-8'))));
                                $sheet->row(3, array('Fecha Desde', $fecha1V,'Fecha Hasta',$fecha2V));
                                $sheet->row(4, array('Estado', 'Total'));
                                $sheet->row(5, array('Aprobados', $cantidadAprobadosCovid));
                                $sheet->row(6, array('Por revisar', $cantidadPorRevisionCovid));
                                $sheet->row(7, array('Rechazado', $cantidadRechazadosCovid));
                                $sheet->row(8, array('Vencido', $cantidadVencidosCovid));
                                $sheet->row(9, array('Total general', $TotalGeneralCovid));
                                $Aprobados = round(($cantidadAprobadosCovid*100)/$TotalGeneralCovid,0);
                                $Porrevisar = round(($cantidadPorRevisionCovid*100)/$TotalGeneralCovid,0);
                                $Rechazado = round(($cantidadRechazadosCovid*100)/$TotalGeneralCovid,0);
                                $Vencido = round(($cantidadVencidosCovid*100)/$TotalGeneralCovid,0);

                                $nombreHoja ='Porcentaje_Covid';
                                $data = [
                                    ['Estado','Porcentaje de Acreditación'],
                                    ['Aprobados',$Aprobados],
                                    ['Por Revisar',$Porrevisar],
                                    ['Rechazado',$Rechazado],
                                    ['Vencido',$Vencido],

                                ];

                                $sheet->fromArray($data,null, 'A12', false, false);
      
                                $labels1 = [
                                    new  \PHPExcel_Chart_DataSeriesValues('String', $nombreHoja. '!$A$12', null, 1), // 2011
                                ];

                                $categories1 = [
                                    new \PHPExcel_Chart_DataSeriesValues('String', $nombreHoja. '!$A$13:$A$16', null, 4), // Q1 to Q4
                                ];

                                 $values1 = [
                                    new \PHPExcel_Chart_DataSeriesValues('Number', $nombreHoja. '!$B$13:$B$16', null, 4),
                                ];

                        

                                   $series = new \PHPExcel_Chart_DataSeries(
                                        \PHPExcel_Chart_DataSeries::TYPE_PIECHART,       // plotType
                                        null,       // plotType
                                        range(0, count($values1)-1),           // plotOrder
                                        $labels1,                              // plotLabel
                                        $categories1,                               // plotCategory
                                        $values1                               // plotValues
                                    );

                                //  Set up a layout object for the Pie chart
                                $layout1 = new \PHPExcel_Chart_Layout();
                                $layout1->setShowPercent(TRUE);

                                //  Set the series in the plot area
                                $plotarea1 = new \PHPExcel_Chart_PlotArea($layout1, array($series));
                                //  Set the chart legend
                                $legend1 = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                               
                                $title1 = new \PHPExcel_Chart_Title('Porcentaje Documentos Covid');


                                //  Create the chart
                                $chart2 = new \PHPExcel_Chart(
                                    'Porcentaje Documentos Covid',       // name
                                    $title1,        // title
                                    $legend1,       // legend
                                    $plotarea1,     // plotArea
                                    true,           // plotVisibleOnly
                                    0,              // displayBlanksAs
                                    NULL,           // xAxisLabel
                                    NULL            // yAxisLabel       - Pie charts don't have a Y-Axis
                                );

                                //  Set the position where the chart should appear in the worksheet
                                $chart2->setTopLeftPosition('F4');
                                $chart2->setBottomRightPosition('M16');
                                $sheet->addChart($chart2); 

                            }else{
                                $sheet->row(12, array('Estado', 'Porcentaje de Acreditación'));
                                $sheet->row(13, array('Aprobados', 0));
                                $sheet->row(14, array('Por revisar', 0));
                                $sheet->row(15, array('Rechazado', 0));
                                $sheet->row(16, array('Vencido', 0));
                            }
                        });

                        if(!empty($listaDatosReporteRechazadosCovid)){

                            $excel->sheet('Documentos Rechazados Covid', function($sheet) use($listaDatosReporteRechazadosCovid,$empresasTexto,$fecha1V,$fecha2V) {
                                $sheet->cells('A5:Q5', function($cells) {
                                  $cells->setBorder('thin','thin','thin','thin');
                                });
                                $sheet->row(2, array('Empresas',ucwords(mb_strtolower($empresasTexto,'UTF-8'))));
                                $sheet->row(3, array('Fecha Desde', $fecha1V,'Fecha Hasta',$fecha2V));
                               
                                $sheet->row(5, array('folio','Empresa Principal','RUT Principal','Empresa Contratista','RUT Contratista','Empresa Sub Contratista','RUT Sub Contratista','Nombre','Apellido Paterno','Apellido Materno','RUT Trabajador','Cargo','Documento','Fecha de creación','Observación','Estado','Fecha de vencimiento'));
                                foreach ($listaDatosReporteRechazadosCovid as $datosReporte) {
                                 $sheet->appendRow($datosReporte);
                                }

                            });
                        }
                    }

                    if(!empty($listaDatosReporte)){
                        $excel->sheet('Otros Documentos', function($sheet) use($listaDatosReporte,$empresasTexto,$fecha1V,$fecha2V) {
                            $sheet->cells('A5:Q5', function($cells) {
                                  $cells->setBorder('thin','thin','thin','thin');
                                });
                            $sheet->row(2, array('Empresas',ucwords(mb_strtolower($empresasTexto,'UTF-8'))));
                            $sheet->row(3, array('Fecha Desde', $fecha1V,'Fecha Hasta',$fecha2V));
                    
                            $sheet->row(5, array('folio','Empresa Principal','RUT Principal','Empresa Contratista','RUT Contratista','Empresa Sub Contratista','RUT Sub Contratista','Nombre','Apellido Paterno','Apellido Materno','RUT Trabajador','Cargo','Documento','Fecha de creación','Observación','Estado','Fecha de vencimiento'));
                           foreach ($listaDatosReporte as $datosReporte) {
                             $sheet->appendRow($datosReporte);
                           }

                        });


                        $excel->sheet('Porcentaje_Otros_Doc', function($sheet) use($cantidadAprobados,$cantidadRechazados,$cantidadVencidos,$cantidadPorRevision,$totalTrabajadoresEmpresa,$empresasTexto,$fecha1V,$fecha2V) {

                            $TotalGeneral=$cantidadAprobados+$cantidadPorRevision+$cantidadRechazados+$cantidadVencidos;
                            $nombreHoja = 'Porcentaje_Otros_Doc';
                            $sheet->row(2, array('Empresa',ucwords(mb_strtolower($empresasTexto,'UTF-8'))));
                            $sheet->row(3, array('Fecha Desde', $fecha1V,'Fecha Hasta',$fecha2V));
                           
                            $sheet->row(4, array('Estado', 'Total'));
                            $sheet->row(5, array('Aprobados', $cantidadAprobados));
                            $sheet->row(6, array('Por revisar', $cantidadPorRevision));
                            $sheet->row(7, array('Rechazado', $cantidadRechazados));
                            $sheet->row(8, array('Vencido', $cantidadVencidos));
                            $sheet->row(9, array('Total general', $TotalGeneral));

                            if($TotalGeneral != 0){
                                $sheet->row(12, array('Estado', 'Porcentaje de Acreditación'));
                                $sheet->row(13, array('Aprobados', round(($cantidadAprobados*100)/$TotalGeneral,0)));
                                $sheet->row(14, array('Por revisar', round(($cantidadPorRevision*100)/$TotalGeneral,0)));
                                $sheet->row(15, array('Rechazado', round(($cantidadRechazados*100)/$TotalGeneral,0)));
                                $sheet->row(16, array('Vencido', round(($cantidadVencidos*100)/$TotalGeneral,0)));


                                $labels2 = [
                                    new  \PHPExcel_Chart_DataSeriesValues('String', $nombreHoja. '!$A$12', null, 1), // 2011
                                ];

                                $categories2 = [
                                    new \PHPExcel_Chart_DataSeriesValues('String', $nombreHoja. '!$A$13:$A$16', null, 4), // Q1 to Q4
                                ];

                                 $values2 = [
                                    new \PHPExcel_Chart_DataSeriesValues('Number', $nombreHoja. '!$B$13:$B$16', null, 4),
                                ];

                        

                                   $series2 = new \PHPExcel_Chart_DataSeries(
                                        \PHPExcel_Chart_DataSeries::TYPE_PIECHART,       // plotType
                                        null,       // plotType
                                        range(0, count($values2)-1),           // plotOrder
                                        $labels2,                              // plotLabel
                                        $categories2,                               // plotCategory
                                        $values2                               // plotValues
                                    );

                                //  Set up a layout object for the Pie chart
                                $layout2 = new \PHPExcel_Chart_Layout();
                                $layout2->setShowPercent(TRUE);

                                //  Set the series in the plot area
                                $plotarea2 = new \PHPExcel_Chart_PlotArea($layout2, array($series2));
                                //  Set the chart legend
                                $legend2 = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                               
                                $title2 = new \PHPExcel_Chart_Title('Porcentaje Otros Documentos');


                                //  Create the chart
                                $chart3 = new \PHPExcel_Chart(
                                    'Porcentaje Otros Documentos',       // name
                                    $title2,        // title
                                    $legend2,       // legend
                                    $plotarea2,     // plotArea
                                    true,           // plotVisibleOnly
                                    0,              // displayBlanksAs
                                    NULL,           // xAxisLabel
                                    NULL            // yAxisLabel       - Pie charts don't have a Y-Axis
                                );

                                //  Set the position where the chart should appear in the worksheet
                                $chart3->setTopLeftPosition('F4');
                                $chart3->setBottomRightPosition('M16');
                                $sheet->addChart($chart3); 

                            }else{
                                $sheet->row(12, array('Estado', 'Porcentaje de Acreditación'));
                                $sheet->row(13, array('Aprobados', 0));
                                $sheet->row(14, array('Por revisar',0 ));
                                $sheet->row(15, array('Rechazado',0 ));
                                $sheet->row(16, array('Vencido', 0));
                            }

                                
                        });

                        if(!empty($listaDatosReporteRechazados)){

                            $excel->sheet('Otros Documentos Rechazado', function($sheet) use($listaDatosReporteRechazados,$empresasTexto,$fecha1V,$fecha2V) {
                                $sheet->cells('A5:Q5', function($cells) {
                                  $cells->setBorder('thin','thin','thin','thin');
                                });
                                $sheet->row(2, array('Empresas',ucwords(mb_strtolower($empresasTexto,'UTF-8'))));
                                $sheet->row(3, array('Fecha Desde', $fecha1V,'Fecha Hasta',$fecha2V));
                                
                                $sheet->row(5, array('folio','Empresa Principal','RUT Principal','Empresa Contratista','RUT Contratista','Empresa Sub Contratista','RUT Sub Contratista','Nombre','Apellido Paterno','Apellido Materno','RUT Trabajador','Cargo','Documento','Fecha de creación','Observación','Estado','Fecha de vencimiento'));
                                foreach ($listaDatosReporteRechazados as $datosReporte) {
                                    $sheet->appendRow($datosReporte);
                                }

                            });

                        }
                    }

                    if(!empty($listaDatosReporteGlobal)){
                        $excel->sheet('Documentos Globales', function($sheet) use($listaDatosReporteGlobal,$empresasTexto,$fecha1V,$fecha2V) {
                            $sheet->cells('A5:Q5', function($cells) {
                                  $cells->setBorder('thin','thin','thin','thin');
                                });
                            $sheet->row(2, array('Empresas',ucwords(mb_strtolower($empresasTexto,'UTF-8'))));
                            $sheet->row(3, array('Fecha Desde', $fecha1V,'Fecha Hasta',$fecha2V));
                            
                            $sheet->row(5, array('folio','Empresa Principal','RUT Principal','Empresa Contratista','RUT Contratista','Empresa Sub Contratista','RUT Sub Contratista','Nombre','Apellido Paterno','Apellido Materno','RUT Trabajador','Cargo','Documento','Fecha de creación','Observación','Estado','Fecha de vencimiento'));
                            foreach ($listaDatosReporteGlobal as $datosReporte) {
                                $sheet->appendRow($datosReporte);
                            }
                            

                        });


                        $excel->sheet('Porcentaje_Doc_Global', function($sheet) use($cantidadAprobadosGlobal,$cantidadRechazadosGlobal,$cantidadVencidosGlobal,$cantidadPorRevisionGlobal,$empresasTexto,$fecha1V,$fecha2V) {
                            $TotalGeneralGlobal=$cantidadAprobadosGlobal+$cantidadPorRevisionGlobal+$cantidadRechazadosGlobal+$cantidadVencidosGlobal;
                            $sheet->row(2, array('Empresas',ucwords(mb_strtolower($empresasTexto,'UTF-8'))));
                            $sheet->row(3, array('Fecha Desde', $fecha1V,'Fecha Hasta',$fecha2V));
                            
                            $sheet->row(4, array('Estado', 'Total'));
                            $sheet->row(5, array('Aprobados', $cantidadAprobadosGlobal));
                            $sheet->row(6, array('Por revisar', $cantidadPorRevisionGlobal));
                            $sheet->row(7, array('Rechazado', $cantidadRechazadosGlobal));
                            $sheet->row(8, array('Vencido', $cantidadVencidosGlobal));
                            $sheet->row(9, array('Total general', $TotalGeneralGlobal));
                            $nombreHoja = 'Porcentaje_Doc_Global';
                            if($TotalGeneralGlobal!= 0){
                                $sheet->row(12, array('Estado', 'Porcentaje de Acreditación'));
                                $sheet->row(13, array('Aprobados', round(($cantidadAprobadosGlobal*100)/$TotalGeneralGlobal,0)));
                                $sheet->row(14, array('Por revisar', round(($cantidadPorRevisionGlobal*100)/$TotalGeneralGlobal,0)));
                                $sheet->row(15, array('Rechazado', round(($cantidadRechazadosGlobal*100)/$TotalGeneralGlobal,0)));
                                $sheet->row(16, array('Vencido', round(($cantidadVencidosGlobal*100)/$TotalGeneralGlobal,0)));

                                $labels3 = [
                                    new  \PHPExcel_Chart_DataSeriesValues('String', $nombreHoja. '!$A$12', null, 1), // 2011
                                ];

                                $categories3 = [
                                    new \PHPExcel_Chart_DataSeriesValues('String', $nombreHoja. '!$A$13:$A$16', null, 4), // Q1 to Q4
                                ];

                                 $values3 = [
                                    new \PHPExcel_Chart_DataSeriesValues('Number', $nombreHoja. '!$B$13:$B$16', null, 4),
                                ];

                        

                                   $series3 = new \PHPExcel_Chart_DataSeries(
                                        \PHPExcel_Chart_DataSeries::TYPE_PIECHART,       // plotType
                                        null,       // plotType
                                        range(0, count($values3)-1),           // plotOrder
                                        $labels3,                              // plotLabel
                                        $categories3,                               // plotCategory
                                        $values3                               // plotValues
                                    );

                                //  Set up a layout object for the Pie chart
                                $layout3 = new \PHPExcel_Chart_Layout();
                                $layout3->setShowPercent(TRUE);

                                //  Set the series in the plot area
                                $plotarea3 = new \PHPExcel_Chart_PlotArea($layout3, array($series3));
                                //  Set the chart legend
                                $legend3 = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                               
                                $title3 = new \PHPExcel_Chart_Title('Porcentaje Otros Documentos');


                                //  Create the chart
                                $chart4 = new \PHPExcel_Chart(
                                    'Porcentaje Otros Documentos',       // name
                                    $title3,        // title
                                    $legend3,       // legend
                                    $plotarea3,     // plotArea
                                    true,           // plotVisibleOnly
                                    0,              // displayBlanksAs
                                    NULL,           // xAxisLabel
                                    NULL            // yAxisLabel       - Pie charts don't have a Y-Axis
                                );

                                //  Set the position where the chart should appear in the worksheet
                                $chart4->setTopLeftPosition('F4');
                                $chart4->setBottomRightPosition('M16');
                                $sheet->addChart($chart4); 

                            }else{
                                $sheet->row(12, array('Estado', 'Porcentaje de Acreditación'));
                                $sheet->row(13, array('Aprobados', 0));
                                $sheet->row(14, array('Por revisar', 0));
                                $sheet->row(15, array('Rechazado', 0));
                                $sheet->row(16, array('Vencido', 0));
                            }                                
                        });

                        if(!empty($listaDatosReporteRechazadosGlobales)){

                            $excel->sheet('Documentos Globales Rechazados', function($sheet) use($listaDatosReporteRechazadosGlobales,$empresasTexto,$fecha1V,$fecha2V) {
                                 $sheet->cells('A5:Q5', function($cells) {
                                  $cells->setBorder('thin','thin','thin','thin');
                                });

                                $sheet->row(2, array('Empresas',ucwords(mb_strtolower($empresasTexto,'UTF-8'))));
                                $sheet->row(3, array('Fecha Desde', $fecha1V,'Fecha Hasta',$fecha2V));
                                
                                $sheet->row(5, array('folio','Empresa Principal','RUT Principal','Empresa Contratista','RUT Contratista','Empresa Sub Contratista','RUT Sub Contratista','Nombre','Apellido Paterno','Apellido Materno','RUT Trabajador','Cargo','Documento','Fecha de creación','Observación','Estado','Fecha de vencimiento'));
                                foreach ($listaDatosReporteRechazadosGlobales as $datosReporte) {
                                    $sheet->appendRow($datosReporte);
                                }

                            });

                        }
                    }
               
           
            })->export('xlsx');

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
