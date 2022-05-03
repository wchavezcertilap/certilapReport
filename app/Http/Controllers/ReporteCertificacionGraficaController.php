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


use Illuminate\Http\Request;

class ReporteCertificacionGraficaController extends Controller
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
        return view('reporteCertificacionGrafica.index',compact('EmpresasP','periodos','datosUsuarios','etiquetasEstados','valoresEstados','certificacion','periodosT','principalesTexto','usuarioAqua','usuarioABBChile','usuarioNOKactivo'));
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

            $periodoTexto = "";
            $estadoCerficacionTexto = "";

            if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista != 0 AND $centroCosto != 0){
                $totalempresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                ->whereIn('rut',$rutcontratistasR)
                ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
                ->where('id',$centroCosto)->count();
                $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                ->whereIn('rut',$rutcontratistasR)
                ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
                ->where('id',$centroCosto)
                ->orderBy('id', 'ASC')->select('id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType','servicioId','classserv','adminContrato')->chunk($totalempresasContratista, function ($query) use($peridoInicio,$peridoFinal) {

                    foreach((array)$query as $empresasContratista){

                        if(!empty($empresasContratista)){

                            foreach($empresasContratista AS $empresa){

                                if($empresa['certificateState'] != 1){

                                    $datosSolictud = Solicitud::distinct()->where('companyId',$empresa['id'])->orderby('serial','DESC')->take(1)->get(['workersNumber','workerstotales','serial'])->toArray();
                                    
                                    if(!empty($datosSolictud)){
                                        $numeroTrabajadoresTotales = $datosSolictud[0]['workerstotales'];
                                        $numeroTrabajadores = $datosSolictud[0]['workersNumber'];
                                    
                                       
                                    }
                                }else{
                                    $numeroTrabajadores = 0;
                                    $numeroTrabajadoresTotales = $empresa['workersNumber'];
                                }

                                if($empresa['certificateState'] == 10 or $empresa['certificateState'] == 5){
                                    $datosCertificado = Certificado::where('companyId',$empresa['id'])->orderby('serial','DESC')->take(1)->get(['number','serial'])->toArray(); 
                                    $numeroCertificado = $datosCertificado[0]['number']."-".$datosCertificado[0]['serial'];
                                }else{
                                   
                                    $numeroCertificado = "";
                                }

                                switch ((int)$empresa['certificateState']) {
                                    case 1:
                                         $estadoCerficacionTexto ="Ingresado";
                                        break;
                                    case 2:
                                         $estadoCerficacionTexto ="Solicitado";
                                        break;
                                    case 3:
                                         $estadoCerficacionTexto ="Aprobado";
                                        break;
                                    case 4:
                                         $estadoCerficacionTexto ="No Aprobado";
                                        break;
                                    case 5:
                                         $estadoCerficacionTexto ="Certificado";
                                        break;
                                    case 6:
                                         $estadoCerficacionTexto ="Documentado";
                                        break;
                                    case 7:
                                         $estadoCerficacionTexto ="Histórico";
                                        break;
                                    case 8:
                                         $estadoCerficacionTexto ="Completo";
                                        break;
                                    case 9:
                                         $estadoCerficacionTexto ="En Proceso";
                                        break;
                                    case 10:
                                         $estadoCerficacionTexto ="No Conforme";
                                        break;
                                    case 11:
                                         $estadoCerficacionTexto ="Inactivo";
                                        break;
                                }

                                if($empresa['certificateState'] == 5 or $empresa['certificateState'] == 6 or $empresa['certificateState'] == 8 or $empresa['certificateState'] == 3 or $empresa['certificateState'] == 4 or $empresa['certificateState'] == 5 or $empresa['certificateState'] == 11 or $empresa['certificateState'] == 10){

                                        $datosCuadratura = Cuadratura::where('companyId',$empresa['id'])->orderby('id','DESC')->take(1)->get(['observations','id'])->toArray();
                                        if(!empty($datosCuadratura)){
                                           
                                            $observaciones = $datosCuadratura[0]['observations'];
                                        }else{
                                          
                                           $observaciones =  $empresa['certificateObservations'];
                                        }
                                }else{
                                    $observaciones =  $empresa['certificateObservations'];
                                }
                                $periodo = DB::table('Period')
                                ->join('Month', 'Month.id', '=', 'Period.monthId')
                                ->where(['Period.id' => $empresa["periodId"]])
                                ->select('Period.year','Month.name')
                                ->get();

                                $periodoTexto =  $periodo[0]->name."-".$periodo[0]->year;            
                                $datosReporte["id"] = $empresa["id"];
                                $datosReporte["rutprincipal"] = $empresa["mainCompanyRut"];
                                $datosReporte["principal"] = ucwords(mb_strtolower($empresa["mainCompanyName"],'UTF-8'));
                                $datosReporte["rutcontratistas"] = $empresa["rut"]."-".$empresa["dv"];
                                $datosReporte["contratista"] = ucwords(mb_strtolower($empresa["name"],'UTF-8'));
                                $datosReporte["center"] = ucwords(mb_strtolower($empresa["center"],'UTF-8'));
                                $datosReporte["rutSubContratista"] = $empresa["subcontratistaRut"]."-".$empresa["subcontratistaDv"];
                                $datosReporte["subcontratistaName"] = ucwords(mb_strtolower($empresa["subcontratistaName"],'UTF-8'));
                                $datosReporte["center"] = ucwords(mb_strtolower($empresa["center"],'UTF-8'));
                                $datosReporte["periodo"] = ucwords(mb_strtolower($periodoTexto,'UTF-8'));
                                $datosReporte["periodoID"] = $empresa["periodId"];
                                $datosReporte['numeroTrabajadoresCertificar'] = $numeroTrabajadores;     
                                $datosReporte['numeroTrabajadoresTotales'] = $numeroTrabajadoresTotales; 
                                $datosReporte["estadoCerticacion"] = ucwords(mb_strtolower($estadoCerficacionTexto,'UTF-8'));
                                $datosReporte["estadoCerticacionId"] = $empresa['certificateState'];
                                $datosReporte["fechaCerticacion"] =  date('d/m/Y', $empresa["certificateDate"]);
                                $datosReporte["observacion"] =  mb_strtolower($observaciones,'UTF-8');

                                if(!empty($datosReporte)){
                                    $listaDatosReporte[] = $datosReporte;
                                    
                                }
                            }  
                        } 
                    }

                         
                   for ($peridoInicio; $peridoInicio <= $peridoFinal; $peridoInicio++) { 
                        $trabajadores = 0;
                        $certificados = 0;
                        $Subcertificados = 0;
                        $trabajadoresNo = 0;  
                        $noCertificado = 0;
                        $SubcertificadosNO = 0;
                        foreach ($listaDatosReporte as $value) {
                            if($peridoInicio == $value['periodoID'] AND $value['estadoCerticacionId'] == 5){
                                $trabajadores = $trabajadores + $value['numeroTrabajadoresCertificar'];
                                $certificados = $certificados + 1;
                                $peridosTrabajadore['periodo'] = $value['periodo'];
                                $peridosTrabajadore['trabajadores'] = $trabajadores;
                                $peridosTrabajadore['cantidad'] = $certificados;

                                if($value['rutSubContratista'] != "-"){
                                    $Subcertificados = $Subcertificados + 1;
                                    $peridosTrabajadore['Subcertificados'] = $Subcertificados;
                                }else{
                                    
                                    $peridosTrabajadore['Subcertificados'] = $Subcertificados;
                                }
                            }
                           
                           if ($peridoInicio == $value['periodoID'] AND $value['estadoCerticacionId'] == 10){
                                $trabajadoresNo = $trabajadoresNo + $value['numeroTrabajadoresCertificar'];
                                $noCertificado = $noCertificado + 1;
                                $peridosTrabajadoreNo['periodo'] = $value['periodo'];
                                $peridosTrabajadoreNo['trabajadores'] =  $trabajadoresNo;
                                $peridosTrabajadoreNo['cantidad'] = $noCertificado;
                                if($value['rutSubContratista'] != "-"){
                                    $SubcertificadosNO = $SubcertificadosNO + 1;
                                    $peridosTrabajadoreNo['Subcertificados'] = $SubcertificadosNO;
                                }else{
                                    
                                    $peridosTrabajadoreNo['Subcertificados'] = $SubcertificadosNO;
                                }
                            }
                        }
                       
                        if (!empty($peridosTrabajadore)) {
                           $listaConforme[] = $peridosTrabajadore;
                        }else{
                            $listaConforme[] = 0;
                        }
                        if (!empty($peridosTrabajadoreNo)) {
                           $listaConformeNo[] = $peridosTrabajadoreNo;
                        }else{
                            $listaConformeNo[] = 0;
                        }
                    }
                    
                    $result1 = array_unique($listaConforme, SORT_REGULAR);
                    $result2 = array_unique($listaConformeNo, SORT_REGULAR);
                  
                    //// grafica trabajadores /////
                    foreach ($result1 as $value) {
                       $mesesSeleccion[] = $value['periodo'];
                       $valoresSeleccion[] = $value['trabajadores'];
                       $cantidadCertificados[] = $value['cantidad'];
                       $cantidadSubCertificados[] = $value['Subcertificados'];
                    }
                    $estadoC = array('estado de Certificacion');
                    $mesesGrafico = array_merge($estadoC, $mesesSeleccion);
                    $aprobado = array("Aprobado");
                    $valoresGrafico = array_merge($aprobado, $valoresSeleccion);
                    $dataGraficaArpobados = array($mesesGrafico, $valoresGrafico);
                  
                    foreach ($result2 as $valuen) {
                       $mesesSeleccionN[] = $valuen['periodo'];
                       $valoresSeleccionN[] = $valuen['trabajadores'];
                       $cantidadNoCertificados[] = $valuen['cantidad'];
                       $cantidadSubNoCertificados[] = $valuen['Subcertificados'];
                    }
                    $estadoCN = array('estado de Certificacion');
                    $mesesGraficoNo = array_merge($estadoCN, $mesesSeleccionN);
                    $naprobado = array("No Aprobado");
                    $valoresGraficoNo = array_merge($naprobado, $valoresSeleccionN);
                    $dataGraficaNoAporbado = array($mesesGraficoNo, $valoresGraficoNo);
                    $dataGrafica = array($mesesGrafico, $valoresGrafico,$valoresGraficoNo);
                    ///// grfica estado de certificacion ///

                    $mesesCertificado = array('Cantidad');
                    $mesesGraficoCertificado = array_merge($mesesCertificado, $mesesSeleccion);
                    $CertificadoAprobado = array("Certificados");
                    $cantidadCertificadosAprobados = array_merge($CertificadoAprobado, $cantidadCertificados);
                    $CertificadoNoAprobado = array("No Aprobados");
                    $cantidadCertificadosNoApro = array_merge($CertificadoNoAprobado, $cantidadNoCertificados);
                    $dataGraficaCertificado = array($mesesGraficoCertificado, $cantidadCertificadosAprobados,$cantidadCertificadosNoApro);


                    //// grafica SUB CONTRATISTA /////
                    $mesesCertificado = array('Cantidad');
                    $mesesGraficoCertificadoSub = array_merge($mesesCertificado, $mesesSeleccion);
                    $CertificadoAprobadoSub = array("Certificados");
                    $cantidadCertificadosAprobadosSub = array_merge($CertificadoAprobadoSub, $cantidadSubCertificados);
                    $CertificadoNoAprobadoSub = array("No Aprobados");
                    $cantidadCertificadosNoAproSub = array_merge($CertificadoNoAprobadoSub, $cantidadSubNoCertificados);
                    $dataGraficaCertificadoSub = array($mesesGraficoCertificadoSub, $cantidadCertificadosAprobadosSub,$cantidadCertificadosNoAproSub);

                    Excel::create('Reporte Certificación con Graficos', function($excel) use ($listaDatosReporte, $dataGrafica,$dataGraficaNoAporbado,$dataGraficaCertificado,$dataGraficaCertificadoSub) {

                            $excel->sheet('Lista General', function($sheet) use($listaDatosReporte) {    
                                $sheet->loadView('reporteCertificacionGrafica.listadoGeneral',compact('listaDatosReporte'));
                            });

                            $excel->sheet('Grafico_por_Trabajador', function($sheet) use($dataGrafica) { 

                                $sheet->fromArray($dataGrafica);

                                $columnCount = count($dataGrafica[0]);
                                $rowCount = count($dataGrafica);
                                $keys = array_keys($dataGrafica[0]);
                                $labels = array();
                                $categories = array();
                                $values = array();
                                for ($i = 1; $i < $columnCount; $i++) {
                               
                                $col = \PHPExcel_Cell::stringFromColumnIndex($i);
                                $labels[] = new \PHPExcel_Chart_DataSeriesValues('String', 'Grafico_por_Trabajador!$' . $col . '$2', null, 1);
                                $categories[] = new \PHPExcel_Chart_DataSeriesValues('String', 'Grafico_por_Trabajador!$A$3:$A$' . ($rowCount + 1), null, $rowCount);
                                $values[] = new \PHPExcel_Chart_DataSeriesValues('Number', 'Grafico_por_Trabajador!$' . $col . '$3:$' . $col . '$' . ($rowCount + 1), null, $rowCount);
                            
                                }
                  

                                $series = new \PHPExcel_Chart_DataSeries(
                                    \PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
                                    \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
                                    range(0, count($values)-1),           // plotOrder
                                    $labels,                              // plotLabel
                                    $categories,                               // plotCategory
                                    $values                               // plotValues
                                );
                                $series->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_BAR);

                                $plotArea = new \PHPExcel_Chart_PlotArea(NULL, array($series));
                                $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                                $title = new \PHPExcel_Chart_Title('trabajadores Certificados');
                                $yAxisLabel = new \PHPExcel_Chart_Title('Cantidad de trabajdores');
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

                                //    Add the chart to the worksheet
                                $sheet->addChart($chart); 
                            });

                            $excel->sheet('Lista Estado Empresas', function($sheet) use($listaDatosReporte) {    
                                $sheet->loadView('reporteCertificacionGrafica.generalCertificada',compact('listaDatosReporte'));
                            });

                            $excel->sheet('grafica_por_empresa', function($sheet) use($dataGraficaCertificado) { 

                                $sheet->fromArray($dataGraficaCertificado);

                                $columnCount = count($dataGraficaCertificado[0]);
                                $rowCount = count($dataGraficaCertificado);
                                $keys = array_keys($dataGraficaCertificado[0]);
                                $labels = array();
                                $categories = array();
                                $values = array();
                                for ($i = 1; $i < $columnCount; $i++) {
                               
                                $col = \PHPExcel_Cell::stringFromColumnIndex($i);
                                $labels[] = new \PHPExcel_Chart_DataSeriesValues('String', 'grafica_por_empresa!$' . $col . '$2', null, 1);
                                $categories[] = new \PHPExcel_Chart_DataSeriesValues('String', 'grafica_por_empresa!$A$3:$A$' . ($rowCount + 1), null, $rowCount);
                                $values[] = new \PHPExcel_Chart_DataSeriesValues('Number', 'grafica_por_empresa!$' . $col . '$3:$' . $col . '$' . ($rowCount + 1), null, $rowCount);
                            
                                }
                  

                                $series = new \PHPExcel_Chart_DataSeries(
                                    \PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
                                    \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
                                    range(0, count($values)-1),           // plotOrder
                                    $labels,                              // plotLabel
                                    $categories,                               // plotCategory
                                    $values                               // plotValues
                                );
                                $series->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_BAR);

                                $plotArea = new \PHPExcel_Chart_PlotArea(NULL, array($series));
                                $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                                $title = new \PHPExcel_Chart_Title('Número de Empresas Certificadas');
                                $yAxisLabel = new \PHPExcel_Chart_Title('Cantidad de Empresas');
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

                                //    Add the chart to the worksheet
                                $sheet->addChart($chart); 
                            });

                            $excel->sheet('Empresas Sub Contratista', function($sheet) use($listaDatosReporte) {    
                                $sheet->loadView('reporteCertificacionGrafica.subContratistaGeneral',compact('listaDatosReporte'));
                            });

                            $excel->sheet('grafica_SubContratistas', function($sheet) use($dataGraficaCertificadoSub) { 

                                $sheet->fromArray($dataGraficaCertificadoSub);

                                $columnCount = count($dataGraficaCertificadoSub[0]);
                                $rowCount = count($dataGraficaCertificadoSub);
                                $keys = array_keys($dataGraficaCertificadoSub[0]);
                                $labels = array();
                                $categories = array();
                                $values = array();
                                for ($i = 1; $i < $columnCount; $i++) {
                               
                                    $col = \PHPExcel_Cell::stringFromColumnIndex($i);
                                    $labels[] = new \PHPExcel_Chart_DataSeriesValues('String', 'grafica_SubContratistas!$' . $col . '$2', null, 1);
                                    $categories[] = new \PHPExcel_Chart_DataSeriesValues('String', 'grafica_SubContratistas!$A$3:$A$' . ($rowCount + 1), null, $rowCount);
                                    $values[] = new \PHPExcel_Chart_DataSeriesValues('Number', 'grafica_SubContratistas!$' . $col . '$3:$' . $col . '$' . ($rowCount + 1), null, $rowCount);
                                }
                  
                                $series = new \PHPExcel_Chart_DataSeries(
                                    \PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
                                    \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
                                    range(0, count($values)-1),           // plotOrder
                                    $labels,                              // plotLabel
                                    $categories,                               // plotCategory
                                    $values                               // plotValues
                                );
                                $series->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_BAR);

                                $plotArea = new \PHPExcel_Chart_PlotArea(NULL, array($series));
                                $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                                $title = new \PHPExcel_Chart_Title('Numero de Empresas Sub Contratista Certificadas');
                                $yAxisLabel = new \PHPExcel_Chart_Title('Empresas');
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

                                //    Add the chart to the worksheet
                                $sheet->addChart($chart); 
                            });
                    })->export('xlsx');   
                });
            }
            if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista != 0){
                $totalempresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                ->whereIn('rut',$rutcontratistasR)
                ->whereBetween('periodId', [$peridoInicio,$peridoFinal])->count();
                $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                ->whereIn('rut',$rutcontratistasR)
                ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
                ->orderBy('id', 'ASC')->select('id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType','servicioId','classserv','adminContrato')->chunk($totalempresasContratista, function ($query) use($peridoInicio,$peridoFinal) {

                    foreach((array)$query as $empresasContratista){

                        if(!empty($empresasContratista)){

                            foreach($empresasContratista AS $empresa){

                                if($empresa['certificateState'] != 1){

                                    $datosSolictud = Solicitud::distinct()->where('companyId',$empresa['id'])->orderby('serial','DESC')->take(1)->get(['workersNumber','workerstotales','serial'])->toArray();
                                    
                                    if(!empty($datosSolictud)){
                                        $numeroTrabajadoresTotales = $datosSolictud[0]['workerstotales'];
                                        $numeroTrabajadores = $datosSolictud[0]['workersNumber'];   
                                    }else{
                                        $numeroTrabajadoresTotales = 0;
                                        $numeroTrabajadores = 0;
                                    }
                                }else{
                                    $numeroTrabajadores = 0;
                                    $numeroTrabajadoresTotales = $empresa['workersNumber'];
                                }

                                if($empresa['certificateState'] == 10 or $empresa['certificateState'] == 5){
                                    $datosCertificado = Certificado::where('companyId',$empresa['id'])->orderby('serial','DESC')->take(1)->get(['number','serial'])->toArray(); 
                                    $numeroCertificado = $datosCertificado[0]['number']."-".$datosCertificado[0]['serial'];
                                }else{
                                   
                                    $numeroCertificado = "";
                                }

                                switch ((int)$empresa['certificateState']) {
                                    case 1:
                                         $estadoCerficacionTexto ="Ingresado";
                                        break;
                                    case 2:
                                         $estadoCerficacionTexto ="Solicitado";
                                        break;
                                    case 3:
                                         $estadoCerficacionTexto ="Aprobado";
                                        break;
                                    case 4:
                                         $estadoCerficacionTexto ="No Aprobado";
                                        break;
                                    case 5:
                                         $estadoCerficacionTexto ="Certificado";
                                        break;
                                    case 6:
                                         $estadoCerficacionTexto ="Documentado";
                                        break;
                                    case 7:
                                         $estadoCerficacionTexto ="Histórico";
                                        break;
                                    case 8:
                                         $estadoCerficacionTexto ="Completo";
                                        break;
                                    case 9:
                                         $estadoCerficacionTexto ="En Proceso";
                                        break;
                                    case 10:
                                         $estadoCerficacionTexto ="No Conforme";
                                        break;
                                    case 11:
                                         $estadoCerficacionTexto ="Inactivo";
                                        break;
                                }

                                if($empresa['certificateState'] == 5 or $empresa['certificateState'] == 6 or $empresa['certificateState'] == 8 or $empresa['certificateState'] == 3 or $empresa['certificateState'] == 4 or $empresa['certificateState'] == 5 or $empresa['certificateState'] == 11 or $empresa['certificateState'] == 10){

                                        $datosCuadratura = Cuadratura::where('companyId',$empresa['id'])->orderby('id','DESC')->take(1)->get(['observations','id'])->toArray();
                                        if(!empty($datosCuadratura)){
                                           
                                            $observaciones = $datosCuadratura[0]['observations'];
                                        }else{
                                          
                                           $observaciones =  $empresa['certificateObservations'];
                                        }
                                }else{
                                    $observaciones =  $empresa['certificateObservations'];
                                }
                                $periodo = DB::table('Period')
                                ->join('Month', 'Month.id', '=', 'Period.monthId')
                                ->where(['Period.id' => $empresa["periodId"]])
                                ->select('Period.year','Month.name')
                                ->get();

                                $periodoTexto =  $periodo[0]->name."-".$periodo[0]->year;            

                                $datosReporte["id"] = $empresa["id"];
                                $datosReporte["rutprincipal"] = $empresa["mainCompanyRut"];
                                $datosReporte["principal"] = ucwords(mb_strtolower($empresa["mainCompanyName"],'UTF-8'));
                                $datosReporte["rutcontratistas"] = $empresa["rut"]."-".$empresa["dv"];
                                $datosReporte["contratista"] = ucwords(mb_strtolower($empresa["name"],'UTF-8'));
                                $datosReporte["center"] = ucwords(mb_strtolower($empresa["center"],'UTF-8'));
                                $datosReporte["rutSubContratista"] = $empresa["subcontratistaRut"]."-".$empresa["subcontratistaDv"];
                                $datosReporte["subcontratistaName"] = ucwords(mb_strtolower($empresa["subcontratistaName"],'UTF-8'));
                                $datosReporte["center"] = ucwords(mb_strtolower($empresa["center"],'UTF-8'));
                                $datosReporte["periodo"] = ucwords(mb_strtolower($periodoTexto,'UTF-8'));
                                $datosReporte["periodoID"] = $empresa["periodId"];
                                $datosReporte['numeroTrabajadoresCertificar'] = $numeroTrabajadores;     
                                $datosReporte['numeroTrabajadoresTotales'] = $numeroTrabajadoresTotales; 
                                $datosReporte["estadoCerticacion"] = ucwords(mb_strtolower($estadoCerficacionTexto,'UTF-8'));
                                $datosReporte["estadoCerticacionId"] = $empresa['certificateState'];
                                $datosReporte["fechaCerticacion"] =  date('d/m/Y', $empresa["certificateDate"]);
                                $datosReporte["observacion"] =  mb_strtolower($observaciones,'UTF-8');


                                if(!empty($datosReporte)){
                                    $listaDatosReporte[] = $datosReporte;
                                    
                                }
                            }  
                        } 
                    }

                         
                   for ($peridoInicio; $peridoInicio <= $peridoFinal; $peridoInicio++) { 
                        $trabajadores = 0;
                        $certificados = 0;
                        $Subcertificados = 0;
                        $trabajadoresNo = 0;  
                        $noCertificado = 0;
                        $SubcertificadosNO = 0;
                        foreach ($listaDatosReporte as $value) {
                            if($peridoInicio == $value['periodoID'] AND $value['estadoCerticacionId'] == 5){
                                $trabajadores = $trabajadores + $value['numeroTrabajadoresCertificar'];
                                $certificados = $certificados + 1;
                                $peridosTrabajadore['periodo'] = $value['periodo'];
                                $peridosTrabajadore['trabajadores'] = $trabajadores;
                                $peridosTrabajadore['cantidad'] = $certificados;

                                if($value['rutSubContratista'] != "-"){
                                    $Subcertificados = $Subcertificados + 1;
                                    $peridosTrabajadore['Subcertificados'] = $Subcertificados;
                                }else{
                                    
                                    $peridosTrabajadore['Subcertificados'] = $Subcertificados;
                                }
                            }
                           
                           if ($peridoInicio == $value['periodoID'] AND $value['estadoCerticacionId'] == 10){
                                $trabajadoresNo = $trabajadoresNo + $value['numeroTrabajadoresCertificar'];
                                $noCertificado = $noCertificado + 1;
                                $peridosTrabajadoreNo['periodo'] = $value['periodo'];
                                $peridosTrabajadoreNo['trabajadores'] =  $trabajadoresNo;
                                $peridosTrabajadoreNo['cantidad'] = $noCertificado;
                                if($value['rutSubContratista'] != "-"){
                                    $SubcertificadosNO = $SubcertificadosNO + 1;
                                    $peridosTrabajadoreNo['Subcertificados'] = $SubcertificadosNO;
                                }else{
                                    
                                    $peridosTrabajadoreNo['Subcertificados'] = $SubcertificadosNO;
                                }
                            }
                        }
                        if (!empty($peridosTrabajadore)) {
                           $listaConforme[] = $peridosTrabajadore;
                        }else{
                            $listaConforme[] = 0;
                        }
                        if (!empty($peridosTrabajadoreNo)) {
                           $listaConformeNo[] = $peridosTrabajadoreNo;
                        }else{
                            $listaConformeNo[] = 0;
                        }
                    }
                    
                    $result1 = array_unique($listaConforme, SORT_REGULAR);
                    $result2 = array_unique($listaConformeNo, SORT_REGULAR);
                  
                    //// grafica trabajadores /////
                    foreach ($result1 as $value) {
                       $mesesSeleccion[] = $value['periodo'];
                       $valoresSeleccion[] = $value['trabajadores'];
                       $cantidadCertificados[] = $value['cantidad'];
                       $cantidadSubCertificados[] = $value['Subcertificados'];
                    }
                    $estadoC = array('estado de Certificacion');
                    $mesesGrafico = array_merge($estadoC, $mesesSeleccion);
                    $aprobado = array("Aprobado");
                    $valoresGrafico = array_merge($aprobado, $valoresSeleccion);
                    $dataGraficaArpobados = array($mesesGrafico, $valoresGrafico);
                  
                    foreach ($result2 as $valuen) {
                       $mesesSeleccionN[] = $valuen['periodo'];
                       $valoresSeleccionN[] = $valuen['trabajadores'];
                       $cantidadNoCertificados[] = $valuen['cantidad'];
                       $cantidadSubNoCertificados[] = $valuen['Subcertificados'];
                    }
                    $estadoCN = array('estado de Certificacion');
                    $mesesGraficoNo = array_merge($estadoCN, $mesesSeleccionN);
                    $naprobado = array("No Aprobado");
                    $valoresGraficoNo = array_merge($naprobado, $valoresSeleccionN);
                    $dataGraficaNoAporbado = array($mesesGraficoNo, $valoresGraficoNo);
                    $dataGrafica = array($mesesGrafico, $valoresGrafico,$valoresGraficoNo);
                    ///// grfica estado de certificacion ///

                    $mesesCertificado = array('Cantidad');
                    $mesesGraficoCertificado = array_merge($mesesCertificado, $mesesSeleccion);
                    $CertificadoAprobado = array("Certficados");
                    $cantidadCertificadosAprobados = array_merge($CertificadoAprobado, $cantidadCertificados);
                    $CertificadoNoAprobado = array("No Aprobados");
                    $cantidadCertificadosNoApro = array_merge($CertificadoNoAprobado, $cantidadNoCertificados);
                    $dataGraficaCertificado = array($mesesGraficoCertificado, $cantidadCertificadosAprobados,$cantidadCertificadosNoApro);


                    //// grafica SUB CONTRATISTA /////
                    $mesesCertificado = array('Cantidad');
                    $mesesGraficoCertificadoSub = array_merge($mesesCertificado, $mesesSeleccion);
                    $CertificadoAprobadoSub = array("Certficados");
                    $cantidadCertificadosAprobadosSub = array_merge($CertificadoAprobadoSub, $cantidadSubCertificados);
                    $CertificadoNoAprobadoSub = array("No Aprobados");
                    $cantidadCertificadosNoAproSub = array_merge($CertificadoNoAprobadoSub, $cantidadSubNoCertificados);
                    $dataGraficaCertificadoSub = array($mesesGraficoCertificadoSub, $cantidadCertificadosAprobadosSub,$cantidadCertificadosNoAproSub);

                
                    Excel::create('Reporte Certificación con Graficos', function($excel) use ($listaDatosReporte, $dataGrafica,$dataGraficaNoAporbado,$dataGraficaCertificado,$dataGraficaCertificadoSub) {

                            $excel->sheet('Lista General', function($sheet) use($listaDatosReporte) {    
                                $sheet->loadView('reporteCertificacionGrafica.listadoGeneral',compact('listaDatosReporte'));
                            });

                            $excel->sheet('Grafico_por_Trabajador', function($sheet) use($dataGrafica) { 

                                $sheet->fromArray($dataGrafica);

                                $columnCount = count($dataGrafica[0]);
                                $rowCount = count($dataGrafica);
                                $keys = array_keys($dataGrafica[0]);
                                $labels = array();
                                $categories = array();
                                $values = array();
                                for ($i = 1; $i < $columnCount; $i++) {
                               
                                $col = \PHPExcel_Cell::stringFromColumnIndex($i);
                                $labels[] = new \PHPExcel_Chart_DataSeriesValues('String', 'Grafico_por_Trabajador!$' . $col . '$2', null, 1);
                                $categories[] = new \PHPExcel_Chart_DataSeriesValues('String', 'Grafico_por_Trabajador!$A$3:$A$' . ($rowCount + 1), null, $rowCount);
                                $values[] = new \PHPExcel_Chart_DataSeriesValues('Number', 'Grafico_por_Trabajador!$' . $col . '$3:$' . $col . '$' . ($rowCount + 1), null, $rowCount);
                            
                                }
                  

                                $series = new \PHPExcel_Chart_DataSeries(
                                    \PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
                                    \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
                                    range(0, count($values)-1),           // plotOrder
                                    $labels,                              // plotLabel
                                    $categories,                               // plotCategory
                                    $values                               // plotValues
                                );
                                $series->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_BAR);

                                $plotArea = new \PHPExcel_Chart_PlotArea(NULL, array($series));
                                $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                                $title = new \PHPExcel_Chart_Title('Trabajadores Certificados');
                                $yAxisLabel = new \PHPExcel_Chart_Title('Cantidad de trabajdores');
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

                                //    Add the chart to the worksheet
                                $sheet->addChart($chart); 
                            });

                            $excel->sheet('Lista Certificados', function($sheet) use($listaDatosReporte) {    
                                $sheet->loadView('reporteCertificacionGrafica.generalCertificada',compact('listaDatosReporte'));
                            });

                            $excel->sheet('Grafica_por_Empresas', function($sheet) use($dataGraficaCertificado) { 

                                $sheet->fromArray($dataGraficaCertificado);

                                $columnCount = count($dataGraficaCertificado[0]);
                                $rowCount = count($dataGraficaCertificado);
                                $keys = array_keys($dataGraficaCertificado[0]);
                                $labels = array();
                                $categories = array();
                                $values = array();
                                for ($i = 1; $i < $columnCount; $i++) {
                               
                                $col = \PHPExcel_Cell::stringFromColumnIndex($i);
                                $labels[] = new \PHPExcel_Chart_DataSeriesValues('String', 'Grafica_por_Empresas!$' . $col . '$2', null, 1);
                                $categories[] = new \PHPExcel_Chart_DataSeriesValues('String', 'Grafica_por_Empresas!$A$3:$A$' . ($rowCount + 1), null, $rowCount);
                                $values[] = new \PHPExcel_Chart_DataSeriesValues('Number', 'Grafica_por_Empresas!$' . $col . '$3:$' . $col . '$' . ($rowCount + 1), null, $rowCount);
                            
                                }
                  

                                $series = new \PHPExcel_Chart_DataSeries(
                                    \PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
                                    \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
                                    range(0, count($values)-1),           // plotOrder
                                    $labels,                              // plotLabel
                                    $categories,                               // plotCategory
                                    $values                               // plotValues
                                );
                                $series->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_BAR);

                                $plotArea = new \PHPExcel_Chart_PlotArea(NULL, array($series));
                                $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                                $title = new \PHPExcel_Chart_Title('Número de Empresas');
                                $yAxisLabel = new \PHPExcel_Chart_Title('Empresas');
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

                                //    Add the chart to the worksheet
                                $sheet->addChart($chart); 
                            });

                            $excel->sheet('Empresas Sub Contratista', function($sheet) use($listaDatosReporte) {    
                                $sheet->loadView('reporteCertificacionGrafica.subContratistaGeneral',compact('listaDatosReporte'));
                            });

                            $excel->sheet('grafica_SubContratistas', function($sheet) use($dataGraficaCertificadoSub) { 

                                $sheet->fromArray($dataGraficaCertificadoSub);

                                $columnCount = count($dataGraficaCertificadoSub[0]);
                                $rowCount = count($dataGraficaCertificadoSub);
                                $keys = array_keys($dataGraficaCertificadoSub[0]);
                                $labels = array();
                                $categories = array();
                                $values = array();
                                for ($i = 1; $i < $columnCount; $i++) {
                               
                                $col = \PHPExcel_Cell::stringFromColumnIndex($i);
                                $labels[] = new \PHPExcel_Chart_DataSeriesValues('String', 'grafica_SubContratistas!$' . $col . '$2', null, 1);
                                $categories[] = new \PHPExcel_Chart_DataSeriesValues('String', 'grafica_SubContratistas!$A$3:$A$' . ($rowCount + 1), null, $rowCount);
                                $values[] = new \PHPExcel_Chart_DataSeriesValues('Number', 'grafica_SubContratistas!$' . $col . '$3:$' . $col . '$' . ($rowCount + 1), null, $rowCount);
                            
                                }
                  

                                $series = new \PHPExcel_Chart_DataSeries(
                                    \PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
                                    \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
                                    range(0, count($values)-1),           // plotOrder
                                    $labels,                              // plotLabel
                                    $categories,                               // plotCategory
                                    $values                               // plotValues
                                );
                                $series->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_BAR);

                                $plotArea = new \PHPExcel_Chart_PlotArea(NULL, array($series));
                                $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                                $title = new \PHPExcel_Chart_Title('Numero de Empresas Sub Contratista Certificadas');
                                $yAxisLabel = new \PHPExcel_Chart_Title('Empresas');
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

                                //    Add the chart to the worksheet
                                $sheet->addChart($chart); 
                            });
                    })->export('xlsx');   
                   
                });

            }if($peridoInicio != 0 AND $peridoFinal != 0 AND $countContratista == 0 AND $centroCosto == 0){
                $totalempresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                 ->whereBetween('periodId', [$peridoInicio,$peridoFinal])->count();

                $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                 ->whereBetween('periodId', [$peridoInicio,$peridoFinal])
                 ->orderBy('id', 'ASC')->select('id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType','servicioId','classserv','adminContrato')->chunk($totalempresasContratista, function ($query)  use($peridoInicio,$peridoFinal){

                    foreach((array)$query as $empresasContratista){

                        if(!empty($empresasContratista)){

                            foreach($empresasContratista AS $empresa){

                                if($empresa['certificateState'] != 1){

                                    $datosSolictud = Solicitud::distinct()->where('companyId',$empresa['id'])->orderby('serial','DESC')->take(1)->get(['workersNumber','workerstotales','serial'])->toArray();
                                    
                                    if(!empty($datosSolictud)){
                                        $numeroTrabajadoresTotales = $datosSolictud[0]['workerstotales'];
                                        $numeroTrabajadores = $datosSolictud[0]['workersNumber'];
                                    }else{
                                        $numeroTrabajadoresTotales = 0;
                                        $numeroTrabajadores = 0;
                                    }
                                }else{
                                    $numeroTrabajadores = 0;
                                    $numeroTrabajadoresTotales = $empresa['workersNumber'];
                                }

                                if($empresa['certificateState'] == 10 or $empresa['certificateState'] == 5){
                                    $datosCertificado = Certificado::where('companyId',$empresa['id'])->orderby('serial','DESC')->take(1)->get(['number','serial'])->toArray(); 
                                    $numeroCertificado = $datosCertificado[0]['number']."-".$datosCertificado[0]['serial'];
                                }else{
                                   
                                    $numeroCertificado = "";
                                }

                                switch ((int)$empresa['certificateState']) {
                                    case 1:
                                         $estadoCerficacionTexto ="Ingresado";
                                        break;
                                    case 2:
                                         $estadoCerficacionTexto ="Solicitado";
                                        break;
                                    case 3:
                                         $estadoCerficacionTexto ="Aprobado";
                                        break;
                                    case 4:
                                         $estadoCerficacionTexto ="No Aprobado";
                                        break;
                                    case 5:
                                         $estadoCerficacionTexto ="Certificado";
                                        break;
                                    case 6:
                                         $estadoCerficacionTexto ="Documentado";
                                        break;
                                    case 7:
                                         $estadoCerficacionTexto ="Histórico";
                                        break;
                                    case 8:
                                         $estadoCerficacionTexto ="Completo";
                                        break;
                                    case 9:
                                         $estadoCerficacionTexto ="En Proceso";
                                        break;
                                    case 10:
                                         $estadoCerficacionTexto ="No Conforme";
                                        break;
                                    case 11:
                                         $estadoCerficacionTexto ="Inactivo";
                                        break;
                                }

                                if($empresa['certificateState'] == 5 or $empresa['certificateState'] == 6 or $empresa['certificateState'] == 8 or $empresa['certificateState'] == 3 or $empresa['certificateState'] == 4 or $empresa['certificateState'] == 5 or $empresa['certificateState'] == 11 or $empresa['certificateState'] == 10){

                                        $datosCuadratura = Cuadratura::where('companyId',$empresa['id'])->orderby('id','DESC')->take(1)->get(['observations','id'])->toArray();
                                        if(!empty($datosCuadratura)){
                                           
                                            $observaciones = $datosCuadratura[0]['observations'];
                                        }else{
                                          
                                           $observaciones =  $empresa['certificateObservations'];
                                        }
                                }else{
                                    $observaciones =  $empresa['certificateObservations'];
                                }
                                $periodo = DB::table('Period')
                                ->join('Month', 'Month.id', '=', 'Period.monthId')
                                ->where(['Period.id' => $empresa["periodId"]])
                                ->select('Period.year','Month.name')
                                ->get();

                                $periodoTexto =  $periodo[0]->name."-".$periodo[0]->year;            

                                $datosReporte["id"] = $empresa["id"];
                                $datosReporte["rutprincipal"] = $empresa["mainCompanyRut"];
                                $datosReporte["principal"] = ucwords(mb_strtolower($empresa["mainCompanyName"],'UTF-8'));
                                $datosReporte["rutcontratistas"] = $empresa["rut"]."-".$empresa["dv"];
                                $datosReporte["contratista"] = ucwords(mb_strtolower($empresa["name"],'UTF-8'));
                                $datosReporte["center"] = ucwords(mb_strtolower($empresa["center"],'UTF-8'));
                                $datosReporte["rutSubContratista"] = $empresa["subcontratistaRut"]."-".$empresa["subcontratistaDv"];
                                $datosReporte["subcontratistaName"] = ucwords(mb_strtolower($empresa["subcontratistaName"],'UTF-8'));
                                $datosReporte["center"] = ucwords(mb_strtolower($empresa["center"],'UTF-8'));
                                $datosReporte["periodo"] = ucwords(mb_strtolower($periodoTexto,'UTF-8'));
                                $datosReporte["periodoID"] = $empresa["periodId"];
                                $datosReporte['numeroTrabajadoresCertificar'] = $numeroTrabajadores;     
                                $datosReporte['numeroTrabajadoresTotales'] = $numeroTrabajadoresTotales; 
                                $datosReporte["estadoCerticacion"] = ucwords(mb_strtolower($estadoCerficacionTexto,'UTF-8'));
                                $datosReporte["estadoCerticacionId"] = $empresa['certificateState'];
                                $datosReporte["fechaCerticacion"] =  date('d/m/Y', $empresa["certificateDate"]);
                                $datosReporte["observacion"] =  mb_strtolower($observaciones,'UTF-8');

                                if(!empty($datosReporte)){
                                    $listaDatosReporte[] = $datosReporte;
                                    
                                }
                            }  
                        } 
                    }

                         
                    for ($peridoInicio; $peridoInicio <= $peridoFinal; $peridoInicio++) { 
                        $trabajadores = 0;
                        $certificados = 0;
                        $Subcertificados = 0;
                        $trabajadoresNo = 0;  
                        $noCertificado = 0;
                        $SubcertificadosNO = 0;
                        foreach ($listaDatosReporte as $value) {
                            if($peridoInicio == $value['periodoID'] AND $value['estadoCerticacionId'] == 5){
                                $trabajadores = $trabajadores + $value['numeroTrabajadoresCertificar'];
                                $certificados = $certificados + 1;
                                $peridosTrabajadore['periodo'] = $value['periodo'];
                                $peridosTrabajadore['trabajadores'] = $trabajadores;
                                $peridosTrabajadore['cantidad'] = $certificados;

                                if($value['rutSubContratista'] != "-"){
                                    $Subcertificados = $Subcertificados + 1;
                                    $peridosTrabajadore['Subcertificados'] = $Subcertificados;
                                }else{
                                    
                                    $peridosTrabajadore['Subcertificados'] = $Subcertificados;
                                }
                            }
                           
                           if ($peridoInicio == $value['periodoID'] AND $value['estadoCerticacionId'] == 10){
                                $trabajadoresNo = $trabajadoresNo + $value['numeroTrabajadoresCertificar'];
                                $noCertificado = $noCertificado + 1;
                                $peridosTrabajadoreNo['periodo'] = $value['periodo'];
                                $peridosTrabajadoreNo['trabajadores'] =  $trabajadoresNo;
                                $peridosTrabajadoreNo['cantidad'] = $noCertificado;
                                if($value['rutSubContratista'] != "-"){
                                    $SubcertificadosNO = $SubcertificadosNO + 1;
                                    $peridosTrabajadoreNo['Subcertificados'] = $SubcertificadosNO;
                                }else{
                                    
                                    $peridosTrabajadoreNo['Subcertificados'] = $SubcertificadosNO;
                                }
                            }
                        }
                       
                        if (!empty($peridosTrabajadore)) {
                           $listaConforme[] = $peridosTrabajadore;
                        }else{
                            $listaConforme[] = 0;
                        }
                        if (!empty($peridosTrabajadoreNo)) {
                           $listaConformeNo[] = $peridosTrabajadoreNo;
                        }else{
                            $listaConformeNo[] = 0;
                        }
                    }
                    
                    $result1 = array_unique($listaConforme, SORT_REGULAR);
                    $result2 = array_unique($listaConformeNo, SORT_REGULAR);
                  
                    //// grafica trabajadores /////
                    foreach ($result1 as $value) {
                       $mesesSeleccion[] = $value['periodo'];
                       $valoresSeleccion[] = $value['trabajadores'];
                       $cantidadCertificados[] = $value['cantidad'];
                       $cantidadSubCertificados[] = $value['Subcertificados'];
                    }
                    $estadoC = array('estado de Certificacion');
                    $mesesGrafico = array_merge($estadoC, $mesesSeleccion);
                    $aprobado = array("Certificados");
                    $valoresGrafico = array_merge($aprobado, $valoresSeleccion);
                    $dataGraficaArpobados = array($mesesGrafico, $valoresGrafico);
                  
                    foreach ($result2 as $valuen) {
                       $mesesSeleccionN[] = $valuen['periodo'];
                       $valoresSeleccionN[] = $valuen['trabajadores'];
                       $cantidadNoCertificados[] = $valuen['cantidad'];
                       $cantidadSubNoCertificados[] = $valuen['Subcertificados'];
                    }
                    $estadoCN = array('estado de Certificacion');
                    $mesesGraficoNo = array_merge($estadoCN, $mesesSeleccionN);
                    $naprobado = array("No Aprobado");
                    $valoresGraficoNo = array_merge($naprobado, $valoresSeleccionN);
                    $dataGraficaNoAporbado = array($mesesGraficoNo, $valoresGraficoNo);
                    $dataGrafica = array($mesesGrafico, $valoresGrafico,$valoresGraficoNo);
                    ///// grfica estado de certificacion ///

                    $mesesCertificado = array('Estado');
                    $mesesGraficoCertificado = array_merge($mesesCertificado, $mesesSeleccion);
                    $CertificadoAprobado = array("Certificados");
                    $cantidadCertificadosAprobados = array_merge($CertificadoAprobado, $cantidadCertificados);
                    $CertificadoNoAprobado = array("No Aprobados");
                    $cantidadCertificadosNoApro = array_merge($CertificadoNoAprobado, $cantidadNoCertificados);
                    $dataGraficaCertificado = array($mesesGraficoCertificado, $cantidadCertificadosAprobados,$cantidadCertificadosNoApro);


                    //// grafica SUB CONTRATISTA /////
                    $mesesCertificado = array('Estado');
                    $mesesGraficoCertificadoSub = array_merge($mesesCertificado, $mesesSeleccion);
                    $CertificadoAprobadoSub = array("Certificados");
                    $cantidadCertificadosAprobadosSub = array_merge($CertificadoAprobadoSub, $cantidadSubCertificados);
                    $CertificadoNoAprobadoSub = array("No Aprobados");
                    $cantidadCertificadosNoAproSub = array_merge($CertificadoNoAprobadoSub, $cantidadSubNoCertificados);
                    $dataGraficaCertificadoSub = array($mesesGraficoCertificadoSub, $cantidadCertificadosAprobadosSub,$cantidadCertificadosNoAproSub);

                
                   
                    Excel::create('Reporte Certificación con Graficos', function($excel) use ($listaDatosReporte, $dataGrafica,$dataGraficaNoAporbado,$dataGraficaCertificado,$dataGraficaCertificadoSub) {

                            $excel->sheet('Lista General', function($sheet) use($listaDatosReporte) {    
                                $sheet->loadView('reporteCertificacionGrafica.listadoGeneral',compact('listaDatosReporte'));
                            });

                            $excel->sheet('Grafica_por_trabajador', function($sheet) use($dataGrafica) { 

                                $columnCount = count($dataGrafica[0]);
                                $rowCount = count($dataGrafica);
                                $keys = array_keys($dataGrafica[0]);
                                $labels = array();
                                $categories = array();
                                $values = array();
                                $colunmaLetra = \PHPExcel_Cell::stringFromColumnIndex($columnCount-1);
                              
                                $sheet->getStyle('A2:'.$colunmaLetra.'2')->applyFromArray(
                                        array(
                                            'font' => array(
                                                'bold' => true
                                            ),
                                            'alignment' => array(
                                                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                            ),
                                            'borders' => array(
                                                'top' => array(
                                                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                                                )
                                            ),
                                            'fill' => array(
                                                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                                'color' => array('rgb' => 'e3e3e3')
                                            )
                                        )
                                );

                                $sheet->fromArray($dataGrafica);

                                for ($i = 1; $i < $columnCount; $i++) {
                               
                                $col = \PHPExcel_Cell::stringFromColumnIndex($i);
                                $labels[] = new \PHPExcel_Chart_DataSeriesValues('String', 'Grafica_por_trabajador!$' . $col . '$2', null, 1);
                                $categories[] = new \PHPExcel_Chart_DataSeriesValues('String', 'Grafica_por_trabajador!$A$3:$A$' . ($rowCount + 1), null, 1);
                                $values[] = new \PHPExcel_Chart_DataSeriesValues('Number', 'Grafica_por_trabajador!$' . $col . '$3:$' . $col . '$' . ($rowCount + 1), null, $rowCount);
                            
                                }
                  

                                $series = new \PHPExcel_Chart_DataSeries(
                                    \PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
                                    \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
                                    range(0, count($values)-1),           // plotOrder
                                    $labels,                              // plotLabel
                                    $categories,                               // plotCategory
                                    $values                               // plotValues
                                );
                                $series->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_BAR);

                                $plotArea = new \PHPExcel_Chart_PlotArea(NULL, array($series));
                                $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                                $title = new \PHPExcel_Chart_Title('trabajadores Certificados');
                                $yAxisLabel = new \PHPExcel_Chart_Title('Cantidad de trabajdores');
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

                                //    Add the chart to the worksheet
                                $sheet->addChart($chart); 
                            });

                            $excel->sheet('Lista Certificados', function($sheet) use($listaDatosReporte) {    
                                $sheet->loadView('reporteCertificacionGrafica.generalCertificada',compact('listaDatosReporte'));
                            });

                            $excel->sheet('Grafica_por_Empresas', function($sheet) use($dataGraficaCertificado) {

                                $columnCount = count($dataGraficaCertificado[0]);
                                $rowCount = count($dataGraficaCertificado);
                                $keys = array_keys($dataGraficaCertificado[0]);
                                $labels = array();
                                $categories = array();
                                $values = array();
                                $colunmaLetra = \PHPExcel_Cell::stringFromColumnIndex($columnCount-1);
                              
                                $sheet->getStyle('A2:'.$colunmaLetra.'2')->applyFromArray(
                                        array(
                                            'font' => array(
                                                'bold' => true
                                            ),
                                            'alignment' => array(
                                                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                            ),
                                            'borders' => array(
                                                'top' => array(
                                                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                                                )
                                            ),
                                            'fill' => array(
                                                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                                'color' => array('rgb' => 'e3e3e3')
                                            )
                                        )
                                ); 

                                $sheet->fromArray($dataGraficaCertificado);

                                for ($i = 1; $i < $columnCount; $i++) {
                               
                                $col = \PHPExcel_Cell::stringFromColumnIndex($i);
                                $labels[] = new \PHPExcel_Chart_DataSeriesValues('String', 'Grafica_por_Empresas!$' . $col . '$2', null, 1);
                                $categories[] = new \PHPExcel_Chart_DataSeriesValues('String', 'Grafica_por_Empresas!$A$3:$A$' . ($rowCount + 1), null, 1);
                                $values[] = new \PHPExcel_Chart_DataSeriesValues('Number', 'Grafica_por_Empresas!$' . $col . '$3:$' . $col . '$' . ($rowCount + 1), null, $rowCount);
                            
                                }
                  

                                $series = new \PHPExcel_Chart_DataSeries(
                                    \PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
                                    \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
                                    range(0, count($values)-1),           // plotOrder
                                    $labels,                              // plotLabel
                                    $categories,                               // plotCategory
                                    $values                               // plotValues
                                );
                                $series->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_BAR);

                                $plotArea = new \PHPExcel_Chart_PlotArea(NULL, array($series));
                                $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                                $title = new \PHPExcel_Chart_Title('Número de Empresas Certificadas');
                                $yAxisLabel = new \PHPExcel_Chart_Title('Cantidad de empresas');
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

                                //    Add the chart to the worksheet
                                $sheet->addChart($chart); 
                            });

                            $excel->sheet('Empresas Sub Contratista', function($sheet) use($listaDatosReporte) {    
                                $sheet->loadView('reporteCertificacionGrafica.subContratistaGeneral',compact('listaDatosReporte'));
                            });

                            $excel->sheet('grafica_SubContratistas', function($sheet) use($dataGraficaCertificadoSub) { 

                                $columnCount = count($dataGraficaCertificadoSub[0]);
                                $rowCount = count($dataGraficaCertificadoSub);
                                $keys = array_keys($dataGraficaCertificadoSub[0]);
                                $labels = array();
                                $categories = array();
                                $values = array();
                                $colunmaLetra = \PHPExcel_Cell::stringFromColumnIndex($columnCount-1);
                              
                                $sheet->getStyle('A2:'.$colunmaLetra.'2')->applyFromArray(
                                        array(
                                            'font' => array(
                                                'bold' => true
                                            ),
                                            'alignment' => array(
                                                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                            ),
                                            'borders' => array(
                                                'top' => array(
                                                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                                                )
                                            ),
                                            'fill' => array(
                                                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                                'color' => array('rgb' => 'e3e3e3')
                                            )
                                        )
                                ); 

                                $sheet->fromArray($dataGraficaCertificadoSub);

                                for ($i = 1; $i < $columnCount; $i++) {
                               
                                $col = \PHPExcel_Cell::stringFromColumnIndex($i);
                                $labels[] = new \PHPExcel_Chart_DataSeriesValues('String', 'grafica_SubContratistas!$' . $col . '$2', null, 1);
                                $categories[] = new \PHPExcel_Chart_DataSeriesValues('String', 'grafica_SubContratistas!$A$3:$A$' . ($rowCount + 1), null, $rowCount);
                                $values[] = new \PHPExcel_Chart_DataSeriesValues('Number', 'grafica_SubContratistas!$' . $col . '$3:$' . $col . '$' . ($rowCount + 1), null, $rowCount);
                            
                                }
                  

                                $series = new \PHPExcel_Chart_DataSeries(
                                    \PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
                                    \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
                                    range(0, count($values)-1),           // plotOrder
                                    $labels,                              // plotLabel
                                    $categories,                               // plotCategory
                                    $values                               // plotValues
                                );
                                $series->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_BAR);

                                $plotArea = new \PHPExcel_Chart_PlotArea(NULL, array($series));
                                $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                                $title = new \PHPExcel_Chart_Title('Numero de Empresas Sub Contratista Certificadas');
                                $yAxisLabel = new \PHPExcel_Chart_Title('Empresas');
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

                                //    Add the chart to the worksheet
                                $sheet->addChart($chart); 
                            });
                    })->export('xlsx');   
                });
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
                $totalempresasContratista = Contratista::whereIn('mainCompanyRut',$rutprincipalR)
                ->whereIn('rut',$rutcontratistasR)
                ->whereBetween('certificateDate', [$fechasDesde,$fechasHasta])
                ->where('id',$centroCosto)->count();
                $empresasContratista = Contratista::whereIn('mainCompanyRut',$rutprincipalR)
                ->whereIn('rut',$rutcontratistasR)
                ->whereBetween('certificateDate', [$fechasDesde,$fechasHasta])
                ->where('id',$centroCosto)
                ->orderBy('id', 'ASC')->select('id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType','servicioId','classserv','adminContrato')->chunk($totalempresasContratista, function ($query){

                    foreach((array)$query as $empresasContratista){

                        if(!empty($empresasContratista)){

                            foreach($empresasContratista AS $empresa){

                                if($empresa['certificateState'] != 1){

                                    $datosSolictud = Solicitud::distinct()->where('companyId',$empresa['id'])->orderby('serial','DESC')->take(1)->get(['workersNumber','workerstotales','serial'])->toArray();
                                    
                                    if(!empty($datosSolictud)){
                                        $numeroTrabajadoresTotales = $datosSolictud[0]['workerstotales'];
                                        $numeroTrabajadores = $datosSolictud[0]['workersNumber'];
                                    }else{
                                        $numeroTrabajadoresTotales = 0;
                                        $numeroTrabajadores = 0;
                                    }
                                }else{
                                    $numeroTrabajadores = 0;
                                    $numeroTrabajadoresTotales = $empresa['workersNumber'];
                                }

                                if($empresa['certificateState'] == 10 or $empresa['certificateState'] == 5){
                                    $datosCertificado = Certificado::where('companyId',$empresa['id'])->orderby('serial','DESC')->take(1)->get(['number','serial'])->toArray(); 
                                    $numeroCertificado = $datosCertificado[0]['number']."-".$datosCertificado[0]['serial'];
                                }else{
                                   
                                    $numeroCertificado = "";
                                }

                                switch ((int)$empresa['certificateState']) {
                                    case 1:
                                         $estadoCerficacionTexto ="Ingresado";
                                        break;
                                    case 2:
                                         $estadoCerficacionTexto ="Solicitado";
                                        break;
                                    case 3:
                                         $estadoCerficacionTexto ="Aprobado";
                                        break;
                                    case 4:
                                         $estadoCerficacionTexto ="No Aprobado";
                                        break;
                                    case 5:
                                         $estadoCerficacionTexto ="Certificado";
                                        break;
                                    case 6:
                                         $estadoCerficacionTexto ="Documentado";
                                        break;
                                    case 7:
                                         $estadoCerficacionTexto ="Histórico";
                                        break;
                                    case 8:
                                         $estadoCerficacionTexto ="Completo";
                                        break;
                                    case 9:
                                         $estadoCerficacionTexto ="En Proceso";
                                        break;
                                    case 10:
                                         $estadoCerficacionTexto ="No Conforme";
                                        break;
                                    case 11:
                                         $estadoCerficacionTexto ="Inactivo";
                                        break;
                                }

                                if($empresa['certificateState'] == 5 or $empresa['certificateState'] == 6 or $empresa['certificateState'] == 8 or $empresa['certificateState'] == 3 or $empresa['certificateState'] == 4 or $empresa['certificateState'] == 5 or $empresa['certificateState'] == 11 or $empresa['certificateState'] == 10){

                                        $datosCuadratura = Cuadratura::where('companyId',$empresa['id'])->orderby('id','DESC')->take(1)->get(['observations','id'])->toArray();
                                        if(!empty($datosCuadratura)){
                                           
                                            $observaciones = $datosCuadratura[0]['observations'];
                                        }else{
                                          
                                           $observaciones =  $empresa['certificateObservations'];
                                        }
                                }else{
                                    $observaciones =  $empresa['certificateObservations'];
                                }
                                $periodo = DB::table('Period')
                                ->join('Month', 'Month.id', '=', 'Period.monthId')
                                ->where(['Period.id' => $empresa["periodId"]])
                                ->select('Period.year','Month.name')
                                ->get();

                                $periodoTexto =  $periodo[0]->name."-".$periodo[0]->year;            
                                $datosReporte["id"] = $empresa["id"];
                                $datosReporte["rutprincipal"] = $empresa["mainCompanyRut"];
                                $datosReporte["principal"] = ucwords(mb_strtolower($empresa["mainCompanyName"],'UTF-8'));
                                $datosReporte["rutcontratistas"] = $empresa["rut"]."-".$empresa["dv"];
                                $datosReporte["contratista"] = ucwords(mb_strtolower($empresa["name"],'UTF-8'));
                                $datosReporte["center"] = ucwords(mb_strtolower($empresa["center"],'UTF-8'));
                                $datosReporte["rutSubContratista"] = $empresa["subcontratistaRut"]."-".$empresa["subcontratistaDv"];
                                $datosReporte["subcontratistaName"] = ucwords(mb_strtolower($empresa["subcontratistaName"],'UTF-8'));
                                $datosReporte["center"] = ucwords(mb_strtolower($empresa["center"],'UTF-8'));
                                $datosReporte["periodo"] = ucwords(mb_strtolower($periodoTexto,'UTF-8'));
                                $datosReporte["periodoID"] = $empresa["periodId"];
                                $datosReporte['numeroTrabajadoresCertificar'] = $numeroTrabajadores;     
                                $datosReporte['numeroTrabajadoresTotales'] = $numeroTrabajadoresTotales; 
                                $datosReporte["estadoCerticacion"] = ucwords(mb_strtolower($estadoCerficacionTexto,'UTF-8'));
                                $datosReporte["estadoCerticacionId"] = $empresa['certificateState'];
                                $datosReporte["fechaCerticacion"] =  date('d/m/Y', $empresa["certificateDate"]);
                                $datosReporte["observacion"] =  mb_strtolower($observaciones,'UTF-8');

                                if(!empty($datosReporte)){
                                    $listaDatosReporte[] = $datosReporte;
                                    
                                }
                            }  
                        } 
                    }

                    /// ordeno los periodos
                    foreach ($listaDatosReporte as $perido) {
                        $periodos[]=$perido['periodoID'];
                    }

                    $peridosAc = array_unique($periodos);
                    $periodosOrdenados = sort($peridosAc);
                    $peridoInicio = reset($peridosAc);
                    $peridoFinal = end($peridosAc);
                    //////
                         
                    for ($peridoInicio; $peridoInicio <= $peridoFinal; $peridoInicio++) { 
                        $trabajadores = 0;
                        $certificados = 0;
                        $Subcertificados = 0;
                        $trabajadoresNo = 0;  
                        $noCertificado = 0;
                        $SubcertificadosNO = 0;
                        foreach ($listaDatosReporte as $value) {
                            if($peridoInicio == $value['periodoID'] AND $value['estadoCerticacionId'] == 5){
                                $trabajadores = $trabajadores + $value['numeroTrabajadoresCertificar'];
                                $certificados = $certificados + 1;
                                $peridosTrabajadore['periodo'] = $value['periodo'];
                                $peridosTrabajadore['trabajadores'] = $trabajadores;
                                $peridosTrabajadore['cantidad'] = $certificados;

                                if($value['rutSubContratista'] != "-"){
                                    $Subcertificados = $Subcertificados + 1;
                                    $peridosTrabajadore['Subcertificados'] = $Subcertificados;
                                }else{
                                    
                                    $peridosTrabajadore['Subcertificados'] = $Subcertificados;
                                }
                            }
                           
                           if ($peridoInicio == $value['periodoID'] AND $value['estadoCerticacionId'] == 10){
                                $trabajadoresNo = $trabajadoresNo + $value['numeroTrabajadoresCertificar'];
                                $noCertificado = $noCertificado + 1;
                                $peridosTrabajadoreNo['periodo'] = $value['periodo'];
                                $peridosTrabajadoreNo['trabajadores'] =  $trabajadoresNo;
                                $peridosTrabajadoreNo['cantidad'] = $noCertificado;
                                if($value['rutSubContratista'] != "-"){
                                    $SubcertificadosNO = $SubcertificadosNO + 1;
                                    $peridosTrabajadoreNo['Subcertificados'] = $SubcertificadosNO;
                                }else{
                                    
                                    $peridosTrabajadoreNo['Subcertificados'] = $SubcertificadosNO;
                                }
                            }
                        }
                        if (!empty($peridosTrabajadore)) {
                           $listaConforme[] = $peridosTrabajadore;
                        }else{
                            $listaConforme[] = 0;
                        }
                        if (!empty($peridosTrabajadoreNo)) {
                           $listaConformeNo[] = $peridosTrabajadoreNo;
                        }else{
                            $listaConformeNo[] = 0;
                        }
                        
                    }
                    
                    $result1 = array_unique($listaConforme, SORT_REGULAR);
                    $result2 = array_unique($listaConformeNo, SORT_REGULAR);
                  
                    //// grafica trabajadores /////
                    foreach ($result1 as $value) {
                       $mesesSeleccion[] = $value['periodo'];
                       $valoresSeleccion[] = $value['trabajadores'];
                       $cantidadCertificados[] = $value['cantidad'];
                       $cantidadSubCertificados[] = $value['Subcertificados'];
                    }
                    $estadoC = array('estado de Certificacion');
                    $mesesGrafico = array_merge($estadoC, $mesesSeleccion);
                    $aprobado = array("Aprobado");
                    $valoresGrafico = array_merge($aprobado, $valoresSeleccion);
                    $dataGraficaArpobados = array($mesesGrafico, $valoresGrafico);
                  
                    foreach ($result2 as $valuen) {
                       $mesesSeleccionN[] = $valuen['periodo'];
                       $valoresSeleccionN[] = $valuen['trabajadores'];
                       $cantidadNoCertificados[] = $valuen['cantidad'];
                       $cantidadSubNoCertificados[] = $valuen['Subcertificados'];
                    }
                    $estadoCN = array('estado de Certificacion');
                    $mesesGraficoNo = array_merge($estadoCN, $mesesSeleccionN);
                    $naprobado = array("No Aprobado");
                    $valoresGraficoNo = array_merge($naprobado, $valoresSeleccionN);
                    $dataGraficaNoAporbado = array($mesesGraficoNo, $valoresGraficoNo);
                    $dataGrafica = array($mesesGrafico, $valoresGrafico,$valoresGraficoNo);
                    ///// grfica estado de certificacion ///

                    $mesesCertificado = array('Cantidad');
                    $mesesGraficoCertificado = array_merge($mesesCertificado, $mesesSeleccion);
                    $CertificadoAprobado = array("Aprobados");
                    $cantidadCertificadosAprobados = array_merge($CertificadoAprobado, $cantidadCertificados);
                    $CertificadoNoAprobado = array("No Aprobados");
                    $cantidadCertificadosNoApro = array_merge($CertificadoNoAprobado, $cantidadNoCertificados);
                    $dataGraficaCertificado = array($mesesGraficoCertificado, $cantidadCertificadosAprobados,$cantidadCertificadosNoApro);

                    //// grafica SUB CONTRATISTA /////
                    $mesesCertificado = array('Cantidad');
                    $mesesGraficoCertificadoSub = array_merge($mesesCertificado, $mesesSeleccion);
                    $CertificadoAprobadoSub = array("Aprobados");
                    $cantidadCertificadosAprobadosSub = array_merge($CertificadoAprobadoSub, $cantidadSubCertificados);
                    $CertificadoNoAprobadoSub = array("No Aprobados");
                    $cantidadCertificadosNoAproSub = array_merge($CertificadoNoAprobadoSub, $cantidadSubNoCertificados);
                    $dataGraficaCertificadoSub = array($mesesGraficoCertificadoSub, $cantidadCertificadosAprobadosSub,$cantidadCertificadosNoAproSub);

                    Excel::create('Reporte Certificación con Graficos', function($excel) use ($listaDatosReporte, $dataGrafica,$dataGraficaNoAporbado,$dataGraficaCertificado,$dataGraficaCertificadoSub) {

                            $excel->sheet('Lista General', function($sheet) use($listaDatosReporte) {    
                                $sheet->loadView('reporteCertificacionGrafica.listadoGeneral',compact('listaDatosReporte'));
                            });

                            $excel->sheet('grafica', function($sheet) use($dataGrafica) { 

                                $columnCount = count($dataGrafica[0]);
                                $rowCount = count($dataGrafica);
                                $keys = array_keys($dataGrafica[0]);
                                $labels = array();
                                $categories = array();
                                $values = array();
                                $colunmaLetra = \PHPExcel_Cell::stringFromColumnIndex($columnCount-1);
                              
                                $sheet->getStyle('A2:'.$colunmaLetra.'2')->applyFromArray(
                                        array(
                                            'font' => array(
                                                'bold' => true
                                            ),
                                            'alignment' => array(
                                                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                            ),
                                            'borders' => array(
                                                'top' => array(
                                                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                                                )
                                            ),
                                            'fill' => array(
                                                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                                'color' => array('rgb' => 'e3e3e3')
                                            )
                                        )
                                );

                                $sheet->fromArray($dataGrafica);

                                for ($i = 1; $i < $columnCount; $i++) {
                               
                                $col = \PHPExcel_Cell::stringFromColumnIndex($i);
                                $labels[] = new \PHPExcel_Chart_DataSeriesValues('String', 'grafica!$' . $col . '$2', null, 1);
                                $categories[] = new \PHPExcel_Chart_DataSeriesValues('String', 'grafica!$A$2:$A$' . ($rowCount + 1), null, $rowCount);
                                $values[] = new \PHPExcel_Chart_DataSeriesValues('Number', 'grafica!$' . $col . '$2:$' . $col . '$' . ($rowCount + 1), null, $rowCount);
                            
                                }
                  

                                $series = new \PHPExcel_Chart_DataSeries(
                                    \PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
                                    \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
                                    range(0, count($values)-1),           // plotOrder
                                    $labels,                              // plotLabel
                                    $categories,                               // plotCategory
                                    $values                               // plotValues
                                );
                                $series->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_BAR);

                                $plotArea = new \PHPExcel_Chart_PlotArea(NULL, array($series));
                                $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                                $title = new \PHPExcel_Chart_Title('trabajadores Certificados / No aprobados');
                                $yAxisLabel = new \PHPExcel_Chart_Title('Cantidad de trabajdores');
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

                                //    Add the chart to the worksheet
                                $sheet->addChart($chart); 
                            });

                            $excel->sheet('Lista Certificados', function($sheet) use($listaDatosReporte) {    
                                $sheet->loadView('reporteCertificacionGrafica.generalCertificada',compact('listaDatosReporte'));
                            });

                            $excel->sheet('grafica_Certificados', function($sheet) use($dataGraficaCertificado) { 

                                $columnCount = count($dataGraficaCertificado[0]);
                                $rowCount = count($dataGraficaCertificado);
                                $keys = array_keys($dataGraficaCertificado[0]);
                                $labels = array();
                                $categories = array();
                                $values = array();
                                $colunmaLetra = \PHPExcel_Cell::stringFromColumnIndex($columnCount-1);
                              
                                $sheet->getStyle('A2:'.$colunmaLetra.'2')->applyFromArray(
                                        array(
                                            'font' => array(
                                                'bold' => true
                                            ),
                                            'alignment' => array(
                                                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                            ),
                                            'borders' => array(
                                                'top' => array(
                                                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                                                )
                                            ),
                                            'fill' => array(
                                                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                                'color' => array('rgb' => 'e3e3e3')
                                            )
                                        )
                                ); 

                                $sheet->fromArray($dataGraficaCertificado);

                                for ($i = 1; $i < $columnCount; $i++) {
                               
                                $col = \PHPExcel_Cell::stringFromColumnIndex($i);
                                $labels[] = new \PHPExcel_Chart_DataSeriesValues('String', 'grafica_Certificados!$' . $col . '$2', null, 1);
                                $categories[] = new \PHPExcel_Chart_DataSeriesValues('String', 'grafica_Certificados!$A$2:$A$' . ($rowCount + 1), null, $rowCount);
                                $values[] = new \PHPExcel_Chart_DataSeriesValues('Number', 'grafica_Certificados!$' . $col . '$2:$' . $col . '$' . ($rowCount + 1), null, $rowCount);
                            
                                }
                  

                                $series = new \PHPExcel_Chart_DataSeries(
                                    \PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
                                    \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
                                    range(0, count($values)-1),           // plotOrder
                                    $labels,                              // plotLabel
                                    $categories,                               // plotCategory
                                    $values                               // plotValues
                                );
                                $series->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_BAR);

                                $plotArea = new \PHPExcel_Chart_PlotArea(NULL, array($series));
                                $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                                $title = new \PHPExcel_Chart_Title('Certificados Aprobados / No Aprobados');
                                $yAxisLabel = new \PHPExcel_Chart_Title('Cantidad de certificados');
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

                                //    Add the chart to the worksheet
                                $sheet->addChart($chart); 
                            });

                            $excel->sheet('Empresas Sub Contratista', function($sheet) use($listaDatosReporte) {    
                                $sheet->loadView('reporteCertificacionGrafica.subContratistaGeneral',compact('listaDatosReporte'));
                            });

                            $excel->sheet('grafica_SubContratistas', function($sheet) use($dataGraficaCertificadoSub) { 

                                $columnCount = count($dataGraficaCertificadoSub[0]);
                                $rowCount = count($dataGraficaCertificadoSub);
                                $keys = array_keys($dataGraficaCertificadoSub[0]);
                                $labels = array();
                                $categories = array();
                                $values = array();
                                $colunmaLetra = \PHPExcel_Cell::stringFromColumnIndex($columnCount-1);
                              
                                $sheet->getStyle('A2:'.$colunmaLetra.'2')->applyFromArray(
                                        array(
                                            'font' => array(
                                                'bold' => true
                                            ),
                                            'alignment' => array(
                                                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                            ),
                                            'borders' => array(
                                                'top' => array(
                                                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                                                )
                                            ),
                                            'fill' => array(
                                                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                                'color' => array('rgb' => 'e3e3e3')
                                            )
                                        )
                                ); 

                                $sheet->fromArray($dataGraficaCertificadoSub);

                                for ($i = 1; $i < $columnCount; $i++) {
                               
                                    $col = \PHPExcel_Cell::stringFromColumnIndex($i);
                                    $labels[] = new \PHPExcel_Chart_DataSeriesValues('String', 'grafica_SubContratistas!$' . $col . '$2', null, 1);
                                    $categories[] = new \PHPExcel_Chart_DataSeriesValues('String', 'grafica_SubContratistas!$A$2:$A$' . ($rowCount + 1), null, $rowCount);
                                    $values[] = new \PHPExcel_Chart_DataSeriesValues('Number', 'grafica_SubContratistas!$' . $col . '$2:$' . $col . '$' . ($rowCount + 1), null, $rowCount);
                                }
                  
                                $series = new \PHPExcel_Chart_DataSeries(
                                    \PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
                                    \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
                                    range(0, count($values)-1),           // plotOrder
                                    $labels,                              // plotLabel
                                    $categories,                               // plotCategory
                                    $values                               // plotValues
                                );
                                $series->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_BAR);

                                $plotArea = new \PHPExcel_Chart_PlotArea(NULL, array($series));
                                $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                                $title = new \PHPExcel_Chart_Title('Numero de Empresas Sub Contratista Certificadas');
                                $yAxisLabel = new \PHPExcel_Chart_Title('Empresas');
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

                                //    Add the chart to the worksheet
                                $sheet->addChart($chart); 
                            });
                    })->export('xlsx');

                });

            }if($fechaSeleccion != 0  AND $countContratista != 0 ){
            
                $fechas = $porciones = explode("_", $fechaSeleccion);
                $fecha1 = $fechas[0];
                $fecha2 = $fechas[1];
                $periodosT = $fecha1 ."-".$fecha2;
                $fechasDesde = strtotime ( '+4 hour' ,strtotime($fecha1));
                //sumo 1 día
                $fechasHasta = strtotime ( '+4 hour' ,strtotime($fecha2));
                $totalempresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                ->whereIn('rut',$rutcontratistasR)
                ->whereBetween('certificateDate', [$fechasDesde,$fechasHasta])->count();
                $empresasContratista = Contratista::distinct()->whereIn('mainCompanyRut',$rutprincipalR)
                ->whereIn('rut',$rutcontratistasR)
                ->whereBetween('certificateDate', [$fechasDesde,$fechasHasta])
                ->orderBy('id', 'ASC')->select('id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType','servicioId','classserv','adminContrato')->chunk($totalempresasContratista, function ($query) {

                        foreach((array)$query as $empresasContratista){

                        if(!empty($empresasContratista)){

                            foreach($empresasContratista AS $empresa){

                                if($empresa['certificateState'] != 1){

                                    $datosSolictud = Solicitud::distinct()->where('companyId',$empresa['id'])->orderby('serial','DESC')->take(1)->get(['workersNumber','workerstotales','serial'])->toArray();
                                    
                                    if(!empty($datosSolictud)){
                                        $numeroTrabajadoresTotales = $datosSolictud[0]['workerstotales'];
                                        $numeroTrabajadores = $datosSolictud[0]['workersNumber'];
                                    }else{
                                        $numeroTrabajadoresTotales = 0;
                                        $numeroTrabajadores = 0;
                                    }
                                }else{
                                    $numeroTrabajadores = 0;
                                    $numeroTrabajadoresTotales = $empresa['workersNumber'];
                                }

                                if($empresa['certificateState'] == 10 or $empresa['certificateState'] == 5){
                                    $datosCertificado = Certificado::where('companyId',$empresa['id'])->orderby('serial','DESC')->take(1)->get(['number','serial'])->toArray(); 
                                    $numeroCertificado = $datosCertificado[0]['number']."-".$datosCertificado[0]['serial'];
                                }else{
                                   
                                    $numeroCertificado = "";
                                }

                                switch ((int)$empresa['certificateState']) {
                                    case 1:
                                         $estadoCerficacionTexto ="Ingresado";
                                        break;
                                    case 2:
                                         $estadoCerficacionTexto ="Solicitado";
                                        break;
                                    case 3:
                                         $estadoCerficacionTexto ="Aprobado";
                                        break;
                                    case 4:
                                         $estadoCerficacionTexto ="No Aprobado";
                                        break;
                                    case 5:
                                         $estadoCerficacionTexto ="Certificado";
                                        break;
                                    case 6:
                                         $estadoCerficacionTexto ="Documentado";
                                        break;
                                    case 7:
                                         $estadoCerficacionTexto ="Histórico";
                                        break;
                                    case 8:
                                         $estadoCerficacionTexto ="Completo";
                                        break;
                                    case 9:
                                         $estadoCerficacionTexto ="En Proceso";
                                        break;
                                    case 10:
                                         $estadoCerficacionTexto ="No Conforme";
                                        break;
                                    case 11:
                                         $estadoCerficacionTexto ="Inactivo";
                                        break;
                                }

                                if($empresa['certificateState'] == 5 or $empresa['certificateState'] == 6 or $empresa['certificateState'] == 8 or $empresa['certificateState'] == 3 or $empresa['certificateState'] == 4 or $empresa['certificateState'] == 5 or $empresa['certificateState'] == 11 or $empresa['certificateState'] == 10){

                                        $datosCuadratura = Cuadratura::where('companyId',$empresa['id'])->orderby('id','DESC')->take(1)->get(['observations','id'])->toArray();
                                        if(!empty($datosCuadratura)){
                                           
                                            $observaciones = $datosCuadratura[0]['observations'];
                                        }else{
                                          
                                           $observaciones =  $empresa['certificateObservations'];
                                        }
                                }else{
                                    $observaciones =  $empresa['certificateObservations'];
                                }
                                $periodo = DB::table('Period')
                                ->join('Month', 'Month.id', '=', 'Period.monthId')
                                ->where(['Period.id' => $empresa["periodId"]])
                                ->select('Period.year','Month.name')
                                ->get();

                                $periodoTexto =  $periodo[0]->name."-".$periodo[0]->year;            
                                $datosReporte["id"] = $empresa["id"];
                                $datosReporte["rutprincipal"] = $empresa["mainCompanyRut"];
                                $datosReporte["principal"] = ucwords(mb_strtolower($empresa["mainCompanyName"],'UTF-8'));
                                $datosReporte["rutcontratistas"] = $empresa["rut"]."-".$empresa["dv"];
                                $datosReporte["contratista"] = ucwords(mb_strtolower($empresa["name"],'UTF-8'));
                                $datosReporte["center"] = ucwords(mb_strtolower($empresa["center"],'UTF-8'));
                                $datosReporte["rutSubContratista"] = $empresa["subcontratistaRut"]."-".$empresa["subcontratistaDv"];
                                $datosReporte["subcontratistaName"] = ucwords(mb_strtolower($empresa["subcontratistaName"],'UTF-8'));
                                $datosReporte["center"] = ucwords(mb_strtolower($empresa["center"],'UTF-8'));
                                $datosReporte["periodo"] = ucwords(mb_strtolower($periodoTexto,'UTF-8'));
                                $datosReporte["periodoID"] = $empresa["periodId"];
                                $datosReporte['numeroTrabajadoresCertificar'] = $numeroTrabajadores;     
                                $datosReporte['numeroTrabajadoresTotales'] = $numeroTrabajadoresTotales; 
                                $datosReporte["estadoCerticacion"] = ucwords(mb_strtolower($estadoCerficacionTexto,'UTF-8'));
                                $datosReporte["estadoCerticacionId"] = $empresa['certificateState'];
                                $datosReporte["fechaCerticacion"] =  date('d/m/Y', $empresa["certificateDate"]);
                                $datosReporte["observacion"] =  mb_strtolower($observaciones,'UTF-8');

                                if(!empty($datosReporte)){
                                    $listaDatosReporte[] = $datosReporte;
                                    
                                }
                            }  
                        } 
                    }

                    /// ordeno los periodos
                    foreach ($listaDatosReporte as $perido) {
                        $periodos[]=$perido['periodoID'];
                    }

                    $peridosAc = array_unique($periodos);
                    $periodosOrdenados = sort($peridosAc);
                    $peridoInicio = reset($peridosAc);
                    $peridoFinal = end($peridosAc);
                    //////

                    for ($peridoInicio; $peridoInicio <= $peridoFinal; $peridoInicio++) { 
                        $trabajadores = 0;
                        $certificados = 0;
                        $Subcertificados = 0;
                        $trabajadoresNo = 0;  
                        $noCertificado = 0;
                        $SubcertificadosNO = 0;
                        foreach ($listaDatosReporte as $value) {
                            if($peridoInicio == $value['periodoID'] AND $value['estadoCerticacionId'] == 5){
                                $trabajadores = $trabajadores + $value['numeroTrabajadoresCertificar'];
                                $certificados = $certificados + 1;
                                $peridosTrabajadore['periodo'] = $value['periodo'];
                                $peridosTrabajadore['trabajadores'] = $trabajadores;
                                $peridosTrabajadore['cantidad'] = $certificados;

                                if($value['rutSubContratista'] != "-"){
                                    $Subcertificados = $Subcertificados + 1;
                                    $peridosTrabajadore['Subcertificados'] = $Subcertificados;
                                }else{
                                    
                                    $peridosTrabajadore['Subcertificados'] = $Subcertificados;
                                }
                            }
                           
                           if ($peridoInicio == $value['periodoID'] AND $value['estadoCerticacionId'] == 10){
                                $trabajadoresNo = $trabajadoresNo + $value['numeroTrabajadoresCertificar'];
                                $noCertificado = $noCertificado + 1;
                                $peridosTrabajadoreNo['periodo'] = $value['periodo'];
                                $peridosTrabajadoreNo['trabajadores'] =  $trabajadoresNo;
                                $peridosTrabajadoreNo['cantidad'] = $noCertificado;
                                if($value['rutSubContratista'] != "-"){
                                    $SubcertificadosNO = $SubcertificadosNO + 1;
                                    $peridosTrabajadoreNo['Subcertificados'] = $SubcertificadosNO;
                                }else{
                                    
                                    $peridosTrabajadoreNo['Subcertificados'] = $SubcertificadosNO;
                                }
                            }
                        }
                        if (!empty($peridosTrabajadore)) {
                           $listaConforme[] = $peridosTrabajadore;
                        }else{
                            $listaConforme[] = 0;
                        }
                        if (!empty($peridosTrabajadoreNo)) {
                           $listaConformeNo[] = $peridosTrabajadoreNo;
                        }else{
                            $listaConformeNo[] = 0;
                        }
                    }
                    
                    $result1 = array_unique($listaConforme, SORT_REGULAR);
                    $result2 = array_unique($listaConformeNo, SORT_REGULAR);
                  
                    //// grafica trabajadores /////
                    foreach ($result1 as $value) {
                       $mesesSeleccion[] = $value['periodo'];
                       $valoresSeleccion[] = $value['trabajadores'];
                       $cantidadCertificados[] = $value['cantidad'];
                       $cantidadSubCertificados[] = $value['Subcertificados'];
                    }
                    $estadoC = array('estado de Certificacion');
                    $mesesGrafico = array_merge($estadoC, $mesesSeleccion);
                    $aprobado = array("Aprobado");
                    $valoresGrafico = array_merge($aprobado, $valoresSeleccion);
                    $dataGraficaArpobados = array($mesesGrafico, $valoresGrafico);
                  
                    foreach ($result2 as $valuen) {
                       $mesesSeleccionN[] = $valuen['periodo'];
                       $valoresSeleccionN[] = $valuen['trabajadores'];
                       $cantidadNoCertificados[] = $valuen['cantidad'];
                       $cantidadSubNoCertificados[] = $valuen['Subcertificados'];
                    }
                    $estadoCN = array('estado de Certificacion');
                    $mesesGraficoNo = array_merge($estadoCN, $mesesSeleccionN);
                    $naprobado = array("No Aprobado");
                    $valoresGraficoNo = array_merge($naprobado, $valoresSeleccionN);
                    $dataGraficaNoAporbado = array($mesesGraficoNo, $valoresGraficoNo);
                    $dataGrafica = array($mesesGrafico, $valoresGrafico,$valoresGraficoNo);
                    ///// grfica estado de certificacion ///

                    $mesesCertificado = array('Cantidad');
                    $mesesGraficoCertificado = array_merge($mesesCertificado, $mesesSeleccion);
                    $CertificadoAprobado = array("Aprobados");
                    $cantidadCertificadosAprobados = array_merge($CertificadoAprobado, $cantidadCertificados);
                    $CertificadoNoAprobado = array("No Aprobados");
                    $cantidadCertificadosNoApro = array_merge($CertificadoNoAprobado, $cantidadNoCertificados);
                    $dataGraficaCertificado = array($mesesGraficoCertificado, $cantidadCertificadosAprobados,$cantidadCertificadosNoApro);


                    //// grafica SUB CONTRATISTA /////
                    $mesesCertificado = array('Cantidad');
                    $mesesGraficoCertificadoSub = array_merge($mesesCertificado, $mesesSeleccion);
                    $CertificadoAprobadoSub = array("Aprobados");
                    $cantidadCertificadosAprobadosSub = array_merge($CertificadoAprobadoSub, $cantidadSubCertificados);
                    $CertificadoNoAprobadoSub = array("No Aprobados");
                    $cantidadCertificadosNoAproSub = array_merge($CertificadoNoAprobadoSub, $cantidadSubNoCertificados);
                    $dataGraficaCertificadoSub = array($mesesGraficoCertificadoSub, $cantidadCertificadosAprobadosSub,$cantidadCertificadosNoAproSub);

                        Excel::create('Reporte Certificación con Graficos', function($excel) use ($listaDatosReporte, $dataGrafica,$dataGraficaNoAporbado,$dataGraficaCertificado,$dataGraficaCertificadoSub) {

                                $excel->sheet('Lista General', function($sheet) use($listaDatosReporte) {    
                                    $sheet->loadView('reporteCertificacionGrafica.listadoGeneral',compact('listaDatosReporte'));
                                });

                                $excel->sheet('grafica', function($sheet) use($dataGrafica) { 

                                    $columnCount = count($dataGrafica[0]);
                                    $rowCount = count($dataGrafica);
                                    $keys = array_keys($dataGrafica[0]);
                                    $labels = array();
                                    $categories = array();
                                    $values = array();
                                    $colunmaLetra = \PHPExcel_Cell::stringFromColumnIndex($columnCount-1);
                                  
                                    $sheet->getStyle('A2:'.$colunmaLetra.'2')->applyFromArray(
                                            array(
                                                'font' => array(
                                                    'bold' => true
                                                ),
                                                'alignment' => array(
                                                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                ),
                                                'borders' => array(
                                                    'top' => array(
                                                        'style' => \PHPExcel_Style_Border::BORDER_THIN
                                                    )
                                                ),
                                                'fill' => array(
                                                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                                    'color' => array('rgb' => 'e3e3e3')
                                                )
                                            )
                                    );

                                    $sheet->fromArray($dataGrafica);

                                    for ($i = 1; $i < $columnCount; $i++) {
                                   
                                    $col = \PHPExcel_Cell::stringFromColumnIndex($i);
                                    $labels[] = new \PHPExcel_Chart_DataSeriesValues('String', 'grafica!$' . $col . '$2', null, 1);
                                    $categories[] = new \PHPExcel_Chart_DataSeriesValues('String', 'grafica!$A$3:$A$' . ($rowCount + 1), null, $rowCount);
                                    $values[] = new \PHPExcel_Chart_DataSeriesValues('Number', 'grafica!$' . $col . '$3:$' . $col . '$' . ($rowCount + 1), null, $rowCount);
                                
                                    }
                      

                                    $series = new \PHPExcel_Chart_DataSeries(
                                        \PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
                                        \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
                                        range(0, count($values)-1),           // plotOrder
                                        $labels,                              // plotLabel
                                        $categories,                               // plotCategory
                                        $values                               // plotValues
                                    );
                                    $series->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_BAR);

                                    $plotArea = new \PHPExcel_Chart_PlotArea(NULL, array($series));
                                    $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                                    $title = new \PHPExcel_Chart_Title('trabajadores Certificados / No aprobados');
                                    $yAxisLabel = new \PHPExcel_Chart_Title('Cantidad de trabajdores');
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

                                    //    Add the chart to the worksheet
                                    $sheet->addChart($chart); 
                                });

                                $excel->sheet('Lista Certificados', function($sheet) use($listaDatosReporte) {    
                                    $sheet->loadView('reporteCertificacionGrafica.generalCertificada',compact('listaDatosReporte'));
                                });

                                $excel->sheet('grafica_Certificados', function($sheet) use($dataGraficaCertificado) { 

                                    $columnCount = count($dataGraficaCertificado[0]);
                                    $rowCount = count($dataGraficaCertificado);
                                    $keys = array_keys($dataGraficaCertificado[0]);
                                    $labels = array();
                                    $categories = array();
                                    $values = array();
                                    $colunmaLetra = \PHPExcel_Cell::stringFromColumnIndex($columnCount-1);
                                  
                                    $sheet->getStyle('A2:'.$colunmaLetra.'2')->applyFromArray(
                                            array(
                                                'font' => array(
                                                    'bold' => true
                                                ),
                                                'alignment' => array(
                                                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                ),
                                                'borders' => array(
                                                    'top' => array(
                                                        'style' => \PHPExcel_Style_Border::BORDER_THIN
                                                    )
                                                ),
                                                'fill' => array(
                                                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                                    'color' => array('rgb' => 'e3e3e3')
                                                )
                                            )
                                    ); 

                                    $sheet->fromArray($dataGraficaCertificado);

                                    for ($i = 1; $i < $columnCount; $i++) {
                                   
                                    $col = \PHPExcel_Cell::stringFromColumnIndex($i);
                                    $labels[] = new \PHPExcel_Chart_DataSeriesValues('String', 'grafica_Certificados!$' . $col . '$2', null, 1);
                                    $categories[] = new \PHPExcel_Chart_DataSeriesValues('String', 'grafica_Certificados!$A$3:$A$' . ($rowCount + 1), null, $rowCount);
                                    $values[] = new \PHPExcel_Chart_DataSeriesValues('Number', 'grafica_Certificados!$' . $col . '$3:$' . $col . '$' . ($rowCount + 1), null, $rowCount);
                                
                                    }
                      

                                    $series = new \PHPExcel_Chart_DataSeries(
                                        \PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
                                        \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
                                        range(0, count($values)-1),           // plotOrder
                                        $labels,                              // plotLabel
                                        $categories,                               // plotCategory
                                        $values                               // plotValues
                                    );
                                    $series->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_BAR);

                                    $plotArea = new \PHPExcel_Chart_PlotArea(NULL, array($series));
                                    $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                                    $title = new \PHPExcel_Chart_Title('Certificados Aprobados / No Aprobados');
                                    $yAxisLabel = new \PHPExcel_Chart_Title('Cantidad de certificados');
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

                                    //    Add the chart to the worksheet
                                    $sheet->addChart($chart); 
                                });

                                $excel->sheet('Empresas Sub Contratista', function($sheet) use($listaDatosReporte) {    
                                    $sheet->loadView('reporteCertificacionGrafica.subContratistaGeneral',compact('listaDatosReporte'));
                                });

                                $excel->sheet('grafica_SubContratistas', function($sheet) use($dataGraficaCertificadoSub) { 

                                    $columnCount = count($dataGraficaCertificadoSub[0]);
                                    $rowCount = count($dataGraficaCertificadoSub);
                                    $keys = array_keys($dataGraficaCertificadoSub[0]);
                                    $labels = array();
                                    $categories = array();
                                    $values = array();
                                    $colunmaLetra = \PHPExcel_Cell::stringFromColumnIndex($columnCount-1);
                                  
                                    $sheet->getStyle('A2:'.$colunmaLetra.'2')->applyFromArray(
                                            array(
                                                'font' => array(
                                                    'bold' => true
                                                ),
                                                'alignment' => array(
                                                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                ),
                                                'borders' => array(
                                                    'top' => array(
                                                        'style' => \PHPExcel_Style_Border::BORDER_THIN
                                                    )
                                                ),
                                                'fill' => array(
                                                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                                    'color' => array('rgb' => 'e3e3e3')
                                                )
                                            )
                                    ); 

                                    $sheet->fromArray($dataGraficaCertificadoSub);

                                    for ($i = 1; $i < $columnCount; $i++) {
                                   
                                        $col = \PHPExcel_Cell::stringFromColumnIndex($i);
                                        $labels[] = new \PHPExcel_Chart_DataSeriesValues('String', 'grafica_SubContratistas!$' . $col . '$2', null, 1);
                                        $categories[] = new \PHPExcel_Chart_DataSeriesValues('String', 'grafica_SubContratistas!$A$3:$A$' . ($rowCount + 1), null, $rowCount);
                                        $values[] = new \PHPExcel_Chart_DataSeriesValues('Number', 'grafica_SubContratistas!$' . $col . '$3:$' . $col . '$' . ($rowCount + 1), null, $rowCount);
                                    }
                      
                                    $series = new \PHPExcel_Chart_DataSeries(
                                        \PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
                                        \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
                                        range(0, count($values)-1),           // plotOrder
                                        $labels,                              // plotLabel
                                        $categories,                               // plotCategory
                                        $values                               // plotValues
                                    );
                                    $series->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_BAR);

                                    $plotArea = new \PHPExcel_Chart_PlotArea(NULL, array($series));
                                    $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                                    $title = new \PHPExcel_Chart_Title('Numero de Empresas Sub Contratista Certificadas');
                                    $yAxisLabel = new \PHPExcel_Chart_Title('Empresas');
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

                                    //    Add the chart to the worksheet
                                    $sheet->addChart($chart); 
                                });
                        })->export('xlsx');
                    });

            }
            if($fechaSeleccion != 0  AND $countContratista == 0 AND $centroCosto == 0){
            
                $fechas = $porciones = explode("_", $fechaSeleccion);
                $fecha1 = $fechas[0];
                $fecha2 = $fechas[1];
                $periodosT = $fecha1 ."-".$fecha2;
                $fechasDesde = strtotime ( '+4 hour' ,strtotime($fecha1));
                //sumo 1 día
                $fechasHasta = strtotime ( '+4 hour' ,strtotime($fecha2));
                $totalempresasContratista = Contratista::whereIn('mainCompanyRut',$rutprincipalR)
                ->whereBetween('certificateDate', [$fechasDesde,$fechasHasta])->count();
                
                $empresasContratista = Contratista::whereIn('mainCompanyRut',$rutprincipalR)
                ->whereBetween('certificateDate', [$fechasDesde,$fechasHasta])
                ->orderBy('id', 'ASC')->select('id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType','servicioId','classserv','adminContrato')->chunk($totalempresasContratista, function ($query)  {

                    foreach((array)$query as $empresasContratista){

                        if(!empty($empresasContratista)){

                            foreach($empresasContratista AS $empresa){

                                if($empresa['certificateState'] != 1){

                                    $datosSolictud = Solicitud::distinct()->where('companyId',$empresa['id'])->orderby('serial','DESC')->take(1)->get(['workersNumber','workerstotales','serial'])->toArray();
                                    
                                    if(!empty($datosSolictud)){
                                        $numeroTrabajadoresTotales = $datosSolictud[0]['workerstotales'];
                                        $numeroTrabajadores = $datosSolictud[0]['workersNumber'];
                                    }else{
                                        $numeroTrabajadoresTotales = 0;
                                        $numeroTrabajadores = 0;
                                    }
                                }else{
                                    $numeroTrabajadores = 0;
                                    $numeroTrabajadoresTotales = $empresa['workersNumber'];
                                }

                                if($empresa['certificateState'] == 10 or $empresa['certificateState'] == 5){
                                    $datosCertificado = Certificado::where('companyId',$empresa['id'])->orderby('serial','DESC')->take(1)->get(['number','serial'])->toArray(); 
                                    $numeroCertificado = $datosCertificado[0]['number']."-".$datosCertificado[0]['serial'];
                                }else{
                                   
                                    $numeroCertificado = "";
                                }

                                switch ((int)$empresa['certificateState']) {
                                    case 1:
                                         $estadoCerficacionTexto ="Ingresado";
                                        break;
                                    case 2:
                                         $estadoCerficacionTexto ="Solicitado";
                                        break;
                                    case 3:
                                         $estadoCerficacionTexto ="Aprobado";
                                        break;
                                    case 4:
                                         $estadoCerficacionTexto ="No Aprobado";
                                        break;
                                    case 5:
                                         $estadoCerficacionTexto ="Certificado";
                                        break;
                                    case 6:
                                         $estadoCerficacionTexto ="Documentado";
                                        break;
                                    case 7:
                                         $estadoCerficacionTexto ="Histórico";
                                        break;
                                    case 8:
                                         $estadoCerficacionTexto ="Completo";
                                        break;
                                    case 9:
                                         $estadoCerficacionTexto ="En Proceso";
                                        break;
                                    case 10:
                                         $estadoCerficacionTexto ="No Conforme";
                                        break;
                                    case 11:
                                         $estadoCerficacionTexto ="Inactivo";
                                        break;
                                }

                                if($empresa['certificateState'] == 5 or $empresa['certificateState'] == 6 or $empresa['certificateState'] == 8 or $empresa['certificateState'] == 3 or $empresa['certificateState'] == 4 or $empresa['certificateState'] == 5 or $empresa['certificateState'] == 11 or $empresa['certificateState'] == 10){

                                        $datosCuadratura = Cuadratura::where('companyId',$empresa['id'])->orderby('id','DESC')->take(1)->get(['observations','id'])->toArray();
                                        if(!empty($datosCuadratura)){
                                           
                                            $observaciones = $datosCuadratura[0]['observations'];
                                        }else{
                                          
                                           $observaciones =  $empresa['certificateObservations'];
                                        }
                                }else{
                                    $observaciones =  $empresa['certificateObservations'];
                                }
                                $periodo = DB::table('Period')
                                ->join('Month', 'Month.id', '=', 'Period.monthId')
                                ->where(['Period.id' => $empresa["periodId"]])
                                ->select('Period.year','Month.name')
                                ->get();

                                $periodoTexto =  $periodo[0]->name."-".$periodo[0]->year;            
                                $datosReporte["id"] = $empresa["id"];
                                $datosReporte["rutprincipal"] = $empresa["mainCompanyRut"];
                                $datosReporte["principal"] = ucwords(mb_strtolower($empresa["mainCompanyName"],'UTF-8'));
                                $datosReporte["rutcontratistas"] = $empresa["rut"]."-".$empresa["dv"];
                                $datosReporte["contratista"] = ucwords(mb_strtolower($empresa["name"],'UTF-8'));
                                $datosReporte["center"] = ucwords(mb_strtolower($empresa["center"],'UTF-8'));
                                $datosReporte["rutSubContratista"] = $empresa["subcontratistaRut"]."-".$empresa["subcontratistaDv"];
                                $datosReporte["subcontratistaName"] = ucwords(mb_strtolower($empresa["subcontratistaName"],'UTF-8'));
                                $datosReporte["center"] = ucwords(mb_strtolower($empresa["center"],'UTF-8'));
                                $datosReporte["periodo"] = ucwords(mb_strtolower($periodoTexto,'UTF-8'));
                                $datosReporte["periodoID"] = $empresa["periodId"];
                                $datosReporte['numeroTrabajadoresCertificar'] = $numeroTrabajadores;     
                                $datosReporte['numeroTrabajadoresTotales'] = $numeroTrabajadoresTotales; 
                                $datosReporte["estadoCerticacion"] = ucwords(mb_strtolower($estadoCerficacionTexto,'UTF-8'));
                                $datosReporte["estadoCerticacionId"] = $empresa['certificateState'];
                                $datosReporte["fechaCerticacion"] =  date('d/m/Y', $empresa["certificateDate"]);
                                $datosReporte["observacion"] =  mb_strtolower($observaciones,'UTF-8');

                                if(!empty($datosReporte)){
                                    $listaDatosReporte[] = $datosReporte;
                                    
                                }
                            }  
                        } 
                    }
                    /// ordeno los periodos
                    foreach ($listaDatosReporte as $perido) {
                        $periodos[]=$perido['periodoID'];
                    }

                    $peridosAc = array_unique($periodos);
                    $periodosOrdenados = sort($peridosAc);
                    $peridoInicio = reset($peridosAc);
                    $peridoFinal = end($peridosAc);
                    //////
                         
                    for ($peridoInicio; $peridoInicio <= $peridoFinal; $peridoInicio++) { 
                        $trabajadores = 0;
                        $certificados = 0;
                        $Subcertificados = 0;
                        $trabajadoresNo = 0;  
                        $noCertificado = 0;
                        $SubcertificadosNO = 0;
                        foreach ($listaDatosReporte as $value) {
                            if($peridoInicio == $value['periodoID'] AND $value['estadoCerticacionId'] == 5){
                                $trabajadores = $trabajadores + $value['numeroTrabajadoresCertificar'];
                                $certificados = $certificados + 1;
                                $peridosTrabajadore['periodo'] = $value['periodo'];
                                $peridosTrabajadore['trabajadores'] = $trabajadores;
                                $peridosTrabajadore['cantidad'] = $certificados;

                                if($value['rutSubContratista'] != "-"){
                                    $Subcertificados = $Subcertificados + 1;
                                    $peridosTrabajadore['Subcertificados'] = $Subcertificados;
                                }else{
                                    
                                    $peridosTrabajadore['Subcertificados'] = 0;
                                }
                            }
                           
                           if ($peridoInicio == $value['periodoID'] AND $value['estadoCerticacionId'] == 10){
                                $trabajadoresNo = $trabajadoresNo + $value['numeroTrabajadoresCertificar'];
                                $noCertificado = $noCertificado + 1;
                                $peridosTrabajadoreNo['periodo'] = $value['periodo'];
                                $peridosTrabajadoreNo['trabajadores'] =  $trabajadoresNo;
                                $peridosTrabajadoreNo['cantidad'] = $noCertificado;
                                if($value['rutSubContratista'] != "-"){
                                    $SubcertificadosNO = $SubcertificadosNO + 1;
                                    $peridosTrabajadoreNo['Subcertificados'] = $SubcertificadosNO;
                                }else{
                                    
                                    $peridosTrabajadoreNo['Subcertificados'] = 0;
                                }
                            }
                        }
                        
                        if (!empty($peridosTrabajadore)) {
                           $listaConforme[] = $peridosTrabajadore;
                        }else{
                            $listaConforme[] = 0;
                        }
                        if (!empty($peridosTrabajadoreNo)) {
                           $listaConformeNo[] = $peridosTrabajadoreNo;
                        }else{
                            $listaConformeNo[] = 0;
                        }
                    }
                    
                    $result1 = array_unique($listaConforme, SORT_REGULAR);
                    $result2 = array_unique($listaConformeNo, SORT_REGULAR);
                  
                    //// grafica trabajadores /////
                    foreach ($result1 as $value) {
                       $mesesSeleccion[] = $value['periodo'];
                       $valoresSeleccion[] = $value['trabajadores'];
                       $cantidadCertificados[] = $value['cantidad'];
                       $cantidadSubCertificados[] = $value['Subcertificados'];
                    }
                    $estadoC = array('estado de Certificacion');
                    $mesesGrafico = array_merge($estadoC, $mesesSeleccion);
                    $aprobado = array("Aprobado");
                    $valoresGrafico = array_merge($aprobado, $valoresSeleccion);
                    $dataGraficaArpobados = array($mesesGrafico, $valoresGrafico);
                  
                    foreach ($result2 as $valuen) {
                       $mesesSeleccionN[] = $valuen['periodo'];
                       $valoresSeleccionN[] = $valuen['trabajadores'];
                       $cantidadNoCertificados[] = $valuen['cantidad'];
                       $cantidadSubNoCertificados[] = $valuen['Subcertificados'];
                    }
                    $estadoCN = array('estado de Certificacion');
                    $mesesGraficoNo = array_merge($estadoCN, $mesesSeleccionN);
                    $naprobado = array("No Aprobado");
                    $valoresGraficoNo = array_merge($naprobado, $valoresSeleccionN);
                    $dataGraficaNoAporbado = array($mesesGraficoNo, $valoresGraficoNo);
                    $dataGrafica = array($mesesGrafico, $valoresGrafico,$valoresGraficoNo);
                    ///// grfica estado de certificacion ///

                    $mesesCertificado = array('Cantidad');
                    $mesesGraficoCertificado = array_merge($mesesCertificado, $mesesSeleccion);
                    $CertificadoAprobado = array("Aprobados");
                    $cantidadCertificadosAprobados = array_merge($CertificadoAprobado, $cantidadCertificados);
                    $CertificadoNoAprobado = array("No Aprobados");
                    $cantidadCertificadosNoApro = array_merge($CertificadoNoAprobado, $cantidadNoCertificados);
                    $dataGraficaCertificado = array($mesesGraficoCertificado, $cantidadCertificadosAprobados,$cantidadCertificadosNoApro);


                    //// grafica SUB CONTRATISTA /////
                    $mesesCertificado = array('Cantidad');
                    $mesesGraficoCertificadoSub = array_merge($mesesCertificado, $mesesSeleccion);
                    $CertificadoAprobadoSub = array("Aprobados");
                    $cantidadCertificadosAprobadosSub = array_merge($CertificadoAprobadoSub, $cantidadSubCertificados);
                    $CertificadoNoAprobadoSub = array("No Aprobados");
                    $cantidadCertificadosNoAproSub = array_merge($CertificadoNoAprobadoSub, $cantidadSubNoCertificados);
                    $dataGraficaCertificadoSub = array($mesesGraficoCertificadoSub, $cantidadCertificadosAprobadosSub,$cantidadCertificadosNoAproSub);

                    Excel::create('Reporte Certificación con Graficos', function($excel) use ($listaDatosReporte, $dataGrafica,$dataGraficaNoAporbado,$dataGraficaCertificado,$dataGraficaCertificadoSub) {

                            $excel->sheet('Lista General', function($sheet) use($listaDatosReporte) {    
                                $sheet->loadView('reporteCertificacionGrafica.listadoGeneral',compact('listaDatosReporte'));
                            });

                            $excel->sheet('grafica', function($sheet) use($dataGrafica) { 

                                $columnCount = count($dataGrafica[0]);
                                $rowCount = count($dataGrafica);
                                $keys = array_keys($dataGrafica[0]);
                                $labels = array();
                                $categories = array();
                                $values = array();
                                $colunmaLetra = \PHPExcel_Cell::stringFromColumnIndex($columnCount-1);

                                $sheet->getStyle('A2:'.$colunmaLetra.'2')->applyFromArray(
                                    array(
                                        'font' => array(
                                            'bold' => true
                                        ),
                                        'alignment' => array(
                                            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                        ),
                                        'borders' => array(
                                            'top' => array(
                                                'style' => \PHPExcel_Style_Border::BORDER_THIN
                                            )
                                        ),
                                        'fill' => array(
                                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                            'color' => array('rgb' => 'e3e3e3')
                                        )
                                    )
                                ); 

                                $sheet->fromArray($dataGrafica);

                                for ($i = 1; $i < $columnCount; $i++) {
                               
                                $col = \PHPExcel_Cell::stringFromColumnIndex($i);
                                $labels[] = new \PHPExcel_Chart_DataSeriesValues('String', 'grafica!$' . $col . '$2', null, 1);
                                $categories[] = new \PHPExcel_Chart_DataSeriesValues('String', 'grafica!$A$3:$A$' . ($rowCount + 1), null, $rowCount);
                                $values[] = new \PHPExcel_Chart_DataSeriesValues('Number', 'grafica!$' . $col . '$3:$' . $col . '$' . ($rowCount + 1), null, $rowCount);
                            
                                }
                  

                                $series = new \PHPExcel_Chart_DataSeries(
                                    \PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
                                    \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
                                    range(0, count($values)-1),           // plotOrder
                                    $labels,                              // plotLabel
                                    $categories,                               // plotCategory
                                    $values                               // plotValues
                                );
                                $series->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_BAR);

                                $plotArea = new \PHPExcel_Chart_PlotArea(NULL, array($series));
                                $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                                $title = new \PHPExcel_Chart_Title('trabajadores Certificados / No aprobados');
                                $yAxisLabel = new \PHPExcel_Chart_Title('Cantidad de trabajdores');
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

                                //    Add the chart to the worksheet
                                $sheet->addChart($chart); 
                            });

                            $excel->sheet('Lista Certificados', function($sheet) use($listaDatosReporte) {    
                                $sheet->loadView('reporteCertificacionGrafica.generalCertificada',compact('listaDatosReporte'));
                            });

                            $excel->sheet('grafica_Certificados', function($sheet) use($dataGraficaCertificado) { 

                                $columnCount = count($dataGraficaCertificado[0]);
                                $rowCount = count($dataGraficaCertificado);
                                $keys = array_keys($dataGraficaCertificado[0]);
                                $labels = array();
                                $categories = array();
                                $values = array();
                                $colunmaLetra = \PHPExcel_Cell::stringFromColumnIndex($columnCount-1);

                                $sheet->getStyle('A2:'.$colunmaLetra.'2')->applyFromArray(
                                    array(
                                        'font' => array(
                                            'bold' => true
                                        ),
                                        'alignment' => array(
                                            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                        ),
                                        'borders' => array(
                                            'top' => array(
                                                'style' => \PHPExcel_Style_Border::BORDER_THIN
                                            )
                                        ),
                                        'fill' => array(
                                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                            'color' => array('rgb' => 'e3e3e3')
                                        )
                                    )
                                ); 

                                $sheet->fromArray($dataGraficaCertificado);

                                $colunmaLetra = \PHPExcel_Cell::stringFromColumnIndex($columnCount-1);
                              
                                $sheet->getStyle('A2:'.$colunmaLetra.'2')->applyFromArray(
                                        array(
                                            'font' => array(
                                                'bold' => true
                                            ),
                                            'alignment' => array(
                                                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                            ),
                                            'borders' => array(
                                                'top' => array(
                                                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                                                )
                                            ),
                                            'fill' => array(
                                                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                                'color' => array('rgb' => 'e3e3e3')
                                            )
                                        )
                                );

                                $sheet->fromArray($dataGraficaCertificado);

                              
                                for ($i = 1; $i < $columnCount; $i++) {
                               
                                $col = \PHPExcel_Cell::stringFromColumnIndex($i);
                                $labels[] = new \PHPExcel_Chart_DataSeriesValues('String', 'grafica_Certificados!$' . $col . '$2', null, 1);
                                $categories[] = new \PHPExcel_Chart_DataSeriesValues('String', 'grafica_Certificados!$A$3:$A$' . ($rowCount + 1), null, $rowCount);
                                $values[] = new \PHPExcel_Chart_DataSeriesValues('Number', 'grafica_Certificados!$' . $col . '$3:$' . $col . '$' . ($rowCount + 1), null, $rowCount);
                            
                                }
                  

                                $series = new \PHPExcel_Chart_DataSeries(
                                    \PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
                                    \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
                                    range(0, count($values)-1),           // plotOrder
                                    $labels,                              // plotLabel
                                    $categories,                               // plotCategory
                                    $values                               // plotValues
                                );
                                $series->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_BAR);

                                $plotArea = new \PHPExcel_Chart_PlotArea(NULL, array($series));
                                $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                                $title = new \PHPExcel_Chart_Title('Certificados Aprobados / No Aprobados');
                                $yAxisLabel = new \PHPExcel_Chart_Title('Cantidad de certificados');
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

                                //    Add the chart to the worksheet
                                $sheet->addChart($chart); 
                            });

                            $excel->sheet('Empresas Sub Contratista', function($sheet) use($listaDatosReporte) {    
                                $sheet->loadView('reporteCertificacionGrafica.subContratistaGeneral',compact('listaDatosReporte'));
                            });

                            $excel->sheet('grafica_SubContratistas', function($sheet) use($dataGraficaCertificadoSub) { 

                                $columnCount = count($dataGraficaCertificadoSub[0]);
                                $rowCount = count($dataGraficaCertificadoSub);
                                $keys = array_keys($dataGraficaCertificadoSub[0]);
                                $labels = array();
                                $categories = array();
                                $values = array();
                                $colunmaLetra = \PHPExcel_Cell::stringFromColumnIndex($columnCount-1);

                                $sheet->getStyle('A2:'.$colunmaLetra.'2')->applyFromArray(
                                    array(
                                        'font' => array(
                                            'bold' => true
                                        ),
                                        'alignment' => array(
                                            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                        ),
                                        'borders' => array(
                                            'top' => array(
                                                'style' => \PHPExcel_Style_Border::BORDER_THIN
                                            )
                                        ),
                                        'fill' => array(
                                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                            'color' => array('rgb' => 'e3e3e3')
                                        )
                                    )
                                ); 

                                $sheet->fromArray($dataGraficaCertificadoSub);

                                for ($i = 1; $i < $columnCount; $i++) {
                               
                                    $col = \PHPExcel_Cell::stringFromColumnIndex($i);
                                    $labels[] = new \PHPExcel_Chart_DataSeriesValues('String', 'grafica_SubContratistas!$' . $col . '$2', null, 1);
                                    $categories[] = new \PHPExcel_Chart_DataSeriesValues('String', 'grafica_SubContratistas!$A$3:$A$' . ($rowCount + 1), null, $rowCount);
                                    $values[] = new \PHPExcel_Chart_DataSeriesValues('Number', 'grafica_SubContratistas!$' . $col . '$3:$' . $col . '$' . ($rowCount + 1), null, $rowCount);
                                }
                  
                                $series = new \PHPExcel_Chart_DataSeries(
                                    \PHPExcel_Chart_DataSeries::TYPE_BARCHART,       // plotType
                                    \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,  // plotGrouping
                                    range(0, count($values)-1),           // plotOrder
                                    $labels,                              // plotLabel
                                    $categories,                               // plotCategory
                                    $values                               // plotValues
                                );
                                $series->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_BAR);

                                $plotArea = new \PHPExcel_Chart_PlotArea(NULL, array($series));
                                $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                                $title = new \PHPExcel_Chart_Title('Numero de Empresas Sub Contratista Certificadas');
                                $yAxisLabel = new \PHPExcel_Chart_Title('Empresas');
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

                                //    Add the chart to the worksheet
                                $sheet->addChart($chart); 
                            });
                    })->export('xlsx');
                });
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
