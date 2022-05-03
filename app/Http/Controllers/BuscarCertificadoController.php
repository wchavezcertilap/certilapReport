<?php

namespace App\Http\Controllers;
use App\DatosUsuarioLogin;
use App\UsuarioContratista;
use App\UsuarioPrincipal;
use App\empresaPrincipal;
use App\Periodo;
use App\Month;
use App\Contratista;
use App\EstadoDocumento;
use App\Certificado;
use App\Documento;
use App\TrabajadorVerificacion;


use Illuminate\Http\Request;

class BuscarCertificadoController extends Controller
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

       
        return view('buscarCertificado.index',compact('EmpresasP','datosUsuarios','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile'));
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

        $idUsuario = session('user_id');
        if($idUsuario ==  ""){
            return view('sesion.index');
        }
        $usuarioAqua = session('user_aqua');
        $certificacion = session('certificacion');
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

        foreach ($EmpresasP as $rut){

            $ruts[] = $rut->rut;
        } 

        $input=$request->all();  
        $numeroCertificado = $input['certificado'];

        $certificados = Certificado::distinct()->where('number',$numeroCertificado)->orderBy('id', 'ASC')->get()->toArray();

        foreach ($certificados as $certificado) {

            if($datosUsuarios->type == 3){
            $datosContratista = Contratista::where('id',$certificado['companyId'])
            ->whereIn('mainCompanyRut',$ruts)->get()->toArray();
            }else{    
            $datosContratista = Contratista::where('id',$certificado['companyId'])->get()->toArray();
            }
            if(!empty($datosContratista)){
                $datos["numeroCertificado"]=$certificado['number']."-".$certificado['serial'];
                $datos["empresaPrincipalRut"]=formatRut($datosContratista[0]['mainCompanyRut']);
                $datos["empresaPrincipal"]=ucwords(mb_strtolower($datosContratista[0]['mainCompanyName'],'UTF-8'));
                $datos["empresaContratistaRut"]=$datosContratista[0]['rut']."-".$datosContratista[0]['dv'];
                $datos["empresaContratista"]=ucwords(mb_strtolower($datosContratista[0]['name'],'UTF-8'));
                $datos["empresaSubContratistaRut"]=$datosContratista[0]['subcontratistaRut']."-".$datosContratista[0]['subcontratistaDv'];
                $datos["empresaSubContratista"]=ucwords(mb_strtolower($datosContratista[0]['subcontratistaName'],'UTF-8'));
                $datos["centroCosto"]=ucwords(mb_strtolower($datosContratista[0]['center'],'UTF-8'));
                $datos["idCompany"]=$datosContratista[0]['id'];
                $datosVista[] = $datos;
            }
        }

        if(!empty($datosVista)){
            $cantidaDatos = count($datosVista);
        }else{
            $cantidaDatos = 0;
        }

        return view('buscarCertificado.index',compact('EmpresasP','datosUsuarios','certificacion','usuarioAqua','datosVista','cantidaDatos','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile'));

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
