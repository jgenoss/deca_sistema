<?php
require_once 'dbconnect.php';
/**
 *
 */
class inventario
{
  private $db;

  function __construct()
  {
    $this->db = new dbconnect();
  }
  public function listarInventario($type)
  {
    if ($type == "all") {
      return $this->db->sql(
        "SELECT
  	i.id_inventario,
  	p.codigo,
  	p.codigo_1,
  	p.codigo_2,
  	p.ean,
  	p.nombre AS pNombre,
  	c.nombre AS cNombre,
  	i.cantidad,
  	i.status,
  	p.umb,
  	b.nombre AS bNombre,
  	cl.empresa AS empresa,
  	u.usuario
  FROM
  	inventario AS i
  	INNER JOIN producto AS p ON i.id_producto = p.id_producto
  	INNER JOIN categoria AS c ON p.id_categoria = c.id_categoria
  	INNER JOIN usuario AS u ON i.id_usuario = u.id_usuario
  	INNER JOIN bodega AS b ON p.id_bodega = b.id_bodega
  	INNER JOIN clientes AS cl ON b.id_cliente = cl.id_cliente
  ORDER BY
  	i.id_inventario ASC"
      );
    }elseif ($type == "undefined") {
      return $this->db->sql(
        "SELECT
  	i.id_inventario,
  	p.codigo,
  	p.codigo_1,
  	p.codigo_2,
  	p.ean,
  	p.nombre AS pNombre,
  	c.nombre AS cNombre,
  	i.cantidad,
  	i.status,
  	p.umb,
  	b.nombre AS bNombre,
  	cl.empresa AS empresa,
  	u.usuario
  FROM
  	inventario AS i
  	INNER JOIN producto AS p ON i.id_producto = p.id_producto
  	INNER JOIN categoria AS c ON p.id_categoria = c.id_categoria
  	INNER JOIN usuario AS u ON i.id_usuario = u.id_usuario
  	INNER JOIN bodega AS b ON p.id_bodega = b.id_bodega
  	INNER JOIN clientes AS cl ON b.id_cliente = cl.id_cliente
  ORDER BY
  	i.id_inventario ASC"
      );
    } else {
      return $this->db->sql(
        "SELECT
  	i.id_inventario,
  	p.codigo,
  	p.codigo_1,
  	p.codigo_2,
  	p.ean,
  	p.nombre AS pNombre,
  	c.nombre AS cNombre,
  	i.cantidad,
  	i.status,
  	p.umb,
  	b.nombre AS bNombre,
  	cl.empresa AS empresa,
  	u.usuario
  FROM
  	inventario AS i
  	INNER JOIN producto AS p ON i.id_producto = p.id_producto
  	INNER JOIN categoria AS c ON p.id_categoria = c.id_categoria
  	INNER JOIN usuario AS u ON i.id_usuario = u.id_usuario
  	INNER JOIN bodega AS b ON p.id_bodega = b.id_bodega
  	INNER JOIN clientes AS cl ON b.id_cliente = cl.id_cliente
  WHERE
  	b.id_bodega = $type
  ORDER BY
  	i.id_inventario ASC"
      );
    }


  }
  public function getInventarioaId($id)
  {
    return $this->db->sql(
      "SELECT
      	i.id_inventario,
      	i.id_producto,
      	i.cantidad,
      	p.nombre
      FROM
      	inventario AS i
      	INNER JOIN producto AS p ON i.id_producto = p.id_producto
      WHERE
      	i.id_inventario ='$id'");
  }
  public function editInventario($value)
  {
    return $this->db->sql("UPDATE inventario SET cantidad='$value[1]' WHERE id_inventario='$value[0]'");
  }
}

?>
