<?php
if (isset($_GET['id_bodega'])) {
$bodega="";
header("Content-Type: application/vnd.ms-excel");
header("Content-disposition: attachment; filename=spreadsheet.xls");
require_once '../includes/Config.php';
require_once '../modelo/dbconnect.php';


    $id_bodega = $_GET['id_bodega'];
    $db = new dbconnect();

    $rtn = AllConsult($db->sql("SELECT
      	producto.codigo_1,
      	producto.codigo_2,
      	producto.ean,
      	producto.nombre,
      	bodega.nombre AS bodega
      FROM
      	producto
      	INNER JOIN inventario ON producto.id_producto = inventario.id_producto
      	INNER JOIN bodega ON producto.id_bodega = bodega.id_bodega
      WHERE
      	bodega.id_bodega = '$id_bodega'
      	AND inventario.status = 1
      	AND producto.status = 1 ORDER BY producto.codigo_1 ASC"));

    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" charset="utf-8"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>


    <div id="#content">
      <style media="screen">
      </style>

      <table id="table" style="border: 1px solid black;border-collapse: collapse;">
        <thead>
          <tr>
            <th colspan="3"><img src="../assets/img/logo.png" alt="" width="200px" height="60px"></th>
            <th ></th>
            <th ></th>
          </tr>
          <tr>
            <th colspan="3"><h3>LISTA DE RECUENTO</h3></th>
            <th ><?php print($rtn[0]->bodega); ?></th>
            <th ><?php print(date('Y-m-d h:i:s A')); ?></th>
          </tr>
          <tr>
            <th style="border: 1px solid black;border-collapse: collapse;">CODIGO 1</th>
            <th style="border: 1px solid black;border-collapse: collapse;">CODIGO 2</th>
            <th style="border: 1px solid black;border-collapse: collapse;">EAN</th>
            <th style="border: 1px solid black;border-collapse: collapse;">PRODUCTOS</th>
            <th style="border: 1px solid black;border-collapse: collapse;">CONTEO</th>
          </tr>
        </thead>
        <tbody>
    <?php
    foreach ($rtn as $key => $value) {
      $bodega = $value->bodega;?>
        <tr>
          <td style="border: 1px solid black;border-collapse: collapse;"><?php echo $value->codigo_1; ?></td>
          <td style="border: 1px solid black;border-collapse: collapse;"><?php echo $value->codigo_2; ?></td>
          <td style="border: 1px solid black;border-collapse: collapse;"><?php echo $value->ean; ?></td>
          <td style="border: 1px solid black;border-collapse: collapse;"><?php echo $value->nombre; ?></td>
          <td style="border: 1px solid black;border-collapse: collapse;"><?php echo "\n"; ?></td>
        </tr>

    <?php }
    ?></tbody>
    </table>
    </div>
    <script type="text/javascript">
      $(document).ready(function() {
        window.print();
      });
    </script>
    <?php

  }


?>
