<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('admin', function () {
    return view('admin_template');
});


Route::resource('reporteAquaIntegrado', 'ReporteAquaIntegradoController');
Route::Get('porContratistaAqua/{id}', 'ReporteAquaIntegradoController@porContratistaAqua');
Route::resource('reporteAquaAcreditado', 'ReporteAquaAcreditado');
Route::Get('porContratistaAquaSSO/{id}', 'ReporteAquaAcreditado@porContratistaAquaSSO');
Route::resource('menuHijo', 'menuHijoController');
Route::resource('reporteCompleto', 'reporteCompletoController');
Route::resource('Home', 'HomeController');
Route::resource('documentoReporte', 'DocumentoReporteController');
Route::resource('reporteAcreditacion', 'ReporteAcreditacionController');
Route::Get('porContratista/{id}', 'reporteCompletoController@porContratista');
Route::Get('porContratista/{id}', 'DocumentoReporteController@porContratista');
Route::Get('porFolio/{id}', 'DocumentoReporteController@porFolio');
Route::Get('porProyecto/{id}', 'DocumentoReporteController@porProyecto');
Route::Get('porContratista/{id}', 'ReporteAcreditacionController@porContratista');
Route::Get('porFolio/{id}', 'ReporteAcreditacionController@porFolio');
Route::Get('porProyecto/{id}', 'ReporteAcreditacionController@porProyecto');
Route::Get('porCategoria/{id}', 'ReporteAcreditacionController@porCategoria');
Route::Get('porSubContratista/{id}/{idCat}', 'ReporteAcreditacionController@porSubContratista');
Route::Get('centroCosto/{rut}/{empresaPrincipal}', 'reporteCompletoController@centroCosto');
//Route::post('create', 'datosEstadistico@create');
Route::get('inicio/{id}', 'HomeController@inicio');
Route::get('inicio/ssoTrabajadores/{id}', 'HomeController@ssoTrabajadores');
Route::get('inicio/ssoFolios/{id}', 'HomeController@ssoFolios');
Route::get('inicio/ssoDocumentos/{id}', 'HomeController@ssoDocumentos');
Route::get('inicio/ssoEmpresas/{id}', 'HomeController@ssoEmpresas');
Route::resource('reporteCertificacion', 'ReporteCertificacionController'); 
Route::Get('porContratista/{id}', 'ReporteCertificacionController@porContratista');
Route::Get('porCentroCosto/{contratista}/{principal}/{peridoInicio}/{peridoFinal}/{fechaSeleccion}', 'ReporteCertificacionController@porCentroCosto');
//Route::resource('bloquearContratista', 'BloquearContratistaController');
Route::resource('reporteCertificacion', 'ReporteCertificacionController'); 
/*Route::resource('generarOc', 'GenerarOcController');
Route::get('procesarOc', 'GenerarOcController@procesarOc')->name('generarOc.procesarOc'); 
Route::post('procesarOc', 'GenerarOcController@procesarOc');
Route::Get('empresasPago/{tipoEmpresa}/{tipoPago}/{fechaSeleccion}', 'GenerarOcController@empresasPago');*/
Route::resource('reporteRotacion', 'ReporteRotacion');
Route::Get('porCentroCostoRotacion/{contratista}/{principal}/{peridoInicio}/{peridoFinal}/{fechaSeleccion}', 'ReporteRotacion@porCentroCostoRotacion');
Route::Get('porContratistaRotacion/{id}', 'ReporteRotacion@porContratistaRotacion');
Route::resource('cambiarCiclo', 'CambiarCicloController');
Route::resource('trabajadoresPagadosPre', 'TrabajadoresPagadosPreController');
Route::resource('reporteCumplimientoAqua', 'ReporteCumplimientoAqua'); 
Route::resource('buscarCertificado', 'BuscarCertificadoController');  
Route::resource('reporteTrabajadoresSsoAcre', 'ReporteTrabajadoresSsoAcre');
Route::resource('reporteCertificacionGrafica', 'ReporteCertificacionGraficaController'); 
Route::resource('porcentajeCumplimientoSSO', 'PorcentajeCumplimientoSSOController');
Route::resource('certificadoMasivo', 'CertificadoMasivoController');

Route::resource('usuarioDesactiva', 'UsuarioDesactivarController');
Route::get('desactiva', 'UsuarioDesactivarController@desactiva')->name('usuarioDesactiva.desactiva'); 
Route::post('desactiva', 'UsuarioDesactivarController@desactiva');

Route::resource('historialSso', 'HistorialSsoController');
Route::get('descargar/{filename}', 'HistorialSsoController@descargar');
Route::resource('reporteFTE', 'ReporteFTEController'); 

Route::resource('reporteExtranjero', 'ReporteExtranjeroController'); 
Route::Get('porCentroCostoExt/{contratista}/{principal}/{peridoInicio}/{peridoFinal}', 'ReporteExtranjeroController@porCentroCostoExt');
///controlador reportes excel desde edit sso2 ///
Route::resource('ReporteExcelSso', 'ReporteExcelSsoController');
Route::get('reporteEjecutivoSSO/{idfolio}', 'ReporteExcelSsoController@reporteEjecutivoSSO');

Route::resource('reporteFactCert', 'ReporteFactCert');

Route::resource('productividadCert','ProductividadCertController');
Route::resource('reporteObsCert','ReporteObsCertController');

Route::resource('reporteCSA','ReporteCSAController');

Route::resource('observacionesDoc','ObservacionesDocCertController');
Route::get('obsDoc/{id}', 'ObservacionesDocCertController@obsDoc');
Route::get('store2', 'ObservacionesDocCertController@store2')->name('observacionesDoc.store2');
Route::post('store2', 'ObservacionesDocCertController@store2');

Route::resource('reporteSSOClaro','ReporteSSOClaroController');

Route::resource('informeBE', 'InformeBEController');
Route::resource('habilitarFolio', 'HabilitarFolioController');
Route::resource('habilitarFolio', 'HabilitarFolioController');
Route::resource('reporteSSOClaro','ReporteSSOClaroController');
Route::resource('informeCer','InformeCerController');

