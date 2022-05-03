<table border="2">
                <thead>
                <tr>
                  <th style="background-color:#e3e3e3">Folio</th>
                  <th style="background-color:#e3e3e3">Nombre</th>
                  <th style="background-color:#e3e3e3">Apellido Paterno</th>
                  <th style="background-color:#e3e3e3">Apellido Materno</th>
                  <th style="background-color:#e3e3e3">RUT</th>
                  <th style="background-color:#e3e3e3">Cargo</th>
                  <th style="background-color:#e3e3e3">Empresa Principal</th>
                  <th style="background-color:#e3e3e3">RUT</th>
                  <th style="background-color:#e3e3e3">Empresa Contratista</th>
                  <th style="background-color:#e3e3e3">RUT</th>
                  <th style="background-color:#e3e3e3">Estado Certificación</th>
                  <th style="background-color:#e3e3e3">Fecha Certificación</th>
                  <th style="background-color:#e3e3e3">Cetro de Costo</th>
                  <th style="background-color:#e3e3e3">Certificado</th>
                  <th style="background-color:#e3e3e3">% Acreditación</th>
                </tr>
                </thead>
                 <tbody>
                @foreach($listaDatosReporte as $datos)
                @isset($datos["folioSSO"])
                <tr>
                  <td>
                   {{$datos["folioSSO"]}}
                 </td>
                 <td>
                   {{$datos["nombreTrabajador"]}}
                 </td>
                  <td>
                   {{$datos["apellido1Trabajador"]}}
                 </td>
                 <td>
                   {{$datos["apellido2Trabajador"]}}
                 </td>
                  <td>
                   {{$datos["rutTrabajador"]}}
                 </td>
                 <td>
                   {{$datos["cargo"]}}
                 </td>
                 <td>
                   {{$datos["empresaPrincipal"]}}
                 </td>
                  <td>
                   {{$datos["rutPrincipal"]}}
                 </td>
                 <td>
                   {{$datos["empresaContratista"]}}
                 </td>
                 <td>
                   {{$datos["rutContratista"]}}
                 </td>
                 <td>
                   {{$datos["estadoCertificacion"]}}
                 </td>
                 <td>
                   {{$datos["fechaCertificacion"]}}
                 </td>
                 <td>
                   {{$datos["centroCosto"]}}
                 </td>
                  <td>
                   {{$datos["Certificacion"]}}
                 </td>
                  <td>
                   {{$datos["porcentajeTrabajador"]}}
                 </td>
                </tr>  
              </tbody>
                @endisset
                @endforeach
                </table>

