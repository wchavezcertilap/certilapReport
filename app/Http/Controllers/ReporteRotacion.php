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
use Illuminate\Http\Request;

class ReporteRotacion extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function porContratistaRotacion($id){
        
        return Contratista::distinct()->where('mainCompanyRut','=',$id)->orderBy('name', 'ASC')->get(['name','rut']);   
    }

    public function porCentroCostoRotacion($contratista,$principal,$peridoInicio,$peridoFinal,$fechaSeleccion){

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
            $fechasDesde =  strtotime ( '+4 hour' ,strtotime($fecha1));
            //sumo 1 día
            $fechasHasta =  strtotime ( '+4 hour' ,strtotime($fecha2));
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
        $etiquetasEstados = 0;
        $valores = 0;
        return view('reporteRotacion.index',compact('EmpresasP','periodos','datosUsuarios','etiquetasEstados','valores','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo'));
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
        }else{
             $countContratista = 0;
        }
        $tipoBsuqueda = $input["tipoBsuqueda"];
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

        function periodoFeha($idPerido){

            $periodo = DB::table('Period')
            ->where(['id' => $idPerido])
            ->select('year','monthId')
            ->get();

            return $periodo[0]->year."/".$periodo[0]->monthId;
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
        if($rutprincipalR[0]==1){

            if($datosUsuarios->type == 3){

                $rutprincipalRC = empresaPrincipal::distinct()->whereIn('rut',$rutprincipal)->orderBy('name', 'ASC')->get(['name','rut'])->toArray();
            }
            if($datosUsuarios->type ==2 || $datosUsuarios->type ==1 ){

                $rutprincipalRC = empresaPrincipal::distinct()->orderBy('name', 'ASC')->get(['name','rut'])->toArray();
            }

            $rutprincipalRL = super_unique($rutprincipalRC,'rut');

            foreach ($rutprincipalRL as $value) {
            $rutprincipalR[] = $value['rut'];
            }

        }

        if($tipoBsuqueda == 1){

            $peridoInicio = $input["peridoInicio"];
            $peridoFinal = $input["peridoFinal"];

            if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista != 0 AND $centroCosto != 0){

            $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
            ->whereIn('rut',$rutcontratistasR)
            ->whereIn('certificateState',[5,10])
            ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
            ->where('id',$centroCosto)
            ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType'])->toArray();

            }if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista == 0 AND $centroCosto == 0){

                $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                 ->whereIn('certificateState',[5,10])
                 ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
                 ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType'])->toArray();

            }
            if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista != 0 AND $centroCosto == 0){

            $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
            ->whereIn('rut',$rutcontratistasR)
            ->whereIn('certificateState',[5,10])
            ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
            ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType'])->toArray();

            }

        }
        if($tipoBsuqueda == 2){

            $fechaSeleccion = $input["fechaSeleccion"];
            if($fechaSeleccion != 0  AND $countContratista != 0 AND $centroCosto != 0){
            
            $fechas = $porciones = explode("_", $fechaSeleccion);
            $fecha1 = $fechas[0];
            $fecha2 = $fechas[1];
            $fechasDesde =  strtotime ( '+4 hour' ,strtotime($fecha1));
            //sumo 1 día
            $fechasHasta =  strtotime ( '+4 hour' ,strtotime($fecha2));
            $empresasContratista = Contratista::distinct()->whereIn('rut',$rutcontratistasR)
            ->whereIn('certificateState',[5,10])
            ->whereBetween('certificateDate', [$fechasDesde,$fechasHasta])
            ->where('id',$centroCosto)
            ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType'])->toArray();

            }
            if($fechaSeleccion != 0  AND $countContratista == 0 AND $centroCosto == 0){
            
            $fechas = $porciones = explode("_", $fechaSeleccion);
            $fecha1 = $fechas[0];
            $fecha2 = $fechas[1];
            $fechasDesde =  strtotime ( '+4 hour' ,strtotime($fecha1));
            //sumo 1 día
            $fechasHasta =  strtotime ( '+4 hour' ,strtotime($fecha2));
            $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
            ->whereIn('certificateState',[5,10])
            ->whereBetween('certificateDate', [$fechasDesde,$fechasHasta])
            ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType'])->toArray();

            }
            if($fechaSeleccion != 0  AND $countContratista != 0 AND $centroCosto == 0){
            
            $fechas = $porciones = explode("_", $fechaSeleccion);
            $fecha1 = $fechas[0];
            $fecha2 = $fechas[1];
            $fechasDesde =  strtotime ( '+4 hour' ,strtotime($fecha1));
            //sumo 1 día
            $fechasHasta =  strtotime ( '+4 hour' ,strtotime($fecha2));
            $empresasContratista = Contratista::distinct()->whereIn('rut',$rutcontratistasR)
            ->whereIn('mainCompanyRut',$rutprincipalR)
            ->whereIn('certificateState',[5,10])
            ->whereBetween('certificateDate', [$fechasDesde,$fechasHasta])
            ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType'])->toArray();

            }
        }

        $cuentaEresados = 0;
        $cuentaIngresados = 0;
        if(!empty($empresasContratista)){
            $countContratistaR = 1;
        }else{
           $countContratistaR = 0; 
        }

      
        foreach ($empresasContratista as $value) {

    
                $datoTrabajadores = TrabajadorVerificacion::where('mainCompanyRut',$value["mainCompanyRut"])->
                                                                 where('companyRut',$value["rut"])->
                                                                 where('periodId',$value["periodId"])->
                                                                 where('companyCenter',$value["center"])->
                                                                 get(['id','rut','dv','names','firstLastName','secondLastName','beginDate','position','endDate','dismissalCausal'])->toArray();

               

                ///egresados///
                if(!empty($datoTrabajadores)){

                    foreach ($datoTrabajadores as $key => $datoTrabajador) {
                        # code...
                        if($datoTrabajador['endDate']!=""){
                            $cuentaEresados+=1;
                            $egresados['rutEmpleado'] = $datoTrabajador['rut']."-".$datoTrabajador['dv']; 
                            $egresados['nombre'] = ucwords(mb_strtolower($datoTrabajador['names']." ".$datoTrabajador['firstLastName']." ".$datoTrabajador['secondLastName'],'UTF-8')); 
                            $egresados['cargo'] = ucwords(mb_strtolower($datoTrabajador['position'],'UTF-8'));
                            $egresados['fechaIngreso'] = date('d/m/Y',$datoTrabajador['beginDate']);
                            $egresados['fechaDespido'] = date('d/m/Y',$datoTrabajador['endDate']);
                            $egresados['causaSalida'] = ucwords(mb_strtolower($datoTrabajador['dismissalCausal']));
                            $egresados['fechaCertificado'] = date('d/m/Y',$value['certificateDate']);
                            $estadoCerficacionTexto = estadoCerficacionTexto($value['certificateState']);
                            $egresados['estadoCertificacion'] = ucwords(mb_strtolower($estadoCerficacionTexto,'UTF-8'));
                            $egresados['rutPrincipal'] = formatRut($value['mainCompanyRut']);
                            $egresados['nombrePrincipal'] = ucwords(mb_strtolower($value['mainCompanyName'],'UTF-8'));
                            $egresados['RutContratista'] = $value['rut']."-".$value['dv'];
                            $egresados['nombreContratista'] = ucwords(mb_strtolower($value['name'],'UTF-8'));
                            $egresados['centroCosto'] = ucwords(mb_strtolower($value['center'],'UTF-8'));
                            $peridoTex = periodoTexto($value['periodId']);
                            $egresados['perido'] = ucwords(mb_strtolower($peridoTex,'UTF-8'));
                           
                            $listaEgresado[] = $egresados;

                        }else{

                            $fechaP=periodoFeha($value['periodId']);
                            $fechaPI =  strtotime($fechaP."/01"); 
                            $fechaPF =  strtotime($fechaP."/31");   
                              
                            //echo "fechaI".$fechaPI."</BR>"; 
                            //echo "fechaF".$fechaPF."</BR>";
                            if($datoTrabajador['beginDate'] >= $fechaPI and $datoTrabajador['beginDate']<=$fechaPF){
                                $cuentaIngresados+=1; 
                            
                                $ingresados['rutEmpleado'] = $datoTrabajador['rut']."-".$datoTrabajador['dv']; 
                                $ingresados['nombre'] = ucwords(mb_strtolower($datoTrabajador['names']." ".$datoTrabajador['firstLastName']." ".$datoTrabajador['secondLastName'],'UTF-8')); 
                                $ingresados['cargo'] = ucwords(mb_strtolower($datoTrabajador['position'],'UTF-8'));
                                $ingresados['fechaIngreso'] = date('d/m/Y',$datoTrabajador['beginDate']);
                                $ingresados['fechaCertificado'] = date('d/m/Y',$value['certificateDate']);
                                $estadoCerficacionTexto = estadoCerficacionTexto($value['certificateState']);
                                $ingresados['estadoCertificacion'] = ucwords(mb_strtolower($estadoCerficacionTexto,'UTF-8'));
                                $ingresados['rutPrincipal'] = formatRut($value['mainCompanyRut']);
                                $ingresados['nombrePrincipal'] = ucwords(mb_strtolower($value['mainCompanyName'],'UTF-8'));
                                $ingresados['RutContratista'] = $value['rut']."-".$value['dv'];
                                $ingresados['nombreContratista'] = ucwords(mb_strtolower($value['name'],'UTF-8'));
                                $ingresados['centroCosto'] = ucwords(mb_strtolower($value['center'],'UTF-8'));
                                $peridoTex = periodoTexto($value['periodId']);
                                $ingresados['perido'] = ucwords(mb_strtolower($peridoTex,'UTF-8'));
                               

                                $listaIngresados[] = $ingresados;
                            }
                        }
                    }
                }
        }

        
        $etiquetasEstados = array("Cantidad Ingresado","Cantidad Desvinculados");
        $valores = array($cuentaIngresados,$cuentaEresados);
        return view('reporteRotacion.index',compact('EmpresasP','periodos','datosUsuarios','certificacion','listaEgresado','listaIngresados','etiquetasEstados','valores','cuentaIngresados','cuentaEresados','countContratistaR','usuarioAqua','usuarioABBChile','usuarioNOKactivo'));


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
