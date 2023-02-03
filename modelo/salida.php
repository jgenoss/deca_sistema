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
  public function getUndCaj($id)
  {
    $cantidad = 0;
    $cajas = 0;
    $variable = $this->db->AllConsult($this->db->sql("SELECT
        ent.cantidad,
        pro.umb
      FROM
        salida_detalle AS ent
        INNER JOIN producto AS pro ON ent.id_producto = pro.id_producto
      WHERE
        ent.id_salida = $id
      GROUP BY
        pro.id_producto"));
    foreach ($variable as $key => $value) {
      $cantidad += $value->cantidad;
      $cajas += (round($value->cantidad/$value->umb) < 1) ? 1 : round($value->cantidad/$value->umb);
    }
    return array(
      'cantidad' => $cantidad,
      'cajas' => $cajas,
    );

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
      	s.observacion,
      	s.devolucion,
      	s.fecha_de_comprobante
      FROM
      	salida AS s
      	INNER JOIN clientes AS cl ON s.id_cliente = cl.id_cliente
      ORDER BY
      	s.created_at DESC");
  }
  public function getListDate($var)
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
      	s.observacion,
      	s.devolucion,
      	s.fecha_de_comprobante
      FROM
      	salida AS s
      	INNER JOIN clientes AS cl ON s.id_cliente = cl.id_cliente
      WHERE
      	fecha_de_comprobante BETWEEN '$var[0]'
      	AND '$var[1]'");
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
      	c.nombre AS cNombre,
        b.nombre AS bNombre
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
    $query = $this->db->sql("INSERT INTO salida ( id_cliente, referencia, factura, fecha_de_comprobante, serie, observacion,direccion, tpago, archivo)VALUES('$val[0]','$val[1]','$val[2]','$val[3]','$val[4]','$val[5]','$val[6]','$val[7]','$val[8]')");
    $last_id = $this->db->lastInsertId();
    if ($query) {
      for ($i=0; $i < count($val[9]); $i++) {
        $id = $val[9][$i]['id'];
        $cantidad = $val[9][$i]['cantidad'];
        $query = $this->db->sql("INSERT INTO salida_detalle(id_salida,id_serie,id_producto,cantidad)VALUES('$last_id','$val[4]','$id','$cantidad')");
        $query = $this->db->sql("UPDATE inventario SET cantidad=cantidad-'$cantidad' WHERE id_producto = $id AND status = 1");
        $queryi = $this->db->Consult($this->db->sql("SELECT * FROM inventario WHERE id_producto =".$id));
        if ($queryi):
          $inv_cantidad = $queryi->cantidad;
          $this->db->sql("INSERT INTO inventario_fecha(id_salida,	id_producto, cantidad ,fecha)VALUES('$last_id','$id','$inv_cantidad','$val[3]')");
        endif;

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
        	sd.id_salida =$val");
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
