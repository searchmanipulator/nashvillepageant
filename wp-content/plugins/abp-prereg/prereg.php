<?php
/*
Plugin Name: ABP Pre-Registration Plugin for WordPress
Plugin URI: http://www.64clicks.com/wp/plugins/abp-prereg
Description: A WordPress plugin designed to facilitate the pre-registration for 2012 Miss Teen
Version: 0.2
Author: Levent Gurses
Author URI: http://64clicks.com
License: Private
*/

	global $abp_db_version;
	$abp_db_version = "1.0";
	
	// Start session
	if ( !session_id() ) {
		add_action( 'init', 'session_start' );
	}

	function abp_install () {
		global $wpdb;
   		global $abp_db_version;

		$table_name = $wpdb->prefix . "prereg"; 
	
		$sql = "CREATE TABLE " . $table_name . " (
	  		id mediumint(9) NOT NULL AUTO_INCREMENT,
	  		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	  		fname text NOT NULL,
			lname text NOT NULL,
			email text NOT NULL,
			rcode text NOT NULL,
			address text,
			address2 text,
			city text,
			state text,
			zip text,
			phone text,
			dob text,
	  		UNIQUE KEY id (id)
    	);";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	function abp_install_data() {
		global $wpdb;
		$table_name = $wpdb->prefix . "prereg";
		$welcome_fname = "Levent";
		$welcome_lname = "Gurses";
		$welcome_email = "levent.gurses@64clicks.com";
		$welcome_rcode = "FE45TY2";
		$welcome_address = "21165 Whitfield Pl.";
		$welcome_address2 = "Ste 201";
		$welcome_phone = "703-508-7934";
		$welcome_city = "Sterling";
		$welcome_state = "VA";
		$welcome_zip = "20165";
		$welcome_dob = "05/12/1974";

		$rows_affected = $wpdb->insert( $table_name, array( 'time' => current_time('mysql'),
				'fname' => $welcome_fname, 'lname' => $welcome_lname, 'email' => $welcome_email, 
				'rcode' => $welcome_rcode, 'address'  => $welcome_address, 'address2' => $welcome_address2,
				'city' => $welcome_city, 'state' => $welcome_state, 'zip' => $welcome_zip, 'phone' => $welcome_phone, 'dob' => $welcome_dob) );
	}
	
	function prereg_update_db_check() {
		global $abp_db_version;
		if (get_site_option('abp_db_version') != $abp_db_version) {
			abp_install();
		}
	}
	
	function prereg_form_markup() {

		$form_action = get_permalink();

		if ( ($_SESSION['prereg_form_success']) ) {
			//$prereg_form_success = '<p style="color: green"><strong>Thank you for pre regisering. We will contact you shortly to answer your questions.</strong></p>';
			$markup = '<h2>Thank You!</h2><blockquote style="color: green"><strong>Thank you for pre-regisering. We will contact you shortly to answer your questions.</strong></blockquote>';
			unset($_SESSION['prereg_form_success']);
			
		} else {

$markup .= <<<EOT

<div id="commentform" style="padding:60px 20px 20px 20px; height:600px; background: url('http://missteeninc.com/wp-content/themes/platformpro/core/images/tiara-girl.jpg') no-repeat scroll 0 0 transparent;">

	{$prereg_form_success}
     
   <form onsubmit="return validateForm(this);" action="{$form_action}" method="post" style="text-align: left">
	   <p><label for="fname" style="width:210px;float:left;font-weight:bold;color:#000;"><small>First Name (*):</small></label><input type="text" name="fname" id="fname" value="{$fname}" size="22" /></p>
	   <p><label for="lname" style="width:210px;float:left;font-weight:bold;color:#000;"><small>Last Name (*):</small></label><input type="text" name="lname" id="lname" value="{$lname}" size="22" /></p>
	   <p><label for="email" style="width:210px;float:left;font-weight:bold;color:#000;"><small>Your Contact E-mail (*):</small></label><input type="text" name="email" id="email" value="{$email}" size="32" /></p>
	   <p><label for="rcode" style="width:210px;float:left;font-weight:bold;color:#000;"><small>Pre-Registration Number (*):</small></label><input type="text" name="rcode" id="rcode" value="{$rcode}" size="22" /></p>
	   <p><label for="phone" style="width:210px;float:left;font-weight:bold;color:#000;"><small>Your Phone Number (*):</small></label><input type="text" name="phone" id="phone" value="{$phone}" size="22" /></p>
	   <p><label for="dob" style="width:210px;float:left;font-weight:bold;color:#000;"><small>Date of Birth (MM/DD/YYYY):</small></label><input type="text" name="dob" id="dob" value="{$dob}" size="22" /></p>

	   <p><label for="address" style="width:210px;float:left;font-weight:bold;color:#000;"><small>Address:</small></label><input type="text" name="address" id="address" value="{$address}" size="22" /></p>
	   <p><label for="address2" style="width:210px;float:left;font-weight:bold;color:#000;"><small>Address 2:</small></label><input type="text" name="address2" id="address2" value="{$address2}" size="22" /></p>
	   <p><label for="city" style="width:210px;float:left;font-weight:bold;color:#000;"><small>City:</small></label><input type="text" name="city" id="city" value="{$city}" size="22" /></p>
	   <p><label for="state" style="width:210px;float:left;font-weight:bold;color:#000;"><small>State:</small></label><input type="text" name="state" id="state" value="{$state}" size="22" /></p>
	   <p><label for="zip" style="width:210px;float:left;font-weight:bold;color:#000;"><small>Zip:</small></label><input type="text" name="zip" id="zip" value="{$zip}" size="22" /></p>
	   

	   <p style="margin:20px 200px;font-size:26px;"><input style="font-size:26px;font-weight:bold;color:#000;" name="send" type="submit" id="send" value="Pre-Register"/></p>
	   <input type="hidden" name="prereg_form_submitted" value="1">
   
   </form>
   
</div>

EOT;
		}
	return $markup;

	}
	
	add_shortcode('prereg_form', 'prereg_form_markup');
	
	
	function prereg_form_process() {
		global $wpdb;
		$table_name = $wpdb->prefix . "prereg";
		
		if ( !isset($_POST['prereg_form_submitted']) ) return;
	
		$fname  = ( isset($_POST['fname']) )  ? trim(strip_tags($_POST['fname'])) : null;
		$lname = ( isset($_POST['lname']) ) ? trim(strip_tags($_POST['lname'])) : null;
		$email   = ( isset($_POST['email']) )   ? trim(strip_tags($_POST['email'])) : null;
		$phone   = ( isset($_POST['phone']) )   ? trim(strip_tags($_POST['phone'])) : null;
		$dob   = ( isset($_POST['dob']) )   ? trim(strip_tags($_POST['dob'])) : null;
		$rcode = ( isset($_POST['rcode']) ) ? trim(strip_tags($_POST['rcode'])) : null;

		$address = ( isset($_POST['address']) ) ? trim(strip_tags($_POST['address'])) : null;
		$address2 = ( isset($_POST['address2']) ) ? trim(strip_tags($_POST['address2'])) : null;
		$city = ( isset($_POST['city']) ) ? trim(strip_tags($_POST['city'])) : null;
		$state = ( isset($_POST['state']) ) ? trim(strip_tags($_POST['state'])) : null;
		$zip = ( isset($_POST['zip']) ) ? trim(strip_tags($_POST['zip'])) : null;
	
		if ( $fname == '' ) wp_die('Error: please fill the required field (name).');
		if ( !is_email($email) ) wp_die('Error: please enter a valid email address.');
		if ( $phone == '') wp_die('Error: please enter a valid phone number.');
		if ( $lname == '' ) wp_die('Error: please fill the required field (lname).');
	
		$rows_affected = $wpdb->insert( $table_name, array( 'time' => current_time('mysql'), 'fname' => $fname, 'lname' => $lname, 
								'email' => $email, 'phone' => $phone, 'dob' => $dob, 'rcode' => $rcode, 'address' => $address, 
								'address2' => $address2, 'city' => $city, 'state' => $state, 'zip' => $zip) );
		
		$_SESSION['prereg_form_success'] = 1;
		
		?>
	  		<form method=post action="https://app.icontact.com/icp/signup.php" name="icpsignup" id="icpsignup3942" accept-charset="UTF-8">
				<input type="hidden" name="redirect" value="http://missteeninc.com/thank-you-for-pre-registering">
				<input type="hidden" name="errorredirect" value="http://missteeninc.com/registration-error">
				
				<div id="SignUp">
				    <input type=hidden name="fields_fname" value="<?php echo $fname ?>">
				    <input type=hidden name="fields_lname" value="<?php echo $lname ?>">
				    <input type=hidden name="fields_email" value="<?php echo $email ?>">
				    <input type=hidden name="fields_phone" value="<?php echo $phone ?>">
				    <input type=hidden name="fields_session_time" value="<?php echo $rcode ?>">
				</div>
			    
				<input type="hidden" name="listid" value="32726">
			    <input type="hidden" name="specialid:32726" value="NRGR">
			
			    <input type="hidden" name="clientid" value="856805">
			    <input type="hidden" name="formid" value="3942">
			    <input type="hidden" name="reallistid" value="1">
			    <input type="hidden" name="doubleopt" value="0">

			    <input type="submit" name="Submit" value="Submit" style="display:none;">
			</form>
			
			<center><h1>Submitting Pre-Registration Data...Please wait...</h1></center>

	  		<script type="text/javascript">
	  			document.forms["icpsignup3942"].submit();
			</script>
		<?php

		//header('Location: ' . $_SERVER['HTTP_REFERER']);
		exit();
	
	}
	
	add_action('init', 'prereg_form_process');
	
	function contact_form_js() { ?>
	
	<script type="text/javascript">
		function validateForm(form) {
		
			var errors = '';
			var regexpEmail = /\w{1,}[@][\w\-]{1,}([.]([\w\-]{1,})){1,3}$/;
				
			if (!form.fname.value) errors += "Error: Please fill the required field (First Name).\n";
			if (!form.lname.value) errors += "Error: Please fill the required field (Last Name).\n";
			if (!form.phone.value) errors += "Error: Please fill the required field (Phone Number).\n";
			if (!regexpEmail.test(form.email.value)) errors += "Error: Please enter a valid email address.\n";
			if (!form.rcode.value) errors += "Error: Please fill the required field (Registration Code).\n";
		
			if (errors != '') {
				alert(errors);
				return false;
			}
		return true;
		}
	</script>
	
	<?php }
	
	add_action('wp_head', 'contact_form_js');

	// action function for above hook
	function abp_prereg_pages() {
		add_users_page( 'ABP Preregistrations', 'ABP Pre-Registrations', 'manage_options', 'abpmenu', 'abp_preregs_page');
	}

	function abp_preregs_page() {
		include('abp_prereg_list.php');  
	}


	register_activation_hook(__FILE__,'abp_install');
	register_activation_hook(__FILE__,'abp_install_data');
	add_action('admin_menu', 'abp_prereg_pages');
	add_action('plugins_loaded', 'prereg_update_db_check');
?>