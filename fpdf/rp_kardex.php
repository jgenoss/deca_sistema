<?php
  if(!isset($_GET['id'])){
    exit();
  } else if(isset($_GET['id'])){
    require ("fpdf.php");
    // require ("invoice.php");
    require ("../modelo/dbconnect.php");
    require ("../includes/config.php");
    //customer and invoice details
    $id = $_GET['id'];
    $db = new dbconnect();

    $query = AllConsult($db->sql("SELECT * FROM entrada_detalle INNER JOIN producto ON entrada_detalle.id_producto = producto.id_producto INNER JOIN entrada ON entrada.id_entrada = entrada_detalle.id_entrada WHERE producto.id_producto ='$id'"));


    $data = array();
    foreach ($query as $key => $value) {
      if ($query) {
        $id_entrada = $value->id_entrada;
        $id_producto = $value->id_producto;
        $fv = Consult($db->sql("SELECT * FROM inventario_detallado WHERE inventario_detallado.id_entrada = '$id_entrada' AND inventario_detallado.id_producto = '$id_producto'"));
        // var_dump($fv);exit();
      }
      $data[] = array(
        'fecha' => $value->fecha_de_comprobante,
        'referencia' => $value->referencia,
        'factura' => $value->factura,
        'fv' => '',
        'entrada' => $value->cantidad,
        'salida' => '',
        'saldo' => ''
      );
    }

    // $this->Cell(20,7,$info['list'],1,0,"C");
    // $this->Cell(135,7,"REFERENCIA",1,0,"C");
    // $this->Cell(25,7,"FACTURA",1,0,"C");
    // $this->Cell(20,7,"FV",1,0,"C");
    // $this->Cell(20,7,"ENTREDA",1,0,"C");
    // $this->Cell(20,7,"SALIDA",1,0,"C");
    // $this->Cell(20,7,"SALDO",1,1,"C");
    $info = array(
      'list' => $data
    );

  class PDF extends FPDF
  {
    function Header(){
      //Display Company Info
      // $this->Line(0,10,210,10);
      // LOGO
      $this->Image("../assets/img/logo.png",10,10,50,0,"PNG");

      //center
      $this->SetFont('Arial','B',12);
      $this->Cell(0,3,"DECA SOLUCIONES LOGISTICA",0,1,'C');
      $this->SetY(15);
      $this->SetFont('Arial','',8);
      $this->Cell(0,3,"Cra 29 No. 42-37,",0,1,'C');
      $this->Cell(0,3,"SECTOR SANTA CRUZ.",0,1,'C');
      $this->Cell(0,3,"BODEGA 1A de la MZ 3",0,1,'C');
      $this->Cell(0,3,"Santa Marta, Colombia",0,1,'C');

      //Factura
      $this->SetY(10);
      $this->SetFont('Arial','',12);
      // $this->Cell(0,3,"FACTURA ELECTRONICA",0,1,'R');

      //Display Horizontal line
      // $this->Line(0,48,210,48);
      $this->SetY(25);
      $this->SetFont('Arial','B',12);
      $this->Cell(0,10,"REPORTE DE KARDEX",0,1,'C');
    }
    function body($info){
      $this->SetY(35);
      $this->SetX(10);
      $this->SetFont('Arial','B',10);
      $this->Cell(20,7,"FECHA",1,0,"C");
      $this->Cell(135,7,"REFERENCIA",1,0,"C");
      $this->Cell(25,7,"FACTURA",1,0,"C");
      $this->Cell(20,7,"FV",1,0,"C");
      $this->Cell(20,7,"ENTRADA",1,0,"C");
      $this->Cell(20,7,"SALIDA",1,0,"C");
      $this->Cell(20,7,"SALDO",1,1,"C");

      //Display table product rows
      $this->SetFont('Arial','',10);
      foreach($info['list'] as $row){
        $this->Cell(20,7,$row['fecha'],1,0,"C");
        $this->Cell(135,7,$row['referencia'],1,0,"C");
        $this->Cell(25,7,$row['factura'],1,0,"C");
        $this->Cell(20,7,"FV",1,0,"C");
        $this->Cell(20,7,$row['entrada'],1,0,"C");
        $this->Cell(20,7,$row['salida'],1,0,"C");
        $this->Cell(20,7,$row['saldo'],1,1,"C");
      }
      //Display table empty rows
    }
    function Footer(){
      $this->SetY(-25);

      $this->SetFont('Arial','',10);
      //Display Footer Text
      $this->Cell(0,10,"Esta es una factura generada por computadora.",0,1,"C");
      $this->SetY(-20);
      // Arial italic 8
      $this->SetFont('Arial','I',8);
      // Número de página
      $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');

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

  $pdf=new PDF("L","mm","letter");
  $pdf->AliasNbPages();
  $pdf->AddPage();
  $pdf->body($info);
  $pdf->Output();
}
?>
