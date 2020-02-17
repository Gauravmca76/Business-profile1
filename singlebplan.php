<?php
require('database1.php');
require('fpdf/fpdf.php');
class PDF extends FPDF
{
    var $legends;
	var $wLegend;
	var $sum;
	var $NbVal;
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

    function PieChart1($w, $h, $data, $format, $colors=null)//(100,35)
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
            $this->Rect($x1-145, $y1+56, $hLegend, $hLegend, 'F');
            $this->SetXY($x2-145,$y1+56);
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
    function SetDash($black=null, $white=null)
    {
        if($black!==null)
            $s=sprintf('[%.3F %.3F] 0 d',$black*$this->k,$white*$this->k);
        else
            $s='[] 0 d';
        $this->_out($s);
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
}

$pdf = new PDF();
$pdf->AddPage();
$sql="SELECT * FROM bplan WHERE bemail='demo@gmail.com'";
$result=mysqli_query($conn,$sql);
$row=mysqli_fetch_array($result);
$pdf->SetXY(80,5);
$pdf->SetFont('Times','BI',25);
$pdf->Cell(130,20,'Business Plan',0,0);
$pdf->SetXY(20,18);
$pdf->SetFont('Times','I',18);
$pdf->Cell(130,20,$row['cName'],0,0);
$pdf->SetXY(20,30);
$pdf->MultiCell(130,10,$row['cVisionmission']);
$pdf->SetLineWidth(0.1);
$pdf->SetDash(2,2); //5mm on, 5mm off
$pdf->Line(10,43,200,43);
$pdf->SetXY(18,43);
$pdf->SetFont('Times','BI',18);
$pdf->Cell(130,20,'The Market Problem',0,0);
$prob=json_decode($row['prob']); $x=15;
$pdf->SetFont('Times','I',15);
for($i=0;$i<count($prob);$i++)
{
$pdf->SetXY($x,60);
$pdf->MultiCell(20,5,$prob[$i],0,0);
$x+=30;
}
$pdf->SetXY(120,43);
$pdf->SetFont('Times','BI',18);
$pdf->Cell(130,20,'How we will solve it',0,0);
$sol=json_decode($row['sol']); $x=113;
$pdf->SetFont('Times','I',15);
for($i=0;$i<count($sol);$i++)
{
    $pdf->SetXY($x,60);
    $pdf->MultiCell(20,5,$sol[$i],0,0);
    $x+=30;
}
$pdf->SetLineWidth(0.1);
$pdf->SetDash(2,2); //5mm on, 5mm off
$pdf->Line(10,73,200,73);
$pdf->SetXY(20,73);
$pdf->SetFont('Times','BI',18);
$pdf->Cell(30,20,'Target Market',0,0);
$pdf->SetFont('Times','I',15);
$tar=json_decode($row['tar']); $x=15;
for($i=0;$i<count($tar);$i++)
{
    $pdf->SetXY($x,90);
    $pdf->MultiCell(20,5,$tar[$i],0,0);
    $x+=15;
}
$pdf->SetXY(77,73);
$pdf->SetFont('Times','BI',18);
$pdf->Cell(30,20,'Competitors',0,0);
$pdf->SetFont('Times','I',15);
$scname=json_decode($row['swotcname']);  $y=87;
for($i=0;$i<count($scname);$i++)
{
    $pdf->SetXY(77,$y);
    $pdf->MultiCell(15,10,$scname[$i],0,0);
    $y+=8;
}
$pdf->SetXY(147,73);
$pdf->SetFont('Times','BI',18);
$pdf->Cell(30,20,'Core Team',0,0);
$emp=json_decode($row['empname']);
$pdf->SetXY(143,90);
$pdf->SetFont('Times','I',15);
$pdf->MultiCell(60,5,'Number of Employees in company:');
$pdf->SetXY(165,88);
$pdf->SetFont('Times','I',15);
$pdf->cell(20,20,count($emp),0,0);
$pdf->SetLineWidth(0.1);
$pdf->SetDash(2,2); //5mm on, 5mm off
$pdf->Line(10,123,200,123);
$pdf->SetXY(80,124);
$pdf->SetFont('Times','BI',18);
$pdf->Cell(30,20,'Funding',0,0);
$pdf->SetXY(25,140);
$pdf->SetLineWidth(0.1);
$pdf->SetDash(10,5); //5mm on, 5mm off
$pdf->SetFont('Times','I',15);
$pdf->Cell(150,20,'     Funding Needed Rs.',1,0);
$pdf->SetLineWidth(0.1);
$pdf->SetDash(2,2); //5mm on, 5mm off
$pdf->Line(10,170,200,170);
$pdf->SetXY(64,167);
$pdf->SetFont('Times','BI',18);
$pdf->Cell(30,20,'Financial Projections',0,0);
$pdf->SetXY(14,177);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(30,20,'Sales Online (in %)',0,0);
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
$pdf->SetXY(50,160);
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
$pdf->PieChart(160,160, $data1, '%l (%p)', $col);
$pdf->SetXY($valX, $valY + 10);
$pdf->SetXY(144,177);
$pdf->SetFont('Times','BI',15);
$pdf->Cell(30,20,'Sales Offline (in %)',0,0);
$salesofn=json_decode($row1['salesofn']);
$salesof=json_decode($row1['salesof']);
$data=array();
for($i=0;$i<count($salesofn);$i++)
{
    $data[$salesofn[$i]] = $salesof[$i];    
}
$valX = round($pdf->GetX());
$valY = round($pdf->GetY());
$pdf->SetXY(190,160);
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
$pdf->PieChart1(160,160, $data, '%l (%p)', $col);
$pdf->SetXY($valX, $valY + 10);
$pdf->Output();
?>