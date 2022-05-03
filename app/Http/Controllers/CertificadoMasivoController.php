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
use App\categoriaServicio;
use App\direccion;
use App\gerencia;
use App\EstadoCargaMasiva;
use App\DocumentoRechazdo;
use App\CertificateHistory;
use Illuminate\Http\Request;

class CertificadoMasivoController extends Controller
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
        $usuarioABBChile= session('user_ABB');
        $usuarioClaroChile= session('user_Claro');
        $usuarioNOKactivo = session('usuario_nok');
        $certificacion = session('certificacion');
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
        return view('CertificadoMasivo.index',compact('EmpresasP','periodos','datosUsuarios','etiquetasEstados','valoresEstados','certificacion','periodosT','principalesTexto','usuarioAqua','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile'));
    
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
        $config['server_certmassive_folder'] = "C:/CertilapSysFiles_massivepdf/";
        $tiempo = time();
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


            if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista != 0){

            $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
            ->whereIn('rut',$rutcontratistasR)
            ->whereIn('certificateState',[5,10])
            ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
            ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv'])->toArray();

            }
            if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista == 0){

                $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
                ->whereIn('certificateState',[5,10])
                ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv'])->toArray();
            }
        }if($tipoBsuqueda == 2){

            $fechaSeleccion = $input["fechaSeleccion"];
            if($fechaSeleccion != 0  AND $countContratista != 0){
            
            $fechas = $porciones = explode("_", $fechaSeleccion);
            $fecha1 = $fechas[0];
            $fecha2 = $fechas[1];
            $periodosT = $fecha1 ."-".$fecha2;
            $fechasDesde = strtotime ( '+1 day' ,strtotime($fecha1));
            //sumo 1 día
            $fechasHasta = strtotime ( '+1 day' ,strtotime($fecha2));
            $empresasContratista = Contratista::select('id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv')->whereIn('mainCompanyRut',$rutprincipalR)
            ->whereIn('rut',$rutcontratistasR)
            ->whereIn('certificateState',[5,10])
            ->whereBetween('certificateDate', [$fechasDesde,$fechasHasta])
            ->orderBy('id', 'ASC')->get()->toArray();

            }
            if($fechaSeleccion != 0  AND $countContratista == 0){
            
            $fechas = $porciones = explode("_", $fechaSeleccion);
            $fecha1 = $fechas[0];
            $fecha2 = $fechas[1];
            $periodosT = $fecha1 ."-".$fecha2;
            $fechasDesde = strtotime ( '+1 day' ,strtotime($fecha1));
            //sumo 1 día
            $fechasHasta = strtotime ( '+1 day' ,strtotime($fecha2));
            
            $empresasContratista = Contratista::whereIn('mainCompanyRut',$rutprincipalR)
            ->whereBetween('certificateDate', [$fechasDesde,$fechasHasta])
            ->whereIn('certificateState',[5,10])
            ->orderBy('id', 'ASC')->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType','servicioId','classserv','adminContrato'])->toArray();

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
            return $rut.$dv;
        }
        
        if(!empty($empresasContratista)){
          
 
        
            foreach ($empresasContratista as $contratista) {
 
                $certificados = Certificado::where('companyId',$contratista['id'])->orderBy('id', 'ASC')->get()->toArray();
               
                $datos["numeroCertificado"]=$certificados[0]['number']."-".$certificados[0]['serial'];
                $datos["empresaPrincipalRut"]=formatRut($contratista['mainCompanyRut']);
                $datos["empresaPrincipal"]=ucwords(mb_strtolower($contratista['mainCompanyName'],'UTF-8'));
                $datos["empresaContratistaRut"]=$contratista['rut']."-".$contratista['dv'];
                $datos["empresaContratista"]=ucwords(mb_strtolower($contratista['name'],'UTF-8'));
                $datos["empresaSubContratistaRut"]=$contratista['subcontratistaRut']."-".$contratista['subcontratistaDv'];
                $datos["empresaSubContratista"]=ucwords(mb_strtolower($contratista['subcontratistaName'],'UTF-8'));
                $datos["centroCosto"]=ucwords(mb_strtolower($contratista['center'],'UTF-8'));
                $datos["idCompany"]=$contratista['id'];
                $datosVista[] = $datos;

            }

           // print_r($datosVista);
       
            if(!empty($datosVista)){
            $cantidaDatos = count($datosVista);
            }else{
                $cantidaDatos = 0;
            }
            //echo $cantidaDatos;
            return view('certificadoMasivo.index',compact('EmpresasP','datosUsuarios','certificacion','usuarioAqua','datosVista','cantidaDatos','usuarioABBChile','periodos','usuarioNOKactivo','usuarioClaroChile'));
           
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

        $destdir = $config['server_certmassive_folder'].$empresasContratista[0]["mainCompanyName"].$tiempo;
            mkdir($destdir, 0777, true);
            $serial = "";
            $header = Array("User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36",
                      "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5",
                      "Accept-Encoding: text",
                      "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7",
                      "Date: ".date(DATE_RFC822));


      //----------------------------------------------------------------------------------
      
         $_POSTFIELDS = Array("username"  => "amoyac1",
                           "password"  => "654321");
          $curl = curl_init();
          curl_setopt($curl, CURLOPT_URL,"http://sistema.certilapchile.cl/index.php?aa=login&cn=login");
          curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 600);
          curl_setopt($curl, CURLOPT_TIMEOUT, 600);
          curl_setopt($curl, CURLE_OPERATION_TIMEOUTED, 600);
          curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
          curl_exec($curl);
           
          //----------------------------------------------------------------------------------
          curl_setopt($curl, CURLOPT_URL, "http://sistema.certilapchile.cl/index.php?aa=login&cn=loginSubmit");
          curl_setopt($curl, CURLOPT_POST ,1);
          curl_setopt($curl, CURLOPT_POSTFIELDS, $_POSTFIELDS);
          curl_exec($curl);


          //----------------------------------------------------------------------------------
          curl_setopt($curl, CURLOPT_URL, "http://sistema.certilapchile.cl/index.php?aa=certilap&cn=home");
          curl_setopt($curl, CURLOPT_POST ,0);
          $salida = curl_exec($curl);

          print_r($salida);

        $rutContratista = $contratista["rut"];
                if($rutContratista <= 10000000){
                    $rutContratistaD = str_pad($rutContratista, 8, "0", STR_PAD_LEFT);
                
                }else{
                     $rutContratistaD = $rutContratista;
                }

                $rutPrincipal = $contratista["mainCompanyRut"];
                if($rutPrincipal <= 10000000){

                    $rutPrincipalD = str_pad($rutPrincipal, 8, "0", STR_PAD_LEFT);
                }else{

                    $rutPrincipalD = $rutPrincipal;
                }
                $periodoid = $contratista["periodId"];

                $datosPeriodo = Periodo::where('id', $periodoid)->get(['id', 'monthId','year'])->toArray();

                if($datosPeriodo[0]["monthId"]==10 or $datosPeriodo[0]["monthId"]==11 or $datosPeriodo[0]["monthId"]==12){
                    $fechaNoDoc = $datosPeriodo[0]["monthId"].$datosPeriodo[0]["year"];
                }else{
                    $fechaNoDoc = "0".$datosPeriodo[0]["monthId"].$datosPeriodo[0]["year"];
                }

                if ($serial == 1){
                    $letra = "N";
                }else{
                    $letra = "C";
                }

                $ncontrato = substr($contratista["center"],0,3);
                $rutprincipaldv = formatRut($rutPrincipalD);
                $nombreCertificado = $rutprincipaldv.$rutContratistaD.$contratista["dv"]."C".$fechaNoDoc.$letra.$ncontrato.$contratista["id"].".pdf";
            
                $url = "http://sistema.certilapchile.cl/index.php?aa=pdf&cn=certificate&di=".$contratista['id'];
                echo $url;
                //exit();
                curl_setopt($curl, CURLOPT_URL,$url);
                curl_setopt($curl, CURLOPT_POST ,0);
                $output = curl_exec($curl);
                print_r($output);
                exit();;
                header('Content-Description: File Transfer');
                header('Content-type:application/pdf');
                header('Content-Disposition: attachment; filename="' . basename($output) . '"');
                header('Expires: 0');
                header('Cache-Control: must-rSomething is wrongidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($output));
                readfile($output);
               
                file_put_contents($destdir."/".$nombreCertificado, $output);
                chmod($destdir."/".$nombreCertificado, 0777);    
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
