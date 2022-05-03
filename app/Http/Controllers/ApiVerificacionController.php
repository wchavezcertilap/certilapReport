<?php

namespace App\Http\Controllers;
use DB;
use App\Periodo;
use App\Month;
use App\Contratista;
use App\EstadoDocumento;
use App\Certificado;
use App\Documento;
use App\TrabajadorVerificacion;

use Illuminate\Http\Request;

class ApiVerificacionController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('client.credentials')->only(['getApiVL']);
    }
    /**
     * devuelve los datos consultado por la API.
     *
     * @return \Illuminate\Http\Response
     */
    public function getApiVL($rutContratista,$rutTrabajador)
    {

        
        function estadoCerficacionTexto($idEstadoCert){

            switch ((int)$idEstadoCert) {
                case 1:
                    return $estadoCerficacionTexto ="Ingresado";
                    break;
                case 2:
                    return $estadoCerficacionTexto ="Solicitado";
                    break;
                case 3:
                    return $estadoCerficacionTexto ="Aprobado";
                    break;
                case 4:
                    return $estadoCerficacionTexto ="No Aprobado";
                    break;
                case 5:
                    return $estadoCerficacionTexto ="Certificado";
                    break;
                case 6:
                    return $estadoCerficacionTexto ="Documentado";
                    break;
                case 7:
                    return $estadoCerficacionTexto ="Histórico";
                    break;
                case 8:
                    return $estadoCerficacionTexto ="Completo";
                    break;
                case 9:
                    return $estadoCerficacionTexto ="En Proceso";
                    break;
                case 10:
                    return $estadoCerficacionTexto ="No Conforme";
                    break;
                case 11:
                    return $estadoCerficacionTexto ="Inactivo";
                    break;
            }
        }

        function periodoTexto($idPerido){

            $periodo = DB::table('Period')
            ->join('Month', 'Month.id', '=', 'Period.monthId')
            ->where(['Period.id' => $idPerido])
            ->select('Period.year','Month.name')
            ->get();

            return $periodo[0]->name."-".$periodo[0]->year;
        }

        $estadoTrabajador= DB::table('Company')
        ->join('Worker', function ($join){
            $join->on('Worker.mainCompanyRut','=','Company.mainCompanyRut')
                ->on('Worker.companyRut','=','Company.rut')
                ->on('Worker.periodId','=','Company.periodId');
        })
        ->where('Company.mainCompanyRut','78921690')
        ->where('Company.rut',$rutContratista)
        ->where('Worker.rut', $rutTrabajador)
        ->orderBy('Company.periodId', 'DESC')
        ->take(1)
        ->get(['Company.id as idComp','Company.rut as rutComp','Company.dv as dvComp','Company.name as nameComp','Company.mainCompanyName','Company.companyTypeId','Company.mainCompanyRut','Company.center','Company.certificateState','Company.certificateDate','Company.periodId','Company.subcontratistaRut','Company.subcontratistaName','Company.subcontratistaDv','Worker.rut','Worker.dv','Worker.names','Worker.firstLastName','Worker.secondLastName'])->toArray();
                

        if(!empty($estadoTrabajador[0])){
            foreach ($estadoTrabajador as $value) {

                $fechaCertificiacion=date('d/m/Y', $value->certificateDate);
                $peridoTex = periodoTexto($value->periodId);
                $estadoCertificado=estadoCerficacionTexto($value->certificateState);
                $datos['codigo']= 1;
                $datos['mensaje']= 'Exito';
                $datos['estadoCertificado']=ucwords(mb_strtolower($estadoCertificado,'UTF-8'));
                $datos['fechaCertificacion']=$fechaCertificiacion;
                $datos['PeriodoCertificacion']=ucwords(mb_strtolower($peridoTex,'UTF-8'));
                
            }
            return response()->json($datos);
        }else{

            $estadoTrabajador= DB::table('Company')
            ->join('Worker', function ($join){
                $join->on('Worker.mainCompanyRut','=','Company.mainCompanyRut')
                    ->on('Worker.companyRut','=','Company.rut')
                    ->on('Worker.periodId','=','Company.periodId');
            })
            ->orderBy('Company.periodId', 'DESC')
            ->take(1)
            ->get(['Company.id as idComp','Company.rut as rutComp','Company.dv as dvComp','Company.name as nameComp','Company.mainCompanyName','Company.companyTypeId','Company.mainCompanyRut','Company.center','Company.certificateState','Company.certificateDate','Company.periodId','Company.subcontratistaRut','Company.subcontratistaName','Company.subcontratistaDv','Worker.rut','Worker.dv','Worker.names','Worker.firstLastName','Worker.secondLastName'])->toArray();

            if(!empty($estadoTrabajador[0])){

                foreach ($estadoTrabajador as $value) {

                    $fechaCertificiacion=date('d/m/Y', $value->certificateDate);
                    $peridoTex = periodoTexto($value->periodId);
                    $estadoCertificado=estadoCerficacionTexto($value->certificateState);
                    $datos['codigo']= 1;
                    $datos['mensaje']= 'Exito';
                    $datos['estadoCertificado']=ucwords(mb_strtolower($estadoCertificado,'UTF-8'));
                    $datos['fechaCertificacion']=$fechaCertificiacion;
                    $datos['PeriodoCertificacion']=ucwords(mb_strtolower($peridoTex,'UTF-8'));
                    $datos['Contratista']=ucwords(mb_strtolower($value->nameComp,'UTF-8'));
                    
                }
                return response()->json($datos);
            }else{
                $datos['codigo']= 0;
                $datos['mensaje']="Rut no encotrado";
                return response()->json($datos);
            }
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
}
