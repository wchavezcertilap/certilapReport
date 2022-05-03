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
use App\CertificateHistory;
use App\DocumentoCertificacion;
use App\DocumentoObser;
use Illuminate\Http\Request;

class ObservacionesDocCertController extends Controller
{
    
    

    public function obsDoc($idDoc)
    {
        $observaciones=DocumentoObser::where('idDoc','=',$idDoc)->orderBy('observacion', 'ASC')->get(['id','observacion','status','trabajador'])->toArray(); 
        if(!empty($observaciones)){

            foreach ($observaciones as $value) {
                $data['observacion']=$value['observacion'];
                if($value['trabajador']==1){
                    $data['trabajador']='Si';    
                }else{
                     $data['trabajador']='No';
                }
                if($value['status']==1){
                    $data['status']='Activo';    
                }else{
                     $data['status']='Desactivado';
                }
               
              
                $observacion[]=$data;
            }

            return $observacion;
        }
    }
    /*
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
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
        $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
        $UsuarioPrincipal->load('usuarioDatos');
        
        //$documentos =DocumentoCertificacion::where('mainCompanyRut', '=', 0)->orWhereNull('mainCompanyRut')->orderBy('name', 'ASC')->get(['id','name','type'])->toArray();

        $documento1 =DocumentoCertificacion::orderBy('name', 'ASC')->get(['id','name','type','mainCompanyRut'])->toArray();
                    
        if(!empty($documento1[0]['id'])){

            foreach ($documento1 as  $value) {

                $empresaPrincipal = empresaPrincipal::where('rut',$value['mainCompanyRut'])->orderBy('name', 'ASC')->take(1)->get(['name'])->toArray();
                if(!empty($empresaPrincipal[0]['name'])){
                    $datosDoc['name'] = ucwords(mb_strtolower($value['name'],'UTF-8'))."-".strtoupper($empresaPrincipal[0]['name']); 
                }else{
                    $datosDoc['name'] = ucwords(mb_strtolower($value['name'],'UTF-8'));
                }

                $datosDoc['id'] = $value['id'];


                $documentos[]=$datosDoc;
                # code...
            }
        

         return view('observaciones.index',compact('datosUsuarios','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','UsuarioCertilap','documentos'));
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
        $input=$request->all();
        $observacion = $input["observacion"];
        $AfectaTra = $input["AfectaTra"];
        $idDoc = $input["idDoc"];


         DB::table('documentoObser')->insert(
                ['idDoc' => $idDoc, 
                 'observacion' => $observacion,
                 'trabajador' => $AfectaTra,
                 'status' => 1,
                ]);

        $observaciones=DocumentoObser::where('idDoc','=',$idDoc)->orderBy('observacion', 'ASC')->get(['id','observacion','trabajador','status'])->toArray(); 


        $documento =DocumentoCertificacion::where('id','=',$idDoc)->orderBy('name', 'ASC')->get(['id','name','type','mainCompanyRut'])->toArray();
        if(!empty($documento[0]['mainCompanyRut'])){
            $empresaPrincipal = empresaPrincipal::where('rut',$documento[0]['mainCompanyRut'])->orderBy('name', 'ASC')->take(1)->get(['name'])->toArray();
            $documentoTex =$documento[0]['name']."-".$empresaPrincipal[0]['name'];
        }else{
            $documentoTex =$documento[0]['name'] ;  
        }
        
        $idDoc =$documento[0]['id'] ;
        if(!empty($observaciones)){

            return view('observaciones.addObser',compact('datosUsuarios','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','UsuarioCertilap','observaciones','documentoTex','idDoc'));
        }else{
            return view('observaciones.addObser',compact('datosUsuarios','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','UsuarioCertilap','documentoTex','idDoc')); 
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

        $observaciones=DocumentoObser::where('idDoc','=',$id)->orderBy('observacion', 'ASC')->get(['id','observacion','trabajador','status'])->toArray(); 
        $documento =DocumentoCertificacion::where('id','=',$id)->orderBy('name', 'ASC')->get(['id','name','type','mainCompanyRut'])->toArray();
        if(!empty($documento[0]['mainCompanyRut'])){
            $empresaPrincipal = empresaPrincipal::where('rut',$documento[0]['mainCompanyRut'])->orderBy('name', 'ASC')->take(1)->get(['name'])->toArray();
            $documentoTex =$documento[0]['name']."-".$empresaPrincipal[0]['name'];
        }else{
            $documentoTex =$documento[0]['name'] ;  
        }
       
        $idDoc =$documento[0]['id'] ;
        if(!empty($observaciones)){
            return view('observaciones.addObser',compact('datosUsuarios','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','UsuarioCertilap','observaciones','documentoTex','idDoc'));
           
        }else{

            return view('observaciones.addObser',compact('datosUsuarios','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','UsuarioCertilap','documentoTex','idDoc'));
         
        }
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


    }

    public function store2(Request $request){
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

        $input=$request->all();
        $idObserEdit = $input['idObserEdit']; 
        $observacionEdit = $input['observacionEdit']; 
        $trabajadorEdit = $input['trabajadorEdit'];
        $estadoEdit = $input["estadoEdit"];
        DB::table('documentoObser')
        ->where('id', $idObserEdit)
        ->update(['observacion' => $observacionEdit,
                  'trabajador' => $trabajadorEdit,
                  'status' => $estadoEdit]);

        $observacion=DocumentoObser::where('id','=',$idObserEdit)->orderBy('observacion', 'ASC')->get(['id','observacion','trabajador','idDoc'])->toArray(); 
        $idDoc =$observacion[0]['idDoc'] ;
        $observaciones=DocumentoObser::where('idDoc','=',$idDoc)->orderBy('observacion', 'ASC')->get(['id','observacion','trabajador','status'])->toArray(); 
        $documento =DocumentoCertificacion::where('id','=',$idDoc)->orderBy('name', 'ASC')->get(['id','name','type','mainCompanyRut'])->toArray();
        if(!empty($documento[0]['mainCompanyRut'])){
            $empresaPrincipal = empresaPrincipal::where('rut',$documento[0]['mainCompanyRut'])->orderBy('name', 'ASC')->take(1)->get(['name'])->toArray();
            $documentoTex =$documento[0]['name']."-".$empresaPrincipal[0]['name'];
        }else{
            $documentoTex =$documento[0]['name'] ;  
        }
       

        return view('observaciones.addObser',compact('datosUsuarios','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','UsuarioCertilap','observaciones','documentoTex','idDoc'));
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
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

        $observacion=DocumentoObser::where('id','=',$id)->where('status','=',1)->orderBy('observacion', 'ASC')->get(['id','observacion','trabajador','idDoc'])->toArray(); 
        $idDoc =$observacion[0]['idDoc'] ;
        $observaciones=DocumentoObser::where('idDoc','=',$idDoc)->orderBy('observacion', 'ASC')->get(['id','observacion','trabajador','status'])->toArray(); 
        $documento =DocumentoCertificacion::where('id','=',$idDoc)->orderBy('name', 'ASC')->get(['id','name','type','mainCompanyRut'])->toArray();
        if(!empty($documento[0]['mainCompanyRut'])){
            $empresaPrincipal = empresaPrincipal::where('rut',$documento[0]['mainCompanyRut'])->orderBy('name', 'ASC')->take(1)->get(['name'])->toArray();
            $documentoTex =$documento[0]['name']."-".$empresaPrincipal[0]['name'];
        }else{
            $documentoTex =$documento[0]['name'] ;  
        }
       
        DocumentoObser::destroy($id);
        return view('observaciones.addObser',compact('datosUsuarios','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','UsuarioCertilap','observaciones','documentoTex','idDoc'));

    }
}
