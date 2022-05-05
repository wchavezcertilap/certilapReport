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
use App\Solicitud;
use Illuminate\Http\Request;

class PorcentajeCumplimientoSSOController extends Controller
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
        $usuarioClaroChile= session('user_Claro');
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

            if($datosUsuarios->type ==3){

                $EmpresasP = FolioSso::distinct()->whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['sso_mcomp_name','sso_mcomp_rut']);

                return view('PorcentajeCumplimientoSSO.index',compact('datosUsuarios','EmpresasP','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile')); 

            }
            if($datosUsuarios->type ==2 || $datosUsuarios->type ==1 ){

                $EmpresasP = FolioSso::distinct()->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['sso_mcomp_name','sso_mcomp_rut']);

                return view('PorcentajeCumplimientoSSO.index',compact('datosUsuarios','EmpresasP','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile')); 

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
        $usuarioClaroChile= session('user_Claro');
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

            if($datosUsuarios->type ==3){

                $EmpresasP = FolioSso::distinct()->whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['sso_mcomp_name','sso_mcomp_rut']);

            }
            if($datosUsuarios->type ==2 || $datosUsuarios->type ==1 ){

                $EmpresasP = FolioSso::distinct()->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['sso_mcomp_name','sso_mcomp_rut']);

            }

        $input=$request->all();
        $empresaPrincipal = $input["empresaPrincipal"];
        $cantidadContratista = 0;
        if(!empty($input["empresaContratista"])){
            $empresaContratista = $input["empresaContratista"];

            foreach ($empresaContratista as $value2) {

                $rutcontratistasR[] = $value2;
            }

            $cantidadContratista = count($rutcontratistasR);
        }
        /// empresa principal del formulario
        
        if($empresaPrincipal[0] != 1){

            foreach ($empresaPrincipal as $value) {
            $rutprincipalR[] = $value;
            }
        }if($empresaPrincipal[0] == 1){
            
            foreach ($EmpresasP as $rutP) {
                $rutprincipalR[] = $rutP->sso_mcomp_rut;
                    # code...
                }    
        
        }

        if($cantidadContratista == 0){

            $idFolios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipalR)
             ->where('sso_status',1)->orderBy('id', 'ASC')->get(['id','sso_mcomp_rut','sso_mcomp_name','sso_mcomp_dv','sso_comp_rut','sso_comp_name','sso_comp_dv','sso_subcomp_active','sso_subcomp_rut','sso_subcomp_dv','sso_subcomp_name','sso_cfgid'])->toArray();

            if(!empty($idFolios)){
                foreach ($idFolios as  $idfolio) {
                    $idsf[] = $idfolio['id'];
                } 

                /// documentos trabajador
                $documentosTrabajadoresObligatoriosCargo=trabajadorSSO::distinct('worker_cargoid')->whereIn('sso_id',$idsf)->get(['worker_cargoid'])->toArray();

                foreach ($documentosTrabajadoresObligatoriosCargo as  $docTra) {

                        $idDocTrab[] = $docTra['worker_cargoid'];
                }

                if(!empty($idDocTrab)){

                $documentosTrabObligatorios = DB::table('xt_ssov2_configs_cargos_cats_docs_params')
                ->where(['cfg_id' => $idFolios[0]['sso_cfgid']])
                ->whereIn('cargo_id', $idDocTrab )
                ->distinct('doc_id')
                ->get(['doc_id'])->toArray();

                    foreach ($documentosTrabObligatorios as  $doctrabObl) {

                            $docIdTrab[] = $doctrabObl->doc_id;
                    }
                }
                $totalDocumentosSolicitados =0;
                $cantidadTra = 0;
                $cuentaTra = 0;
                $numeroTrabajadoresTotales = 0;
                foreach ($idFolios as $folio) {

                    $documentosGlobalesObligatoriosF = DB::table('xt_ssov2_configs_glbdocs')
                    ->join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_configs_glbdocs.glb_docid')
                    ->where(['xt_ssov2_configs_glbdocs.cfg_id' => $folio['sso_cfgid']])
                    ->where(['xt_ssov2_doctypes.doc_status' => 1 ])
                    ->where(['xt_ssov2_doctypes.doc_type' => 0])
                    ->where(['xt_ssov2_configs_glbdocs.glb_obligact' => 1])
                    ->orderBy('xt_ssov2_configs_glbdocs.glb_obligact', 'DESC')
                    ->get(['xt_ssov2_doctypes.id','xt_ssov2_doctypes.doc_name','xt_ssov2_configs_glbdocs.glb_obligact'])->toArray();
                

                    $cuenta = count($documentosGlobalesObligatoriosF);

                    if(!empty($documentosGlobalesObligatoriosF)){
                        unset($idDoGlobales);

                        foreach ($documentosGlobalesObligatoriosF as  $docGlo) {

                                $idDoGlobales[] = $docGlo->id;
                        }
                    }

                    /// trabajadores 
                    /// documentos trabajador
                    $documentosTrabajadoresObligatoriosCargo=trabajadorSSO::distinct('worker_cargoid')->where('sso_id',$folio['id'])->get(['worker_cargoid'])->toArray();
                    
                    if(!empty($documentosTrabajadoresObligatoriosCargo)){
                        unset($idDocTrab);
                        foreach ($documentosTrabajadoresObligatoriosCargo as  $docTra) {

                            $idDocTrab[] = $docTra['worker_cargoid'];
                        }
                        $trabajadores=trabajadorSSO::distinct('id')->where('sso_id',$folio['id'])->where('worker_status',1)->get(['id'])->toArray();
                        $cantidadTra = count($trabajadores);
                        if($cantidadTra == 0){
                            $datosSolictud = Solicitud::distinct()->where('contractRut',$folio['sso_comp_rut'])->orderby('serial','DESC')->take(1)->get(['workersNumber','workerstotales','serial'])->toArray();
                        
                            if(!empty($datosSolictud)){
                                $numeroTrabajadoresTotales = $datosSolictud[0]['workerstotales'];
                                $numeroTrabajadores = $datosSolictud[0]['workersNumber'];
                            } 

                        }else{
                            unset($idsTrab);
                            foreach ($trabajadores as  $idTra) {

                                $idsTrab[] = $idTra['id'];
                            }
                        }
                    }
                     
                    if(!empty($idDocTrab)){
                        
                        $documentosTrabObligatorios = DB::table('xt_ssov2_configs_cargos_cats_docs_params')
                        ->join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                        ->where(['xt_ssov2_configs_cargos_cats_docs_params.cfg_id' => $folio['sso_cfgid']])
                        ->whereIn('xt_ssov2_configs_cargos_cats_docs_params.cargo_id', $idDocTrab )
                        ->where(['xt_ssov2_doctypes.doc_status' => 1])
                        ->distinct('xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                        ->get(['xt_ssov2_configs_cargos_cats_docs_params.doc_id'])->toArray();
                        unset($docIdTrab);
                        foreach ($documentosTrabObligatorios as  $doctrabObl){

                                $docIdTrab[] = $doctrabObl->doc_id;
                        }
                        $cuentaTra = count($documentosTrabObligatorios);
                    }

                    $totalDocumentosSolicitados = $cantidadTra * $cuentaTra;
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

                    if(!empty($idDoGlobales)){
                        $documentosGlobalesObligatorios = EstadoDocumento::distinct('upld_docid')->where('upld_sso_id',$folio["id"])->where('upld_status', '1')->where('upld_workerid', '0')->whereIn('upld_docid',$idDoGlobales)->get(['upld_vence_date','upld_upddat','upld_docaprob','upld_docaprob_uid','upld_rechazado','upld_venced','upld_aprobComen','upld_comments'])->toArray();
                    }
                    
                        $cantidadAprobados = 0;
                        $cantidadRechazados = 0;
                        $cantidadVencidos = 0;
                        $cantidadPorRevision = 0;
                        $cantidadAprobadoObse = 0;
                        $totalGlobales = 0;
                    if(!empty($documentosGlobalesObligatorios)){
                        foreach ($documentosGlobalesObligatorios as $value) {

                        $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
                        $fecha2 = $value["upld_vence_date"];
                        $fechaUpdate = $value["upld_upddat"];

                        if($value["upld_docaprob"] == 0 and $value["upld_docaprob_uid"] == 0 and $value["upld_rechazado"]==0 and $value["upld_venced"]== 0 and $value["upld_aprobComen"] == 0){
                                   
                                    $estadoDocumento ="PorRevision";
                            }
                            elseif(($value["upld_docaprob"] == 1 or $fecha_actual <= $fechaUpdate) and $fecha_actual < $fecha2 AND $value["upld_aprobComen"]== 0 and $value["upld_rechazado"] == 0){
                                
                                    $estadoDocumento ="Aprobado";
                            
                            }elseif (($value["upld_venced"]== 1  or $fecha_actual > $fecha2)and $value["upld_rechazado"] == 0 and $fecha2!= 0){
                                    $estadoDocumento ="Vencido";
                            }elseif ($value["upld_aprobComen"] == 1 and $value["upld_comments"]!="" and $value["upld_rechazado"] == 0){
                                    
                                    $estadoDocumento ="AprobadoObs";
                            }elseif ($value["upld_rechazado"] == 1) {
                                    
                                    $estadoDocumento ="Rechazado";
                            }

                            switch ($estadoDocumento) {
                                case 'PorRevision':
                                   $cantidadPorRevision +=1;
                                  break;
                                case 'Aprobado':
                                  $cantidadAprobados +=1; 
                                  break;
                                case 'Vencido':
                                  $cantidadVencidos +=1; ;
                                  break;
                                case 'AprobadoObs':
                                  $cantidadAprobadoObse +=1;
                                  break;
                                case 'Rechazado':
                                  $cantidadRechazados +=1; ;
                                  break;
                             }
                            $totalGlobales = $cantidadPorRevision + $cantidadAprobados + $cantidadVencidos + $cantidadAprobadoObse + $cantidadRechazados;
                            $totalFaltantes = $cuenta - $totalGlobales;
                            if($totalFaltantes < 0){
                                $totalGlobales = $totalGlobales - $totalFaltantes;
                                $totalFaltantes = 0;
                            }
                            $datosReporte["PorRevision"] = $cantidadPorRevision;  
                            $datosReporte["Aprobado"] = $cantidadAprobados;
                            $datosReporte["Vencido"] = $cantidadVencidos;
                            $datosReporte["AprobadoObs"] = $cantidadAprobadoObse; 
                            $datosReporte["Rechazado"] = $cantidadRechazados; 
                            $datosReporte["totalGlobales"] = $totalGlobales;
                            $datosReporte["totalFaltantes"] = $totalFaltantes;
                            $datosReporte["totalGlobalesFolio"] = $cuenta;   
                            $datosReporte["cantidadTra"] = 0;   
                        }   

                    }else{
                       
                        $datosReporte["PorRevision"] = $cantidadPorRevision;  
                        $datosReporte["Aprobado"] = $cantidadAprobados;
                        $datosReporte["Vencido"] = $cantidadVencidos;
                        $datosReporte["AprobadoObs"] = $cantidadAprobadoObse; 
                        $datosReporte["Rechazado"] = $cantidadRechazados; 
                        $datosReporte["totalGlobales"] = $totalGlobales;
                        $datosReporte["totalFaltantes"] = 0;
                        $datosReporte["totalGlobalesFolio"] = 0; 
                        $datosReporte["cantidadTra"] = 0;
                    }   
                  

                    // documentos por trabajador obligatorios
                   
                    if(!empty($docIdTrab)){
                        if(!empty($idsTrab)){
                            $documentosTrabajadoresObligatorios =EstadoDocumento::where('upld_sso_id',$folio["id"])->where('upld_status', '1')
                            ->whereIn('upld_docid',$docIdTrab)
                            ->whereIn('upld_workerid',$idsTrab)
                            ->get(['upld_vence_date','upld_upddat','upld_docaprob','upld_docaprob_uid','upld_rechazado','upld_venced','upld_aprobComen','upld_comments'])->toArray();
                        }
                    }

                    $cantidadAprobadosTrab = 0;
                    $cantidadRechazadosTrab = 0;
                    $cantidadVencidosTrab = 0;
                    $cantidadPorRevisionTrab = 0;
                    $cantidadAprobadoObseTrab = 0;
                    $totalTrab = 0;

                    if(!empty($documentosTrabajadoresObligatorios)){

                        foreach ($documentosTrabajadoresObligatorios as $valueTtra) {

                      
                        $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
                        $fecha2 = $valueTtra["upld_vence_date"];
                        $fechaUpdate = $valueTtra["upld_upddat"];

                        if($valueTtra["upld_docaprob"] == 0 and $valueTtra["upld_docaprob_uid"] == 0 and $valueTtra["upld_rechazado"]==0 and $valueTtra["upld_venced"]== 0 and $valueTtra["upld_aprobComen"] == 0){
                                   
                                    $estadoDocumentoTra ="PorRevision";
                            }
                            elseif(($valueTtra["upld_docaprob"] == 1 or $fecha_actual <= $fechaUpdate) and $fecha_actual < $fecha2 AND $valueTtra["upld_aprobComen"]== 0 and $valueTtra["upld_rechazado"] == 0){
                                
                                    $estadoDocumentoTra ="Aprobado";
                            
                            }elseif (($valueTtra["upld_venced"]== 1  or $fecha_actual > $fecha2)and $valueTtra["upld_rechazado"] == 0 and $fecha2!= 0){
                                    $estadoDocumentoTra ="Vencido";
                            }elseif ($valueTtra["upld_aprobComen"] == 1 and $valueTtra["upld_comments"]!="" and $valueTtra["upld_rechazado"] == 0){
                                    
                                    $estadoDocumentoTra ="AprobadoObs";
                            }elseif ($valueTtra["upld_rechazado"] == 1) {
                                    
                                    $estadoDocumentoTra ="Rechazado";
                            }

                            switch ($estadoDocumentoTra) {
                                case 'PorRevision':
                                   $cantidadPorRevisionTrab +=1;
                                  break;
                                case 'Aprobado':
                                  $cantidadAprobadosTrab +=1; 
                                  break;
                                case 'Vencido':
                                  $cantidadVencidosTrab +=1; ;
                                  break;
                                case 'AprobadoObs':
                                  $cantidadAprobadoObseTrab +=1;
                                  break;
                                case 'Rechazado':
                                  $cantidadRechazadosTrab +=1; ;
                                  break;
                             }
                            $totalTrab = $cantidadPorRevisionTrab + $cantidadAprobadosTrab + $cantidadVencidosTrab + $cantidadAprobadoObseTrab + $cantidadRechazadosTrab;

                            $totalFaltantes = $totalDocumentosSolicitados-$totalTrab;
                            if($totalFaltantes < 0){
                                $totalFaltantes = 0;
                            }
                            $datosReporte["PorRevisionTrab"] = $cantidadPorRevisionTrab;  
                            $datosReporte["AprobadoTrab"] = $cantidadAprobadosTrab;
                            $datosReporte["VencidoTrab"] = $cantidadVencidosTrab;
                            $datosReporte["AprobadoObsTrab"] = $cantidadAprobadoObseTrab; 
                            $datosReporte["RechazadoTrab"] = $cantidadRechazadosTrab; 
                            $datosReporte["totalTrab"] = $totalTrab;   
                            $datosReporte["totalFaltanteTrabajador"] = $totalFaltantes;
                            $datosReporte["totalGlobalesTrabajador"] = $totalDocumentosSolicitados;
                            $datosReporte["Ntrabajadores"] = $totalDocumentosSolicitados; 
                            if ($cantidadTra!=0) {
                                $datosReporte["cantidadTra"] = $cantidadTra;        
                            }else{
                                $datosReporte["cantidadTra"] = $numeroTrabajadoresTotales;    
                            } 
        
                        }
                    }else{

                        $datosReporte["PorRevisionTrab"] = $cantidadPorRevisionTrab;  
                        $datosReporte["AprobadoTrab"] = $cantidadAprobadosTrab;
                        $datosReporte["VencidoTrab"] = $cantidadVencidosTrab;
                        $datosReporte["AprobadoObsTrab"] = $cantidadAprobadoObseTrab; 
                        $datosReporte["RechazadoTrab"] = $cantidadRechazadosTrab; 
                        $datosReporte["totalTrab"] = $totalTrab;
                        $datosReporte["totalFaltanteTrabajador"] = 0;
                        $datosReporte["totalGlobalesTrabajador"] = 0; 
                        $datosReporte["Ntrabajadores"] = $totalDocumentosSolicitados;  
                        if ($cantidadTra!=0) {
                                $datosReporte["cantidadTra"] = $cantidadTra;        
                        }else{
                                $datosReporte["cantidadTra"] = $numeroTrabajadoresTotales;    
                        } 


                    }

                    if(!empty($datosReporte)){
                        $listaDocObl[] = $datosReporte;
                    }   
                }

                //EXCEL

                Excel::create('Porcentaje Acreditanción Empresas', function($excel) use ($listaDocObl) {
                $nombreHoja = "";
                    foreach ($listaDocObl as $dato) {

                        if($dato['empresaSubContratista']!=""){
                            $nombreEm = explode(" ", $dato['empresaSubContratista']);
                            $empresa1 = substr($nombreEm[0], 0, 20); 
                            $empresa  = $empresa1.$dato['folio'];
                        }else{
                            $nombreEm = explode(" ", $dato['empresaContratista']);
                            $empresa1 = substr($nombreEm[0], 0, 20); 
                            $empresa  = $empresa1.$dato['folio'];
                        }

                        //echo $empresa."<br>";

                        $nombreHoja = ucwords(mb_strtoupper($empresa));
                           
                        $excel->sheet($nombreHoja, function($sheet) use($dato,$nombreHoja) { 

                          

                            $sheet->fromArray(
                            [
                                [
                                    'Documentos', //A
                                    'Folio', //B
                                    'Empresa principal', //C
                                    'RUT Principal',  //D
                                    'Empresa Contratista',//E
                                    'RUT Contratista', //F
                                    'Empresa Sub Contratista', //G
                                    'RUT Sub Contratista', //H
                                    'Total Documentos Subidos', //I
                                    'Revision', // J
                                    'Aprobados', //K
                                    'Vencidos',  // L
                                    'Aprobados Obs', // M
                                    'Rechazados', // N
                                    'Faltantes', //O
                                    'Total Documentos', //P
                                    'Total trabajadores' //Q
                                ],
                                [
                                    'Globales',
                                    $dato['folio'], 
                                    $dato['empresaPrincipal'], 
                                    $dato['rutEmpresaPrincipal'],
                                    $dato['empresaContratista'],
                                    $dato['rutEmpresaContratista'],
                                    $dato['empresaSubContratista'],
                                    $dato['rutEmpresaSubContratista'],
                                    $dato['totalGlobales'],
                                    $dato['PorRevision'],
                                    $dato['Aprobado'],
                                    $dato['Vencido'],
                                    $dato['AprobadoObs'],
                                    $dato['Rechazado'],
                                    $dato['totalFaltantes'],
                                    $dato['totalGlobalesFolio'],
                                    $dato['cantidadTra']
                                ],
                                [
                                    'trabajadores', 
                                    $dato['folio'], 
                                    $dato['empresaPrincipal'], 
                                    $dato['rutEmpresaPrincipal'],
                                    $dato['empresaContratista'],
                                    $dato['rutEmpresaContratista'],
                                    $dato['empresaSubContratista'],
                                    $dato['rutEmpresaSubContratista'],
                                    $dato['totalTrab'],
                                    $dato['PorRevisionTrab'],
                                    $dato['AprobadoTrab'],
                                    $dato['VencidoTrab'],
                                    $dato['AprobadoObsTrab'],
                                    $dato['RechazadoTrab'],
                                    $dato['totalFaltanteTrabajador'],
                                    $dato['totalGlobalesTrabajador'],
                                    $dato['cantidadTra'],
                                ]      
                            ],null, 'A2', false, false
                            );

                            /// globales
                            $labels1 = [
                                new  \PHPExcel_Chart_DataSeriesValues('String', $nombreHoja. '!$A$2', null, 1), // 2011
                            ];

                            $categories1 = [
                                new \PHPExcel_Chart_DataSeriesValues('String', $nombreHoja. '!$J$2:$O$2', null, 5), // Q1 to Q4
                            ];

                             $values1 = [
                                new \PHPExcel_Chart_DataSeriesValues('Number', $nombreHoja. '!$J$3:$O$3', null, 5),
                            ];

                            

                           $series = new \PHPExcel_Chart_DataSeries(
                                \PHPExcel_Chart_DataSeries::TYPE_PIECHART,       // plotType
                                null,  // plotGrouping
                                range(0, count($values1)-1),           // plotOrder
                                $labels1,                              // plotLabel
                                $categories1,                               // plotCategory
                                $values1                               // plotValues
                            );

                            //  Set up a layout object for the Pie chart
                            $layout1 = new \PHPExcel_Chart_Layout();
                            $layout1->setShowVal(TRUE);
                            $layout1->setShowPercent(TRUE);

                            //  Set the series in the plot area
                            $plotarea1 = new \PHPExcel_Chart_PlotArea($layout1, array($series));
                            //  Set the chart legend
                            $legend1 = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                           
                            $title1 = new \PHPExcel_Chart_Title('Porcentaje Documentos Globales');


                            //  Create the chart
                            $chart1 = new \PHPExcel_Chart(
                                'Porcentaje Documentos Globales',       // name
                                $title1,        // title
                                $legend1,       // legend
                                $plotarea1,     // plotArea
                                true,           // plotVisibleOnly
                                0,              // displayBlanksAs
                                NULL,           // xAxisLabel
                                NULL            // yAxisLabel       - Pie charts don't have a Y-Axis
                            );

                            //  Set the position where the chart should appear in the worksheet
                            $chart1->setTopLeftPosition('A7');
                            $chart1->setBottomRightPosition('F20');
                            $sheet->addChart($chart1);

                            //// trabajadores //////


                            $labels2 = [
                                new  \PHPExcel_Chart_DataSeriesValues('String', $nombreHoja. '!$A$2', null, 1), // 2011
                            ];

                            $categories2 = [
                                new \PHPExcel_Chart_DataSeriesValues('String', $nombreHoja. '!$J$2:$O$2', null, 5), // Q1 to Q4
                            ];

                            $values2 = [
                                new \PHPExcel_Chart_DataSeriesValues('Number', $nombreHoja. '!$J$4:$O$4', null, 5),
                            ];

                            

                           $series2 = new \PHPExcel_Chart_DataSeries(
                                \PHPExcel_Chart_DataSeries::TYPE_PIECHART,       // plotType
                                null,  // plotGrouping
                                range(0, count($values2)-1),           // plotOrder
                                $labels2,                              // plotLabel
                                $categories2,                               // plotCategory
                                $values2                               // plotValues
                            );

                            //  Set up a layout object for the Pie chart
                            $layout2 = new \PHPExcel_Chart_Layout();
                            $layout2->setShowVal(TRUE);
                            $layout2->setShowPercent(TRUE);

                            //  Set the series in the plot area
                            $plotarea2 = new \PHPExcel_Chart_PlotArea($layout2, array($series2));
                            //  Set the chart legend
                            $legend2 = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                           
                            $title2 = new \PHPExcel_Chart_Title('Porcentaje Documentos trabajadores');


                            //  Create the chart
                            $chart2 = new \PHPExcel_Chart(
                                'Porcentaje Documentos trabajadores',       // name
                                $title2,        // title
                                $legend2,       // legend
                                $plotarea2,     // plotArea
                                true,           // plotVisibleOnly
                                0,              // displayBlanksAs
                                NULL,           // xAxisLabel
                                NULL            // yAxisLabel       - Pie charts don't have a Y-Axis
                            );

                            //  Set the position where the chart should appear in the worksheet
                            $chart2->setTopLeftPosition('I7');
                            $chart2->setBottomRightPosition('O20');
                            $sheet->addChart($chart2);

                        });

                    } 
                                     
                })->export('xlsx');
            }else{
                $WORK = 0;
                return view('PorcentajeCumplimientoSSO.index',compact('datosUsuarios','EmpresasP','certificacion','usuarioAqua','usuarioABBChile','WORK','usuarioNOKactivo','usuarioClaroChile')); 
            }
        }else{

            $idFolios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipalR)
            ->whereIn('sso_comp_rut',$rutcontratistasR)
            ->where('sso_status',1)->orderBy('id', 'ASC')->get(['id','sso_mcomp_rut','sso_mcomp_name','sso_mcomp_dv','sso_comp_rut','sso_comp_name','sso_comp_dv','sso_subcomp_active','sso_subcomp_rut','sso_subcomp_dv','sso_subcomp_name','sso_cfgid'])->toArray();
            if(!empty($idFolios)){
                foreach ($idFolios as  $idfolio) {
                    $idsf[] = $idfolio['id'];
                } 

                /// documentos trabajador
                $documentosTrabajadoresObligatoriosCargo=trabajadorSSO::distinct('worker_cargoid')->whereIn('sso_id',$idsf)->get(['worker_cargoid'])->toArray();

                foreach ($documentosTrabajadoresObligatoriosCargo as  $docTra) {

                        $idDocTrab[] = $docTra['worker_cargoid'];
                }

                if(!empty($idDocTrab)){

                 $documentosTrabObligatorios = DB::table('xt_ssov2_configs_cargos_cats_docs_params')
                        ->join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                        ->where(['xt_ssov2_configs_cargos_cats_docs_params.cfg_id' => $folio['sso_cfgid']])
                        ->whereIn('xt_ssov2_configs_cargos_cats_docs_params.cargo_id', $idDocTrab )
                        ->where(['xt_ssov2_doctypes.doc_status' => 1])
                        ->distinct('xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                        ->get(['xt_ssov2_configs_cargos_cats_docs_params.doc_id'])->toArray();
                    unset($docIdTrab);
                    foreach ($documentosTrabObligatorios as  $doctrabObl) {

                            $docIdTrab[] = $doctrabObl->doc_id;
                    }
                }
                $totalDocumentosSolicitados =0;
                $cantidadTra = 0;
                $cuentaTra = 0;
                foreach ($idFolios as $folio) {

                    $documentosGlobalesObligatoriosF = DB::table('xt_ssov2_configs_glbdocs')
                    ->join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_configs_glbdocs.glb_docid')
                    ->where(['xt_ssov2_configs_glbdocs.cfg_id' => $folio['sso_cfgid']])
                    ->where(['xt_ssov2_doctypes.doc_status' => 1 ])
                    ->where(['xt_ssov2_doctypes.doc_type' => 0])
                    ->where(['xt_ssov2_configs_glbdocs.glb_obligact' => 1])
                    ->orderBy('xt_ssov2_configs_glbdocs.glb_obligact', 'DESC')
                    ->get(['xt_ssov2_doctypes.id','xt_ssov2_doctypes.doc_name','xt_ssov2_configs_glbdocs.glb_obligact'])->toArray();
                

                    $cuenta = count($documentosGlobalesObligatoriosF);

                    if(!empty($documentosGlobalesObligatoriosF)){
                        unset($idDoGlobales);

                        foreach ($documentosGlobalesObligatoriosF as  $docGlo) {

                                $idDoGlobales[] = $docGlo->id;
                        }
                    }

                    /// trabajadores 
                    /// documentos trabajador
                    $documentosTrabajadoresObligatoriosCargo=trabajadorSSO::distinct('worker_cargoid')->where('sso_id',$folio['id'])->get(['worker_cargoid'])->toArray();
                    
                    if(!empty($documentosTrabajadoresObligatoriosCargo)){
                        unset($idDocTrab);
                        foreach ($documentosTrabajadoresObligatoriosCargo as  $docTra) {

                            $idDocTrab[] = $docTra['worker_cargoid'];
                        }
                        $trabajadores=trabajadorSSO::distinct('id')->where('sso_id',$folio['id'])->where('worker_status',1)->get(['id'])->toArray();
                        $cantidadTra = count($trabajadores);
                        if($cantidadTra == 0){
                            $datosSolictud = Solicitud::distinct()->where('contractRut',$folio['sso_comp_rut'])->orderby('serial','DESC')->take(1)->get(['workersNumber','workerstotales','serial'])->toArray();
                        
                            if(!empty($datosSolictud)){
                                $numeroTrabajadoresTotales = $datosSolictud[0]['workerstotales'];
                                $numeroTrabajadores = $datosSolictud[0]['workersNumber'];
                            } 
                        }else{
                                unset($idsTrab);
                            foreach ($trabajadores as  $idTra) {

                                $idsTrab[] = $idTra['id'];
                            }

                        }
                    }
                     
                    if(!empty($idDocTrab)){
                        
                        $documentosTrabObligatorios = DB::table('xt_ssov2_configs_cargos_cats_docs_params')
                        ->join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                        ->where(['xt_ssov2_configs_cargos_cats_docs_params.cfg_id' => $folio['sso_cfgid']])
                        ->whereIn('xt_ssov2_configs_cargos_cats_docs_params.cargo_id', $idDocTrab )
                        ->where(['xt_ssov2_doctypes.doc_status' => 1])
                        ->distinct('xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                        ->get(['xt_ssov2_configs_cargos_cats_docs_params.doc_id'])->toArray();
                        unset($docIdTrab);
                        foreach ($documentosTrabObligatorios as  $doctrabObl){

                                $docIdTrab[] = $doctrabObl->doc_id;
                        }
                        $cuentaTra = count($documentosTrabObligatorios);
                    }

                    $totalDocumentosSolicitados = $cantidadTra * $cuentaTra;
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

                    if(!empty($idDoGlobales)){
                        $documentosGlobalesObligatorios = EstadoDocumento::distinct('upld_docid')->where('upld_sso_id',$folio["id"])->where('upld_status', '1')->where('upld_workerid', '0')->whereIn('upld_docid',$idDoGlobales)->get(['upld_vence_date','upld_upddat','upld_docaprob','upld_docaprob_uid','upld_rechazado','upld_venced','upld_aprobComen','upld_comments'])->toArray();
                    }
                    
                        $cantidadAprobados = 0;
                        $cantidadRechazados = 0;
                        $cantidadVencidos = 0;
                        $cantidadPorRevision = 0;
                        $cantidadAprobadoObse = 0;
                        $totalGlobales = 0;
                    if(!empty($documentosGlobalesObligatorios)){
                        foreach ($documentosGlobalesObligatorios as $value) {

                        $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
                        $fecha2 = $value["upld_vence_date"];
                        $fechaUpdate = $value["upld_upddat"];

                        if($value["upld_docaprob"] == 0 and $value["upld_docaprob_uid"] == 0 and $value["upld_rechazado"]==0 and $value["upld_venced"]== 0 and $value["upld_aprobComen"] == 0){
                                   
                                    $estadoDocumento ="PorRevision";
                            }
                            elseif(($value["upld_docaprob"] == 1 or $fecha_actual <= $fechaUpdate) and $fecha_actual < $fecha2 AND $value["upld_aprobComen"]== 0 and $value["upld_rechazado"] == 0){
                                
                                    $estadoDocumento ="Aprobado";
                            
                            }elseif (($value["upld_venced"]== 1  or $fecha_actual > $fecha2)and $value["upld_rechazado"] == 0 and $fecha2!= 0){
                                    $estadoDocumento ="Vencido";
                            }elseif ($value["upld_aprobComen"] == 1 and $value["upld_comments"]!="" and $value["upld_rechazado"] == 0){
                                    
                                    $estadoDocumento ="AprobadoObs";
                            }elseif ($value["upld_rechazado"] == 1) {
                                    
                                    $estadoDocumento ="Rechazado";
                            }

                            switch ($estadoDocumento) {
                                case 'PorRevision':
                                   $cantidadPorRevision +=1;
                                  break;
                                case 'Aprobado':
                                  $cantidadAprobados +=1; 
                                  break;
                                case 'Vencido':
                                  $cantidadVencidos +=1; ;
                                  break;
                                case 'AprobadoObs':
                                  $cantidadAprobadoObse +=1;
                                  break;
                                case 'Rechazado':
                                  $cantidadRechazados +=1; ;
                                  break;
                             }
                            $totalGlobales = $cantidadPorRevision + $cantidadAprobados + $cantidadVencidos + $cantidadAprobadoObse + $cantidadRechazados;
                             $totalFaltantes = $cuenta - $totalGlobales;
                            if($totalFaltantes < 0){
                                $totalGlobales = $totalGlobales - $totalFaltantes;
                                $totalFaltantes = 0;
                            }
                            $datosReporte["PorRevision"] = $cantidadPorRevision;  
                            $datosReporte["Aprobado"] = $cantidadAprobados;
                            $datosReporte["Vencido"] = $cantidadVencidos;
                            $datosReporte["AprobadoObs"] = $cantidadAprobadoObse; 
                            $datosReporte["Rechazado"] = $cantidadRechazados; 
                            $datosReporte["totalGlobales"] = $totalGlobales;
                            $datosReporte["totalFaltantes"] = $totalFaltantes;
                            $datosReporte["totalGlobalesFolio"] = $cuenta;   
                            $datosReporte["cantidadTra"] = 0;   
                        }   

                    }else{
                       
                        $datosReporte["PorRevision"] = $cantidadPorRevision;  
                        $datosReporte["Aprobado"] = $cantidadAprobados;
                        $datosReporte["Vencido"] = $cantidadVencidos;
                        $datosReporte["AprobadoObs"] = $cantidadAprobadoObse; 
                        $datosReporte["Rechazado"] = $cantidadRechazados; 
                        $datosReporte["totalGlobales"] = $totalGlobales;
                        $datosReporte["totalFaltantes"] = 0;
                        $datosReporte["totalGlobalesFolio"] = 0; 
                        $datosReporte["cantidadTra"] = 0;
                    }   
                  

                    // documentos por trabajador obligatorios
                   
                    if(!empty($docIdTrab)){
                        if(!empty($idsTrab)){

                            $documentosTrabajadoresObligatorios =EstadoDocumento::where('upld_sso_id',$folio["id"])->where('upld_status', '1')
                            ->whereIn('upld_docid',$docIdTrab)
                            ->whereIn('upld_workerid',$idsTrab)->get(['upld_vence_date','upld_upddat','upld_docaprob','upld_docaprob_uid','upld_rechazado','upld_venced','upld_aprobComen','upld_comments'])->toArray();
                        }
                    }
                    $cantidadAprobadosTrab = 0;
                    $cantidadRechazadosTrab = 0;
                    $cantidadVencidosTrab = 0;
                    $cantidadPorRevisionTrab = 0;
                    $cantidadAprobadoObseTrab = 0;
                    $totalTrab = 0;

                    if(!empty($documentosTrabajadoresObligatorios)){

                        foreach ($documentosTrabajadoresObligatorios as $valueTtra) {

                      
                        $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
                        $fecha2 = $valueTtra["upld_vence_date"];
                        $fechaUpdate = $valueTtra["upld_upddat"];

                        if($valueTtra["upld_docaprob"] == 0 and $valueTtra["upld_docaprob_uid"] == 0 and $valueTtra["upld_rechazado"]==0 and $valueTtra["upld_venced"]== 0 and $valueTtra["upld_aprobComen"] == 0){
                                   
                                    $estadoDocumentoTra ="PorRevision";
                            }
                            elseif(($valueTtra["upld_docaprob"] == 1 or $fecha_actual <= $fechaUpdate) and $fecha_actual < $fecha2 AND $valueTtra["upld_aprobComen"]== 0 and $valueTtra["upld_rechazado"] == 0){
                                
                                    $estadoDocumentoTra ="Aprobado";
                            
                            }elseif (($valueTtra["upld_venced"]== 1  or $fecha_actual > $fecha2)and $valueTtra["upld_rechazado"] == 0 and $fecha2!= 0){
                                    $estadoDocumentoTra ="Vencido";
                            }elseif ($valueTtra["upld_aprobComen"] == 1 and $valueTtra["upld_comments"]!="" and $valueTtra["upld_rechazado"] == 0){
                                    
                                    $estadoDocumentoTra ="AprobadoObs";
                            }elseif ($valueTtra["upld_rechazado"] == 1) {
                                    
                                    $estadoDocumentoTra ="Rechazado";
                            }

                            switch ($estadoDocumentoTra) {
                                case 'PorRevision':
                                   $cantidadPorRevisionTrab +=1;
                                  break;
                                case 'Aprobado':
                                  $cantidadAprobadosTrab +=1; 
                                  break;
                                case 'Vencido':
                                  $cantidadVencidosTrab +=1; ;
                                  break;
                                case 'AprobadoObs':
                                  $cantidadAprobadoObseTrab +=1;
                                  break;
                                case 'Rechazado':
                                  $cantidadRechazadosTrab +=1; ;
                                  break;
                             }
                            $totalTrab = $cantidadPorRevisionTrab + $cantidadAprobadosTrab + $cantidadVencidosTrab + $cantidadAprobadoObseTrab + $cantidadRechazadosTrab;
                            $totalFaltantes = $totalDocumentosSolicitados-$totalTrab;
                            if($totalFaltantes < 0){
                                $totalglobalestra = $totalDocumentosSolicitados - $totalFaltantes;
                                $totalFaltantes = 0;
                            }else{
                                $totalglobalestra = $totalDocumentosSolicitados;
                            }
                            $datosReporte["PorRevisionTrab"] = $cantidadPorRevisionTrab;  
                            $datosReporte["AprobadoTrab"] = $cantidadAprobadosTrab;
                            $datosReporte["VencidoTrab"] = $cantidadVencidosTrab;
                            $datosReporte["AprobadoObsTrab"] = $cantidadAprobadoObseTrab; 
                            $datosReporte["RechazadoTrab"] = $cantidadRechazadosTrab; 
                            $datosReporte["totalTrab"] = $totalTrab;   
                            $datosReporte["totalFaltanteTrabajador"] = $totalFaltantes;
                            $datosReporte["totalGlobalesTrabajador"] = $totalglobalestra;
                            $datosReporte["Ntrabajadores"] = $totalDocumentosSolicitados; 
                            if ($cantidadTra!=0) {
                                $datosReporte["cantidadTra"] = $cantidadTra;        
                            }else{
                                $datosReporte["cantidadTra"] = $numeroTrabajadoresTotales;    
                            } 
        
                        }
                    }else{

                        $datosReporte["PorRevisionTrab"] = $cantidadPorRevisionTrab;  
                        $datosReporte["AprobadoTrab"] = $cantidadAprobadosTrab;
                        $datosReporte["VencidoTrab"] = $cantidadVencidosTrab;
                        $datosReporte["AprobadoObsTrab"] = $cantidadAprobadoObseTrab; 
                        $datosReporte["RechazadoTrab"] = $cantidadRechazadosTrab; 
                        $datosReporte["totalTrab"] = $totalTrab;
                        $datosReporte["totalFaltanteTrabajador"] = 0;
                        $datosReporte["totalGlobalesTrabajador"] = 0; 
                        $datosReporte["Ntrabajadores"] = $totalDocumentosSolicitados;  
                        if ($cantidadTra!=0) {
                                $datosReporte["cantidadTra"] = $cantidadTra;        
                        }else{
                                $datosReporte["cantidadTra"] = $numeroTrabajadoresTotales;    
                        } 


                    }

                    if(!empty($datosReporte)){
                        $listaDocObl[] = $datosReporte;
                    }   
                }
               // exit();

                //EXCEL

                Excel::create('Porcentaje Acreditanción Empresas', function($excel) use ($listaDocObl) {
                    $nombreHoja = "";
                    foreach ($listaDocObl as $dato) {

                        if($dato['empresaSubContratista']!=""){
                            $nombreEm = explode(" ", $dato['empresaSubContratista']);
                            $empresa1 = substr($nombreEm[0], 0, 20); 
                            $empresa  = $empresa1.$dato['folio'];
                           

                        }else{
                            $nombreEm = explode(" ", $dato['empresaContratista']);
                            $empresa1 = substr($nombreEm[0], 0, 20); 
                            $empresa  = $empresa1.$dato['folio'];
                           
                        }
                        //echo $empresa."</br>";
                        $nombreHoja = ucwords(mb_strtoupper($empresa));
                           
                        $excel->sheet($nombreHoja, function($sheet) use($dato,$nombreHoja) { 

                          

                            $sheet->fromArray(
                            [
                                [
                                    'Documentos', //A
                                    'Folio', //B
                                    'Empresa principal', //C
                                    'RUT Principal',  //D
                                    'Empresa Contratista',//E
                                    'RUT Contratista', //F
                                    'Empresa Sub Contratista', //G
                                    'RUT Sub Contratista', //H
                                    'Total Documentos Subidos', //I
                                    'Revision', // J
                                    'Aprobados', //K
                                    'Vencidos',  // L
                                    'Aprobados Obs', // M
                                    'Rechazados', // N
                                    'Faltantes', //O
                                    'Total Documentos', //P
                                    'Total trabajadores' //Q
                                ],
                                [
                                    'Globales',
                                    $dato['folio'], 
                                    $dato['empresaPrincipal'], 
                                    $dato['rutEmpresaPrincipal'],
                                    $dato['empresaContratista'],
                                    $dato['rutEmpresaContratista'],
                                    $dato['empresaSubContratista'],
                                    $dato['rutEmpresaSubContratista'],
                                    $dato['totalGlobales'],
                                    $dato['PorRevision'],
                                    $dato['Aprobado'],
                                    $dato['Vencido'],
                                    $dato['AprobadoObs'],
                                    $dato['Rechazado'],
                                    $dato['totalFaltantes'],
                                    $dato['totalGlobalesFolio'],
                                    $dato['cantidadTra']
                                ],
                                [
                                    'trabajadores', 
                                    $dato['folio'], 
                                    $dato['empresaPrincipal'], 
                                    $dato['rutEmpresaPrincipal'],
                                    $dato['empresaContratista'],
                                    $dato['rutEmpresaContratista'],
                                    $dato['empresaSubContratista'],
                                    $dato['rutEmpresaSubContratista'],
                                    $dato['totalTrab'],
                                    $dato['PorRevisionTrab'],
                                    $dato['AprobadoTrab'],
                                    $dato['VencidoTrab'],
                                    $dato['AprobadoObsTrab'],
                                    $dato['RechazadoTrab'],
                                    $dato['totalFaltanteTrabajador'],
                                    $dato['totalGlobalesTrabajador'],
                                    $dato['cantidadTra'],
                                ]      
                            ],null, 'A2', false, false
                            );

                            /// globales
                            $labels1 = [
                                new  \PHPExcel_Chart_DataSeriesValues('String', $nombreHoja. '!$A$2', null, 1), // 2011
                            ];

                            $categories1 = [
                                new \PHPExcel_Chart_DataSeriesValues('String', $nombreHoja. '!$J$2:$O$2', null, 5), // Q1 to Q4
                            ];

                             $values1 = [
                                new \PHPExcel_Chart_DataSeriesValues('Number', $nombreHoja. '!$J$3:$O$3', null, 5),
                            ];

                            

                           $series = new \PHPExcel_Chart_DataSeries(
                                \PHPExcel_Chart_DataSeries::TYPE_PIECHART,       // plotType
                                null,  // plotGrouping
                                range(0, count($values1)-1),           // plotOrder
                                $labels1,                              // plotLabel
                                $categories1,                               // plotCategory
                                $values1                               // plotValues
                            );

                            //  Set up a layout object for the Pie chart
                            $layout1 = new \PHPExcel_Chart_Layout();
                            $layout1->setShowVal(TRUE);
                            $layout1->setShowPercent(TRUE);

                            //  Set the series in the plot area
                            $plotarea1 = new \PHPExcel_Chart_PlotArea($layout1, array($series));
                            //  Set the chart legend
                            $legend1 = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                           
                            $title1 = new \PHPExcel_Chart_Title('Porcentaje Documentos Globales');


                            //  Create the chart
                            $chart1 = new \PHPExcel_Chart(
                                'Porcentaje Documentos Globales',       // name
                                $title1,        // title
                                $legend1,       // legend
                                $plotarea1,     // plotArea
                                true,           // plotVisibleOnly
                                0,              // displayBlanksAs
                                NULL,           // xAxisLabel
                                NULL            // yAxisLabel       - Pie charts don't have a Y-Axis
                            );

                            //  Set the position where the chart should appear in the worksheet
                            $chart1->setTopLeftPosition('A7');
                            $chart1->setBottomRightPosition('F20');
                            $sheet->addChart($chart1);

                            //// trabajadores //////


                            $labels2 = [
                                new  \PHPExcel_Chart_DataSeriesValues('String', $nombreHoja. '!$A$2', null, 1), // 2011
                            ];

                            $categories2 = [
                                new \PHPExcel_Chart_DataSeriesValues('String', $nombreHoja. '!$J$2:$O$2', null, 5), // Q1 to Q4
                            ];

                            $values2 = [
                                new \PHPExcel_Chart_DataSeriesValues('Number', $nombreHoja. '!$J$4:$O$4', null, 5),
                            ];

                            

                           $series2 = new \PHPExcel_Chart_DataSeries(
                                \PHPExcel_Chart_DataSeries::TYPE_PIECHART,       // plotType
                                null,  // plotGrouping
                                range(0, count($values2)-1),           // plotOrder
                                $labels2,                              // plotLabel
                                $categories2,                               // plotCategory
                                $values2                               // plotValues
                            );

                            //  Set up a layout object for the Pie chart
                            $layout2 = new \PHPExcel_Chart_Layout();
                            $layout2->setShowVal(TRUE);
                            $layout2->setShowPercent(TRUE);

                            //  Set the series in the plot area
                            $plotarea2 = new \PHPExcel_Chart_PlotArea($layout2, array($series2));
                            //  Set the chart legend
                            $legend2 = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                           
                            $title2 = new \PHPExcel_Chart_Title('Porcentaje Documentos trabajadores');


                            //  Create the chart
                            $chart2 = new \PHPExcel_Chart(
                                'Porcentaje Documentos trabajadores',       // name
                                $title2,        // title
                                $legend2,       // legend
                                $plotarea2,     // plotArea
                                true,           // plotVisibleOnly
                                0,              // displayBlanksAs
                                NULL,           // xAxisLabel
                                NULL            // yAxisLabel       - Pie charts don't have a Y-Axis
                            );

                            //  Set the position where the chart should appear in the worksheet
                            $chart2->setTopLeftPosition('I7');
                            $chart2->setBottomRightPosition('O20');
                            $sheet->addChart($chart2);

                        });

                    } 
                                            
                })->export('xlsx');
            }else{
                $WORK = 0;
                return view('PorcentajeCumplimientoSSO.index',compact('datosUsuarios','EmpresasP','certificacion','usuarioAqua','usuarioABBChile','WORK','usuarioNOKactivo','usuarioClaroChile'));
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
