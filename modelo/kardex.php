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
  public function getEntradaId($id)
  {
    return $this->db->sql(
    "SELECT
    	entrada.fecha_de_comprobante,
    	entrada.referencia,
    	entrada.factura,
    	entrada_detalle.cantidad,
    	inventario_detallado.fv,
    	inventario_detallado.fecha_ven
    FROM
    	entrada_detalle
    	INNER JOIN inventario_detallado ON entrada_detalle.id_producto = inventario_detallado.id_producto
    	INNER JOIN entrada ON entrada_detalle.id_entrada = entrada.id_entrada
    	AND inventario_detallado.id_entrada = entrada.id_entrada
    WHERE
    	entrada_detalle.id_producto =$id");
  }
  public function getSalidaId($id)
  {
    return $this->db->sql(
    "SELECT
      salida.id_salida,
    	salida.fecha_de_comprobante,
    	salida.referencia,
    	salida.factura,
    	salida_detalle.cantidad
    FROM
    	salida
    	INNER JOIN salida_detalle ON salida.id_salida = salida_detalle.id_salida
    WHERE
    	salida_detalle.id_producto =$id");
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
      	SUM(entrada_detalle.cantidad) AS e_cantidad,
      	SUM(salida_detalle.cantidad) AS s_cantidad,
        SUM(entrada_detalle.cantidad-salida_detalle.cantidad) as entra_salida,
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
