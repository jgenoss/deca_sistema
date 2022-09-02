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
            $button = '<button id="edit" value="'.$row->id_inventario.'" data-toggle="modal" data-target="#modal-edit" class="btn btn-primary"><i class="fas fa-edit"></i></button>';
            $A[] = array(
              $button,
              $row->codigo,
              '<span class="badge badge-primary">'.$row->codigo_1.'</span> <span class="badge badge-info">'.$row->codigo_2.'</span>',
              $row->ean,
              $row->pNombre,
              $row->bNombre,
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
    case 'getInventarioaId':
      {
        if (isset($_POST)) {
          $rtn = Consult($inv->getInventarioaId($_POST['id']));
            $A = array(
              'id_inventario' => $rtn->id_inventario,
              'id_producto' => $rtn->id_producto,
              'cantidad' => $rtn->cantidad,
              'nombre' => $rtn->nombre
            );
          setJson($A);
        }
      }
      break;
    case 'edit':
      {
        if (isset($_POST)) {
          try {
            $inv->editInventario(array($_POST['id_inventario'],$_POST['cantidad']));
            setMsg('Exito','Existencia modificada con exito','success');
          }catch (Exception $e) {
            setMsg("Error",$e->getMessage(),"error");
          }
        }
      }
      break;
    default:
        die("NULL");
      break;
  }

?>
