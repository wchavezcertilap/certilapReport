<?php

namespace App\Http\Controllers;
use App\DatosUsuarioLogin;
use App\UsuarioContratista;
use App\UsuarioPrincipal;
use App\FolioSso;
use App\SsoPeriodo;

use Illuminate\Http\Request;

class HistorialSsoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $certificacion = session('certificacion');
        $usuarioAqua = session('user_aqua');
        $usuarioABBChile= session('user_ABB');
        $usuarioClaroChile= session('user_Claro');
        $usuarioNOKactivo = session('usuario_nok');
        $idUsuario = session('user_id');
        if($idUsuario ==  ""){
            return view('sesion.index');
        }
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
        $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
        $UsuarioPrincipal->load('usuarioDatos');

        foreach ($UsuarioPrincipal as $rut) {

            $rutprincipal[]=$rut['mainCompanyRut'];
            
        }

        if($datosUsuarios->type == 3){

            $EmpresasP = FolioSso::distinct()->whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_rut','sso_subcomp_name','sso_subcomp_dv']);

        }
        if($datosUsuarios->type == 2 or $datosUsuarios->type ==1){

          $EmpresasP = FolioSso::distinct()->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_rut','sso_subcomp_name','sso_subcomp_dv']);

        }
        // echo "<pre>";
        // print_r($EmpresasP);
        // echo "</pre>";

        if(!empty($EmpresasP)){
            $fechaHoy = time ();
            foreach ($EmpresasP as $value) {

                $historialSso = SsoPeriodo::where('activo',1)->where('sso_id',$value['id'])->orderby('id','DESC')->get()->toArray();
                if(!empty($historialSso)){

                        if($historialSso[0]['sso_fecha_vence'] <= $fechaHoy){
                            $datosLista['folio'] = $value['id'];
                            $datosLista['principal'] = ucwords(mb_strtolower($value['sso_mcomp_name']));
                            $datosLista['rutPrincipal'] = $value['sso_mcomp_rut']."-".$value['sso_mcomp_dv'];
                            $datosLista['contratista'] = ucwords(mb_strtolower($value['sso_comp_name']));
                            $datosLista['rutContratista'] = $value['sso_comp_rut']."-".$value['sso_comp_dv'];
                            if($value['sso_subcomp_active'] == 1){
                                $datosLista['subContratista'] = $value['sso_subcomp_name'];
                                $datosLista['rutsubContratista'] = ucwords(mb_strtolower($value['sso_subcomp_rut']."-".$value['sso_subcomp_dv']));
                            }else{
                                $datosLista['subContratista'] = "";
                                $datosLista['rutsubContratista'] = "";
                            }
                            $datosLista['fechaIncio'] = date('d/m/Y',$historialSso[0]['sso_fecha_inicio']);
                            $datosLista['fechaFin'] = date('d/m/Y',$historialSso[0]['sso_fecha_vence']);
                            $datosLista['archivo'] = 'estadoCertficacionSSO_'.$value['id'].'_'.$historialSso[0]['sso_fecha_vence'].'.xlsx';
                            $listaDatos[] = $datosLista;    
                        }
                }
                
                
            }
        }

       
            return view('historialSso.index',compact('datosUsuarios','EmpresasP','listaDatos','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile')); 
        
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

    public function descargar($filename)
    {
        $directorio = "E:/CertilapSysFiles_ssodocs/ABB/".$filename;
        //echo $directorio;
        if(file_exists($directorio)){
          header("Content-Type:");
          header("Content-disposition: attachment; filename=\"{$filename}\"");
          header('Expires: 0');
          header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
          header('Pragma: no-cache');
          header('Content-Length: ' . filesize($directorio));
          ob_clean();
          flush();
          if(file_exists($directorio))
             readfile($directorio);
        }
    }
}
