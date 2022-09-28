<?php
session_start();
require_once '../includes/config.php';

$_POST = json_decode(file_get_contents("php://input"), true);

require_once '../modelo/dbconnect.php';
$db = new dbconnect();

switch (@$_GET['op']) {
	case 'listUsers':
		{
			if (isset($_GET)) {
				$rtn = AllConsult($db->sql("SELECT * FROM usuario"));
				$A = array();
				foreach ($rtn as $row) {
					$button = "";
					$A[] = array(
						$button,
						$row->usuario,
						$row->nombre
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
	case 'new':
		{
			if (isset($_POST)) {
				$A = array(
					$_POST['login'],
					$_POST['password'],
					$_POST['name'],
					$_POST['status']
				);
				try {
					$db->sql("INSERT INTO usuario(usuario, contrasena, nombre, habilitado)VALUES('$A[0]','$A[1]','$A[2]','$A[3]')");
					setMsg("ALERT","Usuario regisatrdo con exito","success");
				} catch (Exception $e) {
					setMsg("Error",$e->getMessage(),"error");
				}
			}
		}
		break;
	default:
			die();
		break;
}
?>
