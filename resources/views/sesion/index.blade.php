<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Certilap Reportes</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="{{ asset("/AdminLTE-2.3.11/bootstrap/css/bootstrap.min.css") }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset("/AdminLTE-2.3.11/dist/css/AdminLTE.min.css") }}">
  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect.
  -->
  <link rel="stylesheet" href="{{ asset("/AdminLTE-2.3.11/dist/css/skins/skin-blue.min.css") }}">

<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 2.2.3 -->
<script src="{{ asset("/AdminLTE-2.3.11/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ asset("/AdminLTE-2.3.11/plugins/jQueryUI/jquery-ui.min.js") }}"></script>
<!-- Bootstrap 3.3.6 -->
<script src="{{ asset("/AdminLTE-2.3.11/bootstrap/js/bootstrap.min.js") }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset("/AdminLTE-2.3.11/dist/js/app.min.js") }}"></script>
</head>


           
      

 <div class="modal modal-success" id="modal_success">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="cerrarModal">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Session Expirada</h4>
              </div>
              <div class="modal-body">
                <p>Su session ha caducado</p>
              </div>
              <div class="modal-footer">
                <button type="button" id="cerrarModal2" class="btn btn-outline pull-left">cerrar</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>

<!-- /.row -->


    <!-- /.content -->

<link rel="stylesheet" href="{{ asset("/AdminLTE-2.3.11/plugins/select2/select2.min.css") }}">
<script src="{{ asset("/AdminLTE-2.3.11/plugins/select2/select2.min.js") }}""></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<script src="{{ asset("/AdminLTE-2.3.11/plugins/moment/moment.js") }}""></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/locale/es.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<link rel="stylesheet" href="{{ asset("/AdminLTE-2.3.11/plugins/toaster/toastr.min.css") }}">
<script src="{{ asset("/AdminLTE-2.3.11/plugins/toaster/toastr.min.js") }}""></script>

<!-- SlimScroll -->
<script src="{{ asset("/AdminLTE-2.3.11/plugins/slimScroll/jquery.slimscroll.min.js") }}""></script>
<!-- FastClick -->
<script src="{{ asset("/AdminLTE-2.3.11/plugins/fastclick/fastclick.js") }}""></script>

</script>
<script type="text/javascript">
$(function () {

     $('#modal_success').show();

    $("#cerrarModal2").click(function(){
      $('#modal_success').hide();
      var url = "https://sistema.certilapchile.cl/index.php?aa=login&cn=login";    
      $(location).attr('href',url);
    })
 });


</script>
 
</html>