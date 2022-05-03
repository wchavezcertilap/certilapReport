<table border="2">
    <thead>
    <tr>
      <th style="background-color:#e3e3e3">ID</th>
      <th style="background-color:#e3e3e3">RUT Principal</th>
      <th style="background-color:#e3e3e3">Principal</th>
      <th style="background-color:#e3e3e3">RUT Contratista</th>
      <th style="background-color:#e3e3e3">Contratista</th>
      <th style="background-color:#e3e3e3">RUT Sub Contratista</th>
      <th style="background-color:#e3e3e3">Sub Contratista</th>
      <th style="background-color:#e3e3e3">Centro de Costo</th>
      <th style="background-color:#e3e3e3">Periodo</th>
      <th style="background-color:#e3e3e3">Trabajadores Certificados</th>
      <th style="background-color:#e3e3e3">Trabajadores Totales</th>
      <th style="background-color:#e3e3e3">Trabajadores Carga Masiva</th>
      <th style="background-color:#e3e3e3">Estado Certificación</th>
      <th style="background-color:#e3e3e3">Fecha Certificación</th>
      <th style="background-color:#e3e3e3">Ciclo</th>
      <th style="background-color:#e3e3e3">Certificador</th>
      <th style="background-color:#e3e3e3">Certificado</th>
      <th style="background-color:#e3e3e3">Tipo de Pago</th>
      <th style="background-color:#e3e3e3">N° OC</th>
      <th style="background-color:#e3e3e3">Fecha OC</th>
      <th style="background-color:#e3e3e3">Fecha Actualizacion OC</th>
      <th style="background-color:#e3e3e3">Factura</th>
      <th style="background-color:#e3e3e3">Fecha Factura</th>
      <th style="background-color:#e3e3e3">Fecha Pago Factura</th>
      <th style="background-color:#e3e3e3">Estatus Factura</th>
      <th style="background-color:#e3e3e3">Monto Factura</th>
      <th style="background-color:#e3e3e3">Fecha Contable WeyPay</th>
      <th style="background-color:#e3e3e3">Fecha transaccion WeyPay</th>
      <th style="background-color:#e3e3e3">Monto WeyPay</th>
      <th style="background-color:#e3e3e3">Fecha subida Deposito</th>
      <th style="background-color:#e3e3e3">Fecha Deposito</th>
      <th style="background-color:#e3e3e3">Comentario Deposito</th>
      <th style="background-color:#e3e3e3">Tipo Deposito</th>
      <th style="background-color:#e3e3e3">Monto Deposito</th>
    </tr>
    </thead>
     <tbody>
    @foreach($reporteCertificacion as $datos)
    @isset($datos["id"])
    <tr>
      <td>
       {{$datos["id"]}}
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
       {{$datos["numeroTrabajadoresCertificar"]}}
     </td>
     <td>
       {{$datos["numeroTrabajadoresTotales"]}}
     </td>
     <td>
       {{$datos["numeroTrabajadoresCarga"]}}
     </td>
      <td>
       {{$datos["estadoCertificacion"]}}
     </td>
     <td>
       {{$datos["fechaCertificado"]}}
     </td>
     <td>
       {{$datos["ciclo"]}}
     </td>
     <td>
       {{$datos["certificador"]}}
     </td>
     <td>
       {{$datos["numeroCertificado"]}}
     </td>
     <td>
       {{$datos["tipoDePago"]}}
     </td>
     <td>
       {{$datos["numOC"]}}
     </td>
     <td>
       {{$datos["FechaOC"]}}
     </td>
     <td>
       {{$datos["FechaOCAct"]}}
     </td>
     <td>
       {{$datos["nunFactura"]}}
     </td>
     <td>
       {{$datos["fechaFactura"]}}
     </td>
      <td>
       {{$datos["fechaPago"]}}
     </td>
      <td>
       {{$datos["estatusFactura"]}}
     </td>
      <td>
       {{$datos["montoFactura"]}}
     </td>
      <td>
       {{$datos["fechacontableWebPay"]}}
     </td>
      <td>
       {{$datos["fechatransaccionWebPay"]}}
     </td>
      <td>
       {{$datos["montoWebPay"]}}
     </td>
     <td>
       {{$datos["fechaSubidaDeposito"]}}
     </td>
      <td>
       {{$datos["fechaDeposito"]}}
     </td>
     <td>
       {{$datos["comentarioDeposito"]}}
     </td>
      <td>
       {{$datos["tipoDeposito"]}}
     </td>
     <td>
       {{$datos["montoDeposito"]}}
     </td>
    </tr>  
  </tbody>
    @endisset
    @endforeach
    </table>