<?php
session_start();
require_once '../includes/config.php';

$_POST = json_decode(file_get_contents("php://input"), true);

require_once '../modelo/dbconnect.php';
switch (@$_GET['op']) {
	case 'listUsers':
		{	
			if (isset($_GET)) {
				echo "string";
			}
		}	
		break;
	case 'addUser':
		{
			if (isset($_POST)) {
				var_dump($_POST);
			}
		}
		break;
	default:
			die();
		break;
}
?>