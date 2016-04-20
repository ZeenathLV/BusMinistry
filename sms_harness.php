<?php
include_once("constants.php");
include_once(ROOT_DIR . "inc/database_tables.php");

echo "ROOT_DIR: " . ROOT_DIR . EEOL;
echo "ROOT_PATH: " . ROOT_PATH . EEOL;

try 
{
  echo "ROOT_DIR: " . ROOT_DIR . EEOL;  
  echo "DB_USER: " . DB_USER . EEOL;  
  echo "DB_PASS: " . DB_PASS . EEOL;  
  echo "DB_SERVER: " . DB_SERVER . EEOL;  
  echo "DB_NAME: " . DB_NAME . EEOL;  

  $dsn = "mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME;
  $pdo = new PDO($dsn, DB_USER, DB_PASS);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $sql = "SELECT * FROM `albums`";
  if ($statement = $pdo->prepare($sql))
  {
    $statement->execute();
    while ($row_set = $statement->fetch(PDO::FETCH_BOTH)) 
    {
      echo $row_set['NAME'] . EEOL;
    }
  }
  else
  {
    throw new Exception("Could not create prepared statement.");
  }
} 
catch (Exception $e) 
{
  echo "Error: " . $e->getMessage();  
}


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


?>


