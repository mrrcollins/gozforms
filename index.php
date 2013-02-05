<?php

include "config.php";

$form="MeetingRequestForm";
//print_r($_POST);
$action=$_POST["action"];

switch($action) {
    case "Send form":
        $data=$_POST["return"];
        //print_r($data);
        fillForm($form,$data);
        break;
    default:
        $schema = importFormSchema($form);
        $smarty->assign('formname',$form);
        $smarty->assign('schema',$schema);
        $smarty->display('inputform.html');
}



//fillForm("MeetingRequestForm",$data);

?>
