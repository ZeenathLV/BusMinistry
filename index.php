<?php
include_once("constants.php");
$phone = "NOT_SET";
if ($_REQUEST)
{
  $phone = $_REQUEST['From'];
  $message = $_REQUEST['Body'];
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Rock Church Bus Ministry</title>
	
  <style type="text/css">
  body {
		font-family:arial,helvetica,sans-serif;
		font-size:12px;
	}
  #wrapper {
		width:900px;
		margin:0px auto;
		border:1px solid #bbb;
		padding:10px;
	}
  #header {
		border:1px solid #bbb;
		height:80px;
		padding:10px;
	}
	#content {
		margin-top:10px;
		padding-bottom:10px;
	}
	/* applies to all divs within the content div */
  #content div {
		padding:10px;
		border:1px solid #bbb;
    float:left;
	}
  #content-left {
  	width:200px;
  	height:300px;
	}
  #content-main {
		margin-left:10px;
		width:666px;
		height:150px;
	}
  #content-box1, #content-box2, #content-box3 {
		padding:10px;
		border:1px solid #bbb;
    position:absolute;
    margin-top:10px;
    height:300px;
	}
  #content-box1 {
		margin-left:232px;
		width:150px;
	}
  #content-box2 {
		margin-left:415px;
		width:275px;
	}
  #content-box3 {
		margin-left:725px;
		width:152px;
	}
  #footer {
		float:left;
		margin-top:10px;
		margin-bottom:10px;
    padding:10px;
    border:1px solid #bbb;
    width:878px;
	}
  #bottom {
		clear:both;
		text-align:right;
	}
  table {
      width: 100%;
  }
  thead, tbody, tr, td, th { 
    display: block; 
  }
  tr:after {
      content: ' ';
      display: block;
      visibility: hidden;
      clear: both;
  }
  thead th {
      height: 30px;

      /*text-align: left;*/
  }
  tbody {
      height: 265px;
      overflow-y: auto;
      overflow-x: hidden;
  }
  thead {
      /* fallback */
  }
  tbody td, thead th {
      width: 100%;
      float: left;
  }
  td { border-bottom: 1px solid #000; }
  #message { padding: 3px; }
  </style>
  <script type="text/javascript">
<!-- 
function submitenter(myfield,e) 
{ 
  var keycode; 
  if (window.event) keycode = window.event.keyCode; 
  else if (e) keycode = e.which; 
  else return true;
   
  if (keycode == 13) 
  { 
    myfield.form.submit(); 
    return false; 
  } 
  else return true; 
} 
//--> 
</SCRIPT>
</head>
<body>

<div id="wrapper">
	<div id="header"><h1>Rock Church Bus Ministry</h1></div>
	<div id="content-box1">
    <!--Begin Table-->
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Conversation:</th>
        </tr>
        </thead>
        <tbody id="scroll_table">
<?php
if($phone == "NOT_SET")
{
  echo "        <tr>\n";
  echo "            <td class=\"filterable-cell\">\n";
  echo "              No phone number.\n";             
  echo "            </td>\n";
  echo "        </tr>\n";
}
else
{
  try
  {
    $rider = new BusRider(false);
    
    $rider->load_rider($phone, $message);
    
    if($rider->has_error())
       throw new Exception($rider->get_error());
    
    $rider->process_message();

    if($rider->has_error())
       throw new Exception($rider->get_error());
    
    echo $rider->get_log_display();
  }
  catch(Exception $e)
  {
    echo "        <tr>\n";
    echo "            <td class=\"filterable-cell\">\n";
    echo "              Error: " . $e->getMessage() . ".\n";             
    echo "            </td>\n";
    echo "        </tr>\n";
  }
}
?>             
        </tbody>
    </table>
    <!--End Table-->
  </div>
	<div id="content-box2">
    <h3>Send Message:</h3>
    <form method="GET" action="" >
      <select name="From">
        <option value="7026124216" <?php if ($phone == "7026124216") echo ' selected '; ?> >Dad's Phone</option>
        <option value="7022793038" <?php if ($phone == "7022793038") echo ' selected '; ?> >Mom's Phone</option>
        <option value="7027556684" <?php if ($phone == "7027556684") echo ' selected '; ?> >Maddy's Phone</option>
        <option value="3108017973" <?php if ($phone == "3108017973") echo ' selected '; ?> >Jen's Phone</option>
        <option value="9098004619" <?php if ($phone == "9098004619") echo ' selected '; ?> >Michael's Phone</option>
        <option value="9512081896" <?php if ($phone == "9512081896") echo ' selected '; ?> >Family's Phone (Blocked)</option>
      </select>
      <br>Message:<br>
      <INPUT NAME="Body"  SIZE=25
           onKeyPress="return submitenter(this,event)" autofocus><BR>
      <br>
      <input type="submit" value="Send Message" >
    </form>
    <br><b>Non-Standard Commands:</b>
    <br>RESET_TABLES...Resets the tables
    <br>REGISTER_ME...Simulates registering the phone number
    <br>RIDE...Start a new session. Cannot be used once session is started.
  </div>
	<div id="content-box3"><p>Box 3</p></div>
	<div id="content">
		<div id="content-left">Left Box</div>
	</div>
	<div id="footer">Footer Box</div>
	<div id="bottom">Bottom Box</div>
</div></body>
</html>
