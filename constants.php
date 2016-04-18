<?php
// Root path for all web pages
define("ROOT_PATH", "http://www.rockchurch-media.com/nate/");
define("ROOT_DIR", $_SERVER['DOCUMENT_ROOT'] . "/rock_church/nate/");

// Twillio Constants
define("TWILIO_SID", "ACa182c1d04b387e0024e46850f9dd92e8");
define("TWILIO_TOKEN", "9d8caba36b36376c4973215e678f183c");
define("TWILIO_NUMBER", "+19094543377");
define("TWILIO_PREFIX", "+1");

// Database Constants
define("DB_USER", "bus-admin");
define("DB_PASS", "only-1-King");
define("DB_SERVER", "localhost");
define("DB_NAME", "bus-riders");

// General Constants
define("SUCCESS", "SUCCESS");
define("FAIL", "FAIL");
define("SENDER", "S");
define("RECEIVER", "R");
define("EEOL", "<br>\n");
define("NEW_ID", "-1");

// Status Values

define("DISPATCH_BLOCKED", "DISPATCH_BLOCKED");               
define("DISPATCH_CLEANUP", "DISPATCH_CLEANUP");               
define("DISPATCH_CONVERSATION", "DISPATCH_CONVERSATION");
define("DISPATCH_END_SESSION", "DISPATCH_END_SESSION");       
define("DISPATCH_LIST_CHOICES", "DISPATCH_LIST_CHOICES");
define("DISPATCH_NEW_SESSION", "DISPATCH_NEW_SESSION");
define("DISPATCH_PAX_CONFIRM", "DISPATCH_PAX_CONFIRM");       
define("DISPATCH_PAX_COUNT", "DISPATCH_PAX_COUNT");
define("DISPATCH_REGISTRATION", "DISPATCH_REGISTRATION");
define("DISPATCH_RSVP_CHOICES", "DISPATCH_RSVP_CHOICES");
define("DISPATCH_RSVP_CONFIRM", "DISPATCH_RSVP_CONFIRM");
define("DISPATCH_RSVP_LIST", "DISPATCH_RSVP_LIST");

// define("RSVP_STS", "RSVP_STS");
// define("RSVP_NAME", "RSVP_NAME");
// define("RSVP_REGISTER", "RSVP_REGISTER");
// define("RSVP_NEW", "RSVP_NEW");
// define("RSVP_CANCEL", "RSVP_CANCEL");
// define("RSVP_ADDRESS", "RSVP_ADDRESS");
// define("RSVP_LIST", "RSVP_LIST");
// define("RSVP_SELECT", "RSVP_SELECT");
// define("RSVP_DELETE", "RSVP_DELETE");
// define("RSVP_CHOICE", "RSVP_CHOICE");
// define("RSVP_PASSENGERS", "RSVP_PASSENGERS");
// define("RSVP_CONFIRM", "RSVP_CONFIRM");
// define("RSVP_CLEANUP", "RSVP_CLEANUP");
// define("RSVP_CLOSED", "RSVP_CLOSED");
// define("RSVP_BLOCKED", "RSVP_BLOCKED");
// define("RSVP_ERR", "RSVP_ERR");

// Special Commands
define("RESET_TABLES", "RESET_TABLES");
define("REGISTER_ME", "REGISTER_ME");

// Response Values
define("RIDE", "RIDE");
define("RIDE2", "-RIDE-");
define("CANCEL", "CANCEL");
define("RESET", "RESET");
define("EXIT", "EXIT");

//==============================================================
// hack_check: Function checks for know exploits
//==============================================================
function hack_check($data) 
{
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
//==============================================================
// startsWith: Function compares start of string
//==============================================================
function startsWith($haystack, $needle) 
{
    // search backwards starting from haystack length characters from the end
    return $needle === "" 
      || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}
//==============================================================
// endsWith: Function compares end of string
//==============================================================
function endsWith($haystack, $needle) 
{
    // search forward starting from end minus needle length characters
    return $needle === "" 
      || (($temp = strlen($haystack) - strlen($needle)) >= 0 
        && strpos($haystack, $needle, $temp) !== false);
}
//==============================================================
// get_pdo_connection: Returns the current PDO Connection
//==============================================================
$_connection = NULL;
function get_pdo_connection() 
{
  global $_connection;
  if($_connection == NULL)
  {
    $dsn = "mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME;
    $_connection = new PDO($dsn, DB_USER, DB_PASS);
    $_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Catch Expection if there is a connection error...
  }
  return $_connection;
}
//==============================================================
// __autoload: Specifies path for all class files
//==============================================================
function __autoload($class_name)
{
  include_once 'inc/class.' . $class_name . '.inc.php';
}
//==============================================================
// format_phone: Formats the phone number as (999) 999-9990
//==============================================================
function format_phone($phone) 
{
  return "(" . substr($phone,0,3) . ") "
    . substr($phone,3,3) . "-" . substr($phone,6,4);
}

?>
