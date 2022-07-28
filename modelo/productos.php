<?php
require_once 'dbconnect.php';
/**
 *
 */
class productos
{
  private $db;

  function __construct()
  {
    $this->db = new dbconnect();
  }
  public function listarProductos()
  {
    return $this->db->sql(
      "SELECT
      	p.id_producto,
      	p.codigo,
      	p.ean,
      	p.nombre AS pNombre,
      	p.status,
      	p.estampilla,
      	p.umb,
      	p.created_at,
        p.codigo_1,
        p.codigo_2,
      	c.nombre AS cNombre,
      	b.nombre AS bNombre,
      	u.nombre AS uNombre,
      	cl.empresa AS empresa
      FROM
      	producto AS p
      	INNER JOIN
      	categoria AS c
      	ON
      		p.id_categoria = c.id_categoria
      	INNER JOIN
      	bodega AS b
      	ON
      		p.id_bodega = b.id_bodega
      	INNER JOIN
      	usuario AS u
      	ON
      		p.id_usuario = u.id_usuario
      	INNER JOIN
      	clientes AS cl
      	ON
      		b.id_cliente = cl.id_cliente"
    );
  }
  public function getCategoria()
  {
    return $this->db->sql("SELECT * FROM categoria");
  }
  public function getBodega()
  {
    return $this->db->sql("SELECT * FROM bodega");
  }
  public function getProductoId($id)
  {
    return $this->db->sql("SELECT * FROM producto WHERE id_producto = $id");
  }
  public function setProduct($val)
  {
    return $this->db->sql("INSERT INTO producto ( codigo, ean, nombre, id_categoria, status, estampilla, umb, id_bodega, id_usuario, codigo_1, codigo_2)VALUES ('$val[1]','$val[2]','$val[3]','$val[4]','$val[5]','$val[6]','$val[7]','$val[8]','$val[9]','$val[10]','$val[11]')");
  }
  public function upProduct($val)
  {
    return $this->db->sql(
      "UPDATE producto
      SET codigo = '$val[1]',
      ean = '$val[2]',
      nombre = '$val[3]',
      id_categoria = '$val[4]',
      status = '$val[5]',
      estampilla = '$val[6]',
      umb = '$val[7]',
      id_bodega = '$val[8]',
      id_usuario = '$val[9]',
      codigo_1 = '$val[10]',
      codigo_2 = '$val[11]'
      WHERE id_producto = '$val[0]'"
    );
  }
}

?>
