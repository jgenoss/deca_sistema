<?php
session_start();
require_once '../includes/sql_check.php';
require_once '../includes/config.php';

$_POST = json_decode(file_get_contents("php://input"), true);

require_once '../modelo/dbconnect.php';

  $db = new dbconnect();
  switch (@$_GET['op']) {
    case 'Modulos':
      {
        if (isset($_GET)) {
          
        }
      }
      break;
    default:
        die("NULL");
      break;
  }

?>
