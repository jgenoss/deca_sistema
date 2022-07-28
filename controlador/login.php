<?php
session_start();
$_POST = json_decode(file_get_contents("php://input"), true);
require_once '../includes/config.php';
require_once '../modelo/dbconnect.php';
  $db = new dbconnect();

  switch (@$_GET['op']) {
    case 'setLogin':
      {
        if (isset($_POST)) {
          $user = ClearInput($_POST['user']);
          $pass = ClearInput($_POST['pass']);
          $rtn = Consult($db->select_condition("usuario","usuario = '$user' AND contrasena = '$pass'"));
          if ($rtn) {
            $_SESSION['START'] = array(
              "0" => true,
              "1" => $rtn->id_usuario,
              "2" => $rtn->usuario,
            );
            setMsg('Info','Iniciando session','success');
          }else {
            setMsg('Info','Usuario o Contraseña incorrectos','error');
          }
        }
      }
      break;
    case 'destroySession':
      {
        if (isset($_GET)) {
          unset($_SESSION['START']);
          session_destroy();
          setJson(array('status' => true));
        }
      }
      break;
    default:
        die("NULL");
      break;
  }

?>
