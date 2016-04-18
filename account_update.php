<?php
include_once("constants.php");

// Grab the phone from the text
$update_pwd = $_GET['access'];
$show_message = 'hidden';
$show_form = 'not_hidden';
$confirmation_message = "";
$text_ride_msg = "Text -RIDE- at " 
    . format_phone(substr(TWILIO_NUMBER,2,10))
    . " to make a reservation.";

// Check to see if the form has been submitted
if ($_POST) 
{
  try
  {
    $rider = new BusRider();
   
    $val = hack_check($_POST['rider_id']);
    $rider->set_rider_id($val);

    $val = hack_check($_POST['phone_number']);
    $rider->set_phone_number($val);
    $rider->set_sms_number($val);
    
    $val = hack_check($_POST['name_first']);
    $rider->set_name_first($val);
    
    $val = hack_check($_POST['name_last']);
    $rider->set_name_last($val);
    
    $val = hack_check($_POST['address1']);
    $rider->set_address1($val);

    $val = hack_check($_POST['address2']);
    $rider->set_address2($val);

    $val = hack_check($_POST['city']);
    $rider->set_city($val);

    $val = hack_check($_POST['zip_code']);
    $rider->set_zip_code($val);

    $val = hack_check($_POST['special_handling']);
    $rider->set_special_handling($val);

    $rider->set_update_pwd("");
    $rider->set_update_pwd_exp("");
    
    $rider->save();
    if($rider->has_error())
      throw new Exception("Save Error: " . $rider->get_error());
    
    $confirmation_message = "Your account has been updated.<br>" 
         . $text_ride_msg;

    $rider->log_message(SENDER,"Registration updated by user.");
    $rider->send_sms_message(RECEIVER,$confirmation_message);   
    
    $show_form = 'hidden';
    $show_message = 'not_hidden';
  }
  catch(Exception $e)
  {
    $confirmation_message = "Error: " . $e->getMessage();
    $show_form = 'hidden';
    $show_message = 'not_hidden';
  }
}
else
{
  $time = new Timestamp();
  $rider = new BusRider();
  
  $now = $time->get_datetime();
  $rider->load_update_pwd($update_pwd);
  
  if($rider->get_rider_id()==NEW_ID)
  {
    $confirmation_message = "Your update link is invalid.<br>"
         . $text_ride_msg;
         
    $show_form = 'hidden';
    $show_message = 'not_hidden';
  }
  else if($now > $rider->get_update_pwd_exp())
  {
    $confirmation_message = "Your update link has expired.<br>"
         . $text_ride_msg;
    
    $show_form = 'hidden';
    $show_message = 'not_hidden';
  }
  else
  {
    $show_form = 'not_hidden';
    $show_message = 'hidden';
    $formatted_phone = format_phone($rider->get_phone_number());
  }
}

?>


<!DOCTYPE html>

<html lang="en">
<head>

  <title>Rock Church Bus Ministry Registration</title>

  <!-- Meta Tags -->
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- CSS -->
  <style type="text/css">
  body {
		font-family:arial,helvetica,sans-serif;
		font-size:12px;
    color: black;
    <!-- color: white; -->
	}

    .hidden {
      display: none;
    }
    #container
    {
      width: 225px;
      height: 550px;
      <!-- background-image: url("images/bkgrnd.png"); -->
      padding-left: 20px;
   }
   #ministry-title
   {
     margin-left: 5px;
     margin-bottom: 0px;
     font-weight: bold;
     font-size:14px;
   }
   #logo
   {
     margin-bottom: 0px;
   }
  </style>

  <script type="text/javascript">
  function validate_form()
  {
    var result = "";
    
//    alert("NateWasHere!!!");
    
    var val = document.getElementById("name_first").value;
    if(val.trim().length==0)
      result = result + "First Name cannot be blank.\n";        
    
    val = document.getElementById("name_last").value;
    if(val.trim().length==0)
      result = result + "Last Name cannot be blank.\n";      

    val = document.getElementById("address1").value;
    if(val.trim().length==0)
      result = result + "Address cannot be blank.\n";      

    val = document.getElementById("city").value;
    if(val.trim().length==0)
      result = result + "City cannot be blank.\n";      

    val = document.getElementById("zip_code").value;
    if(val.trim().length==0)
      result = result + "Zip Code cannot be blank.\n";      

    if(val.trim().length<5)
      result = result + "Zip Code is invalid.\n";      
    
    if(result.trim().length==0)
    {  
      return true
    }
    else
    {
      alert("The following errors were noted:\n" +
        result);
      return false;
    }
  }  
  </script>
  
  
  <!-- JavaScript -->


</head>
<body>
  <div id="container">
    <!--<img src="logo.png" alt="Rock Church " style="width:282px;height:130px;">-->
    <img id="logo" src="images/logo.png" alt="Rock Church " style="width:185px;">
    <div id="ministry-title">Bus Ministry Registration</div>
    <h4 class="<?=$show_message;?>"><?=$confirmation_message;?></h4>

    <form name="registration" onsubmit="return validate_form()" method="POST" action="<?=$_SERVER["PHP_SELF"];?>" class="<?=$show_form;?>">

      <input type="hidden" name="rider_id" value="<?=$rider->get_rider_id();?>" />
      <input type="hidden" name="phone_number" value="<?=$rider->get_phone_number();?>" />
      <input type="hidden" name="update_pwd" value="<?=$update_pwd;?>" />
    
      <label for="formatted_phone">Phone Number:</label><br>
      <input type="text" name="formatted_phone" size="15" value="<?=$formatted_phone;?>" readonly /><br>

      <label for="name_first">First Name:</label><br>
      <input type="text" size="25" name="name_first" id="name_first" value="<?=$rider->get_name_first();?>" /><br>

      <label for="name_last">Last Name:</label><br>
      <input type="text" size="25" name="name_last" id="name_last" value="<?=$rider->get_name_last();?>" /><br>

      <label for="address1">Street Address:</label><br>
      <input type="text" size="25" name="address1" id="address1" value="<?=$rider->get_address1();?>" /><br>
      <input type="text" size="25" name="address2" id="address2" value="<?=$rider->get_address2();?>" /><br>

      <label for="city">City:</label><br>
      <input type="text" size="25" name="city" id="city" value="<?=$rider->get_city();?>" /><br>

      <label for="zip_code">Zip Code:</label><br>
      <input type="text" size="25" name="zip_code" id="zip_code" value="<?=$rider->get_zip_code();?>" /><br>

      <label for="special_handling">Special Handling<br>(Gate code,special directions, etc.):</label><br>
      <input type="text" size="25" name="special_handling" id="special_handling"  value="<?=$rider->get_special_handling();?>" /><br><br>

      <input type="submit" name="btn-submit" value="Save"/>
    </form>
  </div>

</body>
</html>
