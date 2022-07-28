<?php
session_start();
require_once '../includes/sql_check.php';
require_once '../includes/config.php';

$_POST = json_decode(file_get_contents("php://input"), true);

/*
if (check_inject() == true) {
  die(json_encode(array("injection"=> true)));
*/

require_once '../modelo/dbconnect.php';
require_once '../modelo/modulos.php';
require_once '../modelo/permisos.php';

  $db = new dbconnect();
  $md = new modulos();
  switch (@$_GET['op']) {
    case 'getModulos':
      {
        echo "string";
      }
      break;
    default:
        die("NULL");
      break;
  }

?>
