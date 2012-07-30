<?php

include_once('dbconfig.php');

    function dbInsert($table, $link, $listfields, $listvalues, $close=false)
	{	  
      global $dbconfig;
 	  $query = sprintf("insert into %s (%s) VALUES (%s) ", $table, $listfields, $listvalues);
      mysql_select_db($dbconfig['databasename'], $link);
      $result = mysql_query($query);
      //debug
	  if (!$result) {
         $message = '<!- ';
         $message  .= 'Invalid query: ' . mysql_error() . "\n";
         $message .= 'Whole query: ' . $query;
		 $message .= ' -->' . "\n";
         echo $message;
		 return -255; //error
      } else
	  {
	    echo "<!- insert: " . enumRows(mysql_affected_rows()) . " updated -->\n";
		$locid = mysql_insert_id();
	  }
	  if($close==true)
	  {
	     mysql_close($link);
		 echo "<!-- database connection closed: $table -->";
	  }
      return $locid;
	}
	
	function enumRows($num) {
	$rowsAffected = $num;
	  if ($num < 1) return "no rows ";
	  if ($num == 1) return "1 row ";
	  return " $num rows ";
	}

    function sendEmail($f, $l, $e, $confNum, $citymail="registration@americasbeautypageants.com")
	{
	   global $pageantYear;
	   $CEmail = $citymail;
	   
	    $to = $e;
		$subject = $pageantYear . " Pageant Registration";
		include('registrationmessage.php');
		
		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		// Additional headers
		$headers .= "To: $f $l <$e>\r\n";
		$headers .= "From: " . $pageantYear . " Nationals Pageant <$CEmail>\r\n";
		
		// Mail it
		$debug['success'] = mail($to, $subject, $regmessage, $headers);
		$debug['to'] = "TO: $to";
		$debug['subject'] = "SUBJECT: $subject";
		$debug['msg'] = "MSG: $regmessage";
		$debug['headers'] = "HEADERS: $headers";
		$debug['cityEmail'] = "CITYEMAIL: $CEmail";
                print "<!-- " . $debug['cityEmail'] . " -->\n\n";
				//print $debug['success'] . "\n";
				/*  */

	}	
?>