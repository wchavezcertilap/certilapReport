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
use App\CategoriaDocSso;

use Illuminate\Http\Request;

class ReporteExcelSsoController extends Controller
{
    
     public function reporteEjecutivoSSO($idfolio)
    {
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
        $folio = FolioSso::where('id',$idfolio)
        ->get(['id','sso_mcomp_rut','sso_mcomp_name','sso_mcomp_dv','sso_comp_rut','sso_comp_name','sso_comp_dv','sso_subcomp_active','sso_subcomp_rut','sso_subcomp_dv','sso_subcomp_name','sso_cfgid','sso_project'])->toArray();

        $trabajadoresT = trabajadorSSO::where('sso_id',$idfolio)->where('worker_status',1)->get()->toArray();

        foreach ($trabajadoresT as $tb) {
            $idsTrab[] = $tb['id'];
        }
        $cantidadTrabajadores = count($trabajadoresT);

        $tablaFolio = '<table border="2">
        <thead><tr><th style="background-color:#e3e3e3" colspan="9">Datos Empresa</th></tr>
        <tr>
        <td>Folio</td>
        <td>RUT Principal</td>
        <td>Principal</td>
        <td>RUT Contratista</td>
        <td>Contratista</td>
        <td>RUT Sub Contratista</td>
        <td>Sub Contratista</td>
        <td>Proyecto</td>
        <td>N° trabajadores</td></tr>';
        $tablaFolio.='<tr><td>'.$folio[0]['id'].'</td>';
        $tablaFolio.='<td>'.$folio[0]['sso_mcomp_rut'].'-'.$folio[0]['sso_mcomp_dv'].'</td>';
        $tablaFolio.='<td>'.ucwords(mb_strtolower($folio[0]['sso_mcomp_name'],'UTF-8')).'</td>';
        $tablaFolio.='<td>'.$folio[0]['sso_comp_rut'].'-'.$folio[0]['sso_comp_dv'].'</td>';
        $tablaFolio.='<td>'.ucwords(mb_strtolower($folio[0]['sso_comp_name'],'UTF-8')).'</td>';
        if($folio[0]['sso_subcomp_active']==1){
            $tablaFolio.='<td>'.$folio[0]['sso_subcomp_rut'].'-'.$folio[0]['sso_subcomp_dv'].'</td>';
            $tablaFolio.='<td>'.ucwords(mb_strtolower($folio[0]['sso_subcomp_name'],'UTF-8')).'</td>';
        }else{
            $tablaFolio.='<td></td>';
            $tablaFolio.='<td></td>';
        }
        $tablaFolio.='<td>'.ucwords(mb_strtolower($folio[0]['sso_project'],'UTF-8')).'</td>';
        $tablaFolio.='<td>'.$cantidadTrabajadores.'</td></tr></table>';
        


        $documentosGlobalesObligatoriosF = DB::table('xt_ssov2_configs_glbdocs')
        ->join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_configs_glbdocs.glb_docid')
        ->where(['xt_ssov2_configs_glbdocs.cfg_id' => $folio[0]['sso_cfgid']])
        ->where(['xt_ssov2_doctypes.doc_status' => 1 ])
        ->where(['xt_ssov2_doctypes.doc_type' => 0])
        ->where(['xt_ssov2_configs_glbdocs.glb_obligact' => 1])
        ->orderBy('xt_ssov2_configs_glbdocs.glb_obligact', 'DESC')
        ->get(['xt_ssov2_doctypes.id','xt_ssov2_doctypes.doc_name','xt_ssov2_configs_glbdocs.glb_obligact'])->toArray();



        if(!empty($documentosGlobalesObligatoriosF)){
            $cantidadDocGlobales = count($documentosGlobalesObligatoriosF);
            unset($idDoGlobales);

            foreach ($documentosGlobalesObligatoriosF as  $docGlo) {

                    $idDoGlobales[] = $docGlo->id;
            }
        }

        $documentosTrabajadoresObligatoriosCargo=trabajadorSSO::distinct('worker_cargoid')
        ->where('sso_id',$folio[0]['id'])
        ->get(['worker_cargoid'])->toArray();

        if(!empty($documentosTrabajadoresObligatoriosCargo)){

            foreach ($documentosTrabajadoresObligatoriosCargo as  $docTra) {

                $idDocTrab[] = $docTra['worker_cargoid'];
            }

            if(!empty($idDocTrab)){


                $categoriaDocumentoTrabajador=DB::table('xt_ssov2_configs_cargos_cats_docs_params')
                    ->join('xt_ssov2_doccats', function ($join) {
                        $join->on('xt_ssov2_doccats.id', '=', 'xt_ssov2_configs_cargos_cats_docs_params.doc_cat_id')
                        ->where('xt_ssov2_doccats.cat_status', '=', 1);
                    })
                    ->join('xt_ssov2_doctypes', function ($join) {
                        $join->on('xt_ssov2_doctypes.id', '=', 'xt_ssov2_configs_cargos_cats_docs_params.doc_id')
                        ->where('xt_ssov2_doctypes.doc_status', '=', 1);
                    })
                ->whereIn('xt_ssov2_configs_cargos_cats_docs_params.cargo_id',$idDocTrab)    
                ->where('xt_ssov2_configs_cargos_cats_docs_params.cfg_id', '=' ,$folio[0]['sso_cfgid']) 
                ->distinct('xt_ssov2_configs_cargos_cats_docs_params.doc_cat_id') 
                ->orderBy('xt_ssov2_doccats.id', 'ASC') 
                ->orderBy('xt_ssov2_doctypes.id', 'ASC') 
                ->get(['xt_ssov2_doccats.id as catid','xt_ssov2_doccats.cat_name','xt_ssov2_doctypes.id','xt_ssov2_doctypes.doc_name']);


                
                $categoriaDocumentoTrabajador = collect($categoriaDocumentoTrabajador)->map(function($x){ return (array) $x; })->toArray(); 
                $categoriasHabiles =super_unique($categoriaDocumentoTrabajador,'catid');
                $docHabiles =super_unique($categoriaDocumentoTrabajador,'id');

                foreach ($categoriasHabiles as $cat) {

                    $categoriaAc[]= $cat['catid'];
                   
                }
                $cantidadCategoria = count($categoriaAc);

                foreach ($docHabiles as $doc) {

                    $documentoAc[]= $doc['id'];
                   
                }

                
                foreach ($idsTrab as $idt) {
                    foreach ($documentoAc as $docID) {
                
                        $documentosTrabajadores=DB::table('xt_ssov2_header_worker')
                        ->join('xt_ssov2_header_uploads', function ($join) use($categoriaAc,$docID) {
                            $join->on('xt_ssov2_header_uploads.upld_workerid', '=', 'xt_ssov2_header_worker.id')
                            ->whereIn('xt_ssov2_header_uploads.upld_catid', $categoriaAc)
                            ->where('xt_ssov2_header_uploads.upld_docid', $docID)
                            ->where('xt_ssov2_header_uploads.upld_status', '=' ,1) ;
                        })
                        ->where('xt_ssov2_header_worker.id', '=' ,$idt)
                        ->orderBy('xt_ssov2_header_uploads.upld_catid', 'ASC') 
                        ->orderBy('xt_ssov2_header_uploads.upld_docid', 'ASC') 
                        ->get(['xt_ssov2_header_worker.id',
                               'xt_ssov2_header_worker.worker_name',
                               'xt_ssov2_header_worker.worker_name1',
                               'xt_ssov2_header_worker.worker_name2',
                               'xt_ssov2_header_worker.worker_name3',
                               'xt_ssov2_header_worker.worker_rut',
                               'xt_ssov2_header_worker.worker_cargoid',
                               'xt_ssov2_header_worker.worker_syscargoname',
                               'xt_ssov2_header_uploads.upld_catid',
                               'xt_ssov2_header_uploads.upld_docid',
                               'xt_ssov2_header_uploads.upld_vence_date',
                               'xt_ssov2_header_uploads.upld_upddat',
                               'xt_ssov2_header_uploads.upld_docaprob',
                               'xt_ssov2_header_uploads.upld_rechazado',
                               'xt_ssov2_header_uploads.upld_venced',
                               'xt_ssov2_header_uploads.upld_aprobComen',
                               'xt_ssov2_header_uploads.upld_comments',
                               'xt_ssov2_header_uploads.upld_docaprob_uid']);

                        
                        if(!empty($documentosTrabajadores[0])) {
                            
                            $trabajadorDoc['idTrabajador'] = $documentosTrabajadores[0]->id;
                            $trabajadorDoc['nombreCompleto'] = $documentosTrabajadores[0]->worker_name;
                            $trabajadorDoc['nombre1'] = $documentosTrabajadores[0]->worker_name1;
                            $trabajadorDoc['nombre2'] = $documentosTrabajadores[0]->worker_name2;
                            $trabajadorDoc['nombre3'] = $documentosTrabajadores[0]->worker_name3;
                            $trabajadorDoc['rut'] = $documentosTrabajadores[0]->worker_rut;
                            $trabajadorDoc['cargoidTra'] = $documentosTrabajadores[0]->worker_cargoid;
                            $trabajadorDoc['cargoTexto'] = $documentosTrabajadores[0]->worker_syscargoname;
                            $trabajadorDoc['doc_id'] = $documentosTrabajadores[0]->upld_docid;
                            $trabajadorDoc['upld_catid'] = $documentosTrabajadores[0]->upld_catid;
                            $trabajadorDoc['upld_vence_date'] = $documentosTrabajadores[0]->upld_vence_date;
                            $trabajadorDoc['upld_upddat'] = $documentosTrabajadores[0]->upld_upddat;
                            $trabajadorDoc['upld_docaprob'] = $documentosTrabajadores[0]->upld_docaprob;
                            $trabajadorDoc['upld_rechazado'] = $documentosTrabajadores[0]->upld_rechazado;
                            $trabajadorDoc['upld_venced'] = $documentosTrabajadores[0]->upld_venced;
                            $trabajadorDoc['upld_aprobComen'] = $documentosTrabajadores[0]->upld_aprobComen;
                            $trabajadorDoc['upld_comments'] = $documentosTrabajadores[0]->upld_comments;
                            $trabajadorDoc['upld_docaprob_uid'] = $documentosTrabajadores[0]->upld_docaprob_uid;


                            
                            
                        }else{
                            $trabajadores = trabajadorSSO::where('id',$idt)->where('worker_status',1)->get()->toArray();
                           
                            $trabajadorDoc['idTrabajador'] = $trabajadores[0]['id'];
                            $trabajadorDoc['nombreCompleto'] = $trabajadores[0]['worker_name'];
                            $trabajadorDoc['nombre1'] = $trabajadores[0]['worker_name'];
                            $trabajadorDoc['nombre2'] = $trabajadores[0]['worker_name'];
                            $trabajadorDoc['nombre3'] = $trabajadores[0]['worker_name'];
                            $trabajadorDoc['rut'] = $trabajadores[0]['worker_rut'];
                            $trabajadorDoc['cargoidTra'] = $trabajadores[0]['worker_cargoid'];
                            $trabajadorDoc['cargoTexto'] = $trabajadores[0]['worker_syscargoname'];
                            $trabajadorDoc['doc_id'] = $docID;
                            $trabajadorDoc['upld_catid'] = "";
                            $trabajadorDoc['upld_vence_date'] = "";
                            $trabajadorDoc['upld_upddat'] = "";
                            $trabajadorDoc['upld_docaprob'] = "";
                            $trabajadorDoc['upld_rechazado'] = "";
                            $trabajadorDoc['upld_venced'] = "";
                            $trabajadorDoc['upld_aprobComen'] = "";
                            $trabajadorDoc['upld_comments'] = "";
                            $trabajadorDoc['upld_docaprob_uid'] = "";
                        }    
                        $datosTraDoc[] = $trabajadorDoc;
                    }
            
                        

                }
                
                $tabla='<table border="2"><thead><tr><th style="background-color:#e3e3e3" colspan="4">Datos Trabajador</th>';
                foreach ($categoriasHabiles as  $cat) {
                    $cantidadDoc=0;
                    $nombreCtegoria ="";
                    foreach ($categoriaDocumentoTrabajador as $catDocTra) {
                     
                        if($cat['catid']==$catDocTra['catid']){
                            $cantidadDoc = $cantidadDoc + 1;  
                            $nombreCtegoria = ucwords(mb_strtolower($catDocTra['cat_name'],'UTF-8'));
                            
                        }
                    }
                    $tabla.='<th style="background-color:#e3e3e3" colspan='.$cantidadDoc.'>'.$nombreCtegoria.'</th>';
                }

                $tabla.='</thead></tr>';
                //// fila documentos ///
                $tabla.='<tr>
                             <td style="background-color:#FB3838">N°</td>
                             <td style="background-color:#FB3838">Trabajador</td>
                             <td style="background-color:#FB3838">RUT</td>
                             <td style="background-color:#FB3838">Cargo</td>';
                foreach ($categoriasHabiles as  $cat) {
                       $nombreDoc="";
                       $nombreCtegoria ="";
                    foreach ($categoriaDocumentoTrabajador as $catDocTra) {
                        
                        if($cat['catid']==$catDocTra['catid']){
                            $nombreCtegoria = ucwords(mb_strtolower($catDocTra['cat_name'],'UTF-8'));
                            $nombreDoc = ucwords(mb_strtolower($catDocTra['doc_name'],'UTF-8'));
                            $tabla.='<td style="background-color:#FB3838">'.$nombreDoc.'</td>';
                        }
                       
                    } 
                
                }
                $tabla.='</tr>';
                
                /// TRABAJADORES CON DOC //
                $cuenta = 0;
                $nombreT ="";$RUTT="";$cargoT="";
                foreach ($trabajadoresT as $trabajador) {
                        $cuenta = $cuenta +1;
                        $nombreT = ucwords(mb_strtolower($trabajador['worker_name'],'UTF-8'));
                        $RUTT = ucwords(mb_strtolower($trabajador['worker_rut'],'UTF-8'));
                        $cargoT = ucwords(mb_strtolower($trabajador['worker_syscargoname'],'UTF-8'));
                        $tabla.='<tr>
                                <td>'.$cuenta.'</td>
                                <td>'.$nombreT.'</td>
                                <td>'.$RUTT.'</td>
                                <td>'.$cargoT.'</td>';
                   $estadoDocumento="";
                    foreach ($datosTraDoc as $documentosTrabajadores) {
                       
                        if($documentosTrabajadores['idTrabajador']==$trabajador['id']){
                            $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
                            $fecha2 = $documentosTrabajadores['upld_vence_date'];
                            $fechaUpdate = $documentosTrabajadores['upld_upddat'];

                           
                            if($documentosTrabajadores['upld_catid']!=""){
                                if($documentosTrabajadores['upld_docaprob'] == 0 and $documentosTrabajadores['upld_docaprob_uid'] == 0 and $documentosTrabajadores['upld_rechazado']== 0 and $documentosTrabajadores['upld_venced']== 0 and $documentosTrabajadores['upld_aprobComen'] == 0){
                                                   
                                 $estadoDocumento ="PorRevision";
                                }
                                elseif(($documentosTrabajadores['upld_docaprob'] == 1 or $fecha_actual <= $fechaUpdate) and $fecha_actual < $fecha2 AND $documentosTrabajadores['upld_aprobComen']== 0 and $documentosTrabajadores['upld_rechazado'] == 0){
                                    
                                        $estadoDocumento ="Aprobado";
                                
                                }elseif (($documentosTrabajadores['upld_venced']== 1  or $fecha_actual > $fecha2) and $documentosTrabajadores['upld_rechazado'] == 0 and $fecha2!= 0){
                                        $estadoDocumento ="Vencido";
                                }elseif ($documentosTrabajadores['upld_aprobComen'] == 1 and $documentosTrabajadores['upld_comments']!="" and $documentosTrabajadores['upld_rechazado'] == 0){
                                        
                                        $estadoDocumento ="AprobadoObs";
                                }elseif ($documentosTrabajadores['upld_rechazado'] == 1) {
                                        
                                        $estadoDocumento ="Rechazado";
                                }

                                $tabla.='<td>'.$estadoDocumento.'</td>';
                               
                            }else{
                                $tabla.='<td>Sin Documento</td>';
                            } 
                        }              
                        
                       
                    } 
                 $tabla.='</tr>' ;    
                }
               $tabla.='</table>'; 
              
                ///grafifica por trabajdor certificado y acreditacion////
                foreach ($categoriasHabiles as  $cat) {
                    $cantidadDocCat = 0;
                    $nombreCtegoria ="";
                    foreach ($categoriaDocumentoTrabajador as $categoriaDoc) {
                        
                        if($cat['catid']==$categoriaDoc['catid']){

                            $nombreCtegoria = ucwords(mb_strtolower($categoriaDoc['cat_name'],'UTF-8'));
                            $cantidadDocCat = $cantidadDocCat +1;
                            $arregloCatDoc['nombreCat']=$nombreCtegoria;
                            $arregloCatDoc['id']=$cat['catid'];
                            $arregloCatDoc['cantidadDoc']=$cantidadDocCat;  
                        }

                               
                    }
                    $arregloCategoria[]=$arregloCatDoc;
                }

                

                foreach ($idsTrab as $idt) {
                    foreach ($arregloCategoria as $idc) {
                        $documentosTrabajadores=DB::table('xt_ssov2_header_worker')
                        ->join('xt_ssov2_header_uploads', function ($join) use($idc) {
                            $join->on('xt_ssov2_header_uploads.upld_workerid', '=', 'xt_ssov2_header_worker.id')
                            ->where('xt_ssov2_header_uploads.upld_catid','=', $idc['id'])
                            ->where('xt_ssov2_header_uploads.upld_status', '=' ,1) ;
                        })
                        ->where('xt_ssov2_header_worker.id', '=' ,$idt)
                        ->orderBy('xt_ssov2_header_uploads.upld_catid', 'ASC') 
                        ->orderBy('xt_ssov2_header_uploads.upld_docid', 'ASC') 
                        ->get(['xt_ssov2_header_worker.id',
                               'xt_ssov2_header_worker.worker_name',
                               'xt_ssov2_header_worker.worker_name1',
                               'xt_ssov2_header_worker.worker_name2',
                               'xt_ssov2_header_worker.worker_name3',
                               'xt_ssov2_header_worker.worker_rut',
                               'xt_ssov2_header_worker.worker_cargoid',
                               'xt_ssov2_header_worker.worker_syscargoname',
                               'xt_ssov2_header_uploads.upld_catid',
                               'xt_ssov2_header_uploads.upld_docid',
                               'xt_ssov2_header_uploads.upld_vence_date',
                               'xt_ssov2_header_uploads.upld_upddat',
                               'xt_ssov2_header_uploads.upld_docaprob',
                               'xt_ssov2_header_uploads.upld_rechazado',
                               'xt_ssov2_header_uploads.upld_venced',
                               'xt_ssov2_header_uploads.upld_aprobComen',
                               'xt_ssov2_header_uploads.upld_comments',
                               'xt_ssov2_header_uploads.upld_docaprob_uid']);
                        $docApro =0;
                        $docAproOb =0;
                        $totalAporObs = 0;

                      
                     
                        if(!empty($documentosTrabajadores[0])){

                            foreach ($documentosTrabajadores as $docTrabajador) {
                       
                                $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
                                $fecha2 = $docTrabajador->upld_vence_date;
                                $fechaUpdate = $docTrabajador->upld_upddat;

                                if(($docTrabajador->upld_docaprob == 1 or $fecha_actual <= $fechaUpdate) and $fecha_actual < $fecha2 AND $docTrabajador->upld_aprobComen== 0 and $docTrabajador->upld_rechazado == 0){
                                        
                                    $docApro = $docApro +1;                           
                                }
                                if($docTrabajador->upld_aprobComen == 1 and $docTrabajador->upld_comments!="" and $docTrabajador->upld_rechazado == 0){
                                    $docAproOb = $docAproOb + 1;            
                                            
                                }     
                                $totalAporObs = $docApro + $docAproOb;   
                                $porcentajeApro = (($totalAporObs/$idc['cantidadDoc'])*100);
                                $datoG['id']=$docTrabajador->id;
                                $datoG['worker_name']=$docTrabajador->worker_name;
                                $datoG['worker_name1']=$docTrabajador->worker_name1;
                                $datoG['worker_name2']=$docTrabajador->worker_name2;
                                $datoG['worker_name3']=$docTrabajador->worker_name3;
                                $datoG['worker_rut']=$docTrabajador->worker_rut;
                                $datoG['worker_cargoid']=$docTrabajador->worker_cargoid;
                                $datoG['worker_syscargoname']=$docTrabajador->worker_syscargoname;
                                $datoG['categoria']=$idc['nombreCat'];
                                $datoG['pabrocion']=round($porcentajeApro);    
                            }
                           
                        }else{
                                $trabajadores = trabajadorSSO::where('id',$idt)->where('worker_status',1)->get()->toArray();
                           
                                $datoG['id']=$trabajadores[0]['id'];
                                $datoG['worker_name']=$trabajadores[0]['worker_name'];
                                $datoG['worker_name1']=$trabajadores[0]['worker_name1'];
                                $datoG['worker_name2']=$trabajadores[0]['worker_name2'];
                                $datoG['worker_name3']=$trabajadores[0]['worker_name3'];
                                $datoG['worker_rut']=$trabajadores[0]['worker_rut'];
                                $datoG['worker_cargoid']=$trabajadores[0]['worker_cargoid'];
                                $datoG['worker_syscargoname']=$trabajadores[0]['worker_syscargoname'];
                                $datoG['categoria']=$idc['nombreCat'];
                                $datoG['pabrocion']=0;

                        }
                        $graficaPorTrabajador[] = $datoG;
                    }

                        
                }


                Excel::create('Reporte SSO Ejecutivo', function($excel) use($tablaFolio,$tabla,$graficaPorTrabajador,$cantidadTrabajadores,$trabajadoresT,$categoriasHabiles,$cantidadCategoria) {
                        
                       $excel->sheet('Datos Empresa', function($sheet) use($tablaFolio) {  
                            $sheet->loadView('excel.datosFolio',compact('tablaFolio'));
                        });

                        $excel->sheet('Detalle Certificacion', function($sheet) use($tabla) {  
                            $sheet->loadView('excel.ssoReporteEjecutivo',compact('tabla'));
                        });

                        $excel->sheet('Graficas', function($sheet) use($graficaPorTrabajador,$trabajadoresT,$cantidadCategoria) {    
                           
                            $fila = 1;
                            $nombreHoja = "Graficas";
                            $totalTrabajadores = count($trabajadoresT);
                            foreach ($trabajadoresT as $id) {

                                $data['Nombre']=$id['worker_name'];
                               // $data['nombre1']=$id['worker_name1'];
                               // $data['nombre2']=$id['worker_name2'];
                               // $data['nombre3']=$id['worker_name3'];
                                $data['RUT']=$id['worker_rut'];
                                $data['Cargo']=$id['worker_syscargoname'];
                                //$data['idcargo']=$id['worker_cargoid'];
                                unset($pdataAp);
                                foreach ($graficaPorTrabajador as $value) {
                                   if($id['id']==$value['id']){

                                        $dataAp[$value['categoria']]=$value['pabrocion'];                                    }
                                  
                                }
                                $dataGap[] =array_merge($data, $dataAp); 
                               
                                
                            }
                          
                            
                            $sheet->fromArray($dataGap, null, 'A'.$fila, false, true);
                            $valorColDibujo= 3;
                            $letrafilaC= 0;
                            $letrafilaCP = 0;
                            $valorColDibujoC = 0;
                            $letraAnun = 0;
                            $valorColDibujo2 = 0;
                            $fila1 = 0;
                            $NOMBRET = "";
                        foreach($dataGap as $data){
                            $fila1 = $fila1 +1;
                            $NOMBRET = $data['Nombre'];
                            $fila=$fila+1;
                            $valColumna= 2+$cantidadCategoria;


                            $col = \PHPExcel_Cell::stringFromColumnIndex($valColumna);
                            $sheet->getStyle('A1:'.$col.'1')->applyFromArray(
                                    array(
                                        'font' => array(
                                            'bold' => true
                                        ),
                                        'alignment' => array(
                                            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                        ),
                                        'borders' => array(
                                            'top' => array(
                                                'style' => \PHPExcel_Style_Border::BORDER_THIN
                                            )
                                        ),
                                        'fill' => array(
                                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                            'color' => array('rgb' => 'e3e3e3')
                                        )
                                    )
                                ); 

                            //echo $fila;
                            $labels1 = [
                                new  \PHPExcel_Chart_DataSeriesValues('String', $nombreHoja. '!$A$1', null, 1), // 2011
                            ];

                            $categories1 = [
                                new \PHPExcel_Chart_DataSeriesValues('String', $nombreHoja. '!$D$1:$'.$col.'$1', null, $cantidadCategoria), // Q1 to Q4
                            ];

                            $values1 = [
                                 new \PHPExcel_Chart_DataSeriesValues('Number', $nombreHoja. '!$D$'.$fila.':$'.$col.'$'.$fila, null, $cantidadCategoria),
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
                           // $layout1->setShowPercent(TRUE);

                            //  Set the series in the plot area
                            $plotarea1 = new \PHPExcel_Chart_PlotArea($layout1, array($series));
                            //  Set the chart legend
                            $legend1 = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                           
                            $title1 = new \PHPExcel_Chart_Title('% Acreditacion:'.$NOMBRET);
                            //$title1->getFont()->setSize(10);


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

                            if($fila1 == 1){
                                $letraAnun = $totalTrabajadores + 5;
                                $valorBorde = 10;
                                
                            }
                            
                            if($fila1 >= 2){
                                $letraAnun = $valorBorde + 2;
                                $valorColDibujo2 =$valorColDibujo2;
                               
                            }
                            $letrafilaCP= $letrafilaC;
                            
                            $letrafila = \PHPExcel_Cell::stringFromColumnIndex($letrafilaCP);
                            $valorColDibujoC = $valorColDibujo + $valorColDibujo2;
                            $letraborder = \PHPExcel_Cell::stringFromColumnIndex($valorColDibujoC);
                            $valorBorde = $letraAnun+10;
                            //  Set the position where the chart should appear in the worksheet
                            $chart1->setTopLeftPosition($letrafila.$letraAnun);
                            $chart1->setBottomRightPosition($letraborder.$valorBorde);
                            $sheet->addChart($chart1);
                           
                            
                        }

                    });       
                        //exit();             
                })->export('xlsx');

            }

        }

      



        
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
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
