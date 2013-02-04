<?php
date_default_timezone_set("America/New_York");
set_include_path('includes/fpdf:includes/fpdi:includes/smarty');

require_once('fpdf.php');
require_once('fpdi.php');

// initiate FPDI
$pdf = new FPDI();
// add a page
$pdf->AddPage("P","Letter");
// set the sourcefile
$pdf->setSourceFile('forms/MeetingRequestForm.pdf');
// import page 1
$tplIdx = $pdf->importPage(1);
// use the imported page and place it at point 10,10 with a width of 100 mm
//$pdf->useTemplate($tplIdx, 10, 10, 100);
$pdf->useTemplate($tplIdx);

// now write some text above the imported page
$pdf->SetFont('Courier','B');
$pdf->SetTextColor(0,0,0);
$pdf->SetXY(35, 32);
$pdf->Write(0, "Ryan Collins");

$pdf->SetXY(150, 32);
$today=date("now");
$pdf->Write(0,"$today");



$pdf->Output('newpdf.pdf', 'I');
