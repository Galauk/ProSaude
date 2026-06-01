<?php
	session_start();
	require_once "fpdf/fpdf.php";
	define('FPDF_FONTPATH','fpdf/font/');
	$pdf = new FPDF("L","cm",array(29.9,12.9));
	$pdf->Open();
	$pdf->AddPage();
	$pdf->SetFont('Arial','',10);
	$pdf->SetMargins(0,0,0);
	
	

	$pdf->Image('".$_SESSION[linkroot].$_SESSION[comum]."imgs/topo.png',0,0,29.9,1.42,"png");
	
	//$pdf->Image('".$_SESSION[linkroot].$_SESSION[comum]."imgs/atencao.png',10,10,110,100,png);
	//$pdf->Multicell(5, 0.5, "LUCHOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO");
	$pdf->Output("arquivo","I");
	
	 
?>