<?php
	include_once('../../../wp-config.php');
	
	global $wpdb;
	$table_name = $wpdb->prefix . "prereg";
	
	$reg_code = ( isset($_POST['reg_code']) ) ? trim(strip_tags($_POST['reg_code'])) : null;
	$_SESSION['reg_code'] = $reg_code;
	
	$file = 'abp_prereg';
	$wpdb->show_errors();

	$values = $wpdb->get_results("SELECT * FROM " . $table_name . " WHERE rcode LIKE '%" . $reg_code ."%'");

	$csv_output = "Id, Date, First Name, Last Name, Email Address, Registation Code, Address, Address 2, City, State, ZIP, Phone Number, Date of Birth";
	$csv_output .= "\n";

	if ($wpdb->num_rows > 0) {
  		foreach ($values as $value) {
			foreach ($value as $col) {
				$csv_output .= $col;
				$csv_output .= ",";
			}
		$csv_output .= "\n";
		}
	}
				
	$filename = $file . "_" . time() . ".csv";

	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-type: text/x-csv");
	header("Content-Disposition: attachment; filename=" . $filename);
	header("Content-Length: " . strlen($csv_output));
	echo "$csv_output";
	exit;
?>