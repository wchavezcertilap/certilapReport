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
use App\Solicitud;
use App\Certificado;
use App\Cuadratura;
use App\TrabajadorVerificacion;
use App\tipoEmpresa;
use App\tipoServicio;
use App\categoriaServicio;
use App\direccion;
use App\gerencia;
use App\EstadoCargaMasiva;
use App\DocumentoRechazdo;
use App\CertificateHistory;
use App\ObserTrabComp;
use Illuminate\Http\Request;

class ReporteObsCertController extends Controller
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
        return view('reporteObser.index',compact('EmpresasP','periodos','datosUsuarios','etiquetasEstados','valoresEstados','certificacion','periodosT','principalesTexto','usuarioAqua','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile'));
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
        $estados=[10,4];
        if($tipoBsuqueda == 1){

            $peridoInicio = $input["peridoInicio"];
            $periodosIT = Periodo::where('id', $peridoInicio)->get(['id', 'monthId','year']);
            $periodosIT->load('mes')->toArray();
            $peridoFinal = $input["peridoFinal"];
            $periodosFT = Periodo::where('id', $peridoFinal)->get(['id', 'monthId','year']);
            $periodosFT->load('mes')->toArray();
           
            $periodosFT= $periodosFT[0]['mes'][0]['name'];
            $periodosIT= $periodosIT[0]['mes'][0]['name'];
            $periodosT =  $periodosIT ."-".$periodosFT ;           


            if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista != 0 AND $centroCosto != 0){

            $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
          
            ->whereIn('rut',$rutcontratistasR)
            ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
            ->where('id',$centroCosto)
            ->whereIn('certificateState',$estados)
            ->orderBy('Company.id', 'ASC')->get(['Company.id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType','servicioId'])->toArray();

            }
            if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista != 0){

            $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
           
            ->whereIn('rut',$rutcontratistasR)
            ->whereIn('certificateState',$estados)
            ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
            ->orderBy('Company.id', 'ASC')->get(['Company.id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType','servicioId'])->toArray();

            }if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista == 0 AND $centroCosto == 0){

                $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
               
                 ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
                 ->whereIn('certificateState',$estados)
                 ->orderBy('Company.id', 'ASC')->get(['Company.id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType','servicioId'])->toArray();

            }

        }

        if($tipoBsuqueda == 2){

            $fechaSeleccion = $input["fechaSeleccion"];
            if($fechaSeleccion != 0  AND $countContratista != 0 AND $centroCosto != 0){
            
            $fechas = $porciones = explode("_", $fechaSeleccion);
            $fecha1 = $fechas[0];
            $fecha2 = $fechas[1];
            $periodosT = $fecha1 ."-".$fecha2;
             $fechasDesde = strtotime ( '+4 hour' ,strtotime($fecha1));
            //sumo 1 día
            $fechasHasta = strtotime ( '+4 hour' ,strtotime($fecha2));
            $empresasContratista = Contratista::distinct()->select('Company.id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType','servicioId')->whereIn('mainCompanyRut',$rutprincipalR)
            
            ->whereIn('rut',$rutcontratistasR)
            ->whereBetween('certificateDate', [$fechasDesde,$fechasHasta])
            ->where('id',$centroCosto)
            ->whereIn('certificateState',$estados)
            ->orderBy('Company.id', 'ASC')->get()->toArray();

            }if($fechaSeleccion != 0  AND $countContratista != 0 ){
            
            $fechas = $porciones = explode("_", $fechaSeleccion);
            $fecha1 = $fechas[0];
            $fecha2 = $fechas[1];
            $periodosT = $fecha1 ."-".$fecha2;
            $fechasDesde = strtotime ( '+4 hour' ,strtotime($fecha1));
            //sumo 1 día
            $fechasHasta = strtotime ( '+4 hour' ,strtotime($fecha2));
            $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
           
            ->whereIn('rut',$rutcontratistasR)
            ->whereBetween('certificateDate', [$fechasDesde,$fechasHasta])
            ->whereIn('certificateState',$estados)
            ->orderBy('Company.id', 'ASC')->get(['Company.id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType','servicioId'])->toArray();

            }
            if($fechaSeleccion != 0  AND $countContratista == 0 AND $centroCosto == 0){
            
            $fechas = $porciones = explode("_", $fechaSeleccion);
            $fecha1 = $fechas[0];
            $fecha2 = $fechas[1];
            $periodosT = $fecha1 ."-".$fecha2;
            $fechasDesde = strtotime ( '+4 hour' ,strtotime($fecha1));
            //sumo 1 día
            $fechasHasta = strtotime ( '+4 hour' ,strtotime($fecha2));
            
            $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
            
            ->whereBetween('certificateDate', [$fechasDesde,$fechasHasta])
            ->whereIn('certificateState',$estados)
            ->orderBy('Company.id', 'ASC')->get(['Company.id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType','servicioId'])->toArray();

            }
        }

        ;

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


       
        $obsCargaMasiva = 0;
        $obsFiniquitos = 0;
        $obsExtranjero = 0;
        $obsLibroRemu = 0;
        $obsLaboral = 0;
        $obsLicenciaMed =0;
        $obsTributarios = 0;
        $obsDeclaracion = 0;
        $obsPrevisional = 0;

        
        if(!empty($empresasContratista)){
            foreach ($empresasContratista as $contratista) {

                $peridoTex = periodoTexto($contratista['periodId']);
                $estadoCerficacionTexto = estadoCerficacionTexto($contratista['certificateState']);
                $fechaCertificiacion=date('d/m/Y', $contratista['certificateDate']);
                    
                if($contratista['companyTypeId'] == 1){
                        $rutContratista = $contratista['rut']."-".$contratista['dv']; 
                        $nombreContratista = ucwords(mb_strtolower($contratista['name'],'UTF-8')); 
                        $rutSubContratista = "";
                        $nombreSubContratista = "";
                        $rutContratista2 = $contratista['rut'];
                }
                if($contratista['companyTypeId'] == 2){
                        $rutContratista = $contratista['subcontratistaRut']."-".$contratista['subcontratistaDv'];
                        $nombreContratista =  ucwords(mb_strtolower($contratista['subcontratistaName'],'UTF-8'));  
                        $rutSubContratista = $contratista['rut']."-".$contratista['dv']; 
                        $nombreSubContratista = ucwords(mb_strtolower($contratista['name'],'UTF-8')); 
                        $rutContratista2 = $contratista['rut'];

                }

                // segundo ciclo obtiene datos del certificador
                $idUsuarioDoc = DocumentoRechazdo::where('id_company',$contratista['id'])->where('doc_reenviado',1)->orderby('fecha','DESC')->take(1)->get(['id_usuario','fecha'])->toArray();
                $idUsuarioCar = EstadoCargaMasiva::where('id_company',$contratista['id'])->where('cargaerror',1)->orderby('fecha','DESC')->take(1)->get(['id_usuario','fecha','cargaerror','id_company'])->toArray();

                // print_r($idUsuarioCar);
                if(!empty($idUsuarioDoc) and !empty($idUsuarioCar)){
                    $historialCertificado6 = CertificateHistory::where('companyId',$contratista['id'])->where('certificateState',6)->get(['userName','certificateState'])->toArray();

                    $historialCertificado8 = CertificateHistory::where('companyId',$contratista['id'])->where('certificateState',8)->get(['userName','certificateState'])->toArray();


                        if(!empty($historialCertificado6)){
                            $cantidadEstadoDoc = count($historialCertificado6);
                            if ($cantidadEstadoDoc >= 2) {
                                
                                $historialCertificador = CertificateHistory::where('companyId',$contratista['id'])->whereIn('certificateState',array(4))->orderby('id','DESC')->take(1)->get(['userName','id'])->toArray();
                                if(!empty($historialCertificador)){
                                    $nombreCertificador = $historialCertificador[0]['userName'];
                                     $ciclo = "Segundo";
                                }
                                
                            }else{
                                $historialCertificador = CertificateHistory::where('companyId',$contratista['id'])->whereIn('certificateState',array(9))->orderby('id','DESC')->take(1)->get(['userName','id'])->toArray();
                                if(!empty($historialCertificador)){
                                    $nombreCertificador = $historialCertificador[0]['userName'];
                                    $ciclo = "Primero";
                                }else{
                                    $nombreCertificador = "";
                                    $ciclo = "Primero";
                                }

                            }
                        }elseif(!empty($historialCertificado8)){
                            $cantidadEstadoCom = count($historialCertificado8);
                            if ($cantidadEstadoCom >= 2) {
                               
                                $historialCertificador = CertificateHistory::where('companyId',$contratista['id'])->whereIn('certificateState',array(4))->orderby('id','DESC')->take(1)->get(['userName','id'])->toArray();
                                if(!empty($historialCertificador)){
                                    $nombreCertificador = $historialCertificador[0]['userName'];
                                     $ciclo = "Segundo";
                                }
                                
                            }else{
                                $historialCertificador = CertificateHistory::where('companyId',$contratista['id'])->whereIn('certificateState',array(9))->orderby('id','DESC')->take(1)->get(['userName','id'])->toArray();
                                if(!empty($historialCertificador)){
                                    $nombreCertificador = $historialCertificador[0]['userName'];
                                    $ciclo = "Primero";
                                }else{
                                    $nombreCertificador = "";
                                    $ciclo = "Primero";
                                }
                            }
                        }
                }else{
                    
                    $historialCertificado6 = CertificateHistory::where('companyId',$contratista['id'])->where('certificateState',6)->get(['userName','certificateState'])->toArray();

                    $historialCertificado8 = CertificateHistory::where('companyId',$contratista['id'])->where('certificateState',8)->get(['userName','certificateState'])->toArray();

                    if(!empty($historialCertificado6)){
                        $cantidadEstadoDoc = count($historialCertificado6);
                        if ($cantidadEstadoDoc >= 2) {
                            $ciclo = "Segundo";
                            $historialCertificador = CertificateHistory::where('companyId',$contratista['id'])->whereIn('certificateState',array(4))->orderby('id','DESC')->take(1)->get(['userName','id'])->toArray();
                            if(!empty($historialCertificador)){
                                    $nombreCertificador = $historialCertificador[0]['userName'];
                                     $ciclo = "Segundo";
                            }
                           
                        }else{
                            $historialCertificador = CertificateHistory::where('companyId',$contratista['id'])->whereIn('certificateState',array(9))->orderby('id','DESC')->take(1)->get(['userName','id'])->toArray();
                            if(!empty($historialCertificador)){
                                    $nombreCertificador = $historialCertificador[0]['userName'];
                                    $ciclo = "Primero";
                            }else{
                                    $nombreCertificador = "";
                                    $ciclo = "Primero";
                            }
                        }
                    }elseif(!empty($historialCertificado8)){
                        $cantidadEstadoCom = count($historialCertificado8);
                        if ($cantidadEstadoCom >= 2) {
                            
                            $historialCertificador = CertificateHistory::where('companyId',$contratista['id'])->whereIn('certificateState',array(4))->orderby('id','DESC')->take(1)->get(['userName','id'])->toArray();
                            if(!empty($historialCertificador)){
                                    $nombreCertificador = $historialCertificador[0]['userName'];
                                     $ciclo = "Segundo";
                            }
                            
                        }else{
                            $historialCertificador = CertificateHistory::where('companyId',$contratista['id'])->whereIn('certificateState',array(9))->orderby('id','DESC')->take(1)->get(['userName','id'])->toArray();
                            if(!empty($historialCertificador)){
                                    $nombreCertificador = $historialCertificador[0]['userName'];
                                    $ciclo = "Primero";
                            }else{
                                    $nombreCertificador = "";
                                    $ciclo = "Primero";
                            }
                        }
                    }else{
                        $nombreCertificador = "";
                        $ciclo = "Primero";
                    }
                }


                $Datoscertificacion['id'] = $contratista['id']; 
                $Datoscertificacion['rutPrincipal'] = formatRut($contratista['mainCompanyRut']); 
                $Datoscertificacion['nombrePrincipal'] = ucwords(mb_strtolower($contratista['mainCompanyName'],'UTF-8'));    
                $Datoscertificacion['rutContratista'] = $rutContratista;
                $Datoscertificacion['nombreContratista'] = $nombreContratista; 
                $Datoscertificacion['rutSubContratista'] =  $rutSubContratista;
                $Datoscertificacion['nombreSubContratista'] = $nombreSubContratista;
                $Datoscertificacion['centroCosto'] = ucwords(mb_strtolower($contratista['center'],'UTF-8'));
                $Datoscertificacion['perido'] = ucwords(mb_strtolower($peridoTex,'UTF-8'));
                $Datoscertificacion['ciclo'] = ucwords(mb_strtolower($ciclo,'UTF-8'));  
                $Datoscertificacion['certificador'] = ucwords(mb_strtolower($nombreCertificador,'UTF-8'));
                $Datoscertificacion['estadoCertificacion'] = ucwords(mb_strtolower($estadoCerficacionTexto,'UTF-8')); 
                $Datoscertificacion['fechaCertificado'] =  $fechaCertificiacion;

                $observacion1=DB::table('obserTrabComp')
                ->join('DocumentType', function ($join) {
                    $join->on('DocumentType.id', '=', 'obserTrabComp.idDocumento');
                })
                 ->join('documentoObser', function ($join) {
                    $join->on('documentoObser.id', '=', 'obserTrabComp.idObservacion');
                })
                ->where('obserTrabComp.idCompany', '=' ,$contratista['id']) 

                ->get(['DocumentType.id as idDoc','DocumentType.name as docName','documentoObser.observacion','obserTrabComp.idTrabajador','obserTrabComp.idObservacion','obserTrabComp.datotrabajdor'])->toArray();
                    $nombreTra = ""; 
                    $rutTrab = "";
                    $observacionText ="";
                    $documentoText = "";
                        $motivoOBS = "";
                      
                    if(!empty($observacion1[0])){
                       
                        foreach ($observacion1 as $obs1) {
                           


                            if ($obs1->idDoc == 240 or $obs1->idDoc == 238){
                                $obsCargaMasiva++;
                                $motivoOBS= "Modificar Solicitud y/o Carga Masiva";
                            }///carga masiva
                            if ($obs1->idDoc == 6){
                                $obsFiniquitos++;
                                $motivoOBS= "Finiquitos";
                            }//finiquitos
                            if ($obs1->idDoc == 225 or $obs1->idDoc == 224 or $obs1->idDoc == 226){
                                $obsExtranjero++;
                                $motivoOBS= "Extranjeros";
                            }//extranjeros
                            if ($obs1->idDoc == 1 or $obs1->idDoc == 8 or $obs1->idObservacion == 127){
                                $obsLibroRemu++;
                                $motivoOBS= "LAR";
                            }// libro remuneracion
                            if ($obs1->idDoc == 2 or $obs1->idDoc == 9 or $obs1->idDoc == 97 or $obs1->idDoc == 94 or $obs1->idDoc == 98 or $obs1->idDoc == 93){
                                $obsLaboral++;
                                $motivoOBS= "Laboral";
                            }// laboral
                            if ($obs1->idDoc == 13){
                                $obsLicenciaMed++;
                                $motivoOBS= "Licencias Médicas";
                            }//previsionales
                            if ($obs1->idDoc == 3){
                                $obsPrevisional++;
                                $motivoOBS= "Previsional";
                            }
                            if ($obs1->idDoc == 10){
                                $obsDeclaracion++;
                                $motivoOBS= "Declaración Jurada";
                            }
                            if ($obs1->idDoc == 242 or $obs1->idDoc == 4  or $obs1->idDoc == 5){
                                $obsTributarios++;
                                $motivoOBS= "Tributarios";
                            }

                            if($obs1->idTrabajador > 0){
                                $datoTrabajadores = TrabajadorVerificacion::where('id',$obs1->idTrabajador)->
                                                                     select('rut','dv','names','firstLastName','secondLastName')->get()->toArray();

                                if(!empty($datoTrabajadores[0]['names'])){
                                         $nombreTra = $datoTrabajadores[0]['names'].' '.$datoTrabajadores[0]['firstLastName'].' '.$datoTrabajadores[0]['secondLastName'];
                                        $rutTrab = $datoTrabajadores[0]['rut'].'-'.$datoTrabajadores[0]['dv'];  
                                }else{

                                    if(!empty($obs1->datotrabajdor)){
                                        $datotrabajdorT = explode('_', $obs1->datotrabajdor);
                                        
                                        for ($i=0; $i <= count($datotrabajdorT); $i++) { 
                                            $nombreTra = $nombreTra." ".$nombreTra[$i];
                                        }
                                        $rutTrab = $datotrabajdor[0];
                                    }else{
                                        $nombreTra = ""; 
                                        $rutTrab = "";
                                    }  
                                }
                            }else{
                               unset($datotrabajdorT);
                               if(!empty($obs1->datotrabajdor)){
                                    $datotrabajdorT = explode('_', $obs1->datotrabajdor);

                                    if(count($datotrabajdorT) > 0){
                                        
                                        $nombreTra = $datotrabajdorT[1]." ".$datotrabajdorT[2]." ".$datotrabajdorT[3];
                                        $rutTrab = $datotrabajdorT[0];

                                    }else{
                                       $nombreTra = ""; 
                                        $rutTrab = ""; 
                                    }
                                    
                                    
                                }else{
                                    $nombreTra = ""; 
                                    $rutTrab = "";
                                }  
                            }

                            $observacionText = $obs1->observacion;
                            $documentoText = $obs1->docName;
                            unset($DatoObser);
                            $DatoObser['rutTrabajasor'] = $rutTrab;
                            $DatoObser['nombreTrabajador'] = ucwords(mb_strtolower($nombreTra,'UTF-8'));
                            $DatoObser['documento'] = ucwords(mb_strtolower($documentoText,'UTF-8'));  
                            $DatoObser['observacion'] = ucwords(mb_strtolower($observacionText,'UTF-8'));
                            $DatoObser['observacionMotivo'] = ucwords(mb_strtolower($motivoOBS,'UTF-8'));
                            $bservacion[] = $DatoObser;
                           
                        }
                        $Datoscertificacion['observaciones'] = $bservacion;
                        unset($bservacion);
                       
                    }

                    $reporteObservacion[] = $Datoscertificacion;

            }

           
            if(!empty($reporteObservacion[0])){
                $lista='<table id="datosTabla" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                          <th>id</th>
                          <th>RUT Principal</th>
                          <th>Empresa Principal</th>
                          <th>RUT Contratista</th>
                          <th>Empresa Contratista</th>
                          <th>Centro de Costo</th>
                          <th>RUT Sub Contratista</th>
                          <th>Sub Contratista</th>
                          <th>Periodo</th>
                          <th>Estado Certificación</th>
                          <th>Fecha Estado Certificación</th>
                          <th>RUT Trabajador</th>
                          <th>Nombre Trabajador</th>
                          <th>Documento</th>
                          <th>observación</th>
                          <th>Motivo</th>
                          </thead>
                          <tbody>';
            
                foreach ($reporteObservacion as $datoOBS) {
                    if(!empty($datoOBS["observaciones"][0])){
                        foreach ($datoOBS["observaciones"] as  $datosObs) {
                            $lista.= "<tr>";
                            $lista.= "<td>".$datoOBS["id"]."</td>";
                            $lista.= "<td>".$datoOBS["rutPrincipal"]."</td>";
                            $lista.= "<td>".$datoOBS["nombrePrincipal"]."</td>";
                            $lista.= "<td>".$datoOBS["rutContratista"]."</td>";
                            $lista.= "<td>".$datoOBS["nombreContratista"]."</td>";
                            $lista.= "<td>".$datoOBS["centroCosto"]."</td>";
                            $lista.= "<td>".$datoOBS["rutSubContratista"]."</td>";
                            $lista.= "<td>".$datoOBS["nombreSubContratista"]."</td>";
                            $lista.= "<td>".$datoOBS["perido"]."</td>";
                            $lista.= "<td>".$datoOBS["estadoCertificacion"]."</td>";
                            $lista.= "<td>".$datoOBS["fechaCertificado"]."</td>";
                            $lista.= "<td>".$datosObs["rutTrabajasor"]."</td>";
                            $lista.= "<td>".$datosObs["nombreTrabajador"]."</td>";
                            $lista.= "<td>".$datosObs["documento"]."</td>";
                            $lista.= "<td>".$datosObs["observacion"]."</td>"; 
                            $lista.= "<td>".$datosObs["observacionMotivo"]."</td>";    
                            $lista.= "</tr>";      
                        }    
                    }             
                }

                $lista.="</tbody></table>";


                $fechaRep =date('d/m/Y');
                Excel::create('ReporteObservaciones'.$fechaRep, function($excel) use($lista,$obsCargaMasiva,$obsFiniquitos,$obsExtranjero,$obsLibroRemu,$obsLaboral,$obsLicenciaMed,$obsTributarios,$obsDeclaracion,$obsPrevisional) {
                            
                    $excel->sheet('observaciones', function($sheet) use($lista) {  
                        $sheet->loadView('reporteObser.excelData',compact('lista'));
                    });


                    $excel->sheet('Graficos', function($sheet) use($obsCargaMasiva,$obsFiniquitos,$obsExtranjero,$obsLibroRemu,$obsLaboral,$obsLicenciaMed,$obsTributarios,$obsDeclaracion,$obsPrevisional) { 

                        // $sheet->fromArray([['a1', 'b1'], ['a2', 'b2']]); 
                         $sheet->fromArray(
                            [
                                    [   'Observaciones', //a
                                        'Carga Masiva', //b
                                        'Finiquitos', //c
                                        'extranjeros', //d
                                        'Libro de Remuneraciones',  //e
                                        'Laboral',//f
                                        'Licencias Medicas', //g
                                        'Tributarios', //h
                                        'Declaración Jurada', // i 
                                        'previsionales', // j
                                        
                                    ],
                                    [
                                        '',
                                        $obsCargaMasiva,
                                        $obsFiniquitos,
                                        $obsExtranjero,
                                        $obsLibroRemu,
                                        $obsLaboral,
                                        $obsLicenciaMed,
                                        $obsTributarios,
                                        $obsDeclaracion,
                                        $obsPrevisional

                                    ]
                            ],null, 'A2', false, false);

                            $nombreHoja= 'Graficos';

                        /// globales
                            $labels = [
                                new  \PHPExcel_Chart_DataSeriesValues('String', $nombreHoja. '!$A$2', null, 1), // 2011
                            ];

                            $categories = [
                                new \PHPExcel_Chart_DataSeriesValues('String', $nombreHoja. '!$B$2:$J$2', null, 7), // Q1 to Q4
                            ];

                             $values = [
                                new \PHPExcel_Chart_DataSeriesValues('Number', $nombreHoja. '!$B$3:$J$3', null, 7),
                            ];

                            

                           $series = new \PHPExcel_Chart_DataSeries(
                                \PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
                                \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
                                range(0, count($values)-1),           // plotOrder
                                $labels,                              // plotLabel
                                $categories,                               // plotCategory
                                $values                              // plotValues
                            );

                            $series->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_COL);

                            $plotArea = new \PHPExcel_Chart_PlotArea(NULL, array($series));
                            $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                            $title = new \PHPExcel_Chart_Title('Observaciones');
                            $yAxisLabel = new \PHPExcel_Chart_Title('Cantidad de observaciones');
                            //    Create the chart
                            $chart = new \PHPExcel_Chart(
                            'chart1',       // name
                            $title,         // title
                            $legend,        // legend
                            $plotArea,      // plotArea
                            true,           // plotVisibleOnly
                            0,              // displayBlanksAs
                            NULL,           // xAxisLabel
                            $yAxisLabel     // yAxisLabel
                            );

                            //    Set the position where the chart should appear in the worksheet
                            $chart->setTopLeftPosition('A10');
                            $chart->setBottomRightPosition('H25');
                            $sheet->addChart($chart);


                    });
                                    
                })->export('xlsx');
            }

        }else{
            $registros = 0;
            return view('reporteObser.index',compact('EmpresasP','periodos','datosUsuarios','etiquetasEstados','valoresEstados','certificacion','periodosT','principalesTexto','usuarioAqua','usuarioABBChile','usuarioNOKactivo','registros','usuarioClaroChile'));
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
