<?php
include_once("constants.php");
     
class Calendar
{
  private $_error = "";
  
  function __construct() {}

  public function get_error() { return $this->_error; }
  public function set_error($error) { $this->_error = $error; }
  
  public function set_session_calendar($rider_id, $ses_id, $ses_datetime)
  {
    try 
    {
      $time = new Timestamp();
      
      $next_week = $time->get_tenth_day();

      $pdo = get_pdo_connection();

      $sql = "DELETE FROM session_choices WHERE ses_id = " . $ses_id;
      $cnt = $pdo->exec($sql);

      $sql = "SELECT svs_id FROM services "
        . "WHERE svs_expiration > :ses_datetime "
        . "AND svs_datetime < :next_week "
        . "AND svs_id NOT IN "
        . "(SELECT svs_id FROM bus_rides "
        . "WHERE rider_id = :rider_id) "
        . "ORDER BY svs_datetime";

      if ($statement = $pdo->prepare($sql))
      {
        $statement->bindValue(":next_week", $next_week, PDO::PARAM_STR);
        $statement->bindValue(":ses_datetime", $ses_datetime, PDO::PARAM_STR);
        $statement->bindValue(":rider_id", $rider_id, PDO::PARAM_INT);
        $statement->execute();

        $sql = "INSERT INTO session_choices "
         . "(ses_id, svs_id, identifier) "
         . "VALUES(:ses_id, :svs_id, :identifier)";

        if ($inner_stmt = $pdo->prepare($sql))
        {
          $identifier = ord("A");
          
          while ($row_set = $statement->fetch(PDO::FETCH_BOTH)) 
          {
            $inner_stmt->bindValue(":ses_id", $ses_id, PDO::PARAM_INT);
            $inner_stmt->bindValue(":svs_id", $row_set['svs_id'], PDO::PARAM_INT);
            $inner_stmt->bindValue(":identifier", chr($identifier), PDO::PARAM_STR);
            $inner_stmt->execute();

            $identifier++;
          }
        }
        else 
        {
          throw new Exception("Could not create prepared inner_stmt.");
        }
      }
      else
      {
        throw new Exception("Could not create prepared statement.");
      }
    } 
    catch (Exception $e) 
    {
      $this->set_error("set_session_calendar::" . $e->getMessage());
    }
  }
  
  public function update_calendar()
  {
    try 
    {
      $time = new Timestamp();
      
      $max_service_date = $this->get_max_service_date();
    
      $sql = "SELECT * FROM default_services "
        . "ORDER BY day_of_week,	time_of_day";

      $pdo = get_pdo_connection();
      if ($statement = $pdo->prepare($sql))
      {
        $statement->execute();

        $next_month = $time->get_next_month();

        $sql = "INSERT INTO services (svs_datetime, svs_expiration) "
          . "VALUES (:svs_datetime, :svs_expiration)";

        if ($inner_stmt = $pdo->prepare($sql)){}
        else 
        {
          throw new Exception("Could not create prepared inner_stmt.");
        }

        while ($row_set = $statement->fetch(PDO::FETCH_BOTH)) 
        {
          $dow = $row_set['day_of_week'];
          $tod = $row_set['time_of_day'];
          
          $curr_date = $time->get_next_day($max_service_date);
          while($curr_date < $next_month)
          {
            if($dow == $time->get_day_of_week($curr_date))
            {
              $new_service = $time->fix_timestamp($curr_date, $tod);
              $new_expiration = $time->get_service_expiration($new_service);

              $inner_stmt->bindValue(":svs_datetime", 
                  $new_service, PDO::PARAM_STR);
              $inner_stmt->bindValue(":svs_expiration", 
                  $new_expiration, PDO::PARAM_STR);
              $inner_stmt->execute();
              
              // echo "Added " . $new_service 
                // . "  " . $time->format_timestamp($new_service)
                // . "  " . $new_expiration
                // . "  " . $time->format_timestamp($new_expiration)
                // . "<br>\n";
              
              $curr_date = $time->get_next_week($curr_date);
            }
            else
            {
              $curr_date = $time->get_next_day($curr_date);
            }
          }
        }
      }
    } 
    catch (Exception $e) 
    {
      $this->set_error("update_calendar::" . $e->getMessage());
    }
  }
  
  private function get_max_service_date()
  {
    $time = new Timestamp();
    
    $max_service_date = $time->get_datetime();
    try 
    {
      $sql = "SELECT MAX(svs_datetime) AS max_service_date "
        . "FROM services";
      
      $pdo = get_pdo_connection();
      if ($statement = $pdo->prepare($sql))
      {
        $statement->execute();

        if ($row_set = $statement->fetch(PDO::FETCH_BOTH)) 
        {
          $max_service_date = (strlen($row_set['max_service_date']) == 0) ?
            $max_service_date : $row_set['max_service_date'];
        }
      }
      else 
      {
        throw new Exception("Could not create prepared statement.");
      }
    } 
    catch (Exception $e) 
    {
      $this->_error = "get_max_service_date::" . $e->getMessage();
    }
    return $max_service_date;
  }
}
?>
