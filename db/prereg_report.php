<?php

	require_once( "SQLDB.php" );
	require_once( "dbconfig.php" );
	include("reportfuncs.php");

	define(REPORTS_PASSWORD, "CONFETTI");    	
	
	$SERVERTIMEDIFFERENCE = -7;

	define("DELIMITER", ",");
	define(USEEXCEL, false);
//	define(DEBUG, true);  //rem this out for production
	$onlyRetrieved = ""; //" AND retrieved = 0";
	
	// getCityList( )
	//
	// Queries the database and returns a distinct array of all the cities
	// contained in the preregister table.
	function getCityList( )
	{
		// Include the database config variables
		global $dbconfig;
		
		// Create an instance of SQLDB for MySQL access
		$db = new SQLDB( $dbconfig[ "domain" ], $dbconfig[ "databasename" ], $dbconfig[ "user" ], $dbconfig[ "pass" ] );

		// Get a list of all the cities in the preregister table		
		$db->query( "SELECT pageant_city FROM preregister WHERE 1");

		// Create an array to store them
		$arrCities = array( );

		// Go thru each result (city)
		for( $x = 0; $x < $db->getNumRows(); $x++ )
		{
			$city = $db->getResult( $x, 0 );

			// Check to make sure it doesn't already exist
			if( !in_array( $city, $arrCities ) )
				// Add the city to the array
				array_push( $arrCities, $city );
		}
				
		// Return the array of distinct cities
		return $arrCities;
	}
	
	// getCityCounts( )
	//
	// Queries the database and returns an associative array of all the cities
	// and the number of records for each
	function getCityCounts()
	{
		// Include the database config variables
		global $dbconfig;
		
		// Create an instance of SQLDB for MySQL access
		$db = new SQLDB( $dbconfig[ "domain" ], $dbconfig[ "databasename" ], $dbconfig[ "user" ], $dbconfig[ "pass" ] );

		// Get a list of all the cities in the preregister table		
		$db->query( "SELECT pageant_city FROM preregister WHERE 1" );

		// Create an array to store them
		$arrCities = array();

		$cy = new SQLDB( $dbconfig[ "domain" ], $dbconfig[ "databasename" ], $dbconfig[ "user" ], $dbconfig[ "pass" ] );
		// Go thru each result (city)
		for( $x = 0; $x < $db->getNumRows(); $x++ )
		{
			$city = $db->getResult( $x, 0 );

			// Check to make sure it doesn't already exist
			if( !in_array( $city, $arrCities ) )
			{
				$q = $cy->query( "SELECT pageant_city, COUNT(*) FROM preregister GROUP BY pageant_city ORDER BY date" );
				while( $r = mysql_fetch_row( $q ) )
					$retArray[$r[0]] = $r[1];
				array_push( $arrCities, $city );
			}
		}
		return $retArray;
	}
	
	//has there been activity in the last week?
	//return a boolean indicating whether the most recent record is dated less than one week ago
	function recentActivity($cityName)
	{
		/*
		global $dbconfig;
		$db = new SQLDB( $dbconfig[ "domain" ], $dbconfig[ "databasename" ], $dbconfig[ "user" ], $dbconfig[ "pass" ] );
		$db->query( "CURDATE() > DATE_ADD(SELECT last(`date_entered`) FROM preregister, INTERVAL 7 DAYS)" );
		echo($db->getResult());
		*/
		return true;  //temporary stub
	}
	
 /* ************************* END COMMON REPORT FUNCTIONS ****************************** */
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Nationals, Incorporated City PreRegister Report</title>
</head>
<style>
  form { margin:2em; }
  #city { padding-bottom:12px; color:#0033FF; }
  #counts { padding:8px; width:24em; background-color:#ffc;
  border:solid thin #3366CC;
  }
  .current { margin-left:1em; }
  .old { color:#CCCCCC; padding-left:0; }
  #output { background-color:#f0f0f0;padding:0.5em; height:550px; width:90%; overflow:scroll; }
  #recordcount { position:absolute; left:55%; top:30px;
                 font-weight:bold;border:2px solid #FF0000; background-color:#0000FF;color:white;padding:0.5em; width:12em; }
  .citydetailslink, .citydetailslink:visited { text-decoration:none; color:red; font-weight: bold; }
  .citydetailslink:hover { font-weight:bolder; color:green; }
</style>
<body>
<?php
  $password = $_POST['pwd'];
  if (8 == strlen($_GET['auth']))  //primitive authentication by sending an 8 digit token
  {
?>

<? /*********************** DETAIL REPORT ************************/ ?>
<h1><?php echo $city; ?> Detail Report</h1>
<h6 style="text-align:right; color:#dddddd;">created by Thom Parkin</h6>
<?php

	$city = ($_POST[ "city" ]) ? $_POST['city'] : $_GET['city'];		
	
	// Include the database config variables
	global $dbconfig;
	// Create an instance of SQLDB for MySQL access
	$db = new SQLDB( $dbconfig[ "domain" ], $dbconfig[ "databasename" ], $dbconfig[ "user" ], $dbconfig[ "pass" ] );
	// Get a list of all the cities in the preregister table		
	$db->query( "SELECT * FROM preregister WHERE pageant_city = '$city'" . $onlyRetrieved);

        $rptHeader = "<p style=\"color:#ff0000;font-weight:bold;\" onclick=\"copyText();\">Copy and paste this list into Excel</p>\n\r<p style=\"font-weight:bold;font-size:12pt;\">";
        /* Header Titles */
 	$rptHeader .= "City Code,City Name,First Name,Last Name,Address,Address,City,State,Zip,Phone,Email,Parent Name,Parent E-mail,Hotel,Date,Time,Hotel-Date-Time,<span title=\"Eastern Standard Time\">Pre-Reg Date</span>&nbsp;<span style=\"font-size:50%;\">EST</span>";
	$rtpHeader .= "</p>\n\r";		


        print $rptHeader;
	print '<div id="output">';
			
	$y = 1;
	$arrRetrieved = array( );
			
	while( $row = $db->getArray(MYSQL_ASSOC) )
	{
		array_push( $arrRetrieved, $row[ "entry_id" ] );  //array used later for UPDATE SQL command
                array_walk_recursive($row, "nocomma");
                //ereg_replace to remove the entry_id and first comma
                echo ereg_replace("^[0-9]+,", "<span class='record'>", implode(DELIMITER, $row)) . "</span>";
		echo "<br />";
		$y++;
	}
        print '</div>';
	print '<p id="recordcount">' . count($arrRetrieved) . ' records retrieved.</p>'; //marked as viewed.</p>';

	//DEPRECATED ####
	$query = "UPDATE preregister SET retrieved = '1' WHERE entry_id IN(";
	for( $x = 0; $x < count( $arrRetrieved ); $x++ )
		$query .= sprintf( "%d%s", $arrRetrieved[ $x ], ( $x + 1 < count( $arrRetrieved ) ) ? "," : "" );
	$query .= ")";
	if(!defined(DEBUG)) $db->query( $query );
  }
  else
  {
	  if(strtoupper($password)==REPORTS_PASSWORD)
	  { 
	?>
	<!--
			<form action="<?php echo $_SERVER[ "PHP_SELF" ]; ?>" method="post">
			<input type="hidden" name="report" id="report" />
				Please select your city:<br>
				<select id="city" name="city">
					<?php
						// Build the drop-down options from the list of cities
						foreach( getCityList( ) as $city )
							printf( "<option value=\"%s\">%s\n", $city, $city );
					?>
				</select>
				&nbsp;&nbsp;&nbsp;
				<input type="submit" name="submit" value="Report" />
			</form>
	<hr width="100%" />
	-->
	<?php
	  print '<div id="counts" ><h3>TOTALS<span style="font-size:70%;"> as of ' . date("M d, Y") . '</span></h3>';
	  foreach(getCityCounts() as $city => $cityCount)
	    printf("<span class=\"%s\">&nbsp;&nbsp;<a class=\"citydetailslink\" href=\"{$_SERVER['PHP_SELF']}?auth=12345678&city=%s\" target=\"detail_report\" title=\"Details for %s\">%s</a>: %s </span><br />", (recentActivity($city)) ? "current" : "old", $city, $city, $city, $cityCount);
	  print '</div>';	
	  } else {
	  ?>
		<h2>Nationals PreRegistration Reports</h2>
		<form action="<?php echo $_SERVER[ "PHP_SELF" ]; ?>" method="post">
	        <label for="pwd">Enter Password: </label><input type="password" name="pwd" id="pwd" />
			<input type="submit" name="submit" value="ENTER" />
	        </form>
	        <script language="javascript" type="text/javascript">
			var defaultField = document.getElementById("pwd");
			defaultField.focus();
			</script>
	<?php
	  }
	 }
?>
</body>
</html>