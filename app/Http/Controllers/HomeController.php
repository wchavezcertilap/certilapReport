<?php

namespace App\Http\Controllers;
use App\DatosUsuarioLogin;
use App\UsuarioContratista;
use App\UsuarioPrincipal;
use App\FolioSso;
use App\empresaPrincipal;
use App\Periodo;
use App\Month;
use App\Contratista;
use App\EstadoDocumento;
use App\trabajadorSSO;
use App\Documento;
use App\TrabajadorVerificacion;
use App\CargoSSO;
use App\UsuarioSSOnok;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function inicio($idUser)
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
        $idUsuario = rtrim(base64_decode($idUser));
        if($idUsuario == ""){
            return view('sesion.index');
        }
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
        $datosUsuarios->load('cargaUsuarioContratista');
        $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
        $UsuarioPrincipal->load('usuarioDatos');

        $usuarioNOK = UsuarioSSOnok::where('user_id','=',$idUsuario)->get(['user_sso_isnoc'])->toArray();
       
        if(!empty($usuarioNOK)){
            if($usuarioNOK[0]['user_sso_isnoc']==1){
                $usuarioNOKactivo = 1;
                session(['usuario_nok' => $usuarioNOKactivo]);
            }else{
                $usuarioNOKactivo = 0;
                session(['usuario_nok' => $usuarioNOKactivo]);
            }
        }else{
            $usuarioNOKactivo = 0;
            session(['usuario_nok' => $usuarioNOKactivo]);
        }


        if($datosUsuarios->type ==3){

            foreach ($UsuarioPrincipal as $rut) {

                $rutprincipal[]=$rut['mainCompanyRut'];
                
            }

            //// validacion empresas holding aqua chile ///
            $aquaChile = 0;
            foreach($rutprincipal AS $rut)
            {
              
              switch ($rut) {
                 case 76452811:
                      $aquaChile = 1;
                      break;
                 case 76794910:
                      $aquaChile = 1;
                      break;
                 case 79872420:
                      $aquaChile = 1;
                      break;
                 case 86247400:
                      $aquaChile = 1;
                      break;
                 case 79800600:
                      $aquaChile = 1;
                      break;
                 case 84449400:
                      $aquaChile = 1;
                      break;
                 case 88274600:
                      $aquaChile = 1;
                      break;
                 case 87782700:
                      $aquaChile = 1;
                      break;
                 case 76495180:
                      $aquaChile = 1;
                      break;
                 case 99595500:
                      $aquaChile = 1;
                      break;
                 case 89604200:
                      $aquaChile = 1;
                      break;
                 case 78754560:
                      $aquaChile = 1;
                      break;
                 case 76125666:
                      $aquaChile = 1;
                      break;
                 case 78512930:
                      $aquaChile = 1;
                      break;
                 case 76650680:
                      $aquaChile = 1;
                      break;
                 }
            }
            if($aquaChile==1){
                session(['user_aqua' => $aquaChile]);
                $usuarioAqua = 1;
            }else{
                $usuarioAqua = 0;
            }

            /// validacion ABB ///
            $ABBChile = 0;
            foreach($rutprincipal AS $rut)
            {
              
              switch ($rut) {
                case 92805000:
                      $ABBChile = 1;
                      break;
                }
            }
            if($ABBChile==1){
                session(['user_ABB' => $ABBChile]);
                $usuarioABBChile = 1;
            }else{
                $usuarioABBChile = 0;
            }

            /// validacion Claro ///
            $ClaroChile = 0;
            foreach($rutprincipal AS $rut)
            {
              
              switch ($rut) {
                case 96631610:
                      $ClaroChile = 1;
                      break;
                case 96799250:
                      $ClaroChile = 1;
                      break;
                case 94675000:
                      $ClaroChile = 1;
                      break;
                case 96901710:
                      $ClaroChile = 1;
                      break;
                case 88381200:
                      $ClaroChile = 1;
                      break;
                case 95714000:
                      $ClaroChile = 1;
                      break;
                case 95714000:
                      $ClaroChile = 1;
                      break;   
                }
            }
            if($ClaroChile==1){
                session(['user_Claro' => $ClaroChile]);
                $usuarioClaroChile = 1;
            }else{
                $usuarioClaroChile = 0;
            }
        }
  
        $UsuarioContratista = UsuarioContratista::where('systemUserId','=',$idUsuario)->get();
        $UsuarioContratista->load('usuarioDatos');

        foreach ($UsuarioContratista as $rut) {

            $rutcontratista[]=$rut['companyRut'];

            # code...
        }

        session(['user_id' => $idUsuario]);
        $idUsuario = session('user_id');
        if($idUsuario ==  ""){
            return view('sesion.index');
        }
    
            //// usuarios certilap ///
            if($datosUsuarios->type ==2 or $datosUsuarios->type == 1){
                $certificacion = 0;
                $aquaChile = 0;
                session(['user_aqua' => $aquaChile]);
                $usuarioAqua = 0;
                session(['certificacion' => $certificacion]);
                $usuarioABBChile = 0;
                session(['user_ABB' => $usuarioABBChile]);
                $usuarioClaroChile = 0;
                session(['user_Claro' => $usuarioClaroChile]);

                $fechaHoy = getdate(); 
                $etiquetaMes =array();
                $bMeses = array("void","Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
  
                $anio =date('Y');
                for ($i=$fechaHoy["mon"]; $i > 0; $i--) { 
                    if($i == 1){
                        $mes="Enero"; 
                        $fechaEneroInf = "2-1-".$anio;
                        $fechaEneroIn = strtotime($fechaEneroInf);
                        $fechaEnerofi = "1-2-".$anio;
                        $fechaEneroFi = strtotime($fechaEnerofi);
                        $totalFolio = FolioSso::where('sso_status','1') ->whereBetween('sso_crtdat', array($fechaEneroIn,  $fechaEneroFi))->count();
                        $totalTrabajadoresM = trabajadorSSO::where('worker_status','1')->where('desvinculado','0')->whereBetween('worker_crtdat', array($fechaEneroIn,  $fechaEneroFi))->distinct('worker_rut')->count('worker_rut');;
                        
                    }
                    if($i == 2){
                        $fechaEneroInf = "2-2-".$anio;
                        $fechaEneroIn = strtotime($fechaEneroInf);
                        $fechaEnerofi = "1-3-".$anio;
                        $fechaEneroFi = strtotime($fechaEnerofi);
                        $totalFolio = FolioSso::where('sso_status','1') ->whereBetween('sso_crtdat', array($fechaEneroIn,  $fechaEneroFi))->count();
                        $totalTrabajadoresM = trabajadorSSO::where('worker_status','1')->where('desvinculado','0')->whereBetween('worker_crtdat', array($fechaEneroIn,  $fechaEneroFi))->distinct('worker_rut')->count('worker_rut');
                        $mes="Febrero";
                    }
                    if($i == 3){
                        $mes="Marzo";
                        $fechaEneroInf = "2-3-".$anio;
                        $fechaEneroIn = strtotime($fechaEneroInf);
                        $fechaEnerofi = "1-4-".$anio;
                        $fechaEneroFi = strtotime($fechaEnerofi);
                        $totalFolio = FolioSso::where('sso_status','1') ->whereBetween('sso_crtdat', array($fechaEneroIn,  $fechaEneroFi))->count();
                        $totalTrabajadoresM = trabajadorSSO::where('worker_status','1')->where('desvinculado','0')->whereBetween('worker_crtdat', array($fechaEneroIn,  $fechaEneroFi))->distinct('worker_rut')->count('worker_rut');
                       
                    }
                    if($i == 4){
                        $mes="Abril";
                        $fechaEneroInf = "2-4-".$anio;
                        $fechaEneroIn = strtotime($fechaEneroInf);
                        $fechaEnerofi = "1-5-".$anio;
                        $fechaEneroFi = strtotime($fechaEnerofi);
                        $totalFolio = FolioSso::where('sso_status','1') ->whereBetween('sso_crtdat', array($fechaEneroIn,  $fechaEneroFi))->count();
                        $totalTrabajadoresM = trabajadorSSO::where('worker_status','1')->where('desvinculado','0')->whereBetween('worker_crtdat', array($fechaEneroIn,  $fechaEneroFi))->distinct('worker_rut')->count('worker_rut');
                    }
                    if($i == 5){
                        $mes="Mayo";
                        $fechaEneroInf = "2-5-".$anio;
                        $fechaEneroIn = strtotime($fechaEneroInf);
                        $fechaEnerofi = "1-6-".$anio;
                        $fechaEneroFi = strtotime($fechaEnerofi);
                        $totalFolio = FolioSso::where('sso_status','1') ->whereBetween('sso_crtdat', array($fechaEneroIn,  $fechaEneroFi))->count();
                        $totalTrabajadoresM = trabajadorSSO::where('worker_status','1')->where('desvinculado','0')->whereBetween('worker_crtdat', array($fechaEneroIn,  $fechaEneroFi))->distinct('worker_rut')->count('worker_rut');
                    }
                    if($i == 6){
                        $mes="Junio";
                        $fechaEneroInf = "2-6-".$anio;
                        $fechaEneroIn = strtotime($fechaEneroInf);
                        $fechaEnerofi = "1-7-".$anio;
                        $fechaEneroFi = strtotime($fechaEnerofi);
                        $totalFolio = FolioSso::where('sso_status','1') ->whereBetween('sso_crtdat', array($fechaEneroIn,  $fechaEneroFi))->count();
                        $totalTrabajadoresM = trabajadorSSO::where('worker_status','1')->where('desvinculado','0')->whereBetween('worker_crtdat', array($fechaEneroIn,  $fechaEneroFi))->distinct('worker_rut')->count('worker_rut');
                    }
                    if($i== 7){
                        $mes="Julio";
                        $fechaEneroInf = "2-7-".$anio;
                        $fechaEneroIn = strtotime($fechaEneroInf);
                        $fechaEnerofi = "1-8-".$anio;
                        $fechaEneroFi = strtotime($fechaEnerofi);
                        $totalFolio = FolioSso::where('sso_status','1') ->whereBetween('sso_crtdat', array($fechaEneroIn,  $fechaEneroFi))->count();
                        $totalTrabajadoresM = trabajadorSSO::where('worker_status','1')->where('desvinculado','0')->whereBetween('worker_crtdat', array($fechaEneroIn,  $fechaEneroFi))->distinct('worker_rut')->count('worker_rut');
                    }
                    if($i == 8){
                        $mes="Agosto";
                        $fechaEneroInf = "2-8-".$anio;
                        $fechaEneroIn = strtotime($fechaEneroInf);
                        $fechaEnerofi = "1-9-".$anio;
                        $fechaEneroFi = strtotime($fechaEnerofi);
                        $totalFolio = FolioSso::where('sso_status','1') ->whereBetween('sso_crtdat', array($fechaEneroIn,  $fechaEneroFi))->count();
                        $totalTrabajadoresM = trabajadorSSO::where('worker_status','1')->where('desvinculado','0')->whereBetween('worker_crtdat', array($fechaEneroIn,  $fechaEneroFi))->distinct('worker_rut')->count('worker_rut');
                    }
                    if($i == 9){
                        $mes="Septiembre";
                        $fechaEneroInf = "2-9-".$anio;
                        $fechaEneroIn = strtotime($fechaEneroInf);
                        $fechaEnerofi = "1-10-".$anio;
                        $fechaEneroFi = strtotime($fechaEnerofi);
                        $totalFolio = FolioSso::where('sso_status','1') ->whereBetween('sso_crtdat', array($fechaEneroIn,  $fechaEneroFi))->count();
                        $totalTrabajadoresM = trabajadorSSO::where('worker_status','1')->where('desvinculado','0')->whereBetween('worker_crtdat', array($fechaEneroIn,  $fechaEneroFi))->distinct('worker_rut')->count('worker_rut');
                    }
                    if($i == 10){
                        $mes="Octubre";
                        $fechaEneroInf = "2-10-".$anio;
                        $fechaEneroIn = strtotime($fechaEneroInf);
                        $fechaEnerofi = "1-11-".$anio;
                        $fechaEneroFi = strtotime($fechaEnerofi);
                        $totalFolio = FolioSso::where('sso_status','1') ->whereBetween('sso_crtdat', array($fechaEneroIn,  $fechaEneroFi))->count();
                        $totalTrabajadoresM = trabajadorSSO::where('worker_status','1')->where('desvinculado','0')->whereBetween('worker_crtdat', array($fechaEneroIn,  $fechaEneroFi))->distinct('worker_rut')->count('worker_rut');
                    }
                    if($i == 11){
                        $mes="Noviembre";
                        $fechaEneroInf = "2-11-".$anio;
                        $fechaEneroIn = strtotime($fechaEneroInf);
                        $fechaEnerofi = "1-12-".$anio;
                        $fechaEneroFi = strtotime($fechaEnerofi);
                        $totalFolio = FolioSso::where('sso_status','1') ->whereBetween('sso_crtdat', array($fechaEneroIn,  $fechaEneroFi))->count();
                        $totalTrabajadoresM = trabajadorSSO::where('worker_status','1')->where('desvinculado','0')->whereBetween('worker_crtdat', array($fechaEneroIn,  $fechaEneroFi))->distinct('worker_rut')->count('worker_rut');
                    }
                    if($i == 12){
                        $mes="Diciembre";
                        $fechaEneroInf = "2-12-".$anio;
                        $fechaEneroIn = strtotime($fechaEneroInf);
                        $aniN= $anio+1;
                        $fechaEnerofi = "1-01-".$aniN;
                        $fechaEneroFi = strtotime($fechaEnerofi);
                        $totalFolio = FolioSso::where('sso_status','1') ->whereBetween('sso_crtdat', array($fechaEneroIn,  $fechaEneroFi))->count();
                        $totalTrabajadoresM = trabajadorSSO::where('worker_status','1')->where('desvinculado','0')->whereBetween('worker_crtdat', array($fechaEneroIn,  $fechaEneroFi))->distinct('worker_rut')->count('worker_rut');
                    }
                    
                    $etiquetaMes[] = $mes;
                    $valoresMes[] = $totalFolio;
                    $valoresTrabajador[] = $totalTrabajadoresM;
                   
                }




                $EmpresasP = empresaPrincipal::distinct()->orderBy('name', 'ASC')->get(['name','rut']);

                foreach ($EmpresasP as $value) {
                    $rutprincipal[] = $value['rut'];
                }

                //////////////// certificacion laboral ////////////////////////////
                    $estadoIngresado = 0;
                    $estadoSolicitado = 0;
                    $estadoAprobado = 0;
                    $estadoNoAprobado = 0;
                    $estadoCertificado = 0;
                    $estadoDocumentado = 0;
                    $estadoHistorico = 0;
                    $estadoCompleto = 0;
                    $estadoProceso = 0;
                    $estadoNoConforme = 0;
                    $estadoInactivo = 0;
                    $empresasContratistas = Contratista::whereIn('mainCompanyRut',$rutprincipal)->get(['certificateState','subcontratistaRut','rut','certificateDate']);
                    $cantidadEmpresas = count($empresasContratistas);

                    if(!empty($empresasContratistas)){
                     

                        session(['certificacion' => $certificacion]);
                        $anio =date('Y');
                        for ($i=$fechaHoy["mon"]; $i > 0; $i--) { 
                            if($i == 1){
                                $mes="Enero"; 
                                $fechaEneroInf = "2-1-".$anio;
                                $fechaEneroIn = strtotime($fechaEneroInf);
                                $fechaEnerofi = "1-2-".$anio;
                                $fechaEneroFi = strtotime($fechaEnerofi);
                                $totalContratista = Contratista::whereIn('mainCompanyRut',$rutprincipal)->where('certificateState',5)->whereBetween('certificateDate', array($fechaEneroIn,  $fechaEneroFi))->count();
                                $periodId = Periodo::where('year',$anio)->where('monthId',$i)->get(['id'])->toArray();
                                if(!empty($periodId)){
                                    $trabajodoresCertificacion = TrabajadorVerificacion::whereIn('mainCompanyRut',$rutprincipal)->where('periodId',$periodId[0]['id'])->count();
                                }else{
                                    $trabajodoresCertificacion = 0;
                                }
                           
                            }

                            if($i == 2){ 
                                $mes="Febrero";
                                $fechaEneroInf = "2-2-".$anio;
                                $fechaEneroIn = strtotime($fechaEneroInf);
                                $fechaEnerofi = "1-3-".$anio;
                                $fechaEneroFi = strtotime($fechaEnerofi);
                                $totalContratista = Contratista::whereIn('mainCompanyRut',$rutprincipal)->where('certificateState',5)->whereBetween('certificateDate', array($fechaEneroIn,  $fechaEneroFi))->count();
                                $periodId = Periodo::where('year',$anio)->where('monthId',$i)->get(['id'])->toArray();
                                if(!empty($periodId)){
                                    $trabajodoresCertificacion = TrabajadorVerificacion::whereIn('mainCompanyRut',$rutprincipal)->where('periodId',$periodId[0]['id'])->count();
                                }else{
                                    $trabajodoresCertificacion = 0;
                                }
                            }

                            if($i == 3){
                                $mes="Marzo";
                                $fechaEneroInf = "2-3-".$anio;
                                $fechaEneroIn = strtotime($fechaEneroInf);
                                $fechaEnerofi = "1-4-".$anio;
                                $fechaEneroFi = strtotime($fechaEnerofi);
                                $totalContratista = Contratista::whereIn('mainCompanyRut',$rutprincipal)->where('certificateState',5)->whereBetween('certificateDate', array($fechaEneroIn,  $fechaEneroFi))->count();
                                $periodId = Periodo::where('year',$anio)->where('monthId',$i)->get(['id'])->toArray();
                                if(!empty($periodId)){
                                    $trabajodoresCertificacion = TrabajadorVerificacion::whereIn('mainCompanyRut',$rutprincipal)->where('periodId',$periodId[0]['id'])->count();
                                }else{
                                    $trabajodoresCertificacion = 0;
                                }
                                
                            }

                            if($i == 4){
                                $mes="Abril";
                                $fechaEneroInf = "2-4-".$anio;
                                $fechaEneroIn = strtotime($fechaEneroInf);
                                $fechaEnerofi = "1-5-".$anio;
                                $fechaEneroFi = strtotime($fechaEnerofi);
                                $totalContratista = Contratista::whereIn('mainCompanyRut',$rutprincipal)->where('certificateState',5)->whereBetween('certificateDate', array($fechaEneroIn,  $fechaEneroFi))->count();
                                $periodId = Periodo::where('year',$anio)->where('monthId',$i)->get(['id'])->toArray();
                                if(!empty($periodId)){
                                    $trabajodoresCertificacion = TrabajadorVerificacion::whereIn('mainCompanyRut',$rutprincipal)->where('periodId',$periodId[0]['id'])->count();
                                }else{
                                    $trabajodoresCertificacion = 0;
                                }
                                
                            }

                            if($i == 5){
                                $mes="Mayo";
                                $fechaEneroInf = "2-5-".$anio;
                                $fechaEneroIn = strtotime($fechaEneroInf);
                                $fechaEnerofi = "1-6-".$anio;
                                $fechaEneroFi = strtotime($fechaEnerofi);
                                $totalContratista = Contratista::whereIn('mainCompanyRut',$rutprincipal)->where('certificateState',5)->whereBetween('certificateDate', array($fechaEneroIn,  $fechaEneroFi))->count();
                                $periodId = Periodo::where('year',$anio)->where('monthId',$i)->get(['id'])->toArray();
                                if(!empty($periodId)){
                                    $trabajodoresCertificacion = TrabajadorVerificacion::whereIn('mainCompanyRut',$rutprincipal)->where('periodId',$periodId[0]['id'])->count();
                                }else{
                                    $trabajodoresCertificacion = 0;
                                }
                                
                            }

                            if($i == 6){
                                $mes="Junio";
                                $fechaEneroInf = "2-6-".$anio;
                                $fechaEneroIn = strtotime($fechaEneroInf);
                                $fechaEnerofi = "1-7-".$anio;
                                $fechaEneroFi = strtotime($fechaEnerofi);
                                $totalContratista = Contratista::whereIn('mainCompanyRut',$rutprincipal)->where('certificateState',5)->whereBetween('certificateDate', array($fechaEneroIn,  $fechaEneroFi))->count();
                                $periodId = Periodo::where('year',$anio)->where('monthId',$i)->get(['id'])->toArray();
                               if(!empty($periodId)){
                                    $trabajodoresCertificacion = TrabajadorVerificacion::whereIn('mainCompanyRut',$rutprincipal)->where('periodId',$periodId[0]['id'])->count();
                                }else{
                                    $trabajodoresCertificacion = 0;
                                }
                                
                            }

                            if($i == 7){
                                $mes="Julio";
                                $fechaEneroInf = "2-7-".$anio;
                                $fechaEneroIn = strtotime($fechaEneroInf);
                                $fechaEnerofi = "1-8-".$anio;
                                $fechaEneroFi = strtotime($fechaEnerofi);
                                $totalContratista = Contratista::whereIn('mainCompanyRut',$rutprincipal)->where('certificateState',5)->whereBetween('certificateDate', array($fechaEneroIn,  $fechaEneroFi))->count();
                                $periodId = Periodo::where('year',$anio)->where('monthId',$i)->get(['id'])->toArray();
                                if(!empty($periodId)){
                                    $trabajodoresCertificacion = TrabajadorVerificacion::whereIn('mainCompanyRut',$rutprincipal)->where('periodId',$periodId[0]['id'])->count();
                                }else{
                                    $trabajodoresCertificacion = 0;
                                }
                                
                            }

                            if($i == 8){
                                $mes="Agosto";
                                $fechaEneroInf = "2-8-".$anio;
                                $fechaEneroIn = strtotime($fechaEneroInf);
                                $fechaEnerofi = "1-9-".$anio;
                                $fechaEneroFi = strtotime($fechaEnerofi);
                                $totalContratista = Contratista::whereIn('mainCompanyRut',$rutprincipal)->where('certificateState',5)->whereBetween('certificateDate', array($fechaEneroIn,  $fechaEneroFi))->count();
                                $periodId = Periodo::where('year',$anio)->where('monthId',$i)->get(['id'])->toArray();
                                if(!empty($periodId)){
                                    $trabajodoresCertificacion = TrabajadorVerificacion::whereIn('mainCompanyRut',$rutprincipal)->where('periodId',$periodId[0]['id'])->count();
                                }else{
                                    $trabajodoresCertificacion = 0;
                                }
                                
                            }

                            if($i == 9){
                                $mes="Septiembre";
                                $fechaEneroInf = "2-9-".$anio;
                                $fechaEneroIn = strtotime($fechaEneroInf);
                                $fechaEnerofi = "1-10-".$anio;
                                $fechaEneroFi = strtotime($fechaEnerofi);
                                $totalContratista = Contratista::whereIn('mainCompanyRut',$rutprincipal)->where('certificateState',5)->whereBetween('certificateDate', array($fechaEneroIn,  $fechaEneroFi))->count();
                                $periodId = Periodo::where('year',$anio)->where('monthId',$i)->get(['id'])->toArray();
                                if(!empty($periodId)){
                                    $trabajodoresCertificacion = TrabajadorVerificacion::whereIn('mainCompanyRut',$rutprincipal)->where('periodId',$periodId[0]['id'])->count();
                                }else{
                                    $trabajodoresCertificacion = 0;
                                }
                               
                            }

                            if($i == 10){
                                $mes="Octubre";
                                $fechaEneroInf = "2-10-".$anio;
                                $fechaEneroIn = strtotime($fechaEneroInf);
                                $fechaEnerofi = "1-11-".$anio;
                                $fechaEneroFi = strtotime($fechaEnerofi);
                                $totalContratista = Contratista::whereIn('mainCompanyRut',$rutprincipal)->where('certificateState',5)->whereBetween('certificateDate', array($fechaEneroIn,  $fechaEneroFi))->count();
                                $periodId = Periodo::where('year',$anio)->where('monthId',$i)->get(['id'])->toArray();
                                if(!empty($periodId)){
                                    $trabajodoresCertificacion = TrabajadorVerificacion::whereIn('mainCompanyRut',$rutprincipal)->where('periodId',$periodId[0]['id'])->count();
                                }else{
                                    $trabajodoresCertificacion = 0;
                                }
                            }

                            if($i == 11){
                                $mes="Noviembre";
                                $fechaEneroInf = "2-11-".$anio;
                                $fechaEneroIn = strtotime($fechaEneroInf);
                                $fechaEnerofi = "1-12-".$anio;
                                $fechaEneroFi = strtotime($fechaEnerofi);
                                $totalContratista = Contratista::whereIn('mainCompanyRut',$rutprincipal)->where('certificateState',5)->whereBetween('certificateDate', array($fechaEneroIn,  $fechaEneroFi))->count();
                                $periodId = Periodo::where('year',$anio)->where('monthId',$i)->get(['id'])->toArray();
                                if(!empty($periodId)){
                                    $trabajodoresCertificacion = TrabajadorVerificacion::whereIn('mainCompanyRut',$rutprincipal)->where('periodId',$periodId[0]['id'])->count();
                                }else{
                                    $trabajodoresCertificacion = 0;
                                }
                                
                            }

                            if($i == 12){
                                $mes="Diciembre";
                                $fechaEneroInf = "2-12-".$anio;
                                $fechaEneroIn = strtotime($fechaEneroInf);
                                $aniN= $anio+1;
                                $fechaEnerofi = "1-01-".$aniN;
                                $fechaEneroFi = strtotime($fechaEnerofi);
                                $totalContratista = Contratista::whereIn('mainCompanyRut',$rutprincipal)->where('certificateState',5)->
                                whereBetween('certificateDate', array($fechaEneroIn,  $fechaEneroFi))->count();
                                $periodId = Periodo::where('year',$anio)->where('monthId',$i)->get(['id'])->toArray();
                                if(!empty($periodId)){
                                    $trabajodoresCertificacion = TrabajadorVerificacion::whereIn('mainCompanyRut',$rutprincipal)->where('periodId',$periodId[0]['id'])->count();
                                }else{
                                    $trabajodoresCertificacion = 0;
                                }
                                
                            }
                            
                            $etiquetaMesCertificacion[] = $mes;
                            $valoresMesCerticacion[]  =  $totalContratista;
                            $valoresTrabajadoresCerticacion[]  =  $trabajodoresCertificacion;
                            
                            
                            
                        }

                        $graficaCertificacion = 1;
                        foreach ($empresasContratistas as  $value) {
                            $contratista['rut'] = $value['rut'];
                            $totalContrasitas[] = $contratista;
                            if($value['subcontratistaRut']!=''){

                                $subContratistas['rut'] = $value['subcontratistaRut'];
                                $totalSubContrasitas[] = $subContratistas;

                            }
                         
                            switch ($value['certificateState']) {
                                case 1:
                                $estadoIngresado +=1; 
                                break;
                                case 2:
                                $estadoSolicitado +=1;
                                break;
                                case 3:
                                $estadoAprobado +=1;
                                break;
                                case 4:
                                $estadoNoAprobado +=1;
                                break;
                                case 5:
                                $estadoCertificado +=1;
                                break;
                                case 6:
                                $estadoDocumentado +=1;
                                break;
                                case 7:
                                $estadoHistorico +=1;
                                break;
                                case 8:
                                $estadoCompleto +=1;
                                break;
                                case 9:
                                $estadoProceso +=1;
                                break;
                                case 10:
                                $estadoNoConforme +=1;
                                break;
                                case 11:
                                $estadoInactivo +=1;
                            }
                        } 

                        $datoContratistas = super_unique($totalContrasitas,'rut');
                        $cantidadContratistas = count($datoContratistas);

                        $datoSubContratistas = super_unique($totalSubContrasitas,'rut');
                        $cantidadSubContratistas = count($datoSubContratistas);
                    }  

              
                $folios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status', '1')->get();
                foreach ($folios as $value) {
                    $foliosActivos[] = $value['id']; 
                }
                $totalFolios =count($foliosActivos);
                $totalFolios = number_format($totalFolios);
                $totalDocuementos = EstadoDocumento::where('upld_status','1')->count();
                $totalDocuementos = number_format($totalDocuementos);
                $totalTrabajadores = trabajadorSSO::where('worker_status','1')->where('desvinculado','0')->distinct('worker_rut')->count('worker_rut');
                $totalTrabajadores = number_format($totalTrabajadores);
                $totalEmpresasPriSSO = FolioSso::distinct('sso_mcomp_rut')->where('sso_status','1')->get(['sso_mcomp_rut']);
                $totalEmpresasPriSSO = count($totalEmpresasPriSSO);
                $ssograficos = 1;
                $i = $fechaHoy["mon"];
                if($i == 1){
                    $mes="Enero";
                    $fechaEneroInf = "2-1-".$anio;
                    $fechaEneroIn = strtotime($fechaEneroInf);
                    $fechaEnerofi = "1-2-".$anio;
                    $fechaEneroFi = strtotime($fechaEnerofi); 
                }
                if($i == 2){
                    $mes="Febrero";
                    $fechaEneroInf = "2-2-".$anio;
                    $fechaEneroIn = strtotime($fechaEneroInf);
                    $fechaEnerofi = "1-3-".$anio;
                    $fechaEneroFi = strtotime($fechaEnerofi);
                }
                if($i == 3){
                    $mes="Marzo";
                    $fechaEneroInf = "2-3-".$anio;
                    $fechaEneroIn = strtotime($fechaEneroInf);
                    $fechaEnerofi = "1-4-".$anio;
                    $fechaEneroFi = strtotime($fechaEnerofi);
                }
                if($i == 4){
                    $mes="Abril";
                    $fechaEneroInf = "2-4-".$anio;
                    $fechaEneroIn = strtotime($fechaEneroInf);
                    $fechaEnerofi = "1-5-".$anio;
                    $fechaEneroFi = strtotime($fechaEnerofi);
                }
                if($i == 5){
                    $mes="Mayo";
                    $fechaEneroInf = "2-5-".$anio;
                    $fechaEneroIn = strtotime($fechaEneroInf);
                    $fechaEnerofi = "1-6-".$anio;
                    $fechaEneroFi = strtotime($fechaEnerofi);
                }
                if($i == 6){
                    $mes="Junio";
                    $fechaEneroInf = "2-6-".$anio;
                    $fechaEneroIn = strtotime($fechaEneroInf);
                    $fechaEnerofi = "1-7-".$anio;
                    $fechaEneroFi = strtotime($fechaEnerofi);
                }
                if($i== 7){
                    $mes="Julio";
                    $fechaEneroInf = "2-7-".$anio;
                    $fechaEneroIn = strtotime($fechaEneroInf);
                    $fechaEnerofi = "1-8-".$anio;
                    $fechaEneroFi = strtotime($fechaEnerofi);
                }
                if($i == 8){
                    $mes="Agosto";
                    $fechaEneroInf = "2-8-".$anio;
                    $fechaEneroIn = strtotime($fechaEneroInf);
                    $fechaEnerofi = "1-9-".$anio;
                    $fechaEneroFi = strtotime($fechaEnerofi);
                }
                if($i == 9){
                    $mes="Septiembre";
                    $fechaEneroInf = "2-9-".$anio;
                    $fechaEneroIn = strtotime($fechaEneroInf);
                    $fechaEnerofi = "1-10-".$anio;
                    $fechaEneroFi = strtotime($fechaEnerofi);
                }
                if($i == 10){
                    $mes="Octubre";
                    $fechaEneroInf = "2-10-".$anio;
                    $fechaEneroIn = strtotime($fechaEneroInf);
                    $fechaEnerofi = "1-11-".$anio;
                    $fechaEneroFi = strtotime($fechaEnerofi);
                }
                if($i == 11){
                    $mes="Noviembre";
                    $fechaEneroInf = "2-11-".$anio;
                    $fechaEneroIn = strtotime($fechaEneroInf);
                    $fechaEnerofi = "1-12-".$anio;
                    $fechaEneroFi = strtotime($fechaEnerofi);
                }
                if($i == 12){
                    $mes="Diciembre";
                    $fechaEneroInf = "2-12-".$anio;
                    $fechaEneroIn = strtotime($fechaEneroInf);
                    $aniN= $anio+1;
                    $fechaEnerofi = "1-01-".$aniN;
                    $fechaEneroFi = strtotime($fechaEnerofi);
                }
 
                $diaActual = strtotime("now");
                $hoyVista = date("d-m-Y");  
                $foliosActivos = FolioSso::where('sso_status','1')->whereBetween('sso_crtdat', array($fechaEneroIn,  $diaActual))->get();

                $totalDocuementosEmpresa = 0;
                $cantidadAprobados = 0;
                $cantidadRechazados = 0;
                $cantidadVencidos = 0;
                $cantidadPorRevision = 0;
                $totalTrabajadoresEmpresa = 0;
                $totalDoc = 0;
                $totalDocRechazados = 0;
                $totalDocAprobados = 0;
                $totalDocVencidos = 0;
                $totalDocRevision = 0;
                $totalDocTrabajadores = 0;
                $totalDocRechazadosTrabajadores = 0;
                $totalDocAprobadosTrabajadores = 0;
                $totalDocVencidosTrabajadores = 0;
                $totalDocRevisionTrabajadores = 0;
                $totalDocuementosEmpresaTrabajador = 0;
                $cantidadRechazadosTrabajador = 0;
                $cantidadAprobadosTrabajador = 0;
                $cantidadVencidosTrabajador = 0;
                $cantidadPorRevisionTrabajador = 0;
                foreach ($foliosActivos as $folio) {
                    $documentosGlobales = EstadoDocumento::where('upld_sso_id',$folio["id"])->where('upld_status', '1')->where('upld_type', '0')->whereBetween('upld_crtdat', array($fechaEneroIn,  $diaActual))->get();

                    foreach ($documentosGlobales as $value) {
                        ////////////////// fecha para determinar si esta expirado /////
                        $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
                        $fecha2 = $value["upld_vence_date"];
                        $fechaUpdate = $value["upld_upddat"];
                        //////////////////////
                        /// NOMBRE DOCUMENTOS
                        if($value["upld_venced"] >= 0 and $value["upld_rechazado"] >= 0 and $value["upld_docaprob"] >= 0){
                            $totalDocuementosEmpresa +=1; 
                        }if ($value["upld_rechazado"] == 1) {
                            $cantidadRechazados +=1; 
                        }elseif (($value["upld_docaprob"] == 1 or $fecha_actual <= $fechaUpdate) and $fecha_actual < $fecha2){
                            $cantidadAprobados +=1; 
                        }elseif (($value["upld_venced"]== 1  or $fecha_actual > $fecha2)and $value["upld_rechazado"] == 0 and $fecha2!= 0){
                            $cantidadVencidos +=1; 
                        }elseif ($value["id"] != "" and $value["upld_docaprob"] == 0 and $value["upld_docaprob_uid"] == 0){
                            $cantidadPorRevision +=1; 
                        }

                    }

                    $totalDoc = $totalDoc + $totalDocuementosEmpresa; 
                    $totalDocRechazados = $totalDocRechazados + $cantidadRechazados;
                    $totalDocAprobados = $cantidadAprobados + $totalDocAprobados; 
                    $totalDocVencidos = $totalDocVencidos + $cantidadVencidos;
                    $totalDocRevision = $totalDocRevision + $cantidadPorRevision; 

                    $documentosTrabajadores = EstadoDocumento::where('upld_sso_id',$folio["id"])->where('upld_status', '1')->where('upld_type', '1')->whereBetween('upld_crtdat', array($fechaEneroIn,  $diaActual))->get();

                    foreach ($documentosTrabajadores as $value) {
                        ////////////////// fecha para determinar si esta expirado /////
                        $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
                        $fecha2 = $value["upld_vence_date"];
                        $fechaUpdate = $value["upld_upddat"];
                        
                        $datosTrabajadores = trabajadorSSO::where('worker_status','1')->where('desvinculado','0')->where('id',$value["upld_workerid"])->where('sso_id',$value["upld_sso_id"])->get();
                        foreach ($datosTrabajadores as $trabajador) {

                            if($value["upld_venced"] >= 0 and $value["upld_rechazado"] >= 0 and $value["upld_docaprob"] >= 0){
                                $totalDocuementosEmpresaTrabajador +=1; 
                            }if($value["upld_rechazado"] == 1){
                                $cantidadRechazadosTrabajador +=1; 
                            }elseif (($value["upld_docaprob"] == 1 or $fecha_actual <= $fechaUpdate) and $fecha_actual < $fecha2){
                                $cantidadAprobadosTrabajador +=1; 
                            }elseif (($value["upld_venced"]== 1  or $fecha_actual > $fecha2)and $value["upld_rechazado"] == 0 and $fecha2!= 0){
                                $cantidadVencidosTrabajador +=1; 
                            }elseif ($value["id"] != "" and $value["upld_docaprob"] == 0 and $value["upld_docaprob_uid"] == 0){
                                $cantidadPorRevisionTrabajador +=1; 
                            }

                        }
                    }

                    $totalDocTrabajadores = $totalDocTrabajadores + $totalDocuementosEmpresaTrabajador; 
                    $totalDocRechazadosTrabajadores = $totalDocRechazadosTrabajadores + $cantidadRechazadosTrabajador;
                    $totalDocAprobadosTrabajadores = $totalDocAprobadosTrabajadores + $cantidadAprobadosTrabajador; 
                    $totalDocVencidosTrabajadores = $totalDocVencidosTrabajadores + $cantidadVencidosTrabajador;
                    $totalDocRevisionTrabajadores = $totalDocRevisionTrabajadores + $cantidadPorRevisionTrabajador;
                }
                
            }
            /// usuarios empresa principal ///
            if($datosUsuarios->type ==3){

            
                $EmpresasP = empresaPrincipal::whereIn('rut',$rutprincipal)->orderBy('name', 'ASC')->get(['name','rut']);

                foreach ($EmpresasP as $value) {
                    $rutprincipal[] = $value['rut'];
                }

                 //////////////// certificacion laboral ////////////////////////////
                    $estadoIngresado = 0;
                    $estadoSolicitado = 0;
                    $estadoAprobado = 0;
                    $estadoNoAprobado = 0;
                    $estadoCertificado = 0;
                    $estadoDocumentado = 0;
                    $estadoHistorico = 0;
                    $estadoCompleto = 0;
                    $estadoProceso = 0;
                    $estadoNoConforme = 0;
                    $estadoInactivo = 0;

                    $ssograficos = 0;
                    $totalFolios =0;
                    $totalDocuementos =0;
                    $totalTrabajadores  = 0;
                    $totalEmpresasPriSSO =0;
                    $totalDocRechazados  = 0;
                    $totalDocAprobados  = 0;
                    $totalDocAprobados = 0;
                    $totalDocVencidos = 0;
                    $totalDocRevision  = 0;
                    $totalDoc =0;
                    $totalDocRechazadosTrabajadores = 0;
                    $totalDocRechazadosTrabajadores = 0;
                    $totalDocAprobadosTrabajadores  = 0;
                    $totalDocVencidosTrabajadores  = 0;
                    $totalDocRevisionTrabajadores = 0;
                    $totalDocTrabajadores = 0;
    
                    $etiquetaMes =""; 
                    $valoresMes = is_array(0);
                    $valoresTrabajador =  is_array(0);

                    $empresasContratistas = Contratista::whereIn('mainCompanyRut',$rutprincipal)->get(['certificateState','subcontratistaRut','rut','certificateDate']);
                    $cantidadEmpresas = count($empresasContratistas);

                    if(!empty($empresasContratistas)){
                        $certificacion = 1;

                        session(['certificacion' => $certificacion]);
                        $fechaHoy = getdate(); 
                        $anio =date('Y');
                        for ($i=$fechaHoy["mon"]; $i > 0; $i--) { 
                            if($i == 1){
                                $mes="Enero"; 
                                $fechaEneroInf = "2-1-".$anio;
                                $fechaEneroIn = strtotime($fechaEneroInf);
                                $fechaEnerofi = "1-2-".$anio;
                                $fechaEneroFi = strtotime($fechaEnerofi);
                                $totalContratista = Contratista::whereIn('mainCompanyRut',$rutprincipal)->where('certificateState',5)->whereBetween('certificateDate', array($fechaEneroIn,  $fechaEneroFi))->count();
                                $periodId = Periodo::where('year',$anio)->where('monthId',$i)->get(['id'])->toArray();
                                if(!empty($periodId)){
                                    $trabajodoresCertificacion = TrabajadorVerificacion::whereIn('mainCompanyRut',$rutprincipal)->where('periodId',$periodId[0]['id'])->count();
                                }else{
                                    $trabajodoresCertificacion = 0;
                                }
                           
                            }

                            if($i == 2){ 
                                $mes="Febrero";
                                $fechaEneroInf = "2-2-".$anio;
                                $fechaEneroIn = strtotime($fechaEneroInf);
                                $fechaEnerofi = "1-3-".$anio;
                                $fechaEneroFi = strtotime($fechaEnerofi);
                                $totalContratista = Contratista::whereIn('mainCompanyRut',$rutprincipal)->where('certificateState',5)->whereBetween('certificateDate', array($fechaEneroIn,  $fechaEneroFi))->count();
                                $periodId = Periodo::where('year',$anio)->where('monthId',$i)->get(['id'])->toArray();
                                if(!empty($periodId)){
                                    $trabajodoresCertificacion = TrabajadorVerificacion::whereIn('mainCompanyRut',$rutprincipal)->where('periodId',$periodId[0]['id'])->count();
                                }else{
                                    $trabajodoresCertificacion = 0;
                                }
                            }

                            if($i == 3){
                                $mes="Marzo";
                                $fechaEneroInf = "2-3-".$anio;
                                $fechaEneroIn = strtotime($fechaEneroInf);
                                $fechaEnerofi = "1-4-".$anio;
                                $fechaEneroFi = strtotime($fechaEnerofi);
                                $totalContratista = Contratista::whereIn('mainCompanyRut',$rutprincipal)->where('certificateState',5)->whereBetween('certificateDate', array($fechaEneroIn,  $fechaEneroFi))->count();
                                $periodId = Periodo::where('year',$anio)->where('monthId',$i)->get(['id'])->toArray();
                                if(!empty($periodId)){
                                    $trabajodoresCertificacion = TrabajadorVerificacion::whereIn('mainCompanyRut',$rutprincipal)->where('periodId',$periodId[0]['id'])->count();
                                }else{
                                    $trabajodoresCertificacion = 0;
                                }
                                
                            }

                            if($i == 4){
                                $mes="Abril";
                                $fechaEneroInf = "2-4-".$anio;
                                $fechaEneroIn = strtotime($fechaEneroInf);
                                $fechaEnerofi = "1-5-".$anio;
                                $fechaEneroFi = strtotime($fechaEnerofi);
                                $totalContratista = Contratista::whereIn('mainCompanyRut',$rutprincipal)->where('certificateState',5)->whereBetween('certificateDate', array($fechaEneroIn,  $fechaEneroFi))->count();
                                $periodId = Periodo::where('year',$anio)->where('monthId',$i)->get(['id'])->toArray();
                                if(!empty($periodId)){
                                    $trabajodoresCertificacion = TrabajadorVerificacion::whereIn('mainCompanyRut',$rutprincipal)->where('periodId',$periodId[0]['id'])->count();
                                }else{
                                    $trabajodoresCertificacion = 0;
                                }
                                
                            }

                            if($i == 5){
                                $mes="Mayo";
                                $fechaEneroInf = "2-5-".$anio;
                                $fechaEneroIn = strtotime($fechaEneroInf);
                                $fechaEnerofi = "1-6-".$anio;
                                $fechaEneroFi = strtotime($fechaEnerofi);
                                $totalContratista = Contratista::whereIn('mainCompanyRut',$rutprincipal)->where('certificateState',5)->whereBetween('certificateDate', array($fechaEneroIn,  $fechaEneroFi))->count();
                                $periodId = Periodo::where('year',$anio)->where('monthId',$i)->get(['id'])->toArray();
                                if(!empty($periodId)){
                                    $trabajodoresCertificacion = TrabajadorVerificacion::whereIn('mainCompanyRut',$rutprincipal)->where('periodId',$periodId[0]['id'])->count();
                                }else{
                                    $trabajodoresCertificacion = 0;
                                }
                                
                            }

                            if($i == 6){
                                $mes="Junio";
                                $fechaEneroInf = "2-6-".$anio;
                                $fechaEneroIn = strtotime($fechaEneroInf);
                                $fechaEnerofi = "1-7-".$anio;
                                $fechaEneroFi = strtotime($fechaEnerofi);
                                $totalContratista = Contratista::whereIn('mainCompanyRut',$rutprincipal)->where('certificateState',5)->whereBetween('certificateDate', array($fechaEneroIn,  $fechaEneroFi))->count();
                                $periodId = Periodo::where('year',$anio)->where('monthId',$i)->get(['id'])->toArray();
                               if(!empty($periodId)){
                                    $trabajodoresCertificacion = TrabajadorVerificacion::whereIn('mainCompanyRut',$rutprincipal)->where('periodId',$periodId[0]['id'])->count();
                                }else{
                                    $trabajodoresCertificacion = 0;
                                }
                                
                            }

                            if($i == 7){
                                $mes="Julio";
                                $fechaEneroInf = "2-7-".$anio;
                                $fechaEneroIn = strtotime($fechaEneroInf);
                                $fechaEnerofi = "1-8-".$anio;
                                $fechaEneroFi = strtotime($fechaEnerofi);
                                $totalContratista = Contratista::whereIn('mainCompanyRut',$rutprincipal)->where('certificateState',5)->whereBetween('certificateDate', array($fechaEneroIn,  $fechaEneroFi))->count();
                                $periodId = Periodo::where('year',$anio)->where('monthId',$i)->get(['id'])->toArray();
                                if(!empty($periodId)){
                                    $trabajodoresCertificacion = TrabajadorVerificacion::whereIn('mainCompanyRut',$rutprincipal)->where('periodId',$periodId[0]['id'])->count();
                                }else{
                                    $trabajodoresCertificacion = 0;
                                }
                                
                            }

                            if($i == 8){
                                $mes="Agosto";
                                $fechaEneroInf = "2-8-".$anio;
                                $fechaEneroIn = strtotime($fechaEneroInf);
                                $fechaEnerofi = "1-9-".$anio;
                                $fechaEneroFi = strtotime($fechaEnerofi);
                                $totalContratista = Contratista::whereIn('mainCompanyRut',$rutprincipal)->where('certificateState',5)->whereBetween('certificateDate', array($fechaEneroIn,  $fechaEneroFi))->count();
                                $periodId = Periodo::where('year',$anio)->where('monthId',$i)->get(['id'])->toArray();
                                if(!empty($periodId)){
                                    $trabajodoresCertificacion = TrabajadorVerificacion::whereIn('mainCompanyRut',$rutprincipal)->where('periodId',$periodId[0]['id'])->count();
                                }else{
                                    $trabajodoresCertificacion = 0;
                                }
                                
                            }

                            if($i == 9){
                                $mes="Septiembre";
                                $fechaEneroInf = "2-9-".$anio;
                                $fechaEneroIn = strtotime($fechaEneroInf);
                                $fechaEnerofi = "1-10-".$anio;
                                $fechaEneroFi = strtotime($fechaEnerofi);
                                $totalContratista = Contratista::whereIn('mainCompanyRut',$rutprincipal)->where('certificateState',5)->whereBetween('certificateDate', array($fechaEneroIn,  $fechaEneroFi))->count();
                                $periodId = Periodo::where('year',$anio)->where('monthId',$i)->get(['id'])->toArray();
                                if(!empty($periodId)){
                                    $trabajodoresCertificacion = TrabajadorVerificacion::whereIn('mainCompanyRut',$rutprincipal)->where('periodId',$periodId[0]['id'])->count();
                                }else{
                                    $trabajodoresCertificacion = 0;
                                }
                               
                            }

                            if($i == 10){
                                $mes="Octubre";
                                $fechaEneroInf = "2-10-".$anio;
                                $fechaEneroIn = strtotime($fechaEneroInf);
                                $fechaEnerofi = "1-11-".$anio;
                                $fechaEneroFi = strtotime($fechaEnerofi);
                                $totalContratista = Contratista::whereIn('mainCompanyRut',$rutprincipal)->where('certificateState',5)->whereBetween('certificateDate', array($fechaEneroIn,  $fechaEneroFi))->count();
                                $periodId = Periodo::where('year',$anio)->where('monthId',$i)->get(['id'])->toArray();
                                if(!empty($periodId)){
                                    $trabajodoresCertificacion = TrabajadorVerificacion::whereIn('mainCompanyRut',$rutprincipal)->where('periodId',$periodId[0]['id'])->count();
                                }else{
                                    $trabajodoresCertificacion = 0;
                                }
                            }

                            if($i == 11){
                                $mes="Noviembre";
                                $fechaEneroInf = "2-11-".$anio;
                                $fechaEneroIn = strtotime($fechaEneroInf);
                                $fechaEnerofi = "1-12-".$anio;
                                $fechaEneroFi = strtotime($fechaEnerofi);
                                $totalContratista = Contratista::whereIn('mainCompanyRut',$rutprincipal)->where('certificateState',5)->whereBetween('certificateDate', array($fechaEneroIn,  $fechaEneroFi))->count();
                                $periodId = Periodo::where('year',$anio)->where('monthId',$i)->get(['id'])->toArray();
                                if(!empty($periodId)){
                                    $trabajodoresCertificacion = TrabajadorVerificacion::whereIn('mainCompanyRut',$rutprincipal)->where('periodId',$periodId[0]['id'])->count();
                                }else{
                                    $trabajodoresCertificacion = 0;
                                }
                                
                            }

                            if($i == 12){
                                $mes="Diciembre";
                                $fechaEneroInf = "2-12-".$anio;
                                $fechaEneroIn = strtotime($fechaEneroInf);
                                $aniN= $anio+1;
                                $fechaEnerofi = "1-01-".$aniN;
                                $fechaEneroFi = strtotime($fechaEnerofi);
                                $totalContratista = Contratista::whereIn('mainCompanyRut',$rutprincipal)->where('certificateState',5)->
                                whereBetween('certificateDate', array($fechaEneroIn,  $fechaEneroFi))->count();
                                $periodId = Periodo::where('year',$anio)->where('monthId',$i)->get(['id'])->toArray();
                                if(!empty($periodId)){
                                    $trabajodoresCertificacion = TrabajadorVerificacion::whereIn('mainCompanyRut',$rutprincipal)->where('periodId',$periodId[0]['id'])->count();
                                }else{
                                    $trabajodoresCertificacion = 0;
                                }
                                
                            }
                            
                            $etiquetaMesCertificacion[] = $mes;
                            $valoresMesCerticacion[]  =  $totalContratista;
                            $valoresTrabajadoresCerticacion[]  =  $trabajodoresCertificacion;
                            
                        }

                    
                        $graficaCertificacion = 1;
                        foreach ($empresasContratistas as  $value) {
                            $contratista['rut'] = $value['rut'];
                            $totalContrasitas[] = $contratista;
                            if($value['subcontratistaRut']!=''){

                                $subContratistas['rut'] = $value['subcontratistaRut'];
                                $totalSubContrasitas[] = $subContratistas;

                            }
                         
                            switch ($value['certificateState']) {
                                case 1:
                                $estadoIngresado +=1; 
                                break;
                                case 2:
                                $estadoSolicitado +=1;
                                break;
                                case 3:
                                $estadoAprobado +=1;
                                break;
                                case 4:
                                $estadoNoAprobado +=1;
                                break;
                                case 5:
                                $estadoCertificado +=1;
                                break;
                                case 6:
                                $estadoDocumentado +=1;
                                break;
                                case 7:
                                $estadoHistorico +=1;
                                break;
                                case 8:
                                $estadoCompleto +=1;
                                break;
                                case 9:
                                $estadoProceso +=1;
                                break;
                                case 10:
                                $estadoNoConforme +=1;
                                break;
                                case 11:
                                $estadoInactivo +=1;
                            }
                        } 

                        $datoContratistas = super_unique($totalContrasitas,'rut');
                        $cantidadContratistas = count($datoContratistas);

                        if(!empty($totalSubContrasitas)){
                            $datoSubContratistas = super_unique($totalSubContrasitas,'rut');
                            $cantidadSubContratistas = count($datoSubContratistas);

                        }else{
                            $cantidadSubContratistas = 0;
                        }
                        
                    }else{
                        $certificacion = 0;
                        session(['certificacion' => $certificacion]);
                        $graficaCertificacion = 0;
                    }

                $folios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status', '1')->get();
              
                    foreach ($folios as $value) {
                        $foliosActivos[] = $value['id']; 
                    }

             
                if(!empty($foliosActivos)){
                    $ssograficos = 1;
                    $certificacion = 0;
                    session(['certificacion' => $certificacion]);
                    $fechaHoy = getdate(); 
                    $etiquetaMes =array();
                    $bMeses = array("void","Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
  
                    $anio =date('Y');
                    for ($i=$fechaHoy["mon"]; $i > 0; $i--) { 
                        if($i == 1){
                        $mes="Enero"; 
                        $fechaEneroInf = "2-1-".$anio;
                        $fechaEneroIn = strtotime($fechaEneroInf);
                        $fechaEnerofi = "1-2-".$anio;
                        $fechaEneroFi = strtotime($fechaEnerofi);
                        $totalFolio = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status','1') ->whereBetween('sso_crtdat', array($fechaEneroIn,  $fechaEneroFi))->count();
                        $totalTrabajadoresM = trabajadorSSO::whereIn('sso_id',$foliosActivos)->where('worker_status','1')->where('desvinculado','0')->whereBetween('worker_crtdat', array($fechaEneroIn,  $fechaEneroFi))->distinct('worker_rut')->count('worker_rut');
                        
                        }
                        if($i == 2){
                        $fechaEneroInf = "2-2-".$anio;
                        $fechaEneroIn = strtotime($fechaEneroInf);
                        $fechaEnerofi = "1-3-".$anio;
                        $fechaEneroFi = strtotime($fechaEnerofi);
                        $totalFolio = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status','1') ->whereBetween('sso_crtdat', array($fechaEneroIn,  $fechaEneroFi))->count();
                        $totalTrabajadoresM = trabajadorSSO::whereIn('sso_id',$foliosActivos)->where('worker_status','1')->where('desvinculado','0')->whereBetween('worker_crtdat', array($fechaEneroIn,  $fechaEneroFi))->distinct('worker_rut')->count('worker_rut');
                        $mes="Febrero";
                        }
                        if($i == 3){
                            $mes="Marzo";
                            $fechaEneroInf = "2-3-".$anio;
                            $fechaEneroIn = strtotime($fechaEneroInf);
                            $fechaEnerofi = "1-4-".$anio;
                            $fechaEneroFi = strtotime($fechaEnerofi);
                            $totalFolio = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status','1') ->whereBetween('sso_crtdat', array($fechaEneroIn,  $fechaEneroFi))->count();
                            $totalTrabajadoresM = trabajadorSSO::whereIn('sso_id',$foliosActivos)->where('worker_status','1')->where('desvinculado','0')->whereBetween('worker_crtdat', array($fechaEneroIn,  $fechaEneroFi))->distinct('worker_rut')->count('worker_rut');
                           
                        }
                        if($i == 4){
                            $mes="Abril";
                            $fechaEneroInf = "2-4-".$anio;
                            $fechaEneroIn = strtotime($fechaEneroInf);
                            $fechaEnerofi = "1-5-".$anio;
                            $fechaEneroFi = strtotime($fechaEnerofi);
                            $totalFolio = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status','1')->whereBetween('sso_crtdat', array($fechaEneroIn,  $fechaEneroFi))->count();
                            $totalTrabajadoresM = trabajadorSSO::whereIn('sso_id',$foliosActivos)->where('worker_status','1')->where('desvinculado','0')->whereBetween('worker_crtdat', array($fechaEneroIn,  $fechaEneroFi))->distinct('worker_rut')->count('worker_rut');
                        }
                        if($i == 5){
                            $mes="Mayo";
                            $fechaEneroInf = "2-5-".$anio;
                            $fechaEneroIn = strtotime($fechaEneroInf);
                            $fechaEnerofi = "1-6-".$anio;
                            $fechaEneroFi = strtotime($fechaEnerofi);
                            $totalFolio = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status','1') ->whereBetween('sso_crtdat', array($fechaEneroIn,  $fechaEneroFi))->count();
                            $totalTrabajadoresM = trabajadorSSO::whereIn('sso_id',$foliosActivos)->where('worker_status','1')->where('desvinculado','0')->where('desvinculado','0')->whereBetween('worker_crtdat', array($fechaEneroIn,  $fechaEneroFi))->distinct('worker_rut')->count('worker_rut');
                        }
                        if($i == 6){
                            $mes="Junio";
                            $fechaEneroInf = "2-6-".$anio;
                            $fechaEneroIn = strtotime($fechaEneroInf);
                            $fechaEnerofi = "1-7-".$anio;
                            $fechaEneroFi = strtotime($fechaEnerofi);
                            $totalFolio = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status','1') ->whereBetween('sso_crtdat', array($fechaEneroIn,  $fechaEneroFi))->count();
                            $totalTrabajadoresM = trabajadorSSO::whereIn('sso_id',$foliosActivos)->where('worker_status','1')->where('desvinculado','0')->whereBetween('worker_crtdat', array($fechaEneroIn,  $fechaEneroFi))->distinct('worker_rut')->count('worker_rut');
                        }
                        if($i == 7){
                            $mes="Julio";
                            $fechaEneroInf = "2-7-".$anio;
                            $fechaEneroIn = strtotime($fechaEneroInf);
                            $fechaEnerofi = "1-8-".$anio;
                            $fechaEneroFi = strtotime($fechaEnerofi);
                            $totalFolio = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status','1') ->whereBetween('sso_crtdat', array($fechaEneroIn,  $fechaEneroFi))->count();
                            $totalTrabajadoresM = trabajadorSSO::whereIn('sso_id',$foliosActivos)->where('worker_status','1')->where('desvinculado','0')->whereBetween('worker_crtdat', array($fechaEneroIn,  $fechaEneroFi))->distinct('worker_rut')->count('worker_rut');
                        }
                        if($i == 8){
                            $mes="Agosto";
                            $fechaEneroInf = "2-8-".$anio;
                            $fechaEneroIn = strtotime($fechaEneroInf);
                            $fechaEnerofi = "1-9-".$anio;
                            $fechaEneroFi = strtotime($fechaEnerofi);
                            $totalFolio = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status','1') ->whereBetween('sso_crtdat', array($fechaEneroIn,  $fechaEneroFi))->count();
                            $totalTrabajadoresM = trabajadorSSO::whereIn('sso_id',$foliosActivos)->where('worker_status','1')->where('desvinculado','0')->whereBetween('worker_crtdat', array($fechaEneroIn,  $fechaEneroFi))->distinct('worker_rut')->count('worker_rut');
                        }
                        if($i == 9){
                            $mes="Septiembre";
                            $fechaEneroInf = "2-9-".$anio;
                            $fechaEneroIn = strtotime($fechaEneroInf);
                            $fechaEnerofi = "1-10-".$anio;
                            $fechaEneroFi = strtotime($fechaEnerofi);
                            $totalFolio = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status','1') ->whereBetween('sso_crtdat', array($fechaEneroIn,  $fechaEneroFi))->count();
                            $totalTrabajadoresM = trabajadorSSO::whereIn('sso_id',$foliosActivos)->where('worker_status','1')->where('desvinculado','0')->whereBetween('worker_crtdat', array($fechaEneroIn,  $fechaEneroFi))->distinct('worker_rut')->count('worker_rut');
                        }
                        if($i == 10){
                            $mes="Octubre";
                            $fechaEneroInf = "2-10-".$anio;
                            $fechaEneroIn = strtotime($fechaEneroInf);
                            $fechaEnerofi = "1-11-".$anio;
                            $fechaEneroFi = strtotime($fechaEnerofi);
                            $totalFolio = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status','1') ->whereBetween('sso_crtdat', array($fechaEneroIn,  $fechaEneroFi))->count();
                            $totalTrabajadoresM = trabajadorSSO::whereIn('sso_id',$foliosActivos)->where('worker_status','1')->where('desvinculado','0')->whereBetween('worker_crtdat', array($fechaEneroIn,  $fechaEneroFi))->distinct('worker_rut')->count('worker_rut');
                        }
                        if($i == 11){
                            $mes="Noviembre";
                            $fechaEneroInf = "2-11-".$anio;
                            $fechaEneroIn = strtotime($fechaEneroInf);
                            $fechaEnerofi = "1-12-".$anio;
                            $fechaEneroFi = strtotime($fechaEnerofi);
                            $totalFolio = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status','1') ->whereBetween('sso_crtdat', array($fechaEneroIn,  $fechaEneroFi))->count();
                            $totalTrabajadoresM = trabajadorSSO::whereIn('sso_id',$foliosActivos)->where('worker_status','1')->where('desvinculado','0')->whereBetween('worker_crtdat', array($fechaEneroIn,  $fechaEneroFi))->distinct('worker_rut')->count('worker_rut');
                        }
                        if($i == 12){
                            $mes="Diciembre";
                            $fechaEneroInf = "2-12-".$anio;
                            $fechaEneroIn = strtotime($fechaEneroInf);
                            $aniN= $anio+1;
                            $fechaEnerofi = "1-01-".$aniN;
                            $fechaEneroFi = strtotime($fechaEnerofi);
                            $totalFolio = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status','1') ->whereBetween('sso_crtdat', array($fechaEneroIn,  $fechaEneroFi))->count();
                            $totalTrabajadoresM = trabajadorSSO::whereIn('sso_id',$foliosActivos)->where('worker_status','1')->where('desvinculado','0')->whereBetween('worker_crtdat', array($fechaEneroIn,  $fechaEneroFi))->distinct('worker_rut')->count('worker_rut');
                        }
                        
                        $etiquetaMes[] = $mes;
                        $valoresMes[] = $totalFolio;
                        $valoresTrabajador[] = $totalTrabajadoresM;
                    }

                    $totalFolios =count($foliosActivos);
                    $totalFolios = number_format($totalFolios);
                    $totalDocuementos = EstadoDocumento::whereIn('upld_sso_id',$foliosActivos)->where('upld_status','1')->count();
                    $totalDocuementos = number_format($totalDocuementos);
                    $totalTrabajadores = trabajadorSSO::whereIn('sso_id',$foliosActivos)->where('worker_status','1')->where('desvinculado','0')->distinct('worker_rut')->count('worker_rut');
                    $totalTrabajadores = number_format($totalTrabajadores);
                    $totalEmpresasPriSSO =  FolioSso::distinct('sso_mcomp_rut')->whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status','1')->get(['sso_mcomp_rut']);
                    $totalEmpresasPriSSO = count($totalEmpresasPriSSO);

                    $i = $fechaHoy["mon"];
                    if($i == 1){
                        $mes="Enero";
                        $fechaEneroInf = "2-1-".$anio;
                        $fechaEneroIn = strtotime($fechaEneroInf);
                        $fechaEnerofi = "1-2-".$anio;
                        $fechaEneroFi = strtotime($fechaEnerofi); 
                    }
                    if($i == 2){
                        $mes="Febrero";
                        $fechaEneroInf = "2-2-".$anio;
                        $fechaEneroIn = strtotime($fechaEneroInf);
                        $fechaEnerofi = "1-3-".$anio;
                        $fechaEneroFi = strtotime($fechaEnerofi);
                    }
                    if($i == 3){
                        $mes="Marzo";
                        $fechaEneroInf = "2-3-".$anio;
                        $fechaEneroIn = strtotime($fechaEneroInf);
                        $fechaEnerofi = "1-4-".$anio;
                        $fechaEneroFi = strtotime($fechaEnerofi);
                    }
                    if($i == 4){
                        $mes="Abril";
                        $fechaEneroInf = "2-4-".$anio;
                        $fechaEneroIn = strtotime($fechaEneroInf);
                        $fechaEnerofi = "1-5-".$anio;
                        $fechaEneroFi = strtotime($fechaEnerofi);
                    }
                    if($i == 5){
                        $mes="Mayo";
                        $fechaEneroInf = "2-5-".$anio;
                        $fechaEneroIn = strtotime($fechaEneroInf);
                        $fechaEnerofi = "1-6-".$anio;
                        $fechaEneroFi = strtotime($fechaEnerofi);
                    }
                    if($i == 6){
                        $mes="Junio";
                        $fechaEneroInf = "2-6-".$anio;
                        $fechaEneroIn = strtotime($fechaEneroInf);
                        $fechaEnerofi = "1-7-".$anio;
                        $fechaEneroFi = strtotime($fechaEnerofi);
                    }
                    if($i== 7){
                        $mes="Julio";
                        $fechaEneroInf = "2-7-".$anio;
                        $fechaEneroIn = strtotime($fechaEneroInf);
                        $fechaEnerofi = "1-8-".$anio;
                        $fechaEneroFi = strtotime($fechaEnerofi);
                    }
                    if($i == 8){
                        $mes="Agosto";
                        $fechaEneroInf = "2-8-".$anio;
                        $fechaEneroIn = strtotime($fechaEneroInf);
                        $fechaEnerofi = "1-9-".$anio;
                        $fechaEneroFi = strtotime($fechaEnerofi);
                    }
                    if($i == 9){
                        $mes="Septiembre";
                        $fechaEneroInf = "2-9-".$anio;
                        $fechaEneroIn = strtotime($fechaEneroInf);
                        $fechaEnerofi = "1-10-".$anio;
                        $fechaEneroFi = strtotime($fechaEnerofi);
                    }
                    if($i == 10){
                        $mes="Octubre";
                        $fechaEneroInf = "2-10-".$anio;
                        $fechaEneroIn = strtotime($fechaEneroInf);
                        $fechaEnerofi = "1-11-".$anio;
                        $fechaEneroFi = strtotime($fechaEnerofi);
                    }
                    if($i == 11){
                        $mes="Noviembre";
                        $fechaEneroInf = "2-11-".$anio;
                        $fechaEneroIn = strtotime($fechaEneroInf);
                        $fechaEnerofi = "1-12-".$anio;
                        $fechaEneroFi = strtotime($fechaEnerofi);
                    }
                    if($i == 12){
                        $mes="Diciembre";
                        $fechaEneroInf = "2-12-".$anio;
                        $fechaEneroIn = strtotime($fechaEneroInf);
                        $aniN= $anio+1;
                        $fechaEnerofi = "1-01-".$aniN;
                        $fechaEneroFi = strtotime($fechaEnerofi);
                    }
 
                    $diaActual = strtotime("now");
                    $hoyVista = date("d-m-Y");  
                    $foliosActivos = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status','1')->whereBetween('sso_crtdat', array($fechaEneroIn,  $diaActual))->get();

                    $cantidadAprobados = 0;
                    $cantidadRechazados = 0;
                    $cantidadVencidos = 0;
                    $cantidadPorRevision = 0;
                    $totalDoc = 0;
                    $totalDocTrabajadores = 0;
                    $totalDocRechazadosTrabajadores = 0;
                    $totalDocAprobadosTrabajadores = 0;
                    $totalDocVencidosTrabajadores = 0;
                    $totalDocRevisionTrabajadores = 0;
                    $totalDocuementosEmpresaTrabajador = 0;
                    $cantidadRechazadosTrabajador = 0;
                    $cantidadAprobadosTrabajador = 0;
                    $cantidadVencidosTrabajador = 0;
                    $cantidadPorRevisionTrabajador = 0;
                    foreach ($foliosActivos as $folio) {
                        $cantidadDocGlo = 0;
                        $documentosGlobales = EstadoDocumento::where('upld_sso_id',$folio["id"])->where('upld_status', '1')->where('upld_type', '0')->whereBetween('upld_crtdat', array($fechaEneroIn,  $diaActual))->get();
                        $cantidadDocGlo = count($documentosGlobales);
                        $totalDocRechazados = 0;
                        $totalDocAprobados = 0;
                        $totalDocVencidos = 0;
                        $totalDocRevision = 0;
                        foreach ($documentosGlobales as $value) {
                            ////////////////// fecha para determinar si esta expirado /////
                            $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
                            $fecha2 = $value["upld_vence_date"];
                            $fechaUpdate = $value["upld_upddat"];
                            
                            if ($value["upld_rechazado"] == 1) {
                                $cantidadRechazados +=1; 
                            }elseif (($value["upld_docaprob"] == 1 or $fecha_actual <= $fechaUpdate) and $fecha_actual < $fecha2){
                                $cantidadAprobados +=1; 
                            }elseif (($value["upld_venced"]== 1  or $fecha_actual > $fecha2)and $value["upld_rechazado"] == 0 and $fecha2!= 0){
                                $cantidadVencidos +=1; 
                            }elseif ($value["id"] != "" and $value["upld_docaprob"] == 0 and $value["upld_docaprob_uid"] == 0){
                                $cantidadPorRevision +=1; 
                            }

                        }

                        $totalDoc = $totalDoc + $cantidadDocGlo; 
                        $totalDocRechazados = $totalDocRechazados + $cantidadRechazados;
                        $totalDocAprobados = $cantidadAprobados + $totalDocAprobados; 
                        $totalDocVencidos = $totalDocVencidos + $cantidadVencidos;
                        $totalDocRevision = $totalDocRevision + $cantidadPorRevision; 
                        $cantidadDocTra = 0;
                        $documentosTrabajadores = EstadoDocumento::where('upld_sso_id',$folio["id"])->where('upld_status', '1')->where('upld_type', '1')->whereBetween('upld_crtdat', array($fechaEneroIn,  $diaActual))->get();
                        $cantidadDocTra = count($documentosTrabajadores);
                       // echo $cantidadDocTra."<br>";
                        $cantidadRechazadosTrabajador = 0;
                        $cantidadAprobadosTrabajador = 0;
                        $cantidadVencidosTrabajador = 0;
                        $cantidadPorRevisionTrabajador = 0;
                        foreach ($documentosTrabajadores as $value) {
                            ////////////////// fecha para determinar si esta expirado /////
                            $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
                            $fecha2 = $value["upld_vence_date"];
                            $fechaUpdate = $value["upld_upddat"];
                    
                                if($value["upld_rechazado"] == 1){
                                    $cantidadRechazadosTrabajador +=1; 
                                }elseif (($value["upld_docaprob"] == 1 or $fecha_actual <= $fechaUpdate) and $fecha_actual < $fecha2){
                                    $cantidadAprobadosTrabajador +=1; 
                                }elseif (($value["upld_venced"]== 1  or $fecha_actual > $fecha2)and $value["upld_rechazado"] == 0 and $fecha2!= 0){
                                    $cantidadVencidosTrabajador +=1; 
                                }elseif ($value["id"] != "" and $value["upld_docaprob"] == 0 and $value["upld_docaprob_uid"] == 0){
                                    $cantidadPorRevisionTrabajador +=1; 
                                }

                            
                        }
                        $totalDocTrabajadores = $totalDocTrabajadores + $cantidadDocTra; 
                        $totalDocRechazadosTrabajadores = $totalDocRechazadosTrabajadores + $cantidadRechazadosTrabajador;
                        $totalDocAprobadosTrabajadores = $totalDocAprobadosTrabajadores + $cantidadAprobadosTrabajador; 
                        $totalDocVencidosTrabajadores = $totalDocVencidosTrabajadores + $cantidadVencidosTrabajador;
                        $totalDocRevisionTrabajadores = $totalDocRevisionTrabajadores + $cantidadPorRevisionTrabajador;
                    }

                   
                }
            }

            $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
            $UsuarioContratista = UsuarioContratista::where('systemUserId','=',$idUsuario)->get();
            $UsuarioContratista->load('usuarioDatos');

      //exit();
        return view('home.index',compact('datosUsuarios','totalFolios','totalDocuementos','totalTrabajadores','totalEmpresasPriSSO','EmpresasP','etiquetaMes','valoresMes','valoresTrabajador','anio','totalDoc','totalDocRechazados','totalDocAprobados','totalDocVencidos','totalDocRevision','mes','totalDocTrabajadores','totalDocRechazadosTrabajadores','totalDocAprobadosTrabajadores','totalDocVencidosTrabajadores','totalDocRevisionTrabajadores','aquaChile','certificacion','cantidadContratistas','cantidadSubContratistas','graficaCertificacion','etiquetaMesCertificacion','valoresMesCerticacion','ssograficos','valoresTrabajadoresCerticacion','estadoIngresado','estadoSolicitado','estadoAprobado','estadoNoAprobado','estadoCertificado','estadoDocumentado','estadoHistorico','estadoCompleto','estadoProceso','estadoNoConforme','estadoInactivo','usuarioAqua','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile'));  
        
        //return view('home.index',compact('datosUsuarios'));
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
        //
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

    public function ssoTrabajadores($id)
    {
        $idUsuario = rtrim(base64_decode($id));
        if($idUsuario == ""){
            return view('sesion.index');
        }
        $usuarioAqua = session('user_aqua');
        $usuarioABBChile= session('user_ABB');
        $usuarioClaroChile= session('user_Claro');
        $usuarioNOKactivo = session('usuario_nok');
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
        $datosUsuarios->load('cargaUsuarioContratista');
        $certificacion = session('certificacion');
        $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
        $UsuarioPrincipal->load('usuarioDatos');

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

        if($datosUsuarios->type ==3){

            foreach ($UsuarioPrincipal as $rut) {

                $rutprincipal[]=$rut['mainCompanyRut'];   
            }

            $folios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status', '1')->get(['id','sso_mcomp_rut','sso_mcomp_dv','sso_mcomp_name','sso_comp_rut','sso_comp_dv','sso_comp_name','sso_subcomp_active','sso_subcomp_rut','sso_subcomp_dv','sso_subcomp_name'])->toArray();

              
            foreach ($folios as $id) {
                
                $trabajadores = trabajadorSSO::distinct('worker_rut')->where('sso_id',$id['id'])
                ->where('worker_status','1')
                ->where('desvinculado','0')->get()->toArray();

                if(!empty($trabajadores)){

                    foreach ($trabajadores as $trabajador) {

                        $trabajadores["folio"] = $id['id'];
                        $trabajadores["rutPrincipal"] = $id['sso_mcomp_rut']."-".$id['sso_mcomp_dv'];
                        $trabajadores["nombrePrincipal"] =  ucwords(mb_strtolower($id['sso_mcomp_name'],'UTF-8'));
                        $trabajadores["rutContratista"] = $id['sso_comp_rut']."-".$id['sso_comp_dv'];
                        $trabajadores["nombreContratista"] =  ucwords(mb_strtolower($id['sso_comp_name'],'UTF-8'));
                        if($id['sso_subcomp_active']== 1){

                        $trabajadores["rutSubContra"] = $id['sso_subcomp_rut']."-".$id['sso_subcomp_dv'];
                        $trabajadores["nombreSubContra"] =  ucwords(mb_strtolower($id['sso_subcomp_name'],'UTF-8'));

                        }else{
                        $trabajadores["rutSubContra"] = "";
                        $trabajadores["nombreSubContra"] = "";   
                        }
                        $trabajadores["nombreTrabajador"] =  ucwords(mb_strtolower($trabajador['worker_name1'],'UTF-8'));
                        $trabajadores["apellidoTrabajador"] =  ucwords(mb_strtolower($trabajador['worker_name2']." ".$trabajador['worker_name3'],'UTF-8'));
                        $trabajadores["rutTrabajador"] = $trabajador['worker_rut'];
                        if($trabajador["worker_syscargoname"] == ''){
                            $cargoTrabajador = CargoSSO::distinct('cargo_name')->where('id',$trabajador['worker_cargoid'])->get(['cargo_name'])->toArray();
                            $trabajadores["cargo"] = ucwords(mb_strtolower($cargoTrabajador[0]['cargo_name']));
                        }else{
                             $trabajadores["cargo"] = ucwords(mb_strtolower($trabajador['worker_syscargoname']));
                        }
                        $WORK2[] = $trabajadores;
                    }
                }
            }
            $WORK = super_unique($WORK2,'rutTrabajador');
            return view('home.ssoTrabajadores',compact('datosUsuarios','WORK','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile')); 

        }

        if($datosUsuarios->type ==2){

            $folios = FolioSso::where('sso_status', '1')->get(['id','sso_mcomp_rut','sso_mcomp_dv','sso_mcomp_name','sso_comp_rut','sso_comp_dv','sso_comp_name','sso_subcomp_active','sso_subcomp_rut','sso_subcomp_dv','sso_subcomp_name'])->toArray();

            foreach ($folios as $id) {

                $trabajadores = trabajadorSSO::distinct('worker_rut')->where('sso_id',$id['id'])->where('worker_status','1')->get(['worker_name','worker_name1','worker_name2','worker_name3','worker_rut','worker_syscargoname','worker_cargoid'])->toArray();

                if(!empty($trabajadores)){

                    foreach ($trabajadores as $trabajador) {

                        $trabajadores["folio"] = $id['id'];
                        $trabajadores["rutPrincipal"] = $id['sso_mcomp_rut']."-".$id['sso_mcomp_dv'];
                        $trabajadores["nombrePrincipal"] =  ucwords(mb_strtolower($id['sso_mcomp_name'],'UTF-8'));
                        $trabajadores["rutContratista"] = $id['sso_comp_rut']."-".$id['sso_comp_dv'];
                        $trabajadores["nombreContratista"] =  ucwords(mb_strtolower($id['sso_comp_name'],'UTF-8'));
                        if($id['sso_subcomp_active']== 1){

                        $trabajadores["rutSubContra"] = $id['sso_subcomp_rut']."-".$id['sso_subcomp_dv'];
                        $trabajadores["nombreSubContra"] =  ucwords(mb_strtolower($id['sso_subcomp_name'],'UTF-8'));

                        }else{
                        $trabajadores["rutSubContra"] = "";
                        $trabajadores["nombreSubContra"] = "";   
                        }
                        $trabajadores["nombreTrabajador"] =  ucwords(mb_strtolower($trabajador['worker_name1'],'UTF-8'));
                        $trabajadores["apellidoTrabajador"] =  ucwords(mb_strtolower($trabajador['worker_name2']." ".$trabajador['worker_name3'],'UTF-8'));
                        $trabajadores["rutTrabajador"] = $trabajador['worker_rut'];
                        if($trabajador["worker_syscargoname"] == ''){
                            $cargoTrabajador = CargoSSO::distinct('cargo_name')->where('id',$trabajador['worker_cargoid'])->get(['cargo_name'])->toArray();
                            
                            $trabajadores["cargo"] = ucwords(mb_strtolower($cargoTrabajador[0]['cargo_name']));
                        }else{
                             $trabajadores["cargo"] = ucwords(mb_strtolower($trabajador['worker_syscargoname']));
                        }
                        $WORK[] = $trabajadores;
                    }
                }
            }


             return view('home.ssoTrabajadores',compact('datosUsuarios','WORK','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile')); 
        
            
        }

    }

    public function ssoFolios($id)
    {
        $idUsuario = rtrim(base64_decode($id));
        if($idUsuario == ""){
            return view('sesion.index');
        }
        $usuarioAqua = session('user_aqua');
        $usuarioABBChile= session('user_ABB');
        $usuarioClaroChile= session('user_Claro');
        $usuarioNOKactivo = session('usuario_nok');
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
        $datosUsuarios->load('cargaUsuarioContratista');
        $certificacion = session('certificacion');
        $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
        $UsuarioPrincipal->load('usuarioDatos');

        if($datosUsuarios->type ==3){

            foreach ($UsuarioPrincipal as $rut) {

                $rutprincipal[]=$rut['mainCompanyRut'];   
            }

            $folios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status', '1')->get(['id','sso_mcomp_rut','sso_mcomp_dv','sso_mcomp_name','sso_comp_rut','sso_comp_dv','sso_comp_name','sso_subcomp_active','sso_subcomp_rut','sso_subcomp_dv','sso_subcomp_name','sso_project','sso_correo'])->toArray();

              
            foreach ($folios as $id) {
                
                if(!empty($id)){

                    $folio["folio"] = $id['id'];
                    $folio["rutPrincipal"] = $id['sso_mcomp_rut']."-".$id['sso_mcomp_dv'];
                    $folio["nombrePrincipal"] =  ucwords(mb_strtolower($id['sso_mcomp_name'],'UTF-8'));
                    $folio["rutContratista"] = $id['sso_comp_rut']."-".$id['sso_comp_dv'];
                    $folio["nombreContratista"] =  ucwords(mb_strtolower($id['sso_comp_name'],'UTF-8'));
                    if($id['sso_subcomp_active']== 1){

                    $folio["rutSubContra"] = $id['sso_subcomp_rut']."-".$id['sso_subcomp_dv'];
                    $folio["nombreSubContra"] =  ucwords(mb_strtolower($id['sso_subcomp_name'],'UTF-8'));

                    }else{
                    $folio["rutSubContra"] = "";
                    $folio["nombreSubContra"] = "";   
                    }
                    $folio["proyecto"] =  ucwords(mb_strtolower($id['sso_project'],'UTF-8'));
                    $folio["correo"] =  ucwords(mb_strtolower($id['sso_correo'],'UTF-8'));
                    $FOLIOS[] = $folio;
                    
                }
            }
            return view('home.ssoFolios',compact('datosUsuarios','FOLIOS','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile')); 
        }

        if($datosUsuarios->type ==2){

            $folios = FolioSso::where('sso_status', '1')->get(['id','sso_mcomp_rut','sso_mcomp_dv','sso_mcomp_name','sso_comp_rut','sso_comp_dv','sso_comp_name','sso_subcomp_active','sso_subcomp_rut','sso_subcomp_dv','sso_subcomp_name','sso_project','sso_correo'])->toArray();

            foreach ($folios as $id) {
                
                if(!empty($id)){

                    $folio["folio"] = $id['id'];
                    $folio["rutPrincipal"] = $id['sso_mcomp_rut']."-".$id['sso_mcomp_dv'];
                    $folio["nombrePrincipal"] =  ucwords(mb_strtolower($id['sso_mcomp_name'],'UTF-8'));
                    $folio["rutContratista"] = $id['sso_comp_rut']."-".$id['sso_comp_dv'];
                    $folio["nombreContratista"] =  ucwords(mb_strtolower($id['sso_comp_name'],'UTF-8'));
                    if($id['sso_subcomp_active']== 1){

                    $folio["rutSubContra"] = $id['sso_subcomp_rut']."-".$id['sso_subcomp_dv'];
                    $folio["nombreSubContra"] =  ucwords(mb_strtolower($id['sso_subcomp_name'],'UTF-8'));

                    }else{
                    $folio["rutSubContra"] = "";
                    $folio["nombreSubContra"] = "";   
                    }
                    $folio["proyecto"] =  ucwords(mb_strtolower($id['sso_project'],'UTF-8'));
                    $folio["correo"] =  ucwords(mb_strtolower($id['sso_correo'],'UTF-8'));
                    $FOLIOS[] = $folio;
                    
                }
            }
            return view('home.ssoFolios',compact('datosUsuarios','FOLIOS','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile')); 
        }
    }

    public function ssoDocumentos($id)
    {
        $idUsuario = rtrim(base64_decode($id));
        if($idUsuario == ""){
            return view('sesion.index');
        }
        $usuarioAqua = session('user_aqua');
        $usuarioABBChile= session('user_ABB');
        $usuarioClaroChile= session('user_Claro');
        $usuarioNOKactivo = session('usuario_nok');
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
        $datosUsuarios->load('cargaUsuarioContratista');
        $certificacion = session('certificacion');
        $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
        $UsuarioPrincipal->load('usuarioDatos');

        if($datosUsuarios->type ==3){

            foreach ($UsuarioPrincipal as $rut) {

                $rutprincipal[]=$rut['mainCompanyRut'];   
            }

            $folios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status', '1')->get();
                
            $totalDocu = 0; 
            $totalRechazados = 0;
            $totalAprobados = 0; 
            $totalAprobadosObs = 0;
            $totalVencidos = 0;
            $totalRevision = 0;
            foreach ($folios as $folio) {
        
             /////DOCUMENTOS TOTALES GRAFICA ///////
                $cantidadDocTotales = 0;
                $documentosTotales = EstadoDocumento::where('upld_sso_id',$folio["id"])->where('upld_status', '1')->get();
                $cantidadDocTotales = count($documentosTotales);
               // echo $cantidadDocTra."<br>";
                $cantidadRechazadosTrabajadorTotal = 0;
                $cantidadAprobadosTrabajadorTotal = 0;
                $cantidadVencidosTrabajadorTotal = 0;
                $cantidadPorRevisionTrabajadorTotal = 0;
                $cantidadAprbadObsTrabajadorTotal = 0;
                foreach ($documentosTotales as $docTotal) {
                    ////////////////// fecha para determinar si esta expirado /////
                    $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
                    $fecha2T = $docTotal["upld_vence_date"];
                    $fechaUpdateT = $docTotal["upld_upddat"];

                        if($docTotal["upld_docaprob"] == 0 and $docTotal["upld_docaprob_uid"] == 0 and $docTotal["upld_rechazado"]==0 and $docTotal["upld_venced"]== 0 and $docTotal["upld_aprobComen"] == 0){
                            $cantidadPorRevisionTrabajadorTotal +=1;            
                            $estadoDocumento ="Por Revisión";
                        }
                        elseif(($docTotal["upld_docaprob"] == 1 or $fecha_actual <= $fechaUpdateT) and $fecha_actual < $fecha2T AND $docTotal["upld_aprobComen"]== 0 and $docTotal["upld_rechazado"] == 0){
                            $cantidadAprobadosTrabajadorTotal +=1;         
                            $estadoDocumento ="Aprobado";
                                
                        }elseif (($docTotal["upld_venced"]== 1  or $fecha_actual > $fecha2T)and $docTotal["upld_rechazado"] == 0 and $fecha2T!= 0){
                            $cantidadVencidosTrabajadorTotal +=1;
                             $estadoDocumento ="Vencido";
                        }elseif ($docTotal["upld_aprobComen"] == 1 and $docTotal["upld_comments"]!="" and $docTotal["upld_rechazado"] == 0){
                            $cantidadAprbadObsTrabajadorTotal +=1;           
                            $estadoDocumento ="Aprobado Obs";
                        }elseif ($docTotal["upld_rechazado"] == 1) {       
                            $estadoDocumento ="Rechazado";
                            $cantidadRechazadosTrabajadorTotal +=1;
                        }


            
                       /* if($docTotal["upld_rechazado"] == 1){
                            $cantidadRechazadosTrabajadorTotal +=1; 
                        }elseif (($docTotal["upld_docaprob"] == 1 or $fecha_actual <= $fechaUpdateT) and $fecha_actual < $fecha2T){
                            $cantidadAprobadosTrabajadorTotal +=1; 
                        }elseif (($docTotal["upld_venced"]== 1  or $fecha_actual > $fecha2T)and $docTotal["upld_rechazado"] == 0 and $fecha2T!= 0){
                            $cantidadVencidosTrabajadorTotal +=1; 
                        }elseif ($docTotal["id"] != "" and $docTotal["upld_docaprob"] == 0 and $docTotal["upld_docaprob_uid"] == 0){
                            $cantidadPorRevisionTrabajadorTotal +=1; 
                        }*/

                    
                }
                $totalDocu = $totalDocu + $cantidadDocTotales; 
                $totalRechazados = $totalRechazados + $cantidadRechazadosTrabajadorTotal;
                $totalAprobados = $totalAprobados + $cantidadAprobadosTrabajadorTotal; 
                $totalAprobadosObs = $totalAprobadosObs + $cantidadAprbadObsTrabajadorTotal;
                $totalVencidos = $totalVencidos + $cantidadVencidosTrabajadorTotal;
                $totalRevision = $totalRevision + $cantidadPorRevisionTrabajadorTotal;
            }
            return view('home.ssoDocumentos',compact('datosUsuarios','FOLIOS','certificacion','usuarioAqua','totalDocu','totalRechazados','totalAprobados','totalVencidos','totalRevision','totalAprobadosObs','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile')); 
        }

        if($datosUsuarios->type ==2){

            $folios = FolioSso::where('sso_status', '1')->get(['id','sso_mcomp_rut','sso_mcomp_dv','sso_mcomp_name','sso_comp_rut','sso_comp_dv','sso_comp_name','sso_subcomp_active','sso_subcomp_rut','sso_subcomp_dv','sso_subcomp_name','sso_project'])->toArray();

            $totalDocu = 0; 
            $totalRechazados = 0;
            $totalAprobados = 0; 
            $totalVencidos = 0;
            $totalRevision = 0;
            foreach ($folios as $folio) {
        
             /////DOCUMENTOS TOTALES GRAFICA ///////
                $cantidadDocTotales = 0;
                $documentosTotales = EstadoDocumento::where('upld_sso_id',$folio["id"])->where('upld_status', '1')->get();
                $cantidadDocTotales = count($documentosTotales);
               // echo $cantidadDocTra."<br>";
                $cantidadRechazadosTrabajadorTotal = 0;
                $cantidadAprobadosTrabajadorTotal = 0;
                $cantidadVencidosTrabajadorTotal = 0;
                $cantidadPorRevisionTrabajadorTotal = 0;
                foreach ($documentosTotales as $docTotal) {
                    ////////////////// fecha para determinar si esta expirado /////
                    $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
                    $fecha2T = $docTotal["upld_vence_date"];
                    $fechaUpdateT = $docTotal["upld_upddat"];
            
                        if($docTotal["upld_rechazado"] == 1){
                            $cantidadRechazadosTrabajadorTotal +=1; 
                        }elseif (($docTotal["upld_docaprob"] == 1 or $fecha_actual <= $fechaUpdateT) and $fecha_actual < $fecha2T){
                            $cantidadAprobadosTrabajadorTotal +=1; 
                        }elseif (($docTotal["upld_venced"]== 1  or $fecha_actual > $fecha2T)and $docTotal["upld_rechazado"] == 0 and $fecha2T!= 0){
                            $cantidadVencidosTrabajadorTotal +=1; 
                        }elseif ($docTotal["id"] != "" and $docTotal["upld_docaprob"] == 0 and $docTotal["upld_docaprob_uid"] == 0){
                            $cantidadPorRevisionTrabajadorTotal +=1; 
                        }

                    
                }
                $totalDocu = $totalDocu + $cantidadDocTotales; 
                $totalRechazados = $totalRechazados + $cantidadRechazadosTrabajadorTotal;
                $totalAprobados = $totalAprobados + $cantidadAprobadosTrabajadorTotal; 
                $totalVencidos = $totalVencidos + $cantidadVencidosTrabajadorTotal;
                $totalRevision = $totalRevision + $cantidadPorRevisionTrabajadorTotal;
            }
            return view('home.ssoDocumentos',compact('datosUsuarios','FOLIOS','certificacion','usuarioAqua','totalDocu','totalRechazados','totalAprobados','totalVencidos','totalRevision','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile')); 
        }
    } 

    public function ssoEmpresas($id)
    {
        $idUsuario = rtrim(base64_decode($id));
        if($idUsuario == ""){
            return view('sesion.index');
        }
        $usuarioAqua = session('user_aqua');
        $usuarioNOKactivo = session('usuario_nok');
        $usuarioABBChile= session('user_ABB');
        $usuarioClaroChile= session('user_Claro');
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
        $datosUsuarios->load('cargaUsuarioContratista');
        $certificacion = session('certificacion');
        $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
        $UsuarioPrincipal->load('usuarioDatos');

        if($datosUsuarios->type ==3){

            foreach ($UsuarioPrincipal as $rut) {

                $rutprincipal[]=$rut['mainCompanyRut'];   
            }

            $folios = FolioSso::distinct('sso_mcomp_rut')->whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status', '1')->get(['sso_mcomp_rut','sso_mcomp_dv','sso_mcomp_name'])->toArray();

              
            foreach ($folios as $id) {
                
                if(!empty($id)){

                   
                    $folio["rutPrincipal"] = $id['sso_mcomp_rut']."-".$id['sso_mcomp_dv'];
                    $folio["nombrePrincipal"] =  ucwords(mb_strtolower($id['sso_mcomp_name'],'UTF-8'));
                    $Principal[] = $folio;
                    
                }
            }
            return view('home.ssoPrincipal',compact('datosUsuarios','Principal','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile')); 
        }

        if($datosUsuarios->type ==2){

            $folios = FolioSso::distinct('sso_mcomp_rut')->where('sso_status', '1')->get(['sso_mcomp_rut','sso_mcomp_dv','sso_mcomp_name'])->toArray();

            foreach ($folios as $id) {
                
                if(!empty($id)){

                    $folio["rutPrincipal"] = $id['sso_mcomp_rut']."-".$id['sso_mcomp_dv'];
                    $folio["nombrePrincipal"] =  ucwords(mb_strtolower($id['sso_mcomp_name'],'UTF-8'));
                    $Principal[] = $folio;
                    
                }
            }
            return view('home.ssoPrincipal',compact('datosUsuarios','Principal','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile')); 
        }
    }  
}
