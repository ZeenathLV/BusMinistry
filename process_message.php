<?php
include_once("constants.php");
$phone = "NOT_SET";
if ($_REQUEST)
{
  $phone = $_REQUEST['From'];
  $message = $_REQUEST['Body'];

}

$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

try
{
  $rider = new BusRider(true);

  $rider->load_rider($phone, $message);

  $rider->log_debug("Actual Link: " . $actual_link);
  
  if($rider->has_error())
     throw new Exception($rider->get_error());
  
  $rider->process_message();

  if($rider->has_error())
     throw new Exception($rider->get_error());
  
}
catch(Exception $e)
{
  echo $e->getMessage();
}

?>             
