<?php
/*
Plugin Name: ABP Pre-Registration Plugin for WordPress
Plugin URI: http://www.64clicks.com/wp/plugins/abp-prereg
Description: A WordPress plugin designed to facilitate the pre-registration for the America's Beauty Pageants events
Version: 0.1
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

		$rows_affected = $wpdb->insert( $table_name, array( 'time' => current_time('mysql'), 'fname' => $welcome_fname, 'lname' => $welcome_lname, 'email' => $welcome_email, 'rcode' => $welcome_rcode ) );
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

<div id="commentform" style="padding:170px 20px 100px 20px; height:300px; background: url('http://americasbeautypageants.com/wp-content/themes/platformpro/core/images/tiara-girl.jpg') no-repeat scroll 0 0 transparent;">

	{$prereg_form_success}
     
   <form onsubmit="return validateForm(this);" action="{$form_action}" method="post" style="text-align: left">
	   <p><label for="fname" style="width:210px;float:left;font-weight:bold;"><small>First Name (*):</small></label><input type="text" name="fname" id="fname" value="{$fname}" size="22" /></p>
	   <p><label for="lname" style="width:210px;float:left;font-weight:bold;"><small>Last Name (*):</small></label><input type="text" name="lname" id="lname" value="{$lname}" size="22" /></p>
	   <p><label for="email" style="width:210px;float:left;font-weight:bold;"><small>Your Contact E-mail (*):</small></label><input type="text" name="email" id="email" value="{$email}" size="32" /></p>
	   <p><label for="rcode" style="width:210px;float:left;font-weight:bold;"><small>Pre-Registration Number (*):</small></label><input type="text" name="rcode" id="rcode" value="{$rcode}" size="22" /></p>
	   <p style="margin:20px 200px;font-size:26px;"><input style="font-size:26px;font-weight:bold;" name="send" type="submit" id="send" value="Pre-Register"/></p>
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
		$rcode = ( isset($_POST['rcode']) ) ? trim(strip_tags($_POST['rcode'])) : null;
	
		if ( $fname == '' ) wp_die('Error: please fill the required field (name).');
		if ( !is_email($email) ) wp_die('Error: please enter a valid email address.');
		if ( $lname == '' ) wp_die('Error: please fill the required field (lname).');
	
		$rows_affected = $wpdb->insert( $table_name, array( 'time' => current_time('mysql'), 'fname' => $fname, 'lname' => $lname, 'email' => $email, 'rcode' => $rcode ) );
		
		$_SESSION['prereg_form_success'] = 1;
		
		?>
	  		<form method=post action="https://app.icontact.com/icp/signup.php" name="icpsignup" id="icpsignup3942" accept-charset="UTF-8">
				<input type="hidden" name="redirect" value="http://americasbeautypageants.com/thank-you-for-pre-registering">
				<input type="hidden" name="errorredirect" value="http://americasbeautypageants.com/registration-error">
				
				<div id="SignUp">
				    <input type=hidden name="fields_email" value="<?php echo $email ?>">
				    <input type=hidden name="fields_fname" value="<?php echo $fname ?>">
				    <input type=hidden name="fields_lname" value="<?php echo $lname ?>">
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
			if (!form.lname.value) errors += "Error: Please fill the required field (First Name).\n";
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