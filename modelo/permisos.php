<?php

require_once 'dbconnect.php';
/**
 *
 */
class permisos
{
  private $db;

  function __construct()
  {
    $this->db = new dbconnect();
  }
  public function permisos($val)
  {
    return $this->db->sql(
      "SELECT
      	m.id_modulo AS mId,
      	m.titulo AS mTitulo,
      	m.icono AS mIcono,
      	m.enlace AS mEnlace,
      	m.descripcion AS mDescripcion,
      	m.estado AS mEstado,
      	p.view AS pVer,
      	p.insert AS pCrear,
      	p.update AS pActualizar,
      	p.delete AS pBorrar
      FROM
      	modulos AS m
      	INNER JOIN
      	permisos AS p
      	ON
      		m.id_modulo = p.id_modulos
      WHERE
      	p.id_usuario = $id"
    );
  }
}
?>
