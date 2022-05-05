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
use App\ConfiguracionSso;
use App\CategoriaDocSso;
use App\CategoriaConfSso;
use App\CategoriaCargoSso;
use Illuminate\Http\Request;

class ReporteAcreditacionController extends Controller
{
    public function porContratista($id){
        
        return Contratista::distinct()->where('mainCompanyRut','=',$id)->orderBy('name', 'ASC')->get(['name','rut']);  
    }

    public function porFolio($id){
        
        return FolioSso::distinct()->where('sso_mcomp_rut','=',$id)->orderBy('id', 'ASC')->get(['id']);
    }


    public function porProyecto($id){
        
        return FolioSso::distinct()->where('sso_mcomp_rut','=',$id)->whereNotNull('sso_project')->orderBy('sso_project', 'ASC')->get(['sso_project']);
    }

    public function porCategoria($id){
        
        return ConfiguracionSso::distinct()->where('cfg_mcomp_rut','=',$id)->where('cfg_status',1)->orderBy('cfg_desc', 'ASC')->get(['id','cfg_desc']);
    }

    public function porSubContratista($id,$idCat){
        
       $idcategorias = CategoriaConfSso::where('cfg_id','=',$idCat)->get(['cfg_id','cargo_id']);
       $subcategoria = CategoriaCargoSso::where('cfg_id','=',$idcategorias[0]['cfg_id'])->where('cargo_id','=',$idcategorias[0]['cargo_id'])->orderBy('cat_order', 'DESC')->get(['cat_id','cat_order']);
       foreach ($subcategoria as  $value) {

             $listaCat = CategoriaDocSso::distinct()->where('id','=',$value['cat_id'])->get(['id','cat_name']);
           $listaCategoria['id']=$listaCat[0]['id'];
           $listaCategoria['name']=$listaCat[0]['cat_name'];
           $comboSubCate[] = $listaCategoria;
       }
       
       return $comboSubCate;
      
    }


    public function index(Request $request) 
    {
        $usuarioAqua = session('user_aqua');
        $certificacion = session('certificacion');
        $usuarioABBChile= session('user_ABB');
        $usuarioClaroChile= session('user_Claro');
        $usuarioNOKactivo = session('usuario_nok');
        $idUsuario = session('user_id');
        if($idUsuario ==  ""){
            return view('sesion.index');
        }
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);

            if($datosUsuarios->type ==2 or $datosUsuarios->type ==3){

                $EmpresasP = empresaPrincipal::distinct()->orderBy('name', 'ASC')->get(['name','rut']);

                foreach ($EmpresasP as $value) {
                    $rutprincipal[] = $value['rut'];
                }

              
                $folios = FolioSso::whereIn('sso_mcomp_rut',$rutprincipal)->where('sso_status', '1')->get();
                foreach ($folios as $value) {
                    $foliosActivos[] = $value['id']; 
                }
                $totalFolios =count($foliosActivos);
                $totalFolios = number_format($totalFolios);
                $totalDocuementos = EstadoDocumento::where('upld_status','1')->count();
                $totalDocuementos = number_format($totalDocuementos);
                $totalTrabajadores = trabajadorSSO::where('worker_status','1')->count();
                $totalTrabajadores = number_format($totalTrabajadores);
                $totalEmpresasPriSSO = FolioSso::distinct()->get(['sso_mcomp_rut']);
                $totalEmpresasPriSSO = count($totalEmpresasPriSSO);
             //  $documentos = EstadoDocumento::whereIn('upld_sso_id',$folioP1)->where('upld_status','1')->get();
              
                 
                /*$documentos = EstadoDocumento::whereIn('upld_sso_id',$foliosActivos)->where('upld_status','1')->get();
            number_format
               
               echo "<pre>";
               echo print_r($folioP1);
               echo "</pre>";*/
            }
            $EmpresasP = FolioSso::distinct()->orderBy('sso_mcomp_name', 'ASC')->get(['sso_mcomp_rut','sso_mcomp_name']);

            
            $UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
            $UsuarioContratista = UsuarioContratista::where('systemUserId','=',$idUsuario)->get();
            $UsuarioContratista->load('usuarioDatos');


      

        foreach ($UsuarioContratista as $rut) {

            $rutcontratista[]=$rut['companyRut'];
            # code...
        }


        foreach ($UsuarioPrincipal as $rut) {

            $rutprincipal[]=$rut['mainCompanyRut'];
            
        }
  
         return view('reporteAcreditacion.index',compact('datosUsuarios','EmpresasP','totalFolios','totalDocuementos','totalTrabajadores','totalEmpresasPriSSO','EmpresasP','usuarioAqua','certificacion','usuarioABBChile','usuarioNOKactivo','usuarioClaroChile')); 
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
        $usuarioAqua = session('user_aqua');
        $certificacion = session('certificacion');
        $usuarioNOKactivo = session('usuario_nok');
        $idUsuario = session('user_id');
        if($idUsuario ==  ""){
            return view('sesion.index');
        }
        $datosUsuarios = DatosUsuarioLogin::find($idUsuario);
        $input=$request->all();
        //print_r($input);
        $totalFolios = $input["totalFolios"];
        $totalDocuementos = $input["totalDocuementos"];
        $totalTrabajadores = $input["totalTrabajadores"];
        $totalEmpresasPriSSO = $input["totalEmpresasPriSSO"];
        $empresasPrincipales = $input["empresaPrincipal"];
        $tipoInforme = $input["subCategoria"];
        $fechaSeleccion = $input["fechaSeleccion"];
        $totalTB = 0;
        $totalDoc = 0;
        $totalDocRechazados = 0;
        $totalDocAprobados = 0;
        $totalDocVencidos = 0;
        $totalDocRevision = 0;
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
