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
          $rtn = AllConsult($inv->listarInventario($_GET['bodega_id']));
          $A = array();
          foreach ($rtn as $row) {
            $button = ($row->status == 1)? '
            <button id="delete" value="'.$row->id_producto.'" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
            <button id="edit" value="'.$row->id_inventario.'" data-toggle="modal" data-target="#modal-edit" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></button>
            <button id="duplicate" value="'.$row->id_producto.'" class="btn btn-sm btn-info"><i class="fas fa-clone"></i></button>':'<button id="delete" value="'.$row->id_producto.'" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
            <button id="edit" value="'.$row->id_inventario.'" data-toggle="modal" data-target="#modal-edit" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></button>';
            try {
              $caja = round($row->cantidad/$row->umb);
            } catch (DivisionByZeroError $e) {
              $caja= "0";
            }
            $A[] = array(
              $button,
              $row->codigo_1,
              $row->codigo_2,
              $row->ean,
              ($row->id_bodega == 52)? $row->pNombre." ".$row->tipo_val:$row->pNombre,
              $row->empresa,
              $row->bNombre,
              $row->cNombre,
              $row->cantidad,
              $caja,
              $row->umb,
              ($row->status == 1)?
              '<span class="badge badge-success">ACTIVO</span>':
              '<span class="badge badge-danger">INACTIVO</span>',
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
              'nombre' => $rtn->nombre,
              'umb' => $rtn->umb,
              'status' => $rtn->status
            );
          setJson($A);
        }
      }
      break;
    case 'edit':
      {
        if (isset($_POST)) {
          try {
            $inv->editInventario(array(
              $_POST['id_inventario'],//0
              $_POST['cantidad'],//1
              $_POST['nombre'],//2
              $_POST['umb'],//3
              $_POST['status'],//4
              $_POST['id_producto']//5
            ));
            setMsg('Exito','Existencia modificada con exito','success');
          }catch (Exception $e) {
            setMsg("Error",$e->getMessage(),"error");
          }
        }
      }
      break;
    case 'delete':
      {
        if (isset($_POST)) {
          try {
            $inv->deleteInventario($_POST['id']);
            setMsg('Exito','Producto eliminado con exito','success');
          }catch (Exception $e) {
            setMsg("Error",$e->getMessage(),"error");
          }
        }
      }
      break;
    case 'duplicate':
      {
        if (isset($_POST)) {
          try {
            $inv->duplicate($_POST['id']);
            setMsg('Exito','Producto duplicado con exito','success');
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
