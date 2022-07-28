<?php
  require ("fpdf.php");
  // require ("invoice.php");
  require ("../modelo/dbconnect.php");
  //customer and invoice details
  $info=[
    "customer"=>"Ram Kumar",
    "address"=>"4th cross,Car Street,",
    "city"=>"Salem 636204.",
    "invoice_no"=>"1000001",
    "invoice_date"=>"30-11-2021",
    "total_amt"=>"5200.00",
    "words"=>"Rupees Five Thousand Two Hundred Only",
  ];


  //invoice Products
  $products_info=[
    [
      "name"=>"Keyboard",
      "price"=>"500.00",
      "qty"=>2,
      "total"=>"1000.00"
    ],
    [
      "name"=>"Mouse",
      "price"=>"400.00",
      "qty"=>3,
      "total"=>"1200.00"
    ],
    [
      "name"=>"UPS",
      "price"=>"3000.00",
      "qty"=>1,
      "total"=>"3000.00"
    ],
  ];

  class PDF extends FPDF
  {
    function Header(){
      //Display Company Info
      // $this->Line(0,10,210,10);
      // LOGO
      $this->Image("../assets/img/logo.png",10,10,50,0,"PNG");

      //center
      $this->SetY(10);
      $this->SetFont('Courier','',8);
      // direccion
      $this->Cell(0,3,"DECA SOLUCIONES LOGISTICA",0,1,'C');
      $this->Cell(0,3,"Cra 29 No. 42-37,",0,1,'C');
      $this->Cell(0,3,"SECTOR SANTA CRUZ.",0,1,'C');
      $this->Cell(0,3,"BODEGA 1A de la MZ 3",0,1,'C');
      $this->Cell(0,3,"Santa Marta, Colombia",0,1,'C');

      //Factura
      $this->SetY(10);
      $this->SetFont('Courier','',12);
      // $this->Cell(0,3,"FACTURA ELECTRONICA",0,1,'R');
      $this->SetY(17);
      $this->SetX(-57);
      $this->SetFillColor(255);
      // $this->RoundedRect(x, y, w, h, r, style);
      $this->RoundedRect(150, 15, 50, 8, 3, 'DF');
      $this->SetFont('Courier','B',17);
      $this->Cell(0,3,"DC-00000001",0,1);
      //Display Horizontal line
      // $this->Line(0,48,210,48);
      $this->SetY(29);
      $this->SetFont('Arial','B',12);
      $this->Cell(0,3,"REPORTE DE SALIDA",0,1,'C');
    }

    function body($info,$products_info){
      // CLIENTE
      $this->SetY(38);
      $this->SetX(10);
      $this->SetFillColor(255);
      $this->SetFont('Courier','',9);
      $this->RoundedRect(10, 35, 135, 15, 2, 'DF');
      $this->Cell(20,3,"CLIENTE:",0,0,'L');
      $this->Cell(0,3,"ESTO ES SOLO UNA PRUEBA DE MI CONOCIMIENTO",0,0,'');
      $this->Line(10,43,145,43);
      // DIRECCION
      $this->SetY(45);
      $this->SetX(10);
      $this->SetFont('Courier','',9);
      $this->Cell(25,3,"DIRECCION:",0,0,'L');
      $this->Cell(0,3,"ESTO ES SOLO UNA PRUEBA DE MI CONOCIMIENTO",0,0,'');
      // FECHA
      $this->SetY(37);
      $this->SetX(168);
      $this->SetFillColor(255);
      $this->SetFont('Courier','',10);
      $this->RoundedRect(160, 35, 30, 15, 2, 'DF');
      $this->Cell(0,5,"FECHA:",0,0);
      $this->SetY(44);
      $this->SetX(163);
      $this->Cell(0,5,"2022-07-27",0,0);
      $this->Line(190,43,160,43);

      //Display Table headings
      $this->SetY(95);
      $this->SetX(10);
      $this->SetFont('Arial','B',12);
      $this->Cell(80,9,"DESCRIPTION",1,0);
      $this->Cell(40,9,"PRICE",1,0,"C");
      $this->Cell(30,9,"QTY",1,0,"C");
      $this->Cell(40,9,"TOTAL",1,1,"C");
      $this->SetFont('Arial','',12);

      //Display table product rows
      foreach($products_info as $row){
        $this->Cell(80,9,$row["name"],"LR",0);
        $this->Cell(40,9,$row["price"],"R",0,"R");
        $this->Cell(30,9,$row["qty"],"R",0,"C");
        $this->Cell(40,9,$row["total"],"R",1,"R");
      }
      //Display table empty rows
      for($i=0;$i<12-count($products_info);$i++)
      {
        $this->Cell(80,9,"","LR",0);
        $this->Cell(40,9,"","R",0,"R");
        $this->Cell(30,9,"","R",0,"C");
        $this->Cell(40,9,"","R",1,"R");
      }
      //Display table total row
      $this->SetFont('Arial','B',12);
      $this->Cell(150,9,"TOTAL",1,0,"R");
      $this->Cell(40,9,$info["total_amt"],1,1,"R");

      $this->SetFont('Arial','',12);
      $this->Cell(0,9,$info["words"],0,1);

    }
    function Footer(){

      //set footer position
      $this->SetY(-50);
      $this->SetFont('Arial','B',12);
      $this->Cell(0,10,"for ABC COMPUTERS",0,1,"R");
      $this->Ln(15);
      $this->SetFont('Arial','',12);
      $this->Cell(0,10,"Authorized Signature",0,1,"R");
      $this->SetFont('Arial','',10);

      //Display Footer Text
      $this->Cell(0,10,"This is a computer generated invoice",0,1,"C");

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
  $pdf=new PDF("P","mm","A4");
  $pdf->AddPage();
  $pdf->body($info,$products_info);
  $pdf->Output();
?>
