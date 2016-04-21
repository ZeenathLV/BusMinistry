<?php
include_once("constants.php");

$phone = "NOT_SET";
if ($_REQUEST)
{
  $phone = $_REQUEST['From'];
  $message = $_REQUEST['Body'];
}

$rider = new BusRider();

$rider->load_rider($phone,$message);

echo "phone_number: " . $rider->get_phone_number() . EEOL;
echo "name_first: " . $rider->get_name_first() . EEOL;
echo "sms_number: " . $rider->get_sms_number() . EEOL;
echo "sms_message: " . $rider->get_sms_message() . EEOL;




// $ndx = 1;
// $table_count = db_func_get_table_count();
// $table_name = db_func_get_table_name($ndx);
// $table_query = db_func_get_table_query($ndx);
// $table_remarks = db_func_get_table_remarks($ndx);

// echo "table_count: " . $table_count . EEOL;
// echo " table_name: " . $table_name . EEOL;
// echo "table_query: " . $table_query . EEOL;
// echo " table_remarks: " . $table_remarks . EEOL;

// try 
// {
//   $pdo = get_pdo_connection();
//   if(endsWith($table_name,"insert"))
//   {
//     /*    Do nothing   */
//   }
//   else if(endsWith($table_name,"_v"))
//   {
//     $sql = "DROP VIEW IF EXISTS " . $table_name;
//     $cnt = $pdo->exec($sql);
//     echo "DROP VIEW: " . $table_name . EEOL;
//   }
//   else
//   {
//     $sql = "DROP TABLE IF EXISTS " . $table_name;
//     $cnt = $pdo->exec($sql);
//     echo "DROP TABLE: " . $table_name . EEOL;
//   }

//   $cnt = $pdo->exec($table_query);
//   echo "EXECUTE QUERY..." . EEOL . EEOL;
//   db_func_set_table_remarks($ndx, "Success");
// } 
// catch (Exception $e) 
// {
//   db_func_set_table_remarks($ndx, $e->getMessage());
// }
// echo "final table_remarks: " . db_func_get_table_remarks($ndx) . EEOL;


/*

/homepages/16/d207522547/htdocs/bolden1/rcwoc/twilio-php-master/Services/Twilio.php
*/

?>


