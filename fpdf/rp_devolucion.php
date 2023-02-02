<?php

if(!isset($_GET['id'])){
  exit();
} else if(isset($_GET['id'])){
    require ("fpdf.php");
    // require ("invoice.php");
    require ("../modelo/dbconnect.php");
    require ("../includes/config.php");
    //customer and invoice details

    $db = new dbconnect();

    $rtn[0] = Consult($db->sql("SELECT * FROM devolucion WHERE id_devolucion = ".$_GET['id']));
    $rtn[1] = Consult($db->sql("SELECT * FROM clientes WHERE id_cliente = ".$rtn[0]->id_cliente));

    $rtn[2] = AllConsult($db->sql(
      "SELECT
      	p.*,
      	sd.*
      FROM
      	devolucion_detalle AS sd
      	INNER JOIN
      	producto AS p
      	ON
      		sd.id_producto = p.id_producto
      WHERE
      	sd.id_serie =".$rtn[0]->serie));

    foreach ($rtn[2] as $key) {
      $products[] = array(
        'id' => $key->id_producto,
        'codigo_0' => $key->ean,
        'codigo_1' => $key->codigo_1,
        'codigo_2' => $key->codigo_2,
        'nombre' => utf8_decode($key->nombre),
        'cantidad' => $key->cantidad,
      );

    }

    $info = array(
      'cliente' => $rtn[1]->empresa,
      'referencia' => $rtn[0]->referencia,
      'factura' => $rtn[0]->factura,
      'fecha' => $rtn[0]->fecha_de_comprobante,
      'observacion' => $rtn[0]->observacion,
      'listp' => $products
    );
  class PDF extends FPDF
  {
    function Header(){
      //Display Company Info
      // $this->Line(0,10,210,10);
      // LOGO
      $this->Image("../assets/img/logo.png",10,10,50,0,"PNG");
      // posicion de la informacion
      $this->SetY(15);
      $this->SetFont('Arial','',8);
      $this->Cell(0,3,"Cra 29 No. 42-37,",0,1,'C');
      $this->Cell(0,3,"SECTOR SANTA CRUZ.",0,1,'C');
      $this->Cell(0,3,"BODEGA 1A de la MZ 3",0,1,'C');
      $this->Cell(0,3,"Santa Marta, Magdalena, Colombia",0,1,'C');
      $this->Ln();
    }

    function body($info){

      $this->SetY(10);
      $this->SetX(-55);
      $this->SetFont('Arial','B',8);
      $this->Cell(0,6,"FECHA: ".$info['fecha'],1,1,"C");

      $this->SetY(20);
      $this->SetX(-70);
      $this->SetFont('Arial','B',8);
      $this->Cell(60,6,"FACTURA: ".$info['factura'],1,1,"C");

      $this->SetY(30);
      $this->SetX(-60);
      $this->SetFont('Arial','',8);
      $this->Cell(0,6,"TIPO DE DEVOLUCION: COMPLETA",1,0,"R");

      // CLIENTE
      $this->SetY(30);
      $this->SetX(10);
      $this->SetFont('Arial','',9);
      $this->Cell(140,8,"CLIENTE: ".$info['cliente'],1,0,"L");
      $this->Ln();
      $this->Cell(140,8,"REFERENCIA: ".$info['referencia'],1,1,"L");
      //Display Table headings

      $this->SetY(45);
      $this->SetX(10);
      $this->SetFont('Arial','B',12);
      $this->Cell(0,8,"CONCEPTO DE DEVOLUCION",0,1,"C");
      $this->SetFont('Arial','B',10);

      $this->Cell(170,7,"DESCRIPCION DEL PRODUCTO",1,0,"C");
      $this->Cell(26,7,"CANTIDAD",1,1,"C");

      //Display table product rows
      $this->SetFont('Arial','',10);
      foreach($info['listp'] as $row){
        $this->Cell(170,6,str_limit($row["codigo_1"]." - / - ".$row["nombre"],'100','...'),1,0,"L");
        $this->Cell(26,6,$row["cantidad"],1,0,"C");
        $this->Ln();
      }
      //Display table empty rows
      for($i=0;$i<28-count($info['listp']);$i++)
      {
        $this->Cell(170,6,"",1,0);
        $this->Cell(26,6,"",1,0);
        $this->Ln();
      }

      $this->SetY(235);
      $this->SetFont('Arial','B',9);
      $this->Cell(0,4,"OBSERVACION",0,1,'C');
      $obs = explode("\n",$info['observacion']);
      $this->SetFont('Arial','',7);

      foreach ($obs as $row) {
        $this->Cell(0,3,utf8_decode($row),0,1,"L");
      }
      $this->SetY(239);
      $this->Cell(0,20,"",1,1,"L");

    }
    function Footer(){

      $this->SetY(-20);
      $this->SetFont('Arial','I',8);
      $this->Cell(0,8,"Este es un comprobante digital generado por computadora.",0,1,"C");
      $this->Cell(0,8,'Page '.$this->PageNo().'/{nb}',0,0,'C');

    }
    function RoundedRect($x, $y, $w, $h, $r, $style = '')
    {
    	$k = $this->k;
    	$hp = $this->h;
    	if($style=='F')
    		$op='f';
    	elseif($style=='FD' || $style=='DF')
    		$op='B';
    	else
    		$op='S';
    	$MyArc = 4/3 * (sqrt(2) - 1);
    	$this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));
    	$xc = $x+$w-$r ;
    	$yc = $y+$r;
    	$this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));

    	$this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
    	$xc = $x+$w-$r ;
    	$yc = $y+$h-$r;
    	$this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
    	$this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
    	$xc = $x+$r ;
    	$yc = $y+$h-$r;
    	$this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
    	$this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
    	$xc = $x+$r ;
    	$yc = $y+$r;
    	$this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
    	$this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
    	$this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
    	$h = $this->h;
    	$this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
    						$x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }

    function Rotate($angle, $x=-1, $y=-1)
    {
    	if($x==-1)
    		$x=$this->x;
    	if($y==-1)
    		$y=$this->y;
    	if($this->angle!=0)
    		$this->_out('Q');
    	$this->angle=$angle;
    	if($angle!=0)
    	{
    		$angle*=M_PI/180;
    		$c=cos($angle);
    		$s=sin($angle);
    		$cx=$x*$this->k;
    		$cy=($this->h-$y)*$this->k;
    		$this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
    	}
    }
  }
  //Create A4 Page with Portrait

  $pdf=new PDF("P","mm","letter");
  $pdf->AliasNbPages();
  $pdf->AddPage();
  $pdf->body($info);
  $pdf->Output("I","ENTRADA(test).pdf");
}
?>
