<?php
require('diag11.php');
$pdf= new PDF_Diag();

$pdf->AddPage();

$pdf->Output();
?>