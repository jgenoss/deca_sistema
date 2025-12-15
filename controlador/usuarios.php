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
				$rtn = AllConsult($db->sql("SELECT * FROM usuario ORDER BY id_usuario DESC"));
				$A = array();
				foreach ($rtn as $row) {
					$button = '
					<button id="permissions" value="'.$row->id_usuario.'" class="btn btn-sm btn-info" title="Permisos"><i class="fas fa-key"></i></button>
					<button id="edit" value="'.$row->id_usuario.'" class="btn btn-sm btn-warning" title="Editar"><i class="fas fa-edit"></i></button>
					<button id="disabled" value="'.$row->id_usuario.'" class="btn btn-sm btn-'.($row->habilitado == 1 ? 'success' : 'danger').'" title="'.($row->habilitado == 1 ? 'Activo' : 'Inactivo').'">
						<i class="fas fa-'.($row->habilitado == 1 ? 'check' : 'ban').'"></i>
					</button>';
					$A[] = array(
						$button,
						$row->usuario,
						$row->nombre,
						($row->habilitado==1)? '<span class="badge badge-success">ACTIVO</span>':'<span class="badge badge-danger">INACTIVO</span>'
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
		
	case 'getUserById':
		{
			if (isset($_POST['id'])) {
				$id = $_POST['id'];
				$rtn = Consult($db->sql("SELECT * FROM usuario WHERE id_usuario = $id"));
				if ($rtn) {
					setJson(array(
						'id_usuario' => $rtn->id_usuario,
						'usuario' => $rtn->usuario,
						'nombre' => $rtn->nombre,
						'habilitado' => $rtn->habilitado
					));
				} else {
					setJson(array('error' => 'Usuario no encontrado'));
				}
			}
		}
		break;
		
	case 'new':
		{
			if (isset($_POST)) {
				$usuario = trim($_POST['login']);
				$password = trim($_POST['password']);
				$nombre = trim($_POST['name']);
				$status = $_POST['status'];
				
				// Validar que no exista el usuario
				$existe = Consult($db->sql("SELECT * FROM usuario WHERE usuario = '$usuario'"));
				if ($existe) {
					setMsg("ERROR", "El usuario ya existe", "error");
					break;
				}
				
				// Hash de la contraseña
				$pepper = "sadnadiuaswa989898f2v484jy874j2yuk984";
				$peppered = hash_hmac("sha256", $password, $pepper);
				$hashed = password_hash($peppered, PASSWORD_DEFAULT);
				
				try {
					$db->sql("INSERT INTO usuario(usuario, contrasena, nombre, habilitado) VALUES('$usuario','$hashed','$nombre','$status')");
					setMsg("EXITO", "Usuario registrado con éxito", "success");
				} catch (Exception $e) {
					setMsg("Error", $e->getMessage(), "error");
				}
			}
		}
		break;
		
	case 'update':
		{
			if (isset($_POST)) {
				$id = $_POST['id_usuario'];
				$usuario = trim($_POST['login']);
				$nombre = trim($_POST['name']);
				$status = $_POST['status'];
				
				// Validar que no exista otro usuario con el mismo nombre
				$existe = Consult($db->sql("SELECT * FROM usuario WHERE usuario = '$usuario' AND id_usuario != $id"));
				if ($existe) {
					setMsg("ERROR", "Ya existe otro usuario con ese nombre", "error");
					break;
				}
				
				try {
					$sql = "UPDATE usuario SET usuario='$usuario', nombre='$nombre', habilitado='$status' WHERE id_usuario=$id";
					
					// Si se proporciona nueva contraseña
					if (!empty($_POST['password'])) {
						$password = trim($_POST['password']);
						$pepper = "sadnadiuaswa989898f2v484jy874j2yuk984";
						$peppered = hash_hmac("sha256", $password, $pepper);
						$hashed = password_hash($peppered, PASSWORD_DEFAULT);
						$sql = "UPDATE usuario SET usuario='$usuario', contrasena='$hashed', nombre='$nombre', habilitado='$status' WHERE id_usuario=$id";
					}
					
					$db->sql($sql);
					setMsg("EXITO", "Usuario actualizado con éxito", "success");
				} catch (Exception $e) {
					setMsg("Error", $e->getMessage(), "error");
				}
			}
		}
		break;
		
	case 'disabled':
		{
			if (isset($_POST)) {
				$id = $_POST['id'];
				$user = Consult($db->sql("SELECT habilitado FROM usuario WHERE id_usuario = $id"));
				$newStatus = $user->habilitado == 1 ? 0 : 1;
				$mensaje = $newStatus == 1 ? "habilitado" : "deshabilitado";
				
				try {
					$db->sql("UPDATE usuario SET habilitado=$newStatus WHERE id_usuario=$id");
					setMsg("EXITO", "Usuario $mensaje con éxito", "success");
				} catch (Exception $e) {
					setMsg("Error", $e->getMessage(), "error");
				}
			}
		}
		break;
		
	case 'getModulos':
		{
			if (isset($_GET)) {
				$modulos = AllConsult($db->sql("SELECT * FROM modulos WHERE estado = 1 ORDER BY titulo"));
				$data = array();
				foreach ($modulos as $modulo) {
					$data[] = array(
						'id_modulo' => $modulo->id_modulo,
						'titulo' => $modulo->titulo,
						'icono' => $modulo->icono,
						'descripcion' => $modulo->descripcion
					);
				}
				setJson($data);
			}
		}
		break;
		
	case 'getPermisos':
		{
			if (isset($_POST['id_usuario'])) {
				$id_usuario = $_POST['id_usuario'];
				
				// Obtener todos los módulos con sus permisos
				$modulos = AllConsult($db->sql("
					SELECT 
						m.id_modulo,
						m.titulo,
						m.descripcion,
						COALESCE(p.id_permiso, 0) as id_permiso,
						COALESCE(p.view, 0) as view,
						COALESCE(p.insert, 0) as `insert`,
						COALESCE(p.update, 0) as `update`,
						COALESCE(p.delete, 0) as `delete`
					FROM modulos m
					LEFT JOIN permisos p ON m.id_modulo = p.id_modulos AND p.id_usuario = $id_usuario
					WHERE m.estado = 1
					ORDER BY m.titulo
				"));
				
				$data = array();
				foreach ($modulos as $modulo) {
					$data[] = array(
						'id_modulo' => $modulo->id_modulo,
						'id_permiso' => $modulo->id_permiso,
						'titulo' => $modulo->titulo,
						'descripcion' => $modulo->descripcion,
						'view' => $modulo->view,
						'insert' => $modulo->insert,
						'update' => $modulo->update,
						'delete' => $modulo->delete
					);
				}
				setJson($data);
			}
		}
		break;
		
	case 'savePermisos':
		{
			if (isset($_POST)) {
				$id_usuario = $_POST['id_usuario'];
				$permisos = $_POST['permisos'];
				
				try {
					// Eliminar permisos existentes
					$db->sql("DELETE FROM permisos WHERE id_usuario = $id_usuario");
					
					// Insertar nuevos permisos
					foreach ($permisos as $permiso) {
						$id_modulo = $permiso['id_modulo'];
						$view = $permiso['view'] ? 1 : 0;
						$insert = $permiso['insert'] ? 1 : 0;
						$update = $permiso['update'] ? 1 : 0;
						$delete = $permiso['delete'] ? 1 : 0;
						
						// Solo insertar si al menos tiene un permiso activado
						if ($view || $insert || $update || $delete) {
							$db->sql("
								INSERT INTO permisos (id_usuario, id_modulos, view, `insert`, `update`, `delete`) 
								VALUES ($id_usuario, $id_modulo, $view, $insert, $update, $delete)
							");
						}
					}
					
					setMsg("EXITO", "Permisos actualizados correctamente", "success");
				} catch (Exception $e) {
					setMsg("Error", $e->getMessage(), "error");
				}
			}
		}
		break;
		
	default:
		die("NULL");
		break;
}
?>