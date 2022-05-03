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
use App\CargoCateDoc;
use App\SsoPeriodo;
use Illuminate\Http\Request;

class ReporteAquaAcreditado extends Controller
{

    public function porContratistaAquaSSO($id){

        if($id != 1){
            $rutprincipal = explode(",", $id);
            return FolioSso::distinct()->whereIn('sso_mcomp_rut',$rutprincipal)->orderBy('sso_comp_name', 'ASC')->get(['sso_comp_name','sso_comp_rut']);
        }else{
            $idUsuario = session('user_id');
            $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
            $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
            $UsuarioPrincipal->load('usuarioDatos');   
            foreach ($UsuarioPrincipal as $rut) {
                $rutprincipal[]=$rut['mainCompanyRut'];
            }
            if($datosUsuarios->type ==3){

                return FolioSso::distinct()->whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status',1)->orderBy('sso_comp_name', 'ASC')->get(['sso_comp_name','sso_comp_rut']);
            }
            if($datosUsuarios->type ==2 || $datosUsuarios->type ==1 ){

                return FolioSso::distinct()->where('sso_status',1)->orderBy('sso_comp_name', 'ASC')->get(['sso_comp_name','sso_comp_rut']);
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

            if($datosUsuarios->type ==3){

                $EmpresasP = FolioSso::distinct()->whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['sso_mcomp_name','sso_mcomp_rut']);

                return view('reportesAquaChile.reporteAcreditacionTraSso',compact('datosUsuarios','EmpresasP','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo')); 

            }
            if($datosUsuarios->type ==2 || $datosUsuarios->type ==1 ){

                $EmpresasP = FolioSso::distinct()->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['sso_mcomp_name','sso_mcomp_rut']);

                return view('reportesAquaChile.reporteAcreditacionTraSso',compact('datosUsuarios','EmpresasP','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo')); 

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

            if($datosUsuarios->type == 3){

                $EmpresasP = FolioSso::distinct()->whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['sso_mcomp_name','sso_mcomp_rut']);

            }
            if($datosUsuarios->type == 2 or $datosUsuarios->type ==1){

              $EmpresasP = FolioSso::distinct()->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['sso_mcomp_name','sso_mcomp_rut']);

            }
        $input=$request->all();
        $estado= 0;
        $empresaPrincipal = $input["empresaPrincipal"];
        if(!empty($input["empresaContratista"])){
            $empresaContratista = $input["empresaContratista"];
        }


        foreach ($empresaPrincipal as $value) {

            $rutprincipalR[] = $value;
        }
        $cantidadPrin = count($rutprincipalR);

        if(!empty($empresaContratista)){

            foreach ($empresaContratista as $value2) {

                $rutcontratistasR[] = $value2;
            }
            $cantidadCon = count($rutcontratistasR);

            if($cantidadCon > 0){

                $idFolios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipalR)->whereIn('sso_comp_rut',$rutcontratistasR)->
                where('sso_status',1)->orderBy('id', 'ASC')->get(['id','sso_mcomp_rut','sso_mcomp_name','sso_mcomp_dv','sso_comp_rut','sso_comp_name','sso_comp_dv','sso_subcomp_active','sso_subcomp_rut','sso_subcomp_dv','sso_subcomp_name','sso_cfgid'])->toArray();

                $totalDoc = 0;
                $totalAcreditados = 0;
                $totalNoAcreditados = 0;
             
                foreach ($idFolios as $id) {

                    $idDocumentos = CargoCateDoc::where('cfg_id',$id['sso_cfgid'])->where('cargo_id',1)->get(['cat_id','doc_id'])->toArray();
                    $totalDoc = count($idDocumentos);

             
                    $trabajadores = trabajadorSSO::where('worker_status','1')->where('sso_id',$id['id'])->get(['id','worker_name','worker_name1','worker_name2','worker_name3','worker_rut','sso_id'])->toArray();


                    foreach ($trabajadores as $trabajador) {

                        

                        $documentos = EstadoDocumento::where('upld_sso_id', $trabajador['sso_id'])->where('upld_workerid',$trabajador['id'])->where('upld_status',1)->where('upld_type',1)->
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

                        $trabajadores["folio"] = $id['id'];
                        $trabajadores["rutPrincipal"] = $id['sso_mcomp_rut']."-".$id['sso_mcomp_dv'];
                        $trabajadores["nombrePrincipal"] =  ucwords(mb_strtolower($id['sso_mcomp_name'],'UTF-8'));
                        $trabajadores["rutContratista"] = $id['sso_comp_rut']."-".$id['sso_comp_dv'];
                        $trabajadores["nombreContratista"] =  ucwords(mb_strtolower($id['sso_comp_name'],'UTF-8'));
                        if($id['sso_subcomp_active']== 1){

                        $trabajadores["rutSubContra"] = $id['sso_subcomp_rut']."-".$id['sso_subcomp_dv'];
                        $trabajadores["nombreSubContra"] =  ucwords(mb_strtolower($id['sso_subcomp_name'],'UTF-8'));

                        }else{
                        $trabajadores["rutSubContra"] = "";
                        $trabajadores["nombreSubContra"] = "";   
                        }
                        if($trabajador['worker_name1']!=""){
                            $trabajadores["nombreTrabajador"] =  ucwords(mb_strtolower($trabajador['worker_name1'],'UTF-8'));
                            $trabajadores["apellidoTrabajador"] =  ucwords(mb_strtolower($trabajador['worker_name2']." ".$trabajador['worker_name3'],'UTF-8'));
                        }else{
                            $trabajadores["nombreTrabajador"] =  ucwords(mb_strtolower($trabajador['worker_name'],'UTF-8'));
                            $trabajadores["apellidoTrabajador"] =  "";;    

                        }
                        $trabajadores["rutTrabajador"] = $trabajador['worker_rut'];
                        $trabajadores["porcentajeTrabajador"] =  number_format($porcentajeApro, 2, '.', '');
                        if($id['sso_mcomp_rut']==92805000){
                            $historialSso = SsoPeriodo::where('activo',1)->where('sso_id',$id['id'])->orderby('id','DESC')->get()->toArray();
                            if(!empty($historialSso[0]['sso_fecha_vence'])){
                                $estado = 1;
                                 if($historialSso[0]['sso_fecha_vence']<=$fecha_actual){
                                    $trabajadores["estado"]="Vencido";
                                 }else{
                                    $trabajadores["estado"]="Activo";
                                 }
                            }else{
                            $trabajadores["estado"]="Activo";
                            }
                        }              

                        $WORK[] = $trabajadores;
                        $totalAcreditados = $totalAcreditados + $cantidadcien; 
                        $totalNoAcreditados = $totalNoAcreditados + $noAcreditado; 

                    }// for trabjaador    
                }
            }
        }else{
            
            $idFolios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipalR)->where('sso_status',1)->orderBy('id', 'ASC')->get(['id','sso_mcomp_rut','sso_mcomp_name','sso_mcomp_dv','sso_comp_rut','sso_comp_name','sso_comp_dv','sso_subcomp_active','sso_subcomp_rut','sso_subcomp_dv','sso_subcomp_name','sso_cfgid'])->toArray();

  
            $totalDoc = 0;
            $totalAcreditados = 0;
            $totalNoAcreditados = 0;
          
            foreach ($idFolios as $id) {

               
         
                $trabajadores = trabajadorSSO::where('worker_status','1')->where('sso_id',$id['id'])->get(['id','worker_name','worker_name1','worker_name2','worker_name3','worker_rut','sso_id','worker_cargoid'])->toArray();

               
                foreach ($trabajadores as $trabajador) {

                $idDocumentos = CargoCateDoc::where('cfg_id',$id['sso_cfgid'])->where('cargo_id',$trabajador['worker_cargoid'])->get(['cat_id','doc_id'])->toArray();
                if(!empty($idDocumentos)){
                    $totalDoc = count($idDocumentos);
                }

                 $documentos = EstadoDocumento::where('upld_sso_id', $trabajador['sso_id'])->where('upld_workerid',$trabajador['id'])->where('upld_status',1)->where('upld_type',1)->
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
                    
                    
                    foreach ($documentos as  $doc) {

                        $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
                        // $fecha_actual;
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
                

                    $trabajadores["folio"] = $id['id'];
                    $trabajadores["rutPrincipal"] = $id['sso_mcomp_rut']."-".$id['sso_mcomp_dv'];
                    $trabajadores["nombrePrincipal"] =  ucwords(mb_strtolower($id['sso_mcomp_name'],'UTF-8'));
                    $trabajadores["rutContratista"] = $id['sso_comp_rut']."-".$id['sso_comp_dv'];
                    $trabajadores["nombreContratista"] =  ucwords(mb_strtolower($id['sso_comp_name'],'UTF-8'));
                    if($id['sso_subcomp_active']== 1){

                    $trabajadores["rutSubContra"] = $id['sso_subcomp_rut']."-".$id['sso_subcomp_dv'];
                    $trabajadores["nombreSubContra"] =  ucwords(mb_strtolower($id['sso_subcomp_name'],'UTF-8'));

                    }else{
                    $trabajadores["rutSubContra"] = "";
                    $trabajadores["nombreSubContra"] = "";   
                    }
                    if($trabajador['worker_name1']!=""){
                        $trabajadores["nombreTrabajador"] =  ucwords(mb_strtolower($trabajador['worker_name1'],'UTF-8'));
                        $trabajadores["apellidoTrabajador"] =  ucwords(mb_strtolower($trabajador['worker_name2']." ".$trabajador['worker_name3'],'UTF-8'));
                    }else{
                        $trabajadores["nombreTrabajador"] =  ucwords(mb_strtolower($trabajador['worker_name'],'UTF-8'));
                        $trabajadores["apellidoTrabajador"] =  "";;    

                    }
                    $trabajadores["rutTrabajador"] = $trabajador['worker_rut'];
                    $trabajadores["porcentajeTrabajador"] =  number_format($porcentajeApro, 2, '.', '');
                    if($id['sso_mcomp_rut']==92805000){
                        $historialSso = SsoPeriodo::where('activo',1)->where('sso_id',$id['id'])->orderby('id','DESC')->get()->toArray();
                        if(!empty($historialSso[0]['sso_fecha_vence'])){
                            $estado = 1;
                             if($historialSso[0]['sso_fecha_vence']<=$fecha_actual){
                                $trabajadores["estado"]="Vencido";
                             }else{
                                $trabajadores["estado"]="Activo";
                             }
                        }else{
                            $trabajadores["estado"]="Activo";
                        }
                    }       

                    $WORK[] = $trabajadores;
                    $totalAcreditados = $totalAcreditados + $cantidadcien; 
                    $totalNoAcreditados = $totalNoAcreditados + $noAcreditado; 
                   
                }// for trabjaador    

            } // folios
        }



        if(!empty($WORK)){
            $totalTrabajadores = count($WORK);
        }
    

        return view('reportesAquaChile.reporteAcreditacionTraSso',compact('WORK','datosUsuarios','EmpresasP','aquaChile','totalTrabajadores','totalAcreditados','totalNoAcreditados','certificacion','usuarioAqua','usuarioABBChile','estado','usuarioNOKactivo')); 


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
