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

use Illuminate\Http\Request;

class HabilitarFolioController extends Controller
{
    
    public function index()
    {
        $idUsuario = session('user_id');
        $usuarioAqua = session('user_aqua');
        $usuarioABBChile= session('user_ABB');
        $usuarioNOKactivo = session('usuario_nok');
        if($idUsuario ==  ""){
            return view('sesion.index');
        }
        $certificacion = session('certificacion');
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
        $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
        $UsuarioPrincipal->load('usuarioDatos');


            if($datosUsuarios->type ==2 || $datosUsuarios->type == 1){

                return view('habilitarFolio.index',compact('datosUsuarios','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo')); 

            }
    }

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


        $input=$request->all();

        $folio = $input["folio"];
        $data = FolioSso::where('id',$folio)->where('sso_status',0)
                ->update(['sso_status' => 1]);
        $actualizado = 1;
        return view('habilitarFolio.index',compact('datosUsuarios','actualizado','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo')); 

    }
}
