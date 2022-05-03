<?php

namespace App\Http\Controllers;
use DB;
use Excel;
use DateTime;
use App\DatosUsuarioLogin;
use App\UsuarioContratista;
use App\UsuarioPrincipal;
use App\FolioSso;
use App\empresaPrincipal;
use App\ConfiguracionSso;
use App\Month;
use App\Contratista;
use App\EstadoDocumento;
use App\trabajadorSSO;
use App\Documento;
use App\CargoCateDoc;


use Illuminate\Http\Request;

class ReporteSSOClaroController extends Controller
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

                return view('reporteSSOAcreditado.index',compact('datosUsuarios','EmpresasP','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile')); 

            }
            if($datosUsuarios->type ==2 || $datosUsuarios->type ==1 ){

                $EmpresasP = FolioSso::distinct()->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['sso_mcomp_name','sso_mcomp_rut']);

                return view('reporteSSOAcreditado.index',compact('datosUsuarios','EmpresasP','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile')); 

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
             ->where('sso_status',1)->orderBy('id', 'ASC')->get(['id','sso_mcomp_rut','sso_mcomp_name','sso_mcomp_dv','sso_comp_rut','sso_comp_name','sso_comp_dv','sso_subcomp_active','sso_subcomp_rut','sso_subcomp_dv','sso_subcomp_name','sso_cfgid','sso_project'])->toArray();


            $ssoCfgid = ConfiguracionSso::whereIn('cfg_mcomp_rut',$rutprincipalR)
            ->where('cfg_status',1)
            ->get(['id'])->toArray();
            $sso_cfgid = $ssoCfgid[0]['id'];

            $documentosCategorias = DB::table('xt_ssov2_configs_cargos')
            ->join('xt_ssov2_configs_cargos_cats', 'xt_ssov2_configs_cargos.cfg_id', '=', 'xt_ssov2_configs_cargos_cats.cfg_id')
            ->join('xt_ssov2_doccats', 'xt_ssov2_configs_cargos_cats.cat_id', '=', 'xt_ssov2_doccats.id')
            ->join('xt_ssov2_configs_cargos_cats_docs', 'xt_ssov2_configs_cargos_cats_docs.cfg_id', '=', 'xt_ssov2_configs_cargos.cfg_id')
            ->join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_configs_cargos_cats_docs.doc_id')
            ->where(['xt_ssov2_configs_cargos.cfg_id' => $sso_cfgid])
            ->where(['xt_ssov2_doccats.cat_status' => 1])
            ->where(['xt_ssov2_doctypes.doc_status' => 1])
            ->where(['xt_ssov2_doctypes.doc_type' => 1])
            ->orderBy('xt_ssov2_configs_cargos_cats.cat_order', 'ASC')
            ->distinct()->get(['xt_ssov2_doccats.id','xt_ssov2_doccats.cat_name','xt_ssov2_doccats.cat_desc','xt_ssov2_doctypes.doc_name','xt_ssov2_doctypes.doc_desc','xt_ssov2_configs_cargos_cats_docs.doc_id','xt_ssov2_configs_cargos_cats.cat_order'])->toArray();

            ///categorias y documentos ////
            foreach($documentosCategorias AS $doc)
            {
               $_CAT_DOCS[$doc->id][] = $doc;
               $_ALLDOCS_ACTIVE[$doc->id][$doc->doc_id] = 1;
               $_DISTINCTDOCSACTIVE[$doc->doc_id] = 1;
               $_CATNAMES[$doc->id] = $doc->cat_name;
               $_CATDOCDATA[$doc->id][$doc->doc_id] = $doc;

               $_DATA["STATS"]["DOC_CATS"][$doc->id]["cat_name"]            = mb_strtoupper($doc->cat_name, "UTF-8");
               
               $_DATA["STATS"]["DOC_CATS"][$doc->id]["cat_order"]           = (int)$doc->cat_order;
               $_DATA["STATS"]["DOC_CATS_MERGE"][$doc->id][$doc->doc_id]  = mb_strtoupper($doc->doc_name, "UTF-8");
            }

            $tablaCabecera='<table border="2">
                <thead>
                <tr>
                  <th style="background-color:#e3e3e3">Folio</th>
                  <th style="background-color:#e3e3e3">Empresa Contratista</th>
                  <th style="background-color:#e3e3e3">RUT Contratista</th>
                  <th style="background-color:#e3e3e3">Empresa Sub Contratista</th>
                  <th style="background-color:#e3e3e3">RUT Sub Contratista</th>
                  <th style="background-color:#e3e3e3">Nombres y Apellidos</th>
                  <th style="background-color:#e3e3e3">RUT Empleado</th>';
            $x = 0;
            $cantidadCategorias = count($_DATA["STATS"]["DOC_CATS"]);
            foreach ($_CATNAMES as $catNombre) {
               
                $tablaCabecera.='<th style="background-color:#e3e3e3">'.$catNombre.'</th>'; 
            } 

            $tablaCabecera.='<th style="background-color:#e3e3e3">Acreditacion</th>
            <th style="background-color:#e3e3e3">Dias Restantes Acreditacion</th>
            <th style="background-color:#e3e3e3">Vigencia</th>
            </tr></thead>';
            $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
            /// recorre folios //
            if(!empty($idFolios)){
                foreach ($idFolios as  $row) {
                    
                    $trabajadores = trabajadorSSO::where('worker_status','1')
                    ->where('sso_id',$row['id'])
                    ->get(['id AS workID',
                        'worker_name',
                        'worker_name1',
                        'worker_name2',
                        'worker_name3',
                        'worker_rut',
                        'worker_cargoid'])->toArray();

                    if(!empty($trabajadores)){
                        for($w = 0; $w < count($trabajadores) && $trabajadores != false; $w++){

                            $wrow = $trabajadores[$w];
                            $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["folio"]           = $row["id"];
                            $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["comp"]           = mb_strtoupper($row["sso_comp_name"], "UTF-8");
                            $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["comprut"]        = mb_strtoupper($row["sso_comp_rut"]."-".$row["sso_comp_dv"], "UTF-8");
                            $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["main"]           = mb_strtoupper($row["sso_mcomp_name"], "UTF-8");
                            $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["mainrut"]        = mb_strtoupper($row["sso_mcomp_rut"]."-".$row["sso_mcomp_dv"], "UTF-8");
                            $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["workername"]     = mb_strtoupper($wrow["worker_name"], "UTF-8");
                            $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["workerrut"]      = mb_strtoupper($wrow["worker_rut"], "UTF-8");
                            
                            $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["subrut"]         = "";
                            $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["subname"]        = "";
                            if((int)$row["sso_subcomp_active"]){
                                $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["subrut"]         = mb_strtoupper($row["sso_subcomp_rut"]."-".$row["sso_subcomp_dv"], "UTF-8");
                                $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["subname"]        = mb_strtoupper($row["sso_subcomp_name"], "UTF-8");
                            }

                            $documentosTrabObligatorios = DB::table('xt_ssov2_configs_cargos_cats_docs_params')
                            ->where('cfg_id', $row['sso_cfgid'])
                            ->where('cargo_id', $wrow["worker_cargoid"])
                            ->get(['cat_id','doc_cat_id','doc_id']);
                            
                            unset($_ACREDIT);
                            unset($_CATDOCDATA);
                            unset($_DOCSUPLOADES);
                            unset($dtlscatsobligxstate);
                            unset($dtlscatsobligxstate2);
     
                            foreach($documentosTrabObligatorios AS $param){
                                $_ACREDIT[$param->cat_id][$param->doc_cat_id][$param->doc_id] = 1;
                            }

                            //----------------------------------------------------------------------------------
                            // DOCUMENTOS SUBIDOS
                            //---------------------------------------------------------------------------------- 
                           
                            $documentoSubidos = EstadoDocumento::where('upld_sso_id',$row['id'])
                            ->where('upld_status', '1')
                            ->where('upld_type', '1')
                            ->where('upld_workerid', $wrow["workID"])
                          
                            ->get(['id','upld_catid','upld_docid','upld_docaprob','upld_vence_date','upld_venced','upld_aprobComen','upld_rechazado','upld_upddat'])->toArray();
                           

                           
                            foreach($documentoSubidos AS $docsuploadedrow){
                                
                                $_DOCSUPLOADES[$docsuploadedrow['upld_catid']][$docsuploadedrow['upld_docid']] = $docsuploadedrow;
                                    
                            }
                            $dateDifference = 0;
                            $years = 0;
                            $months = 0;
                            $days = 0;
                            $documentoFecha = EstadoDocumento::where('upld_sso_id',$row['id'])
                            ->where('upld_status', '1')
                            ->where('upld_type', '1')
                            ->where('upld_workerid', $wrow["workID"])
                            ->where('upld_vence_date','!=',0)
                            ->orderBy('upld_vence_date', 'ASC')
                            ->take(1)->get(['upld_vence_date'])->toArray();
                            if(isset($documentoFecha[0]['upld_vence_date'])){
                                $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["FECHAVENCE"] = date('d-m-Y', $documentoFecha[0]['upld_vence_date']);
                                $firstDate  = new \DateTime(date('Y-m-d',$fecha_actual));
                                $secondDate = new \DateTime(date('Y-m-d', $documentoFecha[0]['upld_vence_date']));
                                $intvl = $firstDate->diff($secondDate);

                               
                                if($fecha_actual > $documentoFecha[0]['upld_vence_date']){
                                    $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["DIAS"] = "-".$intvl->days;
                                }else{
                                    $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["DIAS"] = $intvl->days;
                                }
                            }else{
                                $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["FECHAVENCE"] = "";
                                $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["DIAS"] = "";

                            }

                            
                           
                            foreach(array_keys($_CAT_DOCS) AS $catid){
                                //$dtlscatsobligxstate[$catid]  = "Aprobado";
                                
                                foreach($_CAT_DOCS[$catid] AS $doc){
                               
                                    if(isset($_ACREDIT[$catid][$catid][$doc->doc_id])){
                                        if($_ACREDIT[$catid][$catid][$doc->doc_id]==1){
                                            if(isset($_DOCSUPLOADES[$catid][$doc->doc_id]["upld_docaprob"]) AND $_DOCSUPLOADES[$catid][$doc->doc_id]["upld_docaprob"] == 1 AND $_DOCSUPLOADES[$catid][$doc->doc_id]["upld_vence_date"] > $fecha_actual){
                                              
                                                $dtlscatsobligxstate[$catid][$doc->doc_id]  = 1;
                                            }else{
                                                
                                                $dtlscatsobligxstate[$catid][$doc->doc_id]  = 0;

                                            }
                                            
                                        }
                                    }
                                }
                            }
                         
                            foreach(array_keys($_CAT_DOCS) AS $catid){
                                $dtlscatsobligxstate2[$catid]  = 1;
                                  $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["CATSAPROBSTR"][$catid] = "SI";  
                                foreach($dtlscatsobligxstate[$catid] AS $doc){
                                    if($doc==0){
                                        $dtlscatsobligxstate2[$catid]  = 0;
                                        $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["CATSAPROBSTR"][$catid] = "NO";  
                                    }

                                }
                            }
                            $totalAcred = 0;
                            foreach($dtlscatsobligxstate2 AS $acred){
                                if($acred == 1){
                                    $totalAcred=$totalAcred+1; 
                                }
                            }
                            
                            if($totalAcred == $cantidadCategorias){
                                $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["ACREDITACION"]="ACREDITADO";
                            }else{
                                $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["ACREDITACION"]="NO ACREDITADO";
                            }   
                        }
                    }  
                }
            }
            
            $tablaCuerpo='<tbody>';
            foreach(array_keys($_DATA["STATS"]["WORKERS"]) AS $ssoid){
                $tablaCuerpo.='<tr>';
                foreach(array_keys($_DATA["STATS"]["WORKERS"][$ssoid]) AS $wrow){
                    $row = $_DATA["STATS"]["WORKERS"][$ssoid][$wrow];
                    
                    $tablaCuerpo.='<td>'.$row['folio'].'</td>';
                    $tablaCuerpo.='<td>'.$row['comp'].'</td>';
                    $tablaCuerpo.='<td>'.$row['comprut'].'</td>';
                    $tablaCuerpo.='<td>'.$row['subname'].'</td>';
                    $tablaCuerpo.='<td>'.$row['subrut'].'</td>';
                    $tablaCuerpo.='<td>'.$row['workername'].'</td>';
                    $tablaCuerpo.='<td>'.$row['workerrut'].'</td>';

                    $acreditado="NO ACREDITADO";
                    foreach(array_keys($_DATA["STATS"]["DOC_CATS"]) AS $catid){
                        //$catdata = $_DATA["STATS"]["DOC_CATS"][$catid];
                        $xstat = $_DATA["STATS"]["WORKERS"][$ssoid][$wrow]["CATSAPROBSTR"][$catid];
                        
                        $tablaCuerpo.='<td>'.$xstat.'</td>';
                    }
                    $acreditado= $_DATA["STATS"]["WORKERS"][$ssoid][$wrow]["ACREDITACION"];
                    if($acreditado == "ACREDITADO"){
                        $estilo = 'style="background-color:#2ECC71"';

                    }else{
                        $estilo = 'style="background-color:#EC7063"';
                    }
                    if($_DATA["STATS"]["WORKERS"][$ssoid][$wrow]["DIAS"] > 0){
                        $estilo2 = 'style="background-color:#2ECC71"';

                    }else{
                        $estilo2 = 'style="background-color:#EC7063"';
                    }
                    $tablaCuerpo.='<td '.$estilo.'>'.$acreditado.'</td>';
                    $tablaCuerpo.='<td '.$estilo2.'>'.$_DATA["STATS"]["WORKERS"][$ssoid][$wrow]["DIAS"].'</td>';
                    $tablaCuerpo.='<td>'.$_DATA["STATS"]["WORKERS"][$ssoid][$wrow]["FECHAVENCE"].'</td>';
                    $tablaCuerpo.='<tr>';      
                }

            }
            $tablaCuerpo.='</tbody></table>';
            $lista=$tablaCabecera.$tablaCuerpo; 

            Excel::create('Reporte Acreditacion', function($excel) use ($lista) {
                $excel->sheet('Lista General', function($sheet) use($lista) {    
                    $sheet->loadView('reporteCertificacion.excelCertificacion',compact('lista'));
                });
            })->export('xlsx'); 
                        
                      
        }if($cantidadContratista > 0){

            $idFolios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipalR)
            ->whereIn('sso_comp_rut',$rutcontratistasR)
            ->where('sso_status',1)->orderBy('id', 'ASC')->get(['id','sso_mcomp_rut','sso_mcomp_name','sso_mcomp_dv','sso_comp_rut','sso_comp_name','sso_comp_dv','sso_subcomp_active','sso_subcomp_rut','sso_subcomp_dv','sso_subcomp_name','sso_cfgid','sso_project'])->toArray();

            $ssoCfgid = ConfiguracionSso::whereIn('cfg_mcomp_rut',$rutprincipalR)
            ->where('cfg_status',1)
            ->get(['id'])->toArray();
            $sso_cfgid = $ssoCfgid[0]['id'];

            $documentosCategorias = DB::table('xt_ssov2_configs_cargos')
            ->join('xt_ssov2_configs_cargos_cats', 'xt_ssov2_configs_cargos.cfg_id', '=', 'xt_ssov2_configs_cargos_cats.cfg_id')
            ->join('xt_ssov2_doccats', 'xt_ssov2_configs_cargos_cats.cat_id', '=', 'xt_ssov2_doccats.id')
            ->join('xt_ssov2_configs_cargos_cats_docs', 'xt_ssov2_configs_cargos_cats_docs.cfg_id', '=', 'xt_ssov2_configs_cargos.cfg_id')
            ->join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_configs_cargos_cats_docs.doc_id')
            ->where(['xt_ssov2_configs_cargos.cfg_id' => $sso_cfgid])
            ->where(['xt_ssov2_doccats.cat_status' => 1])
            ->where(['xt_ssov2_doctypes.doc_status' => 1])
            ->where(['xt_ssov2_doctypes.doc_type' => 1])
            ->orderBy('xt_ssov2_configs_cargos_cats.cat_order', 'ASC')
            ->distinct()->get(['xt_ssov2_doccats.id','xt_ssov2_doccats.cat_name','xt_ssov2_doccats.cat_desc','xt_ssov2_doctypes.doc_name','xt_ssov2_doctypes.doc_desc','xt_ssov2_configs_cargos_cats_docs.doc_id','xt_ssov2_configs_cargos_cats.cat_order'])->toArray();

            foreach($documentosCategorias AS $doc)
            {
               $_CAT_DOCS[$doc->id][] = $doc;
               $_DISTINCTDOCSACTIVE[$doc->doc_id] = 1;
               $_CATNAMES[$doc->id] = $doc->cat_name;
               $_CATDOCDATA[$doc->id][$doc->doc_id] = $doc;

               $_DATA["STATS"]["DOC_CATS"][$doc->id]["cat_name"]            = mb_strtoupper($doc->cat_name, "UTF-8");
               
               $_DATA["STATS"]["DOC_CATS"][$doc->id]["cat_order"]           = (int)$doc->cat_order;
               $_DATA["STATS"]["DOC_CATS_MERGE"][$doc->id][$doc->doc_id]  = mb_strtoupper($doc->doc_name, "UTF-8");
            }

            $tablaCabecera='<table border="2">
                <thead>
                <tr>
                  <th style="background-color:#e3e3e3">Folio</th>
                  <th style="background-color:#e3e3e3">Empresa Contratista</th>
                  <th style="background-color:#e3e3e3">RUT Contratista</th>
                  <th style="background-color:#e3e3e3">Empresa Sub Contratista</th>
                  <th style="background-color:#e3e3e3">RUT Sub Contratista</th>
                  <th style="background-color:#e3e3e3">Nombres y Apellidos</th>
                  <th style="background-color:#e3e3e3">RUT Empleado</th>';
            $x = 0;
            $cantidadCategorias = count($_DATA["STATS"]["DOC_CATS"]);
            foreach ($_CATNAMES as $catNombre) {
               
                $tablaCabecera.='<th style="background-color:#e3e3e3">'.$catNombre.'</th>'; 
            } 

            $tablaCabecera.='<th style="background-color:#e3e3e3">Acreditacion</th>
            <th style="background-color:#e3e3e3">Dias Restantes Acreditacion</th>
            <th style="background-color:#e3e3e3">Vigencia</th>
            </tr></thead>';
            $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
            if(!empty($idFolios)){
                foreach ($idFolios as  $row) {
                    
                    $trabajadores = trabajadorSSO::where('worker_status','1')
                    ->where('sso_id',$row['id'])
                    ->get(['id AS workID',
                        'worker_name',
                        'worker_name1',
                        'worker_name2',
                        'worker_name3',
                        'worker_rut',
                        'worker_cargoid'])->toArray();

                    if(!empty($trabajadores)){
                        for($w = 0; $w < count($trabajadores) && $trabajadores != false; $w++){

                            $wrow = $trabajadores[$w];
                            $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["folio"]           = $row["id"];
                            $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["comp"]           = mb_strtoupper($row["sso_comp_name"], "UTF-8");
                            $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["comprut"]        = mb_strtoupper($row["sso_comp_rut"]."-".$row["sso_comp_dv"], "UTF-8");
                            $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["main"]           = mb_strtoupper($row["sso_mcomp_name"], "UTF-8");
                            $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["mainrut"]        = mb_strtoupper($row["sso_mcomp_rut"]."-".$row["sso_mcomp_dv"], "UTF-8");
                            $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["workername"]     = mb_strtoupper($wrow["worker_name"], "UTF-8");
                            $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["workerrut"]      = mb_strtoupper($wrow["worker_rut"], "UTF-8");
                            
                            $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["subrut"]         = "";
                            $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["subname"]        = "";
                            if((int)$row["sso_subcomp_active"]){
                                $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["subrut"]         = mb_strtoupper($row["sso_subcomp_rut"]."-".$row["sso_subcomp_dv"], "UTF-8");
                                $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["subname"]        = mb_strtoupper($row["sso_subcomp_name"], "UTF-8");
                            }

                            $documentosTrabObligatorios = DB::table('xt_ssov2_configs_cargos_cats_docs_params')
                            ->where('cfg_id', $row['sso_cfgid'])
                            ->where('cargo_id', $wrow["worker_cargoid"])
                            ->get(['cat_id','doc_cat_id','doc_id']);
                            
                            unset($_ACREDIT);
                            unset($_CATDOCDATA);
                            unset($_DOCSUPLOADES);
                            unset($dtlscatsobligxstate);
                            unset($dtlscatsobligxstate2);
     
                            foreach($documentosTrabObligatorios AS $param){
                                $_ACREDIT[$param->cat_id][$param->doc_cat_id][$param->doc_id] = 1;
                            }

                            //----------------------------------------------------------------------------------
                            // DOCUMENTOS SUBIDOS
                            //---------------------------------------------------------------------------------- 
                           
                            $documentoSubidos = EstadoDocumento::where('upld_sso_id',$row['id'])
                            ->where('upld_status', '1')
                            ->where('upld_type', '1')
                            ->where('upld_workerid', $wrow["workID"])
                          
                            ->get(['id','upld_catid','upld_docid','upld_docaprob','upld_vence_date','upld_venced','upld_aprobComen','upld_rechazado','upld_upddat'])->toArray();
                           

                           
                            foreach($documentoSubidos AS $docsuploadedrow){
                                
                                $_DOCSUPLOADES[$docsuploadedrow['upld_catid']][$docsuploadedrow['upld_docid']] = $docsuploadedrow;
                                    
                            }
                            $dateDifference = 0;
                            $years = 0;
                            $months = 0;
                            $days = 0;
                            $documentoFecha = EstadoDocumento::where('upld_sso_id',$row['id'])
                            ->where('upld_status', '1')
                            ->where('upld_type', '1')
                            ->where('upld_workerid', $wrow["workID"])
                            ->where('upld_vence_date','!=',0)
                            ->orderBy('upld_vence_date', 'ASC')
                            ->take(1)->get(['upld_vence_date'])->toArray();
                            if(isset($documentoFecha[0]['upld_vence_date'])){
                                $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["FECHAVENCE"] = date('d-m-Y', $documentoFecha[0]['upld_vence_date']);
                                $firstDate  = new \DateTime(date('Y-m-d',$fecha_actual));
                                $secondDate = new \DateTime(date('Y-m-d', $documentoFecha[0]['upld_vence_date']));
                                $intvl = $firstDate->diff($secondDate);

                               
                                if($fecha_actual > $documentoFecha[0]['upld_vence_date']){
                                    $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["DIAS"] = "-".$intvl->days;
                                }else{
                                    $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["DIAS"] = $intvl->days;
                                }
                            }else{
                                $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["FECHAVENCE"] = "";
                                $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["DIAS"] = "";

                            }

                            
                           
                            foreach(array_keys($_CAT_DOCS) AS $catid){
                                //$dtlscatsobligxstate[$catid]  = "Aprobado";
                                
                                foreach($_CAT_DOCS[$catid] AS $doc){
                               
                                    if(isset($_ACREDIT[$catid][$catid][$doc->doc_id])){
                                        if($_ACREDIT[$catid][$catid][$doc->doc_id]==1){
                                            if(isset($_DOCSUPLOADES[$catid][$doc->doc_id]["upld_docaprob"]) AND $_DOCSUPLOADES[$catid][$doc->doc_id]["upld_docaprob"] == 1 AND $_DOCSUPLOADES[$catid][$doc->doc_id]["upld_vence_date"] > $fecha_actual){
                                              
                                                $dtlscatsobligxstate[$catid][$doc->doc_id]  = 1;
                                            }else{
                                                
                                                $dtlscatsobligxstate[$catid][$doc->doc_id]  = 0;

                                            }
                                            
                                        }
                                    }
                                }
                            }
                         
                            foreach(array_keys($_CAT_DOCS) AS $catid){
                                $dtlscatsobligxstate2[$catid]  = 1;
                                  $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["CATSAPROBSTR"][$catid] = "SI";  
                                foreach($dtlscatsobligxstate[$catid] AS $doc){
                                    if($doc==0){
                                        $dtlscatsobligxstate2[$catid]  = 0;
                                        $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["CATSAPROBSTR"][$catid] = "NO";  
                                    }

                                }
                            }
                            $totalAcred = 0;
                            foreach($dtlscatsobligxstate2 AS $acred){
                                if($acred == 1){
                                    $totalAcred=$totalAcred+1; 
                                }
                            }
                            
                            if($totalAcred == $cantidadCategorias){
                                $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["ACREDITACION"]="ACREDITADO";
                            }else{
                                $_DATA["STATS"]["WORKERS"][$row["id"]][$wrow["workID"]]["ACREDITACION"]="NO ACREDITADO";
                            }   
                        }
                    }  
                }
            }
         
            $tablaCuerpo='<tbody>';
            foreach(array_keys($_DATA["STATS"]["WORKERS"]) AS $ssoid){
                $tablaCuerpo.='<tr>';
                foreach(array_keys($_DATA["STATS"]["WORKERS"][$ssoid]) AS $wrow){
                    $row = $_DATA["STATS"]["WORKERS"][$ssoid][$wrow];
                    $tablaCuerpo.='<td>'.$row['folio'].'</td>';
                    $tablaCuerpo.='<td>'.$row['comp'].'</td>';
                    $tablaCuerpo.='<td>'.$row['comprut'].'</td>';
                    $tablaCuerpo.='<td>'.$row['subname'].'</td>';
                    $tablaCuerpo.='<td>'.$row['subrut'].'</td>';
                    $tablaCuerpo.='<td>'.$row['workername'].'</td>';
                    $tablaCuerpo.='<td>'.$row['workerrut'].'</td>';

                    $acreditado="NO ACREDITADO";
                    foreach(array_keys($_DATA["STATS"]["DOC_CATS"]) AS $catid){
                        //$catdata = $_DATA["STATS"]["DOC_CATS"][$catid];
                        $xstat = $_DATA["STATS"]["WORKERS"][$ssoid][$wrow]["CATSAPROBSTR"][$catid];
                        
                        $tablaCuerpo.='<td>'.$xstat.'</td>';
                    }
                    $acreditado= $_DATA["STATS"]["WORKERS"][$ssoid][$wrow]["ACREDITACION"];
                    if($acreditado == "ACREDITADO"){
                        $estilo = 'style="background-color:#2ECC71"';

                    }else{
                        $estilo = 'style="background-color:#EC7063"';
                    }
                    if($_DATA["STATS"]["WORKERS"][$ssoid][$wrow]["DIAS"] > 0){
                        $estilo2 = 'style="background-color:#2ECC71"';

                    }else{
                        $estilo2 = 'style="background-color:#EC7063"';
                    }
                    $tablaCuerpo.='<td '.$estilo.'>'.$acreditado.'</td>';
                    $tablaCuerpo.='<td '.$estilo2.'>'.$_DATA["STATS"]["WORKERS"][$ssoid][$wrow]["DIAS"].'</td>';
                    $tablaCuerpo.='<td>'.$_DATA["STATS"]["WORKERS"][$ssoid][$wrow]["FECHAVENCE"].'</td>';
                    $tablaCuerpo.='<tr>';      
                }

            }
            $tablaCuerpo.='</tbody></table>'; 
            $lista=$tablaCabecera.$tablaCuerpo; 
            //echo $lista;
            Excel::create('Reporte Acreditacion', function($excel) use ($lista) {
                $excel->sheet('Lista General', function($sheet) use($lista) {    
                    $sheet->loadView('reporteCertificacion.excelCertificacion',compact('lista'));
                });
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
