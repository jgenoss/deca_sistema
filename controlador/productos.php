<?php
session_start();
require_once '../includes/config.php';

$_POST = json_decode(file_get_contents("php://input"), true);

require_once '../modelo/dbconnect.php';
require_once '../modelo/productos.php';

  $prd = new productos();
  $db = new dbconnect();

  switch (@$_GET['op']) {
    case 'getProductos':
      {
        if (isset($_GET)) {
          $rtn = AllConsult($prd->listarProductos());
          $A = array();
          foreach ($rtn as $row) {
            $botones = '
                <div class="btn-group">
                    <button id="edit" value="'.$row->id_producto.'" class="btn btn-primary btn-sm" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button id="delete" value="'.$row->id_producto.'" class="btn btn-danger btn-sm" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            ';
            $A[] = array(
              $botones,
              $row->codigo,
              $row->codigo_1,
              $row->codigo_2,
              $row->ean,
              $row->pNombre,
              $row->umb,
              $row->empresa." / ".$row->bNombre,
              $row->cNombre,
              $row->created_at,
              ($row->status == 1)?
              '<span class="badge badge-success">ACTIVO</span>':
              '<span class="badge badge-danger">INACTIVO</span>',
              $row->uNombre
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
    case 'getTipoProducto':
      {
        if (isset($_GET)) {
          $rtn = AllConsult($prd->getCategoria());
          $A = array();
          foreach ($rtn as $row) {
            $A[] = array(
              'id' => $row->id_categoria,
              'nombre' => $row->nombre
            );
          }
          setJson($A);
        }
      }
      break;
    case 'getbodega':
        {
          if (isset($_GET)) {
            $rtn = AllConsult($prd->getBodega());
            $A = array();
            foreach ($rtn as $row) {
              $A[] = array(
                'id' => $row->id_bodega,
                'nombre' => $row->nombre
              );
            }
            setJson($A);
          }
        }
      break;
    case 'getProductoId':
      {
        if (isset($_POST['id'])) {
          $rtn = Consult($prd->getProductoId($_POST['id']));

          $A = array(
            'id' => $rtn->id_producto,
            'codigo' => $rtn->codigo,
            'code1' => ($rtn->codigo_1) ? $rtn->codigo_1 : "N/A",
            'code2' => ($rtn->codigo_2) ? $rtn->codigo_2 : "N/A",
            'ean' => $rtn->ean,
            'nombre' => $rtn->nombre,
            'status' => ($rtn->status == 1)? true:false,
            'estampilla' => $rtn->estampilla,
            'umb' => $rtn->umb,
            'id_bodega' => $rtn->id_bodega,
            'tipo' => $rtn->tipo,
            'tipo_val' => $rtn->tipo_val
          );

          setJson($A);
        }
      }
      break;
    case 'newEdit':
      {
        if (isset($_POST)) {
          $A = array(
            '0' => (!empty($_POST['id']))? $_POST['id']: '',
            '1' => $_POST['codigo'],
            '2' => $_POST['ean'],
            '3' => $_POST['nombre'],
            '4' => $_POST['id_categoria'],
            '5' => $_POST['status'],
            '6' => $_POST['estampilla'],
            '7' => $_POST['umb'],
            '8' => $_POST['id_bodega'],
            '9' => $_SESSION['START'][1],
            '10' => $_POST['code1'],
            '11' => $_POST['code2'],
            '12' => $_POST['tipo'],
            '13' => $_POST['tipo_val']
          );
          if (empty($_POST['id'])) {
              try {
                $prd->setProduct($A);
                setMsg("Exito"," Producto agregado con exito","success");
              } catch (Exception $e) {
                setMsg("Error",$e->getMessage(),"error");
              }
          }else {
            try {
              $prd->upProduct($A);
              setMsg("Exito"," Producto actualizado con exito","success");
            } catch (Exception $e) {
              setMsg("Error", $e->getMessage(),"error");
            }
          }
        }
      }
      break;
    case 'eliminar':
        if (isset($_POST['id'])) {
            $id = intval($_POST['id']);
            if($prd->eliminar($id)){
                echo json_encode(['status' => true, 'msg' => 'Producto y todo su historial eliminado correctamente.']);
            } else {
                echo json_encode(['status' => false, 'msg' => 'Error al eliminar el producto.']);
            }
        }
        break;
    default:
        die("NULL");
      break;
  }

?>
