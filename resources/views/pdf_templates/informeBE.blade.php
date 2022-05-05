<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<style type="text/css" media="print">
    div.page
    {
        page-break-after: always;
        page-break-inside: avoid;
    }
</style>
<div class="page">
	<h1 class="text-center">
		Informe Estadísticas Contratistas y Subcontratistas Banco Estado mes {{ $mes }} - A&ntilde;o {{ $anio }}
	</h1>
	<div class="position-absolute">
		<img src="{{ asset("/img/certilap_pdf_portrait.png") }}" alt="">
	</div>
</div>
<div class="page">
	<h4 class="text-justify">
		Estad&iacute;sticas de Empresas por Estado de Certificaci&oacute;n 
	</h4>
	<div class="position-absolute">
		<table class="table">
			<thead>
				<tr>
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
				<tr>
					<td>
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
</div>
<div class="page">
	<div class="col-12">
		<h3>Gr&aacute;fica por tipo de Empresa</h3>
	</div>
	<div class="col-12 text-center">
		<img src="{{ $chart_by_company_type }}">
	</div>
	<div class="col-12 text-center">
		<img src="{{ $bars_by_company_type }}">
	</div>
</div>
<div class="page">
	<div class="col-12">
		<h3>Gr&aacute;fica por estado de certificacion</h3>
	</div>
	<div class="col-12 text-center">
		<img src="{{ $chart_per_certificate_state }}">
	</div>
	<div class="col-12 text-center">
		<img src="{{ $bars_by_certificate_state }}">
	</div>
</div>
<div class="page">
	<div class="col-12">
		<h3>Gr&aacute;fica por compa&ntilde;&iacute;as recertificadas</h3>
	</div>
	<div class="col-12 text-center">
		<img src="{{ $chart_by_rectificadas }}">
	</div>
	<div class="col-12 text-center">
		<img src="{{ $bars_by_rectificadas }}">
	</div>
</div>
<div class="page">
	<div class="col-12">
		<h3>Gr&aacute;fica por genero de trabajadores</h3>
	</div>
	<div class="col-12 text-center">
		<img src="{{ $chart_genre_worker }}">
	</div>
	<div class="col-12 text-center">
	<img src="{{ $bars_by_genre }}">
	</div>
</div>
<div class="page">
	<div class="col-12">
		<h3>Gr&aacute;fica por empresas contratistas</h3>
	</div>
	<div class="col-12 text-center">
		<img src="{{ $bars_by_empresa_contratista }}">
	</div>
</div>
