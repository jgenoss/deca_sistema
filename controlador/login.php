<?php
session_start();
$_POST = json_decode(file_get_contents("php://input"), true);
require_once '../includes/config.php';
require_once '../modelo/dbconnect.php';
  $db = new dbconnect();

  switch (@$_GET['op']) {
    case 'setLogin':
      {
        if (!empty($_POST['user']) && !empty($_POST['pass'])) {
            $user = trim($_POST['user']);
            $pass = trim($_POST['pass']);

            $sql = "SELECT * FROM usuario WHERE usuario = :usuario";
            $stmt = $db->con()->prepare($sql);
            $stmt->execute([":usuario" => $user]);

            $rtn = $db->Consult($stmt);

            if ($rtn) {
                $pepper   = "sadnadiuaswa989898f2v484jy874j2yuk984";
                $peppered = hash_hmac("sha256", $pass, $pepper);

                if (password_verify($peppered, $rtn->contrasena)) {
                    session_regenerate_id(true);

                    $_SESSION['START'] = [
                        "0" => true,
                        "1" => $rtn->id_usuario,
                        "2" => $rtn->usuario,
                    ];

                    setMsg('Info', 'Iniciando sesión', 'success');
                } else {
                    setMsg('Info', 'Usuario o Contraseña incorrectos', 'error');
                }
            } else {
                setMsg('Info', 'Usuario o Contraseña incorrectos', 'error');
            }
        } else {
            setMsg('Error', 'Faltan credenciales', 'error');
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
