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
use App\TrabajadorVerificacion;

use Illuminate\Http\Request;

class ReporteTrabajadoresSsoAcre extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $idUsuario = session('user_id');
        $usuarioAqua = session('user_aqua');
        $usuarioABBChile= session('user_ABB');
        $usuarioNOKactivo = session('usuario_nok');
        $certificacion = session('certificacion');
        if($idUsuario ==  ""){
            return view('sesion.index');
        }
        $certificacion = session('certificacion');
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
        $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
        $UsuarioPrincipal->load('usuarioDatos');


        foreach ($UsuarioPrincipal as $rut) {

            $rutprincipal[]=$rut['mainCompanyRut'];
            
        }

        $periodos = Periodo::orderBy('id', 'DES')->get(['id', 'monthId','year']);
        $periodos->load('mes');

        if($datosUsuarios->type ==3){

            $EmpresasP = FolioSso::distinct()->whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['sso_mcomp_name','sso_mcomp_rut']);

            return view('reporteSsoCertificacion.index',compact('datosUsuarios','EmpresasP','certificacion','usuarioAqua','periodos','usuarioABBChile','usuarioNOKactivo')); 

        }
        if($datosUsuarios->type ==2 || $datosUsuarios->type ==1 ){

            $EmpresasP = FolioSso::distinct()->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['sso_mcomp_name','sso_mcomp_rut']);

            return view('reporteSsoCertificacion.index',compact('datosUsuarios','EmpresasP','certificacion','usuarioAqua','periodos','usuarioABBChile','usuarioNOKactivo')); 

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
        $usuarioAqua = session('user_aqua');
        $usuarioABBChile= session('user_ABB');
        $usuarioNOKactivo = session('usuario_nok');
        $certificacion = session('certificacion');
        if($idUsuario ==  ""){
            return view('sesion.index');
        }
        $certificacion = session('certificacion');
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
        $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
        $UsuarioPrincipal->load('usuarioDatos');

         foreach ($UsuarioPrincipal as $rut) {

            $rutprincipal[]=$rut['mainCompanyRut'];
            
        }

        $periodos = Periodo::orderBy('id', 'DES')->get(['id', 'monthId','year']);
        $periodos->load('mes');

        if($datosUsuarios->type ==3){

            $EmpresasP = FolioSso::distinct()->whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['sso_mcomp_name','sso_mcomp_rut']);
        }
        if($datosUsuarios->type ==2 || $datosUsuarios->type ==1 ){

            $EmpresasP = FolioSso::distinct()->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['sso_mcomp_name','sso_mcomp_rut']);
        }

        function formatRut($rut,$dv=null) {
            if (!isset($rut)) {
                return "";
            }
            if ($dv == null) {
                $x = 2;
                $s = 0;
                for ($i = strlen($rut) - 1; $i >= 0; $i--) {
                    if ($x > 7) {
                        $x = 2;
                    }
                    $s += $rut[$i] * $x;
                    $x++;
                }
                $dv = 11 - ($s % 11);
                if ($dv == 10) {
                    $dv = 'k';
                }
                if ($dv == 11) {
                    $dv = '0';
                }
            }
            return $dv;
        }

        $input=$request->all();
        $empresaPrincipal = $input["empresaPrincipal"];
        if(!empty($input["empresaContratista"])){
            $empresaContratista = $input["empresaContratista"];
        }
        $peridoInicio = $input["peridoInicio"];
        foreach ($empresaPrincipal as $value) {
            $rutprincipalR[] = $value;
        }
        $cantidadPrin = count($rutprincipalR);

        $rutprincipalAqua = ['76452811','76794910','79872420','86247400','79800600','84449400','88274600','87782700','76495180','99595500','89604200','78754560','76125666','78512930'];

        foreach ($rutprincipalR as $ruts) {

             $result_array = in_array($ruts,$rutprincipalAqua);
                
        }

    
        if(!empty($empresaContratista)){

            foreach ($empresaContratista as $value2) {

                $rutcontratistasR[] = $value2;
            }
            $cantidadCon = count($rutcontratistasR);
            if($cantidadCon > 0){

                $trabajadoresCantidad = FolioSso::whereIn('sso_mcomp_rut',$rutprincipalR)->whereIn('sso_comp_rut',$rutcontratistasR)->where('sso_status',1)
                ->join('xt_ssov2_header_worker', 'xt_ssov2_header.id', '=', 'xt_ssov2_header_worker.sso_id')
                ->where('xt_ssov2_header_worker.worker_status','1')
                ->select('xt_ssov2_header.id')->count();
                
                $trabajadores = FolioSso::whereIn('sso_mcomp_rut',$rutprincipalR)->whereIn('sso_comp_rut',$rutcontratistasR)->where('sso_status',1)
                ->join('xt_ssov2_header_worker', 'xt_ssov2_header.id', '=', 'xt_ssov2_header_worker.sso_id')
                ->where('xt_ssov2_header_worker.worker_status','1')
                ->select('xt_ssov2_header_worker.id as idt','xt_ssov2_header_worker.worker_name','xt_ssov2_header_worker.worker_name1','xt_ssov2_header_worker.worker_name2','xt_ssov2_header_worker.worker_name3','xt_ssov2_header_worker.worker_rut','xt_ssov2_header_worker.worker_syscargoname','xt_ssov2_header.id','xt_ssov2_header.sso_mcomp_rut','xt_ssov2_header.sso_mcomp_dv','xt_ssov2_header.sso_mcomp_name','xt_ssov2_header.sso_comp_name','xt_ssov2_header.sso_comp_dv','xt_ssov2_header.sso_comp_rut','xt_ssov2_header.sso_subcomp_active','xt_ssov2_header.sso_subcomp_rut','xt_ssov2_header.sso_subcomp_dv','xt_ssov2_header.sso_subcomp_name')->chunk($trabajadoresCantidad, function ($query) use ($result_array,$peridoInicio,$rutprincipalAqua) {
                
                    foreach((array)$query as $trabajadores)
                    {
                      
                        if(!empty($trabajadores)){
                            $rutLimpio = "";
                            foreach($trabajadores AS $trabajador){

                                $rutSSO = str_replace(".", "", $trabajador["worker_rut"]);
                                $rut = explode("-",$rutSSO);
                                $rutLimpio = $rut[0];
                                if(!empty($rut[1])){
                                    $rutDV = $rut[1];
                                }else{
                                    $rutDV = formatRut($rutLimpio);
                                }

                                if($result_array > 0){
                                    $trabajadorVerificacion = TrabajadorVerificacion::where('rut',(int)$rutLimpio)->where('dv',$rutDV)->where('periodId',(int)$peridoInicio)
                                    ->where('companyRut',(int)$trabajador["sso_comp_rut"])->whereIn('mainCompanyRut',$rutprincipalAqua)
                                    ->get(['rut','dv','names','firstLastName','secondLastName','mainCompanyRut','companyRut','mainCompanyName','companyName','position','companyCenter'])->toArray();
                                }else{
                                    $trabajadorVerificacion = TrabajadorVerificacion::where('rut',(int)$rutLimpio)->where('dv',$rutDV)->where('periodId',(int)$peridoInicio)
                                    ->where('companyRut',(int)$trabajador["sso_comp_rut"])->where('mainCompanyRut',(int)$trabajador["sso_mcomp_rut"])
                                    ->get(['rut','dv','names','firstLastName','secondLastName','mainCompanyRut','companyRut','mainCompanyName','companyName','position','companyCenter'])->toArray();
                                }

                                if(!empty($trabajadorVerificacion)){
                                    $estadoCertificacion = Contratista::where('rut',$trabajador['sso_comp_rut'])->where('mainCompanyRut',$trabajador['sso_mcomp_rut'])
                                    ->where('periodId',$peridoInicio)->get(['certificateState','certificateDate'])->toArray();
                                    if(!empty($estadoCertificacion)){
                                            switch ((int)$estadoCertificacion[0]["certificateState"]) {
                                                case 1:
                                                    $estadoCerficacionTexto ="Ingresado";
                                                    break;
                                                case 2:
                                                    $estadoCerficacionTexto ="Solicitado";
                                                    break;
                                                case 3:
                                                    $estadoCerficacionTexto ="Aprobado";
                                                    break;
                                                case 4:
                                                    $estadoCerficacionTexto ="No Aprobado";
                                                    break;
                                                case 5:
                                                    $estadoCerficacionTexto ="Certificado";
                                                    break;
                                                case 6:
                                                    $estadoCerficacionTexto ="Documentado";
                                                    break;
                                                case 7:
                                                    $estadoCerficacionTexto ="Histórico";
                                                    break;
                                                case 8:
                                                    $estadoCerficacionTexto ="Completo";
                                                    break;
                                                case 9:
                                                    $estadoCerficacionTexto ="En Proceso";
                                                    break;
                                                case 10:
                                                    $estadoCerficacionTexto ="No Conforme";
                                                    break;
                                                case 11:
                                                    $estadoCerficacionTexto ="Inactivo";
                                                    break;
                                            }

                                            
                                            $fechaCertificacion = date('d/m/Y', $estadoCertificacion[0]["certificateDate"]);
                                    }else{
                                        $estadoCerficacionTexto="Sin datos";
                                        $fechaCertificacion = "";
                                    }

                                    $datosReporte["folioSSO"] = $trabajador["id"];
                                    $datosReporte["nombreTrabajador"] = ucwords(mb_strtolower($trabajadorVerificacion[0]["names"],'UTF-8'));
                                    $datosReporte["apellido1Trabajador"] = ucwords(mb_strtolower($trabajadorVerificacion[0]["firstLastName"],'UTF-8'));
                                    $datosReporte["apellido2Trabajador"] = ucwords(mb_strtolower($trabajadorVerificacion[0]["secondLastName"],'UTF-8'));
                                    $datosReporte["cargo"] = ucwords(mb_strtolower($trabajadorVerificacion[0]["position"],'UTF-8'));
                                    $datosReporte["rutTrabajador"] = $trabajadorVerificacion[0]["rut"]."-".$trabajadorVerificacion[0]["dv"];
                                    $datosReporte["empresaPrincipal"] = ucwords(mb_strtolower($trabajadorVerificacion[0]["mainCompanyName"],'UTF-8'));
                                    $datosReporte["rutPrincipal"] = $trabajador["sso_mcomp_rut"]."-".$trabajador["sso_mcomp_dv"];
                                    $datosReporte["empresaContratista"] = ucwords(mb_strtolower($trabajadorVerificacion[0]["companyName"],'UTF-8'));
                                    $datosReporte["rutContratista"] = $trabajador["sso_comp_rut"]."-".$trabajador["sso_comp_dv"];;
                                    $datosReporte["estadoCertificacion"] = ucwords(mb_strtolower($estadoCerficacionTexto,'UTF-8'));
                                    $datosReporte["fechaCertificacion"] = $fechaCertificacion;
                                    $datosReporte["centroCosto"] = ucwords(mb_strtolower($trabajadorVerificacion[0]['companyCenter'],'UTF-8'));
                                    $datosReporte["Certificacion"] = "SI";
                                    $datosReporte["porcentajeTrabajador"] =  0;
                                }else{
                                    $datosReporte["folioSSO"] = $trabajador["id"];
                                    $datosReporte["nombreTrabajador"] = ucwords(mb_strtolower($trabajador["worker_name1"],'UTF-8'));
                                    $datosReporte["apellido1Trabajador"] = ucwords(mb_strtolower($trabajador["worker_name2"],'UTF-8'));
                                    $datosReporte["apellido2Trabajador"] = ucwords(mb_strtolower($trabajador["worker_name3"],'UTF-8'));
                                    $datosReporte["cargo"] = ucwords(mb_strtolower($trabajador["worker_syscargoname"],'UTF-8'));
                                    $datosReporte["rutTrabajador"] = str_replace(".", "", $trabajador["worker_rut"]);
                                    $datosReporte["empresaPrincipal"] = ucwords(mb_strtolower($trabajador["sso_mcomp_name"],'UTF-8'));
                                    $datosReporte["rutPrincipal"] = $trabajador["sso_mcomp_rut"]."-".$trabajador["sso_mcomp_dv"];
                                    $datosReporte["empresaContratista"] = ucwords(mb_strtolower($trabajador["sso_comp_name"],'UTF-8'));
                                    $datosReporte["rutContratista"] = $trabajador["sso_comp_rut"]."-".$trabajador["sso_comp_dv"];
                                    $datosReporte["estadoCertificacion"] = "";
                                    $datosReporte["fechaCertificacion"] = "";
                                    $datosReporte["centroCosto"] = "";
                                    $datosReporte["Certificacion"] = "NO";

                                    $documentos = EstadoDocumento::where('upld_sso_id', $trabajador["id"])->where('upld_workerid',$trabajador["idt"])->where('upld_status',1)->where('upld_type',1)->
                                    get(['id','upld_catid','upld_docid','upld_docaprob','upld_venced','upld_vence_date', 'upld_rechazado', 'upld_upddat','upld_docaprob_uid'])->toArray();
               
                
                                    $totalDocRechazados = 0;
                                    $totalDocAprobados = 0;
                                    $totalDocVencidos = 0;
                                    $totalDocRevision = 0;
                                    $porcentajeApro = 0; 
                                    $cantidadRechazados = 0;
                                    $cantidadAprobados = 0;
                                    $cantidadVencidos = 0;
                                    $cantidadPorRevision = 0; 
                                    $cantidadcien = 0;
                                    $noAcreditado = 0;
                                    $totalDoc = 0;
                                    if(!empty($documentos[0]['id'])){
                                        $totalDoc = count($documentos);
                                        foreach ($documentos as  $doc) {

                                            $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
                                            //echo $fecha_actual;
                                            $fecha2 = $doc["upld_vence_date"];
                                            $fechaUpdate = $doc["upld_upddat"];  

                                            if ($doc["upld_rechazado"] == 1) {
                                                $cantidadRechazados +=1; 
                                            }elseif (($doc["upld_docaprob"] == 1 or $fecha_actual <= $fechaUpdate) and $fecha_actual < $fecha2){
                                                $cantidadAprobados +=1; 
                                            }elseif (($doc["upld_venced"]== 1  or $fecha_actual > $fecha2)and $doc["upld_rechazado"] == 0 and $fecha2!= 0){
                                                $cantidadVencidos +=1; 
                                            }elseif ($doc["id"] != "" and $doc["upld_docaprob"] == 0 and $doc["upld_docaprob_uid"] == 0){
                                                $cantidadPorRevision +=1; 
                                            }
                                        }
                                        $totalDocRechazados = $totalDocRechazados + $cantidadRechazados;
                                        $totalDocAprobados = $cantidadAprobados + $totalDocAprobados; 
                                        $totalDocVencidos = $totalDocVencidos + $cantidadVencidos;
                                        $totalDocRevision = $totalDocRevision + $cantidadPorRevision; 
                                        $porcentajeApro = ($totalDocAprobados * 100)/($totalDoc);
                                        

                                        $datosReporte["porcentajeTrabajador"] =  number_format($porcentajeApro, 2, '.', ''); 
                                    }else{
                                        $datosReporte["porcentajeTrabajador"] =  0;
                                    }

                                }/// fin if

                                if(!empty($datosReporte)){
                                    $listaDatosReporte[] = $datosReporte;
                                }

                                
                            }
                        }
                    }
                   
                    Excel::create('Reporte Trabajadores', function($excel) use($listaDatosReporte) {

                        $excel->sheet('SSO VS Verificacion', function($sheet) use($listaDatosReporte) {
                                       
                            $sheet->loadView('excel.ssovsacreditacion',compact('listaDatosReporte')) ;

                        });

                    })->export('xls'); 
                    
                });
            }
        }else{

            $trabajadoresCantidad = FolioSso::whereIn('sso_mcomp_rut',$rutprincipalR)->where('sso_status',1)
                ->join('xt_ssov2_header_worker', 'xt_ssov2_header.id', '=', 'xt_ssov2_header_worker.sso_id')
                ->where('xt_ssov2_header_worker.worker_status','1')
                ->select('xt_ssov2_header.id')->count();

            $trabajadores = FolioSso::whereIn('sso_mcomp_rut',$rutprincipalR)->where('sso_status',1)
            ->join('xt_ssov2_header_worker', 'xt_ssov2_header.id', '=', 'xt_ssov2_header_worker.sso_id')
            ->where('xt_ssov2_header_worker.worker_status','1')
            ->where('xt_ssov2_header_worker.ext_act','0')
            ->select('xt_ssov2_header_worker.id as idt','xt_ssov2_header_worker.worker_name','xt_ssov2_header_worker.worker_name1','xt_ssov2_header_worker.worker_name2','xt_ssov2_header_worker.worker_name3','xt_ssov2_header_worker.worker_rut','xt_ssov2_header_worker.worker_syscargoname','xt_ssov2_header.id','xt_ssov2_header.sso_mcomp_rut','xt_ssov2_header.sso_mcomp_dv','xt_ssov2_header.sso_mcomp_name','xt_ssov2_header.sso_comp_name','xt_ssov2_header.sso_comp_dv','xt_ssov2_header.sso_comp_rut','xt_ssov2_header.sso_subcomp_active','xt_ssov2_header.sso_subcomp_rut','xt_ssov2_header.sso_subcomp_dv','xt_ssov2_header.sso_subcomp_name')
            ->chunk($trabajadoresCantidad, function ($query) use ($result_array,$peridoInicio,$rutprincipalAqua){

            
                foreach((array)$query as $trabajadores)
                {
                   
                    if(!empty($trabajadores)){
                        $rutLimpio = "";
                        foreach($trabajadores AS $trabajador){

                            $rutSSO = str_replace(".", "", $trabajador["worker_rut"]);
                            $rut = explode("-",$rutSSO);
                            $rutLimpio = $rut[0];
                            if(!empty($rut[1])){
                                $rutDV = $rut[1];
                            }else{
                                $rutDV = formatRut($rutLimpio);
                            }

                            if($result_array > 0){
                                $trabajadorVerificacion = TrabajadorVerificacion::where('rut',(int)$rutLimpio)->where('dv',$rutDV)->where('periodId',(int)$peridoInicio)
                                ->where('companyRut',(int)$trabajador["sso_comp_rut"])->whereIn('mainCompanyRut',$rutprincipalAqua)
                                ->get(['rut','dv','names','firstLastName','secondLastName','mainCompanyRut','companyRut','mainCompanyName','companyName','position','companyCenter'])->toArray();
                            }else{
                                $trabajadorVerificacion = TrabajadorVerificacion::where('rut',(int)$rutLimpio)->where('dv',$rutDV)->where('periodId',(int)$peridoInicio)
                                ->where('companyRut',(int)$trabajador["sso_comp_rut"])->where('mainCompanyRut',(int)$trabajador["sso_mcomp_rut"])
                                ->get(['rut','dv','names','firstLastName','secondLastName','mainCompanyRut','companyRut','mainCompanyName','companyName','position','companyCenter'])->toArray();
                            }

                            if(!empty($trabajadorVerificacion)){
                                $estadoCertificacion = Contratista::where('rut',$trabajador['sso_comp_rut'])->where('mainCompanyRut',$trabajador['sso_mcomp_rut'])
                                ->where('periodId',$peridoInicio)->get(['certificateState','certificateDate'])->toArray();
                                if(!empty($estadoCertificacion)){
                                        switch ((int)$estadoCertificacion[0]["certificateState"]) {
                                            case 1:
                                                $estadoCerficacionTexto ="Ingresado";
                                                break;
                                            case 2:
                                                $estadoCerficacionTexto ="Solicitado";
                                                break;
                                            case 3:
                                                $estadoCerficacionTexto ="Aprobado";
                                                break;
                                            case 4:
                                                $estadoCerficacionTexto ="No Aprobado";
                                                break;
                                            case 5:
                                                $estadoCerficacionTexto ="Certificado";
                                                break;
                                            case 6:
                                                $estadoCerficacionTexto ="Documentado";
                                                break;
                                            case 7:
                                                $estadoCerficacionTexto ="Histórico";
                                                break;
                                            case 8:
                                                $estadoCerficacionTexto ="Completo";
                                                break;
                                            case 9:
                                                $estadoCerficacionTexto ="En Proceso";
                                                break;
                                            case 10:
                                                $estadoCerficacionTexto ="No Conforme";
                                                break;
                                            case 11:
                                                $estadoCerficacionTexto ="Inactivo";
                                                break;
                                        }

                                        
                                        $fechaCertificacion = date('d/m/Y', $estadoCertificacion[0]["certificateDate"]);
                                }else{
                                    $estadoCerficacionTexto="Sin datos";
                                    $fechaCertificacion = "";
                                }

                                $datosReporte["folioSSO"] = $trabajador["id"];
                                $datosReporte["nombreTrabajador"] = ucwords(mb_strtolower($trabajadorVerificacion[0]["names"],'UTF-8'));
                                $datosReporte["apellido1Trabajador"] = ucwords(mb_strtolower($trabajadorVerificacion[0]["firstLastName"],'UTF-8'));
                                $datosReporte["apellido2Trabajador"] = ucwords(mb_strtolower($trabajadorVerificacion[0]["secondLastName"],'UTF-8'));
                                $datosReporte["cargo"] = ucwords(mb_strtolower($trabajadorVerificacion[0]["position"],'UTF-8'));
                                $datosReporte["rutTrabajador"] = $trabajadorVerificacion[0]["rut"]."-".$trabajadorVerificacion[0]["dv"];
                                $datosReporte["empresaPrincipal"] = ucwords(mb_strtolower($trabajadorVerificacion[0]["mainCompanyName"],'UTF-8'));
                                $datosReporte["rutPrincipal"] = $trabajador["sso_mcomp_rut"]."-".$trabajador["sso_mcomp_dv"];
                                $datosReporte["empresaContratista"] = ucwords(mb_strtolower($trabajadorVerificacion[0]["companyName"],'UTF-8'));
                                $datosReporte["rutContratista"] = $trabajador["sso_comp_rut"]."-".$trabajador["sso_comp_dv"];;
                                $datosReporte["estadoCertificacion"] = ucwords(mb_strtolower($estadoCerficacionTexto,'UTF-8'));
                                $datosReporte["centroCosto"] = ucwords(mb_strtolower($trabajadorVerificacion[0]["companyCenter"],'UTF-8'));;
                                $datosReporte["fechaCertificacion"] = $fechaCertificacion;
                                $datosReporte["Certificacion"] = "SI";
                                $documentos = EstadoDocumento::where('upld_sso_id', $trabajador["id"])->where('upld_workerid',$trabajador["idt"])->where('upld_status',1)->where('upld_type',1)->
                                    get(['id','upld_catid','upld_docid','upld_docaprob','upld_venced','upld_vence_date', 'upld_rechazado', 'upld_upddat','upld_docaprob_uid'])->toArray();
               
                
                                    $totalDocRechazados = 0;
                                    $totalDocAprobados = 0;
                                    $totalDocVencidos = 0;
                                    $totalDocRevision = 0;
                                    $porcentajeApro = 0; 
                                    $cantidadRechazados = 0;
                                    $cantidadAprobados = 0;
                                    $cantidadVencidos = 0;
                                    $cantidadPorRevision = 0; 
                                    $cantidadcien = 0;
                                    $noAcreditado = 0;
                                    $totalDoc = 0;
                                    if(!empty($documentos[0]['id'])){
                                        $totalDoc = count($documentos);
                                        foreach ($documentos as  $doc) {

                                            $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
                                            //echo $fecha_actual;
                                            $fecha2 = $doc["upld_vence_date"];
                                            $fechaUpdate = $doc["upld_upddat"];  

                                            if ($doc["upld_rechazado"] == 1) {
                                                $cantidadRechazados +=1; 
                                            }elseif (($doc["upld_docaprob"] == 1 or $fecha_actual <= $fechaUpdate) and $fecha_actual < $fecha2){
                                                $cantidadAprobados +=1; 
                                            }elseif (($doc["upld_venced"]== 1  or $fecha_actual > $fecha2)and $doc["upld_rechazado"] == 0 and $fecha2!= 0){
                                                $cantidadVencidos +=1; 
                                            }elseif ($doc["id"] != "" and $doc["upld_docaprob"] == 0 and $doc["upld_docaprob_uid"] == 0){
                                                $cantidadPorRevision +=1; 
                                            }
                                        }
                                        $totalDocRechazados = $totalDocRechazados + $cantidadRechazados;
                                        $totalDocAprobados = $cantidadAprobados + $totalDocAprobados; 
                                        $totalDocVencidos = $totalDocVencidos + $cantidadVencidos;
                                        $totalDocRevision = $totalDocRevision + $cantidadPorRevision; 
                                        $porcentajeApro = ($totalDocAprobados * 100)/($totalDoc);
                                        

                                        $datosReporte["porcentajeTrabajador"] =  number_format($porcentajeApro, 2, '.', ''); 
                                    }else{
                                        $datosReporte["porcentajeTrabajador"] =  0;
                                    }
                            }else{
                                $datosReporte["folioSSO"] = $trabajador["id"];
                                $datosReporte["nombreTrabajador"] = ucwords(mb_strtolower($trabajador["worker_name1"],'UTF-8'));
                                $datosReporte["apellido1Trabajador"] = ucwords(mb_strtolower($trabajador["worker_name2"],'UTF-8'));
                                $datosReporte["apellido2Trabajador"] = ucwords(mb_strtolower($trabajador["worker_name3"],'UTF-8'));
                                $datosReporte["cargo"] = ucwords(mb_strtolower($trabajador["worker_syscargoname"],'UTF-8'));
                                $datosReporte["rutTrabajador"] = str_replace(".", "", $trabajador["worker_rut"]);
                                $datosReporte["empresaPrincipal"] = ucwords(mb_strtolower($trabajador["sso_mcomp_name"],'UTF-8'));
                                $datosReporte["rutPrincipal"] = $trabajador["sso_mcomp_rut"]."-".$trabajador["sso_mcomp_dv"];
                                $datosReporte["empresaContratista"] = ucwords(mb_strtolower($trabajador["sso_comp_name"],'UTF-8'));
                                $datosReporte["rutContratista"] = $trabajador["sso_comp_rut"]."-".$trabajador["sso_comp_dv"];
                                $datosReporte["estadoCertificacion"] = "";
                                $datosReporte["fechaCertificacion"] = "";
                                $datosReporte["centroCosto"] = "";
                                $datosReporte["Certificacion"] = "NO";
                                

                                $documentos = EstadoDocumento::where('upld_sso_id', $trabajador["id"])->where('upld_workerid',$trabajador["idt"])->where('upld_status',1)->where('upld_type',1)->
                                    get(['id','upld_catid','upld_docid','upld_docaprob','upld_venced','upld_vence_date', 'upld_rechazado', 'upld_upddat','upld_docaprob_uid'])->toArray();
               
                
                                    $totalDocRechazados = 0;
                                    $totalDocAprobados = 0;
                                    $totalDocVencidos = 0;
                                    $totalDocRevision = 0;
                                    $porcentajeApro = 0; 
                                    $cantidadRechazados = 0;
                                    $cantidadAprobados = 0;
                                    $cantidadVencidos = 0;
                                    $cantidadPorRevision = 0; 
                                    $cantidadcien = 0;
                                    $noAcreditado = 0;
                                    $totalDoc = 0;
                                    if(!empty($documentos[0]['id'])){
                                        $totalDoc = count($documentos);
                                        foreach ($documentos as  $doc) {

                                            $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
                                            //echo $fecha_actual;
                                            $fecha2 = $doc["upld_vence_date"];
                                            $fechaUpdate = $doc["upld_upddat"];  

                                            if ($doc["upld_rechazado"] == 1) {
                                                $cantidadRechazados +=1; 
                                            }elseif (($doc["upld_docaprob"] == 1 or $fecha_actual <= $fechaUpdate) and $fecha_actual < $fecha2){
                                                $cantidadAprobados +=1; 
                                            }elseif (($doc["upld_venced"]== 1  or $fecha_actual > $fecha2)and $doc["upld_rechazado"] == 0 and $fecha2!= 0){
                                                $cantidadVencidos +=1; 
                                            }elseif ($doc["id"] != "" and $doc["upld_docaprob"] == 0 and $doc["upld_docaprob_uid"] == 0){
                                                $cantidadPorRevision +=1; 
                                            }
                                        }
                                        $totalDocRechazados = $totalDocRechazados + $cantidadRechazados;
                                        $totalDocAprobados = $cantidadAprobados + $totalDocAprobados; 
                                        $totalDocVencidos = $totalDocVencidos + $cantidadVencidos;
                                        $totalDocRevision = $totalDocRevision + $cantidadPorRevision; 
                                        $porcentajeApro = ($totalDocAprobados * 100)/($totalDoc);
                                        

                                        $datosReporte["porcentajeTrabajador"] =  number_format($porcentajeApro, 2, '.', ''); 
                                    }else{
                                        $datosReporte["porcentajeTrabajador"] =  0;
                                    }
                            }

                            if(!empty($datosReporte)){
                                $listaDatosReporte[] = $datosReporte;
                            }

                            
                        }
                    }
                }

                Excel::create('Reporte Trabajadores', function($excel) use($listaDatosReporte) {

                    $excel->sheet('SSO VS Verificacion', function($sheet) use($listaDatosReporte) {
                                   
                        $sheet->loadView('excel.ssovsacreditacion',compact('listaDatosReporte')) ;

                    });

                })->export('xls');
                

            });
        } 
     
        $lista=0;
        return view('reporteSsoCertificacion.index',compact('datosUsuarios','EmpresasP','certificacion','usuarioAqua','periodos','lista','usuarioABBChile','usuarioNOKactivo'));

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
