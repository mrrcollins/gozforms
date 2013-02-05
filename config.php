<?php

date_default_timezone_set("America/New_York");
set_include_path('includes/fpdf:includes/fpdi:includes/smarty');

require_once('fpdf.php');
require_once('fpdi.php');
include('Smarty.class.php');

$smarty = new Smarty;

$smarty->setTemplateDir('includes/smarty/templates');
$smarty->setCompileDir('includes/smarty/templates_c');
$smarty->setCacheDir('includes/smarty/cache');
$smarty->setConfigDir('includes/smarty/configs');

function calculate_string( $mathString )    {
    //http://www.website55.com/php-mysql/2010/04/how-to-calculate-strings-with-php.html
    $mathString = trim($mathString);     // trim white spaces
    $mathString = ereg_replace ('[^0-9\+-\*\/\(\) ]', '', $mathString);    // remove any non-numbers chars; exception for math operators
 
    $compute = create_function("", "return (" . $mathString . ");" );
    return 0 + $compute();
}

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

function fillForm($form,$data) {
    $schema = importFormSchema($form);
//print_r($data);
    // initiate FPDI
    $pdf = new FPDI();
    // add a page
    $pdf->AddPage("P","Letter");
    $pdf->SetFont('Courier','B');
    $pdf->SetTextColor(0,0,0);

    $pdf->setSourceFile("forms/$form.pdf");
    $tplIdx = $pdf->importPage(1);
    $pdf->useTemplate($tplIdx);

    foreach($schema as $item) { //print_r($item);
        $pdf->SetXY($item["x"], $item["y"]);

        switch($item['type']) {
            case 'currency':
                //print_r($item); 
                if(!is_numeric($data[$item["id"]])) { $data[$item["id"]] = 0; }
                $format = money_format('$%i',$data[$item["id"]]);
                $pdf->Cell(30,0, $format,0,0,"R");
                break;
            case 'date':
                $pdf->Cell(0,0, date("n/j/y"));
                break;
            case 'calculation':
                $calculation = calculate($item['id'],$item['description']);
                $pdf->Cell(0,0,$calculation);
                break;
            case 'currencycalculation':
                $calculation =  money_format('$%i', calculate($item['id'],$item['description']));
                $pdf->Cell(30,0,$calculation,0,0,"R");
                break;
 
            default:
                $pdf->Cell(0,0, $data[$item["id"]]);
        }
    }
    $filename="/tmp/".$data['name']."-$form.pdf";
    $pdf->Output($filename,"F");
    $parameters ="-f '".$data['name']." <".$data['emailfrom'].">' -t ".$data['emailto']."  -cc ".$data['emailfrom'];
    $parameters.=" -u '$form' -m '$form attached' -a '$filename'";
//echo "<p>$parameters";
    exec("includes/sendEmail $parameters",$emailmsg);
    foreach($emailmsg as $line) {
        $output = "$line\n";
    }
    
    $pdf = new FPDI();
    // add a page
    $pdf->AddPage("P","Letter");
    $pdf->SetFont('Courier','B');
    $pdf->SetTextColor(0,0,0);

    $pdf->setSourceFile("$filename");
    $tplIdx = $pdf->importPage(1);
    $pdf->useTemplate($tplIdx);

    $pdf->SetXY(10,10);
    $pdf->SetTextColor(255,0,0);
    $pdf->SetFontSize(10);
    $stamp = "Email sent to ".$data['emailto']." on ".date("n/j/y"); 
//echo $stamp;
    $pdf->MultiCell(0,5,$stamp,1,"C");
    $pdf->Output('newpdf.pdf', 'I');
}

function importFormSchema($form) {
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
            "id"    => trim($id),
            "data"  => trim($id)
        );

        $items[]=$item;
    }

    return $items;
}

function calculate($id,$formula) {
    global $data;

    $formulaitems = preg_split('/[\+\-\*]/',$formula);

    foreach($formulaitems as $item) {
        if(!is_numeric($item)) {
            if(!is_numeric($data[$item])) { $data[$item] = 0; } 
            $formula = preg_replace("/$item/",$data[$item],$formula);
        }
    }

$answer = calculate_string($formula);
$data[$id] = $answer;
return $answer;

}
?>
