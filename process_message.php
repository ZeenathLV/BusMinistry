<?php
try
{
  $twillio_enabled = true;
  $rider = new BusRider($twillio_enabled);
  
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
