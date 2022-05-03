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
use App\Ftreinta;
use Illuminate\Http\Request;


class ReporteCertificacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */



    public function porContratista($id){
        
        return Contratista::distinct()->where('mainCompanyRut','=',$id)->orderBy('name', 'ASC')->get(['name','rut']);
        
    }

    public function porCentroCosto($contratista,$principal,$peridoInicio,$peridoFinal,$fechaSeleccion){

        if($peridoInicio!= 0 AND $peridoFinal != 0){
             return Contratista::distinct()->where('mainCompanyRut','=',$principal)
             ->where('rut','=',$contratista)
             ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
             ->orderBy('center', 'ASC')->get(['center','id']);

        }

        if($fechaSeleccion != 0){
            $fechas = $porciones = explode("_", $fechaSeleccion);
            $fecha1 = $fechas[0];
            $fecha2 = $fechas[1];
            $fechasDesde = strtotime($fecha1);
            $fechasHasta = strtotime($fecha2);
             return Contratista::distinct()->where('mainCompanyRut','=',$principal)
             ->where('rut','=',$contratista)
             ->whereBetween('certificateDate', [$fechasDesde,$fechasHasta])
             ->orderBy('center', 'ASC')->get(['center','id']);

        }
        
        return Contratista::distinct()->where('mainCompanyRut','=',$id)->orderBy('center', 'ASC')->get(['center']);
        
    }

    public function index()
    {

        $idUsuario = session('user_id');
        if($idUsuario ==  ""){
            return view('sesion.index');
        }
        $usuarioAqua = session('user_aqua');
        $certificacion = session('certificacion');
        $usuarioABBChile= session('user_ABB');
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
        return view('reporteCertificacion.index',compact('EmpresasP','periodos','datosUsuarios','etiquetasEstados','valoresEstados','certificacion','periodosT','principalesTexto','usuarioAqua','usuarioABBChile','usuarioNOKactivo'));
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
        $tipoReporte = $input["tipoReporte"];
        if($tipoReporte == 1){
            $reporte = "Detallado";
        }else{
            $reporte = "Estado por periodo";
        }
        $f30 = $input["f30"];
        if($f30 == 1){
            $f30texto = "Si";
        }else{
            $f30texto = "No";
        }
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
                ->orderBy('id', 'ASC')
                ->orderBy('periodId', 'DESC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType','servicioId','classserv','adminContrato'])->toArray();

            }
            if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista != 0){

            $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
            ->whereIn('rut',$rutcontratistasR)
            ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
            ->orderBy('id', 'ASC')
            ->orderBy('periodId', 'DESC')
            ->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType','servicioId','classserv','adminContrato'])->toArray();

            }if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista == 0 AND $centroCosto == 0){

                $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                 ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
                 ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType','servicioId','classserv','adminContrato'])->toArray();

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
            $empresasContratista = Contratista::select('id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType','servicioId','classserv','adminContrato')->whereIn('mainCompanyRut',$rutprincipalR)
            ->whereIn('rut',$rutcontratistasR)
            ->whereBetween('certificateDate', [$fechasDesde,$fechasHasta])
            ->where('id',$centroCosto)
            ->orderBy('id', 'ASC')
            ->orderBy('periodId', 'DESC')->get()->toArray();

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
            ->orderBy('id', 'ASC')
            ->orderBy('periodId', 'DESC')
            ->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType','servicioId','classserv','adminContrato'])->toArray();

            }
            if($fechaSeleccion != 0  AND $countContratista == 0 AND $centroCosto == 0){
            
            $fechas = $porciones = explode("_", $fechaSeleccion);
            $fecha1 = $fechas[0];
            $fecha2 = $fechas[1];
            $periodosT = $fecha1 ."-".$fecha2;
            $fechasDesde = strtotime ( '+4 hour' ,strtotime($fecha1));
            //sumo 1 día
            $fechasHasta = strtotime ( '+4 hour' ,strtotime($fecha2));
            
            $empresasContratista = Contratista::whereIn('mainCompanyRut',$rutprincipalR)
            ->whereBetween('certificateDate', [$fechasDesde,$fechasHasta])
            ->orderBy('id', 'ASC')
            ->orderBy('periodId', 'DESC')
            ->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType','servicioId','classserv','adminContrato'])->toArray();

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

        function periodoTexto($idPerido){

            $periodo = DB::table('Period')
            ->join('Month', 'Month.id', '=', 'Period.monthId')
            ->where(['Period.id' => $idPerido])
            ->select('Period.year','Month.name')
            ->get();

            return $periodo[0]->name."-".$periodo[0]->year;
        }

        function tipoEmpresa($idtipoEmpresa){

            $tipoEmpresa = tipoEmpresa::where('id',$idtipoEmpresa)->get(['name'])->toArray();
            return $tipoEmpresa[0]['name'];
        }

        function tipoServicio($idServicio){

            $tipoServicio = tipoServicio::where('id',$idServicio)->get(['name'])->toArray();
            if(!empty($tipoServicio)) {
                return $tipoServicio[0]['name'];
            }else{
                $tipoServicio="";
            }
        }

        function categoriaServicioTexto($idCategoria){

            $catServicio = categoriaServicio::where('id',$idCategoria)->get(['class_name'])->toArray();
            if(!empty($catServicio)) {
                return $catServicio[0]['class_name'];
            }else{
                $catServicio="";
            }
        }

        function direccionDes($idDireccion){

            $direccion = direccion::where('id',$idDireccion)->get(['dir_name'])->toArray();
            if(!empty($direccion)) {
                return $direccion[0]['dir_name'];
            }else{
                $direccion="";
            }   
        }

        function gerenciaDes($idGerencia){

            $gerencia = gerencia::where('id',$idGerencia)->get(['ger_name'])->toArray();
            return $gerencia[0]['ger_name'];
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


       /* 1 => "Ingresado",
        2 => "Solicitado",
        3 => "Aprobado",
        4 => "No Aprobado",
        5 => "Certificado",
        6 => "Documentado",
        7 => "Histórico",
        8 => "Completo",
        9 => "En Proceso",
        10 => "No Conforme",
        11 => "Inactivo",*/

        function categoriaServicio($idCategoria){
            switch((int)$idCategoria)
           {
             case 0: return $catservname = "No definido"; break;
             case 1: return $catservname = "Exclusivo"; break;
             case 2: return $catservname = "Mixta"; break;
             case 3: return $catservname = "No Aplica Personal"; break;
           }
        }

        function tipoDePago($idtipoPago){
            switch((int)$idtipoPago)
           {
             case 0: return $tipoPago = "Pago Directo"; break;
             case 1: return $tipoPago = "Pago via Webpay/Deposito/Transferencia"; break;
           }
        }

        function oneTrabajadores($idPerido, $mainCompanyRut, $rut, $center){
    
            $periodo = Periodo::where(['id' => $idPerido])
            ->get(['year','monthId'])->toArray();

            $agno = $periodo[0]["year"];
            $mes = $periodo[0]["monthId"];
            $ultimoDia = date("t",(mktime(0,0,0,$mes,1,$agno))); //Captura el último día del mes

            $datoTrabajadores = TrabajadorVerificacion::where('mainCompanyRut',$mainCompanyRut)->
                                                                 where('companyRut',$rut)->
                                                                 where('periodId',$idPerido)->
                                                                 where('companyCenter',$center)->
                                                                 get(['beginDate'])->toArray();

    
           $fechaReturn="";
           $fechaFin=$agno."-".$mes."-".$ultimoDia;
           $sumaDias=0;

            foreach($datoTrabajadores AS $worker)
            {
              if(!is_null($worker["beginDate"]))
              {
                    $fechaReturn = date('Y-m-d',$worker["beginDate"]);
                    $inicio  = date_create($fechaReturn);
                    $fin  = date_create($fechaFin);
                    $diff=date_diff($inicio,$fin);
                    $sumaDias = $sumaDias + $diff->days;
              }
              
            }

            return $sumaDias;
        }


       

        if($tipoReporte == 1){

            $peridoTexto = "";
            $numeroTrabajadores = 0;
            $estadoCerficacionTexto = "";
            $idUsuarioDoc =0;
            $idUsuarioCar = 0;
            $motivoInactividad = "";
            $numeroTrabajadoresTotales = 0;
            $ciclo = "";
            $nombreCertificador = "";
            $montoTotalObservado= "N/A";
            $numeroCertificado = 0;
            $numeroCertificado= "";
            $numeroEmpleadosContratadosPeriodo= 0;
            $numeroEmpleadosDesviculadosPeriodo = 0;
            $totalDotacionPeriodo = 0;
            $tipoServicio = "";
            $categoriaServiciotexto = "";
            $direccion = "";
            $gerencia = "";
            $totalHaberes=0; 
            $categoriaServicio ="";
            $diasTrabajadores = 0;
            $costoDesmov = 0;
            $riesgoLaboral = 0; 
            $promedioServicios=0;
            $observaciones ="";
            $rutContratista = "";
            $nombreContratista = ""; 
            $rutSubContratista = "";
            $nombreSubContratista = "";
            $cantidadIngresados = 0;
            $cantidadSolicitado = 0;
            $cantidadAprobado = 0;
            $cantidadNoAprobado = 0;
            $cantidadCertificado = 0;
            $cantidadDocumentado = 0;
            $cantidadHistorico = 0;
            $cantidadCompleto = 0;
            $cantidadEnProceso = 0;
            $cantidadNoConforme = 0;
            $cantidadInactivo = 0;
            $fechaCertificiacion = "";

            foreach ($empresasContratista as $contratista) {

                if($contratista['certificateState'] == 1){
                    $cantidadIngresados+=1; 

                }elseif($contratista['certificateState'] == 2){
                    $cantidadSolicitado+=1; 

                }elseif($contratista['certificateState'] == 3){
                    $cantidadAprobado+=1; 

                }elseif($contratista['certificateState'] == 4){
                    $cantidadNoAprobado+=1; 

                }elseif($contratista['certificateState'] == 5){
                    $cantidadCertificado+=1; 

                }elseif($contratista['certificateState'] == 6){
                    $cantidadDocumentado+=1; 

                }elseif($contratista['certificateState'] == 7){
                    $cantidadHistorico+=1; 

                }elseif($contratista['certificateState'] == 8){
                    $cantidadCompleto+=1; 

                }elseif($contratista['certificateState'] == 9){
                    $cantidadEnProceso+=1; 

                }elseif($contratista['certificateState'] == 10){
                    $cantidadNoConforme+=1; 

                }elseif($contratista['certificateState'] == 11){
                    $cantidadInactivo+=1;

                } 

                if($contratista['adminContrato']!=""){
                    $adminContrato = $contratista['adminContrato'];  
                }else{
                $adminContrato = "No Aplica";
                }    

                
                $peridoTex = periodoTexto($contratista['periodId']);
                $tipoEmpresa = tipoEmpresa($contratista['companyTypeId']);

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
                if($contratista['servicioId']!=0){
                    $tipoServicio = tipoServicio($contratista['servicioId']);
                }

                if($contratista['classserv']!=0){
                    $categoriaServiciotexto = categoriaServicioTexto($contratista['classserv']);
                }

                $estadoCerficacionTexto = estadoCerficacionTexto($contratista['certificateState']);
                if($contratista['direccion']!=0){
                    $direccion = direccionDes($contratista['direccion']);
                }
                if($contratista['gerencia']!=0){
                    $gerencia = gerenciaDes($contratista['gerencia']);
                }

                if($contratista['companycatid']!=0){
                    $categoriaServicio = categoriaServicio($contratista['companycatid']);
                }

                if($contratista['contratoPaymentType']!=""){
                    $tipoDePago = tipoDePago($contratista['contratoPaymentType']);
                }else{
                    $tipoDePago = "N/A";
                }

                $fechaCertificiacion=date('d/m/Y', $contratista['certificateDate']);
                    if($contratista['certificateState'] != 1){

                        $datosSolictud = Solicitud::distinct()->where('companyId',$contratista['id'])->orderby('serial','DESC')->take(1)->get(['workersNumber','workerstotales','serial'])->toArray();
                        
                        if(!empty($datosSolictud)){
                            $numeroTrabajadoresTotales = $datosSolictud[0]['workerstotales'];
                            $numeroTrabajadores = $datosSolictud[0]['workersNumber'];
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
                        //monto total observado
                        if($contratista['certificateState'] == 10 or $contratista['certificateState'] == 5 ){
                            $datosCertificado = Certificado::where('companyId',$contratista['id'])->orderby('serial','DESC')->take(1)->get(['remunerationsMount','compensationsMount','serviceYearsMount','number','rotationInWorkers','rotationOutLawWorkers','rotationOutLawWorkers','workersNumber','serial'])->toArray();
                           
                            if($contratista['certificateState'] == 10){
                                $montoTotalObservado = $datosCertificado[0]['remunerationsMount'] + $datosCertificado[0]['compensationsMount'] + $datosCertificado[0]['serviceYearsMount'];
                            }
                            if($contratista['certificateState'] == 5){
                                $montoTotalObservado = "N/A";
                            }
                             
                            $numeroCertificado = $datosCertificado[0]['number']."-".$datosCertificado[0]['serial'];
                            $numeroEmpleadosContratadosPeriodo = $datosCertificado[0]['rotationInWorkers'];
                            $numeroEmpleadosDesviculadosPeriodo = $datosCertificado[0]['rotationOutLawWorkers']+$datosCertificado[0]['rotationOutLawWorkers'];
                            $totalDotacionPeriodo = $datosCertificado[0]['workersNumber'];
                        }else{
                            $montoTotalObservado = "N/A";
                            $numeroCertificado = "";
                            $numeroEmpleadosContratadosPeriodo = 0;
                            $numeroEmpleadosDesviculadosPeriodo = 0;
                            $totalDotacionPeriodo =0;
                            

                        }

                        if($contratista['certificateState'] == 4 or $contratista['certificateState'] == 5 or $contratista['certificateState'] == 8 or $contratista['certificateState'] == 9 or $contratista['certificateState'] == 10){
                            
                            $totalHaberes = TrabajadorVerificacion::where('mainCompanyRut',$contratista['mainCompanyRut'])->
                                                                 where('companyRut',$contratista['rut'])->
                                                                 where('periodId',$contratista['periodId'])->
                                                                 where('companyCenter',$contratista['center'])->
                                                                 sum('totalIncome');

                            $datosCuadratura = Cuadratura::where('companyId',$contratista['id'])->orderby('id','DESC')->take(1)->get(['taxablePayListMount','observations','id'])->toArray();

                            
                            if(!empty($datosCuadratura)){
                                $montoImponible = $datosCuadratura[0]['taxablePayListMount'];
                            }else{
                               $montoImponible = 0;
                            }


                           $diasTrabajadores = oneTrabajadores($contratista['periodId'], $contratista['mainCompanyRut'], $contratista['rut'], $contratista['center']);



                        }else{
                           $totalHaberes = 0; 
                           $montoImponible = 0;
                        }

                        if($contratista['certificateState'] == 11 or $contratista['certificateState'] == 7){
                            $motivoInactividad = $contratista['motivo_inactivo'];
                        }else{
                            $motivoInactividad = "";
                        }

                        if($ciclo=="Primero" and ($contratista['certificateState'] == 5 or $contratista['certificateState'] == 8 or $contratista['certificateState'] == 2 or $contratista['certificateState'] == 10)){
                            if(!empty($datosCuadratura)){
                               
                                $observaciones = $datosCuadratura[0]['observations'];
                            }else{
                              
                               $observaciones =  $contratista['certificateObservations'];
                            }
                        }

                        if($ciclo=="Segundo" and ($contratista['certificateState'] == 5 or $contratista['certificateState'] == 6 or $contratista['certificateState'] == 8 or $contratista['certificateState'] == 3 or $contratista['certificateState'] == 4 or $contratista['certificateState'] == 5 or $contratista['certificateState'] == 11 or $contratista['certificateState'] == 10)){
                            if(!empty($datosCuadratura)){
                               
                                $observaciones = $datosCuadratura[0]['observations'];
                            }else{
                              
                               $observaciones =  $contratista['certificateObservations'];
                            }
                        }
                              
                    }else{
                        $totalHaberes = 0; 
                        $numeroTrabajadores = 0;
                        $montoTotalObservado = "N/A";
                        $numeroCertificado = "";
                        $motivoInactividad = "";
                        $numeroEmpleadosContratadosPeriodo = 0;
                        $numeroEmpleadosDesviculadosPeriodo = 0;
                        $totalDotacionPeriodo =0;
                        $montoImponible = 0;
                        $numeroTrabajadoresTotales = $contratista['workersNumber'];
                        $observaciones =  $contratista['certificateObservations'];
                        $numeroCertificado = "";
                        $ciclo = "";  

                    }  

                    if($numeroTrabajadores > 0){
                        $promedioServicios = ($diasTrabajadores/$numeroTrabajadores)/360;
                        $haberesPromedio = ($totalHaberes/$numeroTrabajadores);

                        if($haberesPromedio > 0){

                            $costoDesmov = (($haberesPromedio * $promedioServicios) + $haberesPromedio + (($haberesPromedio / 30) * 21) + $haberesPromedio) * $numeroTrabajadores;


                            $riesgoLaboral = (($haberesPromedio * $promedioServicios) + $haberesPromedio + (($haberesPromedio / 30) * 21)) * $numeroTrabajadores;
            
                        }else{
                           $costo_desmov = 0;
                           $riesgoLaboral = 0;

                        }

                
                    }else{
                        $promedioServicios=0;
                        $haberesPromedio = 0;
                        $costoDesmov = 0;
                        $riesgoLaboral = 0; 

                    }

                    if($montoTotalObservado!="N/A"){
                       $montoTotalObservado = number_format($montoTotalObservado,'2',',','.');
                    }

                    if($f30 == 1){
                        $datosf30 = Ftreinta::where('form_status', 1)->
                        where('form_periodId',$contratista['periodId'])->
                        where('form_mainCompany',$contratista['mainCompanyRut'])->
                        where('form_company',$rutContratista2)->
                        where('form_cco',$contratista['center'])->
                        orderby('id','DESC')->take(1)->
                        get(['id','form_number_worker','form_hired_period','form_unlink_period','form_staff_period','form_amount_tax','form_nopayed','form_payedobs','form_out_law_qty','form_out_law','form_contribution_unpayed_wk_qty','form_contribution_unpayed_qty','form_inspection_certificate'])->toArray();

                        if(!empty($datosf30[0]['id'])){
                            $F30NumeroTrabajadores = $datosf30[0]['form_number_worker'];
                            $F30ConEmpleadosPeriodo = $datosf30[0]['form_hired_period'];
                            $F30DesPeriodo = $datosf30[0]['form_unlink_period'];
                            $F30TotalDotacion = $datosf30[0]['form_number_worker'];
                            $F30MontoImponible = $datosf30[0]['form_amount_tax'];
                            $F30CantidadTrabajadorsSinPagoRenumeracion = $datosf30[0]['form_nopayed'];
                            $F30PagoRenumeracionesObservaciones = $datosf30[0]['form_payedobs'];
                            $F30TrabajadoresSinPagoIndemnizacion = $datosf30[0]['form_out_law_qty'];
                            $F30PagoIndemnizacionesSinPagoPrevio = $datosf30[0]['form_out_law'];
                            $F30TrabajadoresSinImposiciones = $datosf30[0]['form_contribution_unpayed_wk_qty'];
                            $F30TrabajadoresIndemnizacionesSinImposiciones = $datosf30[0]['form_contribution_unpayed_qty'];
                            $F30Certificado = $datosf30[0]['form_inspection_certificate'];
                        }else{
                            $F30NumeroTrabajadores = 0;
                            $F30ConEmpleadosPeriodo = 0;
                            $F30DesPeriodo = 0;
                            $F30TotalDotacion = 0;
                            $F30MontoImponible = 0;
                            $F30CantidadTrabajadorsSinPagoRenumeracion = 0;
                            $F30PagoRenumeracionesObservaciones = 0;
                            $F30TrabajadoresSinPagoIndemnizacion = 0;
                            $F30PagoIndemnizacionesSinPagoPrevio = 0;
                            $F30TrabajadoresSinImposiciones = 0;
                            $F30TrabajadoresIndemnizacionesSinImposiciones = 0;
                            $F30Certificado = 0;

                        }

                    }else{
                        $F30NumeroTrabajadores = 0;
                        $F30ConEmpleadosPeriodo = 0;
                        $F30DesPeriodo = 0;
                        $F30TotalDotacion = 0;
                        $F30MontoImponible = 0;
                        $F30CantidadTrabajadorsSinPagoRenumeracion = 0;
                        $F30PagoRenumeracionesObservaciones = 0;
                        $F30TrabajadoresSinPagoIndemnizacion = 0;
                        $F30PagoIndemnizacionesSinPagoPrevio = 0;
                        $F30TrabajadoresSinImposiciones = 0;
                        $F30TrabajadoresIndemnizacionesSinImposiciones = 0;
                        $F30Certificado = 0;
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
                    $Datoscertificacion['tipoEmpresa'] = ucwords(mb_strtolower($tipoEmpresa,'UTF-8'));
                    $Datoscertificacion['actividad'] = ucwords(mb_strtolower($contratista['activity'],'UTF-8')); 
                    $Datoscertificacion['numeroTrabajadoresCertificar'] = $numeroTrabajadores;     
                    $Datoscertificacion['numeroTrabajadoresTotales'] = $numeroTrabajadoresTotales;  
                    $Datoscertificacion['estadoCertificacion'] = ucwords(mb_strtolower($estadoCerficacionTexto,'UTF-8')); 
                    $Datoscertificacion['fechaCertificado'] =  $fechaCertificiacion;
                    $Datoscertificacion['motivoinactivo'] = ucwords(mb_strtolower($motivoInactividad,'UTF-8'));   
                    $Datoscertificacion['ciclo'] = ucwords(mb_strtolower($ciclo,'UTF-8'));  
                    $Datoscertificacion['certificador'] = ucwords(mb_strtolower($nombreCertificador,'UTF-8'));
                    $Datoscertificacion['numeroCertificado'] = $numeroCertificado;
                    $Datoscertificacion['montoTotalObservado'] = $montoTotalObservado;
                    $Datoscertificacion['numeroEmpleadosContratadosPeriodo'] = $numeroEmpleadosContratadosPeriodo;
                    $Datoscertificacion['numeroEmpleadosDesviculadosPeriodo'] = $numeroEmpleadosDesviculadosPeriodo;
                    $Datoscertificacion['totalDotacionPeriodo'] = $totalDotacionPeriodo;
                    $Datoscertificacion['direccion'] = ucwords(mb_strtolower($direccion,'UTF-8'));
                    $Datoscertificacion['gerencia'] = ucwords(mb_strtolower($gerencia,'UTF-8'));
                    $Datoscertificacion['tipoServicio'] = ucwords(mb_strtolower($tipoServicio,'UTF-8'));
                    $Datoscertificacion['catServicio'] = ucwords(mb_strtolower($categoriaServiciotexto,'UTF-8'));
                    $Datoscertificacion['totalHabares'] = number_format($totalHaberes,'2',',','.');
                    $Datoscertificacion['montoImponible'] = number_format($montoImponible,'2',',','.');
                    $Datoscertificacion['promedioServicios'] = number_format($promedioServicios,'2','.', '');
                    $Datoscertificacion['haberesPromedio'] = number_format($haberesPromedio,'2',',','.');
                    $Datoscertificacion['diasTrabajadores'] = number_format($diasTrabajadores,'2',',','.');
                    $Datoscertificacion['costoDesmov'] = number_format($costoDesmov,'2',',','.');
                    $Datoscertificacion['riesgoLaboral'] = number_format($riesgoLaboral,'2',',','.');
                    $Datoscertificacion['categoriaServicio'] = ucwords(mb_strtolower($categoriaServicio,'UTF-8'));
                    $Datoscertificacion['observaciones'] = mb_strtolower($observaciones,'UTF-8');
                    $Datoscertificacion['tipoDePago'] = ucwords(mb_strtolower($tipoDePago,'UTF-8'));
                    $Datoscertificacion['adminContrato'] = ucwords(mb_strtolower($adminContrato,'UTF-8'));
                    if(!empty($datosf30[0]['id'])){

                        $Datoscertificacion['F30NumeroTrabajadores'] = $F30NumeroTrabajadores;
                        $Datoscertificacion['F30ConEmpleadosPeriodo'] = $F30ConEmpleadosPeriodo;
                        $Datoscertificacion['F30DesPeriodo'] = $F30DesPeriodo;
                        $Datoscertificacion['F30TotalDotacion'] = $F30TotalDotacion;
                        $Datoscertificacion['F30MontoImponible'] = $F30MontoImponible;
                        $Datoscertificacion['F30CantidadTrabajadorsSinPagoRenumeracion'] = $F30CantidadTrabajadorsSinPagoRenumeracion;
                        $Datoscertificacion['F30PagoRenumeracionesObservaciones'] = $F30PagoRenumeracionesObservaciones;
                        $Datoscertificacion['F30TrabajadoresSinPagoIndemnizacion'] = $F30TrabajadoresSinPagoIndemnizacion;
                        $Datoscertificacion['F30PagoIndemnizacionesSinPagoPrevio'] = $F30PagoIndemnizacionesSinPagoPrevio;
                        $Datoscertificacion['F30TrabajadoresSinImposiciones'] = $F30TrabajadoresSinImposiciones;
                        $Datoscertificacion['F30TrabajadoresIndemnizacionesSinImposiciones'] = $F30TrabajadoresIndemnizacionesSinImposiciones;
                        $Datoscertificacion['F30Certificado'] = $F30Certificado;
                        
                    }
                    

                    $reporteCertificacion[] = $Datoscertificacion;
           }

           if(!empty($reporteCertificacion)){
                $cantidadDatos = count($Datoscertificacion);
            }else{
                $cantidadDatos = 0; 
            }
            
            $etiquetasEstados = array("Ingresado","Solicitado", "Aprobado", "No Aprobado", "Documentado", "completo", "En Proceso", "Certificado", "No Conforme", "Histórico", "Inactivo");


             $valoresEstados = array($cantidadIngresados,$cantidadSolicitado, $cantidadAprobado ,$cantidadNoAprobado, $cantidadDocumentado,$cantidadCompleto,$cantidadEnProceso, $cantidadCertificado, $cantidadNoConforme, $cantidadHistorico, $cantidadInactivo);

            if(!empty($reporteCertificacion)){ 
                if($f30 == 1){
                    $colspant = 12;
                }else{
                   $colspant = 9; 
                }

                $lista='<table border="2">
                    <thead>
                    <tr>
                      <th style="background-color:#e3e3e3" colspan='.$colspant.'>tipo Busquedad</th>
                      <th style="background-color:#e3e3e3" colspan='.$colspant.'>Periodo o Fecha de certificación</th>
                      <th style="background-color:#e3e3e3" colspan='.$colspant.'>Empresa Principal</th>
                      <th style="background-color:#e3e3e3" colspan='.$colspant.'>Reporte</th>
                      <th colspan="1"></th>
                    </tr>
                    <tr>
                      <td colspan='.$colspant.'>'.$textoTipoB.'</td>
                      <td colspan='.$colspant.'>'.$periodosT.'</td>
                      <td colspan='.$colspant.'>'.$principalesTexto.'</td>
                      <td colspan='.$colspant.'>'.$reporte.'</td>
                    <th colspan="1"></th>
                    </tr>
                    <tr>
                      <th style="background-color:#e3e3e3">id</th>
                      <th style="background-color:#e3e3e3">RUT Principal</th>
                      <th style="background-color:#e3e3e3">Empresa Principal</th>
                      <th style="background-color:#e3e3e3">RUT Contratista</th>
                      <th style="background-color:#e3e3e3">Empresa Contratista</th>
                      <th style="background-color:#e3e3e3">Centro de Costo</th>
                      <th style="background-color:#e3e3e3">RUT Sub Contratista</th>
                      <th style="background-color:#e3e3e3">Sub Contratista</th>
                      <th style="background-color:#e3e3e3">Periodo</th>
                      <th style="background-color:#e3e3e3">Tipo</th>
                      <th style="background-color:#e3e3e3">Actividad</th>
                      <th style="background-color:#e3e3e3">N° Trabajadores</th>
                      <th style="background-color:#e3e3e3">Total Trabajadores</th>
                      <th style="background-color:#e3e3e3">Estado Certificación</th>
                      <th style="background-color:#e3e3e3">Fecha Estado Certificación</th>
                      <th style="background-color:#e3e3e3">N° Certificado</th>
                      <th style="background-color:#e3e3e3">Ciclo</th>
                      <th style="background-color:#e3e3e3">Certificador</th>
                      <th style="background-color:#e3e3e3">Motivo Inactividad</th>
                      <th style="background-color:#e3e3e3">Monto Total Observado Certificación</th>
                      <th style="background-color:#e3e3e3">N° Empleados Contratados en el Período</th>
                      <th style="background-color:#e3e3e3">N° Empleados Desvinculados en Período</th>
                      <th style="background-color:#e3e3e3">Total Dotación Período</th>
                      <th style="background-color:#e3e3e3">Dirección</th>
                      <th style="background-color:#e3e3e3">Gerencia</th>
                      <th style="background-color:#e3e3e3">Tipo de Servicio</th>
                      <th style="background-color:#e3e3e3">Categoria de Servicio</th>
                      <th style="background-color:#e3e3e3">Total Haberes</th>
                      <th style="background-color:#e3e3e3">Monto Imponible</th>
                      <th style="background-color:#e3e3e3">Promedio años de Servicio</th>
                      <th style="background-color:#e3e3e3">Total Haberes Promedio</th>
                      <th style="background-color:#e3e3e3">Costo de desmovilización real</th>
                      <th style="background-color:#e3e3e3">Riesgo laboral Boleta Garantía</th>
                      <th style="background-color:#e3e3e3">Clasificiación del Servicio</th>
                      <th style="background-color:#e3e3e3">Observaciones</th>
                      <th style="background-color:#e3e3e3">Tipo de Pago</th>
                      <th style="background-color:#e3e3e3">Administrador del contrato</th>';
                       if($f30 == 1){
                        $lista.='<th style="background-color:#e3e3e3">F30-1: N° Trabajadores</th>
                                <th style="background-color:#e3e3e3">F30-1: Empleados contratados en periodo</th>
                                <th style="background-color:#e3e3e3">F30-1: Empleados desvinculados en periodo</th>
                                <th style="background-color:#e3e3e3">F30-1: Total Dotacion</th>
                                <th style="background-color:#e3e3e3">F30-1: Monto Imponible</th>
                                <th style="background-color:#e3e3e3">F30-1: N° de Trabajadores Sin Pago Renumeraciones</th>
                                <th style="background-color:#e3e3e3">F30-1: Pago Renumeraciones de Trabajadores con observaciones</th>
                                <th style="background-color:#e3e3e3">F30-1: N° de Trabajadores Sin Pago Indemnizaciones Aviso Previo</th> 
                                <th style="background-color:#e3e3e3">F30-1: Pago de Indemnizaciones de Trabajadores Sin Pago Aviso Previo</th> 
                                <th style="background-color:#e3e3e3">F30-1: N° de Trabajadores Sin Pago Imposiciones</th>  
                                <th style="background-color:#e3e3e3">F30-1: N° de Indemnizaciones de Trabajadores Sin Pago de Imposiciones</th> 
                                <th style="background-color:#e3e3e3">F30-1: N° Certificado Inspeccion</th>'; 
                        }
                         
                    $lista.='</tr>
                    </thead>
                    <tbody>';
                    foreach ($reporteCertificacion as $rcertificacion) {

                        $lista.= "<tr>";
                        $lista.= "<td>".$rcertificacion["id"]."</td>";
                        $lista.= "<td>".$rcertificacion["rutPrincipal"]."</td>";
                        $lista.= "<td>".$rcertificacion["nombrePrincipal"]."</td>";
                        $lista.= "<td>".$rcertificacion["rutContratista"]."</td>";
                        $lista.= "<td>".$rcertificacion["nombreContratista"]."</td>";
                        $lista.= "<td>".$rcertificacion["centroCosto"]."</td>";
                        $lista.= "<td>".$rcertificacion["rutSubContratista"]."</td>";
                        $lista.= "<td>".$rcertificacion["nombreSubContratista"]."</td>";
                        $lista.= "<td>".$rcertificacion["perido"]."</td>";
                        $lista.= "<td>".$rcertificacion["tipoEmpresa"]."</td>";
                        $lista.= "<td>".$rcertificacion["actividad"]."</td>";
                        $lista.= "<td>".$rcertificacion["numeroTrabajadoresCertificar"]."</td>";
                        $lista.= "<td>".$rcertificacion["numeroTrabajadoresTotales"]."</td>";
                        $lista.= "<td>".$rcertificacion["estadoCertificacion"]."</td>";
                        $lista.= "<td>".$rcertificacion["fechaCertificado"]."</td>";
                        $lista.= "<td>".$rcertificacion["numeroCertificado"]."</td>";
                        $lista.= "<td>".$rcertificacion["ciclo"]."</td>";
                        $lista.= "<td>".$rcertificacion["certificador"]."</td>";
                        $lista.= "<td>".$rcertificacion["motivoinactivo"]."</td>";
                        $lista.= "<td>".$rcertificacion["montoTotalObservado"]."</td>";
                        $lista.= "<td>".$rcertificacion["numeroEmpleadosContratadosPeriodo"]."</td>";
                        $lista.= "<td>".$rcertificacion["numeroEmpleadosDesviculadosPeriodo"]."</td>";
                        $lista.= "<td>".$rcertificacion["totalDotacionPeriodo"]."</td>";
                        $lista.= "<td>".$rcertificacion["direccion"]."</td>";
                        $lista.= "<td>".$rcertificacion["gerencia"]."</td>";
                        $lista.= "<td>".$rcertificacion["tipoServicio"]."</td>";
                        $lista.= "<td>".$rcertificacion["catServicio"]."</td>";
                        $lista.= "<td>".$rcertificacion["totalHabares"]."</td>";
                        $lista.= "<td>".$rcertificacion["montoImponible"]."</td>";
                        $lista.= "<td>".$rcertificacion["promedioServicios"]."</td>";
                        $lista.= "<td>".$rcertificacion["haberesPromedio"]."</td>";
                        $lista.= "<td>".$rcertificacion["costoDesmov"]."</td>";
                        $lista.= "<td>".$rcertificacion["riesgoLaboral"]."</td>";
                        $lista.= "<td>".$rcertificacion["categoriaServicio"]."</td>";
                        $lista.= "<td>".$rcertificacion["observaciones"]."</td>";
                        $lista.= "<td>".$rcertificacion["tipoDePago"]."</td>";
                        $lista.= "<td>".$rcertificacion["adminContrato"]."</td>";
                        if($f30 == 1){
                            if(!empty($rcertificacion['F30NumeroTrabajadores'])){
                                $lista.= "<td>".$rcertificacion["F30NumeroTrabajadores"]."</td>";
                                $lista.= "<td>".$rcertificacion["F30ConEmpleadosPeriodo"]."</td>";
                                $lista.= "<td>".$rcertificacion["F30DesPeriodo"]."</td>";
                                $lista.= "<td>".$rcertificacion["F30TotalDotacion"]."</td>";
                                $lista.= "<td>".$rcertificacion["F30MontoImponible"]."</td>";
                                $lista.= "<td>".$rcertificacion["F30CantidadTrabajadorsSinPagoRenumeracion"]."</td>";
                                $lista.= "<td>".$rcertificacion["F30PagoRenumeracionesObservaciones"]."</td>";
                                $lista.= "<td>".$rcertificacion["F30TrabajadoresSinPagoIndemnizacion"]."</td>";
                                $lista.= "<td>".$rcertificacion["F30PagoIndemnizacionesSinPagoPrevio"]."</td>";
                                $lista.= "<td>".$rcertificacion["F30TrabajadoresSinImposiciones"]."</td>";
                                $lista.= "<td>".$rcertificacion["F30TrabajadoresIndemnizacionesSinImposiciones"]."</td>";
                                $lista.= "<td>".$rcertificacion["F30Certificado"]."</td>";
                                
                            }else{
                                $lista.= "<td> </td>";
                                $lista.= "<td> </td>";
                                $lista.= "<td> </td>";
                                $lista.= "<td> </td>";
                                $lista.= "<td> </td>";
                                $lista.= "<td> </td>";
                                $lista.= "<td> </td>";
                                $lista.= "<td> </td>";
                                $lista.= "<td> </td>";
                                $lista.= "<td> </td>";
                                $lista.= "<td> </td>";
                                $lista.= "<td> </td>";

                            }
                        }
                        $lista.= "</tr>";
                    }

                    $lista.= "</table>";
            }

            Excel::create('Reporte Certificación con Graficos', function($excel) use ($lista) {
                $excel->sheet('Lista General', function($sheet) use($lista) {    
                    $sheet->loadView('reporteCertificacion.excelCertificacion',compact('lista'));
                });
            })->export('xlsx');   
           
        }

        if($tipoReporte == 2){


            $peridoTexto = "";
            $rutContratista = "";
            $nombreContratista = ""; 
            $rutSubContratista = "";
            $nombreSubContratista = "";

            $listaTitulos="<thead>
                <tr>
                  <th>RUT Principal</th>
                  <th>Empresa Principal</th>
                  <th>RUT Contratista</th>
                  <th>Empresa Contratista</th>
                  <th>Centro de Costo</th>
                  <th>RUT Sub Contratista</th>
                  <th>Empresa Sub Contratista</th>";
            $listaCuerpo ="";
            $x = 0;
            $contratistaPeriodo = count($empresasContratista);
            for ($i=$peridoInicio; $i <= $peridoFinal; $i++) { 
                $x ++;
                $peridoTex = periodoTexto($i);
                $listaTitulos.="<th>".$peridoTex."</th>";                
            }

            $listaTitulos.="</tr></thead>";

            $empresasContratistaU = super_unique($empresasContratista,'rut');
        
            foreach ($empresasContratistaU as $contratista) {

                if($contratista['companyTypeId'] == 1){
                        $rutContratista = $contratista['rut']."-".$contratista['dv']; 
                        $nombreContratista = ucwords(mb_strtolower($contratista['name'],'UTF-8')); 
                        $rutSubContratista = "";
                        $nombreSubContratista = "";
                }
                if($contratista['companyTypeId'] == 2){
                            $rutContratista = $contratista['subcontratistaRut']."-".$contratista['subcontratistaDv'];
                            $nombreContratista =  ucwords(mb_strtolower($contratista['subcontratistaName'],'UTF-8'));  
                            $rutSubContratista = $contratista['rut']."-".$contratista['dv']; 
                            $nombreSubContratista = ucwords(mb_strtolower($contratista['name'],'UTF-8')); 
                }     
                $listaCuerpo.= "<tr>";
                $listaCuerpo.= "<td>".formatRut($contratista['mainCompanyRut'])."</td>";
                $listaCuerpo.= "<td>".ucwords(mb_strtolower($contratista['mainCompanyName'],'UTF-8')) ."</td>";
                $listaCuerpo.= "<td>".$rutContratista ."</td>";
                $listaCuerpo.= "<td>".$nombreContratista ."</td>";
                $listaCuerpo.= "<td>".ucwords(mb_strtolower($contratista['center'],'UTF-8'))."</td>";
                $listaCuerpo.= "<td>".$nombreSubContratista ."</td>";
                $listaCuerpo.= "<td>".$rutSubContratista ."</td>";

                $periodosActivos = Contratista::where('mainCompanyRut',$contratista['mainCompanyRut'])
                ->where('rut',$contratista['rut'])
                ->where('center',$contratista['center'])
                ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
                ->orderBy('periodId', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv'])->toArray();

                    $cuentaP = count($periodosActivos);
                    
                    foreach ($periodosActivos as $contratistap) {
                   
                        
                        $estadoCerficacionTexto = estadoCerficacionTexto($contratistap['certificateState']);
                        $estadoCerficacionTextoP = ucwords(mb_strtolower($estadoCerficacionTexto,'UTF-8'));
                        
                        for ($i=$peridoInicio; $i <= $contratistap['periodId']; $i++) { 
                            if($i == $contratistap['periodId']){
                                if($estadoCerficacionTextoP=="Certificado"){
                                    $style = "bgcolor= '#00a65a'";
                                }else{
                                    $style = "bgcolor= '#f0ad4e'";
                                }
                           
                                $listaCuerpo.= "<td ".$style.">".$estadoCerficacionTextoP ."</td>";
                            }

                        }

                    }
                    if($x != $cuentaP){
                            $tdfaltan = $x-$cuentaP;

                            for ($y=0; $y < $tdfaltan; $y++) { 
                                 $listaCuerpo.= "<td>s/n</td>";
                            }
                        } 
                $listaCuerpo.= "</tr>";    
            }
            $listaCuerpo.= "</table>";
                
            $etiquetasEstados = 0;
            $valoresEstados = 0;
            return view('reporteCertificacion.index',compact('EmpresasP','periodos','datosUsuarios','listaTitulos','listaCuerpo','contratistaPeriodo','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','etiquetasEstados','valoresEstados','periodosT','principalesTexto'));
        }

        if($tipoReporte == 7){

            $peridoTexto = "";
            $estadoCerficacionTexto = "";
            $observaciones ="";
            $rutContratista = "";
            $nombreContratista = ""; 
            $rutSubContratista = "";
            $nombreSubContratista = "";
            $fechaCertificiacion = "";
            $tipoEmpresa = "";
            $cantidadIngresados = 0;
            $cantidadSolicitado = 0;
            $cantidadAprobado = 0;
            $cantidadNoAprobado = 0;
            $cantidadCertificado = 0;
            $cantidadDocumentado = 0;
            $cantidadHistorico = 0;
            $cantidadCompleto = 0;
            $cantidadEnProceso = 0;
            $cantidadNoConforme = 0;
            $cantidadInactivo = 0;
            $numeroCertificado = "";
            $ciclo = "";
            $nombreCertificador = "";
            $numeroTrabajadores = 0;

            foreach ($empresasContratista as $contratista) {

                if($contratista['certificateState'] == 1){
                    $cantidadIngresados+=1; 

                }elseif($contratista['certificateState'] == 2){
                    $cantidadSolicitado+=1; 

                }elseif($contratista['certificateState'] == 3){
                    $cantidadAprobado+=1; 

                }elseif($contratista['certificateState'] == 4){
                    $cantidadNoAprobado+=1; 

                }elseif($contratista['certificateState'] == 5){
                    $cantidadCertificado+=1; 

                }elseif($contratista['certificateState'] == 6){
                    $cantidadDocumentado+=1; 

                }elseif($contratista['certificateState'] == 7){
                    $cantidadHistorico+=1; 

                }elseif($contratista['certificateState'] == 8){
                    $cantidadCompleto+=1; 

                }elseif($contratista['certificateState'] == 9){
                    $cantidadEnProceso+=1; 

                }elseif($contratista['certificateState'] == 10){
                    $cantidadNoConforme+=1; 

                }elseif($contratista['certificateState'] == 11){
                    $cantidadInactivo+=1;
                } 

                   
                $peridoTex = periodoTexto($contratista['periodId']);
                $tipoEmpresa = tipoEmpresa($contratista['companyTypeId']);
               
                if($contratista['companyTypeId'] == 1){
                    $rutContratista = $contratista['rut']."-".$contratista['dv']; 
                    $nombreContratista = ucwords(mb_strtolower($contratista['name'],'UTF-8')); 
                    $rutSubContratista = "";
                    $nombreSubContratista = "";
                }
                if($contratista['companyTypeId'] == 2){
                    $rutContratista = $contratista['subcontratistaRut']."-".$contratista['subcontratistaDv'];
                    $nombreContratista =  ucwords(mb_strtolower($contratista['subcontratistaName'],'UTF-8'));  
                    $rutSubContratista = $contratista['rut']."-".$contratista['dv']; 
                    $nombreSubContratista = ucwords(mb_strtolower($contratista['name'],'UTF-8')); 
                }
                
                $estadoCerficacionTexto = estadoCerficacionTexto($contratista['certificateState']);
                
                $fechaCertificiacion=date('d/m/Y', $contratista['certificateDate']);
                    if($contratista['certificateState'] != 1){

                        $datosSolictud = Solicitud::distinct()->where('companyId',$contratista['id'])->orderby('serial','DESC')->take(1)->get(['workersNumber','workerstotales','serial'])->toArray();
                        
                        if(!empty($datosSolictud)){
                            $numeroTrabajadores = $datosSolictud[0]['workersNumber'];
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
                                    $historialCertificador = CertificateHistory::where('companyId',$contratista['id'])->where('certificateState',array(9))->orderby('id','DESC')->take(1)->get(['userName','id'])->toArray();
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


                        //monto total observado
                        if($contratista['certificateState'] == 10 or $contratista['certificateState'] == 5 ){
                            $datosCertificado = Certificado::where('companyId',$contratista['id'])->orderby('serial','DESC')->take(1)->get(['remunerationsMount','compensationsMount','serviceYearsMount','number','rotationInWorkers','rotationOutLawWorkers','rotationOutLawWorkers','workersNumber','serial'])->toArray(); 
                            $numeroCertificado = $datosCertificado[0]['number']."-".$datosCertificado[0]['serial'];
                        }else{
                           
                            $numeroCertificado = "";
                        }

                        if($contratista['certificateState'] == 5 or $contratista['certificateState'] == 6 or $contratista['certificateState'] == 7  or $contratista['certificateState'] == 8 or $contratista['certificateState'] == 3 or $contratista['certificateState'] == 4 or $contratista['certificateState'] == 5 or $contratista['certificateState'] == 11 or $contratista['certificateState'] == 10){

                            

                            $datosCuadratura = Cuadratura::where('companyId',$contratista['id'])->orderby('id','DESC')->take(1)->get(['taxablePayListMount','observations','id'])->toArray();
                            if(!empty($datosCuadratura)){
                               
                                $observaciones = $datosCuadratura[0]['observations'];
                            }else{
                              
                               $observaciones =  $contratista['certificateObservations'];
                            }

                            if($contratista['certificateState'] == 7){

                                $observaciones = "empresa de datos históricos";

                            } 

                            if($contratista['certificateState'] == 11){
                                 $observaciones = "Empresa Inactiva";
                            }    
                        }
                                          
                    }else{
                       
                        $observaciones =  $contratista['certificateObservations'];
                        $numeroTrabajadores = 0;  
                        $numeroCertificado = "";
                        $ciclo = "";  
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
                    $Datoscertificacion['tipoEmpresa'] = ucwords(mb_strtolower($tipoEmpresa,'UTF-8'));
                    $Datoscertificacion['numeroTrabajadoresCertificar'] = $numeroTrabajadores;   
                    $Datoscertificacion['estadoCertificacion'] = ucwords(mb_strtolower($estadoCerficacionTexto,'UTF-8'));
                    $Datoscertificacion['numeroCertificado'] = $numeroCertificado;
                    $Datoscertificacion['fechaCertificado'] = $fechaCertificiacion;
                    $Datoscertificacion['ciclo'] = $ciclo;
                    $Datoscertificacion['certificador'] = $nombreCertificador;
                    $Datoscertificacion['observaciones'] = mb_strtolower($observaciones,'UTF-8');
                    

                    $reporteCertificacion[] = $Datoscertificacion;
            }

            if(!empty($reporteCertificacion)){
                $cantidadDatos = count($Datoscertificacion);
            }else{
                $cantidadDatos = 0; 
            }
            
            $etiquetasEstados = array("Ingresado","Solicitado", "Aprobado", "No Aprobado", "Documentado", "completo", "En Proceso", "Certificado", "No Conforme", "Histórico", "Inactivo");


             $valoresEstados = array($cantidadIngresados,$cantidadSolicitado, $cantidadAprobado ,$cantidadNoAprobado, $cantidadDocumentado,$cantidadCompleto,$cantidadEnProceso, $cantidadCertificado, $cantidadNoConforme, $cantidadHistorico, $cantidadInactivo);

            if(!empty($reporteCertificacion)){ 

                $lista='<table border="2">
                    <thead>
                    <tr>
                      <th style="background-color:#e3e3e3" colspan="3">tipo Busquedad</th>
                      <th style="background-color:#e3e3e3" colspan="3">Periodo o Fecha de certificación</th>
                      <th style="background-color:#e3e3e3" colspan="4">Empresa Principal</th>
                      <th style="background-color:#e3e3e3" colspan="3">Reporte</th>
                    </tr>
                    <tr>
                      <td colspan="3">'.$textoTipoB.'</td>
                      <td colspan="3">'.$periodosT.'</td>
                      <td colspan="4">'.$principalesTexto.'</td>
                      <td colspan="3">'.$reporte.'</td>
                    </tr>
                    <tr>
                      <th style="background-color:#e3e3e3">id</th>
                      <th style="background-color:#e3e3e3">RUT Principal</th>
                      <th style="background-color:#e3e3e3">Empresa Principal</th>
                      <th style="background-color:#e3e3e3">RUT Contratista</th>
                      <th style="background-color:#e3e3e3">Empresa Contratista</th>
                      <th style="background-color:#e3e3e3">Centro de Costo</th>
                      <th style="background-color:#e3e3e3">RUT Sub Contratista</th>
                      <th style="background-color:#e3e3e3">Sub Contratista</th>
                      <th style="background-color:#e3e3e3">Periodo</th>
                      <th style="background-color:#e3e3e3">N° Trabajadores Certificados</th>
                      <th style="background-color:#e3e3e3">Estado</th>
                      <th style="background-color:#e3e3e3">Fecha Estado Certificación</th>
                      <th style="background-color:#e3e3e3">N° Certificado</th>
                      <th style="background-color:#e3e3e3">Ciclo</th>
                      <th style="background-color:#e3e3e3">Certificador</th>
                      <th style="background-color:#e3e3e3">Observaciones</th>
                      
                    </tr>
                    </thead>
                    <tbody>';
                    foreach ($reporteCertificacion as $rcertificacion) {

                        $lista.= "<tr>";
                        $lista.= "<td>".$rcertificacion["id"]."</td>";
                        $lista.= "<td>".$rcertificacion["rutPrincipal"]."</td>";
                        $lista.= "<td>".$rcertificacion["nombrePrincipal"]."</td>";
                        $lista.= "<td>".$rcertificacion["rutContratista"]."</td>";
                        $lista.= "<td>".$rcertificacion["nombreContratista"]."</td>";
                        $lista.= "<td>".$rcertificacion["centroCosto"]."</td>";
                        $lista.= "<td>".$rcertificacion["rutSubContratista"]."</td>";
                        $lista.= "<td>".$rcertificacion["nombreSubContratista"]."</td>";
                        $lista.= "<td>".$rcertificacion["perido"]."</td>";
                        $lista.= "<td>".$rcertificacion["numeroTrabajadoresCertificar"]."</td>";
                        $lista.= "<td>".$rcertificacion["estadoCertificacion"]."</td>";
                        $lista.= "<td>".$rcertificacion["fechaCertificado"]."</td>";
                        $lista.= "<td>".$rcertificacion["numeroCertificado"]."</td>";
                        $lista.= "<td>".$rcertificacion["ciclo"]."</td>";
                        $lista.= "<td>".$rcertificacion["certificador"]."</td>";
                        $lista.= "<td>".$rcertificacion["observaciones"]."</td>";
                        
                        $lista.= "</tr>";
                    }

                    $lista.= "</table>";
            }

            Excel::create('Reporte Certificación con Graficos', function($excel) use ($lista) {
                $excel->sheet('Lista General', function($sheet) use($lista) {    
                    $sheet->loadView('reporteCertificacion.excelCertificacion',compact('lista'));
                });
            })->export('xlsx');   

            //return view('reporteCertificacion.index',compact('EmpresasP','periodos','datosUsuarios','lista','cantidadDatos','valoresEstados','etiquetasEstados','certificacion','textoTipoB','reporte','f30texto','principalesTexto','periodosT','usuarioAqua','usuarioABBChile','usuarioNOKactivo'));
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
