<?php
require_once 'dbconnect.php';
/**
 *
 */
class kardex
{
  private $db;

  function __construct()
  {
    $this->db = new dbconnect();
  }
  public function search($value)
  {
      return $this->db->sql(
        "SELECT
      	 *
        FROM
        	producto
        WHERE
        	codigo_1 LIKE '%$value'
        	OR codigo_2 LIKE '%$value'
        	OR ean LIKE '%$value'
        	OR nombre LIKE '$value%' LIMIT 5");
  }
  public function queryKardex($id)
  {
    return $this->db->sql(
      "SELECT
        producto.id_producto AS p_id,
      	producto.nombre AS p_nombre,
      	producto.ean AS p_ean,
      	entrada_detalle.cantidad AS e_cantidad,
      	salida_detalle.cantidad AS s_cantidad,
      	inventario.cantidad AS i_cantidad
      FROM
      	salida_detalle
      	INNER JOIN
      	producto
      	ON
      		salida_detalle.id_producto = producto.id_producto
      	INNER JOIN
      	entrada_detalle
      	ON
      		entrada_detalle.id_producto = producto.id_producto
      	INNER JOIN
      	inventario
      	ON
      		producto.id_producto = inventario.id_producto
      	INNER JOIN
      	salida
      	ON
      		salida_detalle.id_serie = salida.serie
      	INNER JOIN
      	entrada
      	ON
      		entrada_detalle.id_serie = entrada.serie
      WHERE
      	producto.id_producto = $id
      GROUP BY
      	producto.id_producto");
  }
}

?>
