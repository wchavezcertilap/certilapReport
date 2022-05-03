<?php

namespace App\Http\Controllers;
use DB;
use Excel;
use App\DatosUsuarioLogin;
use App\UsuarioContratista;
use App\UsuarioPrincipal;
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
use App\CompanyWebPay;
use App\depositoSolictud;
use App\CompanyWebDeposit;
use App\OrdenCompra;
use App\OrdenComContratista;

use Illuminate\Http\Request;

class ReporteFactCert extends Controller
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
        return view('factCertificacion.index',compact('EmpresasP','periodos','datosUsuarios','etiquetasEstados','valoresEstados','certificacion','periodosT','principalesTexto','usuarioAqua','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile'));
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

        function periodoTexto($idPerido){

            $periodo = DB::table('Period')
            ->join('Month', 'Month.id', '=', 'Period.monthId')
            ->where(['Period.id' => $idPerido])
            ->select('Period.year','Month.name')
            ->get();

            return $periodo[0]->name."-".$periodo[0]->year;
        }
        function estadoFactura($idaestadoFac){
             switch ((int)$idaestadoFac) {
                case 1:
                    return $estadoFactura ="Emitida";
                    break;
                case 2:
                    return $estadoFactura ="Pagada";
                    break;
                case 3:
                    return $estadoFactura ="Pendiente";
                    break;
                }
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

        function tipoDePago($idtipoPago){
            switch((int)$idtipoPago)
           {
             case 0: return $tipoPago = "Pago Directo"; break;
             case 1: return $tipoPago = "Pago via Webpay/Deposito/Transferencia"; break;
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
        $tipoBsuqueda = $input["tipoBsuqueda"];
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

            $empresaPrincipal = $input["empresaPrincipal"];
            if($empresaPrincipal[0]==1){
                $principales = 0;
            }else{
                foreach ($empresaPrincipal as $value) {
                $rutprincipalR[] = $value;
                }  
                $principales = count($rutprincipalR); 
            }            
         
            if(!empty($input["empresaContratista"])){
                $empresaContratista = $input["empresaContratista"];

                foreach ($empresaContratista as $value2) {
                    $rutcontratistasR[] = $value2;
                }

                $countContratista =count($rutcontratistasR); 
            }else{
                $countContratista =0; 
            }   
     
            $centroCosto = $input["centroCosto"];
            $estadoCertificacion = $input["estadoCertificacion"];
            if($estadoCertificacion[0]==100){
                $conEstadoCert = 0;
            }else{
                foreach ($estadoCertificacion as $value) {
                $estadoDeCertificacion[] = $value;
                }  
                $conEstadoCert = count($estadoDeCertificacion);
            }
      

            $tipoPago = $input["tipoPago"];
            if($tipoPago[0]==100){
                $conTipoPago = 0;
            }else{
                foreach ($tipoPago as $value) {
                    $tipoDePago[] = $value;
                } 
                $conTipoPago = count($tipoDePago); 
            }
        
              

           /* $fechaPago = $input["fechaPago"];
            $fechas = $porciones = explode("_", $fechaPago);
            $fecha1 = $fechas[0];
            $fecha2 = $fechas[1];
            $periodosT = $fecha1 ."-".$fecha2;
            $fechasDesdePago = strtotime ( '+4 hour' ,strtotime($fecha1));
            //sumo 1 día
            $fechasHastaPago = strtotime ( '+4 hour' ,strtotime($fecha2));
            echo "pi".$peridoInicio."<br>";
            echo "pf".$peridoFinal."<br>";
            echo "contr".$countContratista."<br>";
            echo "centro".$centroCosto."<br>";
             echo "prin".$principales."<br>";
             echo "tp".$conTipoPago."<br>";
             echo "estado".$conEstadoCert."<br>";*/
            if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista != 0 AND $centroCosto != 0 AND $principales != 0 AND $conTipoPago != 0 AND $conEstadoCert != 0){
                //echo "1"."<br>";
                $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                ->whereIn('rut',$rutcontratistasR)
                ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
                ->where('id',$centroCosto)
                ->whereIn('certificateState',$estadoDeCertificacion)
                ->whereIn('contratoPaymentType',$tipoDePago)
                //->whereBetween('facturaPayDate', [$fechasDesdePago,$fechasHastaPago])
                ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','certificateObservations','contratoPaymentType','factura','facturaState','facturaDate','facturaPayDate','facturaTotal'])->toArray();

            }
            if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista != 0  AND $principales != 0 AND $conTipoPago != 0 AND $conEstadoCert != 0 AND $centroCosto == 0){
               // echo "2"."<br>";
                $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                ->whereIn('rut',$rutcontratistasR)
                ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
                ->whereIn('certificateState',$estadoDeCertificacion)
                ->whereIn('contratoPaymentType',$tipoDePago)
               // ->whereBetween('facturaPayDate', [$fechasDesdePago,$fechasHastaPago])
                ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','certificateObservations','contratoPaymentType','factura','facturaState','facturaDate','facturaPayDate','facturaTotal'])->toArray();

            }if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista != 0  AND $principales != 0 AND $conTipoPago != 0 AND $conEstadoCert == 0 AND $centroCosto == 0){
               // echo "3"."<br>";
                $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                ->whereIn('rut',$rutcontratistasR)
                ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
                ->whereIn('contratoPaymentType',$tipoDePago)
               // ->whereBetween('facturaPayDate', [$fechasDesdePago,$fechasHastaPago])
                ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','certificateObservations','contratoPaymentType','factura','facturaState','facturaDate','facturaPayDate','facturaTotal'])->toArray();

            }if($peridoInicio != 0 AND $peridoFinal != 0  AND $principales != 0 AND $countContratista == 0 AND $conEstadoCert == 0 AND $conTipoPago == 0 AND $centroCosto == 0){
               // echo "4"."<br>";
                $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                 ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
               //  ->whereBetween('facturaPayDate', [$fechasDesdePago,$fechasHastaPago])
                 ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','certificateObservations','contratoPaymentType','factura','facturaState','facturaDate','facturaPayDate','facturaTotal'])->toArray();

            }if($peridoInicio != 0 AND $peridoFinal != 0  AND $principales != 0 AND $countContratista == 0 AND $conEstadoCert == 0 AND $conTipoPago != 0 AND $centroCosto == 0){
                //echo "5"."<br>";
                $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                 ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
                 ->whereIn('contratoPaymentType',$tipoDePago)
               //  ->whereBetween('facturaPayDate', [$fechasDesdePago,$fechasHastaPago])
                 ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','certificateObservations','contratoPaymentType','factura','facturaState','facturaDate','facturaPayDate','facturaTotal'])->toArray();

            }if($peridoInicio != 0 AND $peridoFinal != 0 AND $principales == 0 AND $countContratista == 0 AND $conEstadoCert == 0 AND $conTipoPago == 0 ){

               //echo "6"."<br>";
                $empresasContratista = Contratista::distinct()
                 ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
                // ->whereBetween('facturaPayDate', [$fechasDesdePago,$fechasHastaPago])
                 ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','certificateObservations','contratoPaymentType','factura','facturaState','facturaDate','facturaPayDate','facturaTotal'])->toArray();

            }if($peridoInicio != 0 AND $peridoFinal != 0 AND $conEstadoCert != 0 AND $principales == 0 AND $countContratista == 0  AND $conTipoPago == 0 ){

                //echo "7"."<br>";
                $empresasContratista = Contratista::distinct()
                ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
                ->whereIn('certificateState',$estadoDeCertificacion)
                // ->whereBetween('facturaPayDate', [$fechasDesdePago,$fechasHastaPago])
                 ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','certificateObservations','contratoPaymentType','factura','facturaState','facturaDate','facturaPayDate','facturaTotal'])->toArray();

            }if($peridoInicio != 0 AND $peridoFinal != 0 AND $conEstadoCert != 0 AND $principales == 0 AND $countContratista == 0  AND $conTipoPago != 0 ){

                //echo "8"."<br>";
                $empresasContratista = Contratista::distinct()
                ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
                ->whereIn('certificateState',$estadoDeCertificacion)
                ->whereIn('contratoPaymentType',$tipoDePago)
                // ->whereBetween('facturaPayDate', [$fechasDesdePago,$fechasHastaPago])
                 ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','certificateObservations','contratoPaymentType','factura','facturaState','facturaDate','facturaPayDate','facturaTotal'])->toArray();

            }if($peridoInicio != 0 AND $peridoFinal != 0 AND $conEstadoCert != 0 AND $principales != 0 AND $countContratista == 0  AND $conTipoPago == 0 AND $centroCosto == 0){

              //echo "9"."<br>";
                $empresasContratista = Contratista::distinct()
                ->whereIn('mainCompanyRut',$rutprincipalR)
                ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
                ->whereIn('certificateState',$estadoDeCertificacion)
                //->whereBetween('facturaPayDate', [$fechasDesdePago,$fechasHastaPago])
                ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','certificateObservations','contratoPaymentType','factura','facturaState','facturaDate','facturaPayDate','facturaTotal'])->toArray();

            }if($peridoInicio != 0 AND $peridoFinal != 0 AND $conEstadoCert != 0 AND $principales != 0 AND $countContratista == 0  AND $conTipoPago == 0 AND $centroCosto == 0){

               //echo "10"."<br>";
                $empresasContratista = Contratista::distinct()
                ->whereIn('mainCompanyRut',$rutprincipalR)
                ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
                ->whereIn('certificateState',$estadoDeCertificacion)
                //->whereBetween('facturaPayDate', [$fechasDesdePago,$fechasHastaPago])
                ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','certificateObservations','contratoPaymentType','factura','facturaState','facturaDate','facturaPayDate','facturaTotal'])->toArray();

            }if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista != 0 AND $centroCosto == 0 AND $principales != 0 AND $conTipoPago == 0 AND $conEstadoCert == 0){
                //echo "11"."<br>";
                $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                ->whereIn('rut',$rutcontratistasR)
                ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
                ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','certificateObservations','contratoPaymentType','factura','facturaState','facturaDate','facturaPayDate','facturaTotal'])->toArray();

            }if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista == 0 AND $centroCosto == 0 AND $principales != 0 AND $conTipoPago != 0 AND $conEstadoCert != 0){
               // echo "12"."<br>";
                $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
                ->whereIn('certificateState',$estadoDeCertificacion)
                ->whereIn('contratoPaymentType',$tipoDePago)
                ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','certificateObservations','contratoPaymentType','factura','facturaState','facturaDate','facturaPayDate','facturaTotal'])->toArray();

            }if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista != 0 AND $centroCosto == 0 AND $principales != 0 AND $conTipoPago == 0 AND $conEstadoCert != 0){
               //echo "13"."<br>";
                $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                ->whereIn('rut',$rutcontratistasR)
                ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
                ->whereIn('certificateState',$estadoDeCertificacion)
                ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','certificateObservations','contratoPaymentType','factura','facturaState','facturaDate','facturaPayDate','facturaTotal'])->toArray();

            }
            $nombreCertificador="";
           
            if (isset($empresasContratista[0])) {
                    
                foreach ($empresasContratista as $contratista) {
                    unset($Datoscertificacion);    
                    if($contratista['certificateState'] != 1){

                            $datosSolictud = Solicitud::distinct()->where('companyId',$contratista['id'])->orderby('serial','DESC')->take(1)->get(['id','workersNumber','workerstotales','serial','paymentExecType','webpayStatus','depositStatus','depositApDate'])->toArray();

                            $trabajadorCarga = TrabajadorVerificacion::where('companyRut',$contratista['rut'])
                            ->where('companyCenter',$contratista['center'])
                            ->where('mainCompanyRut',$contratista['mainCompanyRut'])
                            ->where('periodId',$contratista['periodId'])->count();}

                            if(!empty($datosSolictud)){
                                $numeroTrabajadoresTotales = $datosSolictud[0]['workerstotales'];
                                $numeroTrabajadores = $datosSolictud[0]['workersNumber'];
                                $idSolicitud = $datosSolictud[0]['id'];
                            }else{
                                $numeroTrabajadoresTotales = 0;
                                $numeroTrabajadores = 0;
                                $idSolicitud = 0;
                                $trabajadorCarga = 0;
                            }
                    $peridoTex = periodoTexto($contratista['periodId']);
                    $estadoCerficacionTexto = estadoCerficacionTexto($contratista['certificateState']);
                    if($contratista['certificateDate'] > 0){
                        $fechaCertificiacion=date('d/m/Y', $contratista['certificateDate']);
                    }else{
                        $fechaCertificiacion="";
                    }
                    
                    if($contratista['facturaPayDate'] > 0){
                        $fechaFactura = date('d/m/Y', $contratista['facturaPayDate']);
                    }else{
                        $fechaFactura="";
                    }
                    if($contratista['facturaDate'] > 0){
                        $fechaPago = date('d/m/Y', $contratista['facturaDate']);
                    }else{
                        $fechaPago ="";
                    }
                     // segundo ciclo obtiene datos del certificador
                    $idUsuarioDoc = DocumentoRechazdo::where('id_company',$contratista['id'])->where('doc_reenviado',1)->orderby('fecha','DESC')->take(1)->get(['id_usuario','fecha'])->toArray();
                    $idUsuarioCar = EstadoCargaMasiva::where('id_company',$contratista['id'])->where('cargaerror',1)->orderby('fecha','DESC')->take(1)->get(['id_usuario','fecha','cargaerror','id_company'])->toArray();
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
                    if($contratista['certificateState'] == 10 or $contratista['certificateState'] == 5 ){
                        $datosCertificado = Certificado::where('companyId',$contratista['id'])->orderby('serial','DESC')->take(1)->get(['number','rotationInWorkers','rotationOutLawWorkers','rotationOutLawWorkers','workersNumber','serial'])->toArray();
                    
                        $numeroCertificado = $datosCertificado[0]['number']."-".$datosCertificado[0]['serial'];
                                
                    }else{
                                
                        $numeroCertificado = "";
                               
                    }
                    if($contratista['contratoPaymentType']!=""){
                        $tipoDePago = tipoDePago($contratista['contratoPaymentType']);
                    }else{
                        $tipoDePago = "N/A";
                    }
                    if($contratista['companyTypeId'] == 1){
                        $rutContratista = $contratista['rut']."-".$contratista['dv']; 
                        $rutContratistaSDV = $contratista['rut'];
                        $nombreContratista = ucwords(mb_strtolower($contratista['name'],'UTF-8')); 
                        $rutSubContratista = "";
                        $nombreSubContratista = "";

                    }
                    if($contratista['companyTypeId'] == 2){
                        $rutContratista = $contratista['subcontratistaRut']."-".$contratista['subcontratistaDv'];
                        $rutContratistaSDV = $contratista['subcontratistaRut'];
                        $nombreContratista =  ucwords(mb_strtolower($contratista['subcontratistaName'],'UTF-8'));  
                        $rutSubContratista = $contratista['rut']."-".$contratista['dv']; 
                        $nombreSubContratista = ucwords(mb_strtolower($contratista['name'],'UTF-8')); 

                    }

                    if(!empty($idSolicitud) AND $idSolicitud > 0){
                    //// tabla web pay //////
                        $wepPay = CompanyWebPay::where('refid',$idSolicitud)->where('req_tbk_id_session',$contratista['id'])->take(1)->get(['req_tbk_monto','req_tbk_fecha_contable','req_tbk_fecha_transaccion'])->toArray();
                        if(!empty($wepPay[0]['req_tbk_monto'])){
                            $montoWebPay = $wepPay[0]['req_tbk_monto'];
                            $fechacontableWebPay =$wepPay[0]['req_tbk_fecha_contable'];
                            $fechatransaccionWebPay = $wepPay[0]['req_tbk_fecha_transaccion'];

                        }else{
                            $montoWebPay = "";
                            $fechacontableWebPay = "";
                            $fechatransaccionWebPay = "";
                        }

                        /// tabla deposito ///
                        $deposito = depositoSolictud::where('request_id',$idSolicitud)->take(1)->get(['request_id','dep_amount','dep_comment','dep_transdate','dep_type'])->toArray();
                        if(!empty($deposito[0]['request_id'])){
                            $montoDeposito = $deposito[0]['dep_amount'];
                            $comentarioDeposito =  mb_strtolower($deposito[0]['dep_comment']);
                            $fechaDeposito = date('d/m/Y',$deposito[0]['dep_transdate']);
                            if((int)$deposito[0]['dep_type']){
                                $tipoDeposito ="Transferencia"; 
                            }else{
                               $tipoDeposito ="Deposito"; 
                            }                     
                       

                        }else{
                            $montoDeposito = "";
                            $comentarioDeposito =  "";
                            $fechaDeposito = "";
                            $tipoDeposito = "";
                        }

                        /// tabla deposito subido a solicitud ///
                        $depositoWeb = CompanyWebDeposit::where('refid',$idSolicitud)->where('companyId',$contratista['id'])->take(1)
                        ->get(['refid','dep_crtdat'])->toArray();
                        if(!empty($depositoWeb[0]['refid'])){
                            $fechaSubidaDeposito = date('d/m/Y',$depositoWeb[0]['dep_crtdat']);
                        }else{
                           $fechaSubidaDeposito = "";
                        }
                        $estadoFactura = estadoFactura($contratista['facturaState']);
                    }else{
                        $montoWebPay = "";
                        $fechacontableWebPay = "";
                        $fechatransaccionWebPay = "";
                        $montoDeposito = "";
                        $comentarioDeposito =  "";
                        $fechaDeposito = "";
                        $tipoDeposito = "";
                        $fechaSubidaDeposito = "";
                        $estadoFactura = "";
                    }
                    /// orden compra //
                    $ordenCompra = DB::table('xt_oc_groups')
                                    ->join('xt_oc_groups_pos', 'xt_oc_groups_pos.ocg_id', '=', 'xt_oc_groups.id')
                                    ->where(['xt_oc_groups_pos.company_id' =>$contratista['id']])
                                    ->where(['xt_oc_groups.ocg_mrut' => $contratista['mainCompanyRut']])
                                    ->get(['xt_oc_groups.id','xt_oc_groups.ocg_crtdat','xt_oc_groups.ocg_upddat'])->toArray();

                    if(!empty($ordenCompra)){
                        $numOC =  $ordenCompra[0]->id;
                        $fechaCreOC =  date('d/m/Y',$ordenCompra[0]->ocg_crtdat);
                        $fechaActOC =  date('d/m/Y',$ordenCompra[0]->ocg_upddat);
                       
                    }else{

                        $ordenCompra = DB::table('xt_oc_groups')
                                    ->join('xt_oc_groups_pos', 'xt_oc_groups_pos.ocg_id', '=', 'xt_oc_groups.id')
                                    ->where(['xt_oc_groups_pos.company_id' =>$contratista['id']])
                                    ->take(1)
                                    ->get(['xt_oc_groups.id','xt_oc_groups.ocg_crtdat','xt_oc_groups.ocg_upddat'])->toArray();
                        if(!empty($ordenCompra)){
                            $numOC =  $ordenCompra[0]->id;
                            $fechaCreOC =  date('d/m/Y',$ordenCompra[0]->ocg_crtdat);
                            $fechaActOC =  date('d/m/Y',$ordenCompra[0]->ocg_upddat);
                       
                        }else{
                            $numOC = "";    
                            $fechaCreOC =  "";
                            $fechaActOC =  "";
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
                    $Datoscertificacion['numeroTrabajadoresCertificar'] = $numeroTrabajadores;     
                    $Datoscertificacion['numeroTrabajadoresTotales'] = $numeroTrabajadoresTotales;  
                    $Datoscertificacion['numeroTrabajadoresCarga'] = $trabajadorCarga;  
                    $Datoscertificacion['estadoCertificacion'] = ucwords(mb_strtolower($estadoCerficacionTexto,'UTF-8')); 
                    $Datoscertificacion['fechaCertificado'] =  $fechaCertificiacion;
                    $Datoscertificacion['ciclo'] = ucwords(mb_strtolower($ciclo,'UTF-8'));  
                    $Datoscertificacion['certificador'] = ucwords(mb_strtolower($nombreCertificador,'UTF-8'));
                    $Datoscertificacion['numeroCertificado'] = $numeroCertificado;
                    $Datoscertificacion['numOC'] = $numOC; 
                    $Datoscertificacion['FechaOC'] = $fechaCreOC;
                    $Datoscertificacion['FechaOCAct'] = $fechaActOC;
                    $Datoscertificacion['tipoDePago'] = ucwords(mb_strtolower($tipoDePago,'UTF-8'));
                    $Datoscertificacion['fechaFactura'] = $fechaFactura;
                    $Datoscertificacion['fechaPago'] = $fechaPago;
                    $Datoscertificacion['nunFactura'] = $contratista['factura'];
                    $Datoscertificacion['estatusFactura'] = $estadoFactura;
                    $Datoscertificacion['montoFactura'] = $contratista['facturaTotal'];
                    $Datoscertificacion['montoWebPay'] = $montoWebPay;
                    $Datoscertificacion['fechacontableWebPay'] = $fechacontableWebPay;
                    $Datoscertificacion['fechatransaccionWebPay'] = $fechatransaccionWebPay;
                    $Datoscertificacion['montoDeposito'] = $montoDeposito;
                    $Datoscertificacion['comentarioDeposito'] = $comentarioDeposito;
                    $Datoscertificacion['fechaSubidaDeposito'] = $fechaSubidaDeposito;
                    $Datoscertificacion['fechaDeposito'] = $fechaDeposito;
                    $Datoscertificacion['tipoDeposito'] = $tipoDeposito;
                    $reporteCertificacion[] = $Datoscertificacion;
                   
                }

                //excel ///
                if(!empty($reporteCertificacion)){

                    Excel::create('Reporte Certificación-Facturación', function($excel) use ($reporteCertificacion) {

                        $excel->sheet('Lista General', function($sheet) use($reporteCertificacion) {    
                            $sheet->loadView('excel.certificacionFactura',compact('reporteCertificacion'));
                        });
                    })->export('xls'); 
                }
            }else{
                $cantidadDatos = 0;
                return view('factCertificacion.index',compact('EmpresasP','periodos','datosUsuarios','etiquetasEstados','valoresEstados','certificacion','periodosT','principalesTexto','usuarioAqua','usuarioABBChile','usuarioNOKactivo','cantidadDatos','usuarioClaroChile'));
            }

           
        }if($tipoBsuqueda == 2){
             
            
            $empresaPrincipal = $input["empresaPrincipal"];
            if($empresaPrincipal[0]==1){
                $principales = 0;
            }else{
                foreach ($empresaPrincipal as $value) {
                $rutprincipalR[] = $value;
                }  
                $principales = count($rutprincipalR); 
            }            
            
            if(!empty($input["empresaContratista"])){
                $empresaContratista = $input["empresaContratista"];

                foreach ($empresaContratista as $value2) {
                    $rutcontratistasR[] = $value2;
                }

                $countContratista =count($rutcontratistasR); 
            }else{
                $countContratista =0; 
            }   
     
            $centroCosto = $input["centroCosto"];
            $estadoCertificacion = $input["estadoCertificacion"];
            if($estadoCertificacion[0]==100){
                $conEstadoCert = 0;
            }else{
                foreach ($estadoCertificacion as $value) {
                $estadoDeCertificacion[] = $value;
                }  
                $conEstadoCert = count($estadoDeCertificacion);
            }
      

            $tipoPago = $input["tipoPago"];
            if($tipoPago[0]==100){
                $conTipoPago = 0;
            }else{
                foreach ($tipoPago as $value) {
                    $tipoDePago[] = $value;
                } 
                $conTipoPago = count($tipoDePago); 
            }
        
           /* $fechaPago = $input["fechaPago"];
            $fechas = $porciones = explode("_", $fechaPago);
            $fecha1 = $fechas[0];
            $fecha2 = $fechas[1];
            $periodosT = $fecha1 ."-".$fecha2;
            $fechasDesdePago = strtotime ( '+4 hour' ,strtotime($fecha1));
            $fechasHastaPago = strtotime ( '+4 hour' ,strtotime($fecha2));
*/
            $fechaSeleccion = $input["fechaSeleccion"];
            $fechasSel = $porcionesSel = explode("_", $fechaSeleccion);
            $fecha1Sel = $fechasSel[0];
            $fecha2Sel = $fechasSel[1];
           
            $fechasDesdeSel = strtotime ( '+4 hour' ,strtotime($fecha1Sel));
            $fechasHastaSel = strtotime ( '+27 hour,59 minutes, 59 seconds' ,strtotime($fecha2Sel));
            /*echo "contr".$countContratista."<br>";
            echo "centro".$centroCosto."<br>";
             echo "prin".$principales."<br>";
             echo "tp".$conTipoPago."<br>";
             echo "estado".$conEstadoCert."<br>";*/
            if($fechaSeleccion != 0 AND $countContratista != 0 AND $centroCosto != 0 AND $principales != 0 AND $conTipoPago != 0 AND $conEstadoCert != 0){
 // echo "1"."<br>";
                $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                ->whereIn('rut',$rutcontratistasR)
                ->where('id',$centroCosto)
                ->whereIn('certificateState',$estadoDeCertificacion)
                ->whereIn('contratoPaymentType',$tipoDePago)
                //->whereBetween('facturaPayDate', [$fechasDesdePago,$fechasHastaPago])
                ->whereBetween('certificateDate', [$fechasDesdeSel,$fechasHastaSel])
                ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','certificateObservations','contratoPaymentType','factura','facturaState','facturaDate','facturaPayDate','facturaTotal'])->toArray();

            }
            if($fechaSeleccion != 0 AND $countContratista != 0  AND $principales != 0 AND $conTipoPago != 0 AND $conEstadoCert != 0){
  //echo "2"."<br>";
                $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                ->whereIn('rut',$rutcontratistasR)
                ->whereIn('certificateState',$estadoDeCertificacion)
                ->whereIn('contratoPaymentType',$tipoDePago)
                //->whereBetween('facturaPayDate', [$fechasDesdePago,$fechasHastaPago])
                ->whereBetween('certificateDate', [$fechasDesdeSel,$fechasHastaSel])
                ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','certificateObservations','contratoPaymentType','factura','facturaState','facturaDate','facturaPayDate','facturaTotal'])->toArray();

            }if($fechaSeleccion != 0  AND $principales != 0 AND $countContratista == 0 AND $conEstadoCert == 0 AND $conTipoPago == 0 ){
                 // echo "3"."<br>";    //echo "hola";
                $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                //->whereBetween('facturaPayDate', [$fechasDesdePago,$fechasHastaPago])
                ->whereBetween('certificateDate', [$fechasDesdeSel,$fechasHastaSel])
                ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','certificateObservations','contratoPaymentType','factura','facturaState','facturaDate','facturaPayDate','facturaTotal'])->toArray();

            }if($fechaSeleccion != 0 AND $principales == 0 AND $countContratista == 0 AND $conEstadoCert == 0 AND $conTipoPago == 0 ){

  //echo "4"."<br>";
                $empresasContratista = Contratista::distinct()
                ->whereBetween('certificateDate', [$fechasDesdeSel,$fechasHastaSel])
                //->whereBetween('facturaPayDate', [$fechasDesdePago,$fechasHastaPago])
                ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','certificateObservations','contratoPaymentType','factura','facturaState','facturaDate','facturaPayDate','facturaTotal'])->toArray();

            }if($fechaSeleccion != 0 AND $conEstadoCert != 0 AND $principales == 0 AND $countContratista == 0  AND $conTipoPago == 0 ){
 // echo "5"."<br>";

                $empresasContratista = Contratista::distinct()
                ->whereBetween('certificateDate', [$fechasDesdeSel,$fechasHastaSel])
                ->whereIn('certificateState',$estadoDeCertificacion)
                //->whereBetween('facturaPayDate', [$fechasDesdePago,$fechasHastaPago])
                ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','certificateObservations','contratoPaymentType','factura','facturaState','facturaDate','facturaPayDate','facturaTotal'])->toArray();

            }if($fechaSeleccion != 0 AND $conEstadoCert != 0 AND $principales == 0 AND $countContratista == 0  AND $conTipoPago != 0 ){

                //  echo "6"."<br>";
                $empresasContratista = Contratista::distinct()
                ->whereBetween('certificateDate', [$fechasDesdeSel,$fechasHastaSel])
                ->whereIn('certificateState',$estadoDeCertificacion)
                ->whereIn('contratoPaymentType',$tipoDePago)
                //->whereBetween('facturaPayDate', [$fechasDesdePago,$fechasHastaPago])
                ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','certificateObservations','contratoPaymentType','factura','facturaState','facturaDate','facturaPayDate','facturaTotal'])->toArray();

            }if($fechaSeleccion!= 0 AND $conEstadoCert != 0 AND $principales != 0 AND $countContratista == 0  AND $conTipoPago == 0 AND $centroCosto == 0){

               //echo "10"."<br>";
                $empresasContratista = Contratista::distinct()
                ->whereIn('mainCompanyRut',$rutprincipalR)
                ->whereBetween('certificateDate', [$fechasDesdeSel,$fechasHastaSel])
                ->whereIn('certificateState',$estadoDeCertificacion)
                //->whereBetween('facturaPayDate', [$fechasDesdePago,$fechasHastaPago])
                ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','certificateObservations','contratoPaymentType','factura','facturaState','facturaDate','facturaPayDate','facturaTotal'])->toArray();

            }if($fechaSeleccion != 0 AND $countContratista != 0 AND $centroCosto == 0 AND $principales != 0 AND $conTipoPago == 0 AND $conEstadoCert == 0){
                //echo "11"."<br>";
                $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                ->whereIn('rut',$rutcontratistasR)
                ->whereBetween('certificateDate', [$fechasDesdeSel,$fechasHastaSel])
                ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','certificateObservations','contratoPaymentType','factura','facturaState','facturaDate','facturaPayDate','facturaTotal'])->toArray();

            }if($fechaSeleccion != 0 AND $countContratista == 0 AND $centroCosto == 0 AND $principales != 0 AND $conTipoPago != 0 AND $conEstadoCert != 0){
               // echo "12"."<br>";
                $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                ->whereBetween('certificateDate', [$fechasDesdeSel,$fechasHastaSel])
                ->whereIn('certificateState',$estadoDeCertificacion)
                ->whereIn('contratoPaymentType',$tipoDePago)
                ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','certificateObservations','contratoPaymentType','factura','facturaState','facturaDate','facturaPayDate','facturaTotal'])->toArray();

            }if($fechaSeleccion != 0 AND $countContratista != 0 AND $centroCosto == 0 AND $principales != 0 AND $conTipoPago == 0 AND $conEstadoCert != 0){
               //echo "13"."<br>";
                $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                ->whereIn('rut',$rutcontratistasR)
                ->whereBetween('certificateDate', [$fechasDesdeSel,$fechasHastaSel])
                ->whereIn('certificateState',$estadoDeCertificacion)
                ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','certificateObservations','contratoPaymentType','factura','facturaState','facturaDate','facturaPayDate','facturaTotal'])->toArray();

            }
            $nombreCertificador="";
            //rint_r($empresasContratista);
          
            if (isset($empresasContratista[0])) {
                foreach ($empresasContratista as $contratista) {
                    unset($Datoscertificacion);
                    if($contratista['certificateState'] != 1){

                            $datosSolictud = Solicitud::distinct()->where('companyId',$contratista['id'])->orderby('serial','DESC')->take(1)->get(['id','workersNumber','workerstotales','serial','paymentExecType','webpayStatus','depositStatus','depositApDate'])->toArray();

                            $trabajadorCarga = TrabajadorVerificacion::where('companyRut',$contratista['rut'])
                            ->where('companyCenter',$contratista['center'])
                            ->where('mainCompanyRut',$contratista['mainCompanyRut'])
                            ->where('periodId',$contratista['periodId'])->count();
                            
                            if(!empty($datosSolictud)){
                                $numeroTrabajadoresTotales = $datosSolictud[0]['workerstotales'];
                                $numeroTrabajadores = $datosSolictud[0]['workersNumber'];
                                $idSolicitud = $datosSolictud[0]['id'];
                            }
                    }else{
                        $numeroTrabajadoresTotales = 0;
                        $numeroTrabajadores = 0;
                        $idSolicitud = 0;
                        $trabajadorCarga = 0;
                    }
                    $peridoTex = periodoTexto($contratista['periodId']);
                    $estadoCerficacionTexto = estadoCerficacionTexto($contratista['certificateState']);
                    if($contratista['certificateDate'] > 0){
                        $fechaCertificiacion=date('d/m/Y', $contratista['certificateDate']);
                    }else{
                        $fechaCertificiacion="";
                    }
                    
                    if($contratista['facturaPayDate'] > 0){
                        $fechaFactura = date('d/m/Y', $contratista['facturaPayDate']);
                    }else{
                        $fechaFactura="";
                    }
                    if($contratista['facturaDate'] > 0){
                        $fechaPago = date('d/m/Y', $contratista['facturaDate']);
                    }else{
                        $fechaPago ="";
                    }
                    $idUsuarioDoc = DocumentoRechazdo::where('id_company',$contratista['id'])->where('doc_reenviado',1)->orderby('fecha','DESC')->take(1)->get(['id_usuario','fecha'])->toArray();
                    $idUsuarioCar = EstadoCargaMasiva::where('id_company',$contratista['id'])->where('cargaerror',1)->orderby('fecha','DESC')->take(1)->get(['id_usuario','fecha','cargaerror','id_company'])->toArray();
                    
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
                    if($contratista['certificateState'] == 10 or $contratista['certificateState'] == 5 ){
                        $datosCertificado = Certificado::where('companyId',$contratista['id'])->orderby('serial','DESC')->take(1)->get(['number','rotationInWorkers','rotationOutLawWorkers','rotationOutLawWorkers','workersNumber','serial'])->toArray();
                    
                        $numeroCertificado = $datosCertificado[0]['number']."-".$datosCertificado[0]['serial'];
                                
                    }else{
                                
                        $numeroCertificado = "";
                               
                    }
                    if($contratista['contratoPaymentType']!=""){
                        $tipoDePago = tipoDePago($contratista['contratoPaymentType']);
                    }else{
                        $tipoDePago = "N/A";
                    }
                    if($contratista['companyTypeId'] == 1){
                        $rutContratista = $contratista['rut']."-".$contratista['dv']; 
                        $rutContratistaSDV = $contratista['rut'];
                        $nombreContratista = ucwords(mb_strtolower($contratista['name'],'UTF-8')); 
                        $rutSubContratista = "";
                        $nombreSubContratista = "";

                    }
                    if($contratista['companyTypeId'] == 2){
                        $rutContratista = $contratista['subcontratistaRut']."-".$contratista['subcontratistaDv'];
                        $rutContratistaSDV = $contratista['subcontratistaRut'];
                        $nombreContratista =  ucwords(mb_strtolower($contratista['subcontratistaName'],'UTF-8'));  
                        $rutSubContratista = $contratista['rut']."-".$contratista['dv']; 
                        $nombreSubContratista = ucwords(mb_strtolower($contratista['name'],'UTF-8')); 

                    }

                    if(!empty($idSolicitud) AND $idSolicitud > 0){
                    //// tabla web pay //////
                        $wepPay = CompanyWebPay::where('refid',$idSolicitud)->where('req_tbk_id_session',$contratista['id'])->take(1)->get(['req_tbk_monto','req_tbk_fecha_contable','req_tbk_fecha_transaccion'])->toArray();
                        if(!empty($wepPay[0]['req_tbk_monto'])){
                            $montoWebPay = $wepPay[0]['req_tbk_monto'];
                            $fechacontableWebPay =$wepPay[0]['req_tbk_fecha_contable'];
                            $fechatransaccionWebPay = $wepPay[0]['req_tbk_fecha_transaccion'];

                        }else{
                            $montoWebPay = "";
                            $fechacontableWebPay = "";
                            $fechatransaccionWebPay = "";
                        }

                        /// tabla deposito ///
                        $deposito = depositoSolictud::where('request_id',$idSolicitud)->take(1)->get(['request_id','dep_amount','dep_comment','dep_transdate','dep_type'])->toArray();
                        if(!empty($deposito[0]['request_id'])){
                            $montoDeposito = $deposito[0]['dep_amount'];
                            $comentarioDeposito =  mb_strtolower($deposito[0]['dep_comment']);
                            $fechaDeposito = date('d/m/Y',$deposito[0]['dep_transdate']);
                            if((int)$deposito[0]['dep_type']){
                                $tipoDeposito ="Transferencia"; 
                            }else{
                               $tipoDeposito ="Deposito"; 
                            }                     
                       

                        }else{
                            $montoDeposito = "";
                            $comentarioDeposito =  "";
                            $fechaDeposito = "";
                            $tipoDeposito = "";
                        }

                        /// tabla deposito subido a solicitud ///
                        $depositoWeb = CompanyWebDeposit::where('refid',$idSolicitud)->where('companyId',$contratista['id'])->take(1)
                        ->get(['refid','dep_crtdat'])->toArray();
                        if(!empty($depositoWeb[0]['refid'])){
                            $fechaSubidaDeposito = date('d/m/Y',$depositoWeb[0]['dep_crtdat']);
                        }else{
                           $fechaSubidaDeposito = "";
                        }
                        $estadoFactura = estadoFactura($contratista['facturaState']);
                    }else{
                        $montoWebPay = "";
                        $fechacontableWebPay = "";
                        $fechatransaccionWebPay = "";
                        $montoDeposito = "";
                        $comentarioDeposito =  "";
                        $fechaDeposito = "";
                        $tipoDeposito = "";
                        $fechaSubidaDeposito = "";
                        $estadoFactura = "";
                    }

                    $ordenCompra = DB::table('xt_oc_groups')
                                    ->join('xt_oc_groups_pos', 'xt_oc_groups_pos.ocg_id', '=', 'xt_oc_groups.id')
                                    ->where(['xt_oc_groups_pos.company_id' =>$contratista['id']])
                                    ->where(['xt_oc_groups.ocg_mrut' => $contratista['mainCompanyRut']])
                                    ->get(['xt_oc_groups.id','xt_oc_groups.ocg_crtdat','xt_oc_groups.ocg_upddat'])->toArray();

                    if(!empty($ordenCompra[0]->id)){
                        $numOC =  $ordenCompra[0]->id;
                        $fechaCreOC =  date('d/m/Y',$ordenCompra[0]->ocg_crtdat);
                        $fechaActOC =  date('d/m/Y',$ordenCompra[0]->ocg_upddat);
                       
                    }else{

                        $ordenCompra = DB::table('xt_oc_groups')
                                    ->join('xt_oc_groups_pos', 'xt_oc_groups_pos.ocg_id', '=', 'xt_oc_groups.id')
                                    ->where(['xt_oc_groups_pos.company_id' =>$contratista['id']])
                                    ->take(1)
                                    ->get(['xt_oc_groups.id','xt_oc_groups.ocg_crtdat','xt_oc_groups.ocg_upddat'])->toArray();
                        if(!empty($ordenCompra[0]->id)){
                            $numOC =  $ordenCompra[0]->id;
                            $fechaCreOC =  date('d/m/Y',$ordenCompra[0]->ocg_crtdat);
                            $fechaActOC =  date('d/m/Y',$ordenCompra[0]->ocg_upddat);
                       
                        }else{
                            $numOC = "";    
                            $fechaCreOC =  "";
                            $fechaActOC =  "";
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
                    $Datoscertificacion['numeroTrabajadoresCertificar'] = $numeroTrabajadores;     
                    $Datoscertificacion['numeroTrabajadoresTotales'] = $numeroTrabajadoresTotales;  
                    $Datoscertificacion['numeroTrabajadoresCarga'] = $trabajadorCarga;  
                    $Datoscertificacion['estadoCertificacion'] = ucwords(mb_strtolower($estadoCerficacionTexto,'UTF-8')); 
                    $Datoscertificacion['fechaCertificado'] =  $fechaCertificiacion;
                    $Datoscertificacion['ciclo'] = ucwords(mb_strtolower($ciclo,'UTF-8'));  
                    $Datoscertificacion['certificador'] = ucwords(mb_strtolower($nombreCertificador,'UTF-8'));
                    $Datoscertificacion['numeroCertificado'] = $numeroCertificado;
                    $Datoscertificacion['FechaOC'] = $fechaCreOC;
                    $Datoscertificacion['FechaOCAct'] = $fechaActOC;
                    $Datoscertificacion['numOC'] = $numOC;    
                    $Datoscertificacion['tipoDePago'] = ucwords(mb_strtolower($tipoDePago,'UTF-8'));
                    $Datoscertificacion['fechaFactura'] = $fechaFactura;
                    $Datoscertificacion['fechaPago'] = $fechaPago;
                    $Datoscertificacion['nunFactura'] = $contratista['factura'];
                    $Datoscertificacion['estatusFactura'] = $estadoFactura;
                    $Datoscertificacion['montoFactura'] = $contratista['facturaTotal'];
                    $Datoscertificacion['montoWebPay'] = $montoWebPay;
                    $Datoscertificacion['fechacontableWebPay'] = $fechacontableWebPay;
                    $Datoscertificacion['fechatransaccionWebPay'] = $fechatransaccionWebPay;
                    $Datoscertificacion['montoDeposito'] = $montoDeposito;
                    $Datoscertificacion['comentarioDeposito'] = $comentarioDeposito;
                    $Datoscertificacion['fechaSubidaDeposito'] = $fechaSubidaDeposito;
                    $Datoscertificacion['fechaDeposito'] = $fechaDeposito;
                    $Datoscertificacion['tipoDeposito'] = $tipoDeposito;
                    $reporteCertificacion[] = $Datoscertificacion;
                   
                }

                if(!empty($reporteCertificacion)){

                    Excel::create('Reporte Certificación-Facturación', function($excel) use ($reporteCertificacion) {

                        $excel->sheet('Lista General', function($sheet) use($reporteCertificacion) {    
                            $sheet->loadView('excel.certificacionFactura',compact('reporteCertificacion'));
                        });
                    })->export('xls'); 
                }
            }else{
                $cantidadDatos = 0;
                return view('factCertificacion.index',compact('EmpresasP','periodos','datosUsuarios','etiquetasEstados','valoresEstados','certificacion','periodosT','principalesTexto','usuarioAqua','usuarioABBChile','usuarioNOKactivo','cantidadDatos','usuarioClaroChile'));
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
