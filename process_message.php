<?php
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
