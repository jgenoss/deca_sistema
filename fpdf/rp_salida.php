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

    $rtn[0] = Consult($db->sql("SELECT * FROM salida WHERE id_salida = ".$_GET['id']));
    $rtn[1] = Consult($db->sql("SELECT * FROM clientes WHERE id_cliente = ".$rtn[0]->id_cliente));
    $rtn[2] = AllConsult($db->sql("SELECT p.*,sd.* FROM salida_detalle AS sd INNER JOIN producto AS p ON sd.id_producto = p.id_producto WHERE sd.id_salida=".$rtn[0]->id_salida));

    $total=0;
    $cajas=0;
    foreach ($rtn[2] as $key) {
      $rtnS = (round($key->cantidad/$key->umb) < 1) ? 1 : round($key->cantidad/$key->umb);
      $total += $key->cantidad;
      $cajas += $rtnS;
      $products[] = array(
        'id' => $key->id_producto,
        'codigo' => $key->ean,
        'nombre' => $key->nombre,
        'cantidad' => $key->cantidad,
        'cajas' => $rtnS,
        'umb' => $key->umb
      );
    }
    function generadNumber($num)
    {
      if (intval($num) < 10) {
          return "0000000$num";
      }else if (intval($num) < 100) {
        return "000000$num";
      }else if (intval($num) < 1000) {
        return "00000$num";
      }else if (intval($num) < 10000) {
        return "0000$num";
      }else if (intval($num) < 100000) {
        return "000$num";
      }else {
        return "andale hagale un aumento de sueldo";
      }
    }
    $info = array(
      'total' => $total,
      'cajas' => $cajas,
      'cliente' => $rtn[1]->empresa,
      'referencia' => $rtn[0]->referencia,
      'factura' => $rtn[0]->factura,
      'fecha' => $rtn[0]->fecha_de_comprobante,
      'file' => $rtn[0]->archivo,
      'serie' => generadNumber($rtn[0]->id_salida),
      'observacion' => $rtn[0]->observacion,
      'direccion' => $rtn[0]->direccion,
      'tpago' => $rtn[0]->tpago,
      'listp' => $products
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
      $this->SetY(30);
      $this->SetFont('Arial','B',12);
      $this->Cell(0,3,"REPORTE DE SALIDA",0,1,'C');
    }

    function body($info){

      $this->SetY(10);
      $this->SetX(-55);
      // $this->SetFillColor(255);
      // $this->RoundedRect(150, 10, 50, 8, 3, 'DF');
      $this->SetFont('Arial','B',17);
      // NUMERO DE COMPROBANTE
      $this->Cell(0,10,"DC-".$info['serie'],1,1,"C");

      // NUMERO DE FACTURA
      $this->SetY(25);
      $this->SetX(-60);
      $this->SetFont('Arial','',10);

      $this->Ln();

      // CLIENTE
      $this->SetY(40);
      $this->SetX(10);
      $this->SetFillColor(255);
      $this->SetFont('Arial','',7);
      // $this->RoundedRect(10, 35, 135, 15, 2, 'DF');
      $this->Cell(20,7,"CLIENTE",1,0,"C");
      $this->Cell(110,7,str_limit(utf8_decode($info['cliente']),'60','...'),1,1);
      //$this->Ln();
      $this->Cell(25,7,"REFERENCIA:",1,0,"C");
      $this->Cell(105,7,str_limit(utf8_decode($info['referencia']),'50','...'),1,1);
      //$this->Ln();
      $this->Cell(25,7,"DIRECCION:",1,0,"C");
      $this->Cell(105,7,str_limit(utf8_decode($info['direccion']),'55','...'),1,1);
      //$this->Ln();

      $this->SetY(26);
      $this->SetX(-65);
      $this->Cell(55,7,"FECHA Y HORA DE FACTURA",1,1,'C');
      $this->SetX(-65);
      $this->Cell(22,7,"EXPEDICION",1,0,'C');
      $this->Cell(0,7,$info['fecha'],1,1,'C');
      $this->SetX(-65);
      $this->Cell(22,7,"GENERADO",1,0,'C');
      $this->Cell(0,7,date("Y-m-d"),1,1,'C');
      $this->SetX(-65);
      $this->Cell(22,7,"FACTURA",1,0,'C');
      $this->Cell(0,7,$info['factura'],1,1,'C');
      $this->SetX(-65);
      if ($info['tpago'] == "TRAMITE INTERNO"){
        $this->Cell(55,7,"TRAMITE INTERNO",1,0,'C');
        //$this->Cell(0,7,$info['tpago'],1,1,'C');
      }else {
        $this->Cell(25,7,"TIPO DE PAGO",1,0,'C');
        $this->Cell(0,7,$info['tpago'],1,1,'C');
      }
      $this->SetY(65);
      $this->SetFont('Arial','B',9);
      $this->Cell(0,3,"OBSERVACION",0,1,'C');
      $this->SetY(70);
      // $this->Cell(190,7,$info['observacion']
      $obs = explode("\n",utf8_decode(wordwrap($info['observacion'],130,"\n")));
      $this->SetFont('Arial','',7);
      foreach ($obs as $row) {
        $this->Cell(190,4,$row,0,1,"L");
      }
      $this->SetY(70);
      $suma = (count($obs) < 4 )? 0 : count($obs);
      // CELDA DE CUADRO
      $this->Cell(190,16+$suma,"",1);
      //Display Table headings
      $this->SetY(90+$suma);
      $this->SetX(10);
      $this->SetFont('Arial','B',12);
      $this->Cell(35,7,"Codigo",1,0,"C");
      $this->Cell(125,7,"Descripcion",1,0,"C");
      $this->Cell(15,7,"Umb",1,0,"C");
      $this->Cell(15,7,"Cant.",1,1,"C");


      //Display table product rows
      $this->SetFont('Arial','',10);
      foreach($info['listp'] as $row){
        $this->Cell(35,8,$row["codigo"],1,0,"C");
        $this->Cell(125,8,str_limit(utf8_decode($row["nombre"]),'55','...'),1,0,"L");
        $this->Cell(15,8,$row["umb"],1,0,"C");
        $this->Cell(15,8,$row["cantidad"],1,1,"C");
      }
      //Display table empty rows
      for($i=0;$i<15-count($info['listp']);$i++)
      {
        $this->Cell(35,8,"","LR",0);
        $this->Cell(125,8,"","LR",0,"C");
        $this->Cell(15,8,"","LR",0,"C");
        $this->Cell(15,8,"","LR",1,"C");
      }
      //Display table total row
      $this->SetFont('Arial','B',10);
      $this->Cell(175,7,"TOTAL UNIDADES",1,0,"R");
      $this->Cell(15,7,$info['total'],1,1,"C");
      $this->Cell(175,7,"TOTAL CAJAS",1,0,"R");
      $this->Cell(15,7,$info['cajas'],1,0,"C");

      $this->SetFont('Arial','',12);
      $this->Cell(0,7,"",0,1);

    }
    function Footer(){

      //set footer position
      $this->SetY(-20);
      // $this->SetFont('Arial','B',12);
      // $this->Cell(0,10,"for ABC COMPUTERS",0,1,"R");
      // $this->Ln(15);
      // $this->SetFont('Arial','',12);
      // $this->Cell(0,10,"Authorized Signature",0,1,"R");

      $this->SetFont('Arial','',10);
      //Display Footer Text
      $this->Cell(0,10,"Esta es una factura generada por computadora.",0,1,"C");
      $this->SetY(-15);
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
  $pdf=new PDF("P","mm","letter");
  $pdf->AliasNbPages();
  $pdf->AddPage();
  $pdf->SetAutoPageBreak(true, 20);
  $pdf->body($info);
  $pdf->Output("I","SALIDA(".$info['referencia'].").pdf");

}
?>
