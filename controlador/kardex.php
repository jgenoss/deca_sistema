<?php
session_start();
require_once '../includes/config.php';

$_POST = json_decode(file_get_contents("php://input"), true);

require_once '../modelo/dbconnect.php';
require_once '../modelo/kardex.php';
$db = new dbconnect();
$kr = new kardex();
switch (@$_GET['op']) {
  case 'getProduct':
      {
        if (isset($_POST['txt'])) {
          $txt = $_POST['txt'];
          $query = AllConsult($kr->search($_POST['txt']));
          $A = array();
          foreach ($query as $key => $value) {
            $button="";
            $A[] = array(
              'id' => $value->id_producto,
              'ean' => $value->ean,
              'nombre' => $value->nombre,
              'button' => $button
            );
          }
          setJson($A);
        }
      }


    break;
  case 'addKardex':
      {
        if (isset($_POST['id'])) {
          $query = AllConsult($kr->queryKardex($_POST['id']));
          $A = array();

          foreach ($query as $key => $value) {
            $A[] = array(
              'p_id' => $value->p_id,
              'p_ean' => $value->p_ean,
              'p_nombre' => $value->p_nombre,
              'e_cantidad' => $value->e_cantidad,
              's_cantidad' => $value->s_cantidad,
              'i_cantidad' => $value->i_cantidad
            );
          }
          setJson($A);
        }
      }
    break;
  default:
      die(null);
      exit();
    break;
}
?>
