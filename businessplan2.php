<?php

require('fpdf/fpdf.php');
require('database1.php');

class PDF extends FPDF
{
    var $legends;
	var $wLegend;
	var $sum;
    var $NbVal;
    
    function Header()
    {
        $this->Image('bp2.png',0,0,210,297);
    }
    function PieChart($w, $h, $data, $format, $colors=null)//(100,35)
	{
		$this->SetFont('Courier', '', 10);
        $this->SetLegends($data,$format);
        $XPage = $this->GetX();
        $YPage = $this->GetY();
        $margin = 2;
        $hLegend = 5;
        $radius = min($w - $margin * 4 - $hLegend - $this->wLegend, $h - $margin * 2);
        $radius = floor($radius / 2);
        $XDiag = $XPage + $margin + $radius;
        $YDiag = $YPage + $margin + $radius;
        if($colors == null) {
            for($i = 0; $i < $this->NbVal; $i++) {
                $gray = $i * intval(255 / $this->NbVal);
                $colors[$i] = array($gray,$gray,$gray);
            }
        }
        //Sectors
        $this->SetLineWidth(0);
        $angleStart = 0;
        $angleEnd = 0;
        $i = 0;
        foreach($data as $val) {
            $angle = ($val * 360) / doubleval($this->sum);
            if ($angle != 0) {
                $angleEnd = $angleStart + $angle;
                $this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
                $this->Sector($XDiag-70, $YDiag+15, $radius-20, $angleStart, $angleEnd);
                $angleStart += $angle;
            }
            $i++;
        }
        //Legends
        $this->SetFont('Courier', '', 10);
        $x1 = $XPage + 2 * $radius + 4 * $margin;
        $x2 = $x1 + $hLegend + $margin;
        $y1 = $YDiag - $radius + (2 * $radius - $this->NbVal*($hLegend + $margin)) / 2;
        for($i=0; $i<$this->NbVal; $i++) {
            $this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
            $this->Rect($x1-90, $y1+35, $hLegend, $hLegend, 'F');
            $this->SetXY($x2-90,$y1+35);
            $this->Cell(0,$hLegend,$this->legends[$i]);
            $y1+=$hLegend + $margin;
        }
    }
    function PieChart1($w, $h, $data, $format, $colors=null)
	{
		$this->SetFont('Courier', '', 10);
        $this->SetLegends($data,$format);
        $XPage = $this->GetX();
        $YPage = $this->GetY();
        $margin = 2;
        $hLegend = 5;
        $radius = min($w - $margin * 4 - $hLegend - $this->wLegend, $h - $margin * 2);
        $radius = floor($radius / 2);
        $XDiag = $XPage + $margin + $radius;
        $YDiag = $YPage + $margin + $radius;
        if($colors == null) {
            for($i = 0; $i < $this->NbVal; $i++) {
                $gray = $i * intval(255 / $this->NbVal);
                $colors[$i] = array($gray,$gray,$gray);
            }
        }
        //Sectors
        $this->SetLineWidth(0);
        $angleStart = 0;
        $angleEnd = 0;
        $i = 0;
        foreach($data as $val) {
            $angle = ($val * 360) / doubleval($this->sum);
            if ($angle != 0) {
                $angleEnd = $angleStart + $angle;
                $this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
                $this->Sector($XDiag-70, $YDiag+15, $radius-20, $angleStart, $angleEnd);
                $angleStart += $angle;
            }
            $i++;
        }
        //Legends
        $this->SetFont('Courier', '', 10);
        $x1 = $XPage + 2 * $radius + 4 * $margin;
        $x2 = $x1 + $hLegend + $margin;
        $y1 = $YDiag - $radius + (2 * $radius - $this->NbVal*($hLegend + $margin)) / 2;
        for($i=0; $i<$this->NbVal; $i++) {
            $this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
            $this->Rect($x1-145, $y1+55, $hLegend, $hLegend, 'F');
            $this->SetXY($x2-145,$y1+55);
            $this->Cell(0,$hLegend,$this->legends[$i]);
            $y1+=$hLegend + $margin;
        }
    }
    function SetLegends($data, $format)
	{
		$this->legends=array();
		$this->wLegend=0;
        $this->sum=array_sum($data);
		$this->NbVal=count($data); 
		foreach($data as $l=>$val)
		{
            $p=sprintf('%.2f',$val/$this->sum*100).'%';
			$legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
			$this->legends[]=$legend;
            $this->wLegend=max($this->GetStringWidth($legend),$this->wLegend);
        }
    }
    function Sector($xc, $yc, $r, $a, $b, $style='FD', $cw=true, $o=90)
	{
		$d0 = $a - $b;
    if($cw){
        $d = $b;
        $b = $o - $a;
        $a = $o - $d;
    }else{
        $b += $o;
        $a += $o;
    }
    while($a<0)
        $a += 360;
    while($a>360)
        $a -= 360;
    while($b<0)
        $b += 360;
    while($b>360)
        $b -= 360;
    if ($a > $b)
        $b += 360;
    $b = $b/360*2*M_PI;
    $a = $a/360*2*M_PI;
    $d = $b - $a;
    if ($d == 0 && $d0 != 0)
        $d = 2*M_PI;
    $k = $this->k;
    $hp = $this->h;
    if (sin($d/2))
        $MyArc = 4/3*(1-cos($d/2))/sin($d/2)*$r;
    else
        $MyArc = 0;
    //first put the center
    $this->_out(sprintf('%.2F %.2F m',($xc)*$k,($hp-$yc)*$k));
    //put the first point
    $this->_out(sprintf('%.2F %.2F l',($xc+$r*cos($a))*$k,(($hp-($yc-$r*sin($a)))*$k)));
    //draw the arc
    if ($d < M_PI/2){
        $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                    $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                    $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                    $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                    $xc+$r*cos($b),
                    $yc-$r*sin($b)
                    );
    }else{
        $b = $a + $d/4;
        $MyArc = 4/3*(1-cos($d/8))/sin($d/8)*$r;
        $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                    $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                    $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                    $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                    $xc+$r*cos($b),
                    $yc-$r*sin($b)
                    );
        $a = $b;
        $b = $a + $d/4;
        $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                    $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                    $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                    $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                    $xc+$r*cos($b),
                    $yc-$r*sin($b)
                    );
        $a = $b;
        $b = $a + $d/4;
        $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                    $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                    $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                    $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                    $xc+$r*cos($b),
                    $yc-$r*sin($b)
                    );
        $a = $b;
        $b = $a + $d/4;
        $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                    $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                    $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                    $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                    $xc+$r*cos($b),
                    $yc-$r*sin($b)
                    );
    }
    //terminate drawing
    if($style=='F')
        $op='f';
    elseif($style=='FD' || $style=='DF')
        $op='f';
    else
        $op='f';
    $this->_out($op);
	}

	function _Arc($x1, $y1, $x2, $y2, $x3, $y3 )
	{
		$h = $this->h;
        $this->_out(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c',
            $x1 * $this->k,
            ($h - $y1) * $this->k,
            $x2 * $this->k,
            ($h - $y2) * $this->k,
            $x3 * $this->k,
            ($h - $y3) * $this->k
        ));
    }
    protected $extgstates = array();

    function SetAlpha($alpha, $bm='Normal')
    {
        $gs = $this->AddExtGState(array('ca'=>$alpha, 'CA'=>$alpha, 'BM'=>'/'.$bm));
        $this->SetExtGState($gs);
    }

    function AddExtGState($parms)
    {
        $n = count($this->extgstates)+1;
        $this->extgstates[$n]['parms'] = $parms;
        return $n;
    }

    function SetExtGState($gs)
    {
        $this->_out(sprintf('/GS%d gs', $gs));
    }

    function _enddoc()
    {
        if(!empty($this->extgstates) && $this->PDFVersion<'1.4')
            $this->PDFVersion='1.4';
        parent::_enddoc();
    }

    function _putextgstates()
    {
        for ($i = 1; $i <= count($this->extgstates); $i++)
        {
            $this->_newobj();
            $this->extgstates[$i]['n'] = $this->n;
            $this->_put('<</Type /ExtGState');
            $parms = $this->extgstates[$i]['parms'];
            $this->_put(sprintf('/ca %.3F', $parms['ca']));
            $this->_put(sprintf('/CA %.3F', $parms['CA']));
            $this->_put('/BM '.$parms['BM']);
            $this->_put('>>');
            $this->_put('endobj');
        }
    }

    function _putresourcedict()
    {
        parent::_putresourcedict();
        $this->_put('/ExtGState <<');
        foreach($this->extgstates as $k=>$extgstate)
            $this->_put('/GS'.$k.' '.$extgstate['n'].' 0 R');
        $this->_put('>>');
    }

    function _putresources()
    {
        $this->_putextgstates();
        parent::_putresources();
    }
    var $angle=0;

function Rotate($angle,$x=-1,$y=-1)
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

function _endpage()
{
    if($this->angle!=0)
    {
        $this->angle=0;
        $this->_out('Q');
    }
    parent::_endpage();
}
function RotatedImage($file,$x,$y,$w,$h,$angle)
{
    //Image rotated around its upper-left corner
    $this->Rotate($angle,$x,$y);
    $this->Image($file,$x,$y,$w,$h);
    $this->Rotate(0);
}
}

$pdf= new PDF();
$sql="SELECT * FROM bplan WHERE bemail='demo@gmail.com'";
$result=mysqli_query($conn,$sql);
$row=mysqli_fetch_array($result);
$pdf->AddPage();
$pdf->SetXY(70,0);
$pdf->SetFont('Times','BI',30);
$pdf->Cell(30,20,'BUSINESS PLAN',0,0);
$pdf->Image('bekreta.png',5,20,60,20);
$pdf->SetXY(136,14);
$pdf->SetFont('Times','BI',14);
$pdf->Cell(30,20,$row['cName'],0,0);
$pdf->SetXY(177,14);
$pdf->SetFont('Times','I',14);
$pdf->Cell(30,20,'( '.$row['cBusiness'].' )',0,0);
$pdf->SetXY(136,20);
$pdf->SetFont('Times','I',14);
$pdf->Cell(30,20,$row['cLocation'],0,0);
$pdf->SetXY(136,27);
$pdf->SetFont('Times','I',14);
$pdf->Cell(30,20,$row['cNumber'],0,0);
$pdf->SetXY(136,32);
$pdf->SetFont('Times','I',14);
$pdf->Cell(30,20,$row['cEmailid'],0,0);
$pdf->SetXY(10,50);
$pdf->SetFont('Times','BI',19);
$pdf->Cell(30,20,'PROBLEMS:',0,0);
$prob=json_decode($row['prob']); $a=1;  $y=65;
for($i=0;$i<count($prob);$i++)
{
    $pdf->SetXY(10,$y);
    $pdf->SetFont('Times','I',13);
    $pdf->MultiCell(100,5,'Problem '.$a.' :'.$prob[$i],0,1);
    $a++;  $y+=15;
}
$pdf->SetXY(150,50);
$pdf->SetFont('Times','BI',19);
$pdf->Cell(30,20,'SOLUTIONS:',0,0);
$sol=json_decode($row['sol']); $b=1;  $y=65;
for($i=0;$i<count($sol);$i++)
{
    $pdf->SetXY(109,$y);
    $pdf->SetFont('Times','I',13);
    $pdf->MultiCell(100,5,'Soluation '.$b.' :'.$sol[$i],0,1);
    $b++;  $y+=17;
}
$pdf->SetXY(70,105);
$pdf->SetFont('Times','BI',20);
$pdf->Cell(30,10,'SALES SUMMARY',0,0);
$pdf->SetXY(10,115);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(30,10,'Online Sales (in %)',0,0);
$sql1="SELECT * FROM sales WHERE bemail='demo@gmail.com'";
$result1=mysqli_query($conn,$sql1);
$row1=mysqli_fetch_array($result1);
$salesoln=json_decode($row1['salesoln']);
$salesol=json_decode($row1['salesol']);
$data1=array();
for($i=0;$i<count($salesoln);$i++)
{
    $data1[$salesoln[$i]] = $salesol[$i];    
}
$valX = round($pdf->GetX());
$valY = round($pdf->GetY());
$pdf->SetXY(55,90);
$col[0]=array(100,100,255);
$col[1]=array(255,100,100);
$col[2]=array(255,255,100);
$col[3]=array(63, 81, 181);
$col[4]=array(205, 220, 57);
$col[5]=array(139, 195, 74);
$col[6]=array(255, 152, 0);
$col[7]=array(53, 59, 72);
$col[8]=array(111, 30, 81);
$col[9]=array(0, 98, 102);
$col[10]=array(255,195,18);
$col[11]=array(87,88,187);
$pdf->PieChart(150,90, $data1, '%l (%p)', $col);
$pdf->SetXY($valX, $valY + 10);
$pdf->SetXY(155,115);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(30,10,'Offline Sales (in %)',0,0);
$salesofn=json_decode($row1['salesofn']);
$salesof=json_decode($row1['salesof']);
$data1=array();
for($i=0;$i<count($salesofn);$i++)
{
    $data11[$salesofn[$i]] = $salesof[$i];    
}
$valX = round($pdf->GetX());
$valY = round($pdf->GetY());
$pdf->SetXY(200,90);
$col[0]=array(100,100,255);
$col[1]=array(255,100,100);
$col[2]=array(255,255,100);
$col[3]=array(63, 81, 181);
$col[4]=array(205, 220, 57);
$col[5]=array(139, 195, 74);
$col[6]=array(255, 152, 0);
$col[7]=array(53, 59, 72);
$col[8]=array(111, 30, 81);
$col[9]=array(0, 98, 102);
$col[10]=array(255,195,18);
$col[11]=array(87,88,187);
$pdf->PieChart1(150,90, $data11, '%l (%p)', $col);
$pdf->SetXY($valX, $valY + 10);
$pdf->SetXY(70,200);
$pdf->SetFont('Times','BI',20);
$pdf->Cell(30,20,'TARGET MARKETS',0,0);
$tar=json_decode($row['tar']);   $c=1; $x=10;
for($i=0;$i<count($tar);$i++)
{
    $pdf->SetXY($x,215);
    $pdf->SetFont('Times','I',15);
    $pdf->Cell(30,10,$c." . ".$tar[$i],0,0);
    $c++;  $x+=35;
}
$pdf->SetXY(70,225);
$pdf->SetFont('Times','BI',20);
$pdf->Cell(30,20,'COMPETITORS',0,0);
$comp=json_decode($row['swotcname']);  $d=1;  $x=10;     
for($i=0;$i<count($comp);$i++)  
{
    $pdf->SetXY($x,240);
    $pdf->SetFont('Times','I',15);
    $pdf->Cell(30,10,$d." . ".$comp[$i],0,0);
    $d++;  $x+=35;
}
$pdf->SetXY(8,253);
$pdf->SetFont('Times','',20);
$pdf->SetFillColor(255,127,80);
$pdf->Cell(190,10,'',0,0,'',true);
$pdf->SetXY(8,254);
$pdf->SetFont('Times','',13);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(30,10,'FINANCIALS (000 Rs.)',0,0);
$pdf->SetXY(78,254);
$pdf->SetFont('Times','',13);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(30,10,'Year 1',0,0);
$pdf->SetXY(122,254);
$pdf->SetFont('Times','',13);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(30,10,'Year 2',0,0);
$pdf->SetXY(172,254);
$pdf->SetFont('Times','',13);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(30,10,'Year 3',0,0);
$pdf->SetXY(12,265);
$pdf->SetFont('Times','',13);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(30,2,'REVENUES',0,0);
$pdf->SetXY(12,272);
$pdf->SetFont('Times','',13);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(30,2,'EXPENSES',0,0);
$pdf->SetXY(78,265);
$pdf->SetFont('Times','',13);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(30,3,$row['revenue1'],0,0);
$pdf->SetXY(122,265);
$pdf->SetFont('Times','',13);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(30,3,$row['revenue2'],0,0);
$pdf->SetXY(172,265);
$pdf->SetFont('Times','',13);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(30,3,$row['revenue3'],0,0);
$pdf->SetXY(78,272);
$pdf->SetFont('Times','',13);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(30,3,$row['expense1'],0,0);
$pdf->SetXY(122,272);
$pdf->SetFont('Times','',13);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(30,3,$row['expense2'],0,0);
$pdf->SetXY(172,272);
$pdf->SetFont('Times','',13);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(30,3,$row['expense3'],0,0);
$pdf->SetLineWidth(1.5);
$pdf->SetAlpha(0.2);
$pdf->RotatedImage('bekreta.png',60,160,100,50,55);
$pdf->SetAlpha(1);
$pdf->Output('I','Business Plan');
?>
