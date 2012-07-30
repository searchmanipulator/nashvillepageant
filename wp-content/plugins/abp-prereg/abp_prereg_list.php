<?php
global $wpdb;
$table_name = $wpdb->prefix . "prereg";
$file = 'export';
$wpdb->show_errors();

$regdetail = $wpdb->get_results("SELECT * FROM " . $table_name);

echo "<h2>" . __( 'America\'s Beauty Pageants Pre-Registrations List') . "</h2>";

if($_POST['action'] == 'reg') {
	if ( !isset($_POST['reg_code']) ) return;
	$reg_code = ( isset($_POST['reg_code']) ) ? trim(strip_tags($_POST['reg_code'])) : null;
	//echo $reg_code;
	$_SESSION['reg_code'] = $reg_code;
	$sql = "SELECT COUNT(*) FROM " . $table_name . " WHERE rcode LIKE '%" . $reg_code ."%'";
	$regdetail = $wpdb->get_results("SELECT * FROM " . $table_name . " WHERE rcode LIKE '%" . $reg_code ."%'");
} else if($_POST['action'] == 'export') {
	//echo "Action: Export";
} else {

}

?>




<div class="wrap">

<div>
<div style="float: left">
	<form name="view" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<p class="submit">
		<input type="hidden" name="action" value="reg">
		<?php _e("Registration Code: " ); ?><input type="text" name="reg_code" value="<?php echo $_SESSION['reg_code']; ?>" size="20"><?php _e(" e.g: DF56T5R" ); ?>
		<input type="submit" name="Submit" value="Update View" /></p>
			</form>
			
		</div>
		
		<div style="float: left">
				<form name="view" method="post"
				action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
				<p class="submit">
					<input type="hidden" name="action" value="reg">
		            <input type="hidden" name="reg_code" value="" >  
	            	<input type="submit" name="Submit" value="Reset View" /></p>
			</form>
		</div>	
		
		<div style="float: left">
			<form name="export" method="post"
				action="<?php echo plugins_url('abp-prereg'); ?>/export_prereg_list.php">
				<p class="submit">
					<input type="hidden" name="action" value="export"> <input
						type="hidden" name="reg_code"
						value="<?php echo $_SESSION['reg_code']; ?>"> <input type="submit"
						name="Submit" value="Download File" />
				</p>
			</form>
		</div>
	</div>
	<br><br><br><br>
	<hr>
<?php

echo '<h3>Total registrations: ' . $wpdb->num_rows . '</h3>';
echo "<table width=1000><tr><th>Record ID</th><th>Registration Date</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Phone</th><th>Registration Code</th></tr>";
foreach ($regdetail as $reg) {
	echo "<tr><td>" . $reg->id . "</td><td>" . $reg->time . "</td><td>" . $reg->fname . "</td><td>" . $reg->lname . "</td><td>" . $reg->email . "</td><td>" . $reg->phone . "</td><td>" . $reg->rcode . "</td></tr>";
}
echo "</table>";

?>
<hr>
	<div>
		<div style="float: left">
			<form name="view" method="post"
				action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
				<p class="submit">
					<input type="hidden" name="action" value="reg">
		            <?php _e("Registration Code: " ); ?><input type="text" name="reg_code" value="<?php echo $_SESSION['reg_code']; ?>" size="20"><?php _e(" e.g: DF56T5R" ); ?>  
	            <input type="submit" name="Submit" value="Update View" /></p>
			</form>
			
		</div>
		
		<div style="float: left">
				<form name="view" method="post"
				action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
				<p class="submit">
					<input type="hidden" name="action" value="reg">
		            <input type="hidden" name="reg_code" value="" >  
	            	<input type="submit" name="Submit" value="Reset View" /></p>
			</form>
		</div>	
		
		<div style="float: left">
			<form name="export" method="post"
				action="<?php echo plugins_url('abp-prereg'); ?>/export_prereg_list.php">
				<p class="submit">
					<input type="hidden" name="action" value="export">
					<input type="hidden" name="reg_code" value="<?php echo $_SESSION['reg_code']; ?>">
					<input type="submit" name="Submit" value="Download File" />
				</p>
			</form>
		</div>
	</div>
</div>
