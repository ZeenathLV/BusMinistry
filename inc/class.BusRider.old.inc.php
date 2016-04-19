<?php
include_once("constants.php");
include_once(ROOT_DIR . "twilio-php-master/Services/Twilio.php");
      
class BusRider
{
  private $_twilio_enabled = false;
  
  private $_sms_number;
  private $_sms_message;

  private $_rider_id = NEW_ID;
  private $_phone_number = "";
  private $_name_last = "";
  private $_name_first = "Rider";
  private $_address1 = "";
  private $_address2 = "";
  private $_city = "";
  private $_zip_code = "";
  private $_special_handling = "";
  private $_update_pwd = "";
  private $_update_pwd_exp = "";
  private $_last_update = "";
  private $_ses_id = "";
  private $_ses_datetime = "";
  private $_ses_expiration = "";
  private $_ses_status = "";
  private $_error = "";
  
  function __construct() {}
  
  public function get_rider_id() { return $this->_rider_id; }
  public function get_phone_number() { return $this->_phone_number; }
  public function get_name_last() { return $this->_name_last; }
  public function get_name_first() { return $this->_name_first; }
  public function get_address1() { return $this->_address1; }
  public function get_address2() { return $this->_address2; }
  public function get_city() { return $this->_city; }
  public function get_zip_code() { return $this->_zip_code; }
  public function get_special_handling() { return $this->_special_handling; }

  public function get_update_pwd() { return $this->_update_pwd; }
  public function get_update_pwd_exp() { return $this->_update_pwd_exp; }
  public function get_last_update() { return $this->_last_update; }
  
  public function get_ses_id() { return $this->_ses_id; }
  public function get_ses_datetime() { return $this->_ses_datetime; }
  public function get_ses_expiration() { return $this->_ses_expiration; }
  public function get_ses_status() { return $this->_ses_status; }

  public function get_error() { return $this->_error; }

  public function get_sms_number() { return $this->_sms_number; }
  public function get_sms_message() { return $this->_sms_message; }
  
  public function set_error($error) { $this->_error = $error; }

  public function set_rider_id($rider_id) { $this->_rider_id = $rider_id; }
  public function set_phone_number($phone_number) { $this->_phone_number = $phone_number; }
  public function set_name_last($name_last) { $this->_name_last = $name_last; }
  public function set_name_first($name_first) { $this->_name_first = $name_first; }
  public function set_address1($address1) { $this->_address1 = $address1; }
  public function set_address2($address2) { $this->_address2 = $address2; }
  public function set_city($city) { $this->_city = $city; }
  public function set_zip_code($zip_code) { $this->_zip_code = $zip_code; }
  public function set_special_handling($special_handling) { $this->_special_handling = $special_handling; }

  public function set_update_pwd($update_pwd) { $this->_update_pwd = $update_pwd; }
  public function set_update_pwd_exp($update_pwd_exp) { $this->_update_pwd_exp = $update_pwd_exp; }
  public function set_last_update($last_update) { $this->_last_update = $last_update; }

  public function set_ses_id($ses_id) { $this->_ses_id = $ses_id; }
  public function set_ses_datetime($ses_datetime) { $this->_ses_datetime = $ses_datetime; }
  public function set_ses_expiration($ses_expiration) { $this->_ses_expiration = $ses_expiration; }
  public function set_ses_status($status) { $this->_ses_status = $status; }
  
  public function set_sms_number($sms_number) { $this->_sms_number = $sms_number; }
  public function set_sms_message($sms_message) 
  { 
    $this->_sms_message = hack_check($sms_message);
  }

  public function convert2html($message)
  {
    $len = mb_strlen($message);

    $result = "";
    for ($i = 0; $i < $len; $i++) 
    {
      $char = mb_substr($message, $i, 1);
      if (ord($char) == 10)
        $result .= "<br>";
      else
        $result .= $char;
    }
    return $result;
  }

  public function dispatch_address() 
  { 
    switch($this->get_sms_message())
    {
      case "NEW":
        $this->send_calendar_message();
        break;
      case "UPD":
        $this->send_update_address_message();
        break;
      case "LST":
        $this->send_list_choice_message();
        break;
      case "END":
        $this->send_end_session_message();
        break;
      default:
        $this->send_invalid_message(110);
        $this->set_sms_message(RIDE);
        $this->send_new_session_message();
        break;
    }
  }

  public function dispatch_confirm() 
  { 
    $confirm = $this->get_sms_message();
    
    if($confirm == "END")
    {
      $this->send_end_session_message();
    }
    else if(startsWith($confirm,"N"))
    {
      $this->send_calendar_message();
    }
    else  if(startsWith($confirm,"Y"))
    {
      $this->send_cleanup_message();
    }
    else
    {
      $this->send_invalid_message(120);
      $this->send_choice_message();
    }
  }

  public function dispatch_list() 
  { 
    switch($this->get_sms_message())
    {
      case "NEW":
        $this->send_calendar_message();
        break;
      case "DEL":
        $this->send_delete_message();
        break;
      case "END":
        $this->send_end_session_message();
        break;
      default:
        $this->send_invalid_message(130);
        $this->set_sms_message(RIDE);
        $this->send_new_session_message();
        break;
    }
  }
  
  public function dispatch_status() 
  { 
    switch ($this->get_ses_status()) 
    {
      case REGISTER_ME:
        $this->register_me();
        break;
      case RESET_TABLES:
        $this->reset_tables();
        break;
      case RSVP_REGISTER:
        $this->send_registration_message();
        break;
      case RSVP_CLOSED:
      case RSVP_NEW:
        $this->send_new_session_message();
        break;
      case RSVP_ADDRESS:
        $this->dispatch_address();
        break;
      case RSVP_LIST:
        $this->dispatch_list();
        break;
      case RSVP_CHOICE:
        $this->send_choice_message();
        break;
      case RSVP_CONFIRM:
        $this->dispatch_confirm();
        break;
      case RSVP_DELETE:
        $this->send_confirm_delete();
        break;
      case RSVP_CLEANUP:
        $this->send_cleanup_message();
        break;
      default:
        $this->send_invalid_message(140);
        break;
    }
  }

  public function has_error() 
  {
    return (strlen($this->_error)>0); 
  }

  private function get_update_password()
  {
    $alphabet = "abcdefghijklmnopqrstuwxyz" 
      . "9876543210" 
      . "ABCDEFGHIJKLMNOPQRSTUWXYZ"
      . "0123456789";

    $pass = "";
    $len = strlen($alphabet);
    for ($i = 0; $i < 8; $i++) 
    {
      $n = rand(0, $len-1);
      $pass .= substr($alphabet,$n,1);
    }
    return $pass;
  }

  public function get_log_display()
  {
    try 
    {
      $time = new Timestamp();
      
      $baseline = "\t\t\t";
      
      $sql =
        "SELECT ses_sender, ses_text FROM session_log_v "
          . "WHERE phone_number = :phone_number "
          . "AND ses_expiration > :ses_datetime "
          . "ORDER BY phone_number, ses_datetime DESC, ses_sender_ord DESC, ses_log_id DESC";
      
      $pdo = get_pdo_connection();
      if ($statement = $pdo->prepare($sql))
      {
        $statement->bindValue(":phone_number", $this->get_sms_number(), PDO::PARAM_STR);
        $statement->bindValue(":ses_datetime", $this->get_ses_datetime(), PDO::PARAM_STR);
        $statement->execute();

        $cnt = 0;
        $result = "";
        $rider = (strlen($this->get_name_first())>0)? $this->get_name_first() : "Rider";
        while ($row_set = $statement->fetch(PDO::FETCH_ASSOC))  
        {
          $msg = $this->convert2html($row_set['ses_text']);
          
          $result .= $baseline . "<tr>\n";
          $result .= $baseline . "\t<td class=\"filterable-cell\">\n";
          $result .= $baseline . "\t\t<b>" 
            . (($row_set['ses_sender'] == SENDER) ? $this->get_name_first() . ":" : "Rock:" )
            . "</b><br>";
          $result .= $msg . "\n";
          $result .= $baseline . "\t</td>\n";
          $result .= $baseline . "</tr>\n";
          $cnt++;
        }
        if($cnt == 0)
        {
          $result .= $baseline . "<tr>\n";
          $result .= $baseline . "\t<td class=\"filterable-cell\">\n";
          $result .= $baseline . "\t\t<b>Rock:</b>&nbsp;No Log Records Found\n";
          $result .= $baseline . "\t</td>\n";
          $result .= $baseline . "</tr>\n";
        }
        return $result;
      }
      else 
      {
        throw new Exception("Could not create prepared statement.");
      }
    } 
    catch (Exception $e) 
    {
      $this->set_error("get_log_display::" . $e->getMessage());
      $this->log_error();
    }
  }

  public function is_blocked($phone_number)
  { 
    try 
    { 
      $time = new Timestamp();
   
      $sql =
        "SELECT * FROM blocked_numbers  "
          . "WHERE blocked_number = :blocked_number ";

      $pdo = get_pdo_connection();
      if ($statement = $pdo->prepare($sql))
      {
        $statement->bindValue(":blocked_number", $phone_number, PDO::PARAM_STR);
        $statement->execute();

        if ($row_set = $statement->fetch(PDO::FETCH_BOTH)) 
        {
          return true;
        }
        else
        {
          return false;
        }
      }
      else 
      {
        throw new Exception("Could not create prepared statement.");
      }
    } 
    catch (Exception $e) 
    {
      $this->set_error("is_duplicate::" . $e->getMessage());
      $this->log_error();
      return false;
    }
  }

  public function is_duplicate($phone_number)
  { 
    try 
    { 
      $time = new Timestamp();
   
      $sql =
        "SELECT * FROM bus_riders  "
          . "WHERE phone_number = :phone_number ";

      $pdo = get_pdo_connection();
      if ($statement = $pdo->prepare($sql))
      {
        $statement->bindValue(":phone_number", $phone_number, PDO::PARAM_STR);
        $statement->execute();

        if ($row_set = $statement->fetch(PDO::FETCH_BOTH)) 
        {
          return true;
        }
        else
        {
          return false;
        }
      }
      else 
      {
        throw new Exception("Could not create prepared statement.");
      }
    } 
    catch (Exception $e) 
    {
      $this->set_error("is_duplicate::" . $e->getMessage());
      $this->log_error();
      return false;
    }
  }
  
  public function load($sms_number) 
  { 
    try 
    {
      $sql =
        "SELECT * FROM bus_riders "
          . "WHERE phone_number = :phone_number "
          . "LIMIT 1 ";
        
      $pdo = get_pdo_connection();
      if ($statement = $pdo->prepare($sql))
      {
        $statement->bindValue(":phone_number", $sms_number, PDO::PARAM_STR);
        $statement->execute();

        $rider_not_found = true;
        if ($row_set = $statement->fetch(PDO::FETCH_BOTH)) 
        {         
          $this->set_rider_id($row_set['rider_id']);
          $this->set_phone_number($row_set['phone_number']);
          $this->set_name_last($row_set['name_last']);
          $this->set_name_first($row_set['name_first']);
          $this->set_address1($row_set['address1']);
          $this->set_address2($row_set['address2']);
          $this->set_city($row_set['city']);
          $this->set_zip_code($row_set['zip_code']);
          $this->set_special_handling($row_set['special_handling']);
          $this->set_update_pwd($row_set['update_pwd']);
          $this->set_update_pwd_exp($row_set['update_pwd_exp']);
          $this->set_last_update($row_set['last_update']);
        }
      }
      else 
      {
        throw new Exception("Could not create prepared statement.");
      }
    } 
    catch (Exception $e) 
    {
      $this->set_error("load::" . $e->getMessage());
      $this->log_error();
    }
  }

  public function load_rider($sms_number, $sms_message) 
  { 
    try 
    {
      $time = new Timestamp();

      $this->set_sms_number($sms_number);
      $this->set_sms_message($sms_message);
      
      // Set the values for an unregistered rider
      $this->_rider_id = NEW_ID;
      $this->_phone_number = $sms_number;
      $this->_name_last = "Number not found";

      $this->set_ses_id($NEW_ID);
      $this->set_ses_datetime($time->get_datetime());
      $this->set_ses_expiration($time->get_session_expiration());
      $this->set_ses_status(RSVP_REGISTER);
      
      // Check for special commands to play with the database
      switch ($this->get_sms_message()) 
      {
        case REGISTER_ME:
        case RESET_TABLES:
          $this->set_ses_status($this->get_sms_message());
          return;
        default:
          break;
      }
      
      if($this->is_blocked($sms_number))
      {
        $this->set_ses_status(RSVP_BLOCKED);
        return;
      }
      
      $this->load($sms_number);
      if($this->has_error())
        throw new Exception($this->get_error());
      
      if($this->get_rider_id() == NEW_ID)
      {
        $this->set_ses_status(RSVP_REGISTER);
        return;
      }
      
      $this->load_session();
      
      $this->log_message(SENDER, $sms_message);
    } 
    catch (Exception $e) 
    {
      $this->set_error("load_rider::" . $e->getMessage());
      $this->log_error();
    }
  }

  public function load_session() 
  { 
    try 
    { 
      $time = new Timestamp();
   
      $sql =
        "SELECT * FROM sessions "
          . "WHERE rider_id = :rider_id "
          . "AND ses_expiration > :ses_datetime "
          . "AND ses_status <> :ses_status";

      $pdo = get_pdo_connection();
      if ($statement = $pdo->prepare($sql))
      {
        $statement->bindValue(":rider_id", $this->get_rider_id(), PDO::PARAM_INT);
        $statement->bindValue(":ses_datetime", $time->get_datetime(), PDO::PARAM_STR);
        $statement->bindValue(":ses_status", RSVP_CLOSED, PDO::PARAM_STR);
        $statement->execute();

        if ($row_set = $statement->fetch(PDO::FETCH_BOTH)) 
        {
          $this->set_ses_id($row_set['ses_id']);
          $this->set_ses_datetime($row_set['ses_datetime']);
          $this->set_ses_expiration($row_set['ses_expiration']);
          $this->set_ses_status($row_set['ses_status']);
        }
        else
        {
          $this->start_session();
        }
      }
      else 
      {
        throw new Exception("Could not create prepared statement.");
      }
    } 
    catch (Exception $e) 
    {
      $this->set_error("load_session::" . $e->getMessage());
      $this->log_error();
    }
  }

  public function load_update_pwd($update_pwd) 
  { 
    try 
    {
      $sql =
        "SELECT * FROM bus_riders "
          . "WHERE update_pwd = :update_pwd "
          . "LIMIT 1 ";
        
      $pdo = get_pdo_connection();
      if ($statement = $pdo->prepare($sql))
      {
        $statement->bindValue(":update_pwd", $update_pwd, PDO::PARAM_STR);
        $statement->execute();

        if ($row_set = $statement->fetch(PDO::FETCH_BOTH)) 
        {
          $this->set_rider_id($row_set['rider_id']);
          $this->set_phone_number($row_set['phone_number']);
          $this->set_name_last($row_set['name_last']);
          $this->set_name_first($row_set['name_first']);
          $this->set_address1($row_set['address1']);
          $this->set_address2($row_set['address2']);
          $this->set_city($row_set['city']);
          $this->set_zip_code($row_set['zip_code']);
          $this->set_special_handling($row_set['special_handling']);
          $this->set_update_pwd($row_set['update_pwd']);
          $this->set_update_pwd_exp($row_set['update_pwd_exp']);
          $this->set_last_update($row_set['last_update']);
        }
      }
      else 
      {
        throw new Exception("Could not create prepared statement.");
      }
    } 
    catch (Exception $e) 
    {
      $this->set_error("load::" . $e->getMessage());
      $this->log_error();
    }
  }

  public function log_error($message = NULL)
  {
    try
    {
      $time = new Timestamp();
      
      if($message == NULL) $message = $this->get_error();
      
      $sql =
        "INSERT INTO unregistered_log (
           unr_phone_number, unr_datetime, unr_expiration, unr_text)"
          . "VALUES (:unr_phone_number, :unr_datetime, :unr_expiration, :unr_text)";
 
      $pdo = get_pdo_connection();
      if ($statement = $pdo->prepare($sql))
      {
        $statement->bindValue(":unr_phone_number", $this->get_sms_number(), PDO::PARAM_STR);
        $statement->bindValue(":unr_datetime", $time->get_datetime(), PDO::PARAM_STR);
        $statement->bindValue(":unr_expiration", $time->get_session_expiration(), PDO::PARAM_STR);
        $statement->bindValue(":unr_text", $message, PDO::PARAM_STR);
        $statement->execute();
      }
      else 
      {
        throw new Exception("Could not create prepared statement.");
      }
    }
    catch (Exception $e) 
    {
      $this->set_error("log_error::" . $e->getMessage());
    }
  }

  public function log_message($sender, $message)
  {
    try
    {
      $time = new Timestamp();
      
      
      switch ($this->get_ses_status()) 
      {
        case REGISTER_ME:
        case RESET_TABLES:
        case RSVP_REGISTER:
        case RSVP_CLOSED:
        case RSVP_BLOCKED:
          $unregistered = true;
          break;
        default:
          $unregistered = false;
          break;
      }
      
      if($unregistered)
      {
        $sql =
          "INSERT INTO unregistered_log ("
            . "unr_phone_number, unr_datetime, unr_expiration, "
            . "unr_sender, unr_text)"
            . "VALUES (:unr_phone_number, :unr_datetime, "
            . ":unr_expiration, :unr_sender, :unr_text)";
   
        $pdo = get_pdo_connection();
        if ($statement = $pdo->prepare($sql))
        {
          $statement->bindValue(":unr_phone_number", $this->get_sms_number(), PDO::PARAM_STR);
          $statement->bindValue(":unr_datetime", $time->get_datetime(), PDO::PARAM_STR);
          $statement->bindValue(":unr_expiration", $time->get_session_expiration(), PDO::PARAM_STR);
          $statement->bindValue(":unr_sender", $sender, PDO::PARAM_STR);
          $statement->bindValue(":unr_text", $message, PDO::PARAM_STR);
          $statement->execute();
        }
        else 
        {
          throw new Exception("Could not create prepared statement.");
        }
      }
      else
      {
        $sql =
          "INSERT INTO session_log (ses_id, ses_datetime, ses_sender, ses_text)"
            . "VALUES (:ses_id, :ses_datetime, :ses_sender, :ses_text)";
   
        $pdo = get_pdo_connection();
        if ($statement = $pdo->prepare($sql))
        {
          $statement->bindValue(":ses_id", $this->get_ses_id(), PDO::PARAM_INT);
          $statement->bindValue(":ses_datetime", $time->get_datetime(), PDO::PARAM_STR);
          $statement->bindValue(":ses_sender", $sender, PDO::PARAM_STR);
          $statement->bindValue(":ses_text", $message, PDO::PARAM_STR);
          $statement->execute();
        }
        else 
        {
          throw new Exception("Could not create prepared statement.");
        }
      }
    }
    catch (Exception $e) 
    {
      $this->set_error("log_message::" . $e->getMessage());
      $this->log_error();
    }
  }
  public function register_me()
  {
    try
    {
      $sql = "INSERT INTO bus_riders "
        . "SELECT * FROM bus_riders_reg "
        . "WHERE phone_number = :phone_number ";
 
      $pdo = get_pdo_connection();
      if ($statement = $pdo->prepare($sql))
      {
        $statement->bindValue(":phone_number", $this->get_sms_number(), PDO::PARAM_STR);
        $statement->execute();
        
        $msg = "Your phone is now registered " 
          . "for The Rock Bus Ministry.\n"
          . "Text -RIDE- to make a reservation.";

        $this->set_ses_status(RSVP_REGISTER);
        $this->send_sms_message($msg);
      }
      else 
      {
        throw new Exception("Could not create prepared statement.");
      }
    }
    catch (Exception $e) 
    {
      $this->set_error("log_message::" . $e->getMessage());
      $this->log_error();
    }
  }

  public function reset_error() 
  {
    $this->set_error("");
  }

  public function reset_tables()
  {
    try 
    {
      $sql[] = "TRUNCATE TABLE session_choices";
      $sql[] = "TRUNCATE TABLE sessions";
      $sql[] = "TRUNCATE TABLE session_log";
      $sql[] = "TRUNCATE TABLE unregistered_log";
      $sql[] = "TRUNCATE TABLE bus_riders";
      $sql[] = "TRUNCATE TABLE bus_rides";
      // $sql[] = "ALTER TABLE session_choices AUTO_INCREMENT = 1 ";
      // $sql[] = "ALTER TABLE sessions AUTO_INCREMENT = 1 ";
      // $sql[] = "ALTER TABLE session_log AUTO_INCREMENT = 1 ";
      // $sql[] = "ALTER TABLE unregistered_log AUTO_INCREMENT = 1 ";
      // $sql[] = "ALTER TABLE bus_riders AUTO_INCREMENT = 1 ";
      
      $max = count($sql);
      
      $pdo = get_pdo_connection();
      
      for($i = 0; $i < $max; $i++)
      {
        $count = $pdo->exec($sql[$i]);
      }
      $msg = "The main tables have been reset.";

      $this->set_ses_status(RSVP_REGISTER);
      $this->send_sms_message($msg);
    } 
    catch (Exception $e) 
    {
      $this->set_error("reset_tables::" . $e->getMessage());
      $this->log_error();
    }
  }

  public function save()
  {
    try
    {
      $time = new Timestamp();
      
      if($this->get_rider_id()==$NEW_ID)
      {
        $sql =
          "INSERT INTO bus_riders (phone_number, name_first, name_last, "
          . "address1, address2, city, zip_code, special_handling, "
          . "update_pwd, update_pwd_exp, last_update)"
          . "VALUES (:phone_number, :name_first, :name_last, "
          . ":address1, :address2, :city, :zip_code, :special_handling, "
          . ":update_pwd, :update_pwd_exp, :last_update)";
      } 
      else
      {
        $sql =
          "UPDATE bus_riders SET phone_number = :phone_number, "
          . "name_first = :name_first, "
          . "name_last = :name_last, " 
          . "address1 = :address1, "
          . "address2 = :address2, "
          . "city = :city, " 
          . "zip_code  = :zip_code, "
          . "special_handling = :special_handling, "
          . "update_pwd = :update_pwd, "
          . "update_pwd_exp = :update_pwd_exp, "
          . "last_update = :last_update "
          . "WHERE rider_id = :rider_id";
      }
 
      $pdo = get_pdo_connection();
      if ($statement = $pdo->prepare($sql))
      {
        $statement->bindValue(":phone_number", $this->get_phone_number(), PDO::PARAM_STR);
        $statement->bindValue(":name_first", $this->get_name_first(), PDO::PARAM_STR);
        $statement->bindValue(":name_last", $this->get_name_last(), PDO::PARAM_STR);
        $statement->bindValue(":address1", $this->get_address1(), PDO::PARAM_STR);
        $statement->bindValue(":address2", $this->get_address2(), PDO::PARAM_STR);
        $statement->bindValue(":city", $this->get_city(), PDO::PARAM_STR);
        $statement->bindValue(":zip_code", $this->get_zip_code(), PDO::PARAM_STR);
        $statement->bindValue(":special_handling", $this->get_special_handling(), PDO::PARAM_STR);
        $statement->bindValue(":update_pwd", $this->get_update_pwd(), PDO::PARAM_STR);
        $statement->bindValue(":update_pwd_exp", $this->get_update_pwd_exp(), PDO::PARAM_STR);
        $statement->bindValue(":last_update", $time->get_datetime(), PDO::PARAM_STR);
        
        if($this->get_rider_id()!=$NEW_ID)
            $statement->bindValue(":rider_id", $this->get_rider_id(), PDO::PARAM_INT);

        $statement->execute();

        if($this->get_rider_id()==$NEW_ID)
        {
          $this->set_rider_id($pdo->lastInsertId()); 
        
          $msg = "Your phone is now registered " 
            . "for The Rock Bus Ministry.\n"
            . "Text -RIDE- to make a reservation.";

          $this->set_ses_status(RSVP_REGISTER);
        }
        else
        {
          $msg = "Your changes have been saved " 
            . "for The Rock Bus Ministry.\n"
            . "Text -RIDE- to make a reservation.";
        }
        $this->send_sms_message($msg);
      }
      else 
      {
        throw new Exception("Could not create prepared statement.");
      }
    }
    catch (Exception $e) 
    {
      $this->set_sms_number($this->get_phone_number());
      $this->set_error("start_session::" . $e->getMessage());
      $this->log_error();
    }
  }
 
  public function send_blocked_message()
  {
    $msg = "Regrettably, your number has been "
      . "blocked by the Bus Ministry App. "
      . "Call " . format_phone(substr(TWILIO_NUMBER,2,10))
      . " and leave a message to change this status.";
    $this->set_ses_status(RSVP_REGISTER);
    $this->send_sms_message($msg);
  }
 
  public function send_calendar_message()
  {
    try 
    {
      $time = new Timestamp();
      
      $cal = new Calendar();
      
      $cal->update_calendar();
      
      $cal->set_session_calendar(
          $this->get_rider_id(), 
          $this->get_ses_id(),
          $this->get_ses_datetime());
    
      $sql = "SELECT identifier, svs_datetime FROM session_choices_v "
        . "WHERE ses_id = :ses_id ORDER BY svs_datetime";

      $pdo = get_pdo_connection();
      if ($statement = $pdo->prepare($sql))
      {
        $statement->bindValue(":ses_id", $this->get_ses_id(), PDO::PARAM_INT);
        $statement->execute();

        $msg = "";
        while ($row_set = $statement->fetch(PDO::FETCH_BOTH)) 
        {
          $msg .= $row_set['identifier'] . ". "
            . $time->format_timestamp($row_set['svs_datetime']) . "\n";
        }
        $msg = "The following church services are "
          . "are available for reservations:\n"
          . $msg . "X. End this session\n\n"
          . "Text the letter for your choice.";

        $this->update_session(RSVP_PASSENGERS);
        $this->send_sms_message($msg);
      }
      else
      {
        throw new Exception("Could not create prepared statement.");
      }
    } 
    catch (Exception $e) 
    {
      $this->set_error("send_calendar_message::" . $e->getMessage());
      $this->log_error();
    }
  }

  public function send_choice_message()
  {
    try 
    {
      $time = new Timestamp();
      
      $identifier = $this->get_sms_message();
      
      if($identifier == "X")
      {
        $this->send_end_session_message();
        return;
      }
      $sql = 
        "DELETE FROM session_choices "
          . "WHERE ses_id = :ses_id "
          . "AND identifier <> :identifier";

      $pdo = get_pdo_connection();
      if ($statement = $pdo->prepare($sql))
      {
        $statement->bindValue(":ses_id", $this->get_ses_id(), PDO::PARAM_INT);
        $statement->bindValue(":identifier", $identifier, PDO::PARAM_STR);
        $statement->execute();
      }
      
      $sql = "SELECT identifier, svs_datetime FROM session_choices_v "
        . "WHERE identifier = :identifier";

      if ($statement = $pdo->prepare($sql))
      {
        $statement->bindValue(":identifier", $identifier, PDO::PARAM_STR);
        $statement->execute();

        if ($row_set = $statement->fetch(PDO::FETCH_BOTH)) 
        {
          $msg = "Please confirm your choice:\n"
            . $time->format_timestamp($row_set['svs_datetime'])
            . "\nText YES or NO or END to confirm or end";

          $this->update_session(RSVP_CONFIRM);
          $this->send_sms_message($msg);
        }
        else
        {
          $this->send_invalid_message(160);
          $this->update_session(RSVP_LIST);
          $this->send_calendar_message();
        }
      }
      else
      {
        throw new Exception("Could not create prepared statement.");
      }
    } 
    catch (Exception $e) 
    {
      $this->set_error("send_calendar_message::" . $e->getMessage());
      $this->log_error();
    }
  }

  public function send_confirm_delete()
  {
    try 
    {
      $time = new Timestamp();

      $identifier = $this->get_sms_message();
      
      if($identifier == "X")
      {
        $this->send_end_session_message();
        return;
      }
      
      $sql = "SELECT rider_id, svs_id, svs_datetime "
        . "FROM session_choices_v "
        . "WHERE ses_id = :ses_id "
        . "AND identifier = :identifier";

      $pdo = get_pdo_connection();
      if ($statement = $pdo->prepare($sql))
      {
        $statement->bindValue(":ses_id", $this->get_ses_id(), PDO::PARAM_INT);
        $statement->bindValue(":identifier", $identifier, PDO::PARAM_STR);
        $statement->execute();

        if ($row_set = $statement->fetch(PDO::FETCH_BOTH)) 
        {
          $sql = "DELETE FROM bus_rides "
            . "WHERE rider_id = :rider_id "
            . "AND svs_id = :svs_id";
          
          $choice_text = $time->format_timestamp($row_set['svs_datetime']);
          
          $inner_stmnt = $pdo->prepare($sql);
          if ($inner_stmnt = $pdo->prepare($sql))
          {  
            $inner_stmnt->bindValue(":svs_id", $row_set['svs_id'], PDO::PARAM_INT);
            $inner_stmnt->bindValue(":rider_id", $row_set['rider_id'], PDO::PARAM_INT);
            $inner_stmnt->execute();
          
            $msg = "Your reservation has been deleted:\n"
              . $identifier . ". " . $choice_text . "\n\n"
              . "Thank your for using the Rock Church Bus Ministry App."
              . "Text -RIDE- to make another reservation.\n";
              
            $this->update_session(RSVP_CLOSE);
            $this->send_sms_message($msg);
          }
          else
          {
            throw new Exception("Could not create prepared statement.");
          }
        }
        else
        {
          $this->send_invalid_message(170);
          $this->update_session(RSVP_ADDRESS);
          $this->send_list_choice_message();
        }
      }
      else
      {
        throw new Exception("Could not create prepared statement.");
      }

      $sql = "DELETE FROM session_choices "
        . "WHERE ses_id = :ses_id";

      if ($statement = $pdo->prepare($sql))
      {
        $statement->bindValue(":ses_id", $this->get_ses_id(), PDO::PARAM_INT);
        $statement->execute();
      }
      else
      {
        throw new Exception("Could not create prepared statement.");
      }
    } 
    catch (Exception $e) 
    {
      $this->set_error("send_calendar_message::" . $e->getMessage());
      $this->log_error();
    }
  }

  public function send_cleanup_message()
  {
    try 
    {
      $sql = "INSERT INTO bus_rides (rider_id, svs_id)"
        . "SELECT rider_id, svs_id FROM session_choices_v "
        . "WHERE ses_id = :ses_id";

      $pdo = get_pdo_connection();
      if ($statement = $pdo->prepare($sql))
      {
        $statement->bindValue(":ses_id", $this->get_ses_id(), PDO::PARAM_INT);
        $statement->execute();

        $msg = "On behalf of The Rock Church Bus Ministry "
          . "thank you for your reservation."
          . "Text -RIDE- to make another reservation.\n";
          
        $this->update_session(RSVP_CLOSED);
        $this->send_sms_message($msg);
      }
      else
      {
        throw new Exception("Could not create prepared statement.");
      }

      $sql = "DELETE FROM session_choices "
        . "WHERE ses_id = :ses_id";

      if ($statement = $pdo->prepare($sql))
      {
        $statement->bindValue(":ses_id", $this->get_ses_id(), PDO::PARAM_INT);
        $statement->execute();
      }
      else
      {
        throw new Exception("Could not create prepared statement.");
      }
    } 
    catch (Exception $e) 
    {
      $this->set_error("send_calendar_message::" . $e->getMessage());
      $this->log_error();
    }
  }
  
  public function send_delete_message()
  {
    try 
    {
      $time = new Timestamp();
    
      $sql = "SELECT identifier, svs_datetime FROM session_choices_v "
        . "WHERE ses_id = :ses_id ORDER BY svs_datetime";

      $pdo = get_pdo_connection();
      if ($statement = $pdo->prepare($sql))
      {
        $statement->bindValue(":ses_id", $this->get_ses_id(), PDO::PARAM_INT);
        $statement->execute();

        $msg = "";
        while ($row_set = $statement->fetch(PDO::FETCH_BOTH)) 
        {
          $msg .= $row_set['identifier'] . ". "
            . $time->format_timestamp($row_set['svs_datetime']) . "\n";
        }
        if(strlen($msg)==0)
        {
          $msg = "Delete Reservation:\n\n"  
          . "You have no reservations.\n\n";

          $this->update_session(RSVP_NEW);
          $this->send_sms_message($msg);
        }
        else
        {
          $msg = "Delete Reservation:\n" . $msg 
            . "\n"
            . "Text the letter for your choice.";
           
          $this->update_session(RSVP_DELETE);
          $this->send_sms_message($msg);
        }
      }
      else
      {
        throw new Exception("Could not create prepared statement.");
      }
    } 
    catch (Exception $e) 
    {
      $this->set_error("send_calendar_message::" . $e->getMessage());
      $this->log_error();
    }
  }
  
  public function send_end_session_message()
  {
    $msg = "You have chosen to end your "
      . "session. Thank you for using the . "
      . "Rock Church Bus Ministry App.\n"
      . "Text -RIDE- to make another reservation.\n";
      
    $this->update_session(RSVP_CLOSED);
    $this->send_sms_message($msg);
  }

  public function send_invalid_message($procedure)
  {
    $msg = "It appears you have entered "
      . "an incorrect response.\n" 
      . "Procedure: " . $procedure;

    $this->send_sms_message($msg);
  }

  public function send_list_choice_message()
  {
    try 
    {
      $this->set_current_ride_list();
      
      $time = new Timestamp();
    
      $sql = "SELECT identifier, svs_datetime FROM session_choices_v "
        . "WHERE ses_id = :ses_id ORDER BY svs_datetime";

      $pdo = get_pdo_connection();
      if ($statement = $pdo->prepare($sql))
      {
        $statement->bindValue(":ses_id", $this->get_ses_id(), PDO::PARAM_INT);
        $statement->execute();

        $msg = "";
        while ($row_set = $statement->fetch(PDO::FETCH_BOTH)) 
        {
          $msg .= $time->format_timestamp($row_set['svs_datetime']) . "\n";
        }
        if(strlen($msg)==0) $msg = "No Reservations";

        $msg = "You have the following current reservations:\n\n" . $msg 
          . "\n\n"
          . "NEW - New Bus Reservation\n"
          . "DEL - Delete reservation\n"
          . "END - End this session\n"
          . "Text one of the above 3 letter commands";

        $this->update_session(RSVP_LIST);
        $this->send_sms_message($msg);
      }
      else
      {
        throw new Exception("Could not create prepared statement.");
      }
    } 
    catch (Exception $e) 
    {
      $this->set_error("send_calendar_message::" . $e->getMessage());
      $this->log_error();
    }
  }

  public function send_new_session_message()
  {
    $sms_message = $this->get_sms_message();
    
    if($sms_message === RIDE)
    {
      $msg = "Welcome to the Rock Bus Ministry\n"
        . "Name: " . trim($this->get_name_first()) . " " 
        . trim($this->get_name_last()) . "\n"
        . "Address: " . trim($this->get_address1()) . " " 
        . trim($this->get_address2()) . "\n"
        . "City: " . trim($this->get_city()) . " " 
        . trim($this->get_zip_code()) . "\n"
        . "Notes: " . trim($this->get_special_handling()). "\n\n"
        . "NEW - New Bus Reservation\n"
        . "UPD - Update address\n"
        . "LST - List current reservations\n"
        . "END - End this session\n"
        . "Text one of the above 3 letter commands";
        
      $this->update_session(RSVP_ADDRESS);
    }
    else
    {
      $this->send_invalid_message(180);
      $msg = "If you would like a bus reservation "
        . "please text -RIDE-";
    }
    $this->send_sms_message($msg);
  }

  public function send_passengers_message()
  {
    try 
    {
      $time = new Timestamp();
      
      $identifier = $this->get_sms_message();
      
      if($identifier == "X")
      {
        $this->send_end_session_message();
        return;
      }

      $sql = 
        "DELETE FROM session_choices "
          . "WHERE ses_id = :ses_id "
          . "AND identifier <> :identifier";

      $pdo = get_pdo_connection();
      if ($statement = $pdo->prepare($sql))
      {
        $statement->bindValue(":ses_id", $this->get_ses_id(), PDO::PARAM_INT);
        $statement->bindValue(":identifier", $identifier, PDO::PARAM_STR);
        $statement->execute();
      }
      
      $sql = "SELECT identifier, svs_datetime FROM session_choices_v "
        . "WHERE identifier = :identifier";

      if ($statement = $pdo->prepare($sql))
      {
        $statement->bindValue(":identifier", $identifier, PDO::PARAM_STR);
        $statement->execute();

        if ($row_set = $statement->fetch(PDO::FETCH_BOTH)) 
        {
          $msg = "You have chosen the following:\n"
            . $time->format_timestamp($row_set['svs_datetime'])
            . "\n\nEnter the number of passengers.  Type -0- to cancel:";

          $this->update_session(RSVP_CONFIRM);
          $this->send_sms_message($msg);
        }
        else
        {
          $this->send_invalid_message(160);
          $this->send_calendar_message();
        }
      }
      else
      {
        throw new Exception("Could not create prepared statement.");
      }
    } 
    catch (Exception $e) 
    {
      $this->set_error("send_calendar_message::" . $e->getMessage());
      $this->log_error();
    }
  }

  public function send_update_address_message()
  {
    try 
    {
      $time = new Timestamp();
      
      $sql = "UPDATE bus_riders SET update_pwd = :update_pwd, "
        . "update_pwd_exp = :update_pwd_exp "
        . "WHERE rider_id = :rider_id";
      
      $update_pwd = $this->get_update_password();
      $update_pwd_exp = $time->get_session_expiration();
      
      $pdo = get_pdo_connection();
      if ($statement = $pdo->prepare($sql))
      {
        $statement->bindValue(":update_pwd", $update_pwd, PDO::PARAM_STR);
        $statement->bindValue(":update_pwd_exp", $update_pwd_exp, PDO::PARAM_STR);
        $statement->bindValue(":rider_id", $this->get_rider_id(), PDO::PARAM_INT);
        $statement->execute();

        $msg = "Click on this link  to update your address:\n"
          . "bit.lo?access=" . $update_pwd
          . "\nThis link will be valid for only one hour.\n"
          . "When you have completed the registration, "
          . "text -RIDE- and you will be able to select "
          . "a ride for upcoming Rock Church services.";

        $this->update_session(RSVP_CLOSED);
        $this->send_sms_message($msg);
      }
      else
      {
        throw new Exception("Could not create prepared statement.");
      }
    } 
    catch (Exception $e) 
    {
      $this->set_error("send_update_address_message::" . $e->getMessage());
      $this->log_error();
    }
  }

  public function send_registration_message()
  {
    $msg = "Welcome to The Rock Bus Ministry\n"
      . "It appears you have not registered with us. "
      . "Click on this link: bit.lo/aTky to register. "
      . "When you have completed the registration, "
      . "text -RIDE- and you will be able to select "
      . "a ride for upcoming Rock Church services.";
      
    $this->send_sms_message($msg);
  }
  
  public function send_sms_message($message)
  {
    if($this->_twilio_enabled)
    {
      $client = new Services_Twilio(TWILIO_SID, TWILIO_TOKEN);

      try 
      {
        $message = $client->account->messages->create(array(
            "From" => TWILIO_NUMBER,
            "To" => $this->_phone_number,
            "Body" => $message
        ));
      } 
      catch (Services_Twilio_RestException $e) 
      {
        $this->set_error("send_sms_message::" . $e->getMessage());
        $this->log_error();
      }
    }

    $this->log_message(RECEIVER, $message);
  }

  public function set_session_choice($choice)
  {
    try
    {
      $time = new Timestamp();
      
      $sql =
        "INSERT INTO sessions (rider_id, ses_datetime, ses_expiration, ses_status)"
          . "VALUES (:rider_id, :ses_datetime, :ses_expiration, :ses_status)";
 
      $pdo = get_pdo_connection();
      if ($statement = $pdo->prepare($sql))
      {
        $this->set_ses_datetime($time->get_datetime());
        $this->set_ses_expiration($time->get_session_expiration());
        $this->set_ses_status(RSVP_NEW);

        $statement->bindValue(":rider_id", $this->get_rider_id(), PDO::PARAM_INT);
        $statement->bindValue(":ses_datetime", $this->get_ses_datetime(), PDO::PARAM_STR);
        $statement->bindValue(":ses_expiration", $this->get_ses_expiration(), PDO::PARAM_STR);
        $statement->bindValue(":ses_status", $this->get_ses_status(), PDO::PARAM_STR);
        $statement->execute();
        $this->set_ses_id($pdo->lastInsertId()); 
      }
      else 
      {
        throw new Exception("Could not create prepared statement.");
      }
    }
    catch (Exception $e) 
    {
      $this->set_error("start_session::" . $e->getMessage());
      $this->log_error();
    }
  }

  public function set_current_ride_list()
  {
    try 
    {      
      $pdo = get_pdo_connection();

      $sql = "DELETE FROM session_choices WHERE ses_id = " . $this->get_ses_id();
      $cnt = $pdo->exec($sql);
      
      $sql = "SELECT r.svs_id, s.svs_datetime "
        . "FROM  bus_rides r INNER JOIN services s "
        . "ON r.svs_id = s.svs_id "
        . "WHERE r.rider_id = :rider_id "
        . "AND s.svs_datetime > :ses_datetime "
        . "ORDER BY s.svs_datetime";

      if ($statement = $pdo->prepare($sql))
      {
        $statement->bindValue(":rider_id", $this->get_rider_id(), PDO::PARAM_INT);
        $statement->bindValue(":ses_datetime", $this->get_ses_datetime(), PDO::PARAM_STR);
        $statement->execute();

        $sql = "INSERT INTO session_choices "
         . "(ses_id, svs_id, identifier) "
         . "VALUES(:ses_id, :svs_id, :identifier)";

        if ($inner_stmt = $pdo->prepare($sql))
        {
          $identifier = ord("A");          
          while ($row_set = $statement->fetch(PDO::FETCH_BOTH)) 
          {
            $inner_stmt->bindValue(":ses_id", $this->get_ses_id(), PDO::PARAM_INT);
            $inner_stmt->bindValue(":svs_id", $row_set['svs_id'], PDO::PARAM_INT);
            $inner_stmt->bindValue(":identifier", chr($identifier), PDO::PARAM_STR);
            $inner_stmt->execute();
              
            $identifier++;
          }
          if($identifier == ord("A"))
            echo "No rides found."  . "<br>\n";
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
      $this->set_error("set_current_rides::" . $e->getMessage());
    }
  }

  public function start_session()
  {
    try
    {
      $time = new Timestamp();
      
      $sql =
        "INSERT INTO sessions (rider_id, ses_datetime, ses_expiration, ses_status)"
          . "VALUES (:rider_id, :ses_datetime, :ses_expiration, :ses_status)";
 
      $pdo = get_pdo_connection();
      if ($statement = $pdo->prepare($sql))
      {
        $this->set_ses_datetime($time->get_datetime());
        $this->set_ses_expiration($time->get_session_expiration());
        $this->set_ses_status(RSVP_NEW);

        $statement->bindValue(":rider_id", $this->get_rider_id(), PDO::PARAM_INT);
        $statement->bindValue(":ses_datetime", $this->get_ses_datetime(), PDO::PARAM_STR);
        $statement->bindValue(":ses_expiration", $this->get_ses_expiration(), PDO::PARAM_STR);
        $statement->bindValue(":ses_status", $this->get_ses_status(), PDO::PARAM_STR);
        $statement->execute();
        $this->set_ses_id($pdo->lastInsertId()); 
      }
      else 
      {
        throw new Exception("Could not create prepared statement.");
      }
    }
    catch (Exception $e) 
    {
      $this->set_error("start_session::" . $e->getMessage());
      $this->log_error();
    }
  }

  public function update_session($new_status)
  {
    try
    {
      $time = new Timestamp();
      
      $sql =
        "UPDATE sessions SET "
          . "ses_expiration = :ses_expiration,"
          . "ses_status = :ses_status "
          . "WHERE ses_id = :ses_id";
 
      $pdo = get_pdo_connection();
      
      if ($statement = $pdo->prepare($sql))
      {
        $new_expiration = ($new_status == RSVP_CLOSED)
            ? $time->get_datetime() : $time->get_session_expiration();
        
        $this->set_ses_expiration($new_expiration);
        $this->set_ses_status($new_status);

        $statement->bindValue(":ses_id", $this->get_ses_id(), PDO::PARAM_INT);
        $statement->bindValue(":ses_expiration", $this->get_ses_expiration(), PDO::PARAM_STR);
        $statement->bindValue(":ses_status", $this->get_ses_status(), PDO::PARAM_STR);
        $statement->execute();
      }
      else 
      {
        throw new Exception("Could not create prepared statement.");
      }
    }
    catch (Exception $e) 
    {
      $this->set_error("update_session::" . $e->getMessage());
      $this->log_error();
    }
  }

  public function test_load() 
  { 
    // $this->_rider_id = 1234;
    // $this->_phone_number = "216-441-2048";
    // $this->_name_last = "Bolden";
    // $this->_name_first = "Nate";
    // $this->_address1 = "7819 Sleeping Pine St";
    // $this->_address2 = "Apt 3";
    // $this->_city = "Las Vegas";
    // $this->_zip_code = "89143";
    // $this->_special_handling = "Key under mat";
    // $this->_update_pwd = "OppsUpsideYaHead";
    // $this->_update_pwd_exp = "20160326230015";
    // $this->_last_update = "20160325182211";
    // $this->_ses_id = "C0005";
    // $this->_ses_datetime = get_datetime();
    // $this->_ses_expiration = get_session_expiration();
    // $this->_ses_status = RSVP_NEW;
  }
}
?>
