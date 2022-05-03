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
use App\TrabajadorVerificacion;
use App\AccesoPersona;
use Illuminate\Http\Request;

class ReporteAquaIntegradoController  extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function porContratistaAqua($id){

        $rutprincipal = explode(",", $id);
        
        return Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipal)->orderBy('name', 'ASC')->get(['name','rut']);
        
    }

    public function porFolio($id){
        
        return FolioSso::distinct()->where('sso_mcomp_rut','=',$id)->orderBy('id', 'ASC')->get(['id']);
    }


    public function porProyecto($id){
        
        return FolioSso::distinct()->where('sso_mcomp_rut','=',$id)->whereNotNull('sso_project')->orderBy('sso_project', 'ASC')->get(['sso_project']);
    }


    public function index(Request $request)
    {

        $idUsuario = session('user_id');
        if($idUsuario ==  ""){
            return view('sesion.index');
        }
        $aquaChile = session('user_aqua');
        $usuarioABBChile= session('user_ABB');
        $usuarioClaroChile= session('user_Claro');
        $usuarioNOKactivo = session('usuario_nok');
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
        $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
        $UsuarioPrincipal->load('usuarioDatos');


        foreach ($UsuarioPrincipal as $rut) {

            $rutprincipal[]=$rut['mainCompanyRut'];
            
        }

            if($datosUsuarios->type ==2 or $datosUsuarios->type ==3){

                $EmpresasP = empresaPrincipal::distinct()->whereIn('rut',$rutprincipal)->orderBy('name', 'ASC')->get(['name','rut']);

                $periodos = Periodo::orderBy('id', 'DES')->get();
                $periodos->load('mes');

                return view('reportesAquaChile.reporteIntegradoCaVSso',compact('datosUsuarios','EmpresasP','periodos','aquaChile','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile')); 

            }
           
        //return view('reporteAquaChile.reporteIntegradoCaVSso',compact('datosUsuarios','EmpresasP','periodos','aquaChile')); 
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
        
        DB::connection()->enableQueryLog();
        $queries = DB::getQueryLog();
        $idUsuario = session('user_id');
        if($idUsuario ==  ""){
            return view('sesion.index');
        }
        $aquaChile = session('user_aqua');
        $usuarioABBChile= session('user_ABB');
        $usuarioClaroChile= session('user_Claro');
        $usuarioNOKactivo = session('usuario_nok');
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
        $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
        $UsuarioPrincipal->load('usuarioDatos');


        foreach ($UsuarioPrincipal as $rut) {

            $rutprincipal[]=$rut['mainCompanyRut']; 
        }

        if($datosUsuarios->type ==2 or $datosUsuarios->type ==3){

            $EmpresasP = empresaPrincipal::distinct()->whereIn('rut',$rutprincipal)->orderBy('name', 'ASC')->get(['name','rut']);

            $periodos = Periodo::orderBy('id', 'DES')->get();
            $periodos->load('mes');
        }

        $input=$request->all();
        print_r($input);
        $empresaPrincipal = $input["empresaPrincipal"];
        $periodo = $input["periodo"];
        $estadoCertificacion = $input["estadoCertificacion"];
        $fechaSeleccion = $input["fechaSeleccion"];
        $empresaContratista = isset($input["empresaContratista"]);
        ///seteamos la fecha /////
        if(isset($fechaSeleccion)){
            $fecha = explode("-",$fechaSeleccion);
            $fechaD = trim($fecha[0]);
            $fechaH = trim($fecha[1]);
            $fechaHasta = strtotime(str_replace('/', '-', $fechaH));
            $fechaDesde = strtotime(str_replace('/', '-', $fechaD));
        }
        if($estadoCertificacion == 1){
            $certificacion = "Ingresado";
        }if ($estadoCertificacion == 2) {
            $certificacion = "Ingresado";
        }if ($estadoCertificacion == 8) {
            $certificacion = "Completo";
        }if ($estadoCertificacion == 6) {
            $certificacion = "Documentado";
        }if ($estadoCertificacion == 9) {
            $certificacion = "Proceso";
        }if ($estadoCertificacion == 3) {
            $certificacion = "Aprobado";
        }if ($estadoCertificacion == 4) {
            $certificacion = "No Aprobado";
        }if ($estadoCertificacion == 5) {
            $certificacion = "Certificado";
        }if ($estadoCertificacion == 10) {
            $certificacion = "No Conforme";
        }
      
        foreach ($empresaPrincipal as $value) {

            $rutprincipalR[] = $value;
        }

        
        if(!empty($empresaContratista)){
               echo "existe contratistas";
            foreach ($empresaContratista as $value) {

                $rutcontratistas[] = $value;
            }
                if($periodo!= 0){

                    $contratistas =Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)->
                    whereIn('rut',$rutcontratistas)->
                    where([['periodId', '=', $periodo],
                           ['certificateState', '=', $estadoCertificacion],])->
                    get(['id','rut','dv','name','center','mainCompanyRut','periodId']);
                }
                if($periodo == 0){

                    $contratistas =Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)->
                    whereIn('rut',$rutcontratistas)->
                    where([['certificateState', '=', $estadoCertificacion],])->
                    whereBetween('certificateDate', array($fechaDesde,  $fechaHasta))->
                    get(['id','rut','dv','name','center','mainCompanyRut','periodId']);
                }
        }else{
             ;
            if($periodo == 0){

                $contratistas =Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)->where([
                ['certificateState', '=', $estadoCertificacion],])->
                whereBetween('certificateDate', array($fechaDesde,  $fechaHasta))->
                get(['id','rut','dv','name','center','mainCompanyRut','periodId'])->toArray();      
            }else{
            
                $contratistas =Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)->where([
                ['periodId', '=', $periodo],
                ['certificateState', '=', $estadoCertificacion],])->get(['id','rut','dv','name','center','mainCompanyRut','periodId'])->toArray();  
               
            }
        }

         
        // si existe Contratista//
        if(!empty($contratistas)){

            foreach ($contratistas as $value) {

                $datosContratistas[] = [$value["rut"], $value["mainCompanyRut"]] ;
               
                $datosTrabajadorVerificacion = TrabajadorVerificacion::where([
                ['periodId', '=', $value['periodId'] ],
                ['companyRut', '=', $value['rut'] ],
                ['companyCenter', '=', $value['center'] ],
                ['mainCompanyRut', '=', $value['mainCompanyRut'] ],])->get(['rut','dv','id'])->toArray();
            }

            //// fin verificacion laboral /// 
            /// folios sso con datos de empresas /// 
       
            foreach ($datosContratistas as $ruts) {
                $rutContratistas[]=$ruts[0];
                $folios = FolioSso::where('sso_comp_rut',$ruts[0])->where('sso_mcomp_rut',$ruts[1])->where('sso_status', '1')->get(['id'])->toArray();
               
                $foliosSSO[] = $folios;
             
            }


            foreach (array_filter($foliosSSO) as $folio) {
        
                $datosTrabajadorSSO = trabajadorSSO::distinct()->where('worker_status','1')->where('sso_id',$folio[0]["id"])->get(['sso_id','worker_rut','id'])->toArray();
                
                $trabajadoresSso[] = $datosTrabajadorSSO;
            }
        }else{

            $folios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipalR)->where('sso_status', '1')->get(['id'])->toArray();
        
            foreach (array_filter($folios) as $folio) {
                $datosTrabajadorSSO = trabajadorSSO::distinct()->where('worker_status','1')->where('sso_id',$folio["id"])->get(['sso_id','worker_rut','id'])->toArray();
                
                $trabajadoresSso[] = $datosTrabajadorSSO;
            }
        }

        function eliminaDuplicados($array){
                 $result = array_map("unserialize", array_unique(array_map("serialize", $array)));
               
                 foreach ($result as $key => $value)
                 {
                   if ( is_array($value) )
                   {
                     $result[$key] = eliminaDuplicados($value);
                   }
                 }

                 return $result;
            }

        

        if(!empty($trabajadoresSso)){

            $rutLimpio = 0;
            $dvrut = 0;
            $rut = 0;
            $rut2 = 0;
            $pos = false;
            foreach (array_filter($trabajadoresSso) as $value) {

                foreach ($value as $val) {
                    
                    $findme   = '.';
                    $pos = strpos($val["worker_rut"], $findme);

                    if ($pos === false) {
                        $rut = explode("-",$val["worker_rut"]);
                        $rutLimpio = $rut[0];
                        $dvrut = substr($val["worker_rut"],-1);
                    }else{
                        $rut2 = str_replace(".", "", $val["worker_rut"]);
                        $rut = explode("-",$rut2);
                        $rutLimpio = $rut[0];
                        $dvrut = substr($val["worker_rut"],-1);
                    }
                    $trabajadoresSSOLimpios[] = [$rutLimpio, $dvrut , $val["sso_id"], $val["id"]]; 
                    
                }
            }

            $trabajadoresSSO2 = eliminaDuplicados($trabajadoresSSOLimpios);
        }

  

        $NtrabajadoresCertificacion = count($datosTrabajadorVerificacion);

        $NtrabajadoresSSO = count($trabajadoresSSO2);

        function dv($r){
                $s=1;
                for($m=0;$r!=0;$r/=10)
                $s=($s+$r%10*(9-$m++%6))%11;
                return chr($s?$s+47:75);
        }

        if($NtrabajadoresCertificacion > 0 and $NtrabajadoresSSO > 0 ){


            if($NtrabajadoresSSO > $NtrabajadoresCertificacion){

                foreach($trabajadoresSSO2 as $sso){
                    
                    foreach ($datosTrabajadorVerificacion as $verificacion) {

                        if($sso[0] == $verificacion['rut']){

                            $trabajadores["id"] = $verificacion['id'];
                            $trabajadores["rut"] = $verificacion['rut'];
                            $trabajadores["dv"] = $verificacion['dv'];
                            $trabajadores['sso'] = 0;
                            $trabajadores['ver'] = 1;

                            $_WORK[] = $trabajadores;
                        }
                    }  
                        $trabajadores["id"] = $sso[3];
                        $trabajadores["rut"] = $sso[0];
                        $trabajadores["dv"] = $sso[1];
                        $trabajadores['sso'] = $sso[2];;
                        $trabajadores['ver'] = 0;

                        $_WORK[] = $trabajadores;
                    
                }              
            }else{
               
                foreach ($datosTrabajadorVerificacion as $verificacion) {

                    foreach ($trabajadoresSSO2 as $sso) {
                        
                        if($verificacion['rut'] == $sso[0]){

                            $trabajadores["id"] = $sso[3];
                            $trabajadores["rut"]= $sso[0];
                            $trabajadores["dv"] = $sso[1];
                            $trabajadores['sso'] =$sso[2];
                            $trabajadores['ver'] = 0;
                            $_WORK[] = $trabajadores;
                        }
                    }
                        $trabajadores["id"] = $verificacion['id'];
                        $trabajadores["rut"] = $verificacion['rut'];
                        $trabajadores["dv"] = $verificacion['dv'];
                        $trabajadores['sso'] = 0;
                        $trabajadores['ver'] = 1;

                        $_WORK[] = $trabajadores;
                }
                
            }

            //// consultamos en control de acceso las personas ingresadas duraten la fecha o el periodo ///

            /// consultamos el periodo ////
            if($periodo != 0 ){
                $periodos2 = Periodo::where('id', $periodo)->get(['monthId','year'])->toArray();
                $mesP = $periodos2[0]['monthId'];
                $year = $periodos2[0]['year'];
                $bandera = 0;
            }else{
                $bandera = 1;
            }
        
            if($bandera==0){

                $fechaHoy = getdate();         
                if($mesP == 1){
                    $mes="01"; 
                    $fechaIn =  $year."-".$mes."-01 00:01:01";    
                    $fechaFi = $year."-".$mes."-31 23:59:59";
                       if(!empty($rutContratistas)){
                        $datosAcceso =AccesoPersona::distinct()->
                        whereIn('ACC_RUT_PPAL',$rutprincipalR)->
                        whereIn('ACC_RUT_CONTRATISTA', $rutContratistas)->
                        where('ACC_TAC_ID', '1')->
                        whereBetween('ACC_FECHA_ACCESO', array($fechaIn,  $fechaFi))->
                        get(['ACC_ID','ACC_RUT','ACC_FECHA_ACCESO','ACC_COLOR_CONSULTA','ACC_CENTRO_COSTO'])->toArray();
                    }else{
                        $datosAcceso =AccesoPersona::distinct()->
                        whereIn('ACC_RUT_PPAL',$rutprincipalR)->
                        where('ACC_TAC_ID', '1')->
                        whereBetween('ACC_FECHA_ACCESO', array($fechaIn,  $fechaFi))->
                        get(['ACC_ID','ACC_RUT','ACC_FECHA_ACCESO','ACC_COLOR_CONSULTA','ACC_CENTRO_COSTO'])->toArray();
                    }
                    
                }
                if($mesP == 2){
                    $mes="02"; 
                    $fechaIn =  $year."-".$mes."-01 00:01:01";    
                    $fechaFi = $year."-".$mes."-29 23:59:59";
                   if(!empty($rutContratistas)){
                        $datosAcceso =AccesoPersona::distinct()->
                        whereIn('ACC_RUT_PPAL',$rutprincipalR)->
                        whereIn('ACC_RUT_CONTRATISTA', $rutContratistas)->
                        where('ACC_TAC_ID', '1')->
                        whereBetween('ACC_FECHA_ACCESO', array($fechaIn,  $fechaFi))->
                        get(['ACC_ID','ACC_RUT','ACC_FECHA_ACCESO','ACC_COLOR_CONSULTA','ACC_CENTRO_COSTO'])->toArray();
                    }else{
                        $datosAcceso =AccesoPersona::distinct()->
                        whereIn('ACC_RUT_PPAL',$rutprincipalR)->
                        where('ACC_TAC_ID', '1')->
                        whereBetween('ACC_FECHA_ACCESO', array($fechaIn,  $fechaFi))->
                        get(['ACC_ID','ACC_RUT','ACC_FECHA_ACCESO','ACC_COLOR_CONSULTA','ACC_CENTRO_COSTO'])->toArray(); 
                    }
                }
                if($mesP == 3){
                    $mes="03";
                    $fechaIn =  $year."-".$mes."-01 00:01:01";    
                    $fechaFi = $year."-".$mes."-31 23:59:59";
                    if(!empty($rutContratistas)){
                        $datosAcceso =AccesoPersona::distinct()->
                        whereIn('ACC_RUT_PPAL',$rutprincipalR)->
                        whereIn('ACC_RUT_CONTRATISTA', $rutContratistas)->
                        where('ACC_TAC_ID', '1')->
                        whereBetween('ACC_FECHA_ACCESO', array($fechaIn,  $fechaFi))->
                        get(['ACC_ID','ACC_RUT','ACC_FECHA_ACCESO','ACC_COLOR_CONSULTA','ACC_CENTRO_COSTO'])->toArray();
                    }else{
                        $datosAcceso =AccesoPersona::distinct()->
                        whereIn('ACC_RUT_PPAL',$rutprincipalR)->
                        where('ACC_TAC_ID', '1')->
                        whereBetween('ACC_FECHA_ACCESO', array($fechaIn,  $fechaFi))->
                        get(['ACC_ID','ACC_RUT','ACC_FECHA_ACCESO','ACC_COLOR_CONSULTA','ACC_CENTRO_COSTO'])->toArray(); 
                    }
                   
                }
                if($mesP == 4){
                    $mes="04";
                    $fechaIn =  $year."-".$mes."-01 00:01:01";    
                    $fechaFi = $year."-".$mes."-30 23:59:59";
                   if(!empty($rutContratistas)){
                        $datosAcceso =AccesoPersona::distinct()->
                        whereIn('ACC_RUT_PPAL',$rutprincipalR)->
                        whereIn('ACC_RUT_CONTRATISTA', $rutContratistas)->
                        where('ACC_TAC_ID', '1')->
                        whereBetween('ACC_FECHA_ACCESO', array($fechaIn,  $fechaFi))->
                        get(['ACC_ID','ACC_RUT','ACC_FECHA_ACCESO','ACC_COLOR_CONSULTA','ACC_CENTRO_COSTO'])->toArray();
                    }else{
                        $datosAcceso =AccesoPersona::distinct()->
                        whereIn('ACC_RUT_PPAL',$rutprincipalR)->
                        where('ACC_TAC_ID', '1')->
                        whereBetween('ACC_FECHA_ACCESO', array($fechaIn,  $fechaFi))->
                        get(['ACC_ID','ACC_RUT','ACC_FECHA_ACCESO','ACC_COLOR_CONSULTA','ACC_CENTRO_COSTO'])->toArray(); 
                    }
                }
                if($mesP == 5){
                    $mes="05";
                    $fechaIn = $year."-".$mes."-01 00:01:01";    
                    $fechaFi = $year."-".$mes."-31 23:59:59";
                   if(!empty($rutContratistas)){
                        $datosAcceso =AccesoPersona::distinct()->
                        whereIn('ACC_RUT_PPAL',$rutprincipalR)->
                        whereIn('ACC_RUT_CONTRATISTA', $rutContratistas)->
                        where('ACC_TAC_ID', '1')->
                        whereBetween('ACC_FECHA_ACCESO', array($fechaIn,  $fechaFi))->
                        get(['ACC_ID','ACC_RUT','ACC_FECHA_ACCESO','ACC_COLOR_CONSULTA','ACC_CENTRO_COSTO'])->toArray();
                    }else{
                        $datosAcceso =AccesoPersona::distinct()->
                        whereIn('ACC_RUT_PPAL',$rutprincipalR)->
                        where('ACC_TAC_ID', '1')->
                        whereBetween('ACC_FECHA_ACCESO', array($fechaIn,  $fechaFi))->
                        get(['ACC_ID','ACC_RUT','ACC_FECHA_ACCESO','ACC_COLOR_CONSULTA','ACC_CENTRO_COSTO'])->toArray(); 
                    }
                }
                if($mesP == 6){
                    $mes="06";
                    $fechaIn = $year."-".$mes."-01 00:01:01";    
                    $fechaFi = $year."-".$mes."-30 23:59:59";
                    if(!empty($rutContratistas)){
                        $datosAcceso =AccesoPersona::distinct()->
                        whereIn('ACC_RUT_PPAL',$rutprincipalR)->
                        whereIn('ACC_RUT_CONTRATISTA', $rutContratistas)->
                        where('ACC_TAC_ID', '1')->
                        whereBetween('ACC_FECHA_ACCESO', array($fechaIn,  $fechaFi))->
                        get(['ACC_ID','ACC_RUT','ACC_FECHA_ACCESO','ACC_COLOR_CONSULTA','ACC_CENTRO_COSTO'])->toArray();
                    }else{
                        $datosAcceso =AccesoPersona::distinct()->
                        whereIn('ACC_RUT_PPAL',$rutprincipalR)->
                        where('ACC_TAC_ID', '1')->
                        whereBetween('ACC_FECHA_ACCESO', array($fechaIn,  $fechaFi))->
                        get(['ACC_ID','ACC_RUT','ACC_FECHA_ACCESO','ACC_COLOR_CONSULTA','ACC_CENTRO_COSTO'])->toArray(); 
                    }
                }
                if($mesP== 7){
                    $mes="07";
                    $fechaIn = $year."-".$mes."-01 00:01:01";    
                    $fechaFi = $year."-".$mes."-31 23:59:59";
                    if(!empty($rutContratistas)){
                        $datosAcceso =AccesoPersona::distinct()->
                        whereIn('ACC_RUT_PPAL',$rutprincipalR)->
                        whereIn('ACC_RUT_CONTRATISTA', $rutContratistas)->
                        where('ACC_TAC_ID', '1')->
                        whereBetween('ACC_FECHA_ACCESO', array($fechaIn,  $fechaFi))->
                        get(['ACC_ID','ACC_RUT','ACC_FECHA_ACCESO','ACC_COLOR_CONSULTA','ACC_CENTRO_COSTO'])->toArray();
                    }else{
                        $datosAcceso =AccesoPersona::distinct()->
                        whereIn('ACC_RUT_PPAL',$rutprincipalR)->
                        where('ACC_TAC_ID', '1')->
                        whereBetween('ACC_FECHA_ACCESO', array($fechaIn,  $fechaFi))->
                        get(['ACC_ID','ACC_RUT','ACC_FECHA_ACCESO','ACC_COLOR_CONSULTA','ACC_CENTRO_COSTO'])->toArray(); 
                    }
                }
                if($mesP == 8){
                    $mes="08";
                    $fechaIn = $year."-".$mes."-01 00:01:01";    
                    $fechaFi = $year."-".$mes."-31 23:59:59";
                    if(!empty($rutContratistas)){
                        $datosAcceso =AccesoPersona::distinct()->
                        whereIn('ACC_RUT_PPAL',$rutprincipalR)->
                        whereIn('ACC_RUT_CONTRATISTA', $rutContratistas)->
                        where('ACC_TAC_ID', '1')->
                        whereBetween('ACC_FECHA_ACCESO', array($fechaIn,  $fechaFi))->
                        get(['ACC_ID','ACC_RUT','ACC_FECHA_ACCESO','ACC_COLOR_CONSULTA','ACC_CENTRO_COSTO'])->toArray();
                    }else{
                        $datosAcceso =AccesoPersona::distinct()->
                        whereIn('ACC_RUT_PPAL',$rutprincipalR)->
                        where('ACC_TAC_ID', '1')->
                        whereBetween('ACC_FECHA_ACCESO', array($fechaIn,  $fechaFi))->
                        get(['ACC_ID','ACC_RUT','ACC_FECHA_ACCESO','ACC_COLOR_CONSULTA','ACC_CENTRO_COSTO'])->toArray(); 
                    }
                }
                if($mesP == 9){
                    $mes="09";
                    $fechaIn = $year."-".$mes."-01 00:01:01";    
                    $fechaFi = $year."-".$mes."-30 23:59:59";
                    if(!empty($rutContratistas)){
                        $datosAcceso =AccesoPersona::distinct()->
                        whereIn('ACC_RUT_PPAL',$rutprincipalR)->
                        whereIn('ACC_RUT_CONTRATISTA', $rutContratistas)->
                        where('ACC_TAC_ID', '1')->
                        whereBetween('ACC_FECHA_ACCESO', array($fechaIn,  $fechaFi))->
                        get(['ACC_ID','ACC_RUT','ACC_FECHA_ACCESO','ACC_COLOR_CONSULTA','ACC_CENTRO_COSTO'])->toArray();
                    }else{
                        $datosAcceso =AccesoPersona::distinct()->
                        whereIn('ACC_RUT_PPAL',$rutprincipalR)->
                        where('ACC_TAC_ID', '1')->
                        whereBetween('ACC_FECHA_ACCESO', array($fechaIn,  $fechaFi))->
                        get(['ACC_ID','ACC_RUT','ACC_FECHA_ACCESO','ACC_COLOR_CONSULTA','ACC_CENTRO_COSTO'])->toArray(); 
                    }
                }
                if($mesP == 10){
                    $mes="10";
                    $fechaIn = $year."-".$mes."-01 00:01:01";    
                    $fechaFi = $year."-".$mes."-31 23:59:59";
                    if(!empty($rutContratistas)){
                        $datosAcceso =AccesoPersona::distinct()->
                        whereIn('ACC_RUT_PPAL',$rutprincipalR)->
                        whereIn('ACC_RUT_CONTRATISTA', $rutContratistas)->
                        where('ACC_TAC_ID', '1')->
                        whereBetween('ACC_FECHA_ACCESO', array($fechaIn,  $fechaFi))->
                        get(['ACC_ID','ACC_RUT','ACC_FECHA_ACCESO','ACC_COLOR_CONSULTA','ACC_CENTRO_COSTO'])->toArray();
                    }else{
                        $datosAcceso =AccesoPersona::distinct()->
                        whereIn('ACC_RUT_PPAL',$rutprincipalR)->
                        where('ACC_TAC_ID', '1')->
                        whereBetween('ACC_FECHA_ACCESO', array($fechaIn,  $fechaFi))->
                        get(['ACC_ID','ACC_RUT','ACC_FECHA_ACCESO','ACC_COLOR_CONSULTA','ACC_CENTRO_COSTO'])->toArray(); 
                    }
                }
                if($mesP == 11){
                    $mes="11";
                    $fechaIn = $year."-".$mes."-01 00:01:01";    
                    $fechaFi = $year."-".$mes."-30 23:59:59";
                    if(!empty($rutContratistas)){
                        $datosAcceso =AccesoPersona::distinct()->
                        whereIn('ACC_RUT_PPAL',$rutprincipalR)->
                        whereIn('ACC_RUT_CONTRATISTA', $rutContratistas)->
                        where('ACC_TAC_ID', '1')->
                        whereBetween('ACC_FECHA_ACCESO', array($fechaIn,  $fechaFi))->
                        get(['ACC_ID','ACC_RUT','ACC_FECHA_ACCESO','ACC_COLOR_CONSULTA','ACC_CENTRO_COSTO'])->toArray();
                    }else{
                        $datosAcceso =AccesoPersona::distinct()->
                        whereIn('ACC_RUT_PPAL',$rutprincipalR)->
                        where('ACC_TAC_ID', '1')->
                        whereBetween('ACC_FECHA_ACCESO', array($fechaIn,  $fechaFi))->
                        get(['ACC_ID','ACC_RUT','ACC_FECHA_ACCESO','ACC_COLOR_CONSULTA','ACC_CENTRO_COSTO'])->toArray(); 
                    }
                }
                if($mesP == 12){
                    $mes="12";
                    $fechaIn = $year."-".$mes."-01 00:01:01";    
                    $fechaFi = $year."-".$mes."-31 23:59:59";
                    if(!empty($rutContratistas)){
                        $datosAcceso =AccesoPersona::distinct()->
                        whereIn('ACC_RUT_PPAL',$rutprincipalR)->
                        whereIn('ACC_RUT_CONTRATISTA', $rutContratistas)->
                        where('ACC_TAC_ID', '1')->
                        whereBetween('ACC_FECHA_ACCESO', array($fechaIn,  $fechaFi))->
                        get(['ACC_ID','ACC_RUT','ACC_FECHA_ACCESO','ACC_COLOR_CONSULTA','ACC_CENTRO_COSTO'])->toArray();
                    }else{
                        $datosAcceso =AccesoPersona::distinct()->
                        whereIn('ACC_RUT_PPAL',$rutprincipalR)->
                        where('ACC_TAC_ID', '1')->
                        whereBetween('ACC_FECHA_ACCESO', array($fechaIn,  $fechaFi))->
                        get(['ACC_ID','ACC_RUT','ACC_FECHA_ACCESO','ACC_COLOR_CONSULTA','ACC_CENTRO_COSTO'])->toArray(); 
                    }
                }
            }if($bandera == 1){

               
                $fechaHastaA = str_replace('/', '-', $fechaH);
                $fechahasta = explode("-",$fechaHastaA);
                $diaA = $fechahasta[0];
                $mesA = $fechahasta[1];
                $yearA = $fechahasta[2];
                $fechaDesdeA = str_replace('/', '-', $fechaD);
                $fechadesde = explode("-",$fechaDesdeA);
                $diaD = $fechadesde[0];
                $mesD = $fechadesde[1];
                $yearD = $fechadesde[2];
                $fechaFi = $yearA."-".$mesA."-".$diaA." 00:01:01";    
                $fechaIn = $yearD."-".$mesD."-".$diaD." 23:59:59";
                if(!empty($rutContratistas)){
                    $datosAcceso =AccesoPersona::distinct()->
                    whereIn('ACC_RUT_PPAL',$rutprincipalR)->
                    whereIn('ACC_RUT_CONTRATISTA', $rutContratistas)->
                    where('ACC_TAC_ID', '1')->
                    whereBetween('ACC_FECHA_ACCESO', array($fechaIn,  $fechaFi))->
                    get(['ACC_ID','ACC_RUT','ACC_FECHA_ACCESO','ACC_COLOR_CONSULTA','ACC_CENTRO_COSTO'])->toArray();
                }else{
                    $datosAcceso =AccesoPersona::distinct()->
                    whereIn('ACC_RUT_PPAL',$rutprincipalR)->
                    where('ACC_TAC_ID', '1')->
                    whereBetween('ACC_FECHA_ACCESO', array($fechaIn,  $fechaFi))->
                    get(['ACC_ID','ACC_RUT','ACC_FECHA_ACCESO','ACC_COLOR_CONSULTA','ACC_CENTRO_COSTO'])->toArray();
                }

            }
                 
            foreach ($datosAcceso as  $acceso) {
              
                $rutAcceso["rut"] = $acceso["ACC_RUT"];
                $rutAcceso["id"] = $acceso["ACC_ID"];
                $rutAcceso["fechaAcceso"] = $acceso["ACC_FECHA_ACCESO"];
                $rutAcceso["color"] = $acceso["ACC_COLOR_CONSULTA"];
                $rutAcceso["lugar"] = $acceso["ACC_CENTRO_COSTO"];
                $_ACCESO[] = $rutAcceso;
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
            
            $ACCESO2 = super_unique($_ACCESO,'rut');
            $NtrabajadoresAcceso = count($ACCESO2);

          
                if($NtrabajadoresAcceso > 0){

                    foreach ($_WORK as $WK) {

                        foreach ($ACCESO2 as $AC) {




                            # code...
                        }

                        echo "<pre>";
                            print_r($WK);
                        echo "</pre>";
                        
                    }
                    
                }

            



          /*  echo "<pre>";
            print_r($todoTrabajadores);
            echo "</pre>";*/

             return view('reportesAquaChile.reporteIntegradoCaVSso',compact('todoTrabajadores','datosUsuarios','EmpresasP','periodos','aquaChile','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile'));

        } /// if existe en ambas plataformas

        exit();

      
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

    /// reporte Acreditacion SSO

   
}
