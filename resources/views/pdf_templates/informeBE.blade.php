<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<div class="row">
	<!-- <div class="col-12" style="background-color: #2f4881; width: 1200px; height:90px">
		<img src="http://www.certilapchile.cl/templates/ja_seleni/images/logo2.png" alt="CERTILAP" align="left">
	</div> -->
	<br>
	<div class="col-12">
		<center><h2 class="title">Informe Banco Estado {{ $mes }} {{ $anio }}</h2></center>
	</div>
</div>
<br>
<div class="row">
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
<br>
<br>
<br>
<br>
<div class="row">
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
<div class="row">
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
<br>
<br>
<br>
<br>
<div class="row">
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
<br>
<br>
<br>
<br>
<div class="row">
	<div class="col-12">
		<h3>Gr&aacute;fica por empresas contratistas</h3>
	</div>
	<div class="col-12 text-center">
		<img src="{{ $bars_by_empresa_contratista }}">
	</div>
</div>

