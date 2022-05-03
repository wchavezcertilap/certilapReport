<table border="2">
                <thead>
                <tr>
                  <th style="background-color:#e3e3e3">RUT</th>
                  <th style="background-color:#e3e3e3">Nombre</th>
                  <th style="background-color:#e3e3e3">Apellido Paterno</th>
                  <th style="background-color:#e3e3e3">Apellido Materno</th>
                  <th style="background-color:#e3e3e3">RUT Principal</th>
                  <th style="background-color:#e3e3e3">Empresa Principal</th>
                  <th style="background-color:#e3e3e3">RUT Contratista</th>
                  <th style="background-color:#e3e3e3">Empresa Contratista</th>
                  <th style="background-color:#e3e3e3">Centro de Costo</th>
                  <th style="background-color:#e3e3e3">RUT Sub Contratista</th>
                  <th style="background-color:#e3e3e3">Empresa Sub Contratista</th>
                  <th style="background-color:#e3e3e3">Estado Certificación</th>
                  <th style="background-color:#e3e3e3">Fecha Certificación</th>
                  <th style="background-color:#e3e3e3">Nacionalidad</th>
                  <th style="background-color:#e3e3e3">Fecha Vencimiento RUT</th>
                  <th style="background-color:#e3e3e3">Cotiza AFP</th>
                  <th style="background-color:#e3e3e3">Fecha Ingreso Certificado de Exención</th>
                  <th style="background-color:#e3e3e3">Tipo Visa</th>
                  <th style="background-color:#e3e3e3">Fecha Vencimiento Visa</th>
                  <th style="background-color:#e3e3e3">Documento Subido en Periodo</th>
                  <th style="background-color:#e3e3e3">Observación</th>
                </tr>
                </thead>
                 <tbody>
                @foreach($trabajadoresExtra as $datos)
                @isset($datos["rutExt"])
                <tr style='background-color:#{{$datos["colorFecha"]}}'>
                  <td>
                   {{$datos["rutExt"]}}
                 </td>
                 <td>
                   {{$datos["nombre"]}}
                 </td>
                  <td>
                   {{$datos["apellidoP"]}}
                 </td>
                 <td>
                   {{$datos["apellidoM"]}}
                 </td>
                  <td>
                   {{$datos["rutprincipal"]}}
                 </td>
                 <td>
                   {{$datos["nombrePrincipal"]}}
                 </td>
                 <td>
                   {{$datos["rutcontratista"]}}
                 </td>
                  <td>
                   {{$datos["nombreContratista"]}}
                 </td>
                 <td>
                   {{$datos["centroC"]}}
                 </td>
                 <td>
                   {{$datos["rutsubContratista"]}}
                 </td>
                 <td>
                   {{$datos["subnombre"]}}
                 </td>
                 <td>
                   {{$datos["estadoCertificado"]}}
                 </td>
                  <td>
                   {{$datos["fechaCertificado"]}}
                 </td>
                  <td>
                   {{$datos["nacionalidad"]}}
                 </td>
                  <td>
                   {{$datos["vencimientoRut"]}}
                 </td>
                  <td>
                   {{$datos["cotizaAFP"]}}
                 </td>
                 <td>
                   {{$datos["fechaIngresoCert"]}}
                 </td>
                 <td>
                   {{$datos["tipoVisa"]}}
                 </td>
                 <td>
                   {{$datos["fechaVencimientoVisa"]}}
                 </td>
                  <td>
                   {{$datos["peridoDoc"]}}
                 </td>
                  <td>
                   {{$datos["Observacion"]}}
                 </td>
                </tr>  
              </tbody>
                @endisset
                @endforeach
                </table>