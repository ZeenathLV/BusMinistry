<?php
// Time Constants
define("DOW_SUN", "0");
define("DOW_MON", "1");
define("DOW_TUE", "2");
define("DOW_WED", "3");
define("DOW_THU", "4");
define("DOW_FRI", "5");
define("DOW_SAT", "6");

define("DATETIME_FORMAT", "YmdHis");
define("DATETIME_OUTPUT", "D M d g:ia");
define("DOW", "w");
define("LOCAL_TIMEZONE", "America/Los_Angeles");

class Timestamp
{
  public function fix_timestamp($some_day, $some_time)
  {
    $result = substr($some_day ,0,8) 
      . $some_time . "00";
    
    return $result;
  }

  public function format_timestamp($some_day)
  {
    $exp = $this->convert_datetime($some_day);
    return $exp->format(DATETIME_OUTPUT);
  }

  public function convert_datetime($some_day)
  {
    // 0123456789012345
    // 20160408125511
    
    $y = substr($some_day , 0 , 4 );
    $m = substr($some_day , 4 , 2 );
    $d = substr($some_day , 6 , 2 );
    $h = substr($some_day , 8 , 2 );
    $M = substr($some_day , 10 , 2 );
    $s = substr($some_day , 12 , 2 );
    
    $exp = new DateTime();
    $exp->setTimezone(new DateTimeZone(LOCAL_TIMEZONE));
    $exp->setDate($y,$m,$d);
    $exp->setTime($h,$M,$s);
    return $exp;
  }

  public function get_datetime()
  {
    $exp = new DateTime();
    $exp->setTimezone(new DateTimeZone(LOCAL_TIMEZONE));
    return $exp->format(DATETIME_FORMAT);
  }
  
  public function get_day_of_week($some_day)
  {
    $exp = $this->convert_datetime($some_day);
    return $exp->format(DOW);
  }

  public function get_next_day($some_day = NULL)
  {
    if($some_day == NULL)
    {
      $exp = new DateTime(); 
      $exp->setTimezone(new DateTimeZone(LOCAL_TIMEZONE));
    }
    else
      $exp = $this->convert_datetime($some_day);
    
    $exp->modify('+ 1 day'); 
    return $exp->format(DATETIME_FORMAT);
  }

  public function get_next_month($some_day = NULL)
  {
    if($some_day == NULL)
    {
      $exp = new DateTime(); 
      $exp->setTimezone(new DateTimeZone(LOCAL_TIMEZONE));
    }
    else
      $exp = $this->convert_datetime($some_day);

    $exp->modify('+ 1 month'); 
    return $exp->format(DATETIME_FORMAT);
  }

  public function get_next_week($some_day = NULL)
  {
    if($some_day == NULL)
    {
      $exp = new DateTime(); 
      $exp->setTimezone(new DateTimeZone(LOCAL_TIMEZONE));
    }
    else
      $exp = $this->convert_datetime($some_day);
    
    $exp->modify('+ 1 week'); 
    return $exp->format(DATETIME_FORMAT);
  }

  public function get_previous_day($some_day = NULL)
  {
    if($some_day == NULL)
    {
      $exp = new DateTime(); 
      $exp->setTimezone(new DateTimeZone(LOCAL_TIMEZONE));
    }
    else
      $exp = $this->convert_datetime($some_day);
    
    $exp->modify('- 1 day'); 
    return $exp->format(DATETIME_FORMAT);
  }

  public function get_tenth_day($some_day = NULL)
  {
    if($some_day == NULL)
    {
      $exp = new DateTime(); 
      $exp->setTimezone(new DateTimeZone(LOCAL_TIMEZONE));
    }
    else
      $exp = $this->convert_datetime($some_day);
    
    $exp->modify('+ 10 day'); 
    return $exp->format(DATETIME_FORMAT);
  }

  public function get_service_expiration($some_day)
  {
    // Service cutoff is 1700 the friday before for weekend svs
    // and the day before for all other services.
    
    $cutoff_time = "1700";
    $forever = true;

    $day_of_week = $this->get_day_of_week($some_day);
    
    $curr_day = $some_day;  
    if(($day_of_week == DOW_SAT) || ($day_of_week == DOW_SUN))
    {
      while($forever)
      {
        if($this->get_day_of_week($curr_day) == DOW_FRI)
        {
          return $this->fix_timestamp($curr_day, $cutoff_time);
        }
        $curr_day = $this->get_previous_day($curr_day);
      }
    }
    $curr_day = $this->get_previous_day($curr_day);
    return $this->fix_timestamp($curr_day, $cutoff_time);
  }

  public function get_session_expiration()
  {
    $exp = new DateTime();
    $exp->setTimezone(new DateTimeZone(LOCAL_TIMEZONE));
    $exp->modify('+ 1 hour'); 
    return $exp->format(DATETIME_FORMAT);
  }
}
?>
