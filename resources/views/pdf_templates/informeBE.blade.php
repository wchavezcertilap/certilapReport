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
	<br>

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
						Empresa tiene 2 ciclos de revisión. (2 no aprobaciones) 
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
						Empresa tiene 3 ciclos de revisión. (3 no aprobaciones)
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
</div>

<div class="page">
	<h4 class="text-justify">
		Estad&iacute;sticas de Contrastitas por estado de certificaci&oacute;n
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
	<div class="col-12">
		<h4>Estad&iacute;sticas de contratistas y sub contratistas</h4>
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
		<h4>Estad&iacute;sticas de contratistas recertificadas</h4>
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
		<h4>Estad&iacute;sticas de genero de contratistas</h4>
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
		<h4>Estad&iacute;sticas de contratistas con observaciones</h4>
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
