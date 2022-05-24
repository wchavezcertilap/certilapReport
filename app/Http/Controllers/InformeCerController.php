<?php

namespace App\Http\Controllers;
use DB;
use PDF;
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
use App\CertificateHistory;
use App\Quickchart\Quickchart;
use Illuminate\Http\Request;

class InformeCerController extends Controller
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
        $usuarioClaroChile= session('user_Claro');
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

        $peridoActual = Periodo::orderBy('id', 'DESC')
        ->take(1)->get(['id'])->toArray();

        //print_r( $peridoActual);
                        
        $periodos = Periodo::where('id', '!=', $peridoActual)->orderBy('id', 'DES')->get(['id', 'monthId','year']);
        $periodos->load('mes');

        $etiquetasEstados = 0;
        $valoresEstados = 0;
        return view('informeCer.index',compact('EmpresasP','periodos','datosUsuarios','etiquetasEstados','valoresEstados','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile'));
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
        $usuarioAqua = session('user_aqua');
        $certificacion = session('certificacion');
        $usuarioABBChile= session('user_ABB');
        $usuarioNOKactivo = session('usuario_nok');
        $usuarioClaroChile= session('user_Claro');
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

        $peridoActual = Periodo::orderBy('id', 'DESC')
        ->take(1)->get(['id'])->toArray();

                        
        $periodos = Periodo::where('id', '!=', $peridoActual)->orderBy('id', 'DES')->get(['id', 'monthId','year']);
        $periodos->load('mes');

        $etiquetasEstados = 0;
        $valoresEstados = 0;

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
        /////////logica Informe ///////////
        $input=$request->all();
 
        $tipoBsuqueda = $input["tipoBsuqueda"];
        $rutprincipalR = $input["empresaPrincipal"];

        if($tipoBsuqueda == 1){
             
            $idPerido = $input["peridoUnico"];
            $peridoData = Periodo::where('id',$idPerido)
            ->get(['monthId','year'])->toArray();


            ////MAP OF MONTHS NUMBERS STRINGS
            $MAP_MONTH_NUMBER[1] = '01';
            $MAP_MONTH_NUMBER[2] = '02';
            $MAP_MONTH_NUMBER[3] = '03';
            $MAP_MONTH_NUMBER[4] = '04';
            $MAP_MONTH_NUMBER[5] = '05';
            $MAP_MONTH_NUMBER[6] = '06';
            $MAP_MONTH_NUMBER[7] = '07';
            $MAP_MONTH_NUMBER[8] = '08';
            $MAP_MONTH_NUMBER[9] = '09';
            $MAP_MONTH_NUMBER[10] = '10';
            $MAP_MONTH_NUMBER[11] = '11';
            $MAP_MONTH_NUMBER[12] = '12';
            ////MAP OF MONTHS
            $MAP_MONTH[1] = 'Enero';
            $MAP_MONTH[2] = 'Febrero';
            $MAP_MONTH[3] = 'Marzo';
            $MAP_MONTH[4] = 'Abril';
            $MAP_MONTH[5] = 'Mayo';
            $MAP_MONTH[6] = 'Junio';
            $MAP_MONTH[7] = 'Julio';
            $MAP_MONTH[8] = 'Agosto';
            $MAP_MONTH[9] = 'Septiembre';
            $MAP_MONTH[10] = 'Octubre';
            $MAP_MONTH[11] = 'Noviembre';
            $MAP_MONTH[12] = 'Diciembre';

            //Dates logic
            $currtme    = time();
           /* $curr_year  = (int)date("Y", $currtme);
            $curr_month  = (int)date("m", $currtme);*/
            $curr_year  = (int)$peridoData[0]['year'];
            $curr_month  = (int)$peridoData[0]['monthId']+1;
          
       
            $diaquince = 16;
            ///Este formato para esta operacion de obtener el dia de la semana
            $quincena  = $curr_year . '-' . $MAP_MONTH_NUMBER[$curr_month] . '-' . $diaquince;
            $dayofweek = date('w', strtotime($quincena)); // 0 a 6. 0 Domingo, 6 Sabado
            if($dayofweek == 0 or $dayofweek == 6){
                if($dayofweek == 0){
                    $diaquince = 17; //Variable para la conversion en UNIX
                }
                if($dayofweek == 6){
                    $diaquince = 18; //Variable para la conversion en UNIX
                }
            }
            //Intervalo de fechas
            $fechap = (int)strtotime( $curr_year . '-' . $MAP_MONTH_NUMBER[$curr_month] . '-02' ) - (3600*20); //PHP DLL PROBLEMS PARA FORMATOS DE FECHA
            $fechaf = (int)strtotime( $curr_year . '-' . $MAP_MONTH_NUMBER[$curr_month] . '-' . $diaquince ) - (3600*20); /// EN ESTE FORMATO PARA FECHAS MAYORES A 12
           // echo '<br>' . $fechap; //Primer dia del mes
            //echo '<br>' . $fechaf; //Dia en el que acaba la quincena

            $fechap16 = (int)strtotime( $curr_year . '-' . $MAP_MONTH_NUMBER[$curr_month] . '-17' ) - (3600*20); //PHP DLL PROBLEMS PARA FORMATOS DE FECHA
            $fechap20 = (int)strtotime( $curr_year . '-' . $MAP_MONTH_NUMBER[$curr_month] . '-21' ) - (3600*20); //PHP DLL PROBLEMS PARA FORMATOS DE FECHA
            $fechap21 = (int)strtotime( $curr_year . '-' . $MAP_MONTH_NUMBER[$curr_month] . '-22' ) - (3600*20); //PHP DLL PROBLEMS PARA FORMATOS DE FECHA
            $fechap25 = (int)strtotime( $curr_year . '-' . $MAP_MONTH_NUMBER[$curr_month] . '-26' ) - (3600*20); //PHP DLL PROBLEMS PARA FORMATOS DE FECHA
            $fechaf2 = (int)strtotime( $curr_year . '-' . $MAP_MONTH_NUMBER[$curr_month+1] . '-01') - (3600*20); /// EN ESTE FORMATO PARA FECHAS MAYORES A 

             $empresasContratistaD = Contratista::distinct()->where('mainCompanyRut',$rutprincipalR)
            ->where('periodId',$idPerido)
            ->orderBy('certificateState', 'ASC')
            ->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType','servicioId','classserv','adminContrato'])->toArray();

            foreach ($empresasContratistaD as $empresa) {
                
                    $DataempresasContratista['rut']=$empresa['rut']."-".$empresa['dv'];
                    $DataempresasContratista['name']=strtolower($empresa['name']);
                    $DataempresasContratista['center']=strtolower($empresa['center']);
                    if($empresa['subcontratistaRut']!=""){
                        $DataempresasContratista['subRut']=$empresa['subcontratistaRut']."-".$empresa['subcontratistaDv'];
                        $DataempresasContratista['subName']=strtolower($empresa['subcontratistaName']);
                    }else{
                        $DataempresasContratista['subRut']="";
                        $DataempresasContratista['subName']="";
                    }
                    if($empresa['certificateState']==5){
                        $DataempresasContratista['colorEstado']='#47941b';
                    }
                    elseif($empresa['certificateState']==10){
                        $DataempresasContratista['colorEstado']='#e33109';
                    }else{
                        $DataempresasContratista['colorEstado']='#0984e3';
                    }
                    $DataempresasContratista['estado']=estadoCerficacionTexto($empresa['certificateState']);
                    $DATAEMPRESA[]=$DataempresasContratista;
            }

            $empresasContratista = Contratista::distinct()->where('mainCompanyRut',$rutprincipalR)
            ->where('periodId',$idPerido)
            ->orderBy('id', 'ASC')
            ->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType','servicioId','classserv','adminContrato'])->toArray();

            // crear tabla con rut+dv, name, 

            $count_company_per_type[1] = 0;
            $count_company_per_type[2] = 0;
       
            foreach ($empresasContratista as $empresa) {
                $type_id = $empresa['companyTypeId'];
                $count_company_per_type[$type_id] = $count_company_per_type[$type_id] + 1; //Total de compañias por tipo 1 o 2 Barras
            }

            $percent_company_per_type[1] = 0;
            $percent_company_per_type[2] = 0;
       
            $total_companies = array_sum($count_company_per_type);
            foreach ($count_company_per_type as $key => $value) {
                $percent_company_per_type[$key] = $value * 100 / $total_companies; //Porcentaje por tipo de compañia Torta
            }


            /// Tiempos de respuesta de los Contratistas /////////// deacuerdo al periodo tomar la fecha inicial 01-mes al 15-mes
            $estadosSinDocumentar = [1,2,8];
            $empresaContratistaSinDocumentar = Contratista::distinct()->where('mainCompanyRut',$rutprincipalR)  
            ->where('periodId',$idPerido)
            ->whereIn('certificateState',$estadosSinDocumentar)
            ->whereBetween('certificateDate', array($fechap,  $fechaf))
            ->orderBy('rut', 'ASC')
            ->get(['rut','name'])->toArray();

            // Tiempos de respuesta de los Contratistas /////////// deacuerdo al periodo tomar la fecha inicial 16-mes al 30-mes
            $estadosConformes = [10,5];
            $empresasContratistaAprobados = Contratista::distinct()->where('mainCompanyRut',$rutprincipalR)   
            ->where('periodId',$idPerido)
            ->whereIn('certificateState',$estadosConformes)
            ->whereBetween('certificateState', array($fechap16,  $fechaf))
            ->orderBy('rut', 'ASC')
            ->get(['rut','name'])->toArray();

            $_total_de_empresas_sin_documentar = count($empresaContratistaSinDocumentar);
            $_total_de_empresas_aprobadas = count($empresasContratistaAprobados);
            $_percent_total_de_empresas_sin_documentar = 0;
            $_percent_total_de_empresas_aprobadas = 0;
            if ( $_total_de_empresas_sin_documentar != 0 ) {
                $_total_de_empresas_de_este_query = $_total_de_empresas_sin_documentar + $_total_de_empresas_aprobadas;
                $_percent_total_de_empresas_sin_documentar = $_total_de_empresas_sin_documentar * 100 / $_total_de_empresas_de_este_query;
            }

            if ( $_total_de_empresas_aprobadas != 0 ) {
                $_total_de_empresas_de_este_query = $_total_de_empresas_sin_documentar + $_total_de_empresas_aprobadas;
                $_percent_total_de_empresas_aprobadas = $_total_de_empresas_aprobadas * 100 / $_total_de_empresas_de_este_query;
            }

            $chartEmpresasSinDocumentarAprobadas = new QuickChart(array(
                'width' => 600,
                'height' => 300
            ));

            $string_line_for_data_empresas_sin_documentar_empresas_aprobadas = "";
            $labels_for_pie_chart_empresas_sin_documentar_empresas_aprobadas = ["Sin Documentar","Aprobadas"];
            $percent_empresas_sin_documentar_empresas_aprobadas = [$_percent_total_de_empresas_sin_documentar, $_percent_total_de_empresas_aprobadas];
            $i_labels_for_pie_chart_empresas_sin_documentar_empresas_aprobadas = 0;
            $string_line_for_label_chart_empresas_sin_documentar_empresas_aprobadas = "";
            foreach ($labels_for_pie_chart_empresas_sin_documentar_empresas_aprobadas as $key => $value) {
                if ($i_labels_for_pie_chart_empresas_sin_documentar_empresas_aprobadas > 0) {
                    $string_line_for_label_chart_empresas_sin_documentar_empresas_aprobadas.= ',';
                }
                $string_line_for_label_chart_empresas_sin_documentar_empresas_aprobadas.= '"'. $value .' ('.  round($percent_empresas_sin_documentar_empresas_aprobadas[$i_labels_for_pie_chart_empresas_sin_documentar_empresas_aprobadas], 2) .' %)"';
                $i_labels_for_pie_chart_empresas_sin_documentar_empresas_aprobadas ++;
            }
            $chartEmpresasSinDocumentarAprobadas->setConfig('{
                "type": "pie",
                "data": {
                    "datasets": [{
                        "backgroundColor": ["#6D214F", "#F97F51"],
                        "data": ["'.$_percent_total_de_empresas_sin_documentar.'", "'.$_percent_total_de_empresas_aprobadas.'"],
                        "label": "Empresas sin documentar/aprobadas"
                    }],
                    "labels": [' . $string_line_for_label_chart_empresas_sin_documentar_empresas_aprobadas . ']
                },
            }');
            $chart_empresas_sin_documentar_empresas_aprobadas = $chartEmpresasSinDocumentarAprobadas->getUrl();
            $empresasContratistaRecertificacion = Contratista::distinct()->where('mainCompanyRut',$rutprincipalR)
                ->where('periodId',$idPerido)
                ->where('center','LIKE','%(RECERTIFICACION)%')
                ->orWhere('center','LIKE','%RECERTIFICACION')
                ->orderBy('rut', 'ASC')
                ->get(['rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType','servicioId','classserv','adminContrato'])->toArray();

            // Porcentaje empresas rectificadas
            $total_rectificadas = sizeof($empresasContratistaRecertificacion);
            $total_no_recitificadas = $total_companies - $total_rectificadas;
            $percent_rectificadas = $total_rectificadas * 100 / $total_companies;
            $percent_no_rectificadas = $total_no_recitificadas * 100 / $total_companies;

            //Porcentaje de total de trabajadores y genero
            $trabajadores = TrabajadorVerificacion::distinct()->where('mainCompanyRut',$rutprincipalR)
                ->where('periodId',$idPerido)
                ->orderBy('id', 'ASC')
                ->get(['rut','sex','id'])->toArray();
            $hombres = 0;
            $mujeres = 0;
            $totalTra = count($trabajadores);
            foreach ($trabajadores as $worker) {
                if($worker['sex']==1){
                    $hombres = $hombres + 1;
                }
                if($worker['sex']==2){
                    $mujeres = $mujeres + 1;
                }
            }
            $percent_genre[1] = $hombres * 100 / $totalTra;
            $percent_genre[2] = $mujeres * 100 / $totalTra;
            // Build the charts like a rockstar
            $chart_by_company_type = "https://quickchart.io/chart?c={type:'doughnut',data:{labels:['Contratista','Sub contratista'],datasets:[{data:[";
            foreach ($percent_company_per_type as $key => $value) {
                if ($key > 1) {
                    $chart_by_company_type.= ',';
                }
                $chart_by_company_type.= "'" .  round($value, 2) . "'";
            }
            $chart_by_company_type.= "]}]},options:{plugins:{doughnutlabel:{labels:[{text:'" . $total_companies . "',font:{size:20}},{text:'total'}]}}}}";
            ///By company type bars total count
            $bars_by_company_type = "https://quickchart.io/chart?w=500&h=300&c={type:%27bar%27,data:{labels:['Contratista','Sub contratista'],datasets:[{data:[";
                foreach ($count_company_per_type as $key => $value) {
                    if ($key > 1) {
                        $bars_by_company_type.= ',';
                    }
                    $bars_by_company_type.= "'" . round($value, 2) . "'";
                }
                $bars_by_company_type.= ']}]},options: {
       
                "legend": {
                              "display": false,
                }, plugins:{datalabels: {}}
              }}';
            /// By genre chart
            $chart_genre_worker = "https://quickchart.io/chart?c={type:'doughnut',data:{labels:['Mujeres','Hombres'],datasets:[{data:[";
            foreach ($percent_genre as $key => $value) {
                if ($key > 1) {
                    $chart_genre_worker.= ',';
                }
                $chart_genre_worker.= "'" . round($value, 2) . "'";
            }
            $chart_genre_worker.= "]}]},options:{plugins:{doughnutlabel:{labels:[{text:'" . $totalTra . "',font:{size:20}},{text:'total'}]}}}}";
            // By genre bars
            $bars_by_genre = "https://quickchart.io/chart?w=500&h=300&c={type:%27bar%27,data:{labels:['Mujeres','Hombres'],datasets:[{label:%27Genero%27,data:[" . $hombres . "," . $mujeres . "]}]},options: {'legend':{'display': false}, plugins:{datalabels: {}}}}";
            /// Recitificadas no rectificadas
            $chart_by_rectificadas = "https://quickchart.io/chart?c={type:'doughnut',data:{labels:['Rectificadas','No rectificadas'],datasets:[{data:['" . round($percent_rectificadas, 2) . "','" . round($percent_no_rectificadas, 2) . "']}]},options:{plugins:{doughnutlabel:{labels:[{text:'" . $total_companies . "',font:{size:20}},{text:'total'}]}}}}";
            $bars_by_rectificadas = "https://quickchart.io/chart?w=500&h=300&c={type:%27bar%27,data:{labels:['Rectificadas','No rectificadas'],datasets:[{label:%27Por tipo de empresa%27, data:[" . $total_rectificadas . "," . $total_no_recitificadas . "]}]},options: {'legend':{'display': false}, plugins:{datalabels: {}}}}";
            /// Charts by estado de certificacion
            $count_company_per_certificate_state[1] = 0;
            $count_company_per_certificate_state[2] = 0;
            $count_company_per_certificate_state[3] = 0;
            $count_company_per_certificate_state[4] = 0;
            $count_company_per_certificate_state[5] = 0;
            $count_company_per_certificate_state[6] = 0;
            $count_company_per_certificate_state[7] = 0;
            $count_company_per_certificate_state[8] = 0;
            $count_company_per_certificate_state[9] = 0;
            $count_company_per_certificate_state[10] = 0;
            $count_company_per_certificate_state[11] = 0;
            foreach ($empresasContratista as $empresa) {
                $state = $empresa['certificateState'];
                $count_company_per_certificate_state[$state] = $count_company_per_certificate_state[$state] + 1; //por tipo
            }
            $percent_company_per_certificate_state[1] = 0;
            $percent_company_per_certificate_state[2] = 0;
            $percent_company_per_certificate_state[3] = 0;
            $percent_company_per_certificate_state[4] = 0;
            $percent_company_per_certificate_state[5] = 0;
            $percent_company_per_certificate_state[6] = 0;
            $percent_company_per_certificate_state[7] = 0;
            $percent_company_per_certificate_state[8] = 0;
            $percent_company_per_certificate_state[9] = 0;
            $percent_company_per_certificate_state[10] = 0;
            $percent_company_per_certificate_state[11] = 0;
            foreach ($count_company_per_certificate_state as $key => $value) {
                $percent_company_per_certificate_state[$key] = $value * 100 / $total_companies; //percent
            }
            ////By Company type percent
            $chart = new QuickChart(array(
                'width' => 1200,
                'height' => 800
            ));

            $string_line_for_data_chart = "";
            $i_percent_per_certificate_state = 0;
            foreach ($percent_company_per_certificate_state as $key => $value) {
                if ($i_percent_per_certificate_state > 0) {
                    $string_line_for_data_chart.= ',';
                }
                if ($value > 15) {
                    $string_line_for_data_chart.= "" . round($value, 2) . "";
                } else {
                    $string_line_for_data_chart.= "" . ' ' . "";
                }
                $i_percent_per_certificate_state ++;
            }

            $labels_for_pie_chart = ["Ingresado","Solicitado","Aprobado","No Aprobado","Certificado","Documentado","Historico","Completo","En Proceso","No Conforme","Inactivo"];
            $i_labels_for_pie_chart = 0;
            $string_line_for_label_chart = "";
            foreach ($labels_for_pie_chart as $key => $value) {
                if ($i_labels_for_pie_chart > 0) {
                    $string_line_for_label_chart.= ',';
                }
                $string_line_for_label_chart.= '"'. $value .' ('.  round($percent_company_per_certificate_state[$i_labels_for_pie_chart+1], 2) .' %)"';
                $i_labels_for_pie_chart ++;
            }
            $chart->setConfig('{
                "type": "pie",
               
                "data": {
                    "labels": [' . $string_line_for_label_chart . '],
                    "datasets": [{
                        "backgroundColor": ["#6D214F", "#F97F51", "#FC427B", "#F77825", "#BDC581", "#82589F", "#996600", "#58B19F", "#1533FF", "#EAB543", "#F97F51"],
                        "data": ['. $string_line_for_data_chart .'],
                    }],
                },
                "options":{
                    "legend": {
                      "display": true,
                      "position": "top",
                      "align": "center",
                      "fullWidth": true,
                      "reverse": true,
                      "labels": {
                        "fontSize": 25,
                        "fontFamily": "sans-serif",
                        "fontColor": "#666666",
                        "fontStyle": "normal",
                        "padding": 20,
                        "text":"total"
                      }
                    }

                }
                
            }');
            $chart_per_certificate_state = $chart->getUrl();
            ///By company type bars total count
            $bars_by_certificate_state = "https://quickchart.io/chart?w=500&h=300&c={type:%27bar%27,data:{labels:['Ingresado','Solicitado','Aprobado','No Aprobado','Certificado','Documentado','Historico','Completo','En Proceso','No Conforme','Inactivo'],datasets:[{label:%27Por estado de certificacion%27, data:[";
            foreach ($count_company_per_certificate_state as $key => $value) {
                if ($key > 1) {
                    $bars_by_certificate_state.= ',';
                }
                $bars_by_certificate_state.= "'" . $value . "'";
            }
            $bars_by_certificate_state.= ']}]},options: {"legend":{"display": false}, plugins:{datalabels: {}}}}';

           // plugins: {datalabels: {anchor: "center",align: "center",color:"#666"}}}}}
            /// cantidad de observaciones por contratista /////////// probar este query
            $empresasContratista = Contratista::distinct()->where('mainCompanyRut',$rutprincipalR)
                ->join('obserTrabComp', 'obserTrabComp.idCompany', '=', 'Company.id')                    
                ->where('periodId',$idPerido)
                ->orderBy('Company.id', 'ASC')
                ->get(['Company.id','Company.rut','Company.name'])->toArray();

                
            $ruts = [];
            $names = [];
            foreach ($empresasContratista as $key => $value) {
                array_push($ruts, $value['rut']);
                array_push($names, $value['name']);
            }
            $ruts_no_repeat = array_unique($ruts);
            foreach ($ruts_no_repeat as $key => $value) {
                $rut_counter[$value] = 0; 
            }
            $names_no_repeat = array_unique($names);
            foreach ($empresasContratista as $subkey => $subvalue) {
                $rut_counter[$subvalue['rut']] = $rut_counter[$subvalue['rut']] + 1;
            }
            $bars_by_empresa_contratista = "https://quickchart.io/chart?w=500&h=300&c={type:%27bar%27,data:{labels:[";
                $index_counter_names = 0;
            foreach ($names_no_repeat as $key => $value) {
                if ($index_counter_names > 0) {
                    $bars_by_empresa_contratista.= ',';
                }
                $value_no_especial = strtolower(str_replace('ñ', '', $value));
                $bars_by_empresa_contratista.= "'" . strtolower(str_replace('&', '', $value_no_especial) ) . "'";
                $index_counter_names ++;
            }
            $bars_by_empresa_contratista.= "],datasets:[{label:%27Por empresa contratista%27, data:[";
            $index_counter_rut = 0;
            foreach ($rut_counter as $key => $value) {
                if ($index_counter_rut > 0) {
                    $bars_by_empresa_contratista.= ',';
                }
                $bars_by_empresa_contratista.= "'" . $value . "'";
                $index_counter_rut ++;
            }
            $bars_by_empresa_contratista.= ']}]}}';
            ///Paginate logic
            $page_size = 10;
            $total_records = count($rut_counter);
            $total_pages = ceil($total_records / $page_size);
            $estadistica_por_empresa_charts = [];
            for ($i=1; $i < $total_pages; $i++) { 
                $offset = ($i - 1) * $page_size;
                $_names_paginated = array_slice($names_no_repeat, $offset, $page_size);
                $_data_paginated = array_slice($rut_counter, $offset, $page_size);
                $index_counter_names = 0;
                $string_for_labels = '';
                foreach ($_names_paginated as $key => $value) {
                    if ($index_counter_names > 0) {
                        $string_for_labels.= ',';
                    }
                    $value_no_especial = strtolower(str_replace('ñ', '', $value));
                    $string_for_labels.= "'" . strtolower(str_replace('&', '', $value_no_especial) ) . "'";
                    $index_counter_names ++;
                }
                $index_counter_rut = 0;
                $string_for_data = '';
                foreach ($_data_paginated as $key => $value) {
                    if ($index_counter_rut > 0) {
                        $string_for_data.= ',';
                    }
                    $string_for_data.= "'" . $value . "'";
                    $index_counter_rut ++;
                }
                $qc = new QuickChart(array(
                    'width' => 1000,
                    'height' => 800,
                ));
                $qc->setConfig('{
                    type: "bar",
                    data: {
                      labels: ['. $string_for_labels .'],
                      datasets: [{
                        label: "Contratista",
                        data: ['. $string_for_data .']
                      }]
                    },options: {"legend":{"display": false}, plugins:{datalabels: {}}}
                }');
                array_push($estadistica_por_empresa_charts, $qc->getUrl());
            }
                        //////////////////////////////////////////////////// documentaod al 15 del mes /////
            $empresasContratistaDocumentas1al15 = Contratista::distinct()->where('mainCompanyRut',$rutprincipalR)
            ->join('CertificateHistory', 'CertificateHistory.companyId', '=', 'Company.id')   
            ->where('CertificateHistory.certificateState',6)     
            ->whereBetween('CertificateHistory.date1', array($fechap,  $fechaf))           
            ->where('Company.periodId',$idPerido)
            ->orderBy('Company.id', 'ASC')
            ->get(['Company.id','Company.rut','Company.name','Company.center','Company.dv','Company.subcontratistaName','Company.subcontratistaRut','Company.subcontratistaDv'])->toArray();
 
         

            if(!empty($empresasContratistaDocumentas1al15)){

                foreach ($empresasContratistaDocumentas1al15 as $idC) {
                    $IDC[]=$idC['id'];
                    $Documentas1al15['rut']=$idC['rut']."-".$idC['dv'];
                    $Documentas1al15['name']=strtolower($idC['name']);
                    $Documentas1al15['center']=strtolower($idC['center']);
                    if($idC['subcontratistaRut']!=""){
                        $Documentas1al15['subRut']=$idC['subcontratistaRut']."-".$idC['subcontratistaDv'];
                        $Documentas1al15['subName']=strtolower($idC['subcontratistaName']);
                    }else{
                        $Documentas1al15['subRut']="";
                        $Documentas1al15['subName']="";
                    }
                    
                    $DATOSDocumentas1al15[]=$Documentas1al15;
                }
               
                $countDocumentas1al15 = count($IDC);

            }else{
                $countDocumentas1al15 = 0;
                $DATOSDocumentas1al15 = 0;
            }
          
            if(!empty($IDC)){
                //////////////////////////////////////////////////// documentaod al 16 del mes AL 20 /////
                $empresasContratistaDocumenta16al20 = Contratista::distinct()->where('mainCompanyRut',$rutprincipalR)
                ->join('CertificateHistory', 'CertificateHistory.companyId', '=', 'Company.id')   
                ->where('CertificateHistory.certificateState',6)     
                ->whereBetween('CertificateHistory.date1', array($fechap16,  $fechap20))           
                ->where('Company.periodId',$idPerido)
                ->whereNotIn('Company.id', $IDC)
                ->orderBy('Company.id', 'ASC')
                ->get(['Company.id','Company.rut','Company.name','Company.center','Company.dv','Company.subcontratistaName','Company.subcontratistaRut','Company.subcontratistaDv'])->toArray();

                if(!empty($empresasContratistaDocumenta16al20)){
                    foreach ($empresasContratistaDocumenta16al20 as $idC) {

                        $IDC2[]=$idC['id'];
                        $Documenta16al20['rut']=$idC['rut']."-".$idC['dv'];
                        $Documenta16al20['name']=strtolower($idC['name']);
                        $Documenta16al20['center']=strtolower($idC['center']);
                        if($idC['subcontratistaRut']!=""){
                            $Documenta16al20['subRut']=$idC['subcontratistaRut']."-".$idC['subcontratistaDv'];
                            $Documenta16al20['subName']=strtolower($idC['subcontratistaName']);
                        }else{
                            $Documenta16al20['subRut']="";
                            $Documenta16al20['subName']="";
                        }
                        
                        $DATOSDocumenta16al20[]=$Documenta16al20;
                        # code...
                    }
                    $countDocumentas16al20 = count($IDC2);
                }else{
                    $countDocumentas16al20 = 0;
                    $DATOSDocumenta16al20 = 0;
                }
            }else{
                $empresasContratistaDocumenta16al20 = Contratista::distinct()->where('mainCompanyRut',$rutprincipalR)
                ->join('CertificateHistory', 'CertificateHistory.companyId', '=', 'Company.id')   
                ->where('CertificateHistory.certificateState',6)     
                ->whereBetween('CertificateHistory.date1', array($fechap16,  $fechap20))           
                ->where('Company.periodId',$idPerido)
                ->orderBy('Company.id', 'ASC')
                ->get(['Company.id','Company.rut','Company.name','Company.center','Company.dv','Company.subcontratistaName','Company.subcontratistaRut','Company.subcontratistaDv'])->toArray();

                if(!empty($empresasContratistaDocumenta16al20)){
                    foreach ($empresasContratistaDocumenta16al20 as $idC) {

                        $IDC2[]=$idC['id'];
                        $Documenta16al20['rut']=$idC['rut']."-".$idC['dv'];
                        $Documenta16al20['name']=strtolower($idC['name']);
                        $Documenta16al20['center']=strtolower($idC['center']);
                        if($idC['subcontratistaRut']!=""){
                            $Documenta16al20['subRut']=$idC['subcontratistaRut']."-".$idC['subcontratistaDv'];
                            $Documenta16al20['subName']=strtolower($idC['subcontratistaName']);
                        }else{
                            $Documenta16al20['subRut']="";
                            $Documenta16al20['subName']="";
                        }
                        
                        $DATOSDocumenta16al20[]=$Documenta16al20;
                        # code...
                    }
                    $countDocumentas16al20 = count($IDC2);
                }else{
                    $countDocumentas16al20 = 0;
                    $DATOSDocumenta16al20 = 0;
                }
            }
            if(!empty($IDC2)){    
                //////////////////////////////////////////////////// documentaod al 21 del mes AL 25/////
                $empresasContratistaDocumenta21al25 = Contratista::distinct()->where('mainCompanyRut',$rutprincipalR)
                ->join('CertificateHistory', 'CertificateHistory.companyId', '=', 'Company.id')   
                ->where('CertificateHistory.certificateState',6)     
                ->whereBetween('CertificateHistory.date1', array($fechap21,  $fechap25))           
                ->where('Company.periodId',$idPerido)
                ->whereNotIn('Company.id', $IDC2)
                ->orderBy('Company.id', 'ASC')
                ->get(['Company.id','Company.rut','Company.name','Company.center','Company.dv','Company.subcontratistaName','Company.subcontratistaRut','Company.subcontratistaDv'])->toArray();

                if(!empty($empresasContratistaDocumenta21al25)){
                    foreach ($empresasContratistaDocumenta21al25 as $idC) {

                        $IDC3[]=$idC['id'];
                        $Documenta21al25['rut']=$idC['rut']."-".$idC['dv'];
                        $Documenta21al25['name']=strtolower($idC['name']);
                        $Documenta21al25['center']=strtolower($idC['center']);
                        if($idC['subcontratistaRut']!=""){
                            $Documenta21al25['subRut']=$idC['subcontratistaRut']."-".$idC['subcontratistaDv'];
                            $Documenta21al25['subName']=strtolower($idC['subcontratistaName']);
                        }else{
                            $Documenta21al25['subRut']="";
                            $Documenta21al25['subName']="";
                        }
                        
                        $DATOSDocumenta21al25[]=$Documenta21al25;
                        # code...
                    }

                    $countDocumentas21al25 = count($empresasContratistaDocumenta21al25);
              
                    $documentados = intval($countDocumentas1al15) + intval($countDocumentas16al20) + intval($countDocumentas21al25);
           
                    $sinDocumentar = $total_companies - $documentados;
                    $noID = array_merge($IDC,$IDC2,$IDC3);
                    $empresasContratistaSinDoc = Contratista::distinct()->where('mainCompanyRut',$rutprincipalR)
                    ->join('CertificateHistory', 'CertificateHistory.companyId', '=', 'Company.id')                
                    ->where('Company.periodId',$idPerido)
                    ->whereNotIn('Company.id', $noID)
                    ->orderBy('Company.id', 'ASC')
                    ->get(['Company.id','Company.rut','Company.name','Company.center','Company.dv','Company.subcontratistaName','Company.subcontratistaRut','Company.subcontratistaDv'])->toArray();

                    foreach ($empresasContratistaSinDoc as $idC) {

                        $IDC4[]=$idC['id'];
                        $SinDoc['rut']=$idC['rut']."-".$idC['dv'];
                        $SinDoc['name']=strtolower($idC['name']);
                        $SinDoc['center']=strtolower($idC['center']);
                        if($idC['subcontratistaRut']!=""){
                            $SinDoc['subRut']=$idC['subcontratistaRut']."-".$idC['subcontratistaDv'];
                            $SinDoc['subName']=strtolower($idC['subcontratistaName']);
                        }else{
                            $SinDoc['subRut']="";
                            $SinDoc['subName']="";
                        }
                        
                        $DATOSSinDoc[]=$SinDoc;
                        # code...
                    }

                }else{
                    $countDocumentas21al25 = 0;

                    $documentados = intval($countDocumentas1al15) + intval($countDocumentas16al20) + intval($countDocumentas21al25);
           
                    $sinDocumentar = $total_companies - $documentados;
                    $DATOSDocumenta21al25=0;
                }
            }else{
                $countDocumentas21al25 = 0;
                $DATOSDocumenta21al25=0;
                $documentados = intval($countDocumentas1al15) + intval($countDocumentas16al20) + intval($countDocumentas21al25);
                $sinDocumentar = $total_companies - $documentados;
            }
            
            ////// CONTEO DE CUANTAS VECES HA QUEDADO NO APROBADO ////
            $empresasContratistaCertificadas = Contratista::distinct()->where('mainCompanyRut',$rutprincipalR)
                ->where('periodId',$idPerido)
                ->where('certificateState', 5)
                ->orderBy('id', 'ASC')
                ->get(['id','rut','dv','name','mainCompanyName','companyTypeId','mainCompanyRut','center','certificateState','certificateDate','activity','workersNumber','periodId','subcontratistaRut','subcontratistaName','subcontratistaDv','motivo_inactivo','direccion','gerencia','tiposerv','companycatid','certificateObservations','contratoPaymentType','servicioId','classserv','adminContrato'])->toArray();
            $unaNoAprobados = 0;
            $dosNoAprobados = 0;
            $tresNoAprobados = 0;
            $cuatroNoAprobados = 0;
            $cincoNoAprobados = 0;
            $cantidadtotal = count($empresasContratistaCertificadas);
            foreach ($empresasContratistaCertificadas as $empresa) {
                $cantidadNoAprobados = 0;
                $historialCertificado = CertificateHistory::where('companyId',$empresa['id'])->where('certificateState',4)->get(['certificateState'])->toArray();
                
                $cantidadNoAprobados = count($historialCertificado);
                if(intval($cantidadNoAprobados) == 1){
                    $idUna[]=$empresa['id'];
                    $unaNoAprobados = $unaNoAprobados + 1;
                }     
                if(intval($cantidadNoAprobados) == 2){
                    $idDos[]=$empresa['id'];
                    $dosNoAprobados = $dosNoAprobados + 1;
                }
                if(intval($cantidadNoAprobados) == 3){
                    $idTres[]=$empresa['id'];
                    $tresNoAprobados = $tresNoAprobados + 1;
                }
                if(intval($cantidadNoAprobados) == 4){
                    $idCuatro[]=$empresa['id'];
                    $cuatroNoAprobados = $cuatroNoAprobados + 1;
                }
                if(intval($cantidadNoAprobados) == 5){
                    $idCinco[]=$empresa['id'];
                    $cincoNoAprobados = $cincoNoAprobados + 1;
                }
                if($cantidadNoAprobados == 0 or $historialCertificado[0]['certificateState']==""){

                    $idA[]=$empresa['id'];
                }               
            }
            $ceroNoAprobados = count($idA);
            $empresasCeroNoAprobados = Contratista::distinct()->where('mainCompanyRut',$rutprincipalR)          
            ->where('Company.periodId',$idPerido)
            ->whereIn('Company.id', $idA)
            ->orderBy('Company.id', 'ASC')
            ->get(['Company.id','Company.rut','Company.name','Company.center','Company.dv','Company.subcontratistaName','Company.subcontratistaRut','Company.subcontratistaDv'])->toArray();

                foreach ($empresasCeroNoAprobados as $idCN) {

                    $CeroNoAprobado['rut']=$idCN['rut']."-".$idCN['dv'];
                    $CeroNoAprobado['name']=strtolower($idCN['name']);
                    $CeroNoAprobado['center']=strtolower($idCN['center']);
                    if($idCN['subcontratistaRut']!=""){
                        $CeroNoAprobado['subRut']=$idCN['subcontratistaRut']."-".$idCN['subcontratistaDv'];
                        $CeroNoAprobado['subName']=strtolower($idCN['subcontratistaName']);
                    }else{
                        $CeroNoAprobado['subRut']="";
                        $CeroNoAprobado['subName']="";
                    }
                    
                    $DATOSCeroNoAprobado[]=$CeroNoAprobado;
                    # code...
                }

            $empresasUnaNoAprobados = Contratista::distinct()->where('mainCompanyRut',$rutprincipalR)
            ->whereIn('Company.id', $idUna)
            ->orderBy('Company.id', 'ASC')
            ->get(['Company.id','Company.rut','Company.name','Company.center','Company.dv','Company.subcontratistaName','Company.subcontratistaRut','Company.subcontratistaDv'])->toArray();

                foreach ($empresasUnaNoAprobados as $idC1N) {

                    $UnaNoAprobado['rut']=$idC1N['rut']."-".$idC1N['dv'];
                    $UnaNoAprobado['name']=strtolower($idC1N['name']);
                    $UnaNoAprobado['center']=strtolower($idC1N['center']);
                    if($idC1N['subcontratistaRut']!=""){
                        $UnaNoAprobado['subRut']=$idC1N['subcontratistaRut']."-".$idC1N['subcontratistaDv'];
                        $UnaNoAprobado['subName']=strtolower($idC1N['subcontratistaName']);
                    }else{
                        $UnaNoAprobado['subRut']="";
                        $UnaNoAprobado['subName']="";
                    }
                    
                    $DATOSUnaNoAprobado[]=$UnaNoAprobado;
                    # code...
                }

            $empresasDosNoAprobados = Contratista::distinct()->where('mainCompanyRut',$rutprincipalR)
            ->whereIn('Company.id', $idDos)
            ->orderBy('Company.id', 'ASC')
            ->get(['Company.id','Company.rut','Company.name','Company.center','Company.dv','Company.subcontratistaName','Company.subcontratistaRut','Company.subcontratistaDv'])->toArray();

                foreach ($empresasDosNoAprobados as $idC2N) {

                    $DosNoAprobado['rut']=$idC2N['rut']."-".$idC2N['dv'];
                    $DosNoAprobado['name']=strtolower($idC2N['name']);
                    $DosNoAprobado['center']=strtolower($idC2N['center']);
                    if($idC2N['subcontratistaRut']!=""){
                        $DosNoAprobado['subRut']=$idC2N['subcontratistaRut']."-".$idC2N['subcontratistaDv'];
                        $DosNoAprobado['subName']=strtolower($idC2N['subcontratistaName']);
                    }else{
                        $DosNoAprobado['subRut']="";
                        $DosNoAprobado['subName']="";
                    }
                    
                    $DATOSDosNoAprobado[]=$DosNoAprobado;
                    # code...
                }

            $empresasTresNoAprobados = Contratista::distinct()->where('mainCompanyRut',$rutprincipalR)
            ->whereIn('Company.id', $idTres)
            ->orderBy('Company.id', 'ASC')
            ->get(['Company.id','Company.rut','Company.name','Company.center','Company.dv','Company.subcontratistaName','Company.subcontratistaRut','Company.subcontratistaDv'])->toArray();

                foreach ($empresasTresNoAprobados as $idC3N) {

                    $TresNoAprobado['rut']=$idC3N['rut']."-".$idC3N['dv'];
                    $TresNoAprobado['name']=strtolower($idC3N['name']);
                    $TresNoAprobado['center']=strtolower($idC3N['center']);
                    if($idC3N['subcontratistaRut']!=""){
                        $TresNoAprobado['subRut']=$idC3N['subcontratistaRut']."-".$idC3N['subcontratistaDv'];
                        $TresNoAprobado['subName']=strtolower($idC3N['subcontratistaName']);
                    }else{
                        $TresNoAprobado['subRut']="";
                        $TresNoAprobado['subName']="";
                    }
                    
                    $DATOSTresNoAprobado[]=$TresNoAprobado;
                    # code...
                }

            $empresasCautroNoAprobados = Contratista::distinct()->where('mainCompanyRut',$rutprincipalR)
            ->whereIn('Company.id', $idCuatro)
            ->orderBy('Company.id', 'ASC')
            ->get(['Company.id','Company.rut','Company.name','Company.center','Company.dv','Company.subcontratistaName','Company.subcontratistaRut','Company.subcontratistaDv'])->toArray();

                foreach ($empresasCautroNoAprobados as $idC4N) {

                    $CuatroNoAprobado['rut']=$idC4N['rut']."-".$idC4N['dv'];
                    $CuatroNoAprobado['name']=strtolower($idC4N['name']);
                    $CuatroNoAprobado['center']=strtolower($idC4N['center']);
                    if($idC4N['subcontratistaRut']!=""){
                        $CuatroNoAprobado['subRut']=$idC4N['subcontratistaRut']."-".$idC4N['subcontratistaDv'];
                        $CuatroNoAprobado['subName']=strtolower($idC4N['subcontratistaName']);
                    }else{
                        $CuatroNoAprobado['subRut']="";
                        $CuatroNoAprobado['subName']="";
                    }
                    
                    $DATOSCuatroNoAprobado[]=$CuatroNoAprobado;
                    # code...
                }

            $empresasCincoNoAprobados = Contratista::distinct()->where('mainCompanyRut',$rutprincipalR)
            ->whereIn('Company.id', $idCinco)
            ->orderBy('Company.id', 'ASC')
            ->get(['Company.id','Company.rut','Company.name','Company.center','Company.dv','Company.subcontratistaName','Company.subcontratistaRut','Company.subcontratistaDv'])->toArray();

                foreach ($empresasCincoNoAprobados as $idC5N) {

                    $CincoNoAprobado['rut']=$idC5N['rut']."-".$idC5N['dv'];
                    $CincoNoAprobado['name']=strtolower($idC5N['name']);
                    $CincoNoAprobado['center']=strtolower($idC5N['center']);
                    if($idC5N['subcontratistaRut']!=""){
                        $CincoNoAprobado['subRut']=$idC5N['subcontratistaRut']."-".$idC5N['subcontratistaDv'];
                        $CincoNoAprobado['subName']=strtolower($idC5N['subcontratistaName']);
                    }else{
                        $CincoNoAprobado['subRut']="";
                        $CincoNoAprobado['subName']="";
                    }
                    
                    $DATOSCincoNoAprobado[]=$CincoNoAprobado;
                    # code...
                }
            /*echo $cantidadtotal."<br>";
            echo $unaNoAprobados."<br>";
            echo $dosNoAprobados."<br>";
            echo $tresNoAprobados."<br>";
            echo $cuatroNoAprobados."<br>";
            echo $cincoNoAprobados."<br>";
            echo $ceroNoAprobados."<br>";
            print_r($idA);*/


            //// TIEMPO EN ESTADO entre no aprobado y documentado
            //////////////////////////////////////////////////// documentaod al 15 del mes /////
            //////////////////////////////////////////////////// documentaod al 15 del mes /////
            $tiempoNoaproDoc = CertificateHistory::whereIn('CertificateHistory.certificateState',array(4,  6))
            ->join('Company', 'Company.id', '=', 'CertificateHistory.companyId')   
            ->where('Company.mainCompanyRut',$rutprincipalR)           
            ->where('Company.periodId',$idPerido)
            ->orderBy('Company.id', 'ASC')
            ->get(['CertificateHistory.id as idH','CertificateHistory.certificateState','CertificateHistory.date1','Company.id','Company.rut','Company.name','Company.center','Company.dv','Company.subcontratistaName','Company.subcontratistaRut','Company.subcontratistaDv'])->toArray();
            $tresDias=0;
            $cincoDias =0;
            $ochoDias = 0;
            $diezDias =0;
            $onceDias =0;
            foreach ($tiempoNoaproDoc as $value) {

                $historialCertificados = CertificateHistory::where('companyId',$value['id'])->whereIn('certificateState',array(4,  6))->get(['certificateState','date1','companyId'])->toArray();
               
                $segundos_diferencia = 0;
                $segundos_diferencia1 = 0;
                $dias_diferencia = 0;
                if(isset($historialCertificados[1]['certificateState'])){
                    if($historialCertificados[1]['certificateState']== 4){
                        if(isset($historialCertificados[2]['certificateState'])){

                            if($historialCertificados[2]['certificateState']== 6){
                                $segundos_diferencia = $historialCertificados[2]['date1'] - $historialCertificados[1]['date1'];
                                $dias_diferencia1 = $segundos_diferencia / (60 * 60 * 24);
                                $dias_diferencia = abs($dias_diferencia1);

                                if($dias_diferencia > 0 and $dias_diferencia <= 3){
                                    $Empresas3Dias[] =  $historialCertificados[2]['companyId'];
                                    $tresDias = $tresDias +1;
                                }if($dias_diferencia > 3 and $dias_diferencia <=5){
                                    $Empresas5Dias[] =  $historialCertificados[2]['companyId'];
                                    $cincoDias = $cincoDias +1;
                                }if($dias_diferencia > 5 and $dias_diferencia <=8){
                                    $Empresas8Dias[] =  $historialCertificados[2]['companyId'];
                                    $ochoDias = $ochoDias +1;
                                }if($dias_diferencia > 8 and $dias_diferencia <=10){
                                    $Empresas10Dias[] =  $historialCertificados[2]['companyId'];
                                    $diezDias = $diezDias +1;
                                }if($dias_diferencia >= 11){
                                    $Empresas11Dias[] =  $historialCertificados[2]['companyId'];
                                    $onceDias = $onceDias +1;
                                }
                            }
                        }
                    }
                }

                # code...
            }

            ///data to template pdf
            $header_for_table_first_page = ['Ingresado','Solicitado','Aprobado','No Aprobado','Certificado','Documentado','Historico','Completo','En Proceso','No Conforme','Inactivo'];
            $data = [
                'chart_by_company_type' => $chart_by_company_type,
                'bars_by_company_type' => $bars_by_company_type,
                'chart_genre_worker' => $chart_genre_worker,
                'bars_by_genre' => $bars_by_genre,
                'chart_by_rectificadas' => $chart_by_rectificadas,
                'bars_by_rectificadas' => $bars_by_rectificadas,
                'chart_per_certificate_state' => $chart_per_certificate_state,
                'bars_by_certificate_state' => $bars_by_certificate_state,
                'mes' =>  $MAP_MONTH[$curr_month],
                'anio'  => $curr_year,
                'bars_by_empresa_contratista' => $bars_by_empresa_contratista,
                'header_for_table_first_page' => $header_for_table_first_page,
                'count_company_per_certificate_state' => $count_company_per_certificate_state,
                'total_companies' => $total_companies,
                'chart_empresas_sin_documentar_empresas_aprobadas' => $chart_empresas_sin_documentar_empresas_aprobadas,
                'estadistica_por_empresa_charts' => $estadistica_por_empresa_charts,
                'countDocumentas1al15' => $countDocumentas1al15,
                'countDocumentas16al20' => $countDocumentas16al20,
                'countDocumentas21al25' => $countDocumentas21al25,
                'sinDocumentar' => $sinDocumentar,
                'unaNoAprobados' => $unaNoAprobados,
                'dosNoAprobados' => $dosNoAprobados,
                'tresNoAprobados' => $tresNoAprobados,
                'cuatroNoAprobados' => $cuatroNoAprobados,
                'cincoNoAprobados' => $cincoNoAprobados,
                'ceroNoAprobados' => $ceroNoAprobados,
                'DATOSDocumentas1al15' => $DATOSDocumentas1al15,
                'DATOSDocumenta16al20' => $DATOSDocumenta16al20,
                'DATOSDocumenta21al25' => $DATOSDocumenta21al25,
                'DATOSSinDoc'=> $DATOSSinDoc,
                'DATOSCeroNoAprobado' => $DATOSCeroNoAprobado,
                'DATOSUnaNoAprobado'=> $DATOSUnaNoAprobado,
                'DATOSDosNoAprobado'=> $DATOSDosNoAprobado,
                'DATOSTresNoAprobado'=> $DATOSTresNoAprobado,
                'DATOSCuatroNoAprobado'=> $DATOSCuatroNoAprobado,
                'DATOSCincoNoAprobado' => $DATOSCincoNoAprobado,
                'tresDias' => $tresDias,
                'cincoDias' => $cincoDias,
                'ochoDias' => $ochoDias,
                'diezDias' => $diezDias,
                'onceDias' => $onceDias,
                'DATAEMPRESA' => $DATAEMPRESA,

            ];

            $pdf = PDF::loadView('informeCer.informeCer', $data);            
            $pdf->setOption('enable-javascript', true);
            $pdf->setOption('javascript-delay', 5000);
            $pdf->setOption('enable-smart-shrinking', true);
            $pdf->setOption('no-stop-slow-scripts', true);
            return $pdf->download('InformeCertificacion.pdf');
            /*$filename = "\informerBE_";

            $user = (object)[
                'email' => 'winstonchavez53@gmail.com',
                'name' => 'Desarrollo',
            ];

            $moya = (object)[
                'email' => 'almc86@gmail.com',
                'name' => 'Desarrollo',
            ];

            $pdf->save( base_path('\public\pdf_temp' . $filename . '.pdf'));*/
        }
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
}
