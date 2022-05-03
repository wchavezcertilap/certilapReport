<table border="2">
    <thead>
    <tr>
      <th style="background-color:#e3e3e3">RUT</th>
      <th style="background-color:#e3e3e3">Nombre</th>
      <th style="background-color:#e3e3e3">Apellido Paterno</th>
      <th style="background-color:#e3e3e3">Apellido Materno</th>
      <th style="background-color:#e3e3e3">RUT Principal</th>
      <th style="background-color:#e3e3e3">Principal</th>
      <th style="background-color:#e3e3e3">RUT Contratista</th>
      <th style="background-color:#e3e3e3">Contratista</th>
      <th style="background-color:#e3e3e3">RUT Sub Contratista</th>
      <th style="background-color:#e3e3e3">Sub Contratista</th>
      <th style="background-color:#e3e3e3">Centro de Costo</th>
      <th style="background-color:#e3e3e3">Periodo</th>
      <th style="background-color:#e3e3e3">Estado Certificación</th>
      <th style="background-color:#e3e3e3">Fecha Certificación</th>
      <th style="background-color:#e3e3e3">Contro de Acceso</th>
      <th style="background-color:#e3e3e3">% Acreditacion total por trabajador</th>
    </tr>
    </thead>
     <tbody>
    @foreach($WORKS as $datos)
    @isset($datos["rutTrabajador"])
    <tr>
      <td>
       {{$datos["rutTrabajador"]}}
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
       {{$datos["rutPrincipal"]}}
     </td>
     <td>
       {{$datos["nombrePrincipal"]}}
     </td>
     <td>
       {{$datos["rutContratista"]}}
     </td>
      <td>
       {{$datos["nombreContratista"]}}
     </td>
     <td>
       {{$datos["rutSubContratista"]}}
     </td>
     <td>
       {{$datos["nombreSubContratista"]}}
     </td>
     <td>
       {{$datos["centroCosto"]}}
     </td>
     <td>
       {{$datos["perido"]}}
     </td>
      <td>
       {{$datos["estadoCertificacion"]}}
     </td>
     <td>
       {{$datos["fechaCertificado"]}}
     </td>
      <td>
       {{$datos["ControlAcceso"]}}
     </td>
     <td>
       {{$datos["porcentajeTrabajador"]}}
     </td>
    </tr>  
  </tbody>
    @endisset
    @endforeach
    </table>