<?php


set_include_path('includes/fpdf:includes/fpdi:includes/smarty');

require_once('fpdf.php');
require_once('fpdi.php');

function getForms() {
    $files = array();
    if ($dir = @opendir("forms/")) {
        while($file = readdir($dir)) { echo $file;
                if (!preg_match('/^\.+$/', $file) and 
                preg_match('/\.txt$/', $file)){
 
                // feed the array:
                $files[] = $file;                
                }
            }
        }
    return $files;
}

function createForm($form,$data) {
    // initiate FPDI
    $pdf = new FPDI();
    // add a page
    $pdf->AddPage("P","Letter");
    $pdf->SetFont('Courier','B');
    $pdf->SetTextColor(0,0,0);

    $pdf->setSourceFile("forms/$form.pdf");
    $tplIdx = $pdf->importPage(1);
    $pdf->useTemplate($tplIdx);

    foreach($data as $item) { //print_r($item);
        $pdf->SetXY($item["x"], $item["y"]);
        $pdf->Cell(0,0, $item["data"]);
    }

    $pdf->Output('newpdf.pdf', 'I');
}

function importForm($form) {
    $items = array();
    $in = fopen("forms/$form.txt", "r");

    $title = fgets($in);
    $header = fgets($in);

    while(($line = fgets($in)) !== false) { 
        list($type,$x,$y,$description,$id) = explode(",",$line);
        $item = array (
            "type"  => $type,
            "x"     => $x,
            "y"     => $y,
            "description"   => $description,
            "id"    => $id,
            "data"  => "$id - not entered"
        );

        $items[]=$item;
    }

    return $items;
}
?>
