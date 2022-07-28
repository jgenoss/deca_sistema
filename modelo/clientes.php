<?php
require_once 'dbconnect.php';
/**
 *
 */
class clientes
{
  private $db;

  function __construct()
  {
    $this->db = new dbconnect();
  }
  public function listarClientes()
  {
    return $this->db->sql(
      "SELECT
        c.id_cliente,
        c.empresa,
      	c.nombre,
      	c.telefono,
      	c.direccion,
      	c.habilitado,
      	u.nombre AS uNombre,
      	c.tipo_documento,
      	c.documento,
      	c.correo
      FROM
      	clientes AS c
      	INNER JOIN usuario AS u ON c.id_usuario = u.id_usuario"
    );
  }
  public function getClienteId($val)
  {
    return $this->db->sql("SELECT * FROM clientes WHERE id_cliente = '$val'");
  }
  public function setClient($val)
  {
    return $this->db->sql("INSERT INTO clientes(nombre, telefono, direccion, habilitado, id_usuario, tipo_documento, documento, correo,empresa) VALUES ('$val[1]', '$val[2]', '$val[3]', '$val[4]', '$val[5]', '$val[6]', '$val[7]', '$val[8]', '$val[9]');");
  }
  public function upCliente($val)
  {
    return $this->db->sql("UPDATE clientes SET nombre='$val[1]', telefono='$val[2]', direccion='$val[3]', habilitado='$val[4]', id_usuario='$val[5]', tipo_documento='$val[6]', documento='$val[7]', correo='$val[8]', empresa='$val[9]' WHERE id_cliente = $val[0]");
  }
}

?>
