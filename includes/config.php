<?php
if(isset($_SESSION['START'])){

  function permisos($id)
  {
    require_once 'modelo/dbconnect.php';
    $db = new dbconnect();
    return AllConsult($db->sql(
      "SELECT
      	m.id_modulo AS mId,
      	m.titulo AS mTitulo,
      	m.icono AS mIcono,
      	m.enlace AS mEnlace,
      	m.descripcion AS mDescripcion,
      	m.estado AS mEstado,
      	p.view AS pVer,
      	p.insert AS pCrear,
      	p.update AS pActualizar,
      	p.delete AS pBorrar
      FROM
      	modulos AS m
      	INNER JOIN
      	permisos AS p
      	ON
      		m.id_modulo = p.id_modulos
      WHERE
      	p.id_usuario = $id
      ORDER BY p.id_modulos ASC"
    ));
  }
}
function ConverUTF_8($value)
{
  return mb_convert_encoding($value, "UTF-8", "UTF-8");
}
function getUserIP() {
  $ipaddress = '';
  if (isset($_SERVER['HTTP_CLIENT_IP']))
      $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
  else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
      $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
  else if(isset($_SERVER['HTTP_X_FORWARDED']))
      $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
  else if(isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
      $ipaddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
  else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
      $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
  else if(isset($_SERVER['HTTP_FORWARDED']))
      $ipaddress = $_SERVER['HTTP_FORWARDED'];
  else if(isset($_SERVER['REMOTE_ADDR']))
      $ipaddress = $_SERVER['REMOTE_ADDR'];
  else
      $ipaddress = 'UNKNOWN';
  return $ipaddress;
}
function setMsg($tittle,$message,$type)
{
 echo json_encode(
   array(
     'tittle' => "$tittle",
     'message' => "$message",
     'type' => "$type"
   )
 );
}
function setJson($val)
{
 echo json_encode($val);
}
function Consult($val)
{
 return $val->fetch(PDO::FETCH_OBJ);
}
function AllConsult($val)
{
 return $val->fetchAll(PDO::FETCH_OBJ);
}
function ClearInput($val)
{
 $search = array("'",' ','"','/',',','.','+','-','<','>','$','%','?','¿','!','\n');
 return str_replace($search, '',$val);
}
function str_limit($cadena, $limite, $sufijo){
	// Si la longitud es mayor que el límite...
	if(strlen($cadena) > $limite){
		// Entonces corta la cadena y ponle el sufijo
		return substr($cadena, 0, $limite) . $sufijo;
	}

	// Si no, entonces devuelve la cadena normal
	return $cadena;
}
if (true) {
  function webengineConfigs() {
     $webengineConfigs = file_get_contents(''.dirname(__FILE__).'/web.json');
      return json_decode($webengineConfigs, true);
  }

  function config($config_name, $return = false) {
    $config = webengineConfigs();
      if($return) {
        return $config[$config_name];
      } else {
         echo $config[$config_name];
      }
  }
}

?>
