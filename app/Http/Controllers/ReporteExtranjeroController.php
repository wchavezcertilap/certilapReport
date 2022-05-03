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
use App\direccion;
use App\gerencia;
use App\EstadoCargaMasiva;
use App\DocumentoRechazdo;
use App\Region;
use App\Comuna;
use App\TrabajadorExtrajenro;
use Illuminate\Http\Request;

class ReporteExtranjeroController extends Controller
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
        $etiquetasEstados = 0;
        $valoresEstados = 0;
        return view('reporteExtranjero.index',compact('EmpresasP','periodos','datosUsuarios','etiquetasEstados','valoresEstados','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo'));
    }

    public function porCentroCostoExt($contratista,$principal,$peridoInicio,$peridoFinal){

        if($peridoInicio!= 0 AND $peridoFinal != 0){
             return Contratista::distinct()->where('mainCompanyRut','=',$principal)
             ->where('rut',$contratista)
             ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
             ->orderBy('center', 'ASC')->get(['center','id']);

        }

        
        return Contratista::distinct()->where('mainCompanyRut','=',$id)->orderBy('center', 'ASC')->get(['center']);
        
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

        function tipoVisa($idtipoVisa){

            switch ((int)$idtipoVisa) {
                case 1:
                    return $visaTexto ="Visa Temporaria";
                    break;
                case 2:
                    return $visaTexto ="Cedula de Identidad con permanencia definitiva";
                    break;
                case 3:
                    return $visaTexto ="Solicitud de permanencia definitiva";
                    break;
                case 4:
                    return $visaTexto ="Visa sujeta a contrato";
                    break;
                case 5:
                    return $visaTexto ="Solicitud de regularización de migratoria";
                    break;
                case 6:
                    return $visaTexto ="Sin documento";
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

        function periodoTexto($idPerido){

            $periodo = DB::table('Period')
            ->join('Month', 'Month.id', '=', 'Period.monthId')
            ->where(['Period.id' => $idPerido])
            ->select('Period.year','Month.name')
            ->get();
            if(!empty($periodo[0])){
                return $periodo[0]->name."-".$periodo[0]->year;
            }
        }

        function nacionalidadTexto($id){

            switch ((int)$id) {
                case 1:
                    return $nacionalidad ="Chileno";
                    break;
                case 2:
                    return $nacionalidad ="Extranjero";
                    break;
                case 3:
                    return $nacionalidad ="Antiguano";
                    break;
                case 4:
                    return $nacionalidad ="Argentino";
                    break;
                case 5:
                    return $nacionalidad ="Arubeño";
                    break;
                case 6:
                    return $nacionalidad ="Bahameño";
                    break;
                case 7:
                    return $nacionalidad ="Barbadense";
                    break;
                case 8:
                    return $nacionalidad ="Beliceño";
                    break;
                case 9:
                    return $nacionalidad ="Boliviano";
                    break;
                case 10:
                    return $nacionalidad ="Brasileño";
                    break;    
                case 11:
                    return $nacionalidad ="Caimanes";
                    break; 
                case 12:
                    return $nacionalidad ="Colombiano";
                    break;  
                case 13:
                    return $nacionalidad ="Costarricense";
                    break;     
                case 14:
                    return $nacionalidad ="Cubano";
                    break;   
                case 15:
                    return $nacionalidad ="Dominicano";
                    break;  
                case 16:
                    return $nacionalidad ="Ecuatoriano";
                    break; 
                case 17:
                    return $nacionalidad ="Francoguayanes";
                    break; 
                case 18:
                    return $nacionalidad ="Granadino";
                    break; 
                case 19:
                    return $nacionalidad ="Guadalupenses";
                    break; 
                case 20:
                    return $nacionalidad ="Guatemalteco";
                    break; 
                case 21:
                    return $nacionalidad ="Guayanes";
                    break; 
                case 22:
                    return $nacionalidad ="Haitiano";
                    break; 
                case 23:
                    return $nacionalidad ="Hondureño";
                    break; 
                case 24:
                    return $nacionalidad ="Jamaiquino";
                    break; 
                case 25:
                    return $nacionalidad ="Martinicano";
                    break; 
                case 26:
                    return $nacionalidad ="Mexicano";
                    break; 
                case 27:
                    return $nacionalidad ="Nicaraguense";
                    break; 
                case 28:
                    return $nacionalidad ="Panameño";
                    break; 
                case 29:
                    return $nacionalidad ="Paraguayo";
                    break; 
                case 30:
                    return $nacionalidad ="Peruano";
                    break; 
                case 31:
                    return $nacionalidad ="Puertorriqueño";
                    break; 
                case 32:
                    return $nacionalidad ="Salvadoreño";
                    break; 
                case 33:
                    return $nacionalidad ="Sanbartolomense";
                    break; 
                case 34:
                    return $nacionalidad ="Sancristobaleño";
                    break; 
                case 35:
                    return $nacionalidad ="Santalucense";
                    break; 
                case 36:
                    return $nacionalidad ="Sanvicentino";
                    break; 
                case 37:
                    return $nacionalidad ="Surinames";
                    break; 
                case 38:
                    return $nacionalidad ="Trinitario";
                    break; 
                case 39:
                    return $nacionalidad ="Turcocaiqueño";
                    break; 
                case 40:
                    return $nacionalidad ="Uruguayo";
                    break;
                case 41:
                    return $nacionalidad ="Venezolano";
                    break;
                case 42:
                    return $nacionalidad ="Virgenense";
                    break;

            }
        }

        $idUsuario = session('user_id');
        if($idUsuario ==  ""){
            return view('sesion.index');
        }
        $usuarioAqua = session('user_aqua');
        $usuarioABBChile= session('user_ABB');
        $usuarioClaroChile= session('user_Claro');
        $usuarioNOKactivo = session('usuario_nok');
        $certificacion = session('certificacion');
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
        foreach ($empresaPrincipal as $value) {

            $rutprincipalR[] = $value;
        }
        $countContratista = 0;
        if(!empty($input["empresaContratista"])){
            $empresaContratista = $input["empresaContratista"];

            foreach ($empresaContratista as $value2) {
                $rutcontratistasR[] = $value2;
            }

            $countContratista =count($rutcontratistasR); 
        }



            $centroCosto = $input["centroCosto"];
            $estadoCertificado = $input["estadoCertificado"];
            $peridoInicio = $input["peridoInicio"];
            $peridoFinal = $input["peridoFinal"];

        if($countContratista > 0){


            if($centroCosto != ""){

                DB::table('Worker')
                    ->join('Company', function ($join) {
                        $join->on('Company.rut', '=', 'worker.companyRut')
                        ->where('Company.center', '=', 'worker.companyCenter');
                })
                ->whereIn('worker.mainCompanyRut',$rutprincipalR)    
                ->whereIn('worker.companyRut',$rutcontratistasR) 
                ->where('Company.center',$centroCosto)
                ->where('worker.birthCountryId', '!=' , 1)  
                ->whereBetween('worker.periodId', [$peridoInicio,$peridoFinal])
                ->whereBetween('Company.periodId', [$peridoInicio,$peridoFinal]) 
                ->get(['worker.id','worker.rut','worker.dv','worker.names','worker.firstLastName','worker.secondLastName','worker.birthCountryId','worker.companyRut','worker.mainCompanyRut','worker.mainCompanyName','worker.companyName','worker.companyCenter','Company.subcontratistaRut','Company.subcontratistaDv','Company.subcontratistaName','Company.center','Company.certificateState','Company.certificateDate','Company.periodId'])->toArray();

        /*        $trabajadorCargaM = TrabajadorVerificacion::whereIn('worker.mainCompanyRut',$rutprincipalR)
                 ->whereIn('worker.companyRut',$rutcontratistasR)
                 ->join('Company', 'Company.rut', '=', 'worker.companyRut')
                 ->where('Company.certificateState',$estadoCertificado)
                 ->where('Company.center',$centroCosto)
                 ->where('worker.birthCountryId', '!=' , 1)
                 ->whereBetween('worker.periodId', [$peridoInicio,$peridoFinal])
                 ->whereBetween('Company.periodId', [$peridoInicio,$peridoFinal])
                 ->orderBy('worker.id', 'ASC')->get(['worker.id','worker.rut','worker.dv','worker.names','worker.firstLastName','worker.secondLastName','worker.birthCountryId','worker.companyRut','worker.mainCompanyRut','worker.mainCompanyName','worker.companyName','worker.companyCenter','Company.subcontratistaRut','Company.subcontratistaDv','Company.subcontratistaName','Company.center','Company.certificateState','Company.certificateDate','Company.periodId'])->toArray();*/
            }else{

                $trabajadorCargaM=DB::table('Worker')
                    ->join('Company', function ($join) {
                        $join->on('Company.rut', '=', 'worker.companyRut')
                        ->on('Company.center', '=', 'worker.companyCenter');
                })
                ->whereIn('worker.mainCompanyRut',$rutprincipalR)    
                ->whereIn('worker.companyRut',$rutcontratistasR) 
                ->where('worker.birthCountryId', '!=' , 1)  
                ->whereBetween('worker.periodId', [$peridoInicio,$peridoFinal])
                ->whereBetween('Company.periodId', [$peridoInicio,$peridoFinal]) 
                ->get(['worker.id','worker.rut','worker.dv','worker.names','worker.firstLastName','worker.secondLastName','worker.birthCountryId','worker.companyRut','worker.mainCompanyRut','worker.mainCompanyName','worker.companyName','worker.companyCenter','Company.subcontratistaRut','Company.subcontratistaDv','Company.subcontratistaName','Company.center','Company.certificateState','Company.certificateDate','Company.periodId']);

            }


            $trabajadorCargaM = collect($trabajadorCargaM)->map(function($x){ return (array) $x; })->toArray(); 
           
            if(!empty($trabajadorCargaM)){
                $trabajadorCargaM2 =super_unique($trabajadorCargaM,'id');
                $cotizaAFP="";
                $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));

                foreach ($trabajadorCargaM2 as $carga) {

                    $extrajerosP = TrabajadorExtrajenro::where('rut',$carga["rut"])
                    ->where('dv',$carga["dv"])
                    ->where('companyRut', $carga["companyRut"])
                    ->where('mainCompanyRut', $carga["mainCompanyRut"])
                    ->take(1)->orderBy('id', 'ASC')->get()->toArray();
                    
                    if(!empty($extrajerosP[0])){

                        $datos['rutExt'] = $carga["rut"]."-".$carga["dv"];
                        $datos['nombre'] = ucwords(mb_strtolower($carga["names"],'UTF-8'));
                        $datos['apellidoP'] = ucwords(mb_strtolower($carga["firstLastName"],'UTF-8'));
                        $datos['apellidoM'] =  ucwords(mb_strtolower($carga["secondLastName"],'UTF-8'));
                        $datos['rutprincipal'] = formatRut($carga["mainCompanyRut"]);
                        $datos['nombrePrincipal'] =  ucwords(mb_strtolower($carga["mainCompanyName"],'UTF-8'));
                        $datos['rutcontratista'] = formatRut($carga["companyRut"]);
                        $datos['nombreContratista'] =  ucwords(mb_strtolower($carga["companyName"],'UTF-8'));

                        if(!empty($trabajadorCarga[0]["subcontratistaRut"])){
                            $datos['rutsubContratista'] = $carga["subcontratistaRut"].'-'.$carga["subcontratistaDv"];
                            $datos['subnombre'] =  ucwords(mb_strtolower($carga["subcontratistaName"],'UTF-8'));

                        }else{
                            $datos['rutsubContratista'] ="";
                            $datos['subnombre'] = "";
                        }
                        $datos['centroC'] =  ucwords(mb_strtolower($carga["companyCenter"],'UTF-8'));
                        $datos['estadoCertificado'] = estadoCerficacionTexto($carga["certificateState"]);
                        $datos['fechaCertificado'] =  date('d/m/Y',$carga["certificateDate"]);
                        $datos['nacionalidad'] =  nacionalidadTexto($carga["birthCountryId"]);
                        if($extrajerosP[0]['fechaVencimientoRut'] == 0 or $extrajerosP[0]['fechaVencimientoRut']== ""){
                            $datos['vencimientoRut'] =  "";
                        }else{
                            $datos['vencimientoRut'] =  date('d/m/Y',$extrajerosP[0]["fechaVencimientoRut"]);
                        }
                        if($extrajerosP[0]["cotiza"]=='S'){
                            $cotizaAFP =  'Si';
                        }if($extrajerosP[0]["cotiza"]=='N'){
                            $cotizaAFP =  'No';
                        }
                        $datos['cotizaAFP'] =  $cotizaAFP;
                        if($extrajerosP[0]['fechaIngresoCert'] == 0 or $extrajerosP[0]['fechaIngresoCert']== ""){
                            $datos['fechaIngresoCert'] =  "";
                        }else{
                            $datos['fechaIngresoCert'] =  date('d/m/Y',$extrajerosP[0]["fechaIngresoCert"]);   
                        }
                        
                        $datos['tipoVisa'] =  tipoVisa($extrajerosP[0]["tipoVisa"]);
                        if($extrajerosP[0]['fechaVencimientoVisa']== 0 or $extrajerosP[0]['fechaVencimientoVisa']== ""){
                            $datos['fechaVencimientoVisa'] =   "";
                        }else{
                            $datos['fechaVencimientoVisa'] =   date('d/m/Y',$extrajerosP[0]["fechaVencimientoVisa"]);;    
                        }
                        if($extrajerosP[0]['fechaVencimientoVisa'] <= $fecha_actual){
                            $datos['colorFecha'] = 'fe0000';
                        }if($extrajerosP[0]['fechaVencimientoVisa'] >= $fecha_actual){
                            $datos['colorFecha'] = '008020';
                        }if($extrajerosP[0]['fechaVencimientoVisa'] == ""){
                            $datos['colorFecha'] = 'FFFFFF';
                        }
                       
                        $datos['peridoDoc'] =  periodoTexto($extrajerosP[0]["docPeriodo"]);
                        $datos['Observacion'] =  ucwords(mb_strtolower($extrajerosP[0]["observacion"],'UTF-8'));
                    }else{
                        $datos['rutExt'] = $carga["rut"]."-".$carga["dv"];
                        $datos['nombre'] = ucwords(mb_strtolower($carga["names"],'UTF-8'));
                        $datos['apellidoP'] = ucwords(mb_strtolower($carga["firstLastName"],'UTF-8'));
                        $datos['apellidoM'] =  ucwords(mb_strtolower($carga["secondLastName"],'UTF-8'));
                        $datos['rutprincipal'] = formatRut($carga["mainCompanyRut"]);
                        $datos['nombrePrincipal'] =  ucwords(mb_strtolower($carga["mainCompanyName"],'UTF-8'));

                        $datos['rutcontratista'] = formatRut($carga["companyRut"]);
                        $datos['nombreContratista'] =  ucwords(mb_strtolower($carga["companyName"],'UTF-8'));

                        if(!empty($trabajadorCarga[0]["subcontratistaRut"])){
                            $datos['rutsubContratista'] = $carga["subcontratistaRut"].'-'.$carga["subcontratistaDv"];
                            $datos['subnombre'] =  ucwords(mb_strtolower($carga["subcontratistaName"],'UTF-8'));

                        }else{
                            $datos['rutsubContratista'] ="";
                            $datos['subnombre'] = "";
                        }
                        $datos['centroC'] =  ucwords(mb_strtolower($carga["center"],'UTF-8'));
                        $datos['estadoCertificado'] = estadoCerficacionTexto($carga["certificateState"]);
                        $datos['fechaCertificado'] =  date('d/m/Y',$carga["certificateDate"]);
                        $datos['nacionalidad'] =  nacionalidadTexto($carga["birthCountryId"]);
                        $datos['vencimientoRut'] =  "";
                        $datos['cotizaAFP'] =  "";
                        $datos['fechaIngresoCert'] =  "";
                        $datos['tipoVisa'] = "";
                        $datos['fechaVencimientoVisa'] =   "";
                        $datos['colorFecha'] = "FBED13";
                        $datos['peridoDoc'] =  "";
                        $datos['Observacion'] =  "";
                    }

                    $trabajadoresExtra[] = $datos;
                }

               
                if(!empty($trabajadoresExtra)){

                    Excel::create('Trabajadores Extranjeros', function($excel) use($trabajadoresExtra) {
                        
                        $excel->sheet('Extranjeros', function($sheet) use($trabajadoresExtra) {    
                                $sheet->loadView('reporteExtranjero.excelExt',compact('trabajadoresExtra'));
                        });
                                     
                    })->export('xls');
                }else{
                $nodatos =1;
                return view('reporteExtranjero.index',compact('EmpresasP','periodos','datosUsuarios','etiquetasEstados','valoresEstados','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','nodatos','usuarioClaroChile'));
                }

            }else{
                $nodatos =1;
                return view('reporteExtranjero.index',compact('EmpresasP','periodos','datosUsuarios','etiquetasEstados','valoresEstados','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','nodatos','usuarioClaroChile'));
            }
           

        }else{

            $trabajadorCargaM=DB::table('Worker')
                    ->join('Company', function ($join) {
                        $join->on('Company.rut', '=', 'worker.companyRut')
                         ->on('Company.center', '=', 'worker.companyCenter');
                })
                ->whereIn('worker.mainCompanyRut',$rutprincipalR)    
                ->where('worker.birthCountryId', '!=' , 1)  
                ->whereBetween('worker.periodId', [$peridoInicio,$peridoFinal])
                ->whereBetween('Company.periodId', [$peridoInicio,$peridoFinal]) 
                ->get(['worker.id','worker.rut','worker.dv','worker.names','worker.firstLastName','worker.secondLastName','worker.birthCountryId','worker.companyRut','worker.mainCompanyRut','worker.mainCompanyName','worker.companyName','worker.companyCenter','Company.subcontratistaRut','Company.subcontratistaDv','Company.subcontratistaName','Company.center','Company.certificateState','Company.certificateDate','Company.periodId']);

            $trabajadorCargaM = collect($trabajadorCargaM)->map(function($x){ return (array) $x; })->toArray(); 
               
            if(!empty($trabajadorCargaM)){

                $trabajadorCargaM2 =super_unique($trabajadorCargaM,'id');

                $cotizaAFP="";
                $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));

                foreach ($trabajadorCargaM2 as $carga) {

                    $extrajerosP = TrabajadorExtrajenro::where('rut',$carga['rut'])
                    ->where('dv',$carga["dv"])
                    ->where('companyRut', $carga["companyRut"])
                    ->where('mainCompanyRut', $carga["mainCompanyRut"])
                    ->take(1)->orderBy('id', 'ASC')->get()->toArray();
                    
                    if(!empty($extrajerosP[0])){

                        $datos['rutExt'] = $carga["rut"]."-".$carga["dv"];
                        $datos['nombre'] = ucwords(mb_strtolower($carga["names"],'UTF-8'));
                        $datos['apellidoP'] = ucwords(mb_strtolower($carga["firstLastName"],'UTF-8'));
                        $datos['apellidoM'] =  ucwords(mb_strtolower($carga["secondLastName"],'UTF-8'));
                        $datos['rutprincipal'] = formatRut($carga["mainCompanyRut"]);
                        $datos['nombrePrincipal'] =  ucwords(mb_strtolower($carga["mainCompanyName"],'UTF-8'));
                        $datos['rutcontratista'] = formatRut($carga["companyRut"]);
                        $datos['nombreContratista'] =  ucwords(mb_strtolower($carga["companyName"],'UTF-8'));

                        if(!empty($trabajadorCarga[0]["subcontratistaRut"])){
                            $datos['rutsubContratista'] = $carga["subcontratistaRut"].'-'.$carga["subcontratistaDv"];
                            $datos['subnombre'] =  ucwords(mb_strtolower($carga["subcontratistaName"],'UTF-8'));

                        }else{
                            $datos['rutsubContratista'] ="";
                            $datos['subnombre'] = "";
                        }
                        $datos['centroC'] =  ucwords(mb_strtolower($carga["companyCenter"],'UTF-8'));
                        $datos['estadoCertificado'] = estadoCerficacionTexto($carga["certificateState"]);
                        $datos['fechaCertificado'] =  date('d/m/Y',$carga["certificateDate"]);
                        $datos['nacionalidad'] =  nacionalidadTexto($carga["birthCountryId"]);
                        if($extrajerosP[0]['fechaVencimientoRut'] == 0 or $extrajerosP[0]['fechaVencimientoRut']== ""){
                            $datos['vencimientoRut'] =  "";
                        }else{
                            $datos['vencimientoRut'] =  date('d/m/Y',$extrajerosP[0]["fechaVencimientoRut"]);
                        }
                        if($extrajerosP[0]["cotiza"]=='S'){
                            $cotizaAFP =  'Si';
                        }if($extrajerosP[0]["cotiza"]=='N'){
                            $cotizaAFP =  'No';
                        }
                        $datos['cotizaAFP'] =  $cotizaAFP;
                        if($extrajerosP[0]['fechaIngresoCert'] == 0 or $extrajerosP[0]['fechaIngresoCert']== ""){
                            $datos['fechaIngresoCert'] =  "";
                        }else{
                            $datos['fechaIngresoCert'] =  date('d/m/Y',$extrajerosP[0]["fechaIngresoCert"]);   
                        }
                        
                        $datos['tipoVisa'] =  tipoVisa($extrajerosP[0]["tipoVisa"]);
                        if($extrajerosP[0]['fechaVencimientoVisa']== 0 or $extrajerosP[0]['fechaVencimientoVisa']== ""){
                            $datos['fechaVencimientoVisa'] =   "";
                        }else{
                            $datos['fechaVencimientoVisa'] =   date('d/m/Y',$extrajerosP[0]["fechaVencimientoVisa"]);;    
                        }
                        if($extrajerosP[0]['fechaVencimientoVisa'] <= $fecha_actual){
                            $datos['colorFecha'] = 'fe0000';
                        }if($extrajerosP[0]['fechaVencimientoVisa'] >= $fecha_actual){
                            $datos['colorFecha'] = '008020';
                        }if($extrajerosP[0]['fechaVencimientoVisa'] == ""){
                            $datos['colorFecha'] = 'FFFFFF';
                        }
                       
                        $datos['peridoDoc'] =  periodoTexto($extrajerosP[0]["docPeriodo"]);
                        $datos['Observacion'] =  ucwords(mb_strtolower($extrajerosP[0]["observacion"],'UTF-8'));
                    }else{
                        $datos['rutExt'] = $carga["rut"]."-".$carga["dv"];
                        $datos['nombre'] = ucwords(mb_strtolower($carga["names"],'UTF-8'));
                        $datos['apellidoP'] = ucwords(mb_strtolower($carga["firstLastName"],'UTF-8'));
                        $datos['apellidoM'] =  ucwords(mb_strtolower($carga["secondLastName"],'UTF-8'));
                        $datos['rutprincipal'] = formatRut($carga["mainCompanyRut"]);
                        $datos['nombrePrincipal'] =  ucwords(mb_strtolower($carga["mainCompanyName"],'UTF-8'));

                        $datos['rutcontratista'] = formatRut($carga["companyRut"]);
                        $datos['nombreContratista'] =  ucwords(mb_strtolower($carga["companyName"],'UTF-8'));

                        if(!empty($trabajadorCarga[0]["subcontratistaRut"])){
                            $datos['rutsubContratista'] = $carga["subcontratistaRut"].'-'.$carga["subcontratistaDv"];
                            $datos['subnombre'] =  ucwords(mb_strtolower($carga["subcontratistaName"],'UTF-8'));

                        }else{
                            $datos['rutsubContratista'] ="";
                            $datos['subnombre'] = "";
                        }
                        $datos['centroC'] =  ucwords(mb_strtolower($carga["center"],'UTF-8'));
                        $datos['estadoCertificado'] = estadoCerficacionTexto($carga["certificateState"]);
                        $datos['fechaCertificado'] =  date('d/m/Y',$carga["certificateDate"]);
                        $datos['nacionalidad'] =  nacionalidadTexto($carga["birthCountryId"]);
                        $datos['vencimientoRut'] =  "";
                        $datos['cotizaAFP'] =  "";
                        $datos['fechaIngresoCert'] =  "";
                        $datos['tipoVisa'] = "";
                        $datos['fechaVencimientoVisa'] =   "";
                        $datos['colorFecha'] = 'FFFFFF';
                        $datos['peridoDoc'] =  "";
                        $datos['Observacion'] =  "";
                    }

                    $trabajadoresExtra[] = $datos;
                }

               
                if(!empty($trabajadoresExtra)){

                    Excel::create('Trabajadores Extranjeros', function($excel) use($trabajadoresExtra) {
                        
                        $excel->sheet('Extranjeros', function($sheet) use($trabajadoresExtra) {    
                                $sheet->loadView('reporteExtranjero.excelExt',compact('trabajadoresExtra'));
                        });
                                     
                    })->export('xls');
                }else{
                $nodatos =1;
                return view('reporteExtranjero.index',compact('EmpresasP','periodos','datosUsuarios','etiquetasEstados','valoresEstados','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','nodatos','usuarioClaroChile'));
                }

            }else{
                $nodatos =1;
                return view('reporteExtranjero.index',compact('EmpresasP','periodos','datosUsuarios','etiquetasEstados','valoresEstados','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','nodatos','usuarioClaroChile'));
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
