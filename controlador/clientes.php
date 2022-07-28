<?php
session_start();
require_once '../includes/config.php';

$_POST = json_decode(file_get_contents("php://input"), true);

require_once '../modelo/dbconnect.php';
require_once '../modelo/clientes.php';

  $cl = new clientes();
  $db = new dbconnect();
  switch (@$_GET['op']) {
    case 'getClientes':
      {
        if (isset($_GET)) {
          $rtn = AllConsult($cl->listarClientes());
          $A = array();
          foreach ($rtn as $row) {
            $A[] = array(
              ($row->id_cliente)?'<button id="edit" value="'.$row->id_cliente.'" class="btn btn-primary"><i class="fas fa-edit"></i></button>':'<button id="edit" value="'.$row->id_cliente.'" class="btn btn-primary"><i class="fas fa-edit"></i></button>',
              $row->nombre,
              $row->empresa,
              $row->telefono,
              $row->direccion,
              ($row->habilitado == 1)?
              '<span class="badge badge-success">ACTIVO</span>':
              '<span class="badge badge-danger">INACTIVO</span>',
              $row->uNombre,
              $row->tipo_documento."-".$row->documento,
              $row->correo
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
    case 'getClienteId':
      {
        if (isset($_POST['id'])) {
          $rtn = Consult($cl->getClienteId(ClearInput($_POST['id'])));
          $A = array(
            'id_cliente' => $rtn->id_cliente,
            'nombre' => $rtn->nombre,
            'empresa' => $rtn->empresa,
            'telefono' => $rtn->telefono,
            'direccion' => $rtn->direccion,
            'habilitado' => ($rtn->habilitado == 1)? true:false,
            'id_usuario' => $rtn->id_usuario,
            'tipo_documento' => $rtn->tipo_documento,
            'documento' => $rtn->documento,
            'correo' => $rtn->correo
          );
          setJson($A);
        }
      }
      break;
    case 'newEdit':
      {
        if (isset($_POST)) {
          if (empty($_POST['id_cliente'])) {
            $A = array(
              '1' => $_POST['nombre'],
              '2' => $_POST['telefono'],
              '3' => $_POST['direccion'],
              '4' => ($_POST['habilitado']==true)?1:0,
              '5' => $_SESSION['START'][1],
              '6' => $_POST['tipo_documento'],
              '7' => $_POST['documento'],
              '8' => $_POST['correo'],
              '9' => $_POST['empresa']
            );
              try {
                $cl->setClient($A);
                setMsg("Exito"," Cliente agregado con exito","success");
              } catch (Exception $e) {
                setMsg("Error",$e->getMessage(),"error");
              }
          }else {
            $A = array(
              '0' => $_POST['id_cliente'],
              '1' => $_POST['nombre'],
              '2' => $_POST['telefono'],
              '3' => $_POST['direccion'],
              '4' => ($_POST['habilitado']==true)?1:0,
              '5' => $_SESSION['START'][1],
              '6' => $_POST['tipo_documento'],
              '7' => $_POST['documento'],
              '8' => $_POST['correo'],
              '9' => $_POST['empresa']
            );
            try {
              $cl->upCliente($A);
              setMsg("Exito"," Cliente actualizado con exito","success");
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
