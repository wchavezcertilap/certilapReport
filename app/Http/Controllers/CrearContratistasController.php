<?php

namespace App\Http\Controllers;
use DB;
use App\DatosUsuarioLogin;
use App\UsuarioContratista;
use App\UsuarioPrincipal;
use App\empresaPrincipal;
use App\EmpresaPrincipalRangoPago;
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

class CrearContratistasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $idUsuario = session('user_id');
        $usuarioAqua = session('user_aqua');
        $usuarioABBChile= session('user_ABB');
        $usuarioClaroChile= session('user_Claro');
        $usuarioNOKactivo = session('usuario_nok');
        $certificacion = session('certificacion');
        if($idUsuario ==  ""){
            return view('sesion.index');
        }
        $certificacion = session('certificacion');
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
        $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
        $UsuarioPrincipal->load('usuarioDatos');


            if($datosUsuarios->type ==2 || $datosUsuarios->type == 1){

                $peridoActual = Periodo::orderBy('id', 'DESC')
                ->take(1)->get(['id'])->toArray();

               
                                
                $periodos = Periodo::where('id', $peridoActual)->orderBy('id', 'DES')->get(['id', 'monthId','year']);
                $periodos->load('mes');

            

                return view('crearContratista.index',compact('datosUsuarios','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile','periodos')); 

            }
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
        $usuarioAqua = session('user_aqua');
        $usuarioABBChile= session('user_ABB');
        $usuarioClaroChile= session('user_Claro');
        $usuarioNOKactivo = session('usuario_nok');
        $certificacion = session('certificacion');
        if($idUsuario ==  ""){
            return view('sesion.index');
        }
        $certificacion = session('certificacion');
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
        $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
        $UsuarioPrincipal->load('usuarioDatos');


            if($datosUsuarios->type ==2 || $datosUsuarios->type == 1){

                $peridoActual = Periodo::orderBy('id', 'DESC')
                ->take(1)->get(['id'])->toArray();
             
                $periodos = Periodo::where('id', $peridoActual)->orderBy('id', 'DES')->get(['id', 'monthId','year']);
                $periodos->load('mes');

                //return view('crearPrincipal.index',compact('datosUsuarios','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','periodos')); 

            }
        /////////logica insersion ///////////
        $input=$request->all();
        $idPerido = $input["peridoUnico"];
        $empresaPrincipalPeridoActual = empresaPrincipal::where('periodId',$idPerido)->orderBy('name', 'ASC')->get()->toArray();
        $cuentaActual = count($empresaPrincipalPeridoActual);
        $contratistasActual = Contratista::where('periodId',$idPerido)->get()->toArray();
        $cuentaActualContratista = count($contratistasActual);
        if($cuentaActual > 0 and $cuentaActualContratista <= 0){
            $peridoAnterior = Periodo::orderBy('id', 'DESC')->take(1)->where('id', '!=',$idPerido)->get(['id'])->toArray();
            foreach ($empresaPrincipalPeridoActual as $value) {

                $contratistasOld = Contratista::where('periodId',$peridoAnterior)->where('mainCompanyRut',$value['rut'])->get()->toArray();

                foreach ($contratistasOld as $contratista) {

                    if($contratista['certificateState'] ==  11){
                        if($contratista['mainCompanyRut']==76452811 OR $contratista['mainCompanyRut']==76794910 OR $contratista['mainCompanyRut']==79872420 OR $contratista['mainCompanyRut']==86247400 OR $contratista['mainCompanyRut']==79800600 OR $contratista['mainCompanyRut']==84449400 OR $contratista['mainCompanyRut']==88274600 OR $contratista['mainCompanyRut']==87782700 OR $contratista['mainCompanyRut']==76495180 OR $contratista['mainCompanyRut']==99595500 OR $contratista['mainCompanyRut']== 89604200 OR $contratista['mainCompanyRut']== 78754560 OR $contratista['mainCompanyRut']== 76125666 OR $contratista['mainCompanyRut']== 78512930 OR $contratista['mainCompanyRut']== 76479460 OR $contratista['mainCompanyRut']== 96553830 OR $contratista['mainCompanyRut']== 96563570 OR $contratista['mainCompanyRut']== 92580000 OR $contratista['mainCompanyRut']== 96806980 OR $contratista['mainCompanyRut']== 96672640 OR $contratista['mainCompanyRut']== 78921690 OR $contratista['mainCompanyRut']== 90635000 OR $contratista['mainCompanyRut']== 96803460 OR $contratista['mainCompanyRut']== 96521680 OR $contratista['mainCompanyRut']== 97030000){

                            $certificateState = 1;
                            $certificateObservations = "Empresa Contratista ingresada a proceso de certificación";
                        }else{

                            $certificateState = 11;
                            $certificateObservations = "Empresa Contratista inactiva a proceso de certificación";
                        }
                    }else{
                        $certificateState = 1;
                        $certificateObservations = "Empresa Contratista ingresada a proceso de certificación";

                    }

                    
                    if($contratista['certificateState'] !=  7){
                        $certificateDate = time();
                        $subcontratistaRut = "";
                        $subcontratistaName ="";
                        $subcontratistaDv = "";
                        if ($contratista['subcontratistaRut']!=""){
                            $subcontratistaRut = $contratista['subcontratistaRut'];
                            $subcontratistaName = $contratista['subcontratistaName'];
                            $subcontratistaDv = $contratista['subcontratistaDv'];
                        }

                        DB::table('Company')->insert(
                        ['rut' => $contratista['rut'], 
                         'dv' => $contratista['dv'],
                         'name' => $contratista['name'],
                         'address' => $contratista['address'],
                         'cityName' => $contratista['cityName'],
                         'phoneArea' => $contratista['phoneArea'],
                         'phoneNumber' => $contratista['phoneNumber'],
                         'representantName' => $contratista['representantName'],
                         'representantRut' => $contratista['representantRut'],
                         'representantDv' => $contratista['representantDv'],
                         'activity' => $contratista['activity'],
                         'workersNumber' => $contratista['workersNumber'],
                         'managerRut' => $contratista['managerRut'],
                         'managerDv' => $contratista['managerDv'],
                         'managerEmail' => $contratista['managerEmail'],
                         'managerName' => $contratista['managerName'],
                         'managerPhoneArea'  => $contratista['managerPhoneArea'],
                         'managerPhoneNumber'  => $contratista['managerPhoneArea'],
                         'townId' => $contratista['townId'],
                         'ccafId'  => $contratista['ccafId'],
                         'companyTypeId' => $contratista['companyTypeId'],
                         'certificateState' => $certificateState,
                         'mainCompanyRut' => $contratista['mainCompanyRut'],
                         'periodId' => $idPerido,
                         'certificateObservations' => $certificateObservations,
                         'certificateDate'=> $certificateDate,
                         'mainCompanyName' => $contratista['mainCompanyName'],
                         'regionId' => $contratista['regionId'],
                         'mutualId' => $contratista['mutualId'],
                         'ccafPercentage'=> $contratista['ccafPercentage'],
                         'mutualPercentage'=> $contratista['mutualPercentage'],
                         'center'=> $contratista['center'],
                         'subcontratistaRut' => $subcontratistaRut,
                         'subcontratistaName' => $subcontratistaName,
                         'subcontratistaDv' => $subcontratistaDv,
                         'ownerName' => $contratista['ownerName'],
                         'ownerEmail' => $contratista['ownerEmail'],
                         'companycatid' => $contratista['companycatid'],
                         'workersNumber_mujeres' => 0,
                         'workersNumber_hombres'  => 0,
                         'classserv' => $contratista['classserv'],
                         'servicioId' => $contratista['servicioId'], 
                         'contratoPaymentType'=> $contratista['contratoPaymentType'], 
                         'direccion' => $contratista['direccion'],
                         'gerencia' => $contratista['gerencia'], 
                         'tiposerv'=> $contratista['tiposerv'],
                         'addres_faena' => $contratista['addres_faena'],
                         'city_faena' => $contratista['city_faena'], 
                         'towdId_faena'=> $contratista['towdId_faena'], 
                         'regionId_faena'=> $contratista['regionId_faena'] 
                        ]); 

                        $dataUID = Contratista::latest('id')->first();

                        DB::table('CertificateHistory')->insert(
                        ['companyId' => $dataUID->id, 
                         'certificateState' => $certificateState,
                         'observations' => $certificateObservations,
                         'date1' => $certificateDate,
                         'userName' => "Ingreso automático a periodo",
                        ]);

                        $actividadCode = DB::select('SELECT activityCodeId from ActivityCodeCompany
                        where companyId = '.$contratista["id"].'');

                        if(!empty($actividadCode)){

                            foreach ($actividadCode as $actividadID) {
                                
                                DB::table('ActivityCodeCompany')->insert(
                                ['companyId' => $dataUID->id, 
                                 'activityCodeId' => $actividadID->activityCodeId,
                                ]);

                            }
                        }


                        $adminCon = DB::select('SELECT admin_name, correo_admin from xt_company_adminContrato
                        where compid = '.$contratista["id"].'');

                        if(!empty($adminCon)){

                            foreach ($adminCon as $admin) {
                                
                                DB::table('xt_company_adminContrato')->insert(
                                ['compid' => $dataUID->id,
                                 'admin_name' => $admin->admin_name, 
                                 'correo_admin' => $admin->correo_admin,
                                ]);

                            }
                        }
                    }
                }
            }


            $empresaPrincipalCreadas = Contratista::where('periodId',$idPerido)->orderBy('name', 'ASC')->get()->toArray();

            $totalPrincipales = count($empresaPrincipalCreadas);
            function periodoTexto($idPerido){

                $periodo = DB::table('Period')
                ->join('Month', 'Month.id', '=', 'Period.monthId')
                ->where(['Period.id' => $idPerido])
                ->select('Period.year','Month.name')
                ->get();

                return $periodo[0]->name."-".$periodo[0]->year;
            }
            foreach ($empresaPrincipalCreadas as  $principal) {
                $data['principal'] = $principal['mainCompanyRut'];
                $data['nombreprincipal'] =mb_strtoupper($principal['mainCompanyName']);
                $data['rut'] = $principal['rut'].'-'.$principal['dv']; 
                $data['nombre'] = mb_strtoupper($principal['name']);  
                $data['subContratistaRut'] = $principal['subcontratistaRut'].'-'.$principal['subcontratistaDv']; 
                $data['subContratistanombre'] = mb_strtoupper($principal['subcontratistaName']);  
                $data['perido'] = periodoTexto($principal['periodId']); 
                $dataVista[]= $data;
            }

            return view('crearContratista.index',compact('datosUsuarios','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','periodos','totalPrincipales','dataVista','usuarioClaroChile')); 
        }else{

            if($cuentaActualContratista > 0){

                $empresaPrincipalCreadas = Contratista::where('periodId',$idPerido)->orderBy('name', 'ASC')->get()->toArray();

                $totalPrincipales = count($empresaPrincipalCreadas);
                function periodoTexto($idPerido){

                    $periodo = DB::table('Period')
                    ->join('Month', 'Month.id', '=', 'Period.monthId')
                    ->where(['Period.id' => $idPerido])
                    ->select('Period.year','Month.name')
                    ->get();

                    return $periodo[0]->name."-".$periodo[0]->year;
                }
                foreach ($empresaPrincipalCreadas as  $principal) {
                    $data['principal'] = $principal['mainCompanyRut'];
                    $data['nombreprincipal'] =mb_strtoupper($principal['mainCompanyName']);
                    $data['rut'] = $principal['rut'].'-'.$principal['dv']; 
                    $data['nombre'] = mb_strtoupper($principal['name']);  
                    $data['subContratistaRut'] = $principal['subcontratistaRut'].'-'.$principal['subcontratistaDv']; 
                    $data['subContratistanombre'] = mb_strtoupper($principal['subcontratistaName']);  
                    $data['perido'] = periodoTexto($principal['periodId']); 
                    $dataVista[]= $data;
                }

                return view('crearContratista.index',compact('datosUsuarios','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','periodos','totalPrincipales','dataVista','usuarioClaroChile')); 

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
