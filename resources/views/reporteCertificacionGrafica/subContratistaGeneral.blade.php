<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript">

 </script>
</head>

              <table border="2">
                <thead>
                <tr>
                  <th style="background-color:#e3e3e3">id</th>
                  <th style="background-color:#e3e3e3">RUT Empresa Principal</th>
                  <th style="background-color:#e3e3e3">Razon Social Empresa Principal</th>
                  <th style="background-color:#e3e3e3">RUT Empresa Contratista</th>
                  <th style="background-color:#e3e3e3">Razon Social Empresa Contratista</th>
                  <th style="background-color:#e3e3e3">Centro de Costo</th>
                  <th style="background-color:#e3e3e3">Periodo</th>
                  <th style="background-color:#e3e3e3">RUT Sub Contratista</th>
                  <th style="background-color:#e3e3e3">Razon Social Sub Contratista</th>
                  <th style="background-color:#e3e3e3">Número de Trabajadores</th>
                  <th style="background-color:#e3e3e3">Número Total de Trabajadores</th>
                  <th style="background-color:#e3e3e3">Estado Certificado</th>
                  <th style="background-color:#e3e3e3">Fecha Certificación</th>
                  <th style="background-color:#e3e3e3">Observaciones</th>
                </tr>
                </thead>
                 <tbody>
                @foreach($listaDatosReporte as $datos)
                @isset($datos["id"])
                @if ($datos["estadoCerticacionId"] == 5 or $datos["estadoCerticacionId"] == 10)
                @if ($datos["rutSubContratista"] != "-")
                <tr>
                  <td>
                   {{$datos["id"]}}
                 </td>
                 <td>
                   {{$datos["rutprincipal"]}}
                 </td>
                  <td>
                   {{$datos["principal"]}}
                 </td>
                 <td>
                   {{$datos["rutcontratistas"]}}
                 </td>
                 <td>
                   {{$datos["contratista"]}}
                 </td>
                 <td>
                   {{$datos["center"]}}
                 </td>
                  <td>
                   {{$datos["periodo"]}}
                 </td>
                 <td>
                   {{$datos["rutSubContratista"]}}
                 </td>
                 <td>
                   {{$datos["subcontratistaName"]}}
                 </td>
                 <td>
                   {{$datos["numeroTrabajadoresCertificar"]}}
                 </td>
                 <td>
                   {{$datos["numeroTrabajadoresTotales"]}}
                 </td>
                  <td>
                   {{$datos["estadoCerticacion"]}}
                 </td>
                 <td>
                   {{$datos["fechaCerticacion"]}}
                 </td>
                  <td>
                   {{$datos["observacion"]}}
                 </td>
                </tr>  
              </tbody>
                @endif
                @endif
                @endisset
                @endforeach
                </table>

