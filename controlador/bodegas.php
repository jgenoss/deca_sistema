<?php
session_start();
require_once '../includes/config.php';

$_POST = json_decode(file_get_contents("php://input"), true);

require_once '../modelo/dbconnect.php';
require_once '../modelo/bodegas.php';

  $bd = new bodega();
  $db = new dbconnect();
  switch (@$_GET['op']) {
    case 'getBodegas':
      {
        if (isset($_GET)) {
          $rtn = AllConsult($bd->listarBodega());
          $A = array();
          foreach ($rtn as $row) {
            $A[] = array(
              ($row->id_bodega)?'<button id="edit" value="'.$row->id_bodega.'" class="btn btn-primary"><i class="fas fa-edit"></i></button>':'<button id="edit" value="'.$row->id_bodega.'" class="btn btn-primary"><i class="fas fa-edit"></i></button>',
              $row->empresa,
              $row->descripcion,
              ($row->status == 1)?
              '<span class="badge badge-success">ACTIVO</span>':
              '<span class="badge badge-danger">INACTIVO</span>'
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
    case 'getCliente':
      {
          if (isset($_GET)) {
          $rtn = AllConsult($bd->getCliente());
          $A = array();
          foreach ($rtn as $row) {
            $A[] = array(
              'id' => $row->id_cliente,
              'nombre' => $row->empresa
            );
          }
          setJson($A);
        }
      }
      break;
    case 'getBodegaId':
      {
        if (isset($_POST['id'])) {
          $rtn = Consult($bd->getBodegaId($_POST['id']));
          setJson($rtn);
        }
      }
      break;
    case 'newEdit':
      {
        if (isset($_POST)) {
          $A = array(
            '0' => $_POST['id_bodega'],
            '1' => $_POST['id_cliente'],
            '2' => $_POST['nombre'],
            '3' => $_POST['descripcion'],
            '4' => $_POST['status'],
            '5' => $_SESSION['START'][1]
          );
          if (empty($_POST['id_bodega'])) {
            try {
              $bd->setBodega($A);
              setMsg("Exito"," Bodega agregada con exito","success");
            } catch (Exception $e) {
                setMsg("Error",$e->getMessage(),"error");
            }
          }else {
            try {
              $bd->upBodega($A);
              setMsg("Exito"," Bodega actualizada con exito","success");
            } catch (Exception $e) {
              setMsg("Error", $e->getMessage(),"error");
            }
          }
        }
      }
      break;
    default:
      // code...
      break;
  }
?>
