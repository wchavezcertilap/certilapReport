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
use App\EstadoDocumento;
use App\trabajadorSSO;
use App\Documento;
use App\TrabajadorVerificacion;
use App\AccesoPersona;
use App\Productividad;
use Illuminate\Http\Request;



class ProductividadCertController extends Controller
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
        $UsuarioCertilap =DatosUsuarioLogin::where('type','=',2)->whereNotIn('id', [19,445,1938,2000,3205,3222,4661,15295,25517,25901,26154,26204,26571,26869,27118,27536,27730,28459,28996,4788,4954,4651,4653,4954,39324,39176,39208,27375,26610,39207,60089,60252,49993,60134,49991,60174])->orderBy('name', 'ASC')->get()->toArray();
        $block=0;
        $fecha = date('Y-m-d');
        $registros = Productividad::where('fecha',$fecha)->get();

        $totalreg = count($registros);
            
        if($totalreg > 0){
        foreach ($registros as $value) {

               
                $datosUsuariosV = DatosUsuarioLogin::find($value->idUsuario);
                $datos['usuario'] = ucwords(mb_strtolower($datosUsuariosV->name,'UTF-8'));
                $datos['numCentro'] = $value->numCentro;
                $datos['numTrab'] = $value->numTrab;
                $datos['numCentro2'] = $value->numCentro2;
                $datos['numtraba2'] = $value->numtraba2;
                $datos['fecha'] = $value->fecha;

                $datosVista[] =  $datos; 
            }

            $block=1;
        }

        return view('productividad.index',compact('datosUsuarios','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','UsuarioCertilap','datosVista','block','usuarioClaroChile'));
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
        $usuarioClaroChile= session('user_Claro');
        $usuarioNOKactivo = session('usuario_nok');
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
        $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
        $UsuarioPrincipal->load('usuarioDatos');
        $UsuarioCertilap =DatosUsuarioLogin::where('type','=',2)->whereNotIn('id', [19,25,445,1938,2000,3205,3222,4661,15295,25517,25901,26154,26204,26571,26869,27118,27536,27730,28459,28996,4788,4954,4651,4653,4954,39324,39176,39208,27375,26610,39207,60089,60252,49993,60134,49991,60174])->orderBy('name', 'ASC')->get()->toArray();
        $usuariost = count($UsuarioCertilap);
       
        $idUsuarioR = "";
        $centro = "";
        $centro2 = "";
        $trabajadores = "";
        $trabajadores2 = "";
        for ($i=0; $i < $usuariost ; $i++) { 

            $idUsuarioR = $_POST['usuario_'.$i];
            $centro = $_POST['centro_'.$idUsuarioR];
            $centro2 = $_POST['centro2_'.$idUsuarioR];
            $trabajadores = $_POST['trabajadores_'.$idUsuarioR];
            $trabajadores2 = $_POST['trabajadores2_'.$idUsuarioR];

            DB::table('productividadCert')->insert(
            ['idUsuario' => $idUsuarioR, 
             'numCentro' => $centro,
             'numTrab' => $trabajadores,  
             'numCentro2' => $centro2,
             'numtraba2' => $trabajadores2,
             'fecha' => date('Y-m-d'),
             'idUserReg' => $idUsuario
            ]);
                 
        } 
        $fecha = date('Y-m-d');
        $registros = Productividad::where('fecha',$fecha)->get();
    
        foreach ($registros as $value) {

               
                $datosUsuarios = DatosUsuarioLogin::find($value->idUsuario);
                $datos['usuario'] = ucwords(mb_strtolower($datosUsuarios->name,'UTF-8'));
                $datos['numCentro'] = $value->numCentro;
                $datos['numTrab'] = $value->numTrab;
                $datos['numCentro2'] = $value->numCentro2;
                $datos['numtraba2'] = $value->numtraba2;
                $datos['fecha'] = $value->fecha;

                $datosVista[] =  $datos; 
            }

     
        return view('productividad.index',compact('datosUsuarios','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','UsuarioCertilap','datosVista','usuarioClaroChile'));
      
       
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
