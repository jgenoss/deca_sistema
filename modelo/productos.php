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
      		b.id_cliente = cl.id_cliente AND cl.habilitado = 1"
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
    $rtn = $this->db->sql("INSERT INTO producto ( codigo, ean, nombre, id_categoria, status, estampilla, umb, id_bodega, id_usuario, codigo_1, codigo_2,tipo,tipo_val)VALUES ('$val[1]','$val[2]','$val[3]','$val[4]','$val[5]','$val[6]','$val[7]','$val[8]','$val[9]','$val[10]','$val[11]','$val[12]','$val[13]')");
    $last_id = $this->db->lastInsertId();
    $id_session = $val[9];
    $this->db->sql("INSERT INTO inventario (id_producto,id_usuario,cantidad)VALUES('$last_id','$id_session','0')");
    return $rtn;
  }
  public function upProduct($val)
  {
    $this->db->sql("UPDATE inventario SET status = '$val[5]' WHERE id_producto = '$val[0]'");
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
      codigo_2 = '$val[11]',
      tipo = '$val[12]',
      tipo_val = '$val[13]'
      WHERE id_producto = '$val[0]'"
    );
  }
  public function eliminar($id)
  {
        // 1. Eliminar Movimientos (Kardex) - Nota: en esta tabla se llama 'producto_id'
      $this->db->sql("DELETE FROM movimientos WHERE producto_id = $id");

        // 2. Eliminar detalles de operaciones
      $this->db->sql("DELETE FROM devolucion_detalle WHERE id_producto = $id");
      $this->db->sql("DELETE FROM salida_detalle WHERE id_producto = $id");
      $this->db->sql("DELETE FROM entrada_detalle WHERE id_producto = $id");

        // 3. Eliminar inventarios
      $this->db->sql("DELETE FROM inventario_detallado WHERE id_producto = $id");
      $this->db->sql("DELETE FROM inventario_fecha WHERE id_producto = $id");
      $this->db->sql("DELETE FROM inventario WHERE id_producto = $id");

        // 4. Finalmente eliminar el producto
      return $this->db->sql("DELETE FROM producto WHERE id_producto = $id");
  }
}
?>
