<?php
require_once 'dbconnect.php';
/**
 *
 */
class salida
{

  private $db;

  function __construct()
  {
    $this->db = new dbconnect();
  }
  public function getCliente()
  {
    return $this->db->sql("SELECT * FROM clientes WHERE habilitado = 1");
  }
  public function getSalida()
  {
    return $this->db->sql(
      "SELECT
        s.id_salida,
      	cl.empresa,
      	s.factura,
      	s.created_at,
      	s.referencia,
      	s.serie,
      	s.tpago,
      	s.observacion
      FROM
      	salida AS s
      	INNER JOIN clientes AS cl ON s.id_cliente = cl.id_cliente");
  }
  public function getInventario($val)
  {
    return $this->db->sql(
      "SELECT
      	p.ean,
      	p.codigo,
      	p.codigo_1,
      	p.codigo_2,
        i.id_producto,
      	i.id_inventario,
      	p.nombre AS pNombre,
      	i.cantidad,
      	c.nombre AS cNombre
      FROM
      	inventario AS i
      	INNER JOIN producto AS p ON i.id_producto = p.id_producto
      	INNER JOIN categoria AS c ON p.id_categoria = c.id_categoria
      	INNER JOIN bodega AS b ON p.id_bodega = b.id_bodega
      	INNER JOIN clientes AS cl ON b.id_cliente = cl.id_cliente
      WHERE
      	i.STATUS = 1
      	AND cantidad > 0
      	AND cl.id_cliente = $val"
    );
  }
  public function getProductId($val)
  {
    return $this->db->sql(
      "SELECT
      	p.codigo,
        i.id_producto,
      	i.id_inventario,
      	p.nombre AS pNombre,
      	p.ean,
      	i.cantidad,
      	i.status,
      	c.nombre AS cNombre
      FROM
      	inventario AS i
      	INNER JOIN producto AS p ON i.id_producto = p.id_producto
      	INNER JOIN categoria AS c ON p.id_categoria = c.id_categoria
      WHERE
      	i.status = 1
      	AND i.id_inventario = '$val'"
    );
  }
  public function setSalida($val)
  {
    $query = $this->db->sql("INSERT INTO salida ( id_cliente, referencia, factura, fecha_de_comprobante, serie, observacion, tpago, archivo)VALUES('$val[0]','$val[1]','$val[2]','$val[3]','$val[4]','$val[5]','$val[6]','$val[7]')");
    if ($query) {
      for ($i=0; $i < count($val[8]); $i++) {
        $id = $val[8][$i]['id'];
        $cantidad = $val[8][$i]['cantidad'];
        $query = $this->db->sql("INSERT INTO salida_detalle(id_serie,id_producto,cantidad)VALUES('$val[4]','$id','$cantidad')");
        $query = $this->db->sql("UPDATE inventario SET cantidad=cantidad-'$cantidad'  WHERE id_producto =".$id);
      }
    }
    return $query;
  }
  public function getSalidaId($val)
  {
    return $this->db->sql("SELECT * FROM salida WHERE id_salida = $val");
  }
  public function getsalidaDetallada($val)
  {
    return $this->db->sql(
      "SELECT
        	sd.id_producto,
        	p.ean,
        	sd.cantidad,
        	sd.id,
        	p.nombre
        FROM
        	salida_detalle AS sd
        	INNER JOIN producto AS p ON sd.id_producto = p.id_producto
        WHERE
        	sd.id_serie =$val");
  }
  public function ConvertFilePDF($b64)
  {
    $bin = base64_decode(str_replace('data:application/pdf;base64,','', $b64), true);
    $namefile = time().'_'.date("Y-m-d").'_SALIDA'.'.pdf';
    $ext = '../';
    $file_dir = 'upload_files/';
    try {
      file_put_contents($ext.$file_dir.$namefile, $bin);
      return $file_dir.$namefile;
    } catch (Exception $e) {
      if (strpos($bin, '%PDF') !== 0);
      return $e->getMessage();
    }
  }
}

?>
