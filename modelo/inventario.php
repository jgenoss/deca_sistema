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
  public function listarInventario()
  {
    return $this->db->sql(
      "SELECT
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
  }
}

?>
