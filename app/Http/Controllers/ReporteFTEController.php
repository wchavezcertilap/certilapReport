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
use App\direccion;
use App\gerencia;
use App\EstadoCargaMasiva;
use App\DocumentoRechazdo;
use Illuminate\Http\Request;

class ReporteFTEController extends Controller
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
        $etiquetasEstados = 0;
        $valores = 0;
        return view('reporteFTE.index',compact('EmpresasP','periodos','datosUsuarios','etiquetasEstados','valores','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile'));
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

        function periodoTexto($idPerido){

            $periodo = DB::table('Period')
            ->join('Month', 'Month.id', '=', 'Period.monthId')
            ->where(['Period.id' => $idPerido])
            ->select('Period.year','Month.name')
            ->get();

            if(!empty($periodo[0])){
                return $periodo[0]->name."-".$periodo[0]->year;
            }else{
                return "N_A";
            }

           
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
        //// captura de datos ////
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
        //$tipoBsuqueda = 1;
        $centroCosto = $input["centroCosto"];
        
        foreach ($empresaPrincipal as $value) {

            $rutprincipalR[] = $value;
        }
        // cuando selecciona todas las empresas principales //
        if($rutprincipalR[0]==1){

            if($datosUsuarios->type == 3){

                $rutprincipalRC = empresaPrincipal::distinct()->whereIn('rut',$rutprincipal)->orderBy('name', 'ASC')->get(['name','rut'])->toArray();
            }
            if($datosUsuarios->type ==2 || $datosUsuarios->type ==1 ){

                $rutprincipalRC = empresaPrincipal::distinct()->orderBy('rut', 'ASC')->get(['name','rut'])->toArray();
            }

            $rutprincipalRL = super_unique($rutprincipalRC,'rut');

            foreach ($rutprincipalRL as $value) {
            $rutprincipalR[] = $value['rut'];
            }

        }

        //busquedas de trabajadores para iniciar calculos
        if($tipoBsuqueda == 1){

            $peridoInicioF = $input["peridoInicio"];
            $peridoFinalF = $input["peridoFinal"];
          

            if($peridoInicioF != 0 AND $peridoFinalF != 0 AND $countContratista != 0 AND $centroCosto != 0){

            $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
            ->whereIn('rut',$rutcontratistasR)
            ->whereIn('certificateState',[3,4,5,6,8,9,10])
            ->whereBetween('periodId', [$peridoInicioF,$peridoFinalF])
            ->where('id',$centroCosto)
            ->orderBy('mainCompanyRut', 'ASC')
            ->orderBy('periodId', 'ASC')->get(['id','rut','dv','name','mainCompanyName','mainCompanyRut','center','certificateState','certificateDate','periodId'])->toArray();

            }if($peridoInicioF != 0 AND $peridoFinalF != 0 AND $countContratista == 0 AND $centroCosto == 0){

                $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                 ->whereIn('certificateState',[3,4,5,6,8,9,10])
                 ->whereBetween('periodId', [$peridoInicioF,$peridoFinalF])
                 ->orderBy('mainCompanyRut', 'ASC')
                 ->orderBy('periodId', 'ASC')->get(['id','rut','dv','name','mainCompanyName','mainCompanyRut','center','certificateState','certificateDate','periodId'])->toArray();

            }

        }
        if($tipoBsuqueda == 2){

            $fechaSeleccion = $input["fechaSeleccion"];
            $textoSeleccion = $fechaSeleccion;
            if($fechaSeleccion != 0  AND $countContratista != 0 AND $centroCosto != 0){
            
            $fechas = $porciones = explode("_", $fechaSeleccion);
            $fecha1 = $fechas[0];
            $fecha2 = $fechas[1];
            $fechasDesde =  strtotime ( '+4 hour' ,strtotime($fecha1));
            //sumo 1 día
            $fechasHasta =  strtotime ( '+4 hour' ,strtotime($fecha2));
            
            $empresasContratista = Contratista::distinct()->whereIn('rut',$rutcontratistasR)
            ->whereIn('certificateState',[3,4,5,6,8,9,10])
            ->whereBetween('certificateDate', [$fechasDesde,$fechasHasta])
            ->where('id',$centroCosto)
            ->orderBy('mainCompanyRut', 'ASC')
            ->orderBy('periodId', 'ASC')->get(['id','rut','dv','name','mainCompanyName','mainCompanyRut','center','certificateState','certificateDate','periodId'])->toArray();

            }
            if($fechaSeleccion != 0  AND $countContratista == 0 AND $centroCosto == 0){
            
            $fechas = $porciones = explode("_", $fechaSeleccion);
            $fecha1 = $fechas[0];
            $fecha2 = $fechas[1];
            $fechasDesde =  strtotime ( '+4 hour' ,strtotime($fecha1));
            //sumo 1 día
            $fechasHasta =  strtotime ( '+4 hour' ,strtotime($fecha2));
            $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
            ->whereIn('certificateState',[3,4,5,6,8,9,10])
            ->whereBetween('certificateDate', [$fechasDesde,$fechasHasta])
            ->orderBy('mainCompanyRut', 'ASC')
            ->orderBy('periodId', 'ASC')->
            get(['id','rut','dv','name','mainCompanyName','mainCompanyRut','center','certificateState','certificateDate','periodId'])->toArray();

            }

        }

      
        if(!empty($empresasContratista)){

            foreach ($empresasContratista as $valued) {
                $rutprincipales[] = $valued["mainCompanyRut"];
                $periodosPrincipales[] = $valued["periodId"];
                
            }

            $rutData = array_unique($rutprincipales);
            $periodosData =  array_unique($periodosPrincipales);           

            asort($periodosData);
           
            foreach ($periodosData as $pID) {
                foreach ($empresasContratista as $empresa) {
                    if($pID == $empresa['periodId']){
                        $lista['periodo'] = $empresa['periodId'];
                        $lista['principal'] = $empresa['mainCompanyRut'];
                    }
                    if(!empty($lista)){
                        $dlista[] = $lista;
                    } 
                } 
            }

            $dlista2 = array_unique($dlista, SORT_REGULAR);
           
            foreach ($dlista2 as $rut) {
                foreach ($empresasContratista as $value) {
                    $cantidadContratistas1a5 = 0;
                    $cantidadContratistas6a10 = 0;
                    $cantidadContratistas11a15 = 0;
                    $cantidadContratistas16a20 = 0;
                    $cantidadContratistas21a25 = 0;
                    $cantidadTrabajadores1a5 = 0;
                    $cantidadTrabajadores6a10 = 0;
                    $cantidadTrabajadores11a15 = 0;
                    $cantidadTrabajadores16a20 = 0;
                    $cantidadTrabajadores21a25 = 0;
                    $ct = 0;
                    $tt = 0;

                    if($rut['principal'] == $value['mainCompanyRut'] and $rut['periodo'] == $value['periodId']){
                        $datoTrabajadores1a5 = TrabajadorVerificacion::where('mainCompanyRut',$value["mainCompanyRut"])->
                                                                         where('companyRut',$value["rut"])->
                                                                         where('periodId',$value["periodId"])->
                                                                         where('companyCenter',$value["center"])->
                                                                         whereBetween('workingDaysMainCompany', [1,5])->
                                                                         get(['id','mainCompanyRut'])->toArray();
                     
                        if(!empty($datoTrabajadores1a5)){
                            $cantidadtrabajadores1 = count($datoTrabajadores1a5);
                            $cantidadTrabajadores1a5 = $cantidadTrabajadores1a5 + $cantidadtrabajadores1;
                            $cantidadContratistas1a5 += 1;
                        }


                        $datoTrabajadores6a10 = TrabajadorVerificacion::where('mainCompanyRut',$value["mainCompanyRut"])->
                                                                         where('companyRut',$value["rut"])->
                                                                         where('periodId',$value["periodId"])->
                                                                         where('companyCenter',$value["center"])->
                                                                         whereBetween('workingDaysMainCompany', [6,10])->
                                                                         get(['id'])->toArray();

                        if(!empty($datoTrabajadores6a10)){
                            $cantidadtrabajadores2 = count($datoTrabajadores6a10);

                            $cantidadTrabajadores6a10 = $cantidadTrabajadores6a10 + $cantidadtrabajadores2;
                            $cantidadContratistas6a10 += 1;
                        }


                        $datoTrabajadores11a15 = TrabajadorVerificacion::where('mainCompanyRut',$value["mainCompanyRut"])->
                                                                         where('companyRut',$value["rut"])->
                                                                         where('periodId',$value["periodId"])->
                                                                         where('companyCenter',$value["center"])->
                                                                         whereBetween('workingDaysMainCompany', [11,15])->
                                                                         get(['id'])->toArray();

                        if(!empty($datoTrabajadores11a15)){
                            $cantidadtrabajadores3 = count($datoTrabajadores11a15);

                            $cantidadTrabajadores11a15 = $cantidadTrabajadores11a15 + $cantidadtrabajadores3;
                            $cantidadContratistas11a15 += 1;
                        }


                        $datoTrabajadores16a20 = TrabajadorVerificacion::where('mainCompanyRut',$value["mainCompanyRut"])->
                                                                         where('companyRut',$value["rut"])->
                                                                         where('periodId',$value["periodId"])->
                                                                         where('companyCenter',$value["center"])->
                                                                         whereBetween('workingDaysMainCompany', [16,20])->
                                                                         get(['id'])->toArray();

                        if(!empty($datoTrabajadores16a20)){
                            $cantidadtrabajadores4 = count($datoTrabajadores16a20);

                            $cantidadTrabajadores16a20 = $cantidadTrabajadores16a20 + $cantidadtrabajadores4;
                            $cantidadContratistas16a20 += 1;
                        }


                        $datoTrabajadores21a25 = TrabajadorVerificacion::where('mainCompanyRut',$value["mainCompanyRut"])->
                                                                         where('companyRut',$value["rut"])->
                                                                         where('periodId',$value["periodId"])->
                                                                         where('companyCenter',$value["center"])->
                                                                         whereBetween('workingDaysMainCompany', [21,30])->
                                                                         get(['id'])->toArray();

                        if(!empty($datoTrabajadores21a25)){
                            $cantidadtrabajadores5 = count($datoTrabajadores21a25);

                            $cantidadTrabajadores21a25 = $cantidadTrabajadores21a25 + $cantidadtrabajadores5;
                            $cantidadContratistas21a25 += 1;
                        }



                        $datos['principal'] = $value["mainCompanyRut"];
                        $datos['contratista1a5'] = $cantidadContratistas1a5;
                        $datos['trabajadores1a5'] = $cantidadTrabajadores1a5;
                        $datos['contratista6a10'] = $cantidadContratistas6a10;
                        $datos['trabajadores6a10'] = $cantidadTrabajadores6a10;
                        $datos['contratista11a15'] = $cantidadContratistas11a15;
                        $datos['trabajadores11a15'] = $cantidadTrabajadores11a15;
                        $datos['contratista16a20'] = $cantidadContratistas16a20;
                        $datos['trabajadores16a20'] = $cantidadTrabajadores16a20;
                        $datos['contratista21a25'] = $cantidadContratistas21a25;
                        $datos['trabajadores21a25'] = $cantidadTrabajadores21a25;
                        $datos['periodId'] = $value["periodId"];
                        $ct = $cantidadContratistas1a5 + $cantidadContratistas6a10 + $cantidadContratistas11a15 + $cantidadContratistas16a20 + $cantidadContratistas21a25;
                        $tt = $cantidadTrabajadores1a5 + $cantidadTrabajadores6a10 + $cantidadTrabajadores11a15 + $cantidadTrabajadores16a20 + $cantidadTrabajadores21a25;    
                    }
                    if($ct > 0 or $tt > 0 ){
                            $listaDatos[] = $datos;
                            $rutconData[] = $value["mainCompanyRut"];
                            $periodosAc[] = $value["periodId"];
                    } 
                }               
            }
        }
            
            
        if(!empty($listaDatos)){

          
            $rutData2 = array_unique($rutconData);
            $periodosAct = array_unique($periodosAc);
            asort($periodosAct);
            $peridoInicioAC =  reset($periodosAct);
            $peridoFinalAC = end($periodosAct);
            foreach ($dlista2 as $rut2) {
                $cantidadContratistas1a5total= 0;
                $cantidadTrabajadores1a5total= 0;
                $cantidadContratistas6a10total= 0;
                $cantidadTrabajadores6a10total= 0;
                $cantidadContratistas11a15total= 0;
                $cantidadTrabajadores11a15total= 0;
                $cantidadContratistas16a20total= 0;
                $cantidadTrabajadores16a20total= 0;
                $cantidadContratistas21a25total= 0;
                $cantidadTrabajadores21a25total= 0;
                
                foreach ($listaDatos as $valor) {

                    if($rut2['principal'] == $valor['principal'] and $rut2['periodo'] == $valor['periodId']){
                        
                        $cantidadContratistas1a5total = $cantidadContratistas1a5total + $valor['contratista1a5'];
                        $cantidadTrabajadores1a5total = $cantidadTrabajadores1a5total + $valor['trabajadores1a5'];

                        $cantidadContratistas6a10total = $cantidadContratistas6a10total + $valor['contratista6a10'];
                        $cantidadTrabajadores6a10total = $cantidadTrabajadores6a10total + $valor['trabajadores6a10'];

                        $cantidadContratistas11a15total = $cantidadContratistas11a15total + $valor['contratista11a15'];
                        $cantidadTrabajadores11a15total = $cantidadTrabajadores11a15total + $valor['trabajadores11a15'];

                        $cantidadContratistas16a20total = $cantidadContratistas16a20total + $valor['contratista16a20'];
                        $cantidadTrabajadores16a20total = $cantidadTrabajadores16a20total + $valor['trabajadores16a20'];

                        $cantidadContratistas21a25total = $cantidadContratistas21a25total + $valor['contratista21a25'];
                        $cantidadTrabajadores21a25total = $cantidadTrabajadores21a25total + $valor['trabajadores21a25'];

                        $datos['principal'] = $valor["principal"];
                        $datos['contratista1a5'] = $cantidadContratistas1a5total;
                        $datos['trabajadores1a5'] = $cantidadTrabajadores1a5total;
                        $datos['contratista6a10'] = $cantidadContratistas6a10total;
                        $datos['trabajadores6a10'] = $cantidadTrabajadores6a10total;
                        $datos['contratista11a15'] = $cantidadContratistas11a15total;
                        $datos['trabajadores11a15'] = $cantidadTrabajadores11a15total;
                        $datos['contratista16a20'] = $cantidadContratistas16a20total;
                        $datos['trabajadores16a20'] = $cantidadTrabajadores16a20total;
                        $datos['contratista21a25'] = $cantidadContratistas21a25total;
                        $datos['trabajadores21a25'] = $cantidadTrabajadores21a25total;
                        $datos['periodId'] = $valor["periodId"];
                        $periodosActivos[] = $valor["periodId"];      
                    } 
                }
                $listaDatos3[] = $datos;  
            }

            $tabla = "";
            $peridoTex = "";
            
            $periodosActivosT = array_unique($periodosActivos);        
            $periodosOrdanos = array_reverse($periodosActivosT);
            asort($periodosOrdanos);
            $peridoInicio =  reset($periodosOrdanos);
            $peridoFinal = end($periodosOrdanos);
            $listaDatos2 = array_unique($listaDatos3, SORT_REGULAR);
             
            for ($i=$peridoInicio; $i <= $peridoFinal; $i++) { 
                $servicios1 = 0;
                $servicios2 = 0;
                $servicios3 = 0;
                $servicios4 = 0;
                $servicios5 = 0;
                $trabajadores1 = 0;
                $trabajadores2 = 0;
                $trabajadores3 = 0;
                $trabajadores4 = 0;
                $trabajadores5 = 0;
               
                $peridoTex = periodoTexto($i);
            
                $tabla ='<table border="3">
                    <thead>
                      <tr>
                         <th style="background-color:#e3e3e3" colspan="13">Perido:'.$peridoTex.'</th>
                      </tr>
                      <tr>
                        <th style="background-color:#3333FF" align="center"><font color="#FFFFFF">DIAS HABILES EN QUE SE PRESTARON SS.</font></th>
                        <th style="background-color:#3333FF" colspan="2" align="center" ><font color="#FFFFFF">1 a 5</font></th>
                        <th style="background-color:#3333FF" colspan="2" align="center" ><font color="#FFFFFF">6 a 10</font></th>
                        <th style="background-color:#3333FF" colspan="2" align="center" ><font color="#FFFFFF">11 a 15</font></th>
                        <th style="background-color:#3333FF" colspan="2" align="center" ><font color="#FFFFFF">16 a 20</font></th>
                        <th style="background-color:#3333FF" colspan="2" align="center" ><font color="#FFFFFF">21 a 25</font></th>
                        <th style="background-color:#3333FF" colspan="2" align="center" ><font color="#FFFFFF">Totales</font></th>
                      </tr>
                      <tr>
                        <th style="background-color:#3333FF"><font color="#FFFFFF">UNIDAD</th>
                        <th style="background-color:#3333FF"><font color="#FFFFFF">Numero de Servicio</font></th>
                        <th style="background-color:#3333FF"><font color="#FFFFFF">Numero de Trabajadores</font></th>
                        <th style="background-color:#3333FF"><font color="#FFFFFF">Numero de Servicio</font></th>
                        <th style="background-color:#3333FF"><font color="#FFFFFF">Numero de Trabajadores</font></th>
                        <th style="background-color:#3333FF"><font color="#FFFFFF">Numero de Servicio</font></th>
                        <th style="background-color:#3333FF"><font color="#FFFFFF">Numero de Trabajadores</font></th>
                        <th style="background-color:#3333FF"><font color="#FFFFFF">Numero de Servicio</font></th>
                        <th style="background-color:#3333FF"><font color="#FFFFFF">Numero de Trabajadores</th>
                        <th style="background-color:#3333FF"><font color="#FFFFFF">Numero de Servicio</font></th>
                        <th style="background-color:#3333FF"><font color="#FFFFFF">Numero de Trabajadores</font></th>
                        <th style="background-color:#3333FF"><font color="#FFFFFF">Numero de Servicio</font></th>
                        <th style="background-color:#3333FF"><font color="#FFFFFF">Numero de Trabajadores</font></th>
                      </tr>
                    </thead>
                     <tbody>';
                    $totalServiciotd = 0;
                    $totalTrabajadortd = 0;
                    $totalServiciotd1 = 0;
                    $totalTrabajadortd1 = 0;
                    foreach($listaDatos2 as $datos2){
                        if($i == $datos2['periodId'] ){
                            $tabla.='<tr>';
                            $EmpresasPTexto = empresaPrincipal::where('rut',$datos2['principal'])->take(1)->get(['name','rut'])->toArray();
                            $tabla.='<td>'.strtoupper($EmpresasPTexto[0]['name']).'</td>';
                            $tabla.='<td>'.$datos2['contratista1a5'].'</td>';
                            $tabla.='<td>'.$datos2['trabajadores1a5'].'</td>';
                            $tabla.='<td>'.$datos2['contratista6a10'].'</td>';
                            $tabla.='<td>'.$datos2['trabajadores6a10'].'</td>';
                            $tabla.='<td>'.$datos2['contratista11a15'].'</td>';
                            $tabla.='<td>'.$datos2['trabajadores11a15'].'</td>';
                            $tabla.='<td>'.$datos2['contratista16a20'].'</td>';
                            $tabla.='<td>'.$datos2['trabajadores16a20'].'</td>';
                            $tabla.='<td>'.$datos2['contratista21a25'].'</td>';
                            $tabla.='<td>'.$datos2['trabajadores21a25'].'</td>';
                            $totalServiciotd = $datos2['contratista1a5'] + $datos2['contratista6a10'] + $datos2['contratista11a15'] + $datos2['contratista16a20'] + $datos2['contratista21a25'];

                            $totalTrabajadortd = $datos2['trabajadores1a5'] + $datos2['trabajadores6a10'] + $datos2['trabajadores11a15'] + $datos2['trabajadores16a20'] + $datos2['trabajadores21a25'];
                            $tabla.='<td>'.$totalServiciotd.'</td>';
                            $tabla.='<td>'.$totalTrabajadortd.'</td>';
                            $tabla.='</tr>';
                            $servicios1 = $servicios1 + $datos2['contratista1a5'];
                            $trabajadores1 = $trabajadores1 + $datos2['trabajadores1a5'];
                            
                            $servicios2 = $servicios2 + $datos2['contratista6a10'];
                            $trabajadores2 = $trabajadores2 + $datos2['trabajadores6a10'];
                           
                            $servicios3 = $servicios3 + $datos2['contratista11a15'];
                            $trabajadores3 = $trabajadores3 + $datos2['trabajadores11a15'];

                            $servicios4 = $servicios4 + $datos2['contratista16a20'];
                            $trabajadores4 = $trabajadores4 + $datos2['trabajadores16a20'];

                            $servicios5 = $servicios5 + $datos2['contratista21a25'];
                            $trabajadores5 = $trabajadores5 + $datos2['trabajadores21a25'];

                            $totalServiciotd1 = $totalServiciotd1 + $totalServiciotd;
                            $totalTrabajadortd1 = $totalTrabajadortd1 + $totalTrabajadortd;
                        }
                    }
                    $tabla.='<tr>';
                    $tabla.='<td>Total General</td>';
                    $tabla.='<td>'.$servicios1.'</td>';
                    $tabla.='<td>'.$trabajadores1.'</td>';
                    $tabla.='<td>'.$servicios2.'</td>';
                    $tabla.='<td>'.$trabajadores2.'</td>';
                    $tabla.='<td>'.$servicios3.'</td>';
                    $tabla.='<td>'.$trabajadores3.'</td>';
                    $tabla.='<td>'.$servicios4.'</td>';
                    $tabla.='<td>'.$trabajadores4.'</td>';
                    $tabla.='<td>'.$servicios5.'</td>';
                    $tabla.='<td>'.$trabajadores5.'</td>';
                    $tabla.='<td>'.$totalServiciotd1.'</td>';
                    $tabla.='<td>'.$totalTrabajadortd1.'</td>';
                    $tabla.='</tr>';
                    $tabla.='<tr>';
                    $tabla.='<td><font color="white">Ponderación</td>';
                    $tabla.='<td style="background-color:#3333FF" colspan="2" align="right" ><font color="#FFFFFF">20%</font></td>';
                    $tabla.='<td style="background-color:#3333FF" colspan="2" align="right" ><font color="#FFFFFF">40%</font></td>';
                    $tabla.='<td style="background-color:#3333FF" colspan="2" align="right" ><font color="#FFFFFF">60%</font></td>';
                    $tabla.='<td style="background-color:#3333FF" colspan="2" align="right" ><font color="#FFFFFF">80%</font></td>';
                    $tabla.='<td style="background-color:#3333FF" colspan="2" align="right" ><font color="#FFFFFF">100%</font></td>';
                    $tabla.='<td style="background-color:#3333FF" colspan="2" align="right" ><font color="#FFFFFF">FTE</font></td>';
                    $tabla.='</tr>';
                    $tabla.='<tr>';
                    $tabla.='<td><font color="white">Resultado FTE</td>';
                    $FTE1 = $trabajadores1*0.20;
                    $tabla.='<td style="background-color:#3333FF" colspan="2" align="right" ><font color="#FFFFFF">'.$FTE1.'</font></td>';
                    $FTE2 = $trabajadores2*0.40;
                    $tabla.='<td style="background-color:#3333FF" colspan="2" align="right" ><font color="#FFFFFF">'.$FTE2.'</font></td>';
                    $FTE3 = $trabajadores3*0.60;
                    $tabla.='<td style="background-color:#3333FF" colspan="2" align="right" ><font color="#FFFFFF">'.$FTE3.'</font></td>';
                    $FTE4 = $trabajadores4*0.80;
                    $tabla.='<td style="background-color:#3333FF" colspan="2" align="right" ><font color="#FFFFFF">'.$FTE4.'</font></td>';
                    $FTE5 = $trabajadores5;
                    $tabla.='<td style="background-color:#3333FF" colspan="2" align="right" ><font color="#FFFFFF">'.$FTE5.'</font></td>';
                    $FTET = $FTE1 +$FTE2 + $FTE3 + $FTE4 + $FTE5;
                    $tabla.='<td style="background-color:#3333FF" colspan="2" align="right" ><font color="#FFFFFF">'.$FTET.'</font></td>';
                    $tabla.='<tr>';
                    $tabla.='</tbody></table>';
                    if(!empty($tabla)){
                        $tablaHTML2['tablavista']= $tabla;
                        $tablaHTML2['periodo']= $peridoTex;
                        $tablaHTML[]= $tablaHTML2;
                    }

            }
            
                
        }
       
      
        if(!empty($tablaHTML)){
            if(!empty($listaDatos2)){
            Excel::create('Reporte FTE', function($excel) use($tablaHTML,$peridoInicio,$peridoFinal,$listaDatos2,$periodosOrdanos) {
                
                $nombreHoja = "";
                $tablavista = "";
                    foreach ($tablaHTML as $tabla) {
                 
                            $nombreHoja = ucwords(mb_strtoupper($tabla['periodo']));
                            $excel->sheet($nombreHoja, function($sheet) use($tabla) {    
                                    $tablavista = $tabla['tablavista'];
                                    $sheet->loadView('reporteFTE.listadoGeneral',compact('tablavista'));
                            });
                        
                    }
                        
                    
            })->export('xls');
            }

        }else{
            $nodatos = 0;
            return view('reporteFTE.index',compact('EmpresasP','periodos','datosUsuarios','etiquetasEstados','valores','certificacion','usuarioAqua','usuarioABBChile','nodatos','usuarioNOKactivo','usuarioClaroChile'));
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
