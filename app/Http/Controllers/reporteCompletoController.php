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
use App\Region;
use App\Comuna;
use Illuminate\Http\Request;

class reporteCompletoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function porContratista($id){
        
        return Contratista::distinct()->where('mainCompanyRut','=',$id)->orderBy('name', 'ASC')->get(['name','rut']);
    }


     public function centroCosto($rut,$empresaPrincipal){
        
        return Contratista::distinct()->where([
        ['mainCompanyRut', '=', $empresaPrincipal],
        ['rut', '=', $rut],])->orderBy('center', 'ASC')->get(['center','rut']);
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
        return view('reporteCompleto.index',compact('EmpresasP','periodos','datosUsuarios','etiquetasEstados','valoresEstados','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo'));
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

        function sexoTexto($idsexo){

            switch ((int)$idsexo) {
                case 1:
                    return $sexo ="Masculino";
                    break;
                case 2:
                    return $sexo ="Femenino";
                    break;
            }
        }

        function jubiladoTexto($id){

            switch ((int)$id) {
                case 1:
                    return $jubilado ="Si";
                    break;
                case 2:
                    return $jubilado ="No";
                    break;
            }
        }

        function credencialTexto($id){

            if($id != "") {
                return $credencial ="Si";
                    break;
            }else{
                return $credencial ="No";
                    break;
            }
        }

        function textoSujetoLicencia($id){

            if($id == 1) {
                return $licencia ="Si";
                    break;
            }if($id == 0) {
                return $licencia ="No";
            }
        }

        function licenciaTexto($id){
            if($id == 1) {
                return $licencia ="Si";
                    break;
            }if($id == 2){
                return $licencia ="No";
                    break;
            }

        }

        function textoCharla($id){
            if($id == 1) {
                return $charla ="Si";
                    break;
            }if($id == 2){
                return $charla ="No";
                    break;
            }
        }

        function textoCarta($id){
            if($id == 1) {
                return $charla ="Si";
                    break;
            }if($id == 2){
                return $charla ="No";
                    break;
            }

        }


        function credencialReqTexto($id){
            if($id != "") {
                return $credencial ="Si";
                    break;
            }else{
                return $credencial ="No";
                    break;
            }
        }

        function estadocivilTexto($id){

            switch ((int)$id) {
                case 1:
                    return $civil ="Casado";
                    break;
                case 2:
                    return $civil ="Divorciado";
                    break;
                case 3:
                    return $civil ="Soltero";
                    break;
                case 4:
                    return $civil ="Viudo";
                    break;
                case " ":
                    return $civil ="";
                    break;
            }
        }

        function educacionTexto($id){

            switch ((int)$id) {
                case 1:
                    return $educacion ="Basico";
                    break;
                case 2:
                    return $educacion ="Educación Media Completa";
                    break;
                case 3:
                    return $educacion ="Técnico Profesional";
                    break;
                case 4:
                    return $educacion ="Profesional Universitaria";
                    break;
                case 5:
                    return $educacion ="Postgrado";
                    break;
                case "":
                    return $educacion ="";
                    break;
            }
        }

        function estadoCredencialTexto($id){
            switch ((int)$id) {
                case 1:
                    return $estadoC ="Sin Estado";
                    break;
                case 2:
                    return $estadoC ="Activa";
                    break;
                case 3:
                    return $estadoC ="No Activa";
                    break;
                case 4:
                    return $estadoC ="Vencida";
                    break;
                case "":
                    return $estadoC ="";
                    break;
            }

        }

        function estadoSindicato($id){
            switch ((int)$id) {
                case 1:
                    return $estadoC ="Activo";
                    break;
                case 2:
                    return $estadoC ="No Activo";
                    break;
                case 3:
                    return $estadoC ="Sin Estado";
                    break;
                case 4:
                    return $estadoC ="En Trámite";
                    break;
                case "":
                    return $estadoC ="";
                    break;
            }

        }

        function tipoContrato($id){
            switch ((int)$id) {
                case 1:
                    return $contrato ="Obra o Faena";
                    break;
                case 2:
                    return $contrato ="Indefinido";
                    break;
                case 3:
                    return $contrato ="Plazo Fijo";
                    break;
            }

        }

        function textoDesvinculacion($id){

            switch ((int)$id) {
                case 2:
                    return $contrato ="Artículo 159 Renuncia";
                    break;
                case 3:
                    return $contrato ="Artículo 159 Muerte";
                    break;
                case 4:
                    return $contrato ="Artículo 159 Otro";
                    break;
                case 5:
                    return $contrato ="Artículo 159 Conclusión del trabajo";
                    break;
                case 9:
                    return $contrato ="Artículo 160 Otro";
                    break;
                case 13:
                    return $contrato ="Artículo 160 No concurrencia";
                    break;
                case 18:
                    return $contrato ="Artículo 161";
                    break;
                case 19:
                    return $contrato ="Artículo 171 Autodespido";
                    break;
                case 20:
                    return $contrato ="Artículo 161 Inciso 2";
                    break;
                case "":
                    return $contrato ="";
                    break;
            }

        }

        function textoJornada($id){

            switch ((int)$id) {
                case 1:
                    return $jornada ="45 Horas Semanales";
                    break;
                case 2:
                    return $jornada ="60 horas semanales";
                    break;
                case 3:
                    return $jornada ="Otro tipo de jornada";
                    break;
                case 4:
                    return $jornada ="Jornada Parcial";
                    break;
                case 5:
                    return $jornada ="Bisemanal";
                    break;
                case 6:
                    return $jornada ="Jornada Especial Autorizada por la  DT";
                    break;
                case 7:
                    return $jornada ="Art. 22";
                    break;
                case 8:
                    return $jornada ="Art. 22 Inciso 2";
                    break;
                case "":
                    return $jornada ="";
                    break;
            }

        }

        function textoAFP($id){

            switch ((int)$id) {
                case 1:
                    return $AFP ="Capital";
                    break;
                case 2:
                    return $AFP ="Cuprum";
                    break;
                case 3:
                    return $AFP ="Habitat";
                    break;
                case 4:
                    return $AFP ="Modelo";
                    break;
                case 5:
                    return $AFP ="Plan Vital";
                    break;
                case 6:
                    return $AFP ="Provida";
                    break;
                case 7:
                    return $AFP ="S.S.S- regimen 2";
                    break;
                case 8:
                    return $AFP ="Empart";
                    break;
                case 9:
                    return $AFP ="S:S.S-Regimen 1";
                    break;
                case 10:
                    return $AFP ="Caja Bancaria de Pensiones";
                    break;
                case 11:
                    return $AFP ="Pensionado";
                    break;
                case 12:
                    return $AFP ="Extranjero";
                    break;
                case 13:
                    return $AFP ="CAPREMER";
                    break;
                case 14:
                    return $AFP ="DIPRECA";
                    break;
                case 15:
                    return $AFP ="CAPREDENA";
                    break;
                case 16:
                    return $AFP ="Triomar";
                    break;
                case 17:
                    return $AFP ="Pensionado Invalides Parcial";
                    break;
                case 18:
                    return $AFP ="Pensionado Invalides Total";
                    break;
                case 19:
                    return $AFP ="Uno";
                    break;
                case "":
                    return $AFP ="";
                    break;
            }

        }

        function textoISAPRE($id){

            switch ((int)$id) {
                case 1:
                    return $salud ="Banmédica";
                    break;
                case 2:
                    return $salud ="Colmena";
                    break;
                case 3:
                    return $salud ="ConSalud";
                    break;
                case 4:
                    return $salud ="Cruz del Norte";
                    break;
                case 5:
                    return $salud ="Cruz Blanca";
                    break;
                case 6:
                    return $salud ="Masvida";
                    break;
                case 7:
                    return $salud ="Río Blanco";
                    break;
                case 8:
                    return $salud ="Vida Tres";
                    break;
                case 9:
                    return $salud ="Fonasa";
                    break;
                case 10:
                    return $salud ="Ferrosalud";
                    break;
                case 11:
                    return $salud ="ING Salud";
                    break;
                case 12:
                    return $salud ="Banco Estado";
                    break;
                case 13:
                    return $salud ="Fusat";
                    break;
                case 14:
                    return $salud ="Planvital";
                    break;
                case 15:
                    return $salud ="No Tiene";
                    break;
                case 16:
                    return $salud ="Isapre Fundación";
                    break;
                case 17:
                    return $salud ="Fundación";
                    break;
                case 18:
                    return $salud ="CAPREDENA";
                    break;
                case 19:
                    return $salud ="DIPRECA";
                    break;
                case 20:
                    return $salud ="CAPREMER";
                    break;
                case 21:
                    return $salud ="Chuquicamata";
                    break;
                case 22:
                    return $salud ="San Lorenzo Isapre Ltda";
                    break;
                case 23:
                    return $salud ="Optima";
                    break;
                case "":
                    return $salud ="";
                    break;
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

        function tipoRenta($id){

            switch ((int)$id) {
                case 1:
                    return $renta ="Fija";
                    break;
                case 2:
                    return $renta ="Variable";
                    break;
            }
        }

        function regionTexto($idregion){

            $periodo = DB::table('Region')
            ->where(['id' => $idregion])
            ->select('name')
            ->get();

            return $periodo[0]->name;
        }

        function comunaTexto($idcomuna){

            $periodo = DB::table('Town')
            ->where(['id' => $idcomuna])
            ->select('name')
            ->get();

            return $periodo[0]->name;
        }

        function calculaEdad($birthday)
        {
           
           $years = "";
           if((int)$birthday)
              $years = floor((time() - $birthday) / 31556926);
           return $years;
        }

        function calculaHoraExtras($calculusBase, $baseSalary, $overtimeRecorded) {

            if($overtimeRecorded!= 0){
                $baseSalario =floor($calculusBase * $baseSalary);
            return floor($baseSalario / $overtimeRecorded);
            }
        }

        function calculaImponibles($baseSalary,$incentive,$productionBonus,$treatmentBonus,$totalOtherBonus,$totalOtherTaxable,$totalOverTime,$totalOtherTaxableDiscount) {
            $ingresos = ($baseSalary + $incentive + $productionBonus + $treatmentBonus + $totalOtherBonus + $totalOtherTaxable + $totalOverTime);
            $calculaImponibles = $ingresos - $totalOtherTaxableDiscount;
            return $calculaImponibles;
        }

        function calculaNoImponibles($lunch,$movilization,$totalOtherTaxableFree) {
            return $lunch + $movilization + $totalOtherTaxableFree;
        }

        function descuentosSegunMontos($afpMount,$healthMount,$dismissalInsuranceMount,$totalTaxMount,$totalOtherDiscount,$totalAdvanceDiscount) {
        return $afpMount +
        $healthMount +
        $dismissalInsuranceMount +
        $totalTaxMount +
        $totalOtherDiscount +
        $totalAdvanceDiscount;
        }
     
        function validaSueldoMinimo($edad,$imponiblesCalculados,$minimunTaxable,$minimunTaxableOverAge) {
        
          if((int)$edad < 18 || (int)$edad > 65)
             $compvalue = $minimunTaxableOverAge;
          else
             $compvalue = $minimunTaxable;

          if(!(int)$edad)
              $compvalue = $minimunTaxable;

            if (number_format($compvalue, 0, '', '') <= number_format($imponiblesCalculados, 0, '', '')) {
             return true;
            }
          return false;    
        }


        $idUsuario = session('user_id');
        if($idUsuario ==  ""){
            return view('sesion.index');
        }
        $usuarioAqua = session('user_aqua');
        $usuarioABBChile= session('user_ABB');
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
        $countContratista = 0;
        if(!empty($input["empresaContratista"])){
            $empresaContratista = $input["empresaContratista"];

            foreach ($empresaContratista as $value2) {
                $rutcontratistasR[] = $value2;
            }

            $countContratista =count($rutcontratistasR); 
        }
        $tipoBsuqueda = $input["tipoBsuqueda"];
        $centroCosto = $input["centroCosto"];
        $estadoCertificado = $input["estadoCertificado"];
        

        foreach ($empresaPrincipal as $value) {

            $rutprincipalR[] = $value;
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

            if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista != 0 AND $centroCosto != 0 AND $estadoCertificado != 0){

            $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
            ->whereIn('rut',$rutcontratistasR)
            ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
            ->where('id',$centroCosto)
            ->where('certificateState',$estadoCertificado)
            ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType'])->toArray();

            }if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista == 0 AND $centroCosto == 0 AND $estadoCertificado == 0){

                $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                 ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
                 ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType'])->toArray();

            }if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista != 0){

            $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
            ->whereIn('rut',$rutcontratistasR)
            ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
            ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType'])->toArray();

            }if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista != 0 AND $centroCosto != 0){

            $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
            ->whereIn('rut',$rutcontratistasR)
            ->where('id',$centroCosto)
            ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
            ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType'])->toArray();

            }if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista == 0 AND $centroCosto == 0 AND $estadoCertificado != 0){

                $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                 ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
                 ->where('certificateState',$estadoCertificado)
                 ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType'])->toArray();

            }

        }
        if($tipoBsuqueda == 2){

            $fechaSeleccion = $input["fechaSeleccion"];
            if($fechaSeleccion != 0  AND $countContratista != 0 AND $centroCosto != 0 AND $estadoCertificado != 0){
            
            $fechas = $porciones = explode("_", $fechaSeleccion);
            $fecha1 = $fechas[0];
            $fecha2 = $fechas[1];

            $fechasDesde =  strtotime ( '+4 hour' ,strtotime($fecha1));
            //sumo 1 día
            $fechasHasta =  strtotime ( '+4 hour' ,strtotime($fecha2));
            $empresasContratista = Contratista::distinct()->whereIn('rut',$rutcontratistasR)
            ->whereBetween('certificateDate', [$fechasDesde,$fechasHasta])
            ->where('id',$centroCosto)
            ->where('certificateState',$estadoCertificado)
            ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType'])->toArray();

            }if($fechaSeleccion != 0  AND $countContratista != 0 AND $centroCosto != 0){
            
            $fechas = $porciones = explode("_", $fechaSeleccion);
            $fecha1 = $fechas[0];
            $fecha2 = $fechas[1];
       
            $fechasDesde =  strtotime ( '+4 hour' ,strtotime($fecha1));
            //sumo 1 día
            $fechasHasta =  strtotime ( '+4 hour' ,strtotime($fecha2));
            $empresasContratista = Contratista::distinct()->whereIn('rut',$rutcontratistasR)
            ->whereBetween('certificateDate', [$fechasDesde,$fechasHasta])
            ->where('id',$centroCosto)
            ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType'])->toArray();

            }if($fechaSeleccion != 0  AND $countContratista == 0 AND $centroCosto == 0 AND $estadoCertificado == 0){
            
            $fechas = $porciones = explode("_", $fechaSeleccion);
            $fecha1 = $fechas[0];
            $fecha2 = $fechas[1];
            $fechasDesde =  strtotime ( '+4 hour' ,strtotime($fecha1));
            //sumo 1 día
            $fechasHasta =  strtotime ( '+4 hour' ,strtotime($fecha2));
            $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
            ->whereBetween('certificateDate', [$fechasDesde,$fechasHasta])
            ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType','companyTypeId'])->toArray();

            }
        }

        $cuentaEresados = 0;
        $cuentaIngresados = 0;
        $cuentaTrabajador = 0;
        foreach ($empresasContratista as $value) {

    
                $datoTrabajadores = TrabajadorVerificacion::where('mainCompanyRut',$value["mainCompanyRut"])->
                                                                 where('companyRut',$value["rut"])->
                                                                 where('periodId',$value["periodId"])->
                                                                 where('companyCenter',$value["center"])->
                                                                 get()->toArray();

               
                $cuentaTrabajador= 0;                                                
                ///egresados///
                if(!empty($datoTrabajadores)){

                    foreach ($datoTrabajadores as $datoTrabajador) {
                        $cuentaTrabajador+=1;
                        $trabajador['rutEmpleado']=$datoTrabajador['rut']."-".$datoTrabajador['dv'];
                        $trabajador['nombre']= ucwords(mb_strtolower($datoTrabajador['names'],'UTF-8')); 
                        $trabajador['apellido1']= ucwords(mb_strtolower($datoTrabajador['firstLastName'],'UTF-8'));
                        $trabajador['apellido2']= ucwords(mb_strtolower($datoTrabajador['secondLastName'],'UTF-8'));
                        $trabajador['rutPrincipal']= formatRut($value['mainCompanyRut']);
                        $trabajador['nombrePrincipal'] = ucwords(mb_strtolower($value['mainCompanyName'],'UTF-8'));

                        if((int)$value["companyTypeId"] > 1)
                        {

                            if($value["subcontratistaRut"]!=""){

                                $trabajador['RutContratista'] = $value['subcontratistaRut']."-".$value['subcontratistaDv'];
                                $trabajador['nombreContratista'] = ucwords(mb_strtolower($value['subcontratistaName'],'UTF-8'));
                                
                                $trabajador['centroCosto'] = ucwords(mb_strtolower($value['center'],'UTF-8'));
                                $trabajador['rutSubContratista'] = $value['rut']."-".$value['dv'];
                                $trabajador['subContratista'] = ucwords(mb_strtolower($value['name'],'UTF-8'));


                            }else{

                            $subcompanyRes = Solicitud::where('companyId',$value["id"])->
                                             get(['contractRut','contractName','contractDv'])->toArray();
                
                            $trabajador['RutContratista'] = $subcompanyRes[0]['contractRut']."-".$subcompanyRes[0]['contractDv'];
                            $trabajador['nombreContratista'] = ucwords(mb_strtolower($subcompanyRes[0]['contractName'],'UTF-8'));
                                
                            $trabajador['centroCosto'] = ucwords(mb_strtolower($datoTrabajador['companyCenter'],'UTF-8'));
                            $trabajador['rutSubContratista'] = formatRut($datoTrabajador['companyRut']);
                            $trabajador['subContratista'] = ucwords(mb_strtolower($datoTrabajador['companyName'],'UTF-8'));
                            

                        }
                     
                   }
                   else
                   {
                     $trabajador['RutContratista'] = formatRut($datoTrabajador['companyRut']);
                     $trabajador['nombreContratista'] = ucwords(mb_strtolower($datoTrabajador['companyName'],'UTF-8'));
                     $trabajador['centroCosto'] = ucwords(mb_strtolower($datoTrabajador['companyCenter'],'UTF-8'));
                     $trabajador['rutSubContratista'] = "";
                     $trabajador['subContratista'] = "";
                   }
                        
                        $estadoCerficacionTexto = estadoCerficacionTexto($value['certificateState']);
                        $trabajador['estadoCertificacion'] = ucwords(mb_strtolower($estadoCerficacionTexto,'UTF-8'));
                        $trabajador['fechaCertificado'] = date('d/m/Y',$value['certificateDate']);
                        $sexo = sexoTexto($datoTrabajador['sex']);
                        $trabajador['sexo'] = ucwords(mb_strtolower($sexo,'UTF-8'));
                        $trabajador['direccion'] = ucwords(mb_strtolower($datoTrabajador['address'],'UTF-8'));
                        $comuna = comunaTexto($datoTrabajador['townId']);
                        $trabajador['comuna'] = ucwords(mb_strtolower($comuna,'UTF-8'));
                        $trabajador['ciudad'] = ucwords(mb_strtolower($datoTrabajador['cityName'],'UTF-8'));
                        $region = regionTexto($datoTrabajador['regionId']);
                        $trabajador['region'] = ucwords(mb_strtolower($region,'UTF-8'));
                        $trabajador['codigoArea'] = $datoTrabajador['phoneArea'];
                        $trabajador['phoneNumber'] = $datoTrabajador['phoneNumber'];
                        $trabajador['mobileNumber'] = $datoTrabajador['mobileNumber'];
                        $trabajador['fechaNac'] = date('d/m/Y',$datoTrabajador['birthDate']);
                        $edad = calculaEdad($datoTrabajador['birthDate']);
                        $trabajador['edad'] = $edad;
                        $nacionalidad = nacionalidadTexto($datoTrabajador['birthCountryId']);
                        $trabajador['nacionalidad'] = $nacionalidad;
                        $estadoCivil = estadocivilTexto($datoTrabajador['maritalStatusId']);
                        $trabajador['estadoCivil'] = $estadoCivil;
                        $educacion = educacionTexto($datoTrabajador['educationLevelId']);
                        $trabajador['nivelEducacion'] = $educacion;
                        $jubilado = jubiladoTexto($datoTrabajador['retired']);
                        $trabajador['jubilado'] = $jubilado;
                        $credencial = credencialTexto($datoTrabajador['hasCredential']);
                        $trabajador['credencial'] = $credencial;
                        $trabajador['numeroCredencial'] = $datoTrabajador['credentialNumber'];
                        $estadoCredencial = estadoCredencialTexto($datoTrabajador['credentialStatusId']);
                        $trabajador['estadoCredencial'] = $estadoCredencial;
                        if($datoTrabajador['credentialEndDate']!=""){
                            $trabajador['FechaVenCredencial'] = date('d/m/Y',$datoTrabajador['credentialEndDate']);
                        }else{
                           $trabajador['FechaVenCredencial'] =""; 
                        }
                        $credencialReq = credencialReqTexto($datoTrabajador['credentialRequire']);
                        $trabajador['requiereCredencial'] = $credencialReq;
                        $trabajador['cargo'] = ucwords(mb_strtolower($datoTrabajador['position'],'UTF-8'));
                        $tipoContrato = tipoContrato($datoTrabajador['contractTypeId']);
                        $trabajador['tipoContrato'] = $tipoContrato;
                        $tipoRenta = tipoRenta($datoTrabajador['incomeTypeId']);
                        $trabajador['tipoRenta'] = $tipoRenta;
                        $trabajador['fechaInicioCon'] = date('d/m/Y',$datoTrabajador['beginDate']);
                        if($datoTrabajador['endDate']!=""){
                         $trabajador['fechaTerminoConPF'] = date('d/m/Y',$datoTrabajador['endDate']);
                        }else{
                         $trabajador['fechaTerminoConPF'] = "";    
                        }
                        if($datoTrabajador['annexeDate']!=""){
                         $trabajador['fechaAnexo'] = $datoTrabajador['annexeDate'];
                        }else{
                         $trabajador['fechaAnexo'] = "";    
                        }
                        
                        $trabajador['turno'] = $datoTrabajador['workShift'];
                        $trabajador['lugarTrabajo'] = $datoTrabajador['workPlace'];
                        $estadoSindicato = estadoSindicato($datoTrabajador['unionStatusId']);
                        $trabajador['estadoSindicato'] = $estadoSindicato;
                        $trabajador['nombreSindicato'] = ucwords(mb_strtolower($datoTrabajador['unionName'],'UTF-8'));
                        $licencia = licenciaTexto($datoTrabajador['drivingLicence']);
                        $trabajador['licencia'] = $licencia;
                        $trabajador['tipolicencia'] = $datoTrabajador['licenceType'];
                        if($datoTrabajador['licenceEndDate']!=""){
                            $trabajador['fechaLicencia'] = date('d/m/Y',$datoTrabajador['licenceEndDate']);
                        }else{
                            $trabajador['fechaLicencia'] = "";   
                        }
                        $trabajador['patente'] = $datoTrabajador['vehiclePlate'];
                        $trabajador['modeloCarro'] = $datoTrabajador['vehicleModel'];
                        $recibeCharla = textoCharla($datoTrabajador['talkReception']);
                        $trabajador['recibeCharla'] = $recibeCharla;
                        $trabajador['fechaCharla'] = date('d/m/Y',$datoTrabajador['talkDate']);
                        if($datoTrabajador['settlementDate']!=""){
                            $trabajador['fechaFiniquito'] = date('d/m/Y',$datoTrabajador['settlementDate']);
                        }else{
                            $trabajador['fechaFiniquito'] = "";
                        }
                       
                        $desvinculacion = textoDesvinculacion($datoTrabajador['dismissalTypeId']);
                        $trabajador['tipoDesvinculacion'] = $desvinculacion;
                        $cartaAviso= textoCarta($datoTrabajador['warningLetter']);
                        $trabajador['cartaAviso'] = $cartaAviso;
                        $trabajador['causalDespido'] = ucwords(mb_strtolower($datoTrabajador['dismissalCausal']));
                        $trabajador['indemnisacionS'] = $datoTrabajador['settlementMount'];
                        $trabajador['indemnisacionA'] = $datoTrabajador['settlementYearsMount'];
                        $trabajador['totalOtraIndemnisacion'] = $datoTrabajador['totalOtherSettlement'];
                        $tipoJornada = textoJornada($datoTrabajador['workingTypeId']);
                        $trabajador['tipoJornada'] = $tipoJornada;
                        $trabajador['jornadaSemanal'] = $datoTrabajador['workingWeek'];
                        $trabajador['diasTrabajo'] = $datoTrabajador['workingDays'];
                        $trabajador['diasTrabajoMandante'] = $datoTrabajador['workingDaysMainCompany'];
                        $trabajador['horario'] = $datoTrabajador['timeTable'];
                        $trabajador['sueldoBase'] = $datoTrabajador['baseSalary'];
                        $trabajador['gratificacion'] = $datoTrabajador['incentive'];
                        $trabajador['bonoProduccion'] = $datoTrabajador['productionBonus'];
                        $trabajador['bonoTrato'] = $datoTrabajador['treatmentBonus'];
                        $trabajador['totalOtroBono'] = $datoTrabajador['totalOtherBonus'];
                        $trabajador['totalOtroImponibles'] = $datoTrabajador['totalOtherTaxable'];
                        $trabajador['totalDescImponibles'] = $datoTrabajador['totalOtherTaxableDiscount'];
                        $trabajador['baseCalHE'] = $datoTrabajador['calculusBase'];
                        $trabajador['horasExtrasReg'] = $datoTrabajador['overtimeRecorded'];
                        $trabajador['horasExtrasPaga'] = $datoTrabajador['overtimePaided'];
                        $trabajador['montoPagadoHE'] = $datoTrabajador['totalOverTime'];
                        $trabajador['totalImponible'] = $datoTrabajador['totalTaxable'];
                        $trabajador['colacion'] = $datoTrabajador['lunch'];
                        $trabajador['movilizacion'] = $datoTrabajador['movilization'];
                        $trabajador['totalOtroNoImponibles'] = $datoTrabajador['totalOtherTaxableFree'];
                        $trabajador['totalNoImponibles'] = $datoTrabajador['totalTaxableFree'];
                        $fondoPension = textoAFP($datoTrabajador['afpId']);
                        $trabajador['fondoPension'] = $fondoPension;
                        $trabajador['montoAFP'] = $datoTrabajador['afpMount'];
                        $salud = textoISAPRE($datoTrabajador['healthId']);
                        $trabajador['salud'] = $salud;
                        $trabajador['montoSalud'] = $datoTrabajador['healthMount'];
                        $seguroCesantia = textoAFP($datoTrabajador['afpId']);
                        $trabajador['seguroCesantia'] = $seguroCesantia;
                        $trabajador['montoCesantia'] = $datoTrabajador['dismissalInsuranceMount'];
                        $trabajador['procentajeImpuesto'] = $datoTrabajador['totalTaxPercentage'];
                        $trabajador['totalImpuesto'] = $datoTrabajador['totalTaxMount'];
                        $trabajador['totalAnticipo'] = $datoTrabajador['totalAdvanceDiscount'];
                        $trabajador['totalOtrosDesc'] = $datoTrabajador['totalOtherDiscount'];
                        $trabajador['totalDescuentos'] = $datoTrabajador['totalDiscount'];
                        $trabajador['totalHaberes'] = $datoTrabajador['totalIncome'];
                        $trabajador['pagoLiquido'] = $datoTrabajador['totalHomePay'];
                        
                        $horasExtrasCalculadas = calculaHoraExtras($datoTrabajador['calculusBase'],$datoTrabajador['baseSalary'],$datoTrabajador['overtimeRecorded']);

                        $trabajador['horasExtrasCalculadas'] = $horasExtrasCalculadas;

                        $imponiblesCalculados = calculaImponibles(
                            $datoTrabajador['baseSalary'],
                            $datoTrabajador['incentive'],
                            $datoTrabajador['productionBonus'],
                            $datoTrabajador['treatmentBonus'],
                            $datoTrabajador['totalOtherBonus'],
                            $datoTrabajador['totalOtherTaxable'],
                            $datoTrabajador['totalOverTime'],
                            $datoTrabajador['totalOtherTaxableDiscount']
                        );

                        $trabajador['imponiblesCalculados'] = $imponiblesCalculados;

                        $imponiblesNoCalculados = calculaNoImponibles(
                            $datoTrabajador['lunch'],
                            $datoTrabajador['movilization'],
                            $datoTrabajador['totalOtherTaxableFree']
                        );

                        $trabajador['imponiblesNoCalculados'] = $imponiblesNoCalculados;

                        $descuentosSegunMontos = descuentosSegunMontos(
                            $datoTrabajador['afpMount'],
                            $datoTrabajador['healthMount'],
                            $datoTrabajador['dismissalInsuranceMount'],
                            $datoTrabajador['totalTaxMount'],
                            $datoTrabajador['totalOtherDiscount'],
                            $datoTrabajador['totalAdvanceDiscount']
                        );
                    
                        $trabajador['descuentosSegunMontos'] = $descuentosSegunMontos;

                        $haberesCalculados = $imponiblesNoCalculados + $imponiblesCalculados;
                        $trabajador['haberesCalculados'] = $haberesCalculados;
                        $pagoLiquidoCalculado = $haberesCalculados - $descuentosSegunMontos;
                        $trabajador['pagoLiquidoCalculado'] = $pagoLiquidoCalculado;
                        $datosPeriodo = Periodo::where('id', $value['periodId'])->get(['minimunTaxable', 'minimunTaxableOverAge'])->toArray();
                        $cumpleIngresoMen = validaSueldoMinimo($edad,$imponiblesCalculados,$datosPeriodo[0]['minimunTaxable'],$datosPeriodo[0]['minimunTaxableOverAge']);
                            if($cumpleIngresoMen)
                           {
                              $cumple ="Cumple" ;
                             
                           }
                           else
                           {
                              $cumple = "No Cumple";
                              
                           }
                        $trabajador['cumpleIngresoMen'] = $cumple;
                        $trabajador['sindicato'] = $datoTrabajador['sindicato'];
                        $trabajador['porcentajeSindicalizado'] = $datoTrabajador['syndicatePercent'];
                        if($datoTrabajador['nextNegociationDate']!=""){
                            $trabajador['fechaNegociacionColectiva'] = date('d/m/Y',$datoTrabajador['nextNegociationDate']);
                        }else{
                            $trabajador['fechaNegociacionColectiva'] = "";
                        }
                        $sujetoLicencia = textoSujetoLicencia($datoTrabajador['workerLicense']);
                        $trabajador['sujetoLicencia'] = $sujetoLicencia;
                        if($datoTrabajador['startLicenseDate']!=""){
                            $trabajador['fechaIniLincencia'] = date('d/m/Y',$datoTrabajador['startLicenseDate']);
                        }else{
                           $trabajador['fechaIniLincencia'] ="";
                        }
                        if($datoTrabajador['endLicenseDate']!=""){
                            $trabajador['fechaFinLincencia'] = date('d/m/Y',$datoTrabajador['endLicenseDate']);
                        }else{
                           $trabajador['fechaFinLincencia'] ="";
                        }
                        $trabajador['causaFinLic'] = $datoTrabajador['reasonEndLicense'];
                        $discapacidad = textoSujetoLicencia($datoTrabajador['discapacidad']);
                        $trabajador['discapacidad'] = $discapacidad;
                        $rnd = textoSujetoLicencia($datoTrabajador['rnd']);
                        $trabajador['rnd'] = $rnd;
                        $listaTrabajador[] = $trabajador;
                    }
                }
        }


        if(!empty($listaTrabajador)){
            $listaTitulos="<thead>
                    <tr>
                      <th>RUT</th>
                      <th>Nombre</th>
                      <th>Apellido Paterno</th>
                      <th>Apellido Materno</th>
                      <th>RUT Principal</th>
                      <th>Principal</th>
                      <th>RUT Contratista</th>
                      <th>Contratista</th>
                      <th>Centro de Costo</th>
                      <th>RUT Sub Contratista</th>
                      <th>RUT Sub Contratista</th>
                      <th>Estado Certificación</th>
                      <th>Fecha Certificación</th>
                      <th>Sexo</th>
                      <th>dirección</th>
                      <th>Comuna</th>
                      <th>Ciudad</th>
                      <th>Región</th>
                      <th>Codigo de Area</th>
                      <th>Telefono Particular</th>
                      <th>Telefono Movil</th>
                      <th>Fecha Nacimiento</th>
                      <th>Edad</th>
                      <th>Nacionalidad</th>
                      <th>Estado Civil</th>
                      <th>Nivel de Educación</th>
                      <th>Jubilado</th>
                      <th>Credencial</th>
                      <th>N° Credencial</th>
                      <th>Estado Credencial</th>
                      <th>Fecha Vencimento Credencial</th>
                      <th>Requiere Credencial</th>
                      <th>Cargo</th>
                      <th>Tipo Contrato</th>
                      <th>Tipo Renta</th>
                      <th>Fecha Inicio Contrato</th>
                      <th>Fecha Termino Contrato Plazo Fijo Contrato</th>
                      <th>Fecha Anexo</th>
                      <th>Turno</th>
                      <th>Lugar de Trabajo</th>
                      <th>Estado Sindicato</th>
                      <th>Nombre Sindicato</th>
                      <th>Licencia de Conducir</th>
                      <th>Tipo de Licencia</th>
                      <th>Fecha de Vencimiento Licencia</th>
                      <th>Patente</th>
                      <th>Modelo Auto</th>
                      <th>Recibe Charla</th>
                      <th>Fecha Charla</th>
                      <th>Fecha Finiquito</th>
                      <th>Tipo de Desvínculo</th>
                      <th>Carta de Aviso</th>
                      <th>Causal de Despido</th>
                      <th>Indemnización Sustitutiva</th>
                      <th>Indemnizacion por Años de Servicio</th>
                      <th>Total Otras Indemnizaciones</th>
                      <th>Tipo de Jornada</th>
                      <th>Jornada Semanal</th>
                      <th>Días Trabajados en el Mes</th>
                      <th>Días Reales Trabajados para la Mandante</th>
                      <th>Horario</th>
                      <th>Sueldo Base</th>
                      <th>Gratificación</th>
                      <th>Bono de Producción</th>
                      <th>Bono de Trato</th>
                      <th>Total Otros Bonos</th>
                      <th>Total Otros Imponibles</th>
                      <th>Total Descuentos Imponibles</th>
                      <th>Base de Cálculo Hora Extra (%)</th>
                      <th>Nº Horas Extra Registradas</th>
                      <th>Nº Horas Extra Pagadas</th>
                      <th>Monto Pagado por Horas Extra</th>
                      <th>Total Imponible</th>
                      <th>Colación</th>
                      <th>Movilización</th>
                      <th>Total Otros No Imponibles</th>
                      <th>Total No Imponible</th>
                      <th>Fondo de Pensión</th>
                      <th>Monto de Fondo de Pensión</th>
                      <th>Salud</th>
                      <th>Monto de Salud</th>
                      <th>Seguro de Cesantía</th>
                      <th>Monto de Seguro de Cesantía</th>
                      <th>Total Porcentaje de Impuestos</th>
                      <th>Total Monto de Impuestos</th>
                      <th>Total Anticipos</th>
                      <th>Total Otros Descuentos</th>
                      <th>Total Descuentos</th>
                      <th>Total Haberes</th>
                      <th>Pago Líquido</th>
                      <th>Horas Extras Calculado</th>
                      <th>Imponibles Calculado</th>
                      <th>No Imponibles Calculado</th>
                      <th>Descuentos Calculado Según Montos</th>
                      <th>Haberes Calculado</th>
                      <th>Pago Líquido Calculado</th>
                      <th>Ingreso Mensual Mínimo</th>
                      <th>sindicato</th>
                      <th>Porcentaje sindicalización</th>
                      <th>Fecha próxima negociación colectiva</th>
                      <th>Trabajador sujeto a licencia</th>
                      <th>Fecha inicio licencia</th>
                      <th>Fecha termino licencia</th>
                      <th>Causa termino licencia</th>
                      <th>Posee Discapacidad</th>
                      <th>Inscrito en Registro Nacional de Discapacidad</th>
                    </tr>
                    </thead>";
            $listaCuerpo ="";
            foreach ($listaTrabajador as $trabajador) {
                
                $listaCuerpo.= "<tr>";
                $listaCuerpo.= "<td>".$trabajador['rutEmpleado']."</td>";
                $listaCuerpo.= "<td>".$trabajador['nombre']."</td>";
                $listaCuerpo.= "<td>".$trabajador['apellido1']."</td>";
                $listaCuerpo.= "<td>".$trabajador['apellido2']."</td>";
                $listaCuerpo.= "<td>".$trabajador['rutPrincipal']."</td>";
                $listaCuerpo.= "<td>".$trabajador['nombrePrincipal']."</td>";
                $listaCuerpo.= "<td>".$trabajador['RutContratista']."</td>";
                $listaCuerpo.= "<td>".$trabajador['nombreContratista']."</td>";
                $listaCuerpo.= "<td>".$trabajador['centroCosto']."</td>";
                $listaCuerpo.= "<td>".$trabajador['rutSubContratista']."</td>";
                $listaCuerpo.= "<td>".$trabajador['subContratista']."</td>";
                $listaCuerpo.= "<td>".$trabajador['estadoCertificacion']."</td>";
                $listaCuerpo.= "<td>".$trabajador['fechaCertificado']."</td>";
                $listaCuerpo.= "<td>".$trabajador['sexo']."</td>";
                $listaCuerpo.= "<td>".$trabajador['direccion']."</td>";
                $listaCuerpo.= "<td>".$trabajador['comuna']."</td>";
                $listaCuerpo.= "<td>".$trabajador['ciudad']."</td>";
                $listaCuerpo.= "<td>".$trabajador['region']."</td>";
                $listaCuerpo.= "<td>".$trabajador['codigoArea']."</td>";
                $listaCuerpo.= "<td>".$trabajador['phoneNumber']."</td>";
                $listaCuerpo.= "<td>".$trabajador['mobileNumber']."</td>";
                $listaCuerpo.= "<td>".$trabajador['fechaNac']."</td>";
                $listaCuerpo.= "<td>".$trabajador['edad']."</td>";
                $listaCuerpo.= "<td>".$trabajador['nacionalidad']."</td>";
                $listaCuerpo.= "<td>".$trabajador['estadoCivil']."</td>";
                $listaCuerpo.= "<td>".$trabajador['nivelEducacion']."</td>";
                $listaCuerpo.= "<td>".$trabajador['jubilado']."</td>";
                $listaCuerpo.= "<td>".$trabajador['credencial']."</td>";
                $listaCuerpo.= "<td>".$trabajador['numeroCredencial']."</td>";
                $listaCuerpo.= "<td>".$trabajador['estadoCredencial']."</td>";
                $listaCuerpo.= "<td>".$trabajador['FechaVenCredencial']."</td>";
                $listaCuerpo.= "<td>".$trabajador['requiereCredencial']."</td>";
                $listaCuerpo.= "<td>".$trabajador['cargo']."</td>";
                $listaCuerpo.= "<td>".$trabajador['tipoContrato']."</td>";
                $listaCuerpo.= "<td>".$trabajador['tipoRenta']."</td>";
                $listaCuerpo.= "<td>".$trabajador['fechaInicioCon']."</td>";
                $listaCuerpo.= "<td>".$trabajador['fechaTerminoConPF']."</td>";
                $listaCuerpo.= "<td>".$trabajador['fechaAnexo']."</td>";
                $listaCuerpo.= "<td>".$trabajador['turno']."</td>";
                $listaCuerpo.= "<td>".$trabajador['lugarTrabajo']."</td>";
                $listaCuerpo.= "<td>".$trabajador['estadoSindicato']."</td>";
                $listaCuerpo.= "<td>".$trabajador['nombreSindicato']."</td>";
                $listaCuerpo.= "<td>".$trabajador['licencia']."</td>";
                $listaCuerpo.= "<td>".$trabajador['tipolicencia']."</td>";
                $listaCuerpo.= "<td>".$trabajador['fechaLicencia']."</td>";
                $listaCuerpo.= "<td>".$trabajador['patente']."</td>";
                $listaCuerpo.= "<td>".$trabajador['modeloCarro']."</td>";
                $listaCuerpo.= "<td>".$trabajador['recibeCharla']."</td>";
                $listaCuerpo.= "<td>".$trabajador['fechaCharla']."</td>";
                $listaCuerpo.= "<td>".$trabajador['fechaFiniquito']."</td>";
                $listaCuerpo.= "<td>".$trabajador['tipoDesvinculacion']."</td>";
                $listaCuerpo.= "<td>".$trabajador['cartaAviso']."</td>";
                $listaCuerpo.= "<td>".$trabajador['causalDespido']."</td>";
                $listaCuerpo.= "<td>".$trabajador['indemnisacionS']."</td>";
                $listaCuerpo.= "<td>".$trabajador['indemnisacionA']."</td>";
                $listaCuerpo.= "<td>".$trabajador['totalOtraIndemnisacion']."</td>";
                $listaCuerpo.= "<td>".$trabajador['tipoJornada']."</td>";
                $listaCuerpo.= "<td>".$trabajador['jornadaSemanal']."</td>";
                $listaCuerpo.= "<td>".$trabajador['diasTrabajo']."</td>";
                $listaCuerpo.= "<td>".$trabajador['diasTrabajoMandante']."</td>";
                $listaCuerpo.= "<td>".$trabajador['horario']."</td>";
                $listaCuerpo.= "<td>".$trabajador['sueldoBase']."</td>";
                $listaCuerpo.= "<td>".$trabajador['gratificacion']."</td>";
                $listaCuerpo.= "<td>".$trabajador['bonoProduccion']."</td>";
                $listaCuerpo.= "<td>".$trabajador['bonoTrato']."</td>";
                $listaCuerpo.= "<td>".$trabajador['totalOtroBono']."</td>";
                $listaCuerpo.= "<td>".$trabajador['totalOtroImponibles']."</td>";
                $listaCuerpo.= "<td>".$trabajador['totalDescImponibles']."</td>";
                $listaCuerpo.= "<td>".$trabajador['baseCalHE']."</td>";
                $listaCuerpo.= "<td>".$trabajador['horasExtrasReg']."</td>";
                $listaCuerpo.= "<td>".$trabajador['horasExtrasPaga']."</td>";
                $listaCuerpo.= "<td>".$trabajador['montoPagadoHE']."</td>";
                $listaCuerpo.= "<td>".$trabajador['totalImponible']."</td>";
                $listaCuerpo.= "<td>".$trabajador['colacion']."</td>";
                $listaCuerpo.= "<td>".$trabajador['movilizacion']."</td>";
                $listaCuerpo.= "<td>".$trabajador['totalOtroNoImponibles']."</td>";
                $listaCuerpo.= "<td>".$trabajador['totalNoImponibles']."</td>";
                $listaCuerpo.= "<td>".$trabajador['fondoPension']."</td>";
                $listaCuerpo.= "<td>".$trabajador['montoAFP']."</td>";
                $listaCuerpo.= "<td>".$trabajador['salud']."</td>";
                $listaCuerpo.= "<td>".$trabajador['montoSalud']."</td>";
                $listaCuerpo.= "<td>".$trabajador['seguroCesantia']."</td>";
                $listaCuerpo.= "<td>".$trabajador['montoCesantia']."</td>";
                $listaCuerpo.= "<td>".$trabajador['procentajeImpuesto']."</td>";
                $listaCuerpo.= "<td>".$trabajador['totalImpuesto']."</td>";
                $listaCuerpo.= "<td>".$trabajador['totalAnticipo']."</td>";
                $listaCuerpo.= "<td>".$trabajador['totalOtrosDesc']."</td>";
                $listaCuerpo.= "<td>".$trabajador['totalDescuentos']."</td>";
                $listaCuerpo.= "<td>".$trabajador['totalHaberes']."</td>";
                $listaCuerpo.= "<td>".$trabajador['pagoLiquido']."</td>";
                

                if (number_format($trabajador['montoPagadoHE'], 0, '', '') === number_format($trabajador['horasExtrasCalculadas'], 0, '', '')) {
                 $listaCuerpo.= "<td bgcolor='green'>".$trabajador['horasExtrasCalculadas']."</td>";
                }else{
                 $listaCuerpo.= "<td bgcolor='red'>".$trabajador['horasExtrasCalculadas']."</td>";    
                }

                if (number_format($trabajador['totalImponible'], 0, '', '') === number_format($trabajador['imponiblesCalculados'], 0, '', '')) {
                 $listaCuerpo.= "<td bgcolor='green'>".$trabajador['imponiblesCalculados']."</td>";
                }else{
                 $listaCuerpo.= "<td bgcolor='red'>".$trabajador['imponiblesCalculados']."</td>";    
                }

                if (number_format($trabajador['totalNoImponibles'], 0, '', '') === number_format($trabajador['imponiblesNoCalculados'], 0, '', '')) {
                 $listaCuerpo.= "<td bgcolor='green'>".$trabajador['imponiblesNoCalculados']."</td>";
                }else{
                 $listaCuerpo.= "<td bgcolor='red'>".$trabajador['imponiblesNoCalculados']."</td>";    
                }

                if (number_format($trabajador['totalDescuentos'], 0, '', '') === number_format($trabajador['descuentosSegunMontos'], 0, '', '')) {
                 $listaCuerpo.= "<td bgcolor='green'>".$trabajador['descuentosSegunMontos']."</td>";
                }else{
                 $listaCuerpo.= "<td bgcolor='red'>".$trabajador['descuentosSegunMontos']."</td>";    
                }

                if (number_format($trabajador['totalHaberes'], 0, '', '') === number_format($trabajador['haberesCalculados'], 0, '', '')) {
                 $listaCuerpo.= "<td bgcolor='green'>".$trabajador['haberesCalculados']."</td>";
                }else{
                 $listaCuerpo.= "<td bgcolor='red'>".$trabajador['haberesCalculados']."</td>";    
                }

                if (number_format($trabajador['pagoLiquido'], 0, '', '') === number_format($trabajador['pagoLiquidoCalculado'], 0, '', '')) {
                 $listaCuerpo.= "<td bgcolor='green'>".$trabajador['pagoLiquidoCalculado']."</td>";
                }else{
                 $listaCuerpo.= "<td bgcolor='red'>".$trabajador['pagoLiquidoCalculado']."</td>";    
                }
                $listaCuerpo.= "<td>".$trabajador['cumpleIngresoMen']."</td>";
                $listaCuerpo.= "<td>".$trabajador['sindicato']."</td>";
                $listaCuerpo.= "<td>".$trabajador['porcentajeSindicalizado']."</td>";
                $listaCuerpo.= "<td>".$trabajador['fechaNegociacionColectiva']."</td>";
                $listaCuerpo.= "<td>".$trabajador['sujetoLicencia']."</td>";
                $listaCuerpo.= "<td>".$trabajador['fechaIniLincencia']."</td>";
                $listaCuerpo.= "<td>".$trabajador['fechaFinLincencia']."</td>";
                $listaCuerpo.= "<td>".$trabajador['causaFinLic']."</td>";
                $listaCuerpo.= "<td>".$trabajador['discapacidad']."</td>";
                $listaCuerpo.= "<td>".$trabajador['rnd']."</td>";
                $listaCuerpo.= "</tr>";
              
            }
        } 

        return view('reporteCompleto.index',compact('EmpresasP','periodos','datosUsuarios','etiquetasEstados','valoresEstados','certificacion','listaTitulos','listaCuerpo','cuentaTrabajador','usuarioAqua','usuarioABBChile','usuarioNOKactivo'));
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
