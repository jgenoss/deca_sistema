<?php
/**
 *
 */
class bodega
{

  private $db;

  function __construct()
  {
    $this->db = new dbconnect();
  }
  public function listarBodega()
  {
    return $this->db->sql(
      "SELECT
      	b.nombre,
      	b.id_bodega,
      	b.descripcion,
      	b.status,
      	c.empresa
      FROM
      	bodega AS b
      	INNER JOIN clientes AS c ON b.id_cliente = c.id_cliente"
    );
  }
  public function getCliente()
  {
    return $this->db->sql("SELECT * FROM clientes");
  }
  public function getBodegaId($val)
  {
    return $this->db->sql("SELECT * FROM bodega WHERE id_bodega='$val'");
  }
  public function setBodega($val)
  {
    return $this->db->sql(
      "INSERT INTO bodega ( id_cliente, nombre, descripcion, status, id_usuario )
      VALUES
      	('$val[1]','$val[2]','$val[3]','$val[4]','$val[5]')"
    );
  }
  public function upBodega($val)
  {
    return $this->db->sql(
      "UPDATE bodega
      SET id_cliente='$val[1]',
      nombre='$val[2]',
      descripcion='$val[3]',
      status='$val[4]',
      id_usuario='$val[5]'
      WHERE
      	id_bodega='$val[0]'"
    );
  }
}

?>
