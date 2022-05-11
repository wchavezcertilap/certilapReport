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
