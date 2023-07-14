<?php
require_once 'dbconnect.php';
/**
 *
 */
class salida
{

  private $db;
  private $fecha;
  private $fecha_actual;
  function __construct()
  {
    $this->db = new dbconnect();
    $this->fecha = date('Y-m-')."01";
    $this->fecha_actual = date('Y-m-').date('d')+1;
  }
  public function getCliente()
  {
    return $this->db->sql("SELECT * FROM clientes WHERE habilitado = 1");
  }

  public function getSalida()
  {
    //$fecha = date('Y-m-')."01";
    //$fecha_actual = date('Y-m-').date('d')+1;
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
      	INNER JOIN clientes AS cl ON s.id_cliente = cl.id_cliente WHERE devolucion = 0 ORDER BY s.created_at DESC LIMIT 500");
  }
  public function getDevolucion()
  {
    return $this->db->sql(
      "SELECT
        s.id_devolucion,
      	cl.empresa,
      	s.factura,
      	s.created_at,
      	s.referencia,
      	s.serie,
      	s.tpago,
      	s.observacion
      FROM
      	devolucion AS s
      	INNER JOIN clientes AS cl ON s.id_cliente = cl.id_cliente ORDER BY s.created_at DESC");
  }
  public function getInventario($val)
  {
    return $this->db->sql(
      "SELECT
        	p.ean,
        	p.id_producto,
        	p.codigo,
        	p.codigo_1,
        	p.codigo_2,
        	p.nombre AS pNombre,
        	c.nombre AS cNombre
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
        	clientes AS cl
        	ON
        		b.id_cliente = cl.id_cliente
        WHERE
        	cl.id_cliente = $val AND p.status=1"
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
  public function setDevolucion($val,$id_session)
  {
    $query = $this->db->sql("INSERT INTO devolucion ( id_cliente, referencia, factura, fecha_de_comprobante, serie, observacion, tpago ,id_salida,archivo,tdevolucion,cantidad)VALUES('$val[0]','$val[1]','$val[2]','$val[3]','$val[4]','$val[5]','$val[6]','$val[7]','$val[9]','$val[10]','$val[11]')");
    $last_id = $this->db->lastInsertId();
    if ($query) {
      for ($i=0; $i < count($val[8]); $i++) {

        $id = $val[8][$i]['id'];
        $cantidad = $val[8][$i]['cantidad'];
        $id_salida = $val[7];

        $query = $this->db->sql("INSERT INTO devolucion_detalle(id_devolucion,id_serie,id_producto,cantidad)VALUES('$last_id','$val[4]','$id','$cantidad')");
        if ($this->db->sql("SELECT * FROM salida WHERE id_salida ='$id_salida'")) {
          $query = $this->db->sql("UPDATE salida SET devolucion = 1 WHERE id_salida ='$id_salida'");
          $this->db->sql("INSERT INTO movimientos (fecha,tipo,cantidad,producto_id,referencia,factura,fv,fecha_vencimiento)VALUES('$val[3]','devolucion','$cantidad','$id','$val[1]','$val[2]','0','0000-00-00')");
        }
        if($this->db->Consult($this->db->sql("SELECT * FROM inventario WHERE id_producto=".$id))){
          $this->db->sql("UPDATE inventario SET cantidad=cantidad+'$cantidad' WHERE id_producto =".$id);
        }else {
          $this->db->sql("INSERT INTO inventario (id_producto,id_usuario,cantidad)VALUES('$id','$id_session','$cantidad')");
        }
      }
    }
    return $query;
  }
  public function getSalidaId($val)
  {
    return $this->db->sql("SELECT * FROM salida WHERE id_salida = '$val'");
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
  public function getDevolucionDetallada($val)
  {
    return $this->db->sql(
      "SELECT
        	sd.id_producto,
        	p.ean,
        	sd.cantidad,
        	sd.id,
        	p.nombre
        FROM
        	devolucion_detalle AS sd
        	INNER JOIN producto AS p ON sd.id_producto = p.id_producto
        WHERE
        	sd.id_devolucion =$val");
  }
  public function ConvertFilePDF($b64)
  {
    $bin = base64_decode(str_replace('data:application/pdf;base64,','', $b64), true);
    $namefile = time().'_'.date("Y-m-d").'_DEVOLUCION'.'.pdf';
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
