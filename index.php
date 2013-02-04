<?php

include "config.php";

//print_r(getForms());
$data = importForm("MeetingRequestForm");
//print_r($data);
createForm("MeetingRequestForm",$data);

?>
