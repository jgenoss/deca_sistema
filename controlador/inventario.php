<?php
session_start();
require_once '../includes/config.php';

$_POST = json_decode(file_get_contents("php://input"), true);

require_once '../modelo/inventario.php';

  $inv = new inventario();

  switch (@$_GET['op']) {
    case 'getInventario':
      {
        if (isset($_GET)) {
          $rtn = AllConsult($inv->listarInventario());
          $A = array();
          foreach ($rtn as $row) {
            $A[] = array(
              $row->codigo,
              '<span class="badge badge-primary">'.$row->codigo_1.'</span> <span class="badge badge-info">'.$row->codigo_2.'</span>',
              $row->ean,
              $row->pNombre,
              $row->empresa." / ".$row->bNombre,
              $row->cNombre,
              $row->cantidad,
              $row->umb,
              round($row->cantidad/$row->umb),
              ($row->status == 1)?
              '<span class="badge badge-success">ACTIVO</span>':
              '<span class="badge badge-danger">AGOTADO</span>'
            );
          }
          setJson(array(
            "sEcho"=> 1,
            "iTotalRecords" => count($A),
            "iTotalDisplayRecords" => count($A),
            "data" => $A
          ));
        }
      }
      break;
    default:
        die("NULL");
      break;
  }

?>
