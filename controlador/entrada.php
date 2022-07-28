<?php
session_start();
require_once '../includes/config.php';

$_POST = json_decode(file_get_contents("php://input"), true);

require_once '../modelo/dbconnect.php';
require_once '../modelo/entrada.php';

  $ent = new entrada();
  $db = new dbconnect();

  switch (@$_GET['op']) {
    case 'getClientes':
      {
        if (isset($_GET)) {
          $rtn = AllConsult($ent->getCliente());
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
    case 'getentradas':
      {
        if (isset($_GET)) {
          $rtn = AllConsult($ent->getentrada());
          $A = array();
          foreach ($rtn as $row) {
            $button='<button id="view" value="'.$row->id_entrada.'" class="btn btn-sm btn-warning"><i class="fas fa-eye"></i></button>';
            $A[] = array(
              '0' => $button,
              '1' => $row->empresa,
              '2' => $row->referencia,
              '3' => $row->factura,
              '4' => $row->serie,
              '5' => $row->created_at,
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
    case 'getInventario':
      {
        if (isset($_GET)) {
          $rtn = AllConsult($ent->getInventario($_GET['id']));
          $A = array();
          foreach ($rtn as $row) {
            $button='<button codigo="'.$row->ean.'" nombre="'.$row->pNombre.'" id="prod" value="'.$row->id_producto.'" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i></button>';
            $A[] = array(
              '0' => $button,
              '1' => '<span class="badge badge-primary">'.$row->codigo_1.'</span> <span class="badge badge-info">'.$row->codigo_2.'</span> <span class="badge badge-warning">'.$row->ean.'</span>',
              '2' => $row->pNombre,
              '3' => $row->cNombre,
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
    case 'getProductId':
      {
        if (isset($_POST)) {
          $rtn = Consult($ent->getProductId($_POST['id']));
          $A = array(
            'id' => $rtn->id_inventario,
            'producto' => $rtn->pNombre,
            'codigo' => $rtn->codigo
          );
          setJson($A);
        }
      }
      break;
    case 'regentrada':
      {

        if (isset($_POST)) {
          $A = array(
            '0' => $_POST['id_cliente'],
            '1' => $_POST['referencia'],
            '2' => $_POST['factura'],
            '3' => $_POST['fecha'],
            '4' => $_POST['serie'],
            '5' => $_POST['observacion'],
            '6' => $_POST['file'],
            '7' => $_POST['listp']
          );
          try {
            $A[6] = $ent->ConvertFilePDF($A[6]);
            $ent->setentrada($A,$_SESSION['START'][1]);
            setMsg("Info",'entrada Procesada con exito',"success");
          } catch (Exception $e) {
            setMsg("Error",$e->getMessage(),"error");
          }
        }
      }
      break;
    case 'valiDate':
      {
        if (isset($_POST)) {
           if ($_POST['cantidad'] <= 0) {
            setJson(array(
                'tittle' => 'ERROR',
                'message' => 'Por favor introdusca una cantidad valida',
                'type' => 'info',
                'max' => 0
              )
            );
          }
        }
      }
      break;
    case 'getConsecutivo':
      {
        if (isset($_GET)) {
          $rtn = Consult($db->sql("SELECT MAX(serie) as next  FROM entrada"));
          if ($rtn) {
            setJson(array('next' => $rtn->next+1));
          }else {
            setJson(array('next' => 1));
          }
        }
      }
      break;
    case 'getentradaId':
      {
        if (isset($_POST['id'])) {
          $rtn = Consult($ent->getentradaId($_POST['id']));
          $L = array();
          $rtn0 = AllConsult($ent->getentradaDetallada($rtn->serie));
          $total = 0;
          foreach ($rtn0 as $key) {
            $total += $key->cantidad;
            $L[] = array(
              'id' => $key->id_producto,
              'codigo' => $key->ean,
              'nombre' => $key->nombre,
              'cantidad' => $key->cantidad,
              'fv' => $key->fv,
              'fecha_v' => $key->fecha_v,
              'umb' => $key->umb,
              'cj' => round($key->cantidad/$key->umb)
            );
          }
          $A = array(
            'total' => $total,
            'id_cliente' => $rtn->id_cliente,
            'referencia' => $rtn->referencia,
            'factura' => $rtn->factura,
            'fecha' => $rtn->fecha_de_comprobante,
            'file' => $rtn->archivo,
            'serie' => $rtn->serie,
            'observacion' => $rtn->observacion,
            'listp' => $L
          );
          setJson($A);
        }
      }
      break;
    default:
      die("NULL");
      break;
  }
