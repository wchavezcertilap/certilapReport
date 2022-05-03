<?php

namespace App\Http\Controllers;
use App\DatosUsuarioLogin;
use App\UsuarioContratista;
use App\UsuarioPrincipal;
use App\FolioSso;
use App\empresaPrincipal;
use App\Periodo;
use App\Month;
use App\TipoPagoSso;
use App\EstadoDocumento;
use App\trabajadorSSO;
use App\Documento;
use App\trabajadorFactura;
use App\PagosSso;
use Illuminate\Http\Request;

class TrabajadoresPagadosPreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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


            if($datosUsuarios->type == 2 or $datosUsuarios->type ==1){

                $EmpresasP = FolioSso::distinct()->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['sso_mcomp_name','sso_mcomp_rut'])->toArray();

                foreach($EmpresasP as $empresa){

                    //print_r($empresa);

                    $EmpresasPago = TipoPagoSso::where('mrut',$empresa['sso_mcomp_rut'])->get(['fixed_paytype'])->toArray();
                    if(isset($EmpresasPago[0]['fixed_paytype'])){

                        if($EmpresasPago[0]['fixed_paytype'] == 1 or $EmpresasPago[0]['fixed_paytype'] == 2){
                          
                            $empresaPrincipal['rutprincipal'] = $empresa['sso_mcomp_rut'];
                            $empresaPrincipal['nombrePrincipal'] = $empresa['sso_mcomp_name'];
                            $principalVista[] = $empresaPrincipal;
                        }   
                    }
                }
            }

            
            $empresasPrinpalesPagoD = super_unique($principalVista,'rutprincipal');
        
        return view('trabajadorPagadoSSO.index',compact('datosUsuarios','empresasPrinpalesPagoD','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo')); 
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


            if($datosUsuarios->type == 2 or $datosUsuarios->type ==1){

                $EmpresasP = FolioSso::distinct()->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['sso_mcomp_name','sso_mcomp_rut'])->toArray();

                foreach($EmpresasP as $empresa){

                    //print_r($empresa);

                    $EmpresasPago = TipoPagoSso::where('mrut',$empresa['sso_mcomp_rut'])->get(['fixed_paytype'])->toArray();
                    if(isset($EmpresasPago[0]['fixed_paytype'])){

                        if($EmpresasPago[0]['fixed_paytype'] == 1 or $EmpresasPago[0]['fixed_paytype'] == 2){
                          
                            $empresaPrincipal['rutprincipal'] = $empresa['sso_mcomp_rut'];
                            $empresaPrincipal['nombrePrincipal'] = $empresa['sso_mcomp_name'];
                            $principalVista[] = $empresaPrincipal;
                        }   
                    }
                }
            }

            
            $empresasPrinpalesPagoD = super_unique($principalVista,'rutprincipal');

            $input=$request->all();
            //print_r($input);
        
            $empresasPrincipales = $input["empresaPrincipal"];
            $fechaSeleccion = $input["fechaSeleccion"];
            $fechas = explode(" ", $fechaSeleccion);
                $fecha1 = $fechas[0];
                $fechasDesde = strtotime ( '+4 hour' ,strtotime($fecha1));
                $fecha2 = $fechas[1];
                $fechaHasta = strtotime ( '+4 hour' ,strtotime($fecha2));
            if(isset($input["empresaContratista"])){
                $empresaContratista = $input["empresaContratista"];
            }else{
                $empresaContratista = "";
            }
            if(isset($input["folio"])){
                $folio = $input["folio"];
            }else{
                $folio = "";
            }
            if(isset($input["proyecto"])){
                $proyecto = $input["proyecto"];
            }else{
                $proyecto = "";
            }

            if($empresasPrincipales!="" AND $empresasPrincipales[0]!=1 and $fechaSeleccion!="" and $folio =="" and $proyecto==""){

                $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)->where('sso_status', '1')->get();
                foreach ($folios as $value) {

                    $pagoWebpay = PagosSso::where('sso_id',$value['id'])->whereBetween('req_upddat', array($fechasDesde,  $fechaHasta))->where('req_payment_type','DEPWEB')
                    ->where('req_payment_subtype','WEBPAY')
                    ->where('req_status',2)
                    ->where('req_tbk_status',1);

                    $pagos = PagosSso::where('sso_id',$value['id'])->whereBetween('req_deposit_transdate', array($fechasDesde,  $fechaHasta))->where('req_payment_type','DEPWEB')
                    ->where('req_payment_subtype','DEPOSITO')
                    ->where('req_status',2)
                    ->where('req_deposit_file_approved',1)
                    ->unionAll($pagoWebpay)
                    ->get();

                       
                    if(isset($pagos)){
                        foreach ($pagos as $pg) {
                            $trabajadorF = trabajadorFactura::where('invc_id',$pg['id'])->get();

                        

                            foreach ($trabajadorF as $idtra) {
                                $trabajadorFac = trabajadorSSO::where('id',$idtra['worker_id'])->get(['worker_name','worker_rut','sso_id'])->toArray();

                                

                                if(isset($trabajadorFac)){
                                    $datosTrabajadorFac['folioSSO'] = $value['id'];
                                    $datosTrabajadorFac['folioFact'] = $pg['id'];
                                    $datosTrabajadorFac['rutPrincipal'] = $value['sso_mcomp_rut']."-".$value['sso_mcomp_dv'];
                                    $datosTrabajadorFac['nombrePrincipal'] = ucwords(mb_strtolower($value['sso_mcomp_name'],'UTF-8'));
                                    $datosTrabajadorFac['rutContratista'] = $value['sso_comp_rut']."-".$value['sso_comp_dv'];
                                    $datosTrabajadorFac['nombreContratista'] = ucwords(mb_strtolower($value['sso_comp_name'],'UTF-8'));
                                    if($value['sso_subcomp_active'] == 1){
                                        $datosTrabajadorFac['rutSubContratista'] = $value['sso_subcomp_rut']."-".$value['sso_subcomp_dv'];
                                        $datosTrabajadorFac['nombreSubContratista'] = ucwords(mb_strtolower($value['sso_subcomp_name'],'UTF-8'));
                                    }else{
                                        $datosTrabajadorFac['rutSubContratista'] = "";
                                        $datosTrabajadorFac['nombreSubContratista'] = "";    
                                    }
                                    $datosTrabajadorFac['proyecto'] = ucwords(mb_strtoupper($value['sso_project'],'UTF-8'));
                                    $datosTrabajadorFac['fechaSSO'] = date('d/m/Y',$value['sso_upddat']);
                                    $datosTrabajadorFac['tipoPago'] = ucwords(mb_strtolower($pg['req_payment_subtype'],'UTF-8'));
                                    $datosTrabajadorFac['fechaAprobacion'] = date('d/m/Y',$pg['req_upddat']);
                                    $datosTrabajadorFac['fechaTransaccion'] = date('d/m/Y',$pg['req_crtdat']);
                                    $datosTrabajadorFac['nombreTrabajador'] = ucwords(mb_strtolower($trabajadorFac[0]['worker_name'],'UTF-8'));
                                    $datosTrabajadorFac['rutTrabajador'] = ucwords(mb_strtolower($trabajadorFac[0]['worker_rut'],'UTF-8'));
                                    $datosVista[] = $datosTrabajadorFac;

                                }
                                
                            }
                        }                         
                    }
                }
                

                $lista='<table id="datosTabla" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>N° Folio SSO</th>
                  <th>N° Facturación</th>
                  <th>RUT Principal</th>
                  <th>Empresa Principal</th>
                  <th>RUT Contratista</th>
                  <th>Empresa Contratista</th>
                  <th>RUT Sub Contratista</th>
                  <th>Sub Contratista</th>
                  <th>Proyecto</th>
                  <th>Fecha SSO</th>
                  <th>Tipo de Pago</th>
                  <th>Fecha Aprobación</th>
                  <th>Fecha Transacción</th>
                  <th>Nombre Trabajador</th>
                  <th>RUT Trabajador</th>
                </tr>
                </thead>
                <tbody>';
                $cantidadDatos = count($datosVista);
                foreach ($datosVista as $datoVista) {

                    $lista.= "<tr>";
                    $lista.= "<td>".$datoVista["folioSSO"]."</td>";
                    $lista.= "<td>".$datoVista["folioFact"]."</td>";
                    $lista.= "<td>".$datoVista["rutPrincipal"]."</td>";
                    $lista.= "<td>".$datoVista["nombrePrincipal"]."</td>";
                    $lista.= "<td>".$datoVista["rutContratista"]."</td>";
                    $lista.= "<td>".$datoVista["nombreContratista"]."</td>";
                    $lista.= "<td>".$datoVista["rutSubContratista"]."</td>";
                    $lista.= "<td>".$datoVista["nombreSubContratista"]."</td>";
                    $lista.= "<td>".$datoVista["proyecto"]."</td>";
                    $lista.= "<td>".$datoVista["fechaSSO"]."</td>";
                    $lista.= "<td>".$datoVista["tipoPago"]."</td>";
                    $lista.= "<td>".$datoVista["fechaAprobacion"]."</td>";
                    $lista.= "<td>".$datoVista["fechaTransaccion"]."</td>";
                    $lista.= "<td>".$datoVista["nombreTrabajador"]."</td>";
                    $lista.= "<td>".$datoVista["rutTrabajador"]."</td>";
                    $lista.= "</tr>";
                }
                $lista.= "</table>";
                return view('trabajadorPagadoSSO.index',compact('datosUsuarios','empresasPrinpalesPagoD','certificacion','lista','cantidadDatos','usuarioAqua','usuarioNOKactivo')); 
                
            }elseif ($empresasPrincipales!="" AND $empresasPrincipales[0]==1 and $fechaSeleccion!="" and $folio =="" and $proyecto=="") {

               
                $folios = FolioSso::where('sso_status', '1')->get();
                foreach ($folios as $value) {

                    $pagoWebpay = PagosSso::where('sso_id',$value['id'])->whereBetween('req_upddat', array($fechasDesde,  $fechaHasta))->where('req_payment_type','DEPWEB')
                    ->where('req_payment_subtype','WEBPAY')
                    ->where('req_status',2)
                    ->where('req_tbk_status',1);

                    $pagos = PagosSso::where('sso_id',$value['id'])->whereBetween('req_deposit_transdate', array($fechasDesde,  $fechaHasta))->where('req_payment_type','DEPWEB')
                    ->where('req_payment_subtype','DEPOSITO')
                    ->where('req_status',2)
                    ->where('req_deposit_file_approved',1)
                    ->unionAll($pagoWebpay)
                    ->get();

                   
                    if(isset($pagos)){
                        foreach ($pagos as $pg) {
                            $trabajadorF = trabajadorFactura::where('invc_id',$pg['id'])->get();

                        

                            foreach ($trabajadorF as $idtra) {
                                $trabajadorFac = trabajadorSSO::where('id',$idtra['worker_id'])->get(['worker_name','worker_rut','sso_id'])->toArray();

                                

                                if(isset($trabajadorFac)){
                                    $datosTrabajadorFac['folioSSO'] = $value['id'];
                                    $datosTrabajadorFac['folioFact'] = $pg['id'];
                                    $datosTrabajadorFac['rutPrincipal'] = $value['sso_mcomp_rut']."-".$value['sso_mcomp_dv'];
                                    $datosTrabajadorFac['nombrePrincipal'] = ucwords(mb_strtolower($value['sso_mcomp_name'],'UTF-8'));
                                    $datosTrabajadorFac['rutContratista'] = $value['sso_comp_rut']."-".$value['sso_comp_dv'];
                                    $datosTrabajadorFac['nombreContratista'] = ucwords(mb_strtolower($value['sso_comp_name'],'UTF-8'));
                                    if($value['sso_subcomp_active'] == 1){
                                        $datosTrabajadorFac['rutSubContratista'] = $value['sso_subcomp_rut']."-".$value['sso_subcomp_dv'];
                                        $datosTrabajadorFac['nombreSubContratista'] = ucwords(mb_strtolower($value['sso_subcomp_name'],'UTF-8'));
                                    }else{
                                        $datosTrabajadorFac['rutSubContratista'] = "";
                                        $datosTrabajadorFac['nombreSubContratista'] = "";    
                                    }
                                    $datosTrabajadorFac['proyecto'] = ucwords(mb_strtoupper($value['sso_project'],'UTF-8'));
                                    $datosTrabajadorFac['fechaSSO'] = date('d/m/Y',$value['sso_upddat']);
                                    $datosTrabajadorFac['tipoPago'] = ucwords(mb_strtolower($pg['req_payment_subtype'],'UTF-8'));
                                    $datosTrabajadorFac['fechaAprobacion'] = date('d/m/Y',$pg['req_upddat']);
                                    $datosTrabajadorFac['fechaTransaccion'] = date('d/m/Y',$pg['req_crtdat']);
                                    $datosTrabajadorFac['nombreTrabajador'] = ucwords(mb_strtolower($trabajadorFac[0]['worker_name'],'UTF-8'));
                                    $datosTrabajadorFac['rutTrabajador'] = ucwords(mb_strtolower($trabajadorFac[0]['worker_rut'],'UTF-8'));
                                    $datosVista[] = $datosTrabajadorFac;
                                } 
                            }
                        }                         
                    }
                }
                

                $lista='<table id="datosTabla" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>N° Folio SSO</th>
                  <th>N° Facturación</th>
                  <th>RUT Principal</th>
                  <th>Empresa Principal</th>
                  <th>RUT Contratista</th>
                  <th>Empresa Contratista</th>
                  <th>RUT Sub Contratista</th>
                  <th>Sub Contratista</th>
                  <th>Proyecto</th>
                  <th>Fecha SSO</th>
                  <th>Tipo de Pago</th>
                  <th>Fecha Aprobación</th>
                  <th>Fecha Transacción</th>
                  <th>Nombre Trabajador</th>
                  <th>RUT Trabajador</th>
                </tr>
                </thead>
                <tbody>';
                $cantidadDatos = count($datosVista);
                foreach ($datosVista as $datoVista) {

                    $lista.= "<tr>";
                    $lista.= "<td>".$datoVista["folioSSO"]."</td>";
                    $lista.= "<td>".$datoVista["folioFact"]."</td>";
                    $lista.= "<td>".$datoVista["rutPrincipal"]."</td>";
                    $lista.= "<td>".$datoVista["nombrePrincipal"]."</td>";
                    $lista.= "<td>".$datoVista["rutContratista"]."</td>";
                    $lista.= "<td>".$datoVista["nombreContratista"]."</td>";
                    $lista.= "<td>".$datoVista["rutSubContratista"]."</td>";
                    $lista.= "<td>".$datoVista["nombreSubContratista"]."</td>";
                    $lista.= "<td>".$datoVista["proyecto"]."</td>";
                    $lista.= "<td>".$datoVista["fechaSSO"]."</td>";
                    $lista.= "<td>".$datoVista["tipoPago"]."</td>";
                    $lista.= "<td>".$datoVista["fechaAprobacion"]."</td>";
                    $lista.= "<td>".$datoVista["fechaTransaccion"]."</td>";
                    $lista.= "<td>".$datoVista["nombreTrabajador"]."</td>";
                    $lista.= "<td>".$datoVista["rutTrabajador"]."</td>";
                    $lista.= "</tr>";
                }
                $lista.= "</table>";
                return view('trabajadorPagadoSSO.index',compact('datosUsuarios','empresasPrinpalesPagoD','certificacion','lista','cantidadDatos','usuarioNOKactivo','usuarioAqua','usuarioABBChile')); 
            }elseif (($empresasPrincipales!="" AND $empresasPrincipales[0]!=1) and $fechaSeleccion!="" and $empresaContratista!= "") {
                
                $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)
                ->whereIn('sso_comp_rut',$empresaContratista)
                ->where('sso_status', '1')->get();
                foreach ($folios as $value) {

                    $pagoWebpay = PagosSso::where('sso_id',$value['id'])->whereBetween('req_upddat', array($fechasDesde,  $fechaHasta))->where('req_payment_type','DEPWEB')
                    ->where('req_payment_subtype','WEBPAY')
                    ->where('req_status',2)
                    ->where('req_tbk_status',1);

                    $pagos = PagosSso::where('sso_id',$value['id'])->whereBetween('req_deposit_transdate', array($fechasDesde,  $fechaHasta))->where('req_payment_type','DEPWEB')
                    ->where('req_payment_subtype','DEPOSITO')
                    ->where('req_status',2)
                    ->where('req_deposit_file_approved',1)
                    ->unionAll($pagoWebpay)
                    ->get();

                   
                    if(isset($pagos)){
                        foreach ($pagos as $pg) {
                            $trabajadorF = trabajadorFactura::where('invc_id',$pg['id'])->get();

                        

                            foreach ($trabajadorF as $idtra) {
                                $trabajadorFac = trabajadorSSO::where('id',$idtra['worker_id'])->get(['worker_name','worker_rut','sso_id'])->toArray();

                                

                                if(isset($trabajadorFac)){
                                    $datosTrabajadorFac['folioSSO'] = $value['id'];
                                    $datosTrabajadorFac['folioFact'] = $pg['id'];
                                    $datosTrabajadorFac['rutPrincipal'] = $value['sso_mcomp_rut']."-".$value['sso_mcomp_dv'];
                                    $datosTrabajadorFac['nombrePrincipal'] = ucwords(mb_strtolower($value['sso_mcomp_name'],'UTF-8'));
                                    $datosTrabajadorFac['rutContratista'] = $value['sso_comp_rut']."-".$value['sso_comp_dv'];
                                    $datosTrabajadorFac['nombreContratista'] = ucwords(mb_strtolower($value['sso_comp_name'],'UTF-8'));
                                    if($value['sso_subcomp_active'] == 1){
                                        $datosTrabajadorFac['rutSubContratista'] = $value['sso_subcomp_rut']."-".$value['sso_subcomp_dv'];
                                        $datosTrabajadorFac['nombreSubContratista'] = ucwords(mb_strtolower($value['sso_subcomp_name'],'UTF-8'));
                                    }else{
                                        $datosTrabajadorFac['rutSubContratista'] = "";
                                        $datosTrabajadorFac['nombreSubContratista'] = "";    
                                    }
                                    $datosTrabajadorFac['proyecto'] = ucwords(mb_strtoupper($value['sso_project'],'UTF-8'));
                                    $datosTrabajadorFac['fechaSSO'] = date('d/m/Y',$value['sso_upddat']);
                                    $datosTrabajadorFac['tipoPago'] = ucwords(mb_strtolower($pg['req_payment_subtype'],'UTF-8'));
                                    $datosTrabajadorFac['fechaAprobacion'] = date('d/m/Y',$pg['req_upddat']);
                                    $datosTrabajadorFac['fechaTransaccion'] = date('d/m/Y',$pg['req_crtdat']);
                                    $datosTrabajadorFac['nombreTrabajador'] = ucwords(mb_strtolower($trabajadorFac[0]['worker_name'],'UTF-8'));
                                    $datosTrabajadorFac['rutTrabajador'] = ucwords(mb_strtolower($trabajadorFac[0]['worker_rut'],'UTF-8'));
                                    $datosVista[] = $datosTrabajadorFac;

                                }
                                
                            }
                        }                         
                    }
                }
                
                $lista='<table id="datosTabla" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>N° Folio SSO</th>
                  <th>N° Facturación</th>
                  <th>RUT Principal</th>
                  <th>Empresa Principal</th>
                  <th>RUT Contratista</th>
                  <th>Empresa Contratista</th>
                  <th>RUT Sub Contratista</th>
                  <th>Sub Contratista</th>
                  <th>Proyecto</th>
                  <th>Fecha SSO</th>
                  <th>Tipo de Pago</th>
                  <th>Fecha Aprobación</th>
                  <th>Fecha Transacción</th>
                  <th>Nombre Trabajador</th>
                  <th>RUT Trabajador</th>
                </tr>
                </thead>
                <tbody>';
                $cantidadDatos = count($datosVista);
                foreach ($datosVista as $datoVista) {

                    $lista.= "<tr>";
                    $lista.= "<td>".$datoVista["folioSSO"]."</td>";
                    $lista.= "<td>".$datoVista["folioFact"]."</td>";
                    $lista.= "<td>".$datoVista["rutPrincipal"]."</td>";
                    $lista.= "<td>".$datoVista["nombrePrincipal"]."</td>";
                    $lista.= "<td>".$datoVista["rutContratista"]."</td>";
                    $lista.= "<td>".$datoVista["nombreContratista"]."</td>";
                    $lista.= "<td>".$datoVista["rutSubContratista"]."</td>";
                    $lista.= "<td>".$datoVista["nombreSubContratista"]."</td>";
                    $lista.= "<td>".$datoVista["proyecto"]."</td>";
                    $lista.= "<td>".$datoVista["fechaSSO"]."</td>";
                    $lista.= "<td>".$datoVista["tipoPago"]."</td>";
                    $lista.= "<td>".$datoVista["fechaAprobacion"]."</td>";
                    $lista.= "<td>".$datoVista["fechaTransaccion"]."</td>";
                    $lista.= "<td>".$datoVista["nombreTrabajador"]."</td>";
                    $lista.= "<td>".$datoVista["rutTrabajador"]."</td>";
                    $lista.= "</tr>";
                }
                $lista.= "</table>";
                return view('trabajadorPagadoSSO.index',compact('datosUsuarios','empresasPrinpalesPagoD','certificacion','lista','cantidadDatos','usuarioAqua','usuarioABBChile','usuarioNOKactivo')); 
                
            }elseif (($empresasPrincipales!="" or $$empresasPrincipales[0]!=1) and $fechaSeleccion!="" and $empresaContratista!= "" and $folio!= "") {
              
                $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)
                ->whereIn('sso_comp_rut',$empresaContratista)
                ->where('id',$folio)
                ->where('sso_status', '1')->get();
                foreach ($folios as $value) {

                    $pagoWebpay = PagosSso::where('sso_id',$value['id'])->whereBetween('req_upddat', array($fechasDesde,  $fechaHasta))->where('req_payment_type','DEPWEB')
                    ->where('req_payment_subtype','WEBPAY')
                    ->where('req_status',2)
                    ->where('req_tbk_status',1);

                    $pagos = PagosSso::where('sso_id',$value['id'])->whereBetween('req_deposit_transdate', array($fechasDesde,  $fechaHasta))->where('req_payment_type','DEPWEB')
                    ->where('req_payment_subtype','DEPOSITO')
                    ->where('req_status',2)
                    ->where('req_deposit_file_approved',1)
                    ->unionAll($pagoWebpay)
                    ->get();

                   
                    if(isset($pagos)){
                        foreach ($pagos as $pg) {
                            $trabajadorF = trabajadorFactura::where('invc_id',$pg['id'])->get();

                        

                            foreach ($trabajadorF as $idtra) {
                                $trabajadorFac = trabajadorSSO::where('id',$idtra['worker_id'])->get(['worker_name','worker_rut','sso_id'])->toArray();

                                

                                if(isset($trabajadorFac)){
                                    $datosTrabajadorFac['folioSSO'] = $value['id'];
                                    $datosTrabajadorFac['folioFact'] = $pg['id'];
                                    $datosTrabajadorFac['rutPrincipal'] = $value['sso_mcomp_rut']."-".$value['sso_mcomp_dv'];
                                    $datosTrabajadorFac['nombrePrincipal'] = ucwords(mb_strtolower($value['sso_mcomp_name'],'UTF-8'));
                                    $datosTrabajadorFac['rutContratista'] = $value['sso_comp_rut']."-".$value['sso_comp_dv'];
                                    $datosTrabajadorFac['nombreContratista'] = ucwords(mb_strtolower($value['sso_comp_name'],'UTF-8'));
                                    if($value['sso_subcomp_active'] == 1){
                                        $datosTrabajadorFac['rutSubContratista'] = $value['sso_subcomp_rut']."-".$value['sso_subcomp_dv'];
                                        $datosTrabajadorFac['nombreSubContratista'] = ucwords(mb_strtolower($value['sso_subcomp_name'],'UTF-8'));
                                    }else{
                                        $datosTrabajadorFac['rutSubContratista'] = "";
                                        $datosTrabajadorFac['nombreSubContratista'] = "";    
                                    }
                                    $datosTrabajadorFac['proyecto'] = ucwords(mb_strtoupper($value['sso_project'],'UTF-8'));
                                    $datosTrabajadorFac['fechaSSO'] = date('d/m/Y',$value['sso_upddat']);
                                    $datosTrabajadorFac['tipoPago'] = ucwords(mb_strtolower($pg['req_payment_subtype'],'UTF-8'));
                                    $datosTrabajadorFac['fechaAprobacion'] = date('d/m/Y',$pg['req_upddat']);
                                    $datosTrabajadorFac['fechaTransaccion'] = date('d/m/Y',$pg['req_crtdat']);
                                    $datosTrabajadorFac['nombreTrabajador'] = ucwords(mb_strtolower($trabajadorFac[0]['worker_name'],'UTF-8'));
                                    $datosTrabajadorFac['rutTrabajador'] = ucwords(mb_strtolower($trabajadorFac[0]['worker_rut'],'UTF-8'));
                                    $datosVista[] = $datosTrabajadorFac;

                                }
                                
                            }
                        }                         
                    }
                }
                
                $lista='<table id="datosTabla" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>N° Folio SSO</th>
                  <th>N° Facturación</th>
                  <th>RUT Principal</th>
                  <th>Empresa Principal</th>
                  <th>RUT Contratista</th>
                  <th>Empresa Contratista</th>
                  <th>RUT Sub Contratista</th>
                  <th>Sub Contratista</th>
                  <th>Proyecto</th>
                  <th>Fecha SSO</th>
                  <th>Tipo de Pago</th>
                  <th>Fecha Aprobación</th>
                  <th>Fecha Transacción</th>
                  <th>Nombre Trabajador</th>
                  <th>RUT Trabajador</th>
                </tr>
                </thead>
                <tbody>';
                $cantidadDatos = count($datosVista);
                foreach ($datosVista as $datoVista) {

                    $lista.= "<tr>";
                    $lista.= "<td>".$datoVista["folioSSO"]."</td>";
                    $lista.= "<td>".$datoVista["folioFact"]."</td>";
                    $lista.= "<td>".$datoVista["rutPrincipal"]."</td>";
                    $lista.= "<td>".$datoVista["nombrePrincipal"]."</td>";
                    $lista.= "<td>".$datoVista["rutContratista"]."</td>";
                    $lista.= "<td>".$datoVista["nombreContratista"]."</td>";
                    $lista.= "<td>".$datoVista["rutSubContratista"]."</td>";
                    $lista.= "<td>".$datoVista["nombreSubContratista"]."</td>";
                    $lista.= "<td>".$datoVista["proyecto"]."</td>";
                    $lista.= "<td>".$datoVista["fechaSSO"]."</td>";
                    $lista.= "<td>".$datoVista["tipoPago"]."</td>";
                    $lista.= "<td>".$datoVista["fechaAprobacion"]."</td>";
                    $lista.= "<td>".$datoVista["fechaTransaccion"]."</td>";
                    $lista.= "<td>".$datoVista["nombreTrabajador"]."</td>";
                    $lista.= "<td>".$datoVista["rutTrabajador"]."</td>";
                    $lista.= "</tr>";
                }
                $lista.= "</table>";
                return view('trabajadorPagadoSSO.index',compact('datosUsuarios','empresasPrinpalesPagoD','certificacion','lista','cantidadDatos','usuarioAqua','usuarioABBChile','usuarioNOKactivo'));    
            }elseif (($empresasPrincipales!="" or $$empresasPrincipales[0]!=1) and $fechaSeleccion!="" and $empresaContratista!= "" and $folio!= "" and $proyecto!= "") {
               
                $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)
                ->whereIn('sso_comp_rut',$empresaContratista)
                ->where('id',$folio)
                ->where('id','LIKE',"%{$proyecto}%")
                ->where('sso_status', '1')->get();
                foreach ($folios as $value) {

                    $pagoWebpay = PagosSso::where('sso_id',$value['id'])->whereBetween('req_upddat', array($fechasDesde,  $fechaHasta))->where('req_payment_type','DEPWEB')
                    ->where('req_payment_subtype','WEBPAY')
                    ->where('req_status',2)
                    ->where('req_tbk_status',1);

                    $pagos = PagosSso::where('sso_id',$value['id'])->whereBetween('req_deposit_transdate', array($fechasDesde,  $fechaHasta))->where('req_payment_type','DEPWEB')
                    ->where('req_payment_subtype','DEPOSITO')
                    ->where('req_status',2)
                    ->where('req_deposit_file_approved',1)
                    ->unionAll($pagoWebpay)
                    ->get();

                   
                    if(isset($pagos)){
                        foreach ($pagos as $pg) {
                            $trabajadorF = trabajadorFactura::where('invc_id',$pg['id'])->get();

                        

                            foreach ($trabajadorF as $idtra) {
                                $trabajadorFac = trabajadorSSO::where('id',$idtra['worker_id'])->get(['worker_name','worker_rut','sso_id'])->toArray();

                                

                                if(isset($trabajadorFac)){
                                    $datosTrabajadorFac['folioSSO'] = $value['id'];
                                    $datosTrabajadorFac['folioFact'] = $pg['id'];
                                    $datosTrabajadorFac['rutPrincipal'] = $value['sso_mcomp_rut']."-".$value['sso_mcomp_dv'];
                                    $datosTrabajadorFac['nombrePrincipal'] = ucwords(mb_strtolower($value['sso_mcomp_name'],'UTF-8'));
                                    $datosTrabajadorFac['rutContratista'] = $value['sso_comp_rut']."-".$value['sso_comp_dv'];
                                    $datosTrabajadorFac['nombreContratista'] = ucwords(mb_strtolower($value['sso_comp_name'],'UTF-8'));
                                    if($value['sso_subcomp_active'] == 1){
                                        $datosTrabajadorFac['rutSubContratista'] = $value['sso_subcomp_rut']."-".$value['sso_subcomp_dv'];
                                        $datosTrabajadorFac['nombreSubContratista'] = ucwords(mb_strtolower($value['sso_subcomp_name'],'UTF-8'));
                                    }else{
                                        $datosTrabajadorFac['rutSubContratista'] = "";
                                        $datosTrabajadorFac['nombreSubContratista'] = "";    
                                    }
                                    $datosTrabajadorFac['proyecto'] = ucwords(mb_strtoupper($value['sso_project'],'UTF-8'));
                                    $datosTrabajadorFac['fechaSSO'] = date('d/m/Y',$value['sso_upddat']);
                                    $datosTrabajadorFac['tipoPago'] = ucwords(mb_strtolower($pg['req_payment_subtype'],'UTF-8'));
                                    $datosTrabajadorFac['fechaAprobacion'] = date('d/m/Y',$pg['req_upddat']);
                                    $datosTrabajadorFac['fechaTransaccion'] = date('d/m/Y',$pg['req_crtdat']);
                                    $datosTrabajadorFac['nombreTrabajador'] = ucwords(mb_strtolower($trabajadorFac[0]['worker_name'],'UTF-8'));
                                    $datosTrabajadorFac['rutTrabajador'] = ucwords(mb_strtolower($trabajadorFac[0]['worker_rut'],'UTF-8'));
                                    $datosVista[] = $datosTrabajadorFac;

                                }
                                
                            }
                        }                         
                    }
                }
                
                $lista='<table id="datosTabla" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>N° Folio SSO</th>
                  <th>N° Facturación</th>
                  <th>RUT Principal</th>
                  <th>Empresa Principal</th>
                  <th>RUT Contratista</th>
                  <th>Empresa Contratista</th>
                  <th>RUT Sub Contratista</th>
                  <th>Sub Contratista</th>
                  <th>Proyecto</th>
                  <th>Fecha SSO</th>
                  <th>Tipo de Pago</th>
                  <th>Fecha Aprobación</th>
                  <th>Fecha Transacción</th>
                  <th>Nombre Trabajador</th>
                  <th>RUT Trabajador</th>
                </tr>
                </thead>
                <tbody>';
                $cantidadDatos = count($datosVista);
                foreach ($datosVista as $datoVista) {

                    $lista.= "<tr>";
                    $lista.= "<td>".$datoVista["folioSSO"]."</td>";
                    $lista.= "<td>".$datoVista["folioFact"]."</td>";
                    $lista.= "<td>".$datoVista["rutPrincipal"]."</td>";
                    $lista.= "<td>".$datoVista["nombrePrincipal"]."</td>";
                    $lista.= "<td>".$datoVista["rutContratista"]."</td>";
                    $lista.= "<td>".$datoVista["nombreContratista"]."</td>";
                    $lista.= "<td>".$datoVista["rutSubContratista"]."</td>";
                    $lista.= "<td>".$datoVista["nombreSubContratista"]."</td>";
                    $lista.= "<td>".$datoVista["proyecto"]."</td>";
                    $lista.= "<td>".$datoVista["fechaSSO"]."</td>";
                    $lista.= "<td>".$datoVista["tipoPago"]."</td>";
                    $lista.= "<td>".$datoVista["fechaAprobacion"]."</td>";
                    $lista.= "<td>".$datoVista["fechaTransaccion"]."</td>";
                    $lista.= "<td>".$datoVista["nombreTrabajador"]."</td>";
                    $lista.= "<td>".$datoVista["rutTrabajador"]."</td>";
                    $lista.= "</tr>";
                }
                $lista.= "</table>";
                return view('trabajadorPagadoSSO.index',compact('datosUsuarios','empresasPrinpalesPagoD','certificacion','lista','cantidadDatos','usuarioAqua','usuarioNOKactivo','usuarioABBChile'));    
            }elseif (($empresasPrincipales!="" or $empresasPrincipales[0]!=1) and $fechaSeleccion!="" and $folio!= "") {
                
                $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)
                ->where('id',$folio)
                ->where('sso_status', '1')->get();
                foreach ($folios as $value) {

                    $pagoWebpay = PagosSso::where('sso_id',$value['id'])->whereBetween('req_upddat', array($fechasDesde,  $fechaHasta))->where('req_payment_type','DEPWEB')
                    ->where('req_payment_subtype','WEBPAY')
                    ->where('req_status',2)
                    ->where('req_tbk_status',1);

                    $pagos = PagosSso::where('sso_id',$value['id'])->whereBetween('req_deposit_transdate', array($fechasDesde,  $fechaHasta))->where('req_payment_type','DEPWEB')
                    ->where('req_payment_subtype','DEPOSITO')
                    ->where('req_status',2)
                    ->where('req_deposit_file_approved',1)
                    ->unionAll($pagoWebpay)
                    ->get();

                   
                    if(isset($pagos)){
                        foreach ($pagos as $pg) {
                            $trabajadorF = trabajadorFactura::where('invc_id',$pg['id'])->get();

                        

                            foreach ($trabajadorF as $idtra) {
                                $trabajadorFac = trabajadorSSO::where('id',$idtra['worker_id'])->get(['worker_name','worker_rut','sso_id'])->toArray();

                                

                                if(isset($trabajadorFac)){
                                    $datosTrabajadorFac['folioSSO'] = $value['id'];
                                    $datosTrabajadorFac['folioFact'] = $pg['id'];
                                    $datosTrabajadorFac['rutPrincipal'] = $value['sso_mcomp_rut']."-".$value['sso_mcomp_dv'];
                                    $datosTrabajadorFac['nombrePrincipal'] = ucwords(mb_strtolower($value['sso_mcomp_name'],'UTF-8'));
                                    $datosTrabajadorFac['rutContratista'] = $value['sso_comp_rut']."-".$value['sso_comp_dv'];
                                    $datosTrabajadorFac['nombreContratista'] = ucwords(mb_strtolower($value['sso_comp_name'],'UTF-8'));
                                    if($value['sso_subcomp_active'] == 1){
                                        $datosTrabajadorFac['rutSubContratista'] = $value['sso_subcomp_rut']."-".$value['sso_subcomp_dv'];
                                        $datosTrabajadorFac['nombreSubContratista'] = ucwords(mb_strtolower($value['sso_subcomp_name'],'UTF-8'));
                                    }else{
                                        $datosTrabajadorFac['rutSubContratista'] = "";
                                        $datosTrabajadorFac['nombreSubContratista'] = "";    
                                    }
                                    $datosTrabajadorFac['proyecto'] = ucwords(mb_strtoupper($value['sso_project'],'UTF-8'));
                                    $datosTrabajadorFac['fechaSSO'] = date('d/m/Y',$value['sso_upddat']);
                                    $datosTrabajadorFac['tipoPago'] = ucwords(mb_strtolower($pg['req_payment_subtype'],'UTF-8'));
                                    $datosTrabajadorFac['fechaAprobacion'] = date('d/m/Y',$pg['req_upddat']);
                                    $datosTrabajadorFac['fechaTransaccion'] = date('d/m/Y',$pg['req_crtdat']);
                                    $datosTrabajadorFac['nombreTrabajador'] = ucwords(mb_strtolower($trabajadorFac[0]['worker_name'],'UTF-8'));
                                    $datosTrabajadorFac['rutTrabajador'] = ucwords(mb_strtolower($trabajadorFac[0]['worker_rut'],'UTF-8'));
                                    $datosVista[] = $datosTrabajadorFac;

                                }
                                
                            }
                        }                         
                    }
                }
                
                $lista='<table id="datosTabla" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>N° Folio SSO</th>
                  <th>N° Facturación</th>
                  <th>RUT Principal</th>
                  <th>Empresa Principal</th>
                  <th>RUT Contratista</th>
                  <th>Empresa Contratista</th>
                  <th>RUT Sub Contratista</th>
                  <th>Sub Contratista</th>
                  <th>Proyecto</th>
                  <th>Fecha SSO</th>
                  <th>Tipo de Pago</th>
                  <th>Fecha Aprobación</th>
                  <th>Fecha Transacción</th>
                  <th>Nombre Trabajador</th>
                  <th>RUT Trabajador</th>
                </tr>
                </thead>
                <tbody>';
                $cantidadDatos = count($datosVista);
                foreach ($datosVista as $datoVista) {

                    $lista.= "<tr>";
                    $lista.= "<td>".$datoVista["folioSSO"]."</td>";
                    $lista.= "<td>".$datoVista["folioFact"]."</td>";
                    $lista.= "<td>".$datoVista["rutPrincipal"]."</td>";
                    $lista.= "<td>".$datoVista["nombrePrincipal"]."</td>";
                    $lista.= "<td>".$datoVista["rutContratista"]."</td>";
                    $lista.= "<td>".$datoVista["nombreContratista"]."</td>";
                    $lista.= "<td>".$datoVista["rutSubContratista"]."</td>";
                    $lista.= "<td>".$datoVista["nombreSubContratista"]."</td>";
                    $lista.= "<td>".$datoVista["proyecto"]."</td>";
                    $lista.= "<td>".$datoVista["fechaSSO"]."</td>";
                    $lista.= "<td>".$datoVista["tipoPago"]."</td>";
                    $lista.= "<td>".$datoVista["fechaAprobacion"]."</td>";
                    $lista.= "<td>".$datoVista["fechaTransaccion"]."</td>";
                    $lista.= "<td>".$datoVista["nombreTrabajador"]."</td>";
                    $lista.= "<td>".$datoVista["rutTrabajador"]."</td>";
                    $lista.= "</tr>";
                }
                $lista.= "</table>";
                return view('trabajadorPagadoSSO.index',compact('datosUsuarios','empresasPrinpalesPagoD','certificacion','lista','cantidadDatos','usuarioAqua','usuarioNOKactivo','usuarioABBChile'));    
            }elseif (($empresasPrincipales!="" or $empresasPrincipales[0]!=1) and $fechaSeleccion!="" and $proyecto!= "") {
                
                $folios = FolioSso::whereIn('sso_mcomp_rut',$empresasPrincipales)
                ->where('sso_project',$proyecto)
                ->where('sso_status', '1')->get();
                foreach ($folios as $value) {

                    $pagoWebpay = PagosSso::where('sso_id',$value['id'])->whereBetween('req_upddat', array($fechasDesde,  $fechaHasta))->where('req_payment_type','DEPWEB')
                    ->where('req_payment_subtype','WEBPAY')
                    ->where('req_status',2)
                    ->where('req_tbk_status',1);

                    $pagos = PagosSso::where('sso_id',$value['id'])->whereBetween('req_deposit_transdate', array($fechasDesde,  $fechaHasta))->where('req_payment_type','DEPWEB')
                    ->where('req_payment_subtype','DEPOSITO')
                    ->where('req_status',2)
                    ->where('req_deposit_file_approved',1)
                    ->unionAll($pagoWebpay)
                    ->get();

                   
                    if(isset($pagos)){
                        foreach ($pagos as $pg) {
                            $trabajadorF = trabajadorFactura::where('invc_id',$pg['id'])->get();

                        

                            foreach ($trabajadorF as $idtra) {
                                $trabajadorFac = trabajadorSSO::where('id',$idtra['worker_id'])->get(['worker_name','worker_rut','sso_id'])->toArray();

                                

                                if(isset($trabajadorFac)){
                                    $datosTrabajadorFac['folioSSO'] = $value['id'];
                                    $datosTrabajadorFac['folioFact'] = $pg['id'];
                                    $datosTrabajadorFac['rutPrincipal'] = $value['sso_mcomp_rut']."-".$value['sso_mcomp_dv'];
                                    $datosTrabajadorFac['nombrePrincipal'] = ucwords(mb_strtolower($value['sso_mcomp_name'],'UTF-8'));
                                    $datosTrabajadorFac['rutContratista'] = $value['sso_comp_rut']."-".$value['sso_comp_dv'];
                                    $datosTrabajadorFac['nombreContratista'] = ucwords(mb_strtolower($value['sso_comp_name'],'UTF-8'));
                                    if($value['sso_subcomp_active'] == 1){
                                        $datosTrabajadorFac['rutSubContratista'] = $value['sso_subcomp_rut']."-".$value['sso_subcomp_dv'];
                                        $datosTrabajadorFac['nombreSubContratista'] = ucwords(mb_strtolower($value['sso_subcomp_name'],'UTF-8'));
                                    }else{
                                        $datosTrabajadorFac['rutSubContratista'] = "";
                                        $datosTrabajadorFac['nombreSubContratista'] = "";    
                                    }
                                    $datosTrabajadorFac['proyecto'] = ucwords(mb_strtoupper($value['sso_project'],'UTF-8'));
                                    $datosTrabajadorFac['fechaSSO'] = date('d/m/Y',$value['sso_upddat']);
                                    $datosTrabajadorFac['tipoPago'] = ucwords(mb_strtolower($pg['req_payment_subtype'],'UTF-8'));
                                    $datosTrabajadorFac['fechaAprobacion'] = date('d/m/Y',$pg['req_upddat']);
                                    $datosTrabajadorFac['fechaTransaccion'] = date('d/m/Y',$pg['req_crtdat']);
                                    $datosTrabajadorFac['nombreTrabajador'] = ucwords(mb_strtolower($trabajadorFac[0]['worker_name'],'UTF-8'));
                                    $datosTrabajadorFac['rutTrabajador'] = ucwords(mb_strtolower($trabajadorFac[0]['worker_rut'],'UTF-8'));
                                    $datosVista[] = $datosTrabajadorFac;

                                }
                                
                            }
                        }                         
                    }
                }
                
                $lista='<table id="datosTabla" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>N° Folio SSO</th>
                  <th>N° Facturación</th>
                  <th>RUT Principal</th>
                  <th>Empresa Principal</th>
                  <th>RUT Contratista</th>
                  <th>Empresa Contratista</th>
                  <th>RUT Sub Contratista</th>
                  <th>Sub Contratista</th>
                  <th>Proyecto</th>
                  <th>Fecha SSO</th>
                  <th>Tipo de Pago</th>
                  <th>Fecha Aprobación</th>
                  <th>Fecha Transacción</th>
                  <th>Nombre Trabajador</th>
                  <th>RUT Trabajador</th>
                </tr>
                </thead>
                <tbody>';
                $cantidadDatos = count($datosVista);
                foreach ($datosVista as $datoVista) {

                    $lista.= "<tr>";
                    $lista.= "<td>".$datoVista["folioSSO"]."</td>";
                    $lista.= "<td>".$datoVista["folioFact"]."</td>";
                    $lista.= "<td>".$datoVista["rutPrincipal"]."</td>";
                    $lista.= "<td>".$datoVista["nombrePrincipal"]."</td>";
                    $lista.= "<td>".$datoVista["rutContratista"]."</td>";
                    $lista.= "<td>".$datoVista["nombreContratista"]."</td>";
                    $lista.= "<td>".$datoVista["rutSubContratista"]."</td>";
                    $lista.= "<td>".$datoVista["nombreSubContratista"]."</td>";
                    $lista.= "<td>".$datoVista["proyecto"]."</td>";
                    $lista.= "<td>".$datoVista["fechaSSO"]."</td>";
                    $lista.= "<td>".$datoVista["tipoPago"]."</td>";
                    $lista.= "<td>".$datoVista["fechaAprobacion"]."</td>";
                    $lista.= "<td>".$datoVista["fechaTransaccion"]."</td>";
                    $lista.= "<td>".$datoVista["nombreTrabajador"]."</td>";
                    $lista.= "<td>".$datoVista["rutTrabajador"]."</td>";
                    $lista.= "</tr>";
                }
                $lista.= "</table>";
                return view('trabajadorPagadoSSO.index',compact('datosUsuarios','empresasPrinpalesPagoD','certificacion','lista','cantidadDatos','usuarioAqua','usuarioABBChile','usuarioNOKactivo'));    
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
