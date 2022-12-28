<?php

namespace App\Http\Controllers;
use DB;
use Zipper;
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
use App\CargoCateDoc;
use App\SsoPeriodo;
use App\DocConfigGlobal;
use Illuminate\Http\Request;

class ZipSSOController extends Controller
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
         $totalTrabajadores = 0;
        if($idUsuario ==  ""){
            return view('sesion.index');
        }
        $certificacion = session('certificacion');
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
        $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
        $UsuarioPrincipal->load('usuarioDatos');


        foreach ($UsuarioPrincipal as $rut) {

            $rutprincipal[]=$rut['mainCompanyRut'];

            
        }

            if($datosUsuarios->type ==3){

                $EmpresasP = FolioSso::distinct()->whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['sso_mcomp_name','sso_mcomp_rut']);

                return view('zipSSO.index',compact('datosUsuarios','EmpresasP','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile')); 

            }
            if($datosUsuarios->type ==2 || $datosUsuarios->type ==1 ){

                $EmpresasP = FolioSso::distinct()->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['sso_mcomp_name','sso_mcomp_rut']);

                return view('zipSSO.index',compact('datosUsuarios','EmpresasP','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile')); 

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
         $totalTrabajadores = 0;
        if($idUsuario ==  ""){
            return view('sesion.index');
        }
        $certificacion = session('certificacion');
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
        $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
        $UsuarioPrincipal->load('usuarioDatos');


        foreach ($UsuarioPrincipal as $rut) {

            $rutprincipal[]=$rut['mainCompanyRut']; 
        }

        if($datosUsuarios->type ==3){

            $EmpresasP = FolioSso::distinct()->whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['sso_mcomp_name','sso_mcomp_rut']);
        }
        if($datosUsuarios->type ==2 || $datosUsuarios->type ==1 ){

            $EmpresasP = FolioSso::distinct()->where('sso_status',1)->orderBy('sso_mcomp_name', 'ASC')->get(['sso_mcomp_name','sso_mcomp_rut']);
        }


        $input=$request->all();  
        /*if(file_exists('F:/CertilapSysFiles_ssodocs/Descargas')) {
            unlink('F:/CertilapSysFiles_ssodocs/Descargas');
        }*/
       
        $empresaPrincipal = $input['empresaPrincipal'];
        $empresaContratista = $input['empresaContratista'];
        $folio = $input['folio'];



        $folios = FolioSso::where('id',$folio )
        ->get(['id','sso_mcomp_name','sso_mcomp_rut','sso_mcomp_dv','sso_comp_name','sso_comp_rut','sso_comp_dv','sso_subcomp_active','sso_subcomp_name','sso_subcomp_rut','sso_subcomp_dv'])->toArray();
       
        foreach ($folios as $folio) {
            $directorioDocs = "E:/CertilapSysFiles_ssodocs/";
            $directorioDocsFolio = "E:/CertilapSysFiles_ssodocs/".$folio['id']."/";
            $directorioCreadoDocs = "E:/CertilapSysFiles_ssodocs/Descargas/";

            $documentosGlobalesSubidos = EstadoDocumento::join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_header_uploads.upld_docid')
            ->where('upld_sso_id',$folio['id'])->where('upld_status', 1)->where('upld_type',0)
            ->orderBy('id', 'DESC')
            ->get(['xt_ssov2_header_uploads.id', 'xt_ssov2_header_uploads.upld_hash', 'xt_ssov2_doctypes.doc_name'])->toArray();

          
            if(isset($documentosGlobalesSubidos[0]['id'])){

                $directorioFolio = "H:/CertilapSysFiles_ssodocs/Descargas/".$folio['id'];
                if(!file_exists($directorioFolio)) {
                    mkdir($directorioFolio, 777, true);
                    $directorioFolioGlobal = "H:/CertilapSysFiles_ssodocs/Descargas/".$folio['id']."/Globales_".$folio['id'];
                    if(!file_exists($directorioFolioGlobal)) {
                        mkdir($directorioFolioGlobal, 777, true);
                    }
                }

                foreach ($documentosGlobalesSubidos as $documentosGlobalSubido) {
                    $docGl = explode(".", $documentosGlobalSubido['upld_hash']);
                    $extencionGl =$docGl[1];

                    if(file_exists($directorioDocs.$documentosGlobalSubido['upld_hash'])){

                        copy($directorioDocs.$documentosGlobalSubido['upld_hash'], $directorioFolioGlobal."/".$documentosGlobalSubido['upld_hash']);
                    }else{
                       
                        if(file_exists($directorioDocsFolio.$documentosGlobalSubido['upld_hash'])){
                          
                            copy($directorioDocsFolio.$documentosGlobalSubido['upld_hash'], $directorioFolioGlobal."/".$documentosGlobalSubido['upld_hash']);  
                        }
                    }

                    if(file_exists($directorioFolioGlobal."/".$documentosGlobalSubido['upld_hash'])){
                        $nombreDocumentoGl= trim($documentosGlobalSubido['doc_name'], ".");
                        $nombreDocumentoGl = str_replace('/', '', $nombreDocumentoGl);
                        $nombreDocumentoGl2 = str_replace(' ', '_', $nombreDocumentoGl);
                        rename($directorioFolioGlobal."/".$documentosGlobalSubido['upld_hash'],$directorioFolioGlobal."/".strtoupper($nombreDocumentoGl2).'.'.$extencionGl);


                    }


                }

               
            }
            unset($documentosTrabajadorSubidosT);
            $documentosTrabajadorSubidosT = EstadoDocumento::join('xt_ssov2_doctypes', 'xt_ssov2_doctypes.id', '=', 'xt_ssov2_header_uploads.upld_docid')->
            join('xt_ssov2_header_worker', 'xt_ssov2_header_worker.id', '=', 'xt_ssov2_header_uploads.upld_workerid')
            ->where('upld_sso_id',$folio['id'])->where('upld_status', 1)->where('upld_type',1)
            ->orderBy('xt_ssov2_header_worker.id', 'DESC')
            ->get(['xt_ssov2_header_uploads.id', 'xt_ssov2_header_uploads.upld_hash', 'xt_ssov2_doctypes.doc_name','xt_ssov2_header_worker.worker_name','xt_ssov2_header_worker.worker_rut','xt_ssov2_header_worker.worker_rut'])->toArray();

            

            $directorioFolio = "H:/CertilapSysFiles_ssodocs/Descargas/".$folio['id'];
            if(!file_exists($directorioFolio)) {
                mkdir($directorioFolio, 777, true);
                $directorioFolioTrabajadores = "H:/CertilapSysFiles_ssodocs/Descargas/".$folio['id']."/Trabajadores_".$folio['id'];
                if(!file_exists($directorioFolioTrabajadores)) {
                    mkdir($directorioFolioTrabajadores, 777, true);
                }else{
                   $directorioFolioTrabajadores = "F:/CertilapSysFiles_ssodocs/Descargas/".$folio['id']."/Trabajadores_".$folio['id']; 
                }
            }else{
               $directorioFolio = "H:/CertilapSysFiles_ssodocs/Descargas/".$folio['id']; 

               $directorioFolioTrabajadores = "H:/CertilapSysFiles_ssodocs/Descargas/".$folio['id']."/Trabajadores_".$folio['id'];
                if(!file_exists($directorioFolioTrabajadores)) {
                    mkdir($directorioFolioTrabajadores, 777, true);
                }else{
                   $directorioFolioTrabajadores = "H:/CertilapSysFiles_ssodocs/Descargas/".$folio['id']."/Trabajadores_".$folio['id']; 
                }
            }

             
            if(isset($documentosTrabajadorSubidosT[0]['upld_hash'])){

                foreach ($documentosTrabajadorSubidosT as $documentosTrabajadorSubidos) {

                    $doc = explode(".", $documentosTrabajadorSubidos['upld_hash']);
                    $extencion =$doc[1];
                    
                    

                    if(file_exists($directorioDocs.$documentosTrabajadorSubidos['upld_hash'])){

                       

                        copy($directorioDocs.$documentosTrabajadorSubidos['upld_hash'], $directorioFolioTrabajadores."/".$documentosTrabajadorSubidos['upld_hash']);
                    }else{
                       
                        if(file_exists($directorioDocsFolio.$documentosTrabajadorSubidos['upld_hash'])){
                          
                            copy($directorioDocsFolio.$documentosTrabajadorSubidos['upld_hash'], $directorioFolioTrabajadores."/".$documentosTrabajadorSubidos['upld_hash']);

                            
                        }else{
                           

                        }

                    }

                    if(file_exists($directorioFolioTrabajadores."/".$documentosTrabajadorSubidos['upld_hash'])){

                        
                        $nombreTrabajador = str_replace(' ', '_', $documentosTrabajadorSubidos['worker_name']);


                        $nombreDocumento= trim($documentosTrabajadorSubidos['doc_name'], ".");
                        $nombreDocumentoTl = str_replace('/', '', $nombreDocumento);
                        $nombreDocumentoT2 = str_replace(' ', '_', $nombreDocumentoTl);

                        rename($directorioFolioTrabajadores."/".$documentosTrabajadorSubidos['upld_hash'],$directorioFolioTrabajadores."/".strtoupper($documentosTrabajadorSubidos['worker_rut'].'_'.$nombreTrabajador.'_'.$nombreDocumentoT2).'.'.$extencion);


                    }

                   
                }
                
            }    
        }



    if(file_exists($directorioFolioTrabajadores)){

        $carpeta = @scandir($directorioFolioTrabajadores);
        
        $CANTIDADC = count($carpeta);
        
        if($CANTIDADC > 2){
            //Creamos el archivo
            $zip = new \ZipArchive();

            //abrimos el archivo y lo preparamos para agregarle archivos
            $zip->open("DOCSSO_".$folio['id'].".zip", \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

            //indicamos cual es la carpeta que se quiere comprimir
            $origen = realpath('H:/CertilapSysFiles_ssodocs/Descargas');

                //Ahora usando funciones de recursividad vamos a explorar todo el directorio y a enlistar todos los archivos contenidos en la carpeta
                $files = new \RecursiveIteratorIterator(
                            new \RecursiveDirectoryIterator($origen),
                            \RecursiveIteratorIterator::LEAVES_ONLY
                );

                //Ahora recorremos el arreglo con los nombres los archivos y carpetas y se adjuntan en el zip
                foreach ($files as $name => $file)
                {
                   if (!$file->isDir())
                   {
                       $filePath = $file->getRealPath();
                       $relativePath = substr($filePath, strlen($origen) + 1);

                       $zip->addFile($filePath, $relativePath);
                   }
                }

                //Se cierra el Zip
                $zip->close();

                ///unlink('H:/CertilapSysFiles_ssodocs/Descargas/'.$folio['id']);
                $lines = array();
                //exec("DEL /F/Q "H:/CertilapSysFiles_ssodocs/Descargas"", $lines);
                
                /* Por último, si queremos descarlos, indicaremos la ruta del archiv, su nombre
                y lo descargaremos*/
                return response()->download('C:/inetpub/certilapReport/public/DOCSSO_'.$folio['id'].'.zip');
        }else{
            $sindatos = 1;
            return view('zipSSO.index',compact('datosUsuarios','EmpresasP','certificacion','usuarioAqua','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile','sindatos')); 
        }
    }

        
        
        //return response()->download('C:/inetpub/certilapReport/storage/DOCSSO.zip');
    
       // $directorioFolio = $directorioCreadoDocs.$value["id"];
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
