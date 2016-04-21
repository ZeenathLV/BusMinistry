<?php
include_once("constants.php");
require_once(ROOT_DIR . "inc/database_tables.php");

if ($_REQUEST)
{
  $pdo = get_pdo_connection();
  
  $max = db_func_get_table_count();
  for($i = 0;$i < $max;$i++)
  {
    $tablename = db_func_get_table_name($i);
    try
    {
      if(isset($_REQUEST[$tablename]))
      {
        if(endsWith($tablename,"insert"))
        {
          /*    Do nothing   */
        }
        else if(endsWith($tablename,"_v"))
        {
          $sql = "DROP VIEW IF EXISTS " . $tablename;
          $cnt = $pdo->exec($sql);
        }
        else
        {
          $sql = "DROP TABLE IF EXISTS " . $tablename;
          $cnt = $pdo->exec($sql);
        }
        $cnt = $pdo->exec(db_func_get_table_query($i));
        
        db_func_set_table_remarks($i, "Success");
      }
    }
    catch(Exception $e)
    {
      db_func_set_table_remarks($i, "Error: " . $e->getMessage());
    }
  }
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

  #content-box1, #content-box2, #content-box3 {
		padding:10px;
		border:1px solid #bbb;
    position:absolute;
    margin-top:10px;
    height:300px;
	}

  #content-box1 {
		margin-left:70px;
		width:736px;
	}

  #btn-execute { margin-top: 10px; }

  table.scroll {
      width: 716px; /* 140px * 5 column + 16px scrollbar width */
      border-spacing: 0;
      border: 2px solid black;
  }

  table.scroll tbody,
  table.scroll thead tr { display: block; }

  table.scroll tbody {
      height: 175px;
      overflow-y: auto;
      overflow-x: hidden;
  }

  table.scroll tbody td,
  table.scroll thead th {
      width: 300px;
      border-right: 1px solid black;
  }

  table.scroll thead th:first-child,
  table.scroll tbody td:first-child {
      width: 160px; 
  }
  
  table.scroll tbody td:last-child {
      width: 254px; 
  }

  table.scroll thead th:last-child {
      width: 268px; 
  }


  thead tr th { 
      height: 30px;
      line-height: 30px;
      /*text-align: left;*/
  }

  tbody { border-top: 2px solid black; }

  tbody td:last-child, thead th:last-child {
      border-right: none !important;
  }
  </style>
  <script type="text/javascript">
  function func_select_all() 
  {
    var chkbx_main = document.getElementById("select_all").checked;

    var checkbox_array = document.getElementsByClassName("chxbx");

    for (var i = 0; i < checkbox_array.length; i++ )
    {
      checkbox_array[i].checked = chkbx_main;     
    } 

    //alert("Is this thing working?");
  }
  </script>
</head>
<body>
<div id="wrapper">
  <div id="header"><h1>Rock Church Bus Ministry</h1></div>
	<div id="content-box1">
    <h2>Database Tables</h2>

<form method="get" action="database.php">
<table class="scroll">
    <thead>
        <tr>
            <th align="LEFT">
              <label>
                <input id='select_all' name='select_all' type='checkbox' onclick="func_select_all()" />
                &nbsp;&nbsp;Table Name
              </label>
            </th>
            <th>Description</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>

<?php      
$max = db_func_get_table_count();
$baseline = "      ";
for($i = 0;$i < $max;$i++)
{
  $table_name = db_func_get_table_name($i);
  echo $baseline . "<tr>\n";
  echo $baseline . "  <td>\n";
  echo $baseline . "    <label><input "
      . "class='chxbx'" 
      . "  name='" . $table_name 
      . "' id='" . $table_name 
      . "' type='checkbox' >" 
      . db_func_get_table_long_name($i) 
      . "</label>\n";
  echo $baseline . "  </td>\n";
  echo $baseline . "  <td>\n";
  echo $baseline . "    " . db_func_get_table_description($i) . "\n";
  echo $baseline . "  </td>\n";
  echo $baseline . "  <td>\n";
  echo $baseline . "    " . db_func_get_table_remarks($i) . "\n";
  echo $baseline . "  </td>\n";
  echo $baseline . "</tr>\n";
}
?>    
    </tbody>
</table>
<input id="btn-execute" type="submit" value="Execute" >
</form>
  </div>
</div>
</body>
</html>
