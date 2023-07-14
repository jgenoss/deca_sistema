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
  public function deleteInventario($id)
  {
    $query = $this->db->sql("DELETE FROM producto WHERE id_producto = '$id'");
    $query = $this->db->sql("DELETE FROM inventario WHERE id_producto = '$id'");
    return $query;
  }
  public function duplicate($id)
  {
    $query = $this->db->Consult($this->db->sql("SELECT * FROM producto WHERE id_producto = '$id'"));
    if ($query) {
      $A = array(
        '0' => $query->codigo,
        '1' => $query->ean,
        '2' => $query->nombre,
        '3' => $query->id_categoria,
        '4' => '0',//status 0
        '5' => $query->estampilla,
        '6' => $query->umb,
        '7' => $query->id_bodega,
        '8' => $query->id_usuario,
        '9' => $query->codigo_1,
        '10' => $query->codigo_2,
        '11' => $query->tipo,
        '12' => $query->tipo_val
      );
      $rtn = $this->db->sql("INSERT INTO producto (codigo, ean, nombre, id_categoria, status, estampilla, umb, id_bodega, id_usuario, codigo_1, codigo_2,tipo,tipo_val)VALUES
      ('$A[0]','$A[1]','$A[2]','$A[3]','0','$A[5]','$A[6]','57','$A[8]','$A[9]','$A[10]','$A[11]','$A[12]')");
      $last_id = $this->db->lastInsertId();

      $this->db->sql("INSERT INTO inventario (id_producto,id_usuario,cantidad,status)VALUES('$last_id','$A[8]','0','0')");
      return $rtn;
    }elseif (!$query) {
      return null;
    }
  }
  public function listarInventario($type)
  {
    if ($type == "all") {
      return $this->db->sql(
        "SELECT
        	i.id_inventario,
          p.id_producto,
        	p.codigo,
        	p.codigo_1,
        	p.codigo_2,
        	p.ean,
          p.tipo_val,
        	p.nombre AS pNombre,
        	c.nombre AS cNombre,
        	i.cantidad,
        	p.umb,
        	b.nombre AS bNombre,
        	cl.empresa AS empresa,
          p.id_bodega,
          i.status
        FROM
        	inventario AS i
        	INNER JOIN producto AS p ON i.id_producto = p.id_producto
        	INNER JOIN categoria AS c ON p.id_categoria = c.id_categoria
        	INNER JOIN bodega AS b ON p.id_bodega = b.id_bodega
        	INNER JOIN clientes AS cl ON b.id_cliente = cl.id_cliente"
      );
    }elseif ($type == "undefined") {
      return $this->db->sql(
        "SELECT
        	i.id_inventario,
          p.id_producto,
        	p.codigo,
        	p.codigo_1,
        	p.codigo_2,
        	p.ean,
          p.tipo_val,
        	p.nombre AS pNombre,
        	c.nombre AS cNombre,
        	i.cantidad,
        	p.umb,
        	b.nombre AS bNombre,
        	cl.empresa AS empresa,
          p.id_bodega,
          i.status
        FROM
        	inventario AS i
        	INNER JOIN producto AS p ON i.id_producto = p.id_producto
        	INNER JOIN categoria AS c ON p.id_categoria = c.id_categoria
        	INNER JOIN bodega AS b ON p.id_bodega = b.id_bodega
        	INNER JOIN clientes AS cl ON b.id_cliente = cl.id_cliente"
      );
    } else {
      return $this->db->sql(
        "SELECT
        	i.id_inventario,
          p.id_producto,
        	p.codigo,
        	p.codigo_1,
        	p.codigo_2,
        	p.ean,
          p.tipo_val,
        	p.nombre AS pNombre,
        	c.nombre AS cNombre,
        	i.cantidad,
        	p.umb,
        	b.nombre AS bNombre,
        	cl.empresa AS empresa,
          p.id_bodega,
          i.status
        FROM
        	inventario AS i
        	INNER JOIN producto AS p ON i.id_producto = p.id_producto
        	INNER JOIN categoria AS c ON p.id_categoria = c.id_categoria
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
      	i.*,
      	p.*
      FROM
      	inventario AS i
      	INNER JOIN producto AS p ON i.id_producto = p.id_producto
      WHERE
      	i.id_inventario ='$id'");
  }
  public function editInventario($value)
  {
     $this->db->sql("UPDATE inventario SET cantidad='$value[1]',status='$value[4]' WHERE id_inventario='$value[0]'");
    return $this->db->sql("UPDATE producto SET nombre='$value[2]',umb='$value[3]',status='$value[4]' WHERE id_producto='$value[5]'");
  }
}

?>
