<?php

namespace App\Http\Controllers;
use App\DatosUsuarioLogin;
use App\UsuarioContratista;
use App\UsuarioPrincipal;
use App\FolioSso;

use Illuminate\Http\Request;


class datosEstadisticoController extends Controller
{
    
	public function index()
    {
        
        
    }

    public function valida($idUser, $folio)
    {
       
	    $idfolio   = rtrim(base64_decode($folio));
		$idUsuario = rtrim(base64_decode($idUser));

		$datosUsuarios = DatosUsuarioLogin::find($idUsuario);
		$datosUsuarios->load('cargaUsuarioContratista');


		$UsuarioPrincipal = UsuarioPrincipal::where('systemUserId','=',$idUsuario)->get();
		$UsuarioPrincipal->load('usuarioDatos');


		foreach ($UsuarioPrincipal as $rut) {

			$rutprincipal[]=$rut['mainCompanyRut'];
			
		}

		$UsuarioContratista = UsuarioContratista::where('systemUserId','=',$idUsuario)->get();
		$UsuarioContratista->load('usuarioDatos');

		foreach ($UsuarioContratista as $rut) {

			$rutcontratista[]=$rut['companyRut'];
			# code...
		}
		
		$datosFolio = FolioSso::find($idfolio);

		/*echo "<pre>";
		print_r($datosFolio);
		echo "</pre>";*/

		$meses = array('enero','febrero','marzo','abril','mayo','junio','julio',
               'agosto','septiembre','octubre','noviembre','diciembre');
		$year = array('2014','2015','2016','2017','2018','2019','2020',
               '2021','2022','2023','2024');
		return view('datosEstadistico.index',compact('datosUsuarios','meses','year','datosFolio'));
    }

    public function create(Request $request)
    {
        print_r($request);
    }



}
