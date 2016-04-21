<?php
include_once("constants.php");
$phone = "NOT_SET";
if ($_REQUEST)
{
  $phone = $_REQUEST['From'];
  $message = $_REQUEST['Body'];
}

try
{
  $rider = new BusRider(true);
  
  $rider->load_rider($phone, $message);
  
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
