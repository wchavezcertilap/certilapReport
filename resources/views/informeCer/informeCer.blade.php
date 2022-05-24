<html>
<head>

<style>
    .page
    {
    	page-break-after: always;
        page-break-inside: avoid;
        
    }

	.row-certilap {
		background-color: #0984e3;
		color:white;
	}
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="{{ asset("/js/chartjs-plugin-doughnutlabel.js") }}"></script>
<script src="{{ asset("/js/chartjs-plugin-piechart-outlabels.min.js") }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
</head>
<div class="page">
	<h1 class="text-center">
		Informe Estad&iacute;sticas Contratistas y Subcontratistas Banco Estado mes {{ $mes }} - A&ntilde;o {{ $anio }}
	</h1>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<div class="col-12">
		<img src="{{ asset("/img/certilap_pdf_portrait.png") }}" alt="">
	</div>
</div>
<div class="page">
	<div class="col-12">
		<h4><center>INFORME DE RIESGOS DE CERTIFICACION LABORAL</center></h4>
	</div>
	<br>
	<br>
	<div class="col-12">
		<p><strong>Demora inicio certificaci&oacute;n:</strong> la demora en el inicio de la certificaci&oacute;n puede tener incidencia en no contar en los plazos con el certificado necesario para demostrar el umplimiento laboral.</p>
	</div>
	<div class="col-12">
		<table style="width:100%;" class="table">
			<thead>
				<tr style="background-color:#0984e3;color:white;">
					<th>
						Estado
					</th>
					<th>
						N&deg; de Empresas
					</th>
					<th>
						Cumplimento
					</th>
					<th>
						Da&ntilde;os Asociados
					</th>
				</tr>
			</thead>
			<tbody>
				<tr class="row-certilap">
					<td style="background-color:#0984e3;color:white;">
						Documentado menor o igual a 15 de cada mes
					</td>
					<td style="background-color:#0984e3;color:white;">
						{{ $countDocumentas1al15 }}
					</td>
					<td style="background-color:#0984e3;color:white;">
						100%
					</td>
					<td style="background-color:#0984e3;color:white;">
						Empresa contratista entrega oportunamente la informaci&oacute;n para ser revisada.
					</td>
				</tr>
				<tr class="row-certilap">
					<td style="background-color:#0984e3;color:white;">
						Documentado antes del 20 de cada mes (entre el 16 y el 20)
					</td>
					<td style="background-color:#0984e3;color:white;">
						{{ $countDocumentas16al20 }}
					</td>
					<td style="background-color:#0984e3;color:white;">
						80%
					</td>
					<td style="background-color:#0984e3;color:white;">
						Empresa contratista entrega la informaci&oacute;n dentro de los plazos regulares. Deber esperar 5 días h&aacute;biles para la revisi&oacute;n de documentos, estar&iacute;a en riesgo si se detectan observaciones.&aacute;
					</td>
				</tr>
				<tr class="row-certilap">
					<td style="background-color:#0984e3;color:white;">
						Documentado hasta el 25 de cada mes (entre el 21 y 25)
					</td>
					<td style="background-color:#0984e3;color:white;">
						{{ $countDocumentas21al25 }}
					</td>
					<td style="background-color:#0984e3;color:white;">
						40%
					</td>
					<td style="background-color:#0984e3;color:white;">
						Empresa contratista entrega la informaci&oacute;n fuera de los plazos establecidos. Deber&aacute; esperar 5 días h&aacute;biles para la revisi&oacute;n de documentos, no alcanzar&aacute; a certificar dentro de los plazos.
					</td>
				</tr>
				<tr class="row-certilap">
					<td style="background-color:#0984e3;color:white;">
						Sin documentar
					</td>
					<td style="background-color:#0984e3;color:white;">
						{{ $sinDocumentar }}
					</td>
					<td style="background-color:#0984e3;color:white;">
						0%
					</td>
					<td style="background-color:#0984e3;color:white;">
						Empresa no cumple con su obligaci&oacute;n laboral, poniendo en riesgo a la mandante de que asuma su responsabilidad solidaria.
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	@if($DATOSDocumentas1al15!=0)
	<div class="col-12">
		<p><strong>Empresas documentado menor o igual a 15 de cada mes, del periodo: mes {{ $mes }} - A&ntilde;o {{ $anio }}</strong> </p>
	</div>
	<div class="col-12">
		<table style="width:100%;" class="table">
			
				<tr style="background-color:#47941b;color:white;">
					<td style="width:15%;">
						RUT
					</td>
					<td style="width:25%;">
						Contratista
					</td>
					<td style="width:20%;">
						Centro de Costo
					</td>
					<td style="width:20%;">
						RUT Sub Contratista
					</td>
					<td style="width:20%;">
						Sub Contratista
					</td>
				</tr>
			
			<tbody>
				@foreach($DATOSDocumentas1al15 as $datos)
				<tr class="row-certilap">
					<td style="background-color:#47941b;color:white;">
						{{$datos["rut"]}}
					</td>
					<td style="background-color:#47941b;color:white;">
						{{$datos["name"]}}
					</td>
					<td style="background-color:#47941b;color:white;">
						{{$datos["center"]}}
					</td>
					<td style="background-color:#47941b;color:white;">
						{{$datos["subRut"]}}
					</td>
					<td style="background-color:#47941b;color:white;">
						{{$datos["subName"]}}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	@endif
	@if($DATOSDocumenta16al20!=0)
	<br>
	<div class="col-12">
		<p><strong>Empresas documentado antes del 20 decada mes (entre el 16 y el 20), del periodo: mes {{ $mes }} - A&ntilde;o {{ $anio }}</strong> </p>
	</div>
	<div class="col-12">
		<table style="width:100%;" class="table">
		
				<tr style="background-color:#c2b52b;color:white;">
					<td style="width:15%;">
						RUT
					</td>
					<td style="width:25%;">
						Contratista
					</td>
					<td style="width:20%;">
						Centro de Costo
					</td>
					<td style="width:20%;">
						RUT Sub Contratista
					</td>
					<td style="width:20%;">
						Sub Contratista
					</td>
				</tr>
		
			<tbody>
				@foreach($DATOSDocumenta16al20 as $datos)
				<tr class="row-certilap">
					<td style="background-color:#c2b52b;color:white;">
						{{$datos["rut"]}}
					</td>
					<td style="background-color:#c2b52b;color:white;">
						{{$datos["name"]}}
					</td>
					<td style="background-color:#c2b52b;color:white;">
						{{$datos["center"]}}
					</td>
					<td style="background-color:#c2b52b;color:white;">
						{{$datos["subRut"]}}
					</td>
					<td style="background-color:#c2b52b;color:white;">
						{{$datos["subName"]}}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	@endif
	@if($DATOSDocumenta21al25!=0)
	<br>
	<div class="col-12">
		<p><strong>Empresas documentado hastael 25 decada mes (entre el 21 y 25), del periodo: mes {{ $mes }} - A&ntilde;o {{ $anio }}</strong> </p>
	</div>
	<div class="col-12">
		<table style="width:100%;" class="table">
		
				<tr style="background-color:#e38809;color:white;">
					<td style="width:15%;">
						RUT
					</td>
					<td style="width:25%;">
						Contratista
					</td>
					<td style="width:20%;">
						Centro de Costo
					</td>
					<td style="width:20%;">
						RUT Sub Contratista
					</td>
					<td style="width:20%;">
						Sub Contratista
					</td>
				</tr>
		
			<tbody>
				@foreach($DATOSDocumenta21al25 as $datos)
				<tr class="row-certilap">
					<td style="background-color:#e38809;color:white;">
						{{$datos["rut"]}}
					</td>
					<td style="background-color:#e38809;color:white;">
						{{$datos["name"]}}
					</td>
					<td style="background-color:#e38809;color:white;">
						{{$datos["center"]}}
					</td>
					<td style="background-color:#e38809;color:white;">
						{{$datos["subRut"]}}
					</td>
					<td style="background-color:#e38809;color:white;">
						{{$datos["subName"]}}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	@endif
	@if($DATOSSinDoc!=0)
	<br>
	<div class="col-12">
		<p><strong>Empresas sin documentar, del periodo: mes {{ $mes }} - A&ntilde;o {{ $anio }}</strong> </p>
	</div>
	<div class="col-12">
		<table style="width:100%;" class="table">
		
				<tr style="background-color:#e33109;color:white;">
					<td style="width:15%;">
						RUT
					</td>
					<td style="width:25%;">
						Contratista
					</td>
					<td style="width:20%;">
						Centro de Costo
					</td>
					<td style="width:20%;">
						RUT Sub Contratista
					</td>
					<td style="width:20%;">
						Sub Contratista
					</td>
				</tr>
		
			<tbody>
				@foreach($DATOSSinDoc as $datos)
				<tr class="row-certilap">
					<td style="background-color:#e33109;color:white;">
						{{$datos["rut"]}}
					</td>
					<td style="background-color:#e33109;color:white;">
						{{$datos["name"]}}
					</td>
					<td style="background-color:#e33109;color:white;">
						{{$datos["center"]}}
					</td>
					<td style="background-color:#e33109;color:white;">
						{{$datos["subRut"]}}
					</td>
					<td style="background-color:#e33109;color:white;">
						{{$datos["subName"]}}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	@endif
</div>
<div class="page">
	<div class="col-12">
		<p><strong>Historial de Estado de Certificaci&oacute;n: </strong>la certificaci&oacute;n se debe hacer en un ciclo de aprobaci&oacute;n, sin embargo, se producen iteraciones que deben ser analizadas.</p>
	</div>
	<div class="col-12">
		<table style="width:100%;" class="table">
			<thead>
				<tr style="background-color:#0984e3;color:white;">
					<th>
						Estado
					</th>
					<th>
						N&deg; de Empresas
					</th>
					<th>
						Cumplimento
					</th>
					<th>
						Da&ntilde;os Asociados
					</th>
				</tr>
			</thead>
			<tbody>
				<tr class="row-certilap">
					<td style="background-color:#0984e3;color:white;">
						Empresa certifica sin observaciones (0 no aprobaciones)
					</td>
					<td style="background-color:#0984e3;color:white;">
						{{ $ceroNoAprobados }}
					</td>
					<td style="background-color:#0984e3;color:white;">
						100%
					</td>
					<td style="background-color:#0984e3;color:white;">
						Contratista entrega correctamente la informaci&oacute;n dentro de los plazos acordados y no presenta observaciones.
					</td>
				</tr>
				<tr class="row-certilap">
					<td style="background-color:#0984e3;color:white;">
						Empresa certifica 1 ciclo de revisi&oacute;n. (1 no aprobaciones) 
					</td>
					<td style="background-color:#0984e3;color:white;">
						{{ $unaNoAprobados }}
					</td>
					<td style="background-color:#0984e3;color:white;">
						90%
					</td>
					<td style="background-color:#0984e3;color:white;">
						Contratista debe mejorar la calidad de la informaci&oacute;n entregada..
					</td>
				</tr>
				<tr class="row-certilap">
					<td style="background-color:#0984e3;color:white;">
						Empresa tiene 2 ciclos de revisi&oacute;n. (2 no aprobaciones) 
					</td>
					<td style="background-color:#0984e3;color:white;">
						{{ $dosNoAprobados }}
					</td>
					<td style="background-color:#0984e3;color:white;">
						80%
					</td>
					<td style="background-color:#0984e3;color:white;">
						Contratista debe mejorar la calidad de la informaci&oacute;n entregada.
					</td>
				</tr>
				<tr class="row-certilap">
					<td style="background-color:#0984e3;color:white;">
						Empresa tiene 3 ciclos de revisi&oacute;n. (3 no aprobaciones)
					</td>
					<td style="background-color:#0984e3;color:white;">
						{{ $tresNoAprobados }}
					</td>
					<td style="background-color:#0984e3;color:white;">
						60%
					</td>
					<td style="background-color:#0984e3;color:white;">
						Contratista debe mejorar la calidad de la informaci&oacute;n entregada. Requiere revisi&oacute;n con el usuario.
					</td>
				</tr>
				<tr class="row-certilap">
					<td style="background-color:#0984e3;color:white;">
						Empresa tiene 4 ciclos de revisi&oacute;n. (4 no aprobaciones)
					</td>
					<td style="background-color:#0984e3;color:white;">
						{{ $cuatroNoAprobados }}
					</td>
					<td style="background-color:#0984e3;color:white;">
						40%
					</td>
					<td style="background-color:#0984e3;color:white;">
						Contratista debe mejorar la calidad de la informaci&oacute;n entregada. La empresa requiere capacitaci&oacute;n.
					</td>
				</tr>
				<tr class="row-certilap">
					<td style="background-color:#0984e3;color:white;">
						Empresa tiene 5 o m&aacute;s ciclos de revisi&oacute;n. (5 o mas no aprobaciones)
					</td>
					<td style="background-color:#0984e3;color:white;">
						{{ $cincoNoAprobados }}
					</td>
					<td style="background-color:#0984e3;color:white;">
						0%
					</td>
					<td style="background-color:#0984e3;color:white;">
						Contratista debe ser contactada dado que requiere capacitaci&oacute;n con el fin de mejorar la calidad de la informaci&oacute;n entregada.
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	</br>
	@if($DATOSCeroNoAprobado!=0)
	<br>
	<div class="col-12">
		<p><strong>Empresa certificadas sin observaciones, del periodo: mes {{ $mes }} - A&ntilde;o {{ $anio }}</strong> </p>
	</div>
	<div class="col-12">
		<table style="width:100%;" class="table">
		
				<tr style="background-color:#47941b;color:white;">
					<td style="width:15%;">
						RUT
					</td>
					<td style="width:25%;">
						Contratista
					</td>
					<td style="width:20%;">
						Centro de Costo
					</td>
					<td style="width:20%;">
						RUT Sub Contratista
					</td>
					<td style="width:20%;">
						Sub Contratista
					</td>
				</tr>
		
			<tbody>
				@foreach($DATOSCeroNoAprobado as $datos)
				<tr class="row-certilap">
					<td style="background-color:#47941b;color:white;">
						{{$datos["rut"]}}
					</td>
					<td style="background-color:#47941b;color:white;">
						{{$datos["name"]}}
					</td>
					<td style="background-color:#47941b;color:white;">
						{{$datos["center"]}}
					</td>
					<td style="background-color:#47941b;color:white;">
						{{$datos["subRut"]}}
					</td>
					<td style="background-color:#47941b;color:white;">
						{{$datos["subName"]}}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	@endif
	@if($DATOSUnaNoAprobado!=0)
	<br>
	<div class="col-12">
		<p><strong>Empresa certifica 1 ciclo de revisi&oacute;n, del periodo: mes {{ $mes }} - A&ntilde;o {{ $anio }}</strong> </p>
	</div>
	<div class="col-12">
		<table style="width:100%;" class="table">
		
				<tr style="background-color:#92cc2f;color:white;">
					<td style="width:15%;">
						RUT
					</td>
					<td style="width:25%;">
						Contratista
					</td>
					<td style="width:20%;">
						Centro de Costo
					</td>
					<td style="width:20%;">
						RUT Sub Contratista
					</td>
					<td style="width:20%;">
						Sub Contratista
					</td>
				</tr>
		
			<tbody>
				@foreach($DATOSUnaNoAprobado as $datos)
				<tr class="row-certilap">
					<td style="background-color:#92cc2f;color:white;">
						{{$datos["rut"]}}
					</td>
					<td style="background-color:#92cc2f;color:white;">
						{{$datos["name"]}}
					</td>
					<td style="background-color:#92cc2f;color:white;">
						{{$datos["center"]}}
					</td>
					<td style="background-color:#92cc2f;color:white;">
						{{$datos["subRut"]}}
					</td>
					<td style="background-color:#92cc2f;color:white;">
						{{$datos["subName"]}}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	@endif

	@if($DATOSDosNoAprobado!=0)
	<br>
	<div class="col-12">
		<p><strong>Empresa certifica 2 ciclo de revisi&oacute;n, del periodo: mes {{ $mes }} - A&ntilde;o {{ $anio }}</strong> </p>
	</div>
	<div class="col-12">
		<table style="width:100%;" class="table">
		
				<tr style="background-color:#c2b52b;color:white;">
					<td style="width:15%;">
						RUT
					</td>
					<td style="width:25%;">
						Contratista
					</td>
					<td style="width:20%;">
						Centro de Costo
					</td>
					<td style="width:20%;">
						RUT Sub Contratista
					</td>
					<td style="width:20%;">
						Sub Contratista
					</td>
				</tr>
		
			<tbody>
				@foreach($DATOSDosNoAprobado as $datos)
				<tr class="row-certilap">
					<td style="background-color:#c2b52b;color:white;">
						{{$datos["rut"]}}
					</td>
					<td style="background-color:#c2b52b;color:white;">
						{{$datos["name"]}}
					</td>
					<td style="background-color:#c2b52b;color:white;">
						{{$datos["center"]}}
					</td>
					<td style="background-color:#c2b52b;color:white;">
						{{$datos["subRut"]}}
					</td>
					<td style="background-color:#c2b52b;color:white;">
						{{$datos["subName"]}}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	@endif

	@if($DATOSTresNoAprobado!=0)
	<br>
	<div class="col-12">
		<p><strong>Empresa certifica 3 ciclo de revisi&oacute;n, del periodo: mes {{ $mes }} - A&ntilde;o {{ $anio }}</strong> </p>
	</div>
	<div class="col-12">
		<table style="width:100%;" class="table">
		
				<tr style="background-color:#e38809;color:white;">
					<td style="width:15%;">
						RUT
					</td>
					<td style="width:25%;">
						Contratista
					</td>
					<td style="width:20%;">
						Centro de Costo
					</td>
					<td style="width:20%;">
						RUT Sub Contratista
					</td>
					<td style="width:20%;">
						Sub Contratista
					</td>
				</tr>
		
			<tbody>
				@foreach($DATOSTresNoAprobado as $datos)
				<tr class="row-certilap">
					<td style="background-color:#e38809;color:white;">
						{{$datos["rut"]}}
					</td>
					<td style="background-color:#e38809;color:white;">
						{{$datos["name"]}}
					</td>
					<td style="background-color:#e38809;color:white;">
						{{$datos["center"]}}
					</td>
					<td style="background-color:#e38809;color:white;">
						{{$datos["subRut"]}}
					</td>
					<td style="background-color:#e38809;color:white;">
						{{$datos["subName"]}}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	@endif

	@if($DATOSCuatroNoAprobado!=0)
	<br>
	<div class="col-12">
		<p><strong>Empresa certifica 4 ciclo derevisi&oacute;n, del periodo: mes {{ $mes }} - A&ntilde;o {{ $anio }}</strong> </p>
	</div>
	<div class="col-12">
		<table style="width:100%;" class="table">
		
				<tr style="background-color:#e33109;color:white;">
					<td style="width:15%;">
						RUT
					</td>
					<td style="width:25%;">
						Contratista
					</td>
					<td style="width:20%;">
						Centro de Costo
					</td>
					<td style="width:20%;">
						RUT Sub Contratista
					</td>
					<td style="width:20%;">
						Sub Contratista
					</td>
				</tr>
		
			<tbody>
				@foreach($DATOSCuatroNoAprobado as $datos)
				<tr class="row-certilap">
					<td style="background-color:#e33109;color:white;">
						{{$datos["rut"]}}
					</td>
					<td style="background-color:#e33109;color:white;">
						{{$datos["name"]}}
					</td>
					<td style="background-color:#e33109;color:white;">
						{{$datos["center"]}}
					</td>
					<td style="background-color:#e33109;color:white;">
						{{$datos["subRut"]}}
					</td>
					<td style="background-color:#e33109;color:white;">
						{{$datos["subName"]}}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	@endif

	@if($DATOSCincoNoAprobado!=0)
	<br>
	<div class="col-12">
		<p><strong>Empresa certifica 5 ciclo derevisi&oacute;n, del periodo: mes {{ $mes }} - A&ntilde;o {{ $anio }}</strong> </p>
	</div>
	<div class="col-12">
		<table style="width:100%;" class="table">
		
				<tr style="background-color:#b30202;color:white;">
					<td style="width:15%;">
						RUT
					</td>
					<td style="width:25%;">
						Contratista
					</td>
					<td style="width:20%;">
						Centro de Costo
					</td>
					<td style="width:20%;">
						RUT Sub Contratista
					</td>
					<td style="width:20%;">
						Sub Contratista
					</td>
				</tr>
		
			<tbody>
				@foreach($DATOSCincoNoAprobado as $datos)
				<tr class="row-certilap">
					<td style="background-color:#b30202;color:white;">
						{{$datos["rut"]}}
					</td>
					<td style="background-color:#b30202;color:white;">
						{{$datos["name"]}}
					</td>
					<td style="background-color:#b30202;color:white;">
						{{$datos["center"]}}
					</td>
					<td style="background-color:#b30202;color:white;">
						{{$datos["subRut"]}}
					</td>
					<td style="background-color:#b30202;color:white;">
						{{$datos["subName"]}}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	@endif
</div>
<div class="page">
	<div class="col-12">
		<p><strong>Cumplimiento de plazos en dejar la documentaci&oacute;n corregida: </strong>se deben cumplir los plazos estipulados para que la empresa cuente con su certificado en los tiempos que debe presentarla en la empresa mandante.</p>
	</div>
	<div class="col-12">
		<table style="width:100%;" class="table">
			<thead>
				<tr style="background-color:#0984e3;color:white;">
					<th>
						Estado
					</th>
					<th>
						N&deg; de Empresas
					</th>
					<th>
						Cumplimento
					</th>
					<th>
						Da&ntilde;os Asociados
					</th>
				</tr>
			</thead>
			<tbody>
				<tr class="row-certilap">
					<td style="background-color:#0984e3;color:white;">
						3 d&iacute;as
					</td>
					<td style="background-color:#0984e3;color:white;">
						{{ $tresDias }}
					</td>
					<td style="background-color:#0984e3;color:white;">
						100%
					</td>
					<td style="background-color:#0984e3;color:white;">
						Contratista cumple los plazos preestablecidos.
					</td>
				</tr>
				<tr class="row-certilap">
					<td style="background-color:#0984e3;color:white;">
						5 d&iacute;as 
					</td>
					<td style="background-color:#0984e3;color:white;">
						{{ $cincoDias }}
					</td>
					<td style="background-color:#0984e3;color:white;">
						80%
					</td>
					<td style="background-color:#0984e3;color:white;">
						Contratista debe mejorar los tiempos en resolver sus dudas y corregir documentos observados.
					</td>
				</tr>
				<tr class="row-certilap">
					<td style="background-color:#0984e3;color:white;">
						8 d&iacute;as 
					</td>
					<td style="background-color:#0984e3;color:white;">
						{{ $ochoDias }}
					</td>
					<td style="background-color:#0984e3;color:white;">
						60%
					</td>
					<td style="background-color:#0984e3;color:white;">
						Contratista debe mejorar los tiempos en resolver sus dudas y corregir documentaci&oacute;n observada, se debe contactar al usuario con el fin de verificar la causal del atraso.
					</td>
				</tr>
				<tr class="row-certilap">
					<td style="background-color:#0984e3;color:white;">
						10 d&iacute;as 
					</td>
					<td style="background-color:#0984e3;color:white;">
						{{ $diezDias }}
					</td>
					<td style="background-color:#0984e3;color:white;">
						40%
					</td>
					<td style="background-color:#0984e3;color:white;">
						Contratista en riesgo y exposici&oacute;n de la mandante es alto dado que no ha subsanado aun observaciones detectadas en la revisi&oacute;n.
					</td>
				</tr>
				<tr class="row-certilap">
					<td style="background-color:#0984e3;color:white;">
						11 d&iacute;as o m&aacute;s
					</td>
					<td style="background-color:#0984e3;color:white;">
						{{ $onceDias }}
					</td>
					<td style="background-color:#0984e3;color:white;">
						0%
					</td>
					<td style="background-color:#0984e3;color:white;">
						Mandante debe contactar a la empresa con el fin de verificar la causa del porque la empresa no corrige observaciones.
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<div class="page">
	<h4 class="text-justify">
		Estad&iacute;sticas de Contrastitas por estado de certificaci&oacute;n, del periodo: mes {{ $mes }} - A&ntilde;o {{ $anio }}
		<br>
	    <br>
	</h4>
	<div class="col-12">
		<table style="width:100%;" class="table">
			<thead>
				<tr style="background-color:#0984e3;color:white;">
					<th>
						Periodo
					</th>
					@foreach ($header_for_table_first_page as $th)
					<th>
						{{ $th }}
					</th>
					@endforeach
					<th>
						Total
					</th>
				</tr>
			</thead>
			<tbody>
				<tr class="row-certilap">
					<td style="background-color:#0984e3;color:white;">
						{{ $mes }}
					</td>
					@foreach ($count_company_per_certificate_state as $td)
					<td>
						{{ $td }}
					</td>
					@endforeach
					<td>
						{{ $total_companies }}
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<br>
	<br>
	<div class="col-12 text-center">
		<img src="{{ $bars_by_certificate_state }}">
	</div>
	<br>
	<br>
	<div class="col-12 text-center">
		<img src="{{ $chart_per_certificate_state }}">
	</div>
</div>
<div class="page">
	<h4 class="text-justify">
		Lista de Contrastitas por estado de certificaci&oacute;n, del periodo: mes {{ $mes }} - A&ntilde;o {{ $anio }}
		<br>
	    <br>
	</h4>
	<div class="col-12">
		<table style="width:100%;" class="table">
			<tr style="background-color:#0984e3;color:white;">
					<td style="width:15%;">
						RUT
					</td>
					<td style="width:25%;">
						Contratista
					</td>
					<td style="width:20%;">
						Centro de Costo
					</td>
					<td style="width:20%;">
						RUT Sub Contratista
					</td>
					<td style="width:20%;">
						Sub Contratista
					</td>
					<td style="width:20%;">
						Estado de Certificaci&oacute;n
					</td>
				</tr>
			<tbody>
				@foreach($DATAEMPRESA as $datos)
				<tr>
					<td style='background-color:{{$datos["colorEstado"]}};color:white;'>
						{{$datos["rut"]}}
					</td>
					<td style='background-color:{{$datos["colorEstado"]}};color:white;'>
						{{$datos["name"]}}
					</td>
					<td style='background-color:{{$datos["colorEstado"]}};color:white;'>
						{{$datos["center"]}}
					</td>
					<td style='background-color:{{$datos["colorEstado"]}};color:white;'>
						{{$datos["subRut"]}}
					</td>
					<td style='background-color:{{$datos["colorEstado"]}};color:white;'>
						{{$datos["subName"]}}
					</td>
					<td style='background-color:{{$datos["colorEstado"]}};color:white;'>
						{{$datos["estado"]}}
					</td>
				</tr>
				@endforeach
					
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div class="page">
	<div class="col-12">
		<h4>Estad&iacute;sticas de contratistas y sub contratistas, del periodo: mes {{ $mes }} - A&ntilde;o {{ $anio }}</h4>
	</div>
	<br>
	<br>
	<div class="col-12 text-center">
		<img src="{{ $chart_by_company_type }}">
	</div>
	<br>
	<br>
	<div class="col-12 text-center">
		<img src="{{ $bars_by_company_type }}">
	</div>
</div>
<div class="page">
	<div class="col-12">
		<h4>Estad&iacute;sticas de contratistas recertificadas, del periodo: mes {{ $mes }} - A&ntilde;o {{ $anio }}</h4>
	</div>
	<br>
	<br>
	<div class="col-12 text-center">
		<img src="{{ $chart_by_rectificadas }}">
	</div>
	<br>
	<br>
	<div class="col-12 text-center">
		<img src="{{ $bars_by_rectificadas }}">
	</div>
</div>
<div class="page">
	<div class="col-12">
		<h4>Estad&iacute;sticas de genero de contratistas, del periodo: mes {{ $mes }} - A&ntilde;o {{ $anio }}</h4>
	</div>
	<br>
	<br>
	<div class="col-12 text-center">
		<img src="{{ $chart_genre_worker }}">
	</div>
	<br>
	<br>
	<div class="col-12 text-center">
	<img src="{{ $bars_by_genre }}">
	</div>
</div>
<div class="page">
	<div class="col-12">
		<h4>Estad&iacute;sticas de contratistas con numero observaciones, del periodo: mes {{ $mes }} - A&ntilde;o {{ $anio }}</h4>
	</div>
	<br>
	<br>
	<div class="row">
		@foreach ($estadistica_por_empresa_charts as $grafica)
		<div class="col-12 text-center">
			<img src="{{ $grafica }}">
        </div> 
        @endforeach
	</div>
</div>
</html>
