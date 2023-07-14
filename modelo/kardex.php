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
    	entra.fecha_de_comprobante,
    	entra.referencia,
    	entra.factura,
    	entra_deta.cantidad,
    	inve_deta.fv,
    	inve_deta.fecha_ven
    FROM
    	entrada_detalle AS entra_deta
    	INNER JOIN
    	inventario_detallado AS inve_deta
    	ON
    		entra_deta.id_producto = inve_deta.id_producto
    	INNER JOIN
    	entrada AS entra
    	ON
    		entra_deta.id_entrada = entra.id_entrada AND
    		inve_deta.id_entrada = entra.id_entrada
    WHERE
    	entra_deta.id_producto =$id");
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
        WHERE status = 1 AND
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
