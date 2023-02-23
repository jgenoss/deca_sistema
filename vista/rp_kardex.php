<?php
session_start();
require_once '../includes/Config.php';

if (!isset($_SESSION['START'])) {
	unset($_SESSION['START']);
	session_destroy();
	header('location: index');
}

  require_once '../modelo/dbconnect.php';
  require_once '../modelo/kardex.php';

  $db = new dbconnect();
  $kardex = new kardex();


  if (isset($_GET['id'])):
	$id =	$_GET['id'];
  $rtn =Consult($db->sql("SELECT * FROM producto WHERE id_producto=".$id));
  $rtn_inv =Consult($db->sql("SELECT * FROM inventario WHERE id_producto=".$id));



  $rtne = AllConsult($kardex->getEntradaId($_GET['id']));
  $entradas = array();
  foreach ($rtne as $value) {
    $entradas[] = array(
      'type' =>'entrada',
      'fecha' =>$value->fecha_de_comprobante,
      'referencia' =>$value->referencia,
      'factura' =>$value->factura,
      'cantidad' =>$value->cantidad,
      'fecha_ven' =>$value->fecha_ven,
      'fv' =>$value->fv,
    );
  }

  $rtns = AllConsult($kardex->getSalidaId($_GET['id']));
  $salidas = array();
  foreach ($rtns as $value) {
		$rtn_inv_fecha = Consult($db->sql("SELECT * FROM inventario_fecha WHERE id_producto='$id' AND id_salida=".$value->id_salida));
    $salidas[] = array(
      'type' =>'salida',
      'fecha' =>$value->fecha_de_comprobante,
      'referencia' =>$value->referencia,
      'factura' =>$value->factura,
      'cantidad' =>$value->cantidad,
    );
  }
  $array_convined = array_merge($entradas,$salidas);



?>
<html lang="en" dir="ltr">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>kardex <?php print $rtn->nombre ?></title>
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../assets/plugins/sweetalert2/sweetalert2.min.css">
  <link rel="stylesheet" href="../assets/plugins/adminlte/css/adminlte.min.css">
  <link rel="stylesheet" href="../assets/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="../assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="../assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/summernote/summernote-bs4.min.css">
  <link rel="stylesheet" href="../assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
</head>
<body>
  <div class="tabla m-3">
    <table>
      <table id="list" class="table table-bordered table-striped text-center">
        <thead>
          <tr>
            <th colspan="3"><?php print $rtn->ean." ".$rtn->nombre ?></th>
            <th colspan="2">ENTRADA</th>
            <th>SALIDA</th>
            <th>SALDO</th>
          </tr>
          <tr>
            <th>FECHA</th>
            <th>REFERENCIA</th>
            <th>FACTURA</th>
            <th>FV</th>
            <th>UND</th>
            <th>UND</th>
            <th>UND</th>
          </tr>
        </thead>
        <tbody>
          <?php
						$sum_salida=0;
						$sum_entrada=0;
					 foreach ($array_convined as $key => $value) {

						 	$sum_salida += ($value['type'] == 'salida')? $value['cantidad']:0;
 							$sum_entrada += ($value['type'] == 'entrada')? $value['cantidad']:0;
						 ?>
            <tr>
              <td><?php print $value['fecha'] ?></td>
              <td><?php print $value['referencia'] ?></td>
              <td><?php print $value['factura'] ?></td>
              <td><?php print ($value['type'] == 'entrada' && $value['fv'] == 1)? $value['fecha_ven']:'' ?></td>
              <td><?php print ($value['type'] == 'entrada')? $value['cantidad']:'' ?></td>
              <td><?php print ($value['type'] == 'salida')? $value['cantidad']:'' ?></td>
              <td><?php print $sum_entrada-$sum_salida; ?></td>
            </tr>
          <?php } ?>
        </tbody>
				<tfoot>
					<tr>
						<th colspan="4"></th>
						<th><?php print $sum_entrada; ?></th>
						<th><?php print $sum_salida; ?></th>
						<th><?php print $rtn_inv->cantidad; ?></th>
					</tr>
				</tfoot>
      </table>
    </table>
  </div>
</body>
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="../assets/plugins/sweetalert2/sweetalert2.all.min.js" charset="utf-8"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/plugins/adminlte/js/adminlte.js"></script>
<script src="../assets/plugins/summernote/summernote-bs4.min.js"></script>
<script src="../assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script src="../assets/plugins/select2/js/select2.full.min.js"></script>
<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../assets/plugins/inputmask/jquery.inputmask.min.js"></script>
<script src="../assets/plugins/jszip/jszip.min.js"></script>
<script src="../assets/plugins/pdfmake/pdfmake.min.js"></script>
<script src="../assets/plugins/pdfmake/vfs_fonts.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="../assets/plugins/vue/vue.js"></script>
<script src="../assets/plugins/axios/axios.min.js"></script>
</html>
<script type="text/javascript">
  $(function () {
    $("#list").DataTable({
      dom:'Bfrtip',
      "buttons": ["copy", "excel"],
      "responsive": true,
      "autoWidth": false,
			"iDisplayLength": 25,
    });
  })
</script>
<?php endif; ?>
