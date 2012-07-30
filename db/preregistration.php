<script type="text/javascript">
	function delayRedirect(delay, url)
	{
		var delaySec = delay;
		setTimeout("window.location.href='"+url+"'",delaySec);
	}
</script>

<?php
include('dbfuncs.php');

$dbsuccess = "../registrationmessage.php";
$dberror = "../dberror.php";

$city_code = $_POST['city_code'];
$pageant_city = $_POST['pageant_city'];  //info_session city
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$address1 = $_POST['address1'];
$address2 = $_POST['address2'];
$city = $_POST['city'];
$state = $_POST['state'];
$zip = $_POST['zip'];
$parent = $_POST['parent']; //parent name
$email = $_POST['email'];
$parent_email = $_POST['parent_email'];
$phone = $_POST['phone'];
$hotel_name = $_POST['hotel_name'];
$date = $_POST['date'];  //info_session date
$time = $_POST['time'];  //info_session time
$event_detail = $_POST['event_detail'];
$prereg_code = $_POST['prereg_code'];  //pseudo-random code with prefix
$lead_source = $_POST['lead_source'];  //how did you hear about us

	  $connection = dbConnect();
	
	  $id = dbInsert("preregister", $connection, "city_code, pageant_city, fname, lname, address1, address2, city, state, zip, parent, email, parent_email, phone, hotel_name, date, time, event_detail, prereg_code, lead_source","'$city_code', '$pageant_city', '$fname', '$lname', '$address1', '$address2', '$city', '$state', '$zip', '$parent', '$email', '$parent_email', '$phone', '$hotel_name', '$date', '$time', '$event_detail', '$prereg_code', '$lead_source'", true);

	  	if ( $id > 0) { 
	  		
	  		if ($city_code == "00") {
	  			//Dallas
			    ?>
			  		<form method=post action="http://app.icontact.com/icp/signup.php" name="icpsignup" id="icpsignup845" accept-charset="UTF-8">
						<input type=hidden name=redirect value="http://americasbeautypageants.com/thank-you-for-registering" />
						<input type=hidden name=errorredirect value="http://americasbeautypageants.com/registration-error" />
						
						<center>
							<img src="ajax-loader.gif">
							<h3>Completing registration...</h3>
						</center>
						
						<div id="SignUp">
						    <input type=hidden name="fields_email" value="<?php echo $email ?>">
						    <input type=hidden name="fields_fname" value="<?php echo $fname ?>">
						    <input type=hidden name="fields_lname" value="<?php echo $lname ?>">
						    <input type=hidden name="fields_address1" value="<?php echo $address1 ?>">
						    <input type=hidden name="fields_address2" value="<?php echo $address2 ?>">
						    <input type=hidden name="fields_city" value="<?php echo $city ?>">
						    <input type=hidden name="fields_state" value="<?php echo $state ?>">
						    <input type=hidden name="fields_zip" value="<?php echo $zip ?>">
						    <input type=hidden name="fields_session_time" value="<?php echo $time ?>">
						</div>
					    
					    <input type=hidden name="listid" value="8196">
					    <input type=hidden name="specialid:8196" value="5BJJ">
					    <input type=hidden name=clientid value="856805">
					    <input type=hidden name=formid value="845">
					    <input type=hidden name=reallistid value="1">
					    <input type=hidden name=doubleopt value="0">
					    <input type="submit" name="Submit" value="Submit" style="display:none;">
					</form>
		
			  		<center><h1>Submitting Dallas, TX Registration...</h1></center>
			  		<script type="text/javascript">
			  			document.forms["icpsignup845"].submit();
					</script>
				<?php
			} elseif ($city_code == "01") {
				// Long Island
			    ?>
			  		<form method=post action="http://app.icontact.com/icp/signup.php" name="icpsignup" id="icpsignup" accept-charset="UTF-8">
						<input type=hidden name=redirect value="http://americasbeautypageants.com/thank-you-for-registering-long-island" />
						<input type=hidden name=errorredirect value="http://americasbeautypageants.com/registration-error" />
						
						<center>
							<img src="ajax-loader.gif">
							<h3>Completing registration...</h3>
						</center>
						
						<div id="SignUp">
						    <input type=hidden name="fields_email" value="<?php echo $email ?>">
						    <input type=hidden name="fields_fname" value="<?php echo $fname ?>">
						    <input type=hidden name="fields_lname" value="<?php echo $lname ?>">
						    <input type=hidden name="fields_address1" value="<?php echo $address1 ?>">
						    <input type=hidden name="fields_address2" value="<?php echo $address2 ?>">
						    <input type=hidden name="fields_city" value="<?php echo $city ?>">
						    <input type=hidden name="fields_state" value="<?php echo $state ?>">
						    <input type=hidden name="fields_zip" value="<?php echo $zip ?>">
						    <input type=hidden name="fields_session_time" value="<?php echo $time ?>">
						</div>
					    
					    <input type=hidden name="listid" value="12797">
					   	<input type=hidden name="specialid:12797" value="KICX">
					    <input type=hidden name=clientid value="856805">
					    <input type=hidden name=formid value="1419">
					    <input type=hidden name=reallistid value="1">
					    <input type=hidden name=doubleopt value="0">
					    <input type="submit" name="Submit" value="Submit" style="display:none;">
					</form>
		
					<center><h1>Submitting Long Island, NY Registration...</h1></center>
			  		<script type="text/javascript">
			  			document.forms["icpsignup"].submit();
					</script>
				<?php
			} elseif ($city_code == "02") {
				// New York
			    ?>
			  		<form method=post action="http://app.icontact.com/icp/signup.php" name="icpsignup" id="icpsignup" accept-charset="UTF-8">
						<input type=hidden name=redirect value="http://americasbeautypageants.com/thank-you-for-registering-new-york" />
						<input type=hidden name=errorredirect value="http://americasbeautypageants.com/registration-error" />
						
						<center>
							<img src="ajax-loader.gif">
							<h3>Completing registration...</h3>
						</center>
						
						<div id="SignUp">
						    <input type=hidden name="fields_email" value="<?php echo $email ?>">
						    <input type=hidden name="fields_fname" value="<?php echo $fname ?>">
						    <input type=hidden name="fields_lname" value="<?php echo $lname ?>">
						    <input type=hidden name="fields_address1" value="<?php echo $address1 ?>">
						    <input type=hidden name="fields_address2" value="<?php echo $address2 ?>">
						    <input type=hidden name="fields_city" value="<?php echo $city ?>">
						    <input type=hidden name="fields_state" value="<?php echo $state ?>">
						    <input type=hidden name="fields_zip" value="<?php echo $zip ?>">
						    <input type=hidden name="fields_session_time" value="<?php echo $time ?>">
						</div>
					    
					   	<input type=hidden name="listid" value="12888">
					    <input type=hidden name="specialid:12888" value="TZG6">
					    <input type=hidden name=clientid value="856805">
					    <input type=hidden name=formid value="1430">
					    <input type=hidden name=reallistid value="1">
					    <input type=hidden name=doubleopt value="0">
					    <input type="submit" name="Submit" value="Submit" style="display:none;">
					</form>
		
					<center><h1>Submitting New York Registration...</h1></center>
			  		<script type="text/javascript">
			  			document.forms["icpsignup"].submit();
					</script>
				<?php
			} elseif ($city_code == "03") {
				// Ft. Lauderdale
			    ?>
			  		<form method=post action="http://app.icontact.com/icp/signup.php" name="icpsignup" id="icpsignup" accept-charset="UTF-8">
						<input type=hidden name=redirect value="http://americasbeautypageants.com/thank-you-for-registering-ft-lauderdale" />
						<input type=hidden name=errorredirect value="http://americasbeautypageants.com/registration-error" />
						
						<center>
							<img src="ajax-loader.gif">
							<h3>Completing registration...</h3>
						</center>
						
						<div id="SignUp">
						    <input type=hidden name="fields_email" value="<?php echo $email ?>">
						    <input type=hidden name="fields_fname" value="<?php echo $fname ?>">
						    <input type=hidden name="fields_lname" value="<?php echo $lname ?>">
						    <input type=hidden name="fields_address1" value="<?php echo $address1 ?>">
						    <input type=hidden name="fields_address2" value="<?php echo $address2 ?>">
						    <input type=hidden name="fields_city" value="<?php echo $city ?>">
						    <input type=hidden name="fields_state" value="<?php echo $state ?>">
						    <input type=hidden name="fields_zip" value="<?php echo $zip ?>">
						    <input type=hidden name="fields_session_time" value="<?php echo $time ?>">
						</div>
					    
					    <input type=hidden name="listid" value="12889">
					    <input type=hidden name="specialid:12889" value="X3M8">
					    <input type=hidden name=clientid value="856805">
					    <input type=hidden name=formid value="1431">
					    <input type=hidden name=reallistid value="1">
					    <input type=hidden name=doubleopt value="0">
					    <input type="submit" name="Submit" value="Submit" style="display:none;">
					</form>
		
					<center><h1>Submitting Ft. Lauderdale Registration...</h1></center>
			  		<script type="text/javascript">
			  			document.forms["icpsignup"].submit();
					</script>
				<?php
			} elseif ($city_code == "04") {
				// West Palm Beach
			    ?>
			  		<form method=post action="http://app.icontact.com/icp/signup.php" name="icpsignup" id="icpsignup" accept-charset="UTF-8">
						<input type=hidden name=redirect value="http://americasbeautypageants.com/thank-you-for-registering-west-palm-beach" />
						<input type=hidden name=errorredirect value="http://americasbeautypageants.com/registration-error" />
						
						<center>
							<img src="ajax-loader.gif">
							<h3>Completing registration...</h3>
						</center>
						
						<div id="SignUp">
						    <input type=hidden name="fields_email" value="<?php echo $email ?>">
						    <input type=hidden name="fields_fname" value="<?php echo $fname ?>">
						    <input type=hidden name="fields_lname" value="<?php echo $lname ?>">
						    <input type=hidden name="fields_address1" value="<?php echo $address1 ?>">
						    <input type=hidden name="fields_address2" value="<?php echo $address2 ?>">
						    <input type=hidden name="fields_city" value="<?php echo $city ?>">
						    <input type=hidden name="fields_state" value="<?php echo $state ?>">
						    <input type=hidden name="fields_zip" value="<?php echo $zip ?>">
						    <input type=hidden name="fields_session_time" value="<?php echo $time ?>">
						</div>
					    
					    <input type=hidden name="listid" value="12891">
					    <input type=hidden name="specialid:12891" value="BMPE">
					    <input type=hidden name=clientid value="856805">
					    <input type=hidden name=formid value="1433">
					    <input type=hidden name=reallistid value="1">
					    <input type=hidden name=doubleopt value="0">
					    <input type="submit" name="Submit" value="Submit" style="display:none;">
					</form>
		
					<center><h1>Submitting West Palm Beach Registration...</h1></center>
			  		<script type="text/javascript">
			  			document.forms["icpsignup"].submit();
					</script>
				<?php
			} elseif ($city_code == "05") {
				// Miami
			    ?>
			  		<form method=post action="http://app.icontact.com/icp/signup.php" name="icpsignup" id="icpsignup" accept-charset="UTF-8">
						<input type=hidden name=redirect value="http://americasbeautypageants.com/thank-you-for-registering-miami" />
						<input type=hidden name=errorredirect value="http://americasbeautypageants.com/registration-error" />
						
						<center>
							<img src="ajax-loader.gif">
							<h3>Completing registration...</h3>
						</center>
						
						<div id="SignUp">
						    <input type=hidden name="fields_email" value="<?php echo $email ?>">
						    <input type=hidden name="fields_fname" value="<?php echo $fname ?>">
						    <input type=hidden name="fields_lname" value="<?php echo $lname ?>">
						    <input type=hidden name="fields_address1" value="<?php echo $address1 ?>">
						    <input type=hidden name="fields_address2" value="<?php echo $address2 ?>">
						    <input type=hidden name="fields_city" value="<?php echo $city ?>">
						    <input type=hidden name="fields_state" value="<?php echo $state ?>">
						    <input type=hidden name="fields_zip" value="<?php echo $zip ?>">
						    <input type=hidden name="fields_session_time" value="<?php echo $time ?>">
						</div>
					    
					    <input type=hidden name="listid" value="12892">
					    <input type=hidden name="specialid:12892" value="W2CV">
					    <input type=hidden name=clientid value="856805">
					    <input type=hidden name=formid value="1434">
					    <input type=hidden name=reallistid value="1">
					    <input type=hidden name=doubleopt value="0">
					    <input type="submit" name="Submit" value="Submit" style="display:none;">
					</form>
		
					<center><h1>Submitting Miami Registration...</h1></center>
			  		<script type="text/javascript">
			  			document.forms["icpsignup"].submit();
					</script>
				<?php
			} elseif ($city_code == "06") {
				// Baltimore
			    ?>
			  		<form method=post action="http://app.icontact.com/icp/signup.php" name="icpsignup" id="icpsignup" accept-charset="UTF-8">
						<input type=hidden name=redirect value="http://americasbeautypageants.com/thank-you-for-registering-baltimore" />
						<input type=hidden name=errorredirect value="http://americasbeautypageants.com/registration-error" />
						
						<center>
							<img src="ajax-loader.gif">
							<h3>Completing registration...</h3>
						</center>
						
						<div id="SignUp">
						    <input type=hidden name="fields_email" value="<?php echo $email ?>">
						    <input type=hidden name="fields_fname" value="<?php echo $fname ?>">
						    <input type=hidden name="fields_lname" value="<?php echo $lname ?>">
						    <input type=hidden name="fields_address1" value="<?php echo $address1 ?>">
						    <input type=hidden name="fields_address2" value="<?php echo $address2 ?>">
						    <input type=hidden name="fields_city" value="<?php echo $city ?>">
						    <input type=hidden name="fields_state" value="<?php echo $state ?>">
						    <input type=hidden name="fields_zip" value="<?php echo $zip ?>">
						    <input type=hidden name="fields_session_time" value="<?php echo $time ?>">
						</div>
					    
					    <input type=hidden name="listid" value="22816">
					    <input type=hidden name="specialid:22816" value="CU3N">
					    <input type=hidden name=clientid value="856805">
					    <input type=hidden name=formid value="2808">
					    <input type=hidden name=reallistid value="1">
					    <input type=hidden name=doubleopt value="0">
					    <input type="submit" name="Submit" value="Submit" style="display:none;">
					</form>
		
					<center><h1>Submitting Registration...</h1></center>
			  		<script type="text/javascript">
			  			document.forms["icpsignup"].submit();
					</script>
				<?php
			} elseif ($city_code == "07") {
				// Washington, DC
			    ?>
			  		<form method=post action="http://app.icontact.com/icp/signup.php" name="icpsignup" id="icpsignup" accept-charset="UTF-8">
						<input type=hidden name=redirect value="http://americasbeautypageants.com/thank-you-for-registering-washington-dc" />
						<input type=hidden name=errorredirect value="http://americasbeautypageants.com/registration-error" />
						
						<center>
							<img src="ajax-loader.gif">
							<h3>Completing registration...</h3>
						</center>
						
						<div id="SignUp">
						    <input type=hidden name="fields_email" value="<?php echo $email ?>">
						    <input type=hidden name="fields_fname" value="<?php echo $fname ?>">
						    <input type=hidden name="fields_lname" value="<?php echo $lname ?>">
						    <input type=hidden name="fields_address1" value="<?php echo $address1 ?>">
						    <input type=hidden name="fields_address2" value="<?php echo $address2 ?>">
						    <input type=hidden name="fields_city" value="<?php echo $city ?>">
						    <input type=hidden name="fields_state" value="<?php echo $state ?>">
						    <input type=hidden name="fields_zip" value="<?php echo $zip ?>">
						    <input type=hidden name="fields_session_time" value="<?php echo $time ?>">
						</div>
					    
					    <input type=hidden name="listid" value="22817">
					    <input type=hidden name="specialid:22817" value="4IUM">
					    <input type=hidden name=clientid value="856805">
					    <input type=hidden name=formid value="2809">
					    <input type=hidden name=reallistid value="1">
					    <input type=hidden name=doubleopt value="0">
					</form>
		
					<center><h1>Submitting Registration...</h1></center>
			  		<script type="text/javascript">
			  			document.forms["icpsignup"].submit();
					</script>
				<?php
			} elseif ($city_code == "08") {
				// Chicago, North
			    ?>
			  		<form method=post action="http://app.icontact.com/icp/signup.php" name="icpsignup" id="icpsignup" accept-charset="UTF-8">
						<input type=hidden name=redirect value="http://americasbeautypageants.com/thank-you-for-registering-chicago-north" />
						<input type=hidden name=errorredirect value="http://americasbeautypageants.com/registration-error" />
						
						<center>
							<img src="ajax-loader.gif">
							<h3>Completing registration...</h3>
						</center>
						
						<div id="SignUp">
						    <input type=hidden name="fields_email" value="<?php echo $email ?>">
						    <input type=hidden name="fields_fname" value="<?php echo $fname ?>">
						    <input type=hidden name="fields_lname" value="<?php echo $lname ?>">
						    <input type=hidden name="fields_address1" value="<?php echo $address1 ?>">
						    <input type=hidden name="fields_address2" value="<?php echo $address2 ?>">
						    <input type=hidden name="fields_city" value="<?php echo $city ?>">
						    <input type=hidden name="fields_state" value="<?php echo $state ?>">
						    <input type=hidden name="fields_zip" value="<?php echo $zip ?>">
						    <input type=hidden name="fields_session_time" value="<?php echo $time ?>">
						</div>
					    
					   	<input type=hidden name="listid" value="22953">
					    <input type=hidden name="specialid:22953" value="LAIG">
					    <input type=hidden name=clientid value="856805">
					    <input type=hidden name=formid value="2852">
					    <input type=hidden name=reallistid value="1">
						<input type=hidden name=doubleopt value="0">
					</form>
		
					<center><h1>Submitting Registration...</h1></center>
			  		<script type="text/javascript">
			  			document.forms["icpsignup"].submit();
					</script>
				<?php
			} elseif ($city_code == "09") {
				// Chicago, South
			    ?>
			  		<form method=post action="http://app.icontact.com/icp/signup.php" name="icpsignup" id="icpsignup" accept-charset="UTF-8">
						<input type=hidden name=redirect value="http://americasbeautypageants.com/thank-you-for-registering-chicago-south" />
						<input type=hidden name=errorredirect value="http://americasbeautypageants.com/registration-error" />
						
						<center>
							<img src="ajax-loader.gif">
							<h3>Completing registration...</h3>
						</center>
						
						<div id="SignUp">
						    <input type=hidden name="fields_email" value="<?php echo $email ?>">
						    <input type=hidden name="fields_fname" value="<?php echo $fname ?>">
						    <input type=hidden name="fields_lname" value="<?php echo $lname ?>">
						    <input type=hidden name="fields_address1" value="<?php echo $address1 ?>">
						    <input type=hidden name="fields_address2" value="<?php echo $address2 ?>">
						    <input type=hidden name="fields_city" value="<?php echo $city ?>">
						    <input type=hidden name="fields_state" value="<?php echo $state ?>">
						    <input type=hidden name="fields_zip" value="<?php echo $zip ?>">
						    <input type=hidden name="fields_session_time" value="<?php echo $time ?>">
						</div>
					    
					    <input type=hidden name="listid" value="22954">
					    <input type=hidden name="specialid:22954" value="UU6B">
					    <input type=hidden name=clientid value="856805">
					    <input type=hidden name=formid value="2853">
					    <input type=hidden name=reallistid value="1">
					    <input type=hidden name=doubleopt value="0">
					</form>
		
					<center><h1>Submitting Registration...</h1></center>
			  		<script type="text/javascript">
			  			document.forms["icpsignup"].submit();
					</script>
				<?php
			}
	  		
			
			//readfile($dbsuccess); //success
			//header('Location: http://www.google.com/');
			//include($dbsuccess); 
			//exit();
	  	} else { ?>
	  		<script type="text/javascript">
				delayRedirect(0,'registration-error');
			</script>
	  	<?php
			//readfile($dberror); //error
	  	}
?>

