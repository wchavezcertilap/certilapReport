<?php
// REPORTE TRABAJADORES CERTIFICACION, VERIFICACION CONTROL ACCESO
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
use App\TrabajadorVerificacion;
use App\tipoEmpresa;
use App\tipoServicio;
use App\categoriaServicio;
use App\AccesoPersona;
use App\trabajadorSSO;
use App\EstadoDocumento;


use Illuminate\Http\Request;

class ReporteCSAController extends Controller
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
        $certificacion = session('certificacion');
        $usuarioABBChile= session('user_ABB');
        $usuarioClaroChile= session('user_Claro');
        $usuarioNOKactivo = session('usuario_nok');
        $periodosT = 0;
        $principalesTexto = "";
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
        $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
        $UsuarioPrincipal->load('usuarioDatos');


        foreach ($UsuarioPrincipal as $rut) {

            $rutprincipal[]=$rut['mainCompanyRut'];
            
        }

        if($datosUsuarios->type == 3){

            $EmpresasP = empresaPrincipal::distinct()->whereIn('rut',$rutprincipal)->orderBy('name', 'ASC')->get(['name','rut']);


        }
        if($datosUsuarios->type ==2 || $datosUsuarios->type ==1 ){

            $EmpresasP = empresaPrincipal::distinct()->orderBy('name', 'ASC')->get(['name','rut']);


        }

        $periodos = Periodo::orderBy('id', 'DES')->get(['id', 'monthId','year']);
        $periodos->load('mes');
        $etiquetasEstados = 0;
        $valoresEstados = 0;
        return view('reporteTraCSA.index',compact('EmpresasP','periodos','datosUsuarios','etiquetasEstados','valoresEstados','certificacion','periodosT','principalesTexto','usuarioAqua','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile'));
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
        $certificacion = session('certificacion');
        $usuarioAqua = session('user_aqua');
        $usuarioABBChile= session('user_ABB');
        $usuarioClaroChile= session('user_Claro');
        $usuarioNOKactivo = session('usuario_nok');
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
        $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
        $UsuarioPrincipal->load('usuarioDatos');

        foreach ($UsuarioPrincipal as $rut) {
            $rutprincipal[]=$rut['mainCompanyRut'];
        }

        if($datosUsuarios->type == 3){

            $EmpresasP = empresaPrincipal::distinct()->whereIn('rut',$rutprincipal)->orderBy('name', 'ASC')->get(['name','rut']);
        }
        if($datosUsuarios->type ==2 || $datosUsuarios->type ==1 ){

             $EmpresasP = empresaPrincipal::distinct()->orderBy('name', 'ASC')->get(['name','rut']);
        }

        $periodos = Periodo::orderBy('id', 'DES')->get(['id', 'monthId','year']);
        $periodos->load('mes');

        //////// busqueda de datos //////
        $input=$request->all();    
        $empresaPrincipal = $input["empresaPrincipal"];
        $countContratista = 0;
        if(!empty($input["empresaContratista"])){
            $empresaContratista = $input["empresaContratista"];

            foreach ($empresaContratista as $value2) {
                $rutcontratistasR[] = $value2;
            }

            $countContratista =count($rutcontratistasR); 
        }
        $tipoBsuqueda = $input["tipoBsuqueda"];
        if($tipoBsuqueda == 1){
            $textoTipoB = "Por Periodo";
        }else{
            $textoTipoB = "Por Fecha";
        }
        $centroCosto = $input["centroCosto"];
      
        foreach ($empresaPrincipal as $value) {

            $rutprincipalR[] = $value;
        }

        function super_unique($array,$key)
        {
           $temp_array = [];
           foreach ($array as &$v) {
               if (!isset($temp_array[$v[$key]]))
               $temp_array[$v[$key]] =& $v;
           }
           $array = array_values($temp_array);
           return $array;

        }

        if($rutprincipalR[0]==1){

            $rutprincipalRL = super_unique($EmpresasP,'rut');

            foreach ($rutprincipalRL as $value) {
            $rutprincipalR[] = $value['rut'];
            $rutprincipalRN[] = $value['name'];
            }

            $principalesTexto = "TODAS";

        }else{
            $rutprincipalRC = empresaPrincipal::distinct()->whereIn('rut',$empresaPrincipal)->orderBy('name', 'ASC')->get(['name','rut'])->toArray();

            $rutprincipalRL = super_unique($rutprincipalRC,'rut');
            foreach ($rutprincipalRL as $value) {
            $rutprincipalR[] = $value['rut'];
            $rutprincipalRN[] = $value['name'];
            }

            $principalesTexto = implode(", ", $rutprincipalRN);
   
            $principalesTexto = (mb_strtoupper($principalesTexto,'UTF-8'));
        }

        if($tipoBsuqueda == 1){

            $peridoInicio = $input["peridoInicio"];
            $periodosIT = Periodo::where('id', $peridoInicio)->get(['id', 'monthId','year']);
            $periodosIT->load('mes')->toArray();
            if($periodosIT[0]['monthId']>9){
                $MESI=$periodosIT[0]['monthId'];
            }else{
                $MESI='0'.$periodosIT[0]['monthId'];
            }
            $fechaInicial = $periodosIT[0]['year'].'-'.$MESI.'-01'; 
            $peridoFinal = $input["peridoFinal"];
            $periodosFT = Periodo::where('id', $peridoFinal)->get(['id', 'monthId','year']);
            $periodosFT->load('mes')->toArray();
            if($periodosIT[0]['monthId']>9){
                $MESF=$periodosFT[0]['monthId'];
            }else{
                $MESF='0'.$periodosFT[0]['monthId'];
            }
            $fechaFinal = $periodosFT[0]['year'].'-'.$MESF.'-31'; 
            $periodosFT= $periodosFT[0]['mes'][0]['name'];
            $periodosIT= $periodosIT[0]['mes'][0]['name'];
            $periodosT =  $periodosIT ."-".$periodosFT ; 
            
           
            if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista != 0 AND $centroCosto != 0){

                $WORKCER2 = array();
                $WORKCER = array();

                $countEmpresasContratista= DB::table('Company')
                ->join('Worker', function ($join) use ($value){
                    $join->on('Worker.mainCompanyRut','=','Company.mainCompanyRut')
                        ->on('Worker.companyRut','=','Company.rut')
                        ->on('Worker.periodId','=','Company.periodId')
                        ->on('Worker.CompanyCenter','=','Company.center');
                })
                ->whereIn('Company.mainCompanyRut',$rutprincipalR)
                ->whereIn('Company.rut',$rutcontratistasR)
                ->whereNotIn('Company.certificateState', [11,7])
                ->whereBetween('Company.periodId', [$peridoInicio,$peridoFinal])
                ->where('Company.id',$centroCosto)
                ->orderBy('Company.id', 'ASC')->count();

                $empresasContratista= DB::table('Company')
                ->join('Worker', function ($join) use ($value){
                    $join->on('Worker.mainCompanyRut','=','Company.mainCompanyRut')
                        ->on('Worker.companyRut','=','Company.rut')
                        ->on('Worker.periodId','=','Company.periodId')
                        ->on('Worker.CompanyCenter','=','Company.center');
                })
                ->whereIn('Company.mainCompanyRut',$rutprincipalR)
                ->whereIn('Company.rut',$rutcontratistasR)
                ->whereNotIn('Company.certificateState', [11,7])
                ->whereBetween('Company.periodId', [$peridoInicio,$peridoFinal])
                ->where('Company.id',$centroCosto)
                ->orderBy('Company.id', 'ASC')
                ->select('Company.id as idComp','Company.rut as rutComp','Company.dv as dvComp','Company.name as nameComp','Company.mainCompanyName','Company.companyTypeId','Company.mainCompanyRut','Company.center','Company.certificateState','Company.certificateDate','Company.periodId','Company.subcontratistaRut','Company.subcontratistaName','Company.subcontratistaDv','Worker.rut','Worker.dv','Worker.names','Worker.firstLastName','Worker.secondLastName')->chunk($countEmpresasContratista, function ($query) use (&$WORKCER2,&$WORKCER,$fechaInicial,$fechaFinal)
                {
                    function periodoTexto($idPerido){

                    $periodo = DB::table('Period')
                    ->join('Month', 'Month.id', '=', 'Period.monthId')
                    ->where(['Period.id' => $idPerido])
                    ->select('Period.year','Month.name')
                    ->get();

                    return $periodo[0]->name."-".$periodo[0]->year;
                    }

                    function estadoCerficacionTexto($idEstadoCert){

                        switch ((int)$idEstadoCert) {
                            case 1:
                                return $estadoCerficacionTexto ="Ingresado";
                                break;
                            case 2:
                                return $estadoCerficacionTexto ="Solicitado";
                                break;
                            case 3:
                                return $estadoCerficacionTexto ="Aprobado";
                                break;
                            case 4:
                                return $estadoCerficacionTexto ="No Aprobado";
                                break;
                            case 5:
                                return $estadoCerficacionTexto ="Certificado";
                                break;
                            case 6:
                                return $estadoCerficacionTexto ="Documentado";
                                break;
                            case 7:
                                return $estadoCerficacionTexto ="Histórico";
                                break;
                            case 8:
                                return $estadoCerficacionTexto ="Completo";
                                break;
                            case 9:
                                return $estadoCerficacionTexto ="En Proceso";
                                break;
                            case 10:
                                return $estadoCerficacionTexto ="No Conforme";
                                break;
                            case 11:
                                return $estadoCerficacionTexto ="Inactivo";
                                break;
                        }
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
                                $dv = 'K';
                            }
                            if ($dv == 11) {
                                $dv = '0';
                            }
                        }
                        return number_format($rut, 0, ",", "") . '-' . $dv;
                    }

                    foreach((array)$query as $empresasContratista){

                        if(!empty($empresasContratista)){

                            foreach ($empresasContratista as $contratista) {
                                unset($Datoscertificacion);
                                unset($datosAcceso);
                                unset($documentosTrabObligatorios);
                                if($contratista->companyTypeId == 1){
                                    $rutContratista = $contratista->rutComp."-".$contratista->dvComp; 
                                    $nombreContratista = ucwords(mb_strtolower($contratista->nameComp,'UTF-8')); 
                                    $rutSubContratista = "";
                                    $nombreSubContratista = "";
                                    $rutContratista2 = $contratista->rutComp;
                                    $rutContratistasinDV = $contratista->rutComp;
                                }
                                if($contratista->companyTypeId == 2){
                                    $rutContratista = $contratista->subcontratistaRut."-".$contratista->subcontratistaDv;
                                    $nombreContratista =  ucwords(mb_strtolower($contratista->subcontratistaName,'UTF-8'));  
                                    $rutSubContratista = $contratista->rutComp."-".$contratista->dvComp; 
                                    $nombreSubContratista = ucwords(mb_strtolower($contratista->nameComp,'UTF-8')); 
                                    $rutContratista2 = $contratista->rutComp;
                                    $rutContratistasinDV = $contratista->subcontratistaRut;
                                }

                                $peridoTex = periodoTexto($contratista->periodId);
                                $estadoCerficacionTexto = estadoCerficacionTexto($contratista->certificateState);
                                $fechaCertificiacion=date('d/m/Y', $contratista->certificateDate);
                                $rutTrabajadorCert = $contratista->rut.'-'.$contratista->dv;
                                $Datoscertificacion['rutTrabajador'] = $rutTrabajadorCert; 
                                $Datoscertificacion['nombreTrabajador'] = ucwords(mb_strtolower($contratista->names,'UTF-8')); 
                                $Datoscertificacion['apellido1Trabajador'] = ucwords(mb_strtolower($contratista->firstLastName,'UTF-8'));
                                $Datoscertificacion['apellido2Trabajador'] = ucwords(mb_strtolower($contratista->secondLastName,'UTF-8')); 
                                $Datoscertificacion['idComp'] = $contratista->idComp; 
                                $Datoscertificacion['rutPrincipal'] = formatRut($contratista->mainCompanyRut); 
                                $Datoscertificacion['nombrePrincipal'] = ucwords(mb_strtolower($contratista->mainCompanyName,'UTF-8'));    
                                $Datoscertificacion['rutContratista'] = $rutContratista;
                                $Datoscertificacion['nombreContratista'] = $nombreContratista; 
                                $Datoscertificacion['rutSubContratista'] =  $rutSubContratista;
                                $Datoscertificacion['nombreSubContratista'] = $nombreSubContratista;
                                $Datoscertificacion['centroCosto'] = ucwords(mb_strtolower($contratista->center,'UTF-8'));          
                                $Datoscertificacion['perido'] = ucwords(mb_strtolower($peridoTex,'UTF-8'));   
                                $Datoscertificacion['estadoCertificacion'] = ucwords(mb_strtolower($estadoCerficacionTexto,'UTF-8')); 
                                $Datoscertificacion['fechaCertificado'] =  $fechaCertificiacion;
                    

                
                                $datosAcceso =AccesoPersona::where('ACC_RUT',$contratista->rut)
                                ->where('ACC_RUT_CONTRATISTA',$rutContratistasinDV)
                                ->where('ACC_RUT_PPAL',$contratista->mainCompanyRut)
                                ->where('ACC_CENTRO_COSTO',$contratista->center)
                                ->whereDate('ACC_FECHA_ACCESO', '>=', $fechaInicial)
                                ->whereDate('ACC_FECHA_ACCESO', '<=', $fechaFinal)
                                ->take(1)
                                ->orderBy('ACC_FECHA_ACCESO', 'DESC')
                                ->get(['ACC_FECHA_ACCESO'])->toArray();
                                if(!empty($datosAcceso[0]['ACC_FECHA_ACCESO'])){
                                    $Datoscertificacion['ControlAcceso'] =  $datosAcceso[0]['ACC_FECHA_ACCESO'];
                                }else{
                                    $Datoscertificacion['ControlAcceso'] =  "";    
                                } 
                                
            
                               $EP = $contratista->mainCompanyRut;
                                $empleadoSSO = DB::table('xt_ssov2_header_worker')
                                ->join('xt_ssov2_header', function ($join) use ($EP,$rutContratistasinDV){
                                    $join->where('xt_ssov2_header.sso_mcomp_rut','=',$EP)
                                         ->where('xt_ssov2_header.sso_comp_rut','=',$rutContratistasinDV)
                                         ->on('xt_ssov2_header.id','=','xt_ssov2_header_worker.sso_id');
                                })
                                ->where('worker_status','1')
                                ->where('worker_rut',$rutTrabajadorCert)
                                ->get(['xt_ssov2_header_worker.id','worker_name','worker_name1','worker_name2','worker_name3','worker_rut','worker_cargoid','xt_ssov2_header_worker.sso_id','xt_ssov2_header.sso_cfgid'])
                                ->take(1)->toArray();
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
                                if(!empty($empleadoSSO[0]->id)){
                                    
                                    $documentos = EstadoDocumento::where('upld_sso_id', $empleadoSSO[0]->sso_id)->where('upld_workerid',$empleadoSSO[0]->id)->where('upld_status',1)->where('upld_type',1)->
                                        get(['id','upld_catid','upld_docid','upld_docaprob','upld_venced','upld_vence_date', 'upld_rechazado', 'upld_upddat','upld_docaprob_uid'])->toArray();

                                    $documentosTrabObligatorios = DB::table('xt_ssov2_configs_cargos_cats_docs_params')
                                    ->join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                                    ->where(['xt_ssov2_configs_cargos_cats_docs_params.cfg_id' => $empleadoSSO[0]->sso_cfgid])
                                    ->where(['xt_ssov2_configs_cargos_cats_docs_params.cargo_id' => $empleadoSSO[0]->worker_cargoid])
                                    ->where(['xt_ssov2_doctypes.doc_status' => 1])
                                    ->distinct('xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                                    ->get(['xt_ssov2_configs_cargos_cats_docs_params.doc_id'])->count();

                                    $totalDoc = count($documentos);
                                    if(!empty($documentos[0]['id'])){ 

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
                                 
                                        $porcentajeApro = ($totalDocAprobados / $documentosTrabObligatorios * 100);
                                        if($porcentajeApro>=100){
                                            $cantidadcien +=1; 
                                        }else{
                                            $noAcreditado +=1;
                                        }
                                       
                                        $Datoscertificacion["porcentajeTrabajador"] =  number_format($porcentajeApro, 2, '.', '');
                                    }else{
                                        $Datoscertificacion["porcentajeTrabajador"] =  0;
                                    }

                                }else{
                                    
                                    $rutTrabajadorD = number_format($contratista->rut, 0, "", ".") . '-' .$contratista->dv;
                               
                                    $empleadoSSOD =  DB::table('xt_ssov2_header_worker')
                                    ->join('xt_ssov2_header', function ($join) use ($EP,$rutContratistasinDV){
                                        $join->where('xt_ssov2_header.sso_mcomp_rut','=',$EP)
                                             ->where('xt_ssov2_header.sso_comp_rut','=',$rutContratistasinDV)
                                             ->on('xt_ssov2_header.id','=','xt_ssov2_header_worker.sso_id');
                                    })
                                    ->where('worker_status','1')
                                    ->where('worker_rut',$rutTrabajadorD)
                                    ->get(['xt_ssov2_header_worker.id','worker_name','worker_name1','worker_name2','worker_name3','worker_rut','worker_cargoid','xt_ssov2_header_worker.sso_id','xt_ssov2_header.sso_cfgid'])
                                    ->take(1)->toArray();
                                   
                                    if(!empty($empleadoSSOD[0]->id)){

                                        $documentosTrabObligatorios = DB::table('xt_ssov2_configs_cargos_cats_docs_params')
                                        ->join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                                        ->where(['xt_ssov2_configs_cargos_cats_docs_params.cfg_id' => $empleadoSSOD[0]->sso_cfgid])
                                        ->where(['xt_ssov2_configs_cargos_cats_docs_params.cargo_id' => $empleadoSSOD[0]->worker_cargoid])
                                        ->where(['xt_ssov2_doctypes.doc_status' => 1])
                                        ->distinct('xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                                        ->get(['xt_ssov2_configs_cargos_cats_docs_params.doc_id'])->count();
                                    
                                        $documentos = EstadoDocumento::where('upld_sso_id', $empleadoSSOD[0]->sso_id)->where('upld_workerid',$empleadoSSOD[0]->id)->where('upld_status',1)->where('upld_type',1)->
                                        get(['id','upld_catid','upld_docid','upld_docaprob','upld_venced','upld_vence_date', 'upld_rechazado', 'upld_upddat','upld_docaprob_uid'])->toArray();
                                        
                                        $totalDoc = count($documentos);
                                       
                                        if(!empty($documentos[0]['id'])){ 

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
                                            $porcentajeApro = ($totalDocAprobados / $documentosTrabObligatorios * 100);
                                            if($porcentajeApro>=100){
                                                $cantidadcien +=1; 
                                            }else{
                                                $noAcreditado +=1;
                                            }

                                            $Datoscertificacion["porcentajeTrabajador"] =  number_format($porcentajeApro, 2, '.', '');
                                        }else{
                                            $Datoscertificacion["porcentajeTrabajador"] =  0;
                                        }
                                    }else{
                                        $Datoscertificacion["porcentajeTrabajador"] = "";

                                    }
                                }
                                ///llenamos lista de datos ////
                                $WORKCER[] = $Datoscertificacion;
                            } 
                        }
                    }
                    $WORKCER = $WORKCER2;
                }); 

                ////////////FOLIOS///////////////////

                /*$countEmpresasSSO= DB::table('xt_ssov2_header')
                ->join('xt_ssov2_header_worker', function ($join) use ($value){
                    $join->on('xt_ssov2_header_worker.sso_id','=','xt_ssov2_header.id');
                })
                ->whereIn('xt_ssov2_header.sso_mcomp_rut',$rutprincipalR)
                ->whereIn('xt_ssov2_header.sso_comp_rut',$rutcontratistasR)
                ->where('xt_ssov2_header.sso_status',1)
                ->where('xt_ssov2_header_worker.worker_status',1)
                ->orderBy('xt_ssov2_header.id', 'ASC')->count();

                $WORKCERSSO = array();
                $WORKCERSSO2 = array();
                $empresasSSO= DB::table('xt_ssov2_header')
                ->join('xt_ssov2_header_worker', function ($join) use ($value){
                    $join->on('xt_ssov2_header_worker.sso_id','=','xt_ssov2_header.id');
                })
                ->whereIn('xt_ssov2_header.sso_mcomp_rut',$rutprincipalR)
                ->whereIn('xt_ssov2_header.sso_comp_rut',$rutcontratistasR)
                ->where('xt_ssov2_header.sso_status',1)
                ->where('xt_ssov2_header_worker.worker_status',1)
                ->orderBy('xt_ssov2_header.id', 'ASC')
                ->select('xt_ssov2_header.id as folio','xt_ssov2_header.sso_mcomp_rut','xt_ssov2_header.sso_mcomp_dv','xt_ssov2_header.sso_mcomp_name','xt_ssov2_header.sso_comp_rut','xt_ssov2_header.sso_comp_dv','xt_ssov2_header.sso_comp_name','xt_ssov2_header.sso_subcomp_active','xt_ssov2_header.sso_subcomp_rut','xt_ssov2_header.sso_subcomp_name','xt_ssov2_header_worker.id','xt_ssov2_header_worker.worker_name','xt_ssov2_header_worker.worker_name1','xt_ssov2_header_worker.worker_name2','xt_ssov2_header_worker.worker_name3','xt_ssov2_header_worker.worker_rut')->chunk($countEmpresasSSO, function ($query) use (&$WORKCERSSO,&$WORKCERSSO2) 
                {
                   
                    foreach((array)$query as $empresasSSO){

                        if(!empty($empresasSSO[0])){

                            foreach ($empresasSSO as $ssot) {

                                $rutTraSSO = $ssot->worker_rut;
                                $findme   = '.';
                                $pos = strpos($ssot->worker_rut, $findme);

                                if($pos === false) {
                                    $rut = explode("-",$ssot->worker_rut);
                                    $rutLimpio = $rut[0];
                                    $dvrut = substr($ssot->worker_rut,-1);
                                }else{
                                    $rut2 = str_replace(".", "", $ssot->worker_rut);
                                    $rut = explode("-",$rut2);
                                    $rutLimpio = $rut[0];
                                    $dvrut = substr($ssot->worker_rut,-1);
                                }
                                $rutTrabajadorSSO = $rutLimpio."-".$dvrut;
                                $datosAcceso =AccesoPersona::where('ACC_RUT',$rutLimpio)->take(1)->get(['ACC_FECHA_ACCESO','ACC_CENTRO_COSTO'])->toArray();
               

                                $DatoSSO['rutTrabajador'] = $rutTrabajadorSSO; 
                                $DatoSSO['nombreTrabajador'] = ucwords(mb_strtolower($ssot->worker_name1,'UTF-8')); 
                                $DatoSSO['apellido1Trabajador'] = ucwords(mb_strtolower($ssot->worker_name2,'UTF-8'));
                                $DatoSSO['apellido2Trabajador'] = ucwords(mb_strtolower($ssot->worker_name3,'UTF-8')); 
                                $DatoSSO['idComp'] = $ssot->folio; 
                                $DatoSSO['rutPrincipal'] = formatRut($ssot->sso_mcomp_rut); 
                                $DatoSSO['nombrePrincipal'] = ucwords(mb_strtolower($ssot->sso_mcomp_name,'UTF-8'));    
                                $DatoSSO['rutContratista'] = formatRut($ssot->sso_comp_rut);;
                                $DatoSSO['nombreContratista'] = ucwords(mb_strtolower($ssot->sso_comp_name,'UTF-8')); 
                                if($ssot->sso_subcomp_active == 1){
                                    $DatoSSO['rutSubContratista'] =  formatRut($ssot->sso_subcomp_rut);
                                    $DatoSSO['nombreSubContratista'] = ucwords(mb_strtolower($ssot->sso_subcomp_name,'UTF-8'));
                                }else{
                                    $DatoSSO['rutSubContratista'] =  "";
                                    $DatoSSO['nombreSubContratista'] = "";    
                                }   
                                if(!empty($datosAcceso[0]['ACC_FECHA_ACCESO'])){
                                    $DatoSSO['ControlAcceso'] =  $datosAcceso[0]['ACC_FECHA_ACCESO'];
                                    $DatoSSO['centroCosto'] = ucwords(mb_strtolower($datosAcceso[0]['ACC_CENTRO_COSTO'],'UTF-8')); 
                                }else{
                                    $DatoSSO['ControlAcceso'] =  ""; 
                                    $DatoSSO['centroCosto'] = "";   
                                }   
                                $DatoSSO['perido'] = "";   
                                $DatoSSO['estadoCertificacion'] = ""; 
                                $DatoSSO['fechaCertificado'] =  "";

                                $documentos = EstadoDocumento::where('upld_sso_id', $ssot->folio)->where('upld_workerid',$ssot->id)->where('upld_status',1)->where('upld_type',1)->
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
                                    if($porcentajeApro>=100){
                                        $cantidadcien +=1; 
                                    }else{
                                        $noAcreditado +=1;
                                    }

                                    $DatoSSO["porcentajeTrabajador"] =  number_format($porcentajeApro, 2, '.', ''); 
                                }else{
                                    $DatoSSO["porcentajeTrabajador"] =  0;
                                }

                                $WORKCERSSO2[] = $DatoSSO; 
                            }
                        }
                    }
                     $WORKCERSSO = $WORKCERSSO2;
                });
                $WORKS = array_merge($WORKCER, $WORKCERSSO);*/
                $WORKS = $WORKCER;
                if(!empty($WORKS)){

                    Excel::create('Reporte Cruzado', function($excel) use ($WORKS) {

                        $excel->sheet('Datos', function($sheet) use($WORKS) {    
                            $sheet->loadView('reporteTraCSA.excel',compact('WORKS'));
                        });
                    })->export('xls'); 
                }               

            }
            if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista != 0){
                $WORKCER2 = array();
                $WORKCER = array();
                $countEmpresasContratista= DB::table('Company')
                ->join('Worker', function ($join) use ($value){
                    $join->on('Worker.mainCompanyRut','=','Company.mainCompanyRut')
                        ->on('Worker.companyRut','=','Company.rut')
                        ->on('Worker.periodId','=','Company.periodId')
                        ->on('Worker.CompanyCenter','=','Company.center');
                })
                ->whereIn('Company.mainCompanyRut',$rutprincipalR)
                ->whereIn('Company.rut',$rutcontratistasR)
                ->whereNotIn('Company.certificateState', [11,7])
                ->whereBetween('Company.periodId', [$peridoInicio,$peridoFinal])
                ->orderBy('Company.id', 'ASC')->count();

                $empresasContratista= DB::table('Company')
                ->join('Worker', function ($join) use ($value){
                    $join->on('Worker.mainCompanyRut','=','Company.mainCompanyRut')
                        ->on('Worker.companyRut','=','Company.rut')
                        ->on('Worker.periodId','=','Company.periodId')
                        ->on('Worker.CompanyCenter','=','Company.center');
                })
                ->whereIn('Company.mainCompanyRut',$rutprincipalR)
                ->whereIn('Company.rut',$rutcontratistasR)
                ->whereNotIn('Company.certificateState', [11,7])
                ->whereBetween('Company.periodId', [$peridoInicio,$peridoFinal])
                ->orderBy('Company.id', 'ASC')
                ->select('Company.id as idComp','Company.rut as rutComp','Company.dv as dvComp','Company.name as nameComp','Company.mainCompanyName','Company.companyTypeId','Company.mainCompanyRut','Company.center','Company.certificateState','Company.certificateDate','Company.periodId','Company.subcontratistaRut','Company.subcontratistaName','Company.subcontratistaDv','Worker.rut','Worker.dv','Worker.names','Worker.firstLastName','Worker.secondLastName')->chunk($countEmpresasContratista, function ($query) use (&$WORKCER2,&$WORKCER,$fechaInicial,$fechaFinal){
                        function periodoTexto($idPerido){

                            $periodo = DB::table('Period')
                            ->join('Month', 'Month.id', '=', 'Period.monthId')
                            ->where(['Period.id' => $idPerido])
                            ->select('Period.year','Month.name')
                            ->get();

                            return $periodo[0]->name."-".$periodo[0]->year;
                        }

                        function estadoCerficacionTexto($idEstadoCert){

                            switch ((int)$idEstadoCert) {
                                case 1:
                                    return $estadoCerficacionTexto ="Ingresado";
                                    break;
                                case 2:
                                    return $estadoCerficacionTexto ="Solicitado";
                                    break;
                                case 3:
                                    return $estadoCerficacionTexto ="Aprobado";
                                    break;
                                case 4:
                                    return $estadoCerficacionTexto ="No Aprobado";
                                    break;
                                case 5:
                                    return $estadoCerficacionTexto ="Certificado";
                                    break;
                                case 6:
                                    return $estadoCerficacionTexto ="Documentado";
                                    break;
                                case 7:
                                    return $estadoCerficacionTexto ="Histórico";
                                    break;
                                case 8:
                                    return $estadoCerficacionTexto ="Completo";
                                    break;
                                case 9:
                                    return $estadoCerficacionTexto ="En Proceso";
                                    break;
                                case 10:
                                    return $estadoCerficacionTexto ="No Conforme";
                                    break;
                                case 11:
                                    return $estadoCerficacionTexto ="Inactivo";
                                    break;
                            }
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
                                    $dv = 'K';
                                }
                                if ($dv == 11) {
                                    $dv = '0';
                                }
                            }
                            return number_format($rut, 0, ",", "") . '-' . $dv;
                        }
                    foreach((array)$query as $empresasContratista){
                        unset($Datoscertificacion);
                        unset($datosAcceso);
                        unset($documentosTrabObligatorios);
                        if(!empty($empresasContratista)){

                            foreach ($empresasContratista as $contratista) {
               
                                if($contratista->companyTypeId == 1){
                                    $rutContratista = $contratista->rutComp."-".$contratista->dvComp; 
                                    $nombreContratista = ucwords(mb_strtolower($contratista->nameComp,'UTF-8')); 
                                    $rutSubContratista = "";
                                    $nombreSubContratista = "";
                                    $rutContratista2 = $contratista->rutComp;
                                    $rutContratistasinDV = $contratista->rutComp;
                                }
                                if($contratista->companyTypeId == 2){
                                    $rutContratista = $contratista->subcontratistaRut."-".$contratista->subcontratistaDv;
                                    $nombreContratista =  ucwords(mb_strtolower($contratista->subcontratistaName,'UTF-8'));  
                                    $rutSubContratista = $contratista->rutComp."-".$contratista->dvComp; 
                                    $nombreSubContratista = ucwords(mb_strtolower($contratista->nameComp,'UTF-8')); 
                                    $rutContratista2 = $contratista->rutComp;
                                    $rutContratistasinDV = $contratista->subcontratistaRut;
                                }

                                $peridoTex = periodoTexto($contratista->periodId);
                                $estadoCerficacionTexto = estadoCerficacionTexto($contratista->certificateState);
                                $fechaCertificiacion=date('d/m/Y', $contratista->certificateDate);
                                $rutTrabajadorCert = $contratista->rut.'-'.$contratista->dv;
                                $Datoscertificacion['rutTrabajador'] = $rutTrabajadorCert; 
                                $Datoscertificacion['nombreTrabajador'] = ucwords(mb_strtolower($contratista->names,'UTF-8')); 
                                $Datoscertificacion['apellido1Trabajador'] = ucwords(mb_strtolower($contratista->firstLastName,'UTF-8'));
                                $Datoscertificacion['apellido2Trabajador'] = ucwords(mb_strtolower($contratista->secondLastName,'UTF-8')); 
                                $Datoscertificacion['idComp'] = $contratista->idComp; 
                                $Datoscertificacion['rutPrincipal'] = formatRut($contratista->mainCompanyRut); 
                                $Datoscertificacion['nombrePrincipal'] = ucwords(mb_strtolower($contratista->mainCompanyName,'UTF-8'));    
                                $Datoscertificacion['rutContratista'] = $rutContratista;
                                $Datoscertificacion['nombreContratista'] = $nombreContratista; 
                                $Datoscertificacion['rutSubContratista'] =  $rutSubContratista;
                                $Datoscertificacion['nombreSubContratista'] = $nombreSubContratista;
                                $Datoscertificacion['centroCosto'] = ucwords(mb_strtolower($contratista->center,'UTF-8'));          
                                $Datoscertificacion['perido'] = ucwords(mb_strtolower($peridoTex,'UTF-8'));   
                                $Datoscertificacion['estadoCertificacion'] = ucwords(mb_strtolower($estadoCerficacionTexto,'UTF-8')); 
                                $Datoscertificacion['fechaCertificado'] =  $fechaCertificiacion;
                    

                
                                $datosAcceso =AccesoPersona::where('ACC_RUT',$contratista->rut)
                                ->where('ACC_RUT_CONTRATISTA',$rutContratistasinDV)
                                ->where('ACC_RUT_PPAL',$contratista->mainCompanyRut)
                                ->where('ACC_CENTRO_COSTO',$contratista->center)
                                ->whereDate('ACC_FECHA_ACCESO', '>=', $fechaInicial)
                                ->whereDate('ACC_FECHA_ACCESO', '<=', $fechaFinal)
                                ->take(1)
                                ->orderBy('ACC_FECHA_ACCESO', 'DESC')
                                ->get(['ACC_FECHA_ACCESO'])->toArray();
                               
                                if(!empty($datosAcceso[0]['ACC_FECHA_ACCESO'])){
                                    $Datoscertificacion['ControlAcceso'] =  $datosAcceso[0]['ACC_FECHA_ACCESO'];
                                }else{
                                    $Datoscertificacion['ControlAcceso'] =  "";    
                                } 
                                
                                $empleadoSSO = trabajadorSSO::where('worker_status','1')->where('worker_rut',$rutTrabajadorCert)->get(['id','worker_name','worker_name1','worker_name2','worker_name3','worker_rut','sso_id'])->take(1)->toArray();

                                $EP = $contratista->mainCompanyRut;
                                $empleadoSSO = DB::table('xt_ssov2_header_worker')
                                ->join('xt_ssov2_header', function ($join) use ($EP,$rutContratistasinDV){
                                    $join->where('xt_ssov2_header.sso_mcomp_rut','=',$EP)
                                         ->where('xt_ssov2_header.sso_comp_rut','=',$rutContratistasinDV)
                                         ->on('xt_ssov2_header.id','=','xt_ssov2_header_worker.sso_id');
                                })
                                ->where('worker_status','1')
                                ->where('worker_rut',$rutTrabajadorCert)
                                ->get(['xt_ssov2_header_worker.id','worker_name','worker_name1','worker_name2','worker_name3','worker_rut','worker_cargoid','xt_ssov2_header_worker.sso_id','xt_ssov2_header.sso_cfgid'])
                                ->take(1)->toArray();
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
                                if(!empty($empleadoSSO[0]->id)){
                                    
                                    $documentos = EstadoDocumento::where('upld_sso_id', $empleadoSSO[0]->sso_id)->where('upld_workerid',$empleadoSSO[0]->id)->where('upld_status',1)->where('upld_type',1)->
                                        get(['id','upld_catid','upld_docid','upld_docaprob','upld_venced','upld_vence_date', 'upld_rechazado', 'upld_upddat','upld_docaprob_uid'])->toArray();

                                    $documentosTrabObligatorios = DB::table('xt_ssov2_configs_cargos_cats_docs_params')
                                    ->join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                                    ->where(['xt_ssov2_configs_cargos_cats_docs_params.cfg_id' => $empleadoSSO[0]->sso_cfgid])
                                    ->where(['xt_ssov2_configs_cargos_cats_docs_params.cargo_id' => $empleadoSSO[0]->worker_cargoid])
                                    ->where(['xt_ssov2_doctypes.doc_status' => 1])
                                    ->distinct('xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                                    ->get(['xt_ssov2_configs_cargos_cats_docs_params.doc_id'])->count();

                                    $totalDoc = count($documentos);
                                    if(!empty($documentos[0]['id'])){ 

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
                                        $porcentajeApro = ($totalDocAprobados / $documentosTrabObligatorios * 100);
                                        if($porcentajeApro>=100){
                                            $cantidadcien +=1; 
                                        }else{
                                            $noAcreditado +=1;
                                        }
                                       
                                        $Datoscertificacion["porcentajeTrabajador"] =  number_format($porcentajeApro, 2, '.', '');
                                    }else{
                                        $Datoscertificacion["porcentajeTrabajador"] =  0;
                                    }

                                }else{
                                    
                                    $rutTrabajadorD = number_format($contratista->rut, 0, "", ".") . '-' .$contratista->dv;
                               
                                    $empleadoSSOD =  DB::table('xt_ssov2_header_worker')
                                    ->join('xt_ssov2_header', function ($join) use ($EP,$rutContratistasinDV){
                                        $join->where('xt_ssov2_header.sso_mcomp_rut','=',$EP)
                                             ->where('xt_ssov2_header.sso_comp_rut','=',$rutContratistasinDV)
                                             ->on('xt_ssov2_header.id','=','xt_ssov2_header_worker.sso_id');
                                    })
                                    ->where('worker_status','1')
                                    ->where('worker_rut',$rutTrabajadorD)
                                    ->get(['xt_ssov2_header_worker.id','worker_name','worker_name1','worker_name2','worker_name3','worker_rut','worker_cargoid','xt_ssov2_header_worker.sso_id','xt_ssov2_header.sso_cfgid'])
                                    ->take(1)->toArray();
                                   
                                    if(!empty($empleadoSSOD[0]->id)){
                                    
                                        $documentos = EstadoDocumento::where('upld_sso_id', $empleadoSSOD[0]->sso_id)->where('upld_workerid',$empleadoSSOD[0]->id)->where('upld_status',1)->where('upld_type',1)->
                                        get(['id','upld_catid','upld_docid','upld_docaprob','upld_venced','upld_vence_date', 'upld_rechazado', 'upld_upddat','upld_docaprob_uid'])->toArray();

                                        $documentosTrabObligatorios = DB::table('xt_ssov2_configs_cargos_cats_docs_params')
                                        ->join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                                        ->where(['xt_ssov2_configs_cargos_cats_docs_params.cfg_id' => $empleadoSSOD[0]->sso_cfgid])
                                        ->where(['xt_ssov2_configs_cargos_cats_docs_params.cargo_id' => $empleadoSSOD[0]->worker_cargoid])
                                        ->where(['xt_ssov2_doctypes.doc_status' => 1])
                                        ->distinct('xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                                        ->get(['xt_ssov2_configs_cargos_cats_docs_params.doc_id'])->count();
                                    
                                        $totalDoc = count($documentos);
                                       
                                        if(!empty($documentos[0]['id'])){ 

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
                                            $porcentajeApro = ($totalDocAprobados / $documentosTrabObligatorios * 100);
                                            if($porcentajeApro>=100){
                                                $cantidadcien +=1; 
                                            }else{
                                                $noAcreditado +=1;
                                            }

                                            $Datoscertificacion["porcentajeTrabajador"] =  number_format($porcentajeApro, 2, '.', '');
                                        }else{
                                            $Datoscertificacion["porcentajeTrabajador"] =  0;
                                        }
                                    }else{
                                        $Datoscertificacion["porcentajeTrabajador"] = "";

                                    }
                                }
                                ///llenamos lista de datos ////
                                $WORKCER[] = $Datoscertificacion;
                            } 
                        }
                    }
                    $WORKCER = $WORKCER2;
                });

              /*  $WORKCERSSO = array();
                $WORKCERSSO2 = array();
                $countEmpresasSSO= DB::table('xt_ssov2_header')
                ->join('xt_ssov2_header_worker', function ($join) use ($value){
                    $join->on('xt_ssov2_header_worker.sso_id','=','xt_ssov2_header.id');
                })
                ->whereIn('xt_ssov2_header.sso_mcomp_rut',$rutprincipalR)
                ->whereIn('xt_ssov2_header.sso_comp_rut',$rutcontratistasR)
                ->where('xt_ssov2_header.sso_status',1)
                ->where('xt_ssov2_header_worker.worker_status',1)
                ->orderBy('xt_ssov2_header.id', 'ASC')->count();

                $empresasSSO= DB::table('xt_ssov2_header')
                ->join('xt_ssov2_header_worker', function ($join) use ($value){
                    $join->on('xt_ssov2_header_worker.sso_id','=','xt_ssov2_header.id');
                })
                ->whereIn('xt_ssov2_header.sso_mcomp_rut',$rutprincipalR)
                ->whereIn('xt_ssov2_header.sso_comp_rut',$rutcontratistasR)
                ->where('xt_ssov2_header.sso_status',1)
                ->where('xt_ssov2_header_worker.worker_status',1)
                ->orderBy('xt_ssov2_header.id', 'ASC')
                ->select('xt_ssov2_header.id as folio','xt_ssov2_header.sso_mcomp_rut','xt_ssov2_header.sso_mcomp_dv','xt_ssov2_header.sso_mcomp_name','xt_ssov2_header.sso_comp_rut','xt_ssov2_header.sso_comp_dv','xt_ssov2_header.sso_comp_name','xt_ssov2_header.sso_subcomp_active','xt_ssov2_header.sso_subcomp_rut','xt_ssov2_header.sso_subcomp_name','xt_ssov2_header_worker.id','xt_ssov2_header_worker.worker_name','xt_ssov2_header_worker.worker_name1','xt_ssov2_header_worker.worker_name2','xt_ssov2_header_worker.worker_name3','xt_ssov2_header_worker.worker_rut')->chunk($countEmpresasSSO, function ($query) use (&$WORKCERSSO,&$WORKCERSSO2){
                    
                    foreach((array)$query as $empresasSSO){

                        if(!empty($empresasSSO[0])){

                            foreach ($empresasSSO as $ssot) {

                                $rutTraSSO = $ssot->worker_rut;
                                $findme   = '.';
                                $pos = strpos($ssot->worker_rut, $findme);

                                if($pos === false) {
                                    $rut = explode("-",$ssot->worker_rut);
                                    $rutLimpio = $rut[0];
                                    $dvrut = substr($ssot->worker_rut,-1);
                                }else{
                                    $rut2 = str_replace(".", "", $ssot->worker_rut);
                                    $rut = explode("-",$rut2);
                                    $rutLimpio = $rut[0];
                                    $dvrut = substr($ssot->worker_rut,-1);
                                }
                                $rutTrabajadorSSO = $rutLimpio."-".$dvrut;
                                $datosAcceso =AccesoPersona::where('ACC_RUT',$rutLimpio)->take(1)->get(['ACC_FECHA_ACCESO','ACC_CENTRO_COSTO'])->toArray();
               

                                $DatoSSO['rutTrabajador'] = $rutTrabajadorSSO; 
                                $DatoSSO['nombreTrabajador'] = ucwords(mb_strtolower($ssot->worker_name1,'UTF-8')); 
                                $DatoSSO['apellido1Trabajador'] = ucwords(mb_strtolower($ssot->worker_name2,'UTF-8'));
                                $DatoSSO['apellido2Trabajador'] = ucwords(mb_strtolower($ssot->worker_name3,'UTF-8')); 
                                $DatoSSO['idComp'] = $ssot->folio; 
                                $DatoSSO['rutPrincipal'] = formatRut($ssot->sso_mcomp_rut); 
                                $DatoSSO['nombrePrincipal'] = ucwords(mb_strtolower($ssot->sso_mcomp_name,'UTF-8'));    
                                $DatoSSO['rutContratista'] = formatRut($ssot->sso_comp_rut);;
                                $DatoSSO['nombreContratista'] = ucwords(mb_strtolower($ssot->sso_comp_name,'UTF-8')); 
                                if($ssot->sso_subcomp_active == 1){
                                    $DatoSSO['rutSubContratista'] =  formatRut($ssot->sso_subcomp_rut);
                                    $DatoSSO['nombreSubContratista'] = ucwords(mb_strtolower($ssot->sso_subcomp_name,'UTF-8'));
                                }else{
                                    $DatoSSO['rutSubContratista'] =  "";
                                    $DatoSSO['nombreSubContratista'] = "";    
                                }   
                                if(!empty($datosAcceso[0]['ACC_FECHA_ACCESO'])){
                                    $DatoSSO['ControlAcceso'] =  $datosAcceso[0]['ACC_FECHA_ACCESO'];
                                    $DatoSSO['centroCosto'] = ucwords(mb_strtolower($datosAcceso[0]['ACC_CENTRO_COSTO'],'UTF-8')); 
                                }else{
                                    $DatoSSO['ControlAcceso'] =  ""; 
                                    $DatoSSO['centroCosto'] = "";   
                                }   
                                $DatoSSO['perido'] = "";   
                                $DatoSSO['estadoCertificacion'] = ""; 
                                $DatoSSO['fechaCertificado'] =  "";

                                $documentos = EstadoDocumento::where('upld_sso_id', $ssot->folio)->where('upld_workerid',$ssot->id)->where('upld_status',1)->where('upld_type',1)->
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
                                    if($porcentajeApro>=100){
                                        $cantidadcien +=1; 
                                    }else{
                                        $noAcreditado +=1;
                                    }

                                    $DatoSSO["porcentajeTrabajador"] =  number_format($porcentajeApro, 2, '.', ''); 
                                }else{
                                    $DatoSSO["porcentajeTrabajador"] =  0;
                                }

                                $WORKCERSSO2[] = $DatoSSO; 
                            }
                        }
                    }
                     $WORKCERSSO = $WORKCERSSO2;
                }); */
                $WORKS = $WORKCER;
                if(!empty($WORKS)){

                    Excel::create('Reporte Cruzado', function($excel) use ($WORKS) {

                        $excel->sheet('Datos', function($sheet) use($WORKS) {    
                            $sheet->loadView('reporteTraCSA.excel',compact('WORKS'));
                        });
                    })->export('xls'); 
                }            

            }if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista == 0 AND $centroCosto == 0){

                $countEmpresasContratista= DB::table('Company')
                ->join('Worker', function ($join) use ($value){
                    $join->on('Worker.mainCompanyRut','=','Company.mainCompanyRut')
                        ->on('Worker.companyRut','=','Company.rut')
                        ->on('Worker.periodId','=','Company.periodId')
                        ->on('Worker.CompanyCenter','=','Company.center');
                })
                ->whereIn('Company.mainCompanyRut',$rutprincipalR)
                ->whereBetween('Company.periodId', [$peridoInicio,$peridoFinal])
                ->whereNotIn('Company.certificateState', [11,7])
                ->orderBy('Company.id', 'ASC')->count();

                $WORKCER2 = array();
                $WORKCER = array();

                $empresasContratista= DB::table('Company')
                ->join('Worker', function ($join) use ($value){
                    $join->on('Worker.mainCompanyRut','=','Company.mainCompanyRut')
                        ->on('Worker.companyRut','=','Company.rut')
                        ->on('Worker.periodId','=','Company.periodId')
                        ->on('Worker.CompanyCenter','=','Company.center');
                })
                ->whereIn('Company.mainCompanyRut',$rutprincipalR)
                ->whereBetween('Company.periodId', [$peridoInicio,$peridoFinal])
                ->whereNotIn('Company.certificateState', [11,7])
                ->orderBy('Company.id', 'ASC')
                ->select('Company.id as idComp','Company.rut as rutComp','Company.dv as dvComp','Company.name as nameComp','Company.mainCompanyName','Company.companyTypeId','Company.mainCompanyRut','Company.center','Company.certificateState','Company.certificateDate','Company.periodId','Company.subcontratistaRut','Company.subcontratistaName','Company.subcontratistaDv','Worker.rut','Worker.dv','Worker.names','Worker.firstLastName','Worker.secondLastName')->chunk($countEmpresasContratista, function ($query) use (&$WORKCER2,&$WORKCER,$fechaInicial,$fechaFinal){

                        function periodoTexto($idPerido){

                            $periodo = DB::table('Period')
                            ->join('Month', 'Month.id', '=', 'Period.monthId')
                            ->where(['Period.id' => $idPerido])
                            ->select('Period.year','Month.name')
                            ->get();

                            return $periodo[0]->name."-".$periodo[0]->year;
                        }

                        function estadoCerficacionTexto($idEstadoCert){

                            switch ((int)$idEstadoCert) {
                                case 1:
                                    return $estadoCerficacionTexto ="Ingresado";
                                    break;
                                case 2:
                                    return $estadoCerficacionTexto ="Solicitado";
                                    break;
                                case 3:
                                    return $estadoCerficacionTexto ="Aprobado";
                                    break;
                                case 4:
                                    return $estadoCerficacionTexto ="No Aprobado";
                                    break;
                                case 5:
                                    return $estadoCerficacionTexto ="Certificado";
                                    break;
                                case 6:
                                    return $estadoCerficacionTexto ="Documentado";
                                    break;
                                case 7:
                                    return $estadoCerficacionTexto ="Histórico";
                                    break;
                                case 8:
                                    return $estadoCerficacionTexto ="Completo";
                                    break;
                                case 9:
                                    return $estadoCerficacionTexto ="En Proceso";
                                    break;
                                case 10:
                                    return $estadoCerficacionTexto ="No Conforme";
                                    break;
                                case 11:
                                    return $estadoCerficacionTexto ="Inactivo";
                                    break;
                            }
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
                                    $dv = 'K';
                                }
                                if ($dv == 11) {
                                    $dv = '0';
                                }
                            }
                            return number_format($rut, 0, ",", "") . '-' . $dv;
                        }

                        foreach((array)$query as $empresasContratista){                            
                            unset($Datoscertificacion);
                            unset($datosAcceso);
                            unset($documentosTrabObligatorios);
                            if(!empty($empresasContratista)){

                                foreach ($empresasContratista as $contratista) {
                   
                                if($contratista->companyTypeId == 1){
                                    $rutContratista = $contratista->rutComp."-".$contratista->dvComp; 
                                    $nombreContratista = ucwords(mb_strtolower($contratista->nameComp,'UTF-8')); 
                                    $rutSubContratista = "";
                                    $nombreSubContratista = "";
                                    $rutContratista2 = $contratista->rutComp;
                                    $rutContratistasinDV = $contratista->rutComp;
                                }
                                if($contratista->companyTypeId == 2){
                                    $rutContratista = $contratista->subcontratistaRut."-".$contratista->subcontratistaDv;
                                    $nombreContratista =  ucwords(mb_strtolower($contratista->subcontratistaName,'UTF-8'));  
                                    $rutSubContratista = $contratista->rutComp."-".$contratista->dvComp; 
                                    $nombreSubContratista = ucwords(mb_strtolower($contratista->nameComp,'UTF-8')); 
                                    $rutContratista2 = $contratista->rutComp;
                                    $rutContratistasinDV = $contratista->subcontratistaRut;
                                }

                                    $peridoTex = periodoTexto($contratista->periodId);
                                    $estadoCerficacionTexto = estadoCerficacionTexto($contratista->certificateState);
                                    $fechaCertificiacion=date('d/m/Y', $contratista->certificateDate);
                                    $rutTrabajadorCert = $contratista->rut.'-'.$contratista->dv;
                                    $Datoscertificacion['rutTrabajador'] = $rutTrabajadorCert; 
                                    $Datoscertificacion['nombreTrabajador'] = ucwords(mb_strtolower($contratista->names,'UTF-8')); 
                                    $Datoscertificacion['apellido1Trabajador'] = ucwords(mb_strtolower($contratista->firstLastName,'UTF-8'));
                                    $Datoscertificacion['apellido2Trabajador'] = ucwords(mb_strtolower($contratista->secondLastName,'UTF-8')); 
                                    $Datoscertificacion['idComp'] = $contratista->idComp; 
                                    $Datoscertificacion['rutPrincipal'] = formatRut($contratista->mainCompanyRut); 
                                    $Datoscertificacion['nombrePrincipal'] = ucwords(mb_strtolower($contratista->mainCompanyName,'UTF-8'));    
                                    $Datoscertificacion['rutContratista'] = $rutContratista;
                                    $Datoscertificacion['nombreContratista'] = $nombreContratista; 
                                    $Datoscertificacion['rutSubContratista'] =  $rutSubContratista;
                                    $Datoscertificacion['nombreSubContratista'] = $nombreSubContratista;
                                    $Datoscertificacion['centroCosto'] = ucwords(mb_strtolower($contratista->center,'UTF-8'));          
                                    $Datoscertificacion['perido'] = ucwords(mb_strtolower($peridoTex,'UTF-8'));   
                                    $Datoscertificacion['estadoCertificacion'] = ucwords(mb_strtolower($estadoCerficacionTexto,'UTF-8')); 
                                    $Datoscertificacion['fechaCertificado'] =  $fechaCertificiacion;
                                   
                                    $datosAcceso =AccesoPersona::where('ACC_RUT',$contratista->rut)
                                    ->where('ACC_RUT_CONTRATISTA',$rutContratistasinDV)
                                    ->where('ACC_RUT_PPAL',$contratista->mainCompanyRut)
                                    ->where('ACC_CENTRO_COSTO',$contratista->center)
                                    ->whereDate('ACC_FECHA_ACCESO', '>=', $fechaInicial)
                                    ->whereDate('ACC_FECHA_ACCESO', '<=', $fechaFinal)
                                    ->take(1)
                                    ->orderBy('ACC_FECHA_ACCESO', 'desc')
                                    ->get(['ACC_FECHA_ACCESO'])->toArray();
                                    if(!empty($datosAcceso[0]['ACC_FECHA_ACCESO'])){
                                        $Datoscertificacion['ControlAcceso'] =  $datosAcceso[0]['ACC_FECHA_ACCESO'];
                                    }else{
                                        $Datoscertificacion['ControlAcceso'] =  "";    
                                    } 
                                    
                                    $EP = $contratista->mainCompanyRut;
                                    $empleadoSSO = DB::table('xt_ssov2_header_worker')
                                    ->join('xt_ssov2_header', function ($join) use ($EP,$rutContratistasinDV){
                                        $join->where('xt_ssov2_header.sso_mcomp_rut','=',$EP)
                                             ->where('xt_ssov2_header.sso_comp_rut','=',$rutContratistasinDV)
                                             ->on('xt_ssov2_header.id','=','xt_ssov2_header_worker.sso_id');
                                    })
                                    ->where('worker_status','1')
                                    ->where('worker_rut',$rutTrabajadorCert)
                                    ->get(['xt_ssov2_header_worker.id','worker_name','worker_name1','worker_name2','worker_name3','worker_rut','worker_cargoid','xt_ssov2_header_worker.sso_id','xt_ssov2_header.sso_cfgid'])
                                    ->take(1)->toArray();
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
                                    if(!empty($empleadoSSO[0]->id)){
                                       

                                        $documentosTrabObligatorios = DB::table('xt_ssov2_configs_cargos_cats_docs_params')
                                        ->join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                                        ->where(['xt_ssov2_configs_cargos_cats_docs_params.cfg_id' => $empleadoSSO[0]->sso_cfgid])
                                        ->where(['xt_ssov2_configs_cargos_cats_docs_params.cargo_id' => $empleadoSSO[0]->worker_cargoid])
                                        ->where(['xt_ssov2_doctypes.doc_status' => 1])
                                        ->distinct('xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                                        ->get(['xt_ssov2_configs_cargos_cats_docs_params.doc_id'])->count();
                                    
                                        $documentos = EstadoDocumento::where('upld_sso_id', $empleadoSSO[0]->sso_id)->where('upld_workerid',$empleadoSSO[0]->id)->where('upld_status',1)->where('upld_type',1)->
                                        get(['id','upld_catid','upld_docid','upld_docaprob','upld_venced','upld_vence_date', 'upld_rechazado', 'upld_upddat','upld_docaprob_uid'])->toArray();

                                        $totalDoc = count($documentos);
                                        if(!empty($documentos[0]['id'])){ 

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
                                            $porcentajeApro = ($totalDocAprobados / $documentosTrabObligatorios * 100);
                                            if($porcentajeApro>=100){
                                                $cantidadcien +=1; 
                                            }else{
                                                $noAcreditado +=1;
                                            }
                                       
                                            $Datoscertificacion["porcentajeTrabajador"] =  number_format($porcentajeApro, 2, '.', '');
                                        }else{
                                            $Datoscertificacion["porcentajeTrabajador"] =  0;
                                        }
                                    }else{
                                    
                                        $rutTrabajadorD = number_format($contratista->rut, 0, "", ".") . '-' .$contratista->dv;
                               
                                        $empleadoSSOD =  DB::table('xt_ssov2_header_worker')
                                        ->join('xt_ssov2_header', function ($join) use ($EP,$rutContratistasinDV){
                                            $join->where('xt_ssov2_header.sso_mcomp_rut','=',$EP)
                                                 ->where('xt_ssov2_header.sso_comp_rut','=',$rutContratistasinDV)
                                                 ->on('xt_ssov2_header.id','=','xt_ssov2_header_worker.sso_id');
                                        })
                                        ->where('worker_status','1')
                                        ->where('worker_rut',$rutTrabajadorD)
                                        ->get(['xt_ssov2_header_worker.id','worker_name','worker_name1','worker_name2','worker_name3','worker_rut','worker_cargoid','xt_ssov2_header_worker.sso_id','xt_ssov2_header.sso_cfgid'])
                                        ->take(1)->toArray();

                                       
                                   
                                        if(!empty($empleadoSSOD[0]->id)){

                                            $documentosTrabObligatorios = DB::table('xt_ssov2_configs_cargos_cats_docs_params')
                                            ->join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                                            ->where(['xt_ssov2_configs_cargos_cats_docs_params.cfg_id' => $empleadoSSOD[0]->sso_cfgid])
                                            ->where(['xt_ssov2_configs_cargos_cats_docs_params.cargo_id' => $empleadoSSOD[0]->worker_cargoid])
                                            ->where(['xt_ssov2_doctypes.doc_status' => 1])
                                            ->distinct('xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                                            ->get(['xt_ssov2_configs_cargos_cats_docs_params.doc_id'])->count();
                                    
                                        
                                            $documentos = EstadoDocumento::where('upld_sso_id', $empleadoSSOD[0]->sso_id)->where('upld_workerid',$empleadoSSOD[0]->id)->where('upld_status',1)->where('upld_type',1)->
                                            get(['id','upld_catid','upld_docid','upld_docaprob','upld_venced','upld_vence_date', 'upld_rechazado', 'upld_upddat','upld_docaprob_uid'])->toArray();
                                            
                                            $totalDoc = count($documentos);
                                           
                                            if(!empty($documentos[0]['id'])){ 

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
                                                 
                                                $porcentajeApro = ($totalDocAprobados / $documentosTrabObligatorios * 100);
                                                if($porcentajeApro>=100){
                                                    $cantidadcien +=1; 
                                                }else{
                                                    $noAcreditado +=1;
                                                }

                                                $Datoscertificacion["porcentajeTrabajador"] =  number_format($porcentajeApro, 2, '.', '');
                                            }else{
                                                $Datoscertificacion["porcentajeTrabajador"] =  0;
                                            }
                                        }else{
                                            $Datoscertificacion["porcentajeTrabajador"] = "";

                                        }
                                    }
                                    ///llenamos lista de datos ////
                                    $WORKCER2[] = $Datoscertificacion;
                                }
                            }
                        }
                        $WORKCER = $WORKCER2;
                    });


/*                $WORKCERSSO = array();
                $WORKCERSSO2 = array();
                $countEmpresasSSO= DB::table('xt_ssov2_header')
                ->join('xt_ssov2_header_worker', function ($join) use ($value){
                    $join->on('xt_ssov2_header_worker.sso_id','=','xt_ssov2_header.id');
                })
                ->whereIn('xt_ssov2_header.sso_mcomp_rut',$rutprincipalR)
                ->where('xt_ssov2_header.sso_status',1)
                ->where('xt_ssov2_header_worker.worker_status',1)
                ->orderBy('xt_ssov2_header.id', 'ASC')->count();

                $empresasSSO= DB::table('xt_ssov2_header')
                ->join('xt_ssov2_header_worker', function ($join) use ($value){
                    $join->on('xt_ssov2_header_worker.sso_id','=','xt_ssov2_header.id');
                })
                ->whereIn('xt_ssov2_header.sso_mcomp_rut',$rutprincipalR)
                ->where('xt_ssov2_header.sso_status',1)
                ->where('xt_ssov2_header_worker.worker_status',1)
                ->orderBy('xt_ssov2_header.id', 'ASC')
                ->select('xt_ssov2_header.id as folio','xt_ssov2_header.sso_mcomp_rut','xt_ssov2_header.sso_mcomp_dv','xt_ssov2_header.sso_mcomp_name','xt_ssov2_header.sso_comp_rut','xt_ssov2_header.sso_comp_dv','xt_ssov2_header.sso_comp_name','xt_ssov2_header.sso_subcomp_active','xt_ssov2_header.sso_subcomp_rut','xt_ssov2_header.sso_subcomp_name','xt_ssov2_header_worker.id','xt_ssov2_header_worker.worker_name','xt_ssov2_header_worker.worker_name1','xt_ssov2_header_worker.worker_name2','xt_ssov2_header_worker.worker_name3','xt_ssov2_header_worker.worker_rut')->chunk($countEmpresasSSO, function ($query) use (&$WORKCERSSO,&$WORKCERSSO2){

                   
                    foreach((array)$query as $empresasSSO){

                        if(!empty($empresasSSO[0])){

                            foreach ($empresasSSO as $ssot) {

                                $rutTraSSO = $ssot->worker_rut;
                                $findme   = '.';
                                $pos = strpos($ssot->worker_rut, $findme);

                                if($pos === false) {
                                    $rut = explode("-",$ssot->worker_rut);
                                    $rutLimpio = $rut[0];
                                    $dvrut = substr($ssot->worker_rut,-1);
                                }else{
                                    $rut2 = str_replace(".", "", $ssot->worker_rut);
                                    $rut = explode("-",$rut2);
                                    $rutLimpio = $rut[0];
                                    $dvrut = substr($ssot->worker_rut,-1);
                                }
                                $rutTrabajadorSSO = $rutLimpio."-".$dvrut;
                                $datosAcceso =AccesoPersona::where('ACC_RUT',$rutLimpio)->take(1)->get(['ACC_FECHA_ACCESO','ACC_CENTRO_COSTO'])->toArray();
               

                                $DatoSSO['rutTrabajador'] = $rutTrabajadorSSO; 
                                $DatoSSO['nombreTrabajador'] = ucwords(mb_strtolower($ssot->worker_name1,'UTF-8')); 
                                $DatoSSO['apellido1Trabajador'] = ucwords(mb_strtolower($ssot->worker_name2,'UTF-8'));
                                $DatoSSO['apellido2Trabajador'] = ucwords(mb_strtolower($ssot->worker_name3,'UTF-8')); 
                                $DatoSSO['idComp'] = $ssot->folio; 
                                $DatoSSO['rutPrincipal'] = formatRut($ssot->sso_mcomp_rut); 
                                $DatoSSO['nombrePrincipal'] = ucwords(mb_strtolower($ssot->sso_mcomp_name,'UTF-8'));    
                                $DatoSSO['rutContratista'] = formatRut($ssot->sso_comp_rut);;
                                $DatoSSO['nombreContratista'] = ucwords(mb_strtolower($ssot->sso_comp_name,'UTF-8')); 
                                if($ssot->sso_subcomp_active == 1){
                                    $DatoSSO['rutSubContratista'] =  formatRut($ssot->sso_subcomp_rut);
                                    $DatoSSO['nombreSubContratista'] = ucwords(mb_strtolower($ssot->sso_subcomp_name,'UTF-8'));
                                }else{
                                    $DatoSSO['rutSubContratista'] =  "";
                                    $DatoSSO['nombreSubContratista'] = "";    
                                }   
                                if(!empty($datosAcceso[0]['ACC_FECHA_ACCESO'])){
                                    $DatoSSO['ControlAcceso'] =  $datosAcceso[0]['ACC_FECHA_ACCESO'];
                                    $DatoSSO['centroCosto'] = ucwords(mb_strtolower($datosAcceso[0]['ACC_CENTRO_COSTO'],'UTF-8')); 
                                }else{
                                    $DatoSSO['ControlAcceso'] =  ""; 
                                    $DatoSSO['centroCosto'] = "";   
                                }   
                                $DatoSSO['perido'] = "";   
                                $DatoSSO['estadoCertificacion'] = ""; 
                                $DatoSSO['fechaCertificado'] =  "";

                                $documentos = EstadoDocumento::where('upld_sso_id', $ssot->folio)->where('upld_workerid',$ssot->id)->where('upld_status',1)->where('upld_type',1)->
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
                                    if($porcentajeApro>=100){
                                        $cantidadcien +=1; 
                                    }else{
                                        $noAcreditado +=1;
                                    }

                                    $DatoSSO["porcentajeTrabajador"] =  number_format($porcentajeApro, 2, '.', ''); 
                                }else{
                                    $DatoSSO["porcentajeTrabajador"] =  0;
                                }

                                $WORKCERSSO2[] = $DatoSSO; 
                            }
                        }
                    }
                    $WORKCERSSO = $WORKCERSSO2;
                });*/

               

                $WORKS = $WORKCER;
                if(!empty($WORKS)){

                    Excel::create('Reporte Cruzado', function($excel) use ($WORKS) {

                        $excel->sheet('Datos', function($sheet) use($WORKS) {    
                            $sheet->loadView('reporteTraCSA.excel',compact('WORKS'));
                        });
                    })->export('xls'); 
                }
            }
        }
        if($tipoBsuqueda == 2){

            $fechaSeleccion = $input["fechaSeleccion"];
            if($fechaSeleccion != 0  AND $countContratista != 0 AND $centroCosto != 0){
                 
                $WORKCER2 = array();
                $WORKCER = array();

                $fechas = $porciones = explode("_", $fechaSeleccion);
                $fecha1 = $fechas[0];
                $fecha2 = $fechas[1];
                
                $fechaInicial = date("Y-m-d", strtotime($fecha1));
                $fechaFinal = date("Y-m-d", strtotime($fecha2));

                $periodosT = $fecha1 ."-".$fecha2;
                $fechasDesde = strtotime ( '+4 hour' ,strtotime($fecha1));
                //sumo 1 día
                $fechasHasta = strtotime ( '+4 hour' ,strtotime($fecha2));

                $countEmpresasContratista= DB::table('Company')
                ->join('Worker', function ($join) use ($value){
                    $join->on('Worker.mainCompanyRut','=','Company.mainCompanyRut')
                        ->on('Worker.companyRut','=','Company.rut')
                        ->on('Worker.periodId','=','Company.periodId')
                        ->on('Worker.CompanyCenter','=','Company.center');
                })
                ->whereIn('Company.mainCompanyRut',$rutprincipalR)
                ->whereIn('Company.rut',$rutcontratistasR)
                ->whereNotIn('Company.certificateState', [11,7])
                ->whereBetween('Company.certificateDate', [$fechasDesde,$fechasHasta])
                ->where('Company.id',$centroCosto)
                ->orderBy('Company.id', 'ASC')->count();

                $empresasContratista= DB::table('Company')
                ->join('Worker', function ($join) use ($value){
                    $join->on('Worker.mainCompanyRut','=','Company.mainCompanyRut')
                        ->on('Worker.companyRut','=','Company.rut')
                        ->on('Worker.periodId','=','Company.periodId')
                        ->on('Worker.CompanyCenter','=','Company.center');
                })
                ->whereIn('Company.mainCompanyRut',$rutprincipalR)
                ->whereIn('Company.rut',$rutcontratistasR)
                ->whereNotIn('Company.certificateState', [11,7])
                ->whereBetween('Company.certificateDate', [$fechasDesde,$fechasHasta])
                ->where('Company.id',$centroCosto)
                ->orderBy('Company.id', 'ASC')
                ->select('Company.id as idComp','Company.rut as rutComp','Company.dv as dvComp','Company.name as nameComp','Company.mainCompanyName','Company.companyTypeId','Company.mainCompanyRut','Company.center','Company.certificateState','Company.certificateDate','Company.periodId','Company.subcontratistaRut','Company.subcontratistaName','Company.subcontratistaDv','Worker.rut','Worker.dv','Worker.names','Worker.firstLastName','Worker.secondLastName')->chunk($countEmpresasContratista, function ($query) use (&$WORKCER2,&$WORKCER,$fechaInicial,$fechaFinal){

                    function periodoTexto($idPerido){

                    $periodo = DB::table('Period')
                    ->join('Month', 'Month.id', '=', 'Period.monthId')
                    ->where(['Period.id' => $idPerido])
                    ->select('Period.year','Month.name')
                    ->get();

                    return $periodo[0]->name."-".$periodo[0]->year;
                    }

                    function estadoCerficacionTexto($idEstadoCert){

                        switch ((int)$idEstadoCert) {
                            case 1:
                                return $estadoCerficacionTexto ="Ingresado";
                                break;
                            case 2:
                                return $estadoCerficacionTexto ="Solicitado";
                                break;
                            case 3:
                                return $estadoCerficacionTexto ="Aprobado";
                                break;
                            case 4:
                                return $estadoCerficacionTexto ="No Aprobado";
                                break;
                            case 5:
                                return $estadoCerficacionTexto ="Certificado";
                                break;
                            case 6:
                                return $estadoCerficacionTexto ="Documentado";
                                break;
                            case 7:
                                return $estadoCerficacionTexto ="Histórico";
                                break;
                            case 8:
                                return $estadoCerficacionTexto ="Completo";
                                break;
                            case 9:
                                return $estadoCerficacionTexto ="En Proceso";
                                break;
                            case 10:
                                return $estadoCerficacionTexto ="No Conforme";
                                break;
                            case 11:
                                return $estadoCerficacionTexto ="Inactivo";
                                break;
                        }
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
                                $dv = 'K';
                            }
                            if ($dv == 11) {
                                $dv = '0';
                            }
                        }
                        return number_format($rut, 0, ",", "") . '-' . $dv;
                    }

                    foreach((array)$query as $empresasContratista){

                        if(!empty($empresasContratista)){
                            unset($Datoscertificacion);
                            unset($datosAcceso);
                            unset($documentosTrabObligatorios);
                            foreach ($empresasContratista as $contratista) {
               
                                if($contratista->companyTypeId == 1){
                                    $rutContratista = $contratista->rutComp."-".$contratista->dvComp; 
                                    $nombreContratista = ucwords(mb_strtolower($contratista->nameComp,'UTF-8')); 
                                    $rutSubContratista = "";
                                    $nombreSubContratista = "";
                                    $rutContratista2 = $contratista->rutComp;
                                    $rutContratistasinDV = $contratista->rutComp;
                                }
                                if($contratista->companyTypeId == 2){
                                    $rutContratista = $contratista->subcontratistaRut."-".$contratista->subcontratistaDv;
                                    $nombreContratista =  ucwords(mb_strtolower($contratista->subcontratistaName,'UTF-8'));  
                                    $rutSubContratista = $contratista->rutComp."-".$contratista->dvComp; 
                                    $nombreSubContratista = ucwords(mb_strtolower($contratista->nameComp,'UTF-8')); 
                                    $rutContratista2 = $contratista->rutComp;
                                    $rutContratistasinDV = $contratista->subcontratistaRut;
                                }

                                $peridoTex = periodoTexto($contratista->periodId);
                                $estadoCerficacionTexto = estadoCerficacionTexto($contratista->certificateState);
                                $fechaCertificiacion=date('d/m/Y', $contratista->certificateDate);
                                $rutTrabajadorCert = $contratista->rut.'-'.$contratista->dv;
                                $Datoscertificacion['rutTrabajador'] = $rutTrabajadorCert; 
                                $Datoscertificacion['nombreTrabajador'] = ucwords(mb_strtolower($contratista->names,'UTF-8')); 
                                $Datoscertificacion['apellido1Trabajador'] = ucwords(mb_strtolower($contratista->firstLastName,'UTF-8'));
                                $Datoscertificacion['apellido2Trabajador'] = ucwords(mb_strtolower($contratista->secondLastName,'UTF-8')); 
                                $Datoscertificacion['idComp'] = $contratista->idComp; 
                                $Datoscertificacion['rutPrincipal'] = formatRut($contratista->mainCompanyRut); 
                                $Datoscertificacion['nombrePrincipal'] = ucwords(mb_strtolower($contratista->mainCompanyName,'UTF-8'));    
                                $Datoscertificacion['rutContratista'] = $rutContratista;
                                $Datoscertificacion['nombreContratista'] = $nombreContratista; 
                                $Datoscertificacion['rutSubContratista'] =  $rutSubContratista;
                                $Datoscertificacion['nombreSubContratista'] = $nombreSubContratista;
                                $Datoscertificacion['centroCosto'] = ucwords(mb_strtolower($contratista->center,'UTF-8'));          
                                $Datoscertificacion['perido'] = ucwords(mb_strtolower($peridoTex,'UTF-8'));   
                                $Datoscertificacion['estadoCertificacion'] = ucwords(mb_strtolower($estadoCerficacionTexto,'UTF-8')); 
                                $Datoscertificacion['fechaCertificado'] =  $fechaCertificiacion;
                    

                
                                $datosAcceso =AccesoPersona::where('ACC_RUT',$contratista->rut)
                                ->where('ACC_RUT_CONTRATISTA',$rutContratistasinDV)
                                ->where('ACC_RUT_PPAL',$contratista->mainCompanyRut)
                                ->where('ACC_CENTRO_COSTO',$contratista->center)
                                ->whereDate('ACC_FECHA_ACCESO', '>=', $fechaInicial)
                                ->whereDate('ACC_FECHA_ACCESO', '<=', $fechaFinal)
                                ->take(1)
                                ->orderBy('ACC_FECHA_ACCESO', 'DESC')
                                ->get(['ACC_FECHA_ACCESO','ACC_ID'])->toArray();
                                if(!empty($datosAcceso[0]['ACC_FECHA_ACCESO'])){
                                    $Datoscertificacion['ControlAcceso'] =  $datosAcceso[0]['ACC_FECHA_ACCESO'];
                                }else{
                                    $Datoscertificacion['ControlAcceso'] =  "";    
                                } 
                                
            
                                $EP = $contratista->mainCompanyRut;
                                $empleadoSSO = DB::table('xt_ssov2_header_worker')
                                ->join('xt_ssov2_header', function ($join) use ($EP,$rutContratistasinDV){
                                    $join->where('xt_ssov2_header.sso_mcomp_rut','=',$EP)
                                         ->where('xt_ssov2_header.sso_comp_rut','=',$rutContratistasinDV)
                                         ->on('xt_ssov2_header.id','=','xt_ssov2_header_worker.sso_id');
                                })
                                ->where('worker_status','1')
                                ->where('worker_rut',$rutTrabajadorCert)
                                ->get(['xt_ssov2_header_worker.id','worker_name','worker_name1','worker_name2','worker_name3','worker_rut','worker_cargoid','xt_ssov2_header_worker.sso_id','xt_ssov2_header.sso_cfgid'])
                                ->take(1)->toArray();
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
                                if(!empty($empleadoSSO[0]->id)){

                                    $documentosTrabObligatorios = DB::table('xt_ssov2_configs_cargos_cats_docs_params')
                                    ->join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                                    ->where(['xt_ssov2_configs_cargos_cats_docs_params.cfg_id' => $empleadoSSO[0]->sso_cfgid])
                                    ->where(['xt_ssov2_configs_cargos_cats_docs_params.cargo_id' => $empleadoSSO[0]->worker_cargoid])
                                    ->where(['xt_ssov2_doctypes.doc_status' => 1])
                                    ->distinct('xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                                    ->get(['xt_ssov2_configs_cargos_cats_docs_params.doc_id'])->count();
                                    
                                    $documentos = EstadoDocumento::where('upld_sso_id', $empleadoSSO[0]->sso_id)->where('upld_workerid',$empleadoSSO[0]->id)->where('upld_status',1)->where('upld_type',1)->
                                        get(['id','upld_catid','upld_docid','upld_docaprob','upld_venced','upld_vence_date', 'upld_rechazado', 'upld_upddat','upld_docaprob_uid'])->toArray();

                                    $totalDoc = count($documentos);
                                    if(!empty($documentos[0]['id'])){ 

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
                                        $porcentajeApro = ($totalDocAprobados / $documentosTrabObligatorios * 100);
                                        if($porcentajeApro>=100){
                                            $cantidadcien +=1; 
                                        }else{
                                            $noAcreditado +=1;
                                        }
                                       
                                        $Datoscertificacion["porcentajeTrabajador"] =  number_format($porcentajeApro, 2, '.', '');
                                    }else{
                                        $Datoscertificacion["porcentajeTrabajador"] =  0;
                                    }

                                }else{
                                    
                                    $rutTrabajadorD = number_format($contratista->rut, 0, "", ".") . '-' .$contratista->dv;
                               
                                    $empleadoSSOD =  DB::table('xt_ssov2_header_worker')
                                    ->join('xt_ssov2_header', function ($join) use ($EP,$rutContratistasinDV){
                                        $join->where('xt_ssov2_header.sso_mcomp_rut','=',$EP)
                                             ->where('xt_ssov2_header.sso_comp_rut','=',$rutContratistasinDV)
                                             ->on('xt_ssov2_header.id','=','xt_ssov2_header_worker.sso_id');
                                    })
                                    ->where('worker_status','1')
                                    ->where('worker_rut',$rutTrabajadorD)
                                    ->get(['xt_ssov2_header_worker.id','worker_name','worker_name1','worker_name2','worker_name3','worker_rut','worker_cargoid','xt_ssov2_header_worker.sso_id','xt_ssov2_header.sso_cfgid'])
                                    ->take(1)->toArray();
                                   
                                    if(!empty($empleadoSSOD[0]->id)){

                                        $documentosTrabObligatorios = DB::table('xt_ssov2_configs_cargos_cats_docs_params')
                                        ->join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                                        ->where(['xt_ssov2_configs_cargos_cats_docs_params.cfg_id' => $empleadoSSOD[0]->sso_cfgid])
                                        ->where(['xt_ssov2_configs_cargos_cats_docs_params.cargo_id' => $empleadoSSOD[0]->worker_cargoid])
                                        ->where(['xt_ssov2_doctypes.doc_status' => 1])
                                        ->distinct('xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                                        ->get(['xt_ssov2_configs_cargos_cats_docs_params.doc_id'])->count();
                                    
                                        $documentos = EstadoDocumento::where('upld_sso_id', $empleadoSSOD[0]->sso_id)->where('upld_workerid',$empleadoSSOD[0]->id)->where('upld_status',1)->where('upld_type',1)->
                                        get(['id','upld_catid','upld_docid','upld_docaprob','upld_venced','upld_vence_date', 'upld_rechazado', 'upld_upddat','upld_docaprob_uid'])->toArray();
                                        
                                        $totalDoc = count($documentos);
                                       
                                        if(!empty($documentos[0]['id'])){ 

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
                                            $porcentajeApro = ($totalDocAprobados / $documentosTrabObligatorios * 100);
                                            if($porcentajeApro>=100){
                                                $cantidadcien +=1; 
                                            }else{
                                                $noAcreditado +=1;
                                            }

                                            $Datoscertificacion["porcentajeTrabajador"] =  number_format($porcentajeApro, 2, '.', '');
                                        }else{
                                            $Datoscertificacion["porcentajeTrabajador"] =  0;
                                        }
                                    }else{
                                        $Datoscertificacion["porcentajeTrabajador"] = "";

                                    }
                                }
                                ///llenamos lista de datos ////
                                $WORKCER[] = $Datoscertificacion;
                            } 
                        }
                    }
                    $WORKCER = $WORKCER2;
                });

                /*$WORKCERSSO=array();
                $WORKCERSSO2 = array();
                $countEmpresasSSO= DB::table('xt_ssov2_header')
                ->join('xt_ssov2_header_worker', function ($join) use ($value){
                    $join->on('xt_ssov2_header_worker.sso_id','=','xt_ssov2_header.id');
                })
                ->whereIn('xt_ssov2_header.sso_mcomp_rut',$rutprincipalR)
                ->whereIn('xt_ssov2_header.sso_comp_rut',$rutcontratistasR)
                ->where('xt_ssov2_header.sso_status',1)
                ->where('xt_ssov2_header_worker.worker_status',1)
                ->orderBy('xt_ssov2_header.id', 'ASC')->count();

                $empresasSSO= DB::table('xt_ssov2_header')
                ->join('xt_ssov2_header_worker', function ($join) use ($value){
                    $join->on('xt_ssov2_header_worker.sso_id','=','xt_ssov2_header.id');
                })
                ->whereIn('xt_ssov2_header.sso_mcomp_rut',$rutprincipalR)
                ->whereIn('xt_ssov2_header.sso_comp_rut',$rutcontratistasR)
                ->where('xt_ssov2_header.sso_status',1)
                ->where('xt_ssov2_header_worker.worker_status',1)
                ->orderBy('xt_ssov2_header.id', 'ASC')
                ->select('xt_ssov2_header.id as folio','xt_ssov2_header.sso_mcomp_rut','xt_ssov2_header.sso_mcomp_dv','xt_ssov2_header.sso_mcomp_name','xt_ssov2_header.sso_comp_rut','xt_ssov2_header.sso_comp_dv','xt_ssov2_header.sso_comp_name','xt_ssov2_header.sso_subcomp_active','xt_ssov2_header.sso_subcomp_rut','xt_ssov2_header.sso_subcomp_name','xt_ssov2_header_worker.id','xt_ssov2_header_worker.worker_name','xt_ssov2_header_worker.worker_name1','xt_ssov2_header_worker.worker_name2','xt_ssov2_header_worker.worker_name3','xt_ssov2_header_worker.worker_rut')->chunk($countEmpresasSSO, function ($query) use (&$WORKCERSSO,&$WORKCERSSO2){
                 

                    foreach((array)$query as $empresasSSO){

                        if(!empty($empresasSSO[0])){

                            foreach ($empresasSSO as $ssot) {

                                $rutTraSSO = $ssot->worker_rut;
                                $findme   = '.';
                                $pos = strpos($ssot->worker_rut, $findme);

                                if($pos === false) {
                                    $rut = explode("-",$ssot->worker_rut);
                                    $rutLimpio = $rut[0];
                                    $dvrut = substr($ssot->worker_rut,-1);
                                }else{
                                    $rut2 = str_replace(".", "", $ssot->worker_rut);
                                    $rut = explode("-",$rut2);
                                    $rutLimpio = $rut[0];
                                    $dvrut = substr($ssot->worker_rut,-1);
                                }
                                $rutTrabajadorSSO = $rutLimpio."-".$dvrut;
                                $datosAcceso =AccesoPersona::where('ACC_RUT',$rutLimpio)->take(1)->get(['ACC_FECHA_ACCESO','ACC_CENTRO_COSTO'])->toArray();
               

                                $DatoSSO['rutTrabajador'] = $rutTrabajadorSSO; 
                                $DatoSSO['nombreTrabajador'] = ucwords(mb_strtolower($ssot->worker_name1,'UTF-8')); 
                                $DatoSSO['apellido1Trabajador'] = ucwords(mb_strtolower($ssot->worker_name2,'UTF-8'));
                                $DatoSSO['apellido2Trabajador'] = ucwords(mb_strtolower($ssot->worker_name3,'UTF-8')); 
                                $DatoSSO['idComp'] = $ssot->folio; 
                                $DatoSSO['rutPrincipal'] = formatRut($ssot->sso_mcomp_rut); 
                                $DatoSSO['nombrePrincipal'] = ucwords(mb_strtolower($ssot->sso_mcomp_name,'UTF-8'));    
                                $DatoSSO['rutContratista'] = formatRut($ssot->sso_comp_rut);;
                                $DatoSSO['nombreContratista'] = ucwords(mb_strtolower($ssot->sso_comp_name,'UTF-8')); 
                                if($ssot->sso_subcomp_active == 1){
                                    $DatoSSO['rutSubContratista'] =  formatRut($ssot->sso_subcomp_rut);
                                    $DatoSSO['nombreSubContratista'] = ucwords(mb_strtolower($ssot->sso_subcomp_name,'UTF-8'));
                                }else{
                                    $DatoSSO['rutSubContratista'] =  "";
                                    $DatoSSO['nombreSubContratista'] = "";    
                                }   
                                if(!empty($datosAcceso[0]['ACC_FECHA_ACCESO'])){
                                    $DatoSSO['ControlAcceso'] =  $datosAcceso[0]['ACC_FECHA_ACCESO'];
                                    $DatoSSO['centroCosto'] = ucwords(mb_strtolower($datosAcceso[0]['ACC_CENTRO_COSTO'],'UTF-8')); 
                                }else{
                                    $DatoSSO['ControlAcceso'] =  ""; 
                                    $DatoSSO['centroCosto'] = "";   
                                }   
                                $DatoSSO['perido'] = "";   
                                $DatoSSO['estadoCertificacion'] = ""; 
                                $DatoSSO['fechaCertificado'] =  "";

                                $documentos = EstadoDocumento::where('upld_sso_id', $ssot->folio)->where('upld_workerid',$ssot->id)->where('upld_status',1)->where('upld_type',1)->
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
                                    if($porcentajeApro>=100){
                                        $cantidadcien +=1; 
                                    }else{
                                        $noAcreditado +=1;
                                    }

                                    $DatoSSO["porcentajeTrabajador"] =  number_format($porcentajeApro, 2, '.', ''); 
                                }else{
                                    $DatoSSO["porcentajeTrabajador"] =  0;
                                }

                                $WORKCERSSO2[] = $DatoSSO; 
                            }
                        }
                    }
                    $WORKCERSSO = $WORKCERSSO2;
                });*/

                $WORKS = $WORKCER;
                if(!empty($WORKS)){

                    Excel::create('Reporte Cruzado', function($excel) use ($WORKS) {

                        $excel->sheet('Datos', function($sheet) use($WORKS) {    
                            $sheet->loadView('reporteTraCSA.excel',compact('WORKS'));
                        });
                    })->export('xls'); 
                }          

            }if($fechaSeleccion != 0  AND $countContratista != 0 ){
                $WORKCER2 = array();
                $WORKCER = array();

                $fechas = $porciones = explode("_", $fechaSeleccion);
                $fecha1 = $fechas[0];
                $fecha2 = $fechas[1];
                $fechaInicial = date("Y-m-d", strtotime($fecha1));
                $fechaFinal = date("Y-m-d", strtotime($fecha2));
                $periodosT = $fecha1 ."-".$fecha2;
                $fechasDesde = strtotime ( '+4 hour' ,strtotime($fecha1));
                //sumo 1 día
                $fechasHasta = strtotime ( '+4 hour' ,strtotime($fecha2));

                $countEmpresasContratista= DB::table('Company')
                ->join('Worker', function ($join) use ($value){
                    $join->on('Worker.mainCompanyRut','=','Company.mainCompanyRut')
                        ->on('Worker.companyRut','=','Company.rut')
                        ->on('Worker.periodId','=','Company.periodId')
                        ->on('Worker.CompanyCenter','=','Company.center');
                })
                ->whereIn('Company.mainCompanyRut',$rutprincipalR)
                ->whereIn('Company.rut',$rutcontratistasR)
                ->whereNotIn('Company.certificateState', [11,7])
                ->whereBetween('Company.certificateDate', [$fechasDesde,$fechasHasta])
                ->orderBy('Company.id', 'ASC')->count();

                $empresasContratista= DB::table('Company')
                ->join('Worker', function ($join) use ($value){
                    $join->on('Worker.mainCompanyRut','=','Company.mainCompanyRut')
                        ->on('Worker.companyRut','=','Company.rut')
                        ->on('Worker.periodId','=','Company.periodId')
                        ->on('Worker.CompanyCenter','=','Company.center');
                })
                ->whereIn('Company.mainCompanyRut',$rutprincipalR)
                ->whereIn('Company.rut',$rutcontratistasR)
                ->whereNotIn('Company.certificateState', [11,7])
                ->whereBetween('Company.certificateDate', [$fechasDesde,$fechasHasta])
                ->orderBy('Company.id', 'ASC')
                ->select('Company.id as idComp','Company.rut as rutComp','Company.dv as dvComp','Company.name as nameComp','Company.mainCompanyName','Company.companyTypeId','Company.mainCompanyRut','Company.center','Company.certificateState','Company.certificateDate','Company.periodId','Company.subcontratistaRut','Company.subcontratistaName','Company.subcontratistaDv','Worker.rut','Worker.dv','Worker.names','Worker.firstLastName','Worker.secondLastName')->chunk($countEmpresasContratista, function ($query) use (&$WORKCER2,&$WORKCER,$fechaInicial,$fechaFinal){

                    function periodoTexto($idPerido){

                    $periodo = DB::table('Period')
                    ->join('Month', 'Month.id', '=', 'Period.monthId')
                    ->where(['Period.id' => $idPerido])
                    ->select('Period.year','Month.name')
                    ->get();

                    return $periodo[0]->name."-".$periodo[0]->year;
                    }

                    function estadoCerficacionTexto($idEstadoCert){

                        switch ((int)$idEstadoCert) {
                            case 1:
                                return $estadoCerficacionTexto ="Ingresado";
                                break;
                            case 2:
                                return $estadoCerficacionTexto ="Solicitado";
                                break;
                            case 3:
                                return $estadoCerficacionTexto ="Aprobado";
                                break;
                            case 4:
                                return $estadoCerficacionTexto ="No Aprobado";
                                break;
                            case 5:
                                return $estadoCerficacionTexto ="Certificado";
                                break;
                            case 6:
                                return $estadoCerficacionTexto ="Documentado";
                                break;
                            case 7:
                                return $estadoCerficacionTexto ="Histórico";
                                break;
                            case 8:
                                return $estadoCerficacionTexto ="Completo";
                                break;
                            case 9:
                                return $estadoCerficacionTexto ="En Proceso";
                                break;
                            case 10:
                                return $estadoCerficacionTexto ="No Conforme";
                                break;
                            case 11:
                                return $estadoCerficacionTexto ="Inactivo";
                                break;
                        }
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
                                $dv = 'K';
                            }
                            if ($dv == 11) {
                                $dv = '0';
                            }
                        }
                        return number_format($rut, 0, ",", "") . '-' . $dv;
                    }

                    foreach((array)$query as $empresasContratista){

                        if(!empty($empresasContratista)){
                            unset($Datoscertificacion);
                            unset($datosAcceso);
                            unset($documentosTrabObligatorios);
                            foreach ($empresasContratista as $contratista) {
               
                                if($contratista->companyTypeId == 1){
                                    $rutContratista = $contratista->rutComp."-".$contratista->dvComp; 
                                    $nombreContratista = ucwords(mb_strtolower($contratista->nameComp,'UTF-8')); 
                                    $rutSubContratista = "";
                                    $nombreSubContratista = "";
                                    $rutContratista2 = $contratista->rutComp;
                                    $rutContratistasinDV = $contratista->rutComp;
                                }
                                if($contratista->companyTypeId == 2){
                                    $rutContratista = $contratista->subcontratistaRut."-".$contratista->subcontratistaDv;
                                    $nombreContratista =  ucwords(mb_strtolower($contratista->subcontratistaName,'UTF-8'));  
                                    $rutSubContratista = $contratista->rutComp."-".$contratista->dvComp; 
                                    $nombreSubContratista = ucwords(mb_strtolower($contratista->nameComp,'UTF-8')); 
                                    $rutContratista2 = $contratista->rutComp;
                                    $rutContratistasinDV = $contratista->subcontratistaRut;
                                }

                                $peridoTex = periodoTexto($contratista->periodId);
                                $estadoCerficacionTexto = estadoCerficacionTexto($contratista->certificateState);
                                $fechaCertificiacion=date('d/m/Y', $contratista->certificateDate);
                                $rutTrabajadorCert = $contratista->rut.'-'.$contratista->dv;
                                $Datoscertificacion['rutTrabajador'] = $rutTrabajadorCert; 
                                $Datoscertificacion['nombreTrabajador'] = ucwords(mb_strtolower($contratista->names,'UTF-8')); 
                                $Datoscertificacion['apellido1Trabajador'] = ucwords(mb_strtolower($contratista->firstLastName,'UTF-8'));
                                $Datoscertificacion['apellido2Trabajador'] = ucwords(mb_strtolower($contratista->secondLastName,'UTF-8')); 
                                $Datoscertificacion['idComp'] = $contratista->idComp; 
                                $Datoscertificacion['rutPrincipal'] = formatRut($contratista->mainCompanyRut); 
                                $Datoscertificacion['nombrePrincipal'] = ucwords(mb_strtolower($contratista->mainCompanyName,'UTF-8'));    
                                $Datoscertificacion['rutContratista'] = $rutContratista;
                                $Datoscertificacion['nombreContratista'] = $nombreContratista; 
                                $Datoscertificacion['rutSubContratista'] =  $rutSubContratista;
                                $Datoscertificacion['nombreSubContratista'] = $nombreSubContratista;
                                $Datoscertificacion['centroCosto'] = ucwords(mb_strtolower($contratista->center,'UTF-8'));          
                                $Datoscertificacion['perido'] = ucwords(mb_strtolower($peridoTex,'UTF-8'));   
                                $Datoscertificacion['estadoCertificacion'] = ucwords(mb_strtolower($estadoCerficacionTexto,'UTF-8')); 
                                $Datoscertificacion['fechaCertificado'] =  $fechaCertificiacion;
                    

                
                                $datosAcceso =AccesoPersona::where('ACC_RUT',$contratista->rut)
                                ->where('ACC_RUT_CONTRATISTA',$rutContratistasinDV)
                                ->where('ACC_RUT_PPAL',$contratista->mainCompanyRut)
                                ->where('ACC_CENTRO_COSTO',$contratista->center)
                                ->whereDate('ACC_FECHA_ACCESO', '>=', $fechaInicial)
                                ->whereDate('ACC_FECHA_ACCESO', '<=', $fechaFinal)
                                ->take(1)
                                ->orderBy('ACC_FECHA_ACCESO', 'DESC')
                                ->get(['ACC_FECHA_ACCESO'])->toArray();
                                if(!empty($datosAcceso[0]['ACC_FECHA_ACCESO'])){
                                    $Datoscertificacion['ControlAcceso'] =  $datosAcceso[0]['ACC_FECHA_ACCESO'];
                                }else{
                                    $Datoscertificacion['ControlAcceso'] =  "";    
                                } 
                                
            
                                $EP = $contratista->mainCompanyRut;
                                $empleadoSSO = DB::table('xt_ssov2_header_worker')
                                ->join('xt_ssov2_header', function ($join) use ($EP,$rutContratistasinDV){
                                    $join->where('xt_ssov2_header.sso_mcomp_rut','=',$EP)
                                         ->where('xt_ssov2_header.sso_comp_rut','=',$rutContratistasinDV)
                                         ->on('xt_ssov2_header.id','=','xt_ssov2_header_worker.sso_id');
                                })
                                ->where('worker_status','1')
                                ->where('worker_rut',$rutTrabajadorCert)
                                ->get(['xt_ssov2_header_worker.id','worker_name','worker_name1','worker_name2','worker_name3','worker_rut','worker_cargoid','xt_ssov2_header_worker.sso_id','xt_ssov2_header.sso_cfgid'])
                                ->take(1)->toArray();
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
                                if(!empty($empleadoSSO[0]->id)){

                                    $documentosTrabObligatorios = DB::table('xt_ssov2_configs_cargos_cats_docs_params')
                                    ->join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                                    ->where(['xt_ssov2_configs_cargos_cats_docs_params.cfg_id' => $empleadoSSO[0]->sso_cfgid])
                                    ->where(['xt_ssov2_configs_cargos_cats_docs_params.cargo_id' => $empleadoSSO[0]->worker_cargoid])
                                    ->where(['xt_ssov2_doctypes.doc_status' => 1])
                                    ->distinct('xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                                    ->get(['xt_ssov2_configs_cargos_cats_docs_params.doc_id'])->count();
                                    
                                    $documentos = EstadoDocumento::where('upld_sso_id', $empleadoSSO[0]->sso_id)->where('upld_workerid',$empleadoSSO[0]->id)->where('upld_status',1)->where('upld_type',1)->
                                        get(['id','upld_catid','upld_docid','upld_docaprob','upld_venced','upld_vence_date', 'upld_rechazado', 'upld_upddat','upld_docaprob_uid'])->toArray();

                                    $totalDoc = count($documentos);
                                    if(!empty($documentos[0]['id'])){ 

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
                                        $porcentajeApro = ($totalDocAprobados / $documentosTrabObligatorios * 100);
                                        if($porcentajeApro>=100){
                                            $cantidadcien +=1; 
                                        }else{
                                            $noAcreditado +=1;
                                        }
                                       
                                        $Datoscertificacion["porcentajeTrabajador"] =  number_format($porcentajeApro, 2, '.', '');
                                    }else{
                                        $Datoscertificacion["porcentajeTrabajador"] =  0;
                                    }

                                }else{
                                    
                                    $rutTrabajadorD = number_format($contratista->rut, 0, "", ".") . '-' .$contratista->dv;
                               
                                    $empleadoSSOD =  DB::table('xt_ssov2_header_worker')
                                    ->join('xt_ssov2_header', function ($join) use ($EP,$rutContratistasinDV){
                                        $join->where('xt_ssov2_header.sso_mcomp_rut','=',$EP)
                                             ->where('xt_ssov2_header.sso_comp_rut','=',$rutContratistasinDV)
                                             ->on('xt_ssov2_header.id','=','xt_ssov2_header_worker.sso_id');
                                    })
                                    ->where('worker_status','1')
                                    ->where('worker_rut',$rutTrabajadorD)
                                    ->get(['xt_ssov2_header_worker.id','worker_name','worker_name1','worker_name2','worker_name3','worker_rut','worker_cargoid','xt_ssov2_header_worker.sso_id','xt_ssov2_header.sso_cfgid'])
                                    ->take(1)->toArray();
                                   
                                    if(!empty($empleadoSSOD[0]->id)){

                                        $documentosTrabObligatorios = DB::table('xt_ssov2_configs_cargos_cats_docs_params')
                                        ->join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                                        ->where(['xt_ssov2_configs_cargos_cats_docs_params.cfg_id' => $empleadoSSOD[0]->sso_cfgid])
                                        ->where(['xt_ssov2_configs_cargos_cats_docs_params.cargo_id' => $empleadoSSOD[0]->worker_cargoid])
                                        ->where(['xt_ssov2_doctypes.doc_status' => 1])
                                        ->distinct('xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                                        ->get(['xt_ssov2_configs_cargos_cats_docs_params.doc_id'])->count();
                                    
                                        $documentos = EstadoDocumento::where('upld_sso_id', $empleadoSSOD[0]->sso_id)->where('upld_workerid',$empleadoSSOD[0]->id)->where('upld_status',1)->where('upld_type',1)->
                                        get(['id','upld_catid','upld_docid','upld_docaprob','upld_venced','upld_vence_date', 'upld_rechazado', 'upld_upddat','upld_docaprob_uid'])->toArray();
                                        
                                        $totalDoc = count($documentos);
                                       
                                        if(!empty($documentos[0]['id'])){ 

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
                                            $porcentajeApro = ($totalDocAprobados / $documentosTrabObligatorios * 100);
                                            if($porcentajeApro>=100){
                                                $cantidadcien +=1; 
                                            }else{
                                                $noAcreditado +=1;
                                            }

                                            $Datoscertificacion["porcentajeTrabajador"] =  number_format($porcentajeApro, 2, '.', '');
                                        }else{
                                            $Datoscertificacion["porcentajeTrabajador"] =  0;
                                        }
                                    }else{
                                        $Datoscertificacion["porcentajeTrabajador"] = "";

                                    }
                                }
                                ///llenamos lista de datos ////
                                $WORKCER[] = $Datoscertificacion;
                            } 
                        }
                    }
                    $WORKCER = $WORKCER2;
                });

                /*$WORKCERSSO = array();
                $WORKCERSSO2 = array();
                $countEmpresasSSO= DB::table('xt_ssov2_header')
                ->join('xt_ssov2_header_worker', function ($join) use ($value){
                    $join->on('xt_ssov2_header_worker.sso_id','=','xt_ssov2_header.id');
                })
                ->whereIn('xt_ssov2_header.sso_mcomp_rut',$rutprincipalR)
                ->whereIn('xt_ssov2_header.sso_comp_rut',$rutcontratistasR)
                ->where('xt_ssov2_header.sso_status',1)
                ->where('xt_ssov2_header_worker.worker_status',1)
                ->orderBy('xt_ssov2_header.id', 'ASC')->count();

                $empresasSSO= DB::table('empresasSSOxt_ssov2_header')
                ->join('xt_ssov2_header_worker', function ($join) use ($value){
                    $join->on('xt_ssov2_header_worker.sso_id','=','xt_ssov2_header.id');
                })
                ->whereIn('xt_ssov2_header.sso_mcomp_rut',$rutprincipalR)
                ->whereIn('xt_ssov2_header.sso_comp_rut',$rutcontratistasR)
                ->where('xt_ssov2_header.sso_status',1)
                ->where('xt_ssov2_header_worker.worker_status',1)
                ->orderBy('xt_ssov2_header.id', 'ASC')
                ->select('xt_ssov2_header.id as folio','xt_ssov2_header.sso_mcomp_rut','xt_ssov2_header.sso_mcomp_dv','xt_ssov2_header.sso_mcomp_name','xt_ssov2_header.sso_comp_rut','xt_ssov2_header.sso_comp_dv','xt_ssov2_header.sso_comp_name','xt_ssov2_header.sso_subcomp_active','xt_ssov2_header.sso_subcomp_rut','xt_ssov2_header.sso_subcomp_name','xt_ssov2_header_worker.id','xt_ssov2_header_worker.worker_name','xt_ssov2_header_worker.worker_name1','xt_ssov2_header_worker.worker_name2','xt_ssov2_header_worker.worker_name3','xt_ssov2_header_worker.worker_rut')->chunk($countEmpresasSSO, function ($query) use (&$WORKCERSSO,&$WORKCERSSO2){
                   

                    foreach((array)$query as $empresasSSO){

                        if(!empty($empresasSSO[0])){

                            foreach ($empresasSSO as $ssot) {

                                $rutTraSSO = $ssot->worker_rut;
                                $findme   = '.';
                                $pos = strpos($ssot->worker_rut, $findme);

                                if($pos === false) {
                                    $rut = explode("-",$ssot->worker_rut);
                                    $rutLimpio = $rut[0];
                                    $dvrut = substr($ssot->worker_rut,-1);
                                }else{
                                    $rut2 = str_replace(".", "", $ssot->worker_rut);
                                    $rut = explode("-",$rut2);
                                    $rutLimpio = $rut[0];
                                    $dvrut = substr($ssot->worker_rut,-1);
                                }
                                $rutTrabajadorSSO = $rutLimpio."-".$dvrut;
                                $datosAcceso =AccesoPersona::where('ACC_RUT',$rutLimpio)->take(1)->get(['ACC_FECHA_ACCESO','ACC_CENTRO_COSTO'])->toArray();
               

                                $DatoSSO['rutTrabajador'] = $rutTrabajadorSSO; 
                                $DatoSSO['nombreTrabajador'] = ucwords(mb_strtolower($ssot->worker_name1,'UTF-8')); 
                                $DatoSSO['apellido1Trabajador'] = ucwords(mb_strtolower($ssot->worker_name2,'UTF-8'));
                                $DatoSSO['apellido2Trabajador'] = ucwords(mb_strtolower($ssot->worker_name3,'UTF-8')); 
                                $DatoSSO['idComp'] = $ssot->folio; 
                                $DatoSSO['rutPrincipal'] = formatRut($ssot->sso_mcomp_rut); 
                                $DatoSSO['nombrePrincipal'] = ucwords(mb_strtolower($ssot->sso_mcomp_name,'UTF-8'));    
                                $DatoSSO['rutContratista'] = formatRut($ssot->sso_comp_rut);;
                                $DatoSSO['nombreContratista'] = ucwords(mb_strtolower($ssot->sso_comp_name,'UTF-8')); 
                                if($ssot->sso_subcomp_active == 1){
                                    $DatoSSO['rutSubContratista'] =  formatRut($ssot->sso_subcomp_rut);
                                    $DatoSSO['nombreSubContratista'] = ucwords(mb_strtolower($ssot->sso_subcomp_name,'UTF-8'));
                                }else{
                                    $DatoSSO['rutSubContratista'] =  "";
                                    $DatoSSO['nombreSubContratista'] = "";    
                                }   
                                if(!empty($datosAcceso[0]['ACC_FECHA_ACCESO'])){
                                    $DatoSSO['ControlAcceso'] =  $datosAcceso[0]['ACC_FECHA_ACCESO'];
                                    $DatoSSO['centroCosto'] = ucwords(mb_strtolower($datosAcceso[0]['ACC_CENTRO_COSTO'],'UTF-8')); 
                                }else{
                                    $DatoSSO['ControlAcceso'] =  ""; 
                                    $DatoSSO['centroCosto'] = "";   
                                }   
                                $DatoSSO['perido'] = "";   
                                $DatoSSO['estadoCertificacion'] = ""; 
                                $DatoSSO['fechaCertificado'] =  "";

                                $documentos = EstadoDocumento::where('upld_sso_id', $ssot->folio)->where('upld_workerid',$ssot->id)->where('upld_status',1)->where('upld_type',1)->
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
                                    if($porcentajeApro>=100){
                                        $cantidadcien +=1; 
                                    }else{
                                        $noAcreditado +=1;
                                    }

                                    $DatoSSO["porcentajeTrabajador"] =  number_format($porcentajeApro, 2, '.', ''); 
                                }else{
                                    $DatoSSO["porcentajeTrabajador"] =  0;
                                }

                                $WORKCERSSO2[] = $DatoSSO; 
                            }
                        }
                    }
                     $WORKCERSSO = $WORKCERSSO2;
                });*/

                $WORKS = $WORKCER;
                if(!empty($WORKS)){

                    Excel::create('Reporte Cruzado', function($excel) use ($WORKS) {

                        $excel->sheet('Datos', function($sheet) use($WORKS) {    
                            $sheet->loadView('reporteTraCSA.excel',compact('WORKS'));
                        });
                    })->export('xls'); 
                }           

            }
            if($fechaSeleccion != 0  AND $countContratista == 0 AND $centroCosto == 0){
                $WORKCER2 = array();
                $WORKCER = array();
                $fechas = $porciones = explode("_", $fechaSeleccion);
                $fecha1 = $fechas[0];
                $fecha2 = $fechas[1];
                $fechaInicial = date("Y-m-d", strtotime($fecha1));
                $fechaFinal = date("Y-m-d", strtotime($fecha2));
                
                $periodosT = $fecha1 ."-".$fecha2;
                $fechasDesde = strtotime ( '+4 hour' ,strtotime($fecha1));
                //sumo 1 día
                $fechasHasta = strtotime ( '+4 hour' ,strtotime($fecha2));

                $countEmpresasContratista= DB::table('Company')
                ->join('Worker', function ($join) use ($value){
                    $join->on('Worker.mainCompanyRut','=','Company.mainCompanyRut')
                        ->on('Worker.companyRut','=','Company.rut')
                        ->on('Worker.periodId','=','Company.periodId')
                        ->on('Worker.CompanyCenter','=','Company.center');
                })
                ->whereIn('Company.mainCompanyRut',$rutprincipalR)
                ->whereBetween('Company.certificateDate', [$fechasDesde,$fechasHasta])
                ->whereNotIn('Company.certificateState', [11,7])
                ->orderBy('Company.id', 'ASC')->count();

                $empresasContratista= DB::table('Company')
                ->join('Worker', function ($join) use ($value){
                    $join->on('Worker.mainCompanyRut','=','Company.mainCompanyRut')
                        ->on('Worker.companyRut','=','Company.rut')
                        ->on('Worker.periodId','=','Company.periodId')
                        ->on('Worker.CompanyCenter','=','Company.center');
                })
                ->whereIn('Company.mainCompanyRut',$rutprincipalR)
                ->whereBetween('Company.certificateDate', [$fechasDesde,$fechasHasta])
                ->whereNotIn('Company.certificateState', [11,7])
                ->orderBy('Company.id', 'ASC')
                ->select('Company.id as idComp','Company.rut as rutComp','Company.dv as dvComp','Company.name as nameComp','Company.mainCompanyName','Company.companyTypeId','Company.mainCompanyRut','Company.center','Company.certificateState','Company.certificateDate','Company.periodId','Company.subcontratistaRut','Company.subcontratistaName','Company.subcontratistaDv','Worker.rut','Worker.dv','Worker.names','Worker.firstLastName','Worker.secondLastName')->chunk($countEmpresasContratista, function ($query) use (&$WORKCER2,&$WORKCER,$fechaInicial,$fechaFinal){

                    function periodoTexto($idPerido){

                        $periodo = DB::table('Period')
                        ->join('Month', 'Month.id', '=', 'Period.monthId')
                        ->where(['Period.id' => $idPerido])
                        ->select('Period.year','Month.name')
                        ->get();

                        return $periodo[0]->name."-".$periodo[0]->year;
                    }

                    function estadoCerficacionTexto($idEstadoCert){

                        switch ((int)$idEstadoCert) {
                            case 1:
                                return $estadoCerficacionTexto ="Ingresado";
                                break;
                            case 2:
                                return $estadoCerficacionTexto ="Solicitado";
                                break;
                            case 3:
                                return $estadoCerficacionTexto ="Aprobado";
                                break;
                            case 4:
                                return $estadoCerficacionTexto ="No Aprobado";
                                break;
                            case 5:
                                return $estadoCerficacionTexto ="Certificado";
                                break;
                            case 6:
                                return $estadoCerficacionTexto ="Documentado";
                                break;
                            case 7:
                                return $estadoCerficacionTexto ="Histórico";
                                break;
                            case 8:
                                return $estadoCerficacionTexto ="Completo";
                                break;
                            case 9:
                                return $estadoCerficacionTexto ="En Proceso";
                                break;
                            case 10:
                                return $estadoCerficacionTexto ="No Conforme";
                                break;
                            case 11:
                                return $estadoCerficacionTexto ="Inactivo";
                                break;
                        }
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
                                $dv = 'K';
                            }
                            if ($dv == 11) {
                                $dv = '0';
                            }
                        }
                        return number_format($rut, 0, ",", "") . '-' . $dv;
                    }

                    foreach((array)$query as $empresasContratista){

                        if(!empty($empresasContratista)){
                            unset($Datoscertificacion);
                            unset($datosAcceso);
                            unset($documentosTrabObligatorios);
                            foreach ($empresasContratista as $contratista) {
               
                                if($contratista->companyTypeId == 1){
                                    $rutContratista = $contratista->rutComp."-".$contratista->dvComp; 
                                    $nombreContratista = ucwords(mb_strtolower($contratista->nameComp,'UTF-8')); 
                                    $rutSubContratista = "";
                                    $nombreSubContratista = "";
                                    $rutContratista2 = $contratista->rutComp;
                                    $rutContratistasinDV = $contratista->rutComp;
                                }
                                if($contratista->companyTypeId == 2){
                                    $rutContratista = $contratista->subcontratistaRut."-".$contratista->subcontratistaDv;
                                    $nombreContratista =  ucwords(mb_strtolower($contratista->subcontratistaName,'UTF-8'));  
                                    $rutSubContratista = $contratista->rutComp."-".$contratista->dvComp; 
                                    $nombreSubContratista = ucwords(mb_strtolower($contratista->nameComp,'UTF-8')); 
                                    $rutContratista2 = $contratista->rutComp;
                                    $rutContratistasinDV = $contratista->subcontratistaRut;
                                }

                                $peridoTex = periodoTexto($contratista->periodId);
                                $estadoCerficacionTexto = estadoCerficacionTexto($contratista->certificateState);
                                $fechaCertificiacion=date('d/m/Y', $contratista->certificateDate);
                                $rutTrabajadorCert = $contratista->rut.'-'.$contratista->dv;
                                $Datoscertificacion['rutTrabajador'] = $rutTrabajadorCert; 
                                $Datoscertificacion['nombreTrabajador'] = ucwords(mb_strtolower($contratista->names,'UTF-8')); 
                                $Datoscertificacion['apellido1Trabajador'] = ucwords(mb_strtolower($contratista->firstLastName,'UTF-8'));
                                $Datoscertificacion['apellido2Trabajador'] = ucwords(mb_strtolower($contratista->secondLastName,'UTF-8')); 
                                $Datoscertificacion['idComp'] = $contratista->idComp; 
                                $Datoscertificacion['rutPrincipal'] = formatRut($contratista->mainCompanyRut); 
                                $Datoscertificacion['nombrePrincipal'] = ucwords(mb_strtolower($contratista->mainCompanyName,'UTF-8'));    
                                $Datoscertificacion['rutContratista'] = $rutContratista;
                                $Datoscertificacion['nombreContratista'] = $nombreContratista; 
                                $Datoscertificacion['rutSubContratista'] =  $rutSubContratista;
                                $Datoscertificacion['nombreSubContratista'] = $nombreSubContratista;
                                $Datoscertificacion['centroCosto'] = ucwords(mb_strtolower($contratista->center,'UTF-8'));          
                                $Datoscertificacion['perido'] = ucwords(mb_strtolower($peridoTex,'UTF-8'));   
                                $Datoscertificacion['estadoCertificacion'] = ucwords(mb_strtolower($estadoCerficacionTexto,'UTF-8')); $Datoscertificacion['fechaCertificado'] =  $fechaCertificiacion;
                
                                $datosAcceso =AccesoPersona::where('ACC_RUT',$contratista->rut)
                                ->where('ACC_RUT_CONTRATISTA',$rutContratistasinDV)
                                ->where('ACC_RUT_PPAL',$contratista->mainCompanyRut)
                                ->where('ACC_CENTRO_COSTO',$contratista->center)
                                ->whereDate('ACC_FECHA_ACCESO', '>=', $fechaInicial)
                                ->whereDate('ACC_FECHA_ACCESO', '<=', $fechaFinal)
                                ->take(1)
                                ->orderBy('ACC_FECHA_ACCESO', 'DESC')
                                ->get(['ACC_FECHA_ACCESO'])->toArray();
                                if(!empty($datosAcceso[0]['ACC_FECHA_ACCESO'])){
                                    $Datoscertificacion['ControlAcceso'] =  $datosAcceso[0]['ACC_FECHA_ACCESO'];
                                }else{
                                    $Datoscertificacion['ControlAcceso'] =  "";    
                                } 
                                
                                $EP = $contratista->mainCompanyRut;
                                $empleadoSSO = DB::table('xt_ssov2_header_worker')
                                ->join('xt_ssov2_header', function ($join) use ($EP,$rutContratistasinDV){
                                    $join->where('xt_ssov2_header.sso_mcomp_rut','=',$EP)
                                         ->where('xt_ssov2_header.sso_comp_rut','=',$rutContratistasinDV)
                                         ->on('xt_ssov2_header.id','=','xt_ssov2_header_worker.sso_id');
                                })
                                ->where('worker_status','1')
                                ->where('worker_rut',$rutTrabajadorCert)
                                ->get(['xt_ssov2_header_worker.id','worker_name','worker_name1','worker_name2','worker_name3','worker_rut','worker_cargoid','xt_ssov2_header_worker.sso_id','xt_ssov2_header.sso_cfgid'])
                                ->take(1)->toArray();
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
                                if(!empty($empleadoSSO[0]->id)){

                                    $documentosTrabObligatorios = DB::table('xt_ssov2_configs_cargos_cats_docs_params')
                                        ->join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                                        ->where(['xt_ssov2_configs_cargos_cats_docs_params.cfg_id' => $empleadoSSO[0]->sso_cfgid])
                                        ->where(['xt_ssov2_configs_cargos_cats_docs_params.cargo_id' => $empleadoSSO[0]->worker_cargoid])
                                        ->where(['xt_ssov2_doctypes.doc_status' => 1])
                                        ->distinct('xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                                        ->get(['xt_ssov2_configs_cargos_cats_docs_params.doc_id'])->count();
                                    
                                    $documentos = EstadoDocumento::where('upld_sso_id', $empleadoSSO[0]->sso_id)->where('upld_workerid',$empleadoSSO[0]->id)->where('upld_status',1)->where('upld_type',1)->
                                        get(['id','upld_catid','upld_docid','upld_docaprob','upld_venced','upld_vence_date', 'upld_rechazado', 'upld_upddat','upld_docaprob_uid'])->toArray();

                                    $totalDoc = count($documentos);
                                    if(!empty($documentos[0]['id'])){ 

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
                                        $porcentajeApro = ($totalDocAprobados / $documentosTrabObligatorios * 100);
                                        if($porcentajeApro>=100){
                                            $cantidadcien +=1; 
                                        }else{
                                            $noAcreditado +=1;
                                        }
                                       
                                        $Datoscertificacion["porcentajeTrabajador"] =  number_format($porcentajeApro, 2, '.', '');
                                    }else{
                                        $Datoscertificacion["porcentajeTrabajador"] =  0;
                                    }

                                }else{
                                    
                                    $rutTrabajadorD = number_format($contratista->rut, 0, "", ".") . '-' .$contratista->dv;
                               
                                    $empleadoSSOD =  DB::table('xt_ssov2_header_worker')
                                    ->join('xt_ssov2_header', function ($join) use ($EP,$rutContratistasinDV){
                                        $join->where('xt_ssov2_header.sso_mcomp_rut','=',$EP)
                                             ->where('xt_ssov2_header.sso_comp_rut','=',$rutContratistasinDV)
                                             ->on('xt_ssov2_header.id','=','xt_ssov2_header_worker.sso_id');
                                    })
                                    ->where('worker_status','1')
                                    ->where('worker_rut',$rutTrabajadorD)
                                    ->get(['xt_ssov2_header_worker.id','worker_name','worker_name1','worker_name2','worker_name3','worker_rut','worker_cargoid','xt_ssov2_header_worker.sso_id','xt_ssov2_header.sso_cfgid'])
                                    ->take(1)->toArray();
                                   
                                    if(!empty($empleadoSSOD[0]->id)){

                                        $documentosTrabObligatorios = DB::table('xt_ssov2_configs_cargos_cats_docs_params')
                                        ->join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                                        ->where(['xt_ssov2_configs_cargos_cats_docs_params.cfg_id' => $empleadoSSOD[0]->sso_cfgid])
                                        ->where(['xt_ssov2_configs_cargos_cats_docs_params.cargo_id' => $empleadoSSOD[0]->worker_cargoid])
                                        ->where(['xt_ssov2_doctypes.doc_status' => 1])
                                        ->distinct('xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                                        ->get(['xt_ssov2_configs_cargos_cats_docs_params.doc_id'])->count();
                                    
                                        $documentos = EstadoDocumento::where('upld_sso_id', $empleadoSSOD[0]->sso_id)->where('upld_workerid',$empleadoSSOD[0]->id)->where('upld_status',1)->where('upld_type',1)->
                                        get(['id','upld_catid','upld_docid','upld_docaprob','upld_venced','upld_vence_date', 'upld_rechazado', 'upld_upddat','upld_docaprob_uid'])->toArray();
                                        
                                        $totalDoc = count($documentos);
                                       
                                        if(!empty($documentos[0]['id'])){ 

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
                                            $porcentajeApro = ($totalDocAprobados / $documentosTrabObligatorios * 100);
                                            if($porcentajeApro>=100){
                                                $cantidadcien +=1; 
                                            }else{
                                                $noAcreditado +=1;
                                            }

                                            $Datoscertificacion["porcentajeTrabajador"] =  number_format($porcentajeApro, 2, '.', '');
                                        }else{
                                            $Datoscertificacion["porcentajeTrabajador"] =  0;
                                        }
                                    }else{
                                        $Datoscertificacion["porcentajeTrabajador"] = "";

                                    }
                                }
                                ///llenamos lista de datos ////
                                $WORKCER2[] = $Datoscertificacion;
                            } 
                        }
                    }
                    $WORKCER = $WORKCER2;
                });

                /*$WORKCERSSO = array();
                $WORKCERSSO2 = array();

                $countEmpresasSSO= DB::table('xt_ssov2_header')
                ->join('xt_ssov2_header_worker', function ($join) use ($value){
                    $join->on('xt_ssov2_header_worker.sso_id','=','xt_ssov2_header.id');
                })
                ->whereIn('xt_ssov2_header.sso_mcomp_rut',$rutprincipalR)
                ->where('xt_ssov2_header.sso_status',1)
                ->where('xt_ssov2_header_worker.worker_status',1)
                ->orderBy('xt_ssov2_header.id', 'ASC')->count();

                $empresasSSO= DB::table('xt_ssov2_header')
                ->join('xt_ssov2_header_worker', function ($join) use ($value){
                    $join->on('xt_ssov2_header_worker.sso_id','=','xt_ssov2_header.id');
                })
                ->whereIn('xt_ssov2_header.sso_mcomp_rut',$rutprincipalR)
                ->where('xt_ssov2_header.sso_status',1)
                ->where('xt_ssov2_header_worker.worker_status',1)
                ->orderBy('xt_ssov2_header.id', 'ASC')
                ->select('xt_ssov2_header.id as folio','xt_ssov2_header.sso_mcomp_rut','xt_ssov2_header.sso_mcomp_dv','xt_ssov2_header.sso_mcomp_name','xt_ssov2_header.sso_comp_rut','xt_ssov2_header.sso_comp_dv','xt_ssov2_header.sso_comp_name','xt_ssov2_header.sso_subcomp_active','xt_ssov2_header.sso_subcomp_rut','xt_ssov2_header.sso_subcomp_name','xt_ssov2_header_worker.id','xt_ssov2_header_worker.worker_name','xt_ssov2_header_worker.worker_name1','xt_ssov2_header_worker.worker_name2','xt_ssov2_header_worker.worker_name3','xt_ssov2_header_worker.worker_rut')->chunk($countEmpresasSSO, function ($query) use (&$WORKCERSSO,&$WORKCERSSO2){
                    
                    foreach((array)$query as $empresasSSO){

                        if(!empty($empresasSSO[0])){

                            foreach ($empresasSSO as $ssot) {

                                $rutTraSSO = $ssot->worker_rut;
                                $findme   = '.';
                                $pos = strpos($ssot->worker_rut, $findme);

                                if($pos === false) {
                                    $rut = explode("-",$ssot->worker_rut);
                                    $rutLimpio = $rut[0];
                                    $dvrut = substr($ssot->worker_rut,-1);
                                }else{
                                    $rut2 = str_replace(".", "", $ssot->worker_rut);
                                    $rut = explode("-",$rut2);
                                    $rutLimpio = $rut[0];
                                    $dvrut = substr($ssot->worker_rut,-1);
                                }
                                $rutTrabajadorSSO = $rutLimpio."-".$dvrut;
                                $datosAcceso =AccesoPersona::where('ACC_RUT',$rutLimpio)->take(1)->get(['ACC_FECHA_ACCESO','ACC_CENTRO_COSTO'])->toArray();
               

                                $DatoSSO['rutTrabajador'] = $rutTrabajadorSSO; 
                                $DatoSSO['nombreTrabajador'] = ucwords(mb_strtolower($ssot->worker_name1,'UTF-8')); 
                                $DatoSSO['apellido1Trabajador'] = ucwords(mb_strtolower($ssot->worker_name2,'UTF-8'));
                                $DatoSSO['apellido2Trabajador'] = ucwords(mb_strtolower($ssot->worker_name3,'UTF-8')); 
                                $DatoSSO['idComp'] = $ssot->folio; 
                                $DatoSSO['rutPrincipal'] = formatRut($ssot->sso_mcomp_rut); 
                                $DatoSSO['nombrePrincipal'] = ucwords(mb_strtolower($ssot->sso_mcomp_name,'UTF-8'));    
                                $DatoSSO['rutContratista'] = formatRut($ssot->sso_comp_rut);;
                                $DatoSSO['nombreContratista'] = ucwords(mb_strtolower($ssot->sso_comp_name,'UTF-8')); 
                                if($ssot->sso_subcomp_active == 1){
                                    $DatoSSO['rutSubContratista'] =  formatRut($ssot->sso_subcomp_rut);
                                    $DatoSSO['nombreSubContratista'] = ucwords(mb_strtolower($ssot->sso_subcomp_name,'UTF-8'));
                                }else{
                                    $DatoSSO['rutSubContratista'] =  "";
                                    $DatoSSO['nombreSubContratista'] = "";    
                                }   
                                if(!empty($datosAcceso[0]['ACC_FECHA_ACCESO'])){
                                    $DatoSSO['ControlAcceso'] =  $datosAcceso[0]['ACC_FECHA_ACCESO'];
                                    $DatoSSO['centroCosto'] = ucwords(mb_strtolower($datosAcceso[0]['ACC_CENTRO_COSTO'],'UTF-8')); 
                                }else{
                                    $DatoSSO['ControlAcceso'] =  ""; 
                                    $DatoSSO['centroCosto'] = "";   
                                }   
                                $DatoSSO['perido'] = "";   
                                $DatoSSO['estadoCertificacion'] = ""; 
                                $DatoSSO['fechaCertificado'] =  "";

                                $documentos = EstadoDocumento::where('upld_sso_id', $ssot->folio)->where('upld_workerid',$ssot->id)->where('upld_status',1)->where('upld_type',1)->
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
                                    if($porcentajeApro>=100){
                                        $cantidadcien +=1; 
                                    }else{
                                        $noAcreditado +=1;
                                    }

                                    $DatoSSO["porcentajeTrabajador"] =  number_format($porcentajeApro, 2, '.', ''); 
                                }else{
                                    $DatoSSO["porcentajeTrabajador"] =  0;
                                }

                                $WORKCERSSO2[] = $DatoSSO; 
                            }
                        }
                    }
                    $WORKCERSSO = $WORKCERSSO2;
                });*/  

                $WORKS = $WORKCER;
               
                if(!empty($WORKS)){

                    Excel::create('Reporte Cruzado', function($excel) use ($WORKS) {

                        $excel->sheet('Datos', function($sheet) use($WORKS) {    
                            $sheet->loadView('reporteTraCSA.excel',compact('WORKS'));
                        });
                    })->export('xls'); 
                }           
            }
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
