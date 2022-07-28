<?php
require_once 'dbconnect.php';
/**
 *
 */
class entrada
{

  private $db;

  function __construct()
  {
    $this->db = new dbconnect();
  }
  public function getCliente()
  {
    return $this->db->sql("SELECT * FROM bodega WHERE status = 1");
  }
  public function getentrada()
  {
    return $this->db->sql(
      "SELECT
        s.id_entrada,
      	cl.empresa,
      	s.factura,
      	s.created_at,
      	s.referencia,
      	s.serie,
      	s.observacion
      FROM
      	entrada AS s
      	INNER JOIN clientes AS cl ON s.id_cliente = cl.id_cliente");
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
      WHERE
      	b.id_bodega = $val"
    );
  }
  public function getProductId($val)
  {
    return $this->db->sql(
      "SELECT
      	p.codigo,
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
  public function setentrada($val,$id_session)
  {
    $query = $this->db->sql("INSERT INTO entrada ( id_cliente, referencia, factura, fecha_de_comprobante, serie, observacion, archivo)VALUES('$val[0]','$val[1]','$val[2]','$val[3]','$val[4]','$val[5]','$val[6]')");
    if ($query) {
      for ($i=0; $i < count($val[7]); $i++) {

        $id = $val[7][$i]['id'];
        $cantidad = $val[7][$i]['cantidad'];
        $fv = $val[7][$i]['fv'];
        $fecha_v = $val[7][$i]['fecha_v'];

        $query = $this->db->sql("INSERT INTO entrada_detalle(id_serie,id_producto,cantidad)VALUES('$val[4]','$id','$cantidad')");
        if ($fv) {
          $this->db->sql("INSERT INTO inventario_detallado (id_producto,id_usuario,cantidad,id_serie,fv,fecha_ven)VALUES('$id','$id_session','$cantidad','$val[4]','$fv','$fecha_v')");
        }else {
          $this->db->sql("INSERT INTO inventario_detallado (id_producto,id_usuario,cantidad,id_serie,fv)VALUES('$id','$id_session','$cantidad','$val[4]','$fv')");
        }
        $rtn = $this->db->Consult($this->db->sql("SELECT * FROM inventario WHERE id_producto=".$id));
        if($rtn){
          $this->db->sql("UPDATE inventario SET cantidad=cantidad+'$cantidad' WHERE id_producto =".$id);
        }else {
          $this->db->sql("INSERT INTO inventario (id_producto,id_usuario,cantidad)VALUES('$id','$id_session','$cantidad')");
        }
      }
    }
    return $query;
  }
  public function getentradaId($val)
  {
    return $this->db->sql("SELECT * FROM entrada WHERE id_entrada = $val");
  }
  public function getentradaDetallada($val)
  {
    return $this->db->sql(
      "SELECT
      	sd.id_producto,
      	p.ean,
      	sd.cantidad,
      	sd.id,
      	p.nombre,
        p.umb,
      	inv_d.fv,
      	inv_d.fecha_ven AS fecha_v
      FROM
      	entrada_detalle AS sd
      	INNER JOIN producto AS p ON sd.id_producto = p.id_producto
      	INNER JOIN inventario_detallado AS inv_d ON p.id_producto = inv_d.id_producto
      	AND sd.cantidad = inv_d.cantidad
      WHERE
      	sd.id_serie = $val");
  }
  public function ConvertFilePDF($b64)
  {
    $bin = base64_decode(str_replace('data:application/pdf;base64,','', $b64), true);
    $namefile = time().'_'.date("Y-m-d").'_ENTRADA'.'.pdf';
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
