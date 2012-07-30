<?php
  /*
    Database Configuration Parameters
	This information drives dataConnect.class
    Created by Thom Parkin 2005
    * updated Feb. 2011
  */

  /* Global vars - really belong in their own file (to be include_once right here) */
  $pageantYear = "2011 ";  //<-- this needs to be globally available


  $dbconfig['user'] = "beauty_dbadmin";
  $dbconfig['domain'] = "localhost"; //"americasbeautypageants.com";
  $dbconfig['pass'] = "pr3Reg1sTer";
  $dbconfig['databasename'] = "beauty_preregistration";

  function dbConnect()
  {
    global $dbconfig;
    @$link = mysql_connect($dbconfig['domain'], $dbconfig['user'], $dbconfig['pass']);
        if ($link) {
          echo "<!- database connection established -->\n";
		  return $link;
	    } else /*return -1;*/ die('Could not connect: ' . mysql_error());
  }
?>