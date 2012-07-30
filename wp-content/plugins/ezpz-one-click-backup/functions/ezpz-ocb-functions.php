<?php

require_once ('ezpz-ocb-restore.php');

/***************				General Functions			***************/
function ezpz_ocb_extend_timeout() {
	if (!ini_get('safe_mode')) {
		set_time_limit(1200);
		// 1200 seconds = 20 minutes
	}
}

function ezpz_ocb_check_compatability(){
	global $ezpz_ocb_data_key;
	if(isset($failed)){
		unset($failed);
	}
	if(strtolower(get_option('ezpz_ocb_is_compatible')) != 'true'){
//		check for exec functionality
		if(getcwd() != exec('pwd'))
			$failed[] = 'The php function &quot;exec&quot; is not activated for your server.';
	
//		check for file write/read
		file_put_contents(EZPZOCB . "/compatability-test.$ezpz_ocb_data_key.php", 'is_compatible' );
		if(file_get_contents(EZPZOCB . "/compatability-test.$ezpz_ocb_data_key.php") != 'is_compatible'){
			$failed[] = '<p>EZPZ OCB cannot properly write and/or read new files.';
		} else {
			unlink(EZPZOCB . "/compatability-test.$ezpz_ocb_data_key.php");
		}
		
//		check if php is in safe mode		
		if (ini_get('safe_mode')) {
			$failed[] = 'PHP safe mode is activated.';
		}
		if(isset($failed)){
			$output = '<big><b>Failed Server Compatability Tests:</b></big><p><ol><li> ' . implode('</li> <li>', $failed ) . '</li></ol></p>';
			update_option('ezpz_ocb_is_compatible', $output);
			$return  = 'false';
			set_status('error', get_option('ezpz_ocb_is_compatible'));
		} else {
		update_option('ezpz_ocb_is_compatible', 'true');
		set_status('error', 'unset');
		$return = 'true';
		}
	} else {
		set_status('error', 'unset');
		$return = 'true';		
	}
	return $return;
}

// Return the proper url for subpages
function ezpz_ocb_sp($subpage = 'home') {
	if ($subpage !== 'home') {
		return admin_url() . "admin.php?page=ezpz_ocb&sp=" . $subpage;
	} else {
		return admin_url() . "admin.php?page=ezpz_ocb";
	}
}

// Return a random alpha-numeric string $num characters long
function ezpz_ocb_rnd_alpha_numeric($num = 6) {
	for ($i = 0; $i < $num; $i++) {
		$seed = rand(1, 30) % 3;
		if ($seed == 0) {
			//            A-Z
			$char = chr(rand(97, 122));
		} else if ($seed == 1) {
			//            a-z
			$char = chr(rand(65, 90));
		} else {
			//            0-9
			$char = chr(rand(48, 57));
		}
		$output = $output . $char;
	}
	return $output;
}

function ezpz_ocb_backup_folder() {
	if (get_option('ezpz_ocb_backup_folder_name') == "") {
		$backup_folder_name = ezpz_ocb_rnd_alpha_numeric(rand(12, 18));
		update_option('ezpz_ocb_backup_folder_name', $backup_folder_name);
		sleep(1);
	}
	$output = EZPZOCB . '/backups/' . get_option('ezpz_ocb_backup_folder_name');
	if (!file_exists($output)) {
		create_folder("$output");
	}
	return $output;
}

function get_zip_date() {
	return get_option('ezpz_ocb_zip_date');
}

function set_new_date() {
	$datestamp = ezpz_date('', get_option('ezpz_ocb_ds_format'));
	// Increase backup time by one minute if a backup with timestamp already exists
	while (file_exists(ezpz_ocb_backup_folder() . '/' . clean_name() . $datestamp . ".zip")) {
		$time = time() + 60;
		$datestamp = ezpz_date($time, get_option('ezpz_ocb_ds_format'));
		get_backup_time('set', $time);
	}
	update_option('ezpz_ocb_zip_date', $datestamp);
}

function ezpz_control_bar() {

	global $ezpz_ocb_data_key;
	$site = site_url();
	$dl_url = site_url() . '/' . str_replace(ABSPATH, '', ezpz_ocb_backup_folder()) . '/' . get_option('ezpz_ocb_zip_name');
	$dl_name = get_option('ezpz_ocb_zip_name');
	$dl_load = EZPZOCB_URL . "/backups/data/backup-list.$ezpz_ocb_data_key.php";
	$ocb_style1 = "height: 42px;
	padding: 10px 0 8px 0; 
	border: 3px #bbb solid; 
	border-top-left-radius: 33px;
	border-top-right-radius: 33px;
	border-bottom-right-radius: 33px;
	border-bottom-left-radius: 33px; ";
	$ocb_img = EZPZOCB_URL . "/images/cbar-bg.png";
	$bgr_img = EZPZOCB_URL . "/images/bg-running.png";
	$ocb_img_style = "margin: -22px 0 0 0";
	$bgr_img_style = "margin: 2px 0 0 0; display: none;";
	$output = <<< CBAR
	<div style="clear: both;"></div>
	<div id="c-bar" style='margin: 20px auto'>
		<table style="text-align: center; width: 100%;">
		  <tbody>
		    <tr>
		      <td width='25%'><input id='cpanelBtn' type='button' $style onClick="location.href='$site/wp-admin/admin.php?page=ezpz_ocb'" class='ezpz-btn' value='Control Panel' title='Go to Control Panel'></input></td>		      
		      <td width='75%'>
		      <div style='$ocb_style1'>
		      <div style='$ocb_img_style'><img src='$ocb_img'/></div>
			  <table style="text-align: center; width: 100%;">
		  <tbody>
		    <tr>
		      <td width='33%'><input id='mbuBtn' type='button'  $style onClick="location.href='$site/wp-admin/admin.php?page=ezpz_ocb&sp=backup'" class='ezpz-btn' value='Manual Backup' title='Perform a manual backup'></input></td>
		      <td width='33%'><input id='bbuBtn' type='button'  $style onClick="runBgBackup();" class='ezpz-btn' value='Background Backup' title='Initiate a background backup'></input></td>
		      <td width='33%'><input id='dbuBtn' type='button'  $style onclick="tb_show('Choose which backup you wish to download.','$dl_load?height=200&width=400')" class='ezpz-btn' value='Download Backup' title='Choose which backup you wish to download.'></input></td>
		    </tr>
		  </tbody>
		</table>
		</div>
		      </td>
		    </tr>
		  </tbody>
		</table>
	</div>
<div id='noscript'>
<noscript>
	<p>EZPZ OCB requires JavaScript and jQuery be enabled to properly function.</p>
	<p>Please ensure Javascript is enabled on your browser and you have no addon or plugin which blocks Jacvascript or jQuery.</p>
</noscript>

<script type='text/javascript'>
	document.getElementById('noscript').style.display = 'none';
</script>
</div>
CBAR;
	return $output;
}

function head_template($title, $timer = false) {
	global $wpdb;
	$ocb_version = ezpz_ocb_release_num();
	$site_url = site_url();
	$EZPZOCB_URL = EZPZOCB_URL;
	$ppimg = EZPZOCB_URL . '/images/donate.gif';
	$list_img = EZPZOCB_URL . "/images/bullet.png";
	$cron_time = wp_date(wp_next_scheduled('ezpz_ocb_cron'));
	if (get_option('ezpz_ocb_set_cron') != 'off') {
	    $cron_schedule = "Next scheduled backup: $cron_time";
	} else {
	    $cron_schedule = "Scheduled backups are turned off.";
	}

	$sidebar = <<<SIDEBAR
<p style="text-align: center; line-height: 14px;"><strong>EZPZ One Click Backup</strong><br/>
by <em>EZPZ Solutions</em><br/>
<small>Release $ocb_version</small><br/></p>
    <p style="text-align: justify;">If you find this plugin useful please consider a donation to help <em>EZPZ Solutions</em>
 keep improving existing plugins and develop new, easy to use Wordpress plugins.</p>
   <center><form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="JSQGRHN58DXPE">
<input type="image" src="$ppimg" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
</form></center>
<p style="text-align: center;">
<a href="http://wordpress.org/extend/plugins/ezpz-one-click-backup/" target="_blank">
Rate this plugin at WordPress.org</a></p>
<p style="text-align: center;">Questions, Comments, Suggestions or Requests?</p>
<p style="text-align: center;">Contact us at <br/>EZPZSolutions@gmail.com</p>
<p style="text-align: center;"><a href="http://ezpzsolutions.net/ezpz-wordpress-plugins/ezpz-one-click-backup" target="_blank">Visit Plugin Site</a></p>
<!--[if IE]>
<center><a href="https://affiliates.mozilla.org/link/banner/4336/2/7"><img src="http://affiliates-cdn.mozilla.org/media/uploads/banners/upgrade-tall-green-EN.png" alt="" /></a></center>
<![endif]-->
SIDEBAR;

	$statusbar = <<<STATUS
	<div id='ezpzocb-status'></div>
STATUS;

	echo "
	<div id='ezpzocb'>
	<style type='text/css'>
		ul.ezpz-ul-circle {
			list-style-image: url('$list_img');
			list-style-position: inside;	
		}
	</style>
	<link rel='stylesheet' type='text/css' media='all' href='" . EZPZOCB_URL . "/ezpz-ocb.css' />
<div class='wrap'>";
	echo "
	<div id='page-body' style='min-height: 600px;'>
	<div style='background:transparent url($EZPZOCB_URL/images/gears2.gif) no-repeat; float: left; height: 23px; margin: 6px 6px 0 0; width: 46px;'><br/>
    </div>
	<div class='ezpz-title'>
			<li style='display: inline;'>EZPZ OCB - $title</li>
			<li id='ezpz-next-scheduled'>$cron_schedule</li>
	</div> <!-- ezpz-title -->
	<h2 style='display: none;'></h2>

        <div id='ezpz-ocb-wrapper' style='min-width: 815px; min-height: 600px;'>
            <div id='sidebar' style='margin: 0 10px; padding: 0 10px;
                 float: right; max-width: 180px; min-width: 20%; min-height: 600px;'>
                 $sidebar
            </div> <!-- sidebar -->
            <div id='left-body' style='width: 70%;
                 float: left; min-height: 600px;'>
                ";

	echo ezpz_control_bar();
}

function foot_template() {
	echo "<!-- begin foot template -->
	        </div> <!-- ezpz-ocb-wrapper -->
    </div> <!-- page-body -->
</div> <!-- wrap -->
</div> <!-- ezpzocb -->
<div style='clear: both';></div>";
	echo "
<!-- end foot template -->";
}

function restore_readme() {
	$blog_name = get_bloginfo('name');
	$abspath = ABSPATH;
	$site_url = site_url();
	return <<<README
	<style type='text/css'>
	 #ezpzocbfaq div.faq-answer {
		margin-left: 10px;
		color:666666 !important;
	}
	#ezpzocbfaq h3 {
		cursor: pointer;
	}
</style>
<div id="ezpzocbfaq" style="
font-family: Georgia, 'Times New Roman' , 'Bitstream Charter', Times, serif;
text-shadow:rgba(255,255,255,1) 0 1px 0;
color:#464646;
margin: 2px 18px 2px 20px;">
	<h3 title='Click to toggle answer' onclick="toggle_visibility('a-1')">Q: How do I restore my site from a backup using EZPZ Easy Restore?</h3>
	<div class="faq-answer" id='a-1'>
		<p>
			A: It&apos;s actually pretty easy, only 2 steps are required.
		</p>
		<ol>
			<li>
				Using FTP or Cpanel, upload the zip file you wish to restore from
				your computer to <b>$blog_name&apos;s</b> root folder
				(<em>$abspath</em>) then using Cpanel, unzip (unarchive)
				the zip file you uploaded into the same folder.
				<p class="hilite" >
					If you are unable to unarchive the zip file via Cpanel you&apos;ll need to unzip it
					on your computer then upload the entire <b>EZPZ_RESTORATION_FILES</b> folder
					into <b>$blog_name&apos;s</b> root folder.
					<br/>
					<b>DO NOT</b> unarchive the zip file contained within the restoration
					folder, just unzip the folder.
				</p>
			</li>
			<li>
				In your browser go to <b>$site_url/EZPZ_RESTORATION_FILES/EZPZ_RESTORE.php </b> and watch the magic happen...
				<br/>
				<p class="hilite" >
					The above link is only active after the previous two steps are
					performed. It will give a <b>404 Page Not Found error</b> when an auto restoration	isn&apos;t allowed or possible.
				</p>
			</li>
		</ol>
		<p>
			<b>$blog_name</b> will now be automatically
			restored to the date and time the backup you selected was made.
		</p>
		<p class="hilite" >
			If anything goes wrong there&apos;s no need to worry, all the old
			files have been saved and are available for manual restoration at <i>$abspath</i><em>EZPZ_RESTORATION_FILES</em>
		</p>
	</div>
README;
}

function tab($num = 1) {
	$nn = 0;
	$output = "";
	while ($nn < $num) {
		$output = $output . "&nbsp;&nbsp;&nbsp;&nbsp;";
		$nn++;
	}
	return $output;
}

function clean_name() {
	$cn = preg_replace("#[^0-9a-zA-Z_\s]#", "", str_replace(" ", "_", preg_replace("/&#?[a-z0-9]{2,8};/i", "", get_bloginfo('name')))) . "_";
	return str_replace('__', '_', $cn);
}

function ezpz_ocb_slug() {
	return "ezpz-one-click-backup";
}

function ezpz_ocb_custom_cron_schedules($schedules) {

	$_min = 60;
	$_hr = 60 * $_min;
	$_day = 24 * $_hr;

	$schedules['twodays'] = array('interval' => 2 * $_day, 'display' => __('Every other day'), );
	$schedules['weekly'] = array('interval' => 7 * $_day, 'display' => __('Weekly'), );
	$schedules['4daily'] = array('interval' => $_day / 4, 'display' => __('Four times daily'), );
	//	$schedules['10min'] = array('interval' => 10 * $_min, 'display' => __('Every 10 minutes'), );
	return $schedules;
}

function current_schedule($time, $schedule) {
	$prefix = "Your current schedule is ";

	if ($time > 12) {
		$adj_time = $time - 12;
		$adj_time = "$adj_time:00pm";
	} elseif ($time == 0) {
		$adj_time = "12:00am";
	} elseif ($time == 12) {
		$adj_time = "12:00pm";
	} else {
		$adj_time = "$time:00am";
	}

	switch ($schedule) {
		case 'off' :
			$prefix = "Scheduling is turned off.";
			$output = "";
			break;
		case 'daily' :
			$output = "one time a day at $adj_time";
			break;
		case 'twicedaily' :
			$_2time = $time % 12;
			$output = "two times a day at $_2time:00, am and pm";
			$output = str_replace(' 0', ' 12', $output);
			break;
		case 'twodays' :
			$output = "every other day at $adj_time";
			break;
		case 'weekly' :
			$weekday = get_option('ezpz_ocb_cron_day');
			$output = "once a week on every $weekday at $adj_time";
			break;
		case '4daily' :
			$_4time[1] = $time % 6;
			$_4time[2] = $_4time[1] + 6;
			$output = "four times a day at $_4time[1]:00 &amp; $_4time[2]:00, am and pm";
			$output = str_replace(' 0', ' 12', $output);
			break;
	}
	return $prefix . $output;
}

function get_folder_size($folder) {
	if ($folder == ABSPATH) {
		$orig_dir = exec("pwd");
		chdir($folder);
		$excluded_folders = '';
		$excluded_list = get_option('ezpz_ocb_excluded_folders');
		if ($excluded_list != "" && $excluded_list != "none") {
			$excluded = explode(",", $excluded_list);

			foreach ($excluded as $item) {
				$item = trim($item);
				$item = trim($item, "/\*");
				//                if (substr($item, 0, 2) == '~/') {
				//                    $item2 = substr($item, 2);
				//                    $excluded_folders = $excluded_folders . "--exclude='$item2/*' ";
				//                } else {
				$excluded_folders = $excluded_folders . "--exclude='$item/*' ";
				//                }
			}
			$new_cmd = "du -bc --exclude='*/ezpz-one-click-backup/backups/*' $excluded_folders";
			$NEWSIZE = exec($new_cmd);
		} else {
			$NEWSIZE = '0';
		}
		$orig_dir = exec("pwd");
		chdir($folder);
		$raw_cmd = "du -bc --exclude='*/ezpz-one-click-backup/backups/*'";
		$RAWSIZE = exec($raw_cmd);
		chdir($orig_dir);
		if ($NEWSIZE == $RAWSIZE) {
			$NEWSIZE = '0';
		}
		$X_SIZE = (trim(str_replace("total", "", $RAWSIZE))) - (trim(str_replace("total", "", $NEWSIZE)));
		//        echo"<p>X-Size = $X_SIZE<br>RAW_SIZE = $RAW_SIZE<br>NEW_SIZE = $NEW_SIZE</p>";
		if ($NEWSIZE != 0) {
			$X_SIZE = convert_bytes($X_SIZE);
		} else {
			$X_SIZE = 0;
		}
		$TOTALSIZE = convert_bytes(trim(str_replace("total", "", $RAWSIZE)));
		if ($NEWSIZE != '0') {
			$BACKUPSIZE = convert_bytes(trim(str_replace("total", "", $NEWSIZE)));
		} else {
			$BACKUPSIZE = $TOTALSIZE;
		}
		$SIZE = array('total' => $TOTALSIZE, 'excluded' => $X_SIZE, 'backup' => $BACKUPSIZE);

		//        update_option('ezpz_ocb_get_size', $SIZE);
		return $SIZE;
	} else {
		$orig_dir = exec("pwd");
		chdir($folder);
		$SIZE = exec("du -bc");
		chdir($orig_dir);

		$output = convert_bytes(trim(str_replace("total", "", $SIZE)));

		return $output;
	}
}

function convert_bytes($bytes) {
	$symbols = array('B', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb');
	$exp = floor(log($bytes) / log(1024));
	$size = sprintf("%.2f " . $symbols[$exp], ($bytes / pow(1024, floor($exp))));
	return $size;
}

function get_microtime($decimals = 7){
    list($u, $s) = explode(' ',microtime());
    (float)$microtime = $s . substr(ltrim($u, '0'), 0, strlen($u) - (8 - $decimals));
    return $microtime;
}

function get_thousandths_time(){
	list($u, $s) = explode(' ',microtime());
    return str_pad((string)round((float)$u * 1000), 4, '0', STR_PAD_LEFT);
}

function forbidden() {

	return "
    <!DOCTYPE html PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>
    <html>
        <head>
            <meta content='text/html; charset=ISO-8859-1'
                  http-equiv='content-type'>
            <title>Forbidden Area</title>
        </head>
        <body>
            <center>
            <img style='width: 380px; height: 380px;' alt='Forbidden Area'
            src='/wp-content/plugins/ezpz-one-click-backup/images/forbidden.jpg'>
            </center>
        </body>
    </html>";
}

function getDomain() {
	//    if ($domain === 'self') {
	//        $domain = site_url();
	//    }
	$domain = strtolower(site_url());

	$bits = explode('/', $domain);
	if ($bits[0] == 'http:' || $bits[0] == 'https:') {
		$domain = $bits[2];
	} else {
		$domain = $bits[0];
	}
	unset($bits);
	$bits = explode('.', $domain);
	$idz = count($bits);
	$idz -= 3;
	if (strlen($bits[($idz + 2)]) == 2) {
		$url = $bits[$idz] . '.' . $bits[($idz + 1)] . '.' . $bits[($idz + 2)];
	} else if (strlen($bits[($idz + 2)]) == 0) {
		$url = $bits[($idz)] . '.' . $bits[($idz + 1)];
	} else {
		$url = $bits[($idz + 1)] . '.' . $bits[($idz + 2)];
	}

	if (strpos($url, '.') == 0) {
		$url = substr($url, 1);
	}
	return $url;
}

function set_permissions($dir_permission = '', $file_permission = '') {
	$path = EZPZOCB;
	$permissions = get_option('ezpz_ocb_permissions');
	if ($dir_permission == '') {
		$dir_permission = $permissions['folders'];
		//        $permissions['folders'] = $permissions['def_folder'];
		update_option('ezpz_ocb_permissions', $permissions);
	} else {
		$permissions['folders'] = $dir_permission;
		update_option('ezpz_ocb_permissions', $permissions);
	}

	if ($file_permission == '') {
		$file_permission = $permissions['files'];
		//        $permissions['files'] = $permissions['def_file'];
		update_option('ezpz_ocb_permissions', $permissions);
	} else {
		$permissions['files'] = $file_permission;
		update_option('ezpz_ocb_permissions', $permissions);
	}

	// Set all folders permissions to $dir_permission (755 default)
	$dir_cmd = "find $path -type d -print0 | xargs -0 chmod " . $dir_permission;
	exec($dir_cmd);

	// Set all file permissions to $file_permission (644 default)
	$file_cmd = "find $path -type f -print0 | xargs -0 chmod " . $file_permission;
	exec($file_cmd);
}

function get_permissions($type = 'folder') {
	$permissions = get_option('ezpz_ocb_permissions');
	if ($type == 'folder') {
		$output = $permissions['folders'];
	} elseif ($type == 'file') {
		$output = $permissions['files'];
	} else {
		$output = '0777';
	}
	return $output;
}

function create_folder($folder_path){
	$cmd = "mkdir -m " . get_permissions() . " $folder_path";
	exec("$cmd");
}

function site_name() {
	return html_entity_decode(get_bloginfo('name'), ENT_NOQUOTES, 'UTF-8');
}

/*
 function that reads directory content and
 returns the result as links to every file in the directory
 also it disply type wheather its a file or directory
 */

function installed_extensions() {

	$handle = opendir(EZPZOCB . '/extensions/');

	while ($file = readdir($handle)) {

		if ($file == "." || $file == "..") {

		} else {
			$parts = explode(".", $file);
			if (is_array($parts) && count($parts) > 1) {
				$is_folder = false;
			} else {
				$is_folder = true;
			}
			if ($is_folder) {
				$output .= '|' . $file;
			}
		}
	}
	closedir($handle);
	if ($output != '')
		$output = substr($output, 1, strlen($output) - 1);
	return $output;
}

function file_list($directory) {

	// create an array to hold directory list
	$results = array();

	// create a handler for the directory
	$handler = opendir($directory);

	// open directory and walk through the filenames
	while ($file = readdir($handler)) {

		// if file isn't this directory or its parent, add it to the results
		if ($file != "." && $file != "..") {
			$file_part = pathinfo($file);
			if ($file_part['extension'] != '') {
				$results[] = $file;
			}
		}
	}

	// tidy up: close the handler
	closedir($handler);

	// done!
	return $results;
}

function ezpz_date($time = '', $format = false) {
	if ($time == '') {
		$time = time();
	}
	$ezpz_tz = get_option('ezpz_ocb_save_tz');
	if ($ezpz_tz == "") {
		$ezpz_tz = 'GMT';
	}
	$wp_tz = get_option('timezone_string');
	if ($wp_tz == "") {
		$wp_tz = 'GMT';
	}
	date_default_timezone_set($ezpz_tz);
	if ($format === false) {
		$date_format = get_option('date_format');
		$time_format = get_option('time_format');
		$format = $date_format . " " . $time_format;
	}
	$ezpz_time = date("$format", $time);
	date_default_timezone_set($wp_tz);
	//    $wp_time = date('Y-m-d H:i:s', $time);
	//    echo "<p> EZPZ Time = $ezpz_time --- WP Time = $wp_time</p>";
	return $ezpz_time;
}

function wp_date($time = null) {
	$date_format = get_option('date_format');
	$time_format = get_option('time_format');
	if ($time == null) {
		$time = time();
	} else {
		settype($time, "integer");
	}

	$wp_tz = get_option('timezone_string');
	if ($wp_tz == "") {
		$wp_tz = 'GMT';
	}
	date_default_timezone_set($wp_tz);
	$wp_time = date($date_format . " " . $time_format, $time);
	return $wp_time;
}

function ezpz_time($time = '') {
	if ($time == '') {
		$time = time();
	}
	return strtotime(ezpz_date());
}

function ezpz_clean_date($format) {
	$format = preg_replace("#[^0-9a-zA-Z\_\.\-\,\@\s]#", "", $opt_val['tmp_ds_format']);
	$format = str_replace(" ", "_", $opt_val['tmp_ds_format']);
	$format = str_replace("\\", "", $opt_val['tmp_ds_format']);
	return $format;
}

function convert_secs($seconds) {
	if ($seconds > 0) {
		$mins = floor($seconds / 60);
		$secs = str_pad($seconds % 60, 2, "0", STR_PAD_LEFT);
		return "$mins:$secs";
	}
}

function perms($index) {
	$permissions = get_option('ezpz_ocb_permissions');
	return $permissions[$index];
}

function has_ext($extension) {
	//  extension codes
	//  Dropbox     =   dbx
	//  Amazon S3   =   as3
	if ($extension === "dbx") {
		return file_exists(EZPZOCB . "/extensions/Dropbox/dbx-ver");
	}

	if ($extension === "as3") {
		return file_exists(EZPZOCB . "/extensions/AmazonS3/as3-ver");
	}
}

function install_ext($extension) {
	if ($extension === 'dbx' && !has_ext('dbx')) {
		$dbx_zip = "dbx_v1.1.zip";
		// Dropbox extension version zip file
		$old_dir = getcwd();
		//exec('pwd');
		chdir(EZPZOCB . "/extensions");
		// Remove existing Dropbox folder if it exists
		if (is_dir(EZPZOCB . "/extensions/Dropbox")) {
			exec("rm -r Dropbox");
		}
		// Get Dropbox extension zip file
		$dbx_at_aws = file_get_contents("https://s3.amazonaws.com/ezpz-ocb/extensions/$dbx_zip");
		file_put_contents(EZPZOCB . "/extensions/$dbx_zip", $dbx_at_aws);
		//        $wget_cmd = "wget https://s3.amazonaws.com/ezpz-ocb/extensions/$dbx_zip";
		//        exec($wget_cmd);
		//        while (!file_exists(EZPZOCB . "/extensions/$dbx_zip")) {
		//            // wait...
		//        }
		// Unzip Dropbox extension zip file
		$unzip_cmd = "unzip $dbx_zip";
		exec($unzip_cmd);
		// Remove Dropbox extension zip file if installation succeded
		if (file_exists(EZPZOCB . "/extensions/$dbx_zip") && has_ext('dbx')) {
			unlink(EZPZOCB . "/extensions/$dbx_zip");
		}
		chdir($old_dir);
		$extensions = get_option('ezpz_ocb_extensions');
		$extensions['dbx'] = true;
		update_option("ezpz_ocb_extensions", $extensions);
		if (file_exists(EZPZOCB . "/extensions/Dropbox")) {
			file_put_contents(EZPZOCB . "/extensions/Dropbox/index.html", forbidden());
		}
		if (!get_option('ezpz_ocb_dropbox')) {
			$folder = substr(clean_name(), 0, strlen(clean_name()) - 1);
			$ftxd = 2634336 + time();
			$dropbox = array('active' => '', 'alert' => '', 'pass' => '', 'mail' => '', 'folder' => $folder, 'license' => 'Free 30 day trial', 'ftxd' => $ftxd);
			update_option("ezpz_ocb_dropbox", $dropbox);
		}
	}
}

function uninstall_ext($extension) {
	if ($extension === 'dbx') {
		$old_dir = getcwd();
		chdir(EZPZOCB . "/extensions");
		// Remove existing Dropbox folder if it exists
		if (is_dir(EZPZOCB . "/extensions/Dropbox")) {
			exec("rm -r Dropbox");
		}
		chdir($old_dir);
		$extensions = get_option('ezpz_ocb_extensions');
		$extensions['dbx'] = false;
		update_option("ezpz_ocb_extensions", $extensions);
	}
}

function list_ezpz_ocb_folders($directory = EZPZOCB) {
	$array_items = array($directory);
	if ($handle = @opendir($directory)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				if (is_dir($directory . "/" . $file)) {
					$array_items = array_merge($array_items, list_ezpz_ocb_folders($directory . "/" . $file));
					$file = $directory . "/" . $file;
					if (!in_array($file, $array_items)) {
						array_push($array_items, $file);
						//$array_items[] = preg_replace("/\/\//si", "/", $file);
					}
				}
			}
		}
		closedir($handle);
	}
	return $array_items;
}

function directoryToArray($directory, $recursive) {
	$array_items = array();
	if ($handle = opendir($directory)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				if (is_dir($directory . "/" . $file)) {
					if ($recursive) {
						$array_items = array_merge($array_items, directoryToArray($directory . "/" . $file, $recursive));
					}
					$file = $directory . "/" . $file;
					$array_items[] = preg_replace("/\/\//si", "/", $file);
				} else {
					$file = $directory . "/" . $file;
					$array_items[] = preg_replace("/\/\//si", "/", $file);
				}
			}
		}
		closedir($handle);
	}
	return $array_items;
}

function get_user_browser() {
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
	$ub = '';
	if (preg_match('/MSIE/i', $u_agent)) {
		$ub = "ie";
	} elseif (preg_match('/Firefox/i', $u_agent) && !preg_match('/Flock/i', $u_agent) && !preg_match('/Navigator/i', $u_agent)) {
		$ub = "firefox";
	} elseif (preg_match('/Safari/i', $u_agent) && !preg_match('/Chrome/i', $u_agent)) {
		$ub = "safari";
	} elseif (preg_match('/Chrome/i', $u_agent)) {
		$ub = "chrome";
	} elseif (preg_match('/Flock/i', $u_agent)) {
		$ub = "flock";
	} elseif (preg_match('/Opera/i', $u_agent)) {
		$ub = "opera";
	} elseif (preg_match('/Navigator/i', $u_agent)) {
		$ub = "netscape";
	}

	return $ub;
}

function get_num_backups() {
	count_backups(false);
	$num = get_option('ezpz_ocb_backup_count');
	switch ($num) {
		case 0 :
			if(!backup_in_progress('?')){
				set_status('backup', 'No Backups Found - Please do a backup now.');
			}
			return post_num('No Backups Found - Please backup now.');
			break;
		case 1 :
			return post_num('Last Backup');
			break;
		case 2 :
			return post_num('Last Two Backups');
			break;
		case 3 :
			return post_num('Last Three Backups');
			break;
		case 4 :
			return post_num('Last Four Backups');
			break;
		case 5 :
			return post_num('Last Five Backups');
			break;
		case 6 :
			return post_num('Last Six Backups');
			break;
		case 7 :
			return post_num('Last Seven Backups');
			break;
		case 8 :
			return post_num('Last Eight Backups');
			break;
		case 9 :
			return post_num('Last Nine Backups');
			break;
		case 10 :
			return post_num('Last Ten Backups');
			break;
	}
}

function post_num($output) {
	global $ezpz_ocb_data_key;
	if (!file_exists(EZPZOCB . "/backups/data/")) {
		create_folder(EZPZOCB . "/backups/data/");
	}
	file_put_contents(EZPZOCB . "/backups/data/buNum.$ezpz_ocb_data_key.php", $output);
	return $output;
}

function ezpz_ocb_admin_notices() {
	$wp_dir = ABSPATH;
	//str_replace('/wp-content/plugins', '', WP_PLUGIN_DIR);
	$wp_dir_name_tmp = explode('/', $wp_dir);
	$wp_dir_name = end($wp_dir_name_tmp);
	$wp_parent_dir = str_replace("/$wp_dir_name", "", $wp_dir);
	$ezpz_restore_dir = $wp_dir . "EZPZ_RESTORATION_FILES";

	if (file_exists("$ezpz_restore_dir")) {
		$folder_size = get_folder_size($ezpz_restore_dir);

		if ($folder_size != null) {
			$folder_info = "the $folder_size of";
		} else {
			$folder_info = "the";
		}

		$blog_name = get_bloginfo('name');
		$url_slug = "?dir=$ezpz_restore_dir" . "&amp;site=" . site_url();
		$clear_files = EZPZOCB_URL . "/functions/ezpz-ocb-clear-files.php$url_slug";
		echo "<div id='notice' class='updated'><p><center><b>$blog_name</b> has been
        successfully restored by <b>EZPZ OCB Easy Restore</b>.<br/>If everything seems
        normal it's safe to remove $folder_info <em>EZPZ_RESTORATION_FILES</em>
        left after restoration by <a href='$clear_files'>Clicking here</a>.</center></p></div>";
	}

	$extensions = get_option('ezpz_ocb_extensions');
	$show_notice = '';
	if ($extensions['dbx'] && !has_ext('dbx')) {
		$form_action = str_replace('%7E', '~', $_SERVER['REQUEST_URI']);
		if ($_POST['install'] == "Install" && $_POST['dbx_action_hidden'] && $_POST['dbx_action_hidden'] == 'Y') {
			install_ext('dbx');
			if (has_ext('dbx')) {
				$show_notice = 'installed';
				$_POST['install'] = '';
			}
		}

		if ($_POST['noinstall'] == "Do Not Install" && $_POST['dbx_action_hidden'] && $_POST['dbx_action_hidden'] == 'Y') {
			uninstall_ext('dbx');
			if (!has_ext('dbx')) {
				$show_notice = 'uninstalled';
				$_POST['noinstall'] = '';
			}
		}

		if ($show_notice == '') {
			echo "<div id='notice' class='updated'>
            <form name='dbx_action' method='post' action='" . $form_action . "'>
            <input type='hidden' name='dbx_action_hidden' value='Y' />
            <p>The Dropbox Extension is no longer included in EZPZ OCB downloads and
            must now be installed as a separate installation. To automatically
            install it and continue using the Dropbox Extension as before just click
            <input type='submit' name='install' value='Install' /> or click
            <input type='submit' name='noinstall' value='Do Not Install' /> if you wish
            to not install the Dropbox Extension.</p></form></div>";
		} elseif ($show_notice == 'installed') {
			echo "<div id='notice' class='updated'><p>The Dropbox Extension has been
                reinstalled and will function just as before.</p></div>";
		} elseif ($show_notice == 'uninstalled') {
			echo "<div id='notice' class='updated'><p>The Dropbox Extension has
                not been installed.</p></div>";
		}
	}
}

function empty_folder($folder_path, $del_dir = false) {

	if (file_exists($folder_path)) {
		$cmd = "rm -r $folder_path";
		exec($cmd);

		if ($del_dir === false) {
			create_folder("$folder_path");
		}
		if (file_exists($folder_path) && function_exists(forbidden)) {
			file_put_contents("$folder_path/index.html", forbidden());
		}
	}
}

function show_load_img($id){
	return  tab() . "<img id='$id' src='" . EZPZOCB_URL . "/images/loading.gif' height='20' width='20' align='ABSMIDDLE' />" . tab();
}

function remove_load_img($id){
	sleep(2);
	$temp_ajax = file_get_contents(get_write_file());
	$temp_ajax = str_replace(tab() . "<img id='$id' src='" . EZPZOCB_URL . "/images/loading.gif' height='20' width='20' align='ABSMIDDLE' />" . tab(), "", $temp_ajax);
	tmp_write($temp_ajax, false);
}

/***************			   	Backup  Functions			***************/

// Backup the database
function get_sql_dump($sql_file, $display = false) {
	global $wpdb;
	$sql_load_img = show_load_img('getSql');
	$alt_db = get_option('ezpz_ocb_db_dump');

	//    Set some variables name
	$db_prefix = $wpdb -> prefix;
	$prefix_only = get_option('ezpz_ocb_prefix_only');
	$tablelist = "";
	$db_user = bin2hex(DB_USER);
	$db_password = bin2hex(DB_PASSWORD);
	$db_name = bin2hex(DB_NAME);
	$db_host = bin2hex(DB_HOST);

	if (get_option('ezpz_ocb_db_dump') != "alt") {// Primary database backup method
		//    Perform prefix only extraction if option is selected
		tmp_write($sql_load_img);
		if ($prefix_only === "yes") {
			if (!mysql_connect(DB_HOST, DB_USER, DB_PASSWORD)) {
				echo 'Could not connect to mysql';
				exit ;
			}

			$sql_tables = "SHOW TABLES FROM " . DB_NAME;
			$result = mysql_query($sql_tables);

			if (!$result) {
				echo "DB Error, could not list tables\n";
				echo 'MySQL Error: ' . mysql_error();
				exit ;
			}

			$prefix_len = strlen($db_prefix);
			while ($row = mysql_fetch_row($result)) {
				if (substr($row[0], 0, $prefix_len) == $db_prefix) {
					$tablelist = $tablelist . '"' . $row[0] . '" ';
				}
			}

			//        Prefix only database backup command
			$command = 'mysqldump -p --single-transaction --user="' . pack('H*', $db_user) . '" --password="' . pack('H*', $db_password) . '" --host="' . pack('H*', $db_host) . '" --opt "' . pack('H*', $db_name) . '" ' . $tablelist . ' > "' . $sql_file . '"';
		} else {
			//        Entire database backup command
			$command = 'mysqldump -p --single-transaction --user="' . pack('H*', $db_user) . '" --password="' . pack('H*', $db_password) . '" --host="' . pack('H*', $db_host) . '" --add-drop-table "' . pack('H*', $db_name) . '" > "' . $sql_file . '"';
		}

		exec($command);
		//        $ps = run_in_background("$command");
		//        if ($display !== false) {
		////            $write_file = EZPZOCB . '/' . get_option('ezpz_ocb_backup_folder_name') . '.txt';
		//            $img = "<img style='height: 12px; width: 4px; padding-left: 2px;' src='" . EZPZOCB_URL . "/images/seg.jpg' />";
		//            while (is_process_running($ps)) {
		//                file_put_contents(get_write_file(), "$img", FILE_APPEND);
		//                usleep(10000);
		//            }
		//        }
	} else {// Alternate database backup method
		update_option('ezpz_ocb_prefix_only', "nullified");
		tmp_write(" using the alternative database backup method. $sql_load_img");
		db_dump(pack('H*', $db_host), pack('H*', $db_user), pack('H*', $db_password), pack('H*', $db_name), $sql_file);
	}
	usleep(250000);
}

function db_dump($host, $user, $pass, $name, $backup_file) {

	$link = mysql_connect($host, $user, $pass);
	$return = "";
	mysql_select_db($name, $link);

	//get all of the tables
	$tables = array();
	$result = mysql_query('SHOW TABLES');
	while ($row = mysql_fetch_row($result)) {
		$count++;
		if($count % 100 == 0){
			tmp_write("<img alt = '' style='height: 12px; width: 4px; padding-left: 2px;' src='" . EZPZOCB_URL . "/images/seg.jpg' />");
		}
		$tables[] = $row[0];
	}

	//cycle through
	foreach ($tables as $table) {
		$count++;
		if($count % 100 == 0){
			tmp_write("<img alt = '' style='height: 12px; width: 4px; padding-left: 2px;' src='" . EZPZOCB_URL . "/images/seg.jpg' />");
		}

		$result = mysql_query('SELECT * FROM ' . $table);
		$num_fields = mysql_num_fields($result);

		$return .= 'DROP TABLE ' . $table . ';';
		$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE ' . $table));
		$return .= "\n\n" . $row2[1] . ";\n\n";

		for ($i = 0; $i < $num_fields; $i++) {
			while ($row = mysql_fetch_row($result)) {
				$return .= 'INSERT INTO ' . $table . ' VALUES(';
				for ($j = 0; $j < $num_fields; $j++) {
					$row[$j] = addslashes($row[$j]);
					$row[$j] = preg_replace("/\n/", "\\n", $row[$j]);
					if (isset($row[$j])) {
						$return .= '"' . $row[$j] . '"';
					} else {
						$return .= '""';
					}
					if ($j < ($num_fields - 1)) {
						$return .= ',';
					}
				}
				$return .= ");\n";
			}
		}
		$return .= "\n\n\n";
	}

	//save file
	file_put_contents($backup_file, $return);
}

function rename_backup_folder($name) {
	$ezpz_ocb_data_key = get_option('	ezpz_ocb_backup_folder_name');
	rename(EZPZOCB . '/backups/' . get_option('ezpz_ocb_backup_folder_name'), EZPZOCB . '/backups/' . $name);
	$oldDirectory = file_get_contents(EZPZOCB . "/backups/data/directory.$ezpz_ocb_data_key.php");
	$newDirectory = str_replace(get_option('ezpz_ocb_backup_folder_name'), $name, $oldDirectory);
	file_put_contents(EZPZOCB . "/backups/data/directory.$ezpz_ocb_data_key.php", $newDirectory);
	if ($handle = opendir(EZPZOCB . "/backups/logs/current/")) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." && $file != 'index.html') {
				$thisFile = EZPZOCB . "/backups/logs/current/$file";
				file_put_contents($thisFile, str_replace(get_option('ezpz_ocb_backup_folder_name'), $name, file_get_contents($thisFile)));
			}
		}
		closedir($handle);
	}
}

function count_backups($manage = true) {
	$buCnt = 0;
	if ($handle = opendir(ezpz_ocb_backup_folder())) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." && $file != 'index.html') {
				if(end(explode('.', $file)) == 'zip'){
					$buCnt++;
				}
			}
		}
		closedir($handle);
	}
	if (get_option('ezpz_ocb_backup_count') != $buCnt) {
		update_option('ezpz_ocb_backup_count', $buCnt);
		if ($buCnt > 0 && $manage) {
			manage_backups();
		}
	}
}

function list_backups() {
	global $ezpz_ocb_data_key;
	$backup_folder_url = str_replace(EZPZOCB, EZPZOCB_URL, ezpz_ocb_backup_folder());
	$style = "text-align: center; font-size: 120%; list-style-type: circle;";
	$backup_list[] .= "<ul>";
	if ($handle = opendir(ezpz_ocb_backup_folder())) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." && $file != 'index.html') {
				if (strpos($file, '.zip')) {
					$backup_list[] .= "\n\t<li class='TB-downloads' style='$style'><a href='$backup_folder_url/$file'>$file</a></li>";
					$buCnt++;
				}
			}
		}
		closedir($handle);
		rsort($backup_list);
		$backup_list[] .= "\n</ul>";
		file_put_contents(EZPZOCB . "/backups/data/backup-list.$ezpz_ocb_data_key.php", $backup_list);
		if ($buCnt == 0) {
			file_put_contents(EZPZOCB . "/backups/data/directory.$ezpz_ocb_data_key.php", '');
		}
	}
}

function delete_backup($zip) {
	$zip_path = ezpz_ocb_backup_folder() . '/' . $zip;
	if (file_exists($zip_path)) {
		unlink($zip_path);
	}
	if ($handle = opendir(EZPZOCB . "/backups/logs/current/")) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." && $file != 'index.html') {
				$log = explode('.', $file, 2);
				if ($log[1] . '.zip' == $zip) {
					unlink(EZPZOCB . "/backups/logs/current/" . $file);
				}
			}
		}
		closedir($handle);
	}
	manage_backups();
	list_backups();

}

function manage_backups($zip_file = '') {
	if (file_exists($zip_file) || $zip_file == '') {
		$maxallowed = get_option('ezpz_ocb_max_allowed');
		$logCnt = 0;

		if ($handle = opendir(EZPZOCB . "/backups/logs/current/")) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != ".." && $file != 'index.html') {
					if(strpos(file_get_contents(EZPZOCB . "/backups/logs/current/$file"), "!!!---ABORTED---!!!")){
						unlink(EZPZOCB . "/backups/logs/current/$file");
					} else {						
						$logs[$logCnt] = $file;
						$logCnt++;
					}
				}
			}
			closedir($handle);
		}

		if ($logCnt != 0) {

			sort($logs);
			$logs2 = $logs;
			if ($logCnt > $maxallowed) {
				$n = ($logCnt - $maxallowed);
				for ($counter = 0; $counter < $n; $counter++) {
					array_shift($logs2);
					unlink(EZPZOCB . '/backups/logs/current/' . $logs[$counter]);
				}
			}

			$buCnt = 0;
			if ($handle = opendir(ezpz_ocb_backup_folder())) {
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != ".." && $file != 'index.html') {
						$bkups[$buCnt] = filemtime(ezpz_ocb_backup_folder() . "/$file") . "|$file";
						$buCnt++;
					}
				}
				closedir($handle);
			}
			sort($bkups);
			$bkups2 = $bkups;
			$buCnt--;
			if ($buCnt > $maxallowed) {
				$n = ($buCnt - $maxallowed);
				for ($counter = 0; $counter < $n; $counter++) {
					array_shift($bkups2);
					unlink(ezpz_ocb_backup_folder() . '/' . substr($bkups[$counter], 11));
				}
			}
			update_option('ezpz_ocb_zip_name', substr(end($bkups), 11));

			$backup_list = array($maxallowed);
			$start = "";
			$end = "";
			$date_format = get_option('date_format');
			$time_format = get_option('time_format');
			for ($counter = $maxallowed - 1; $counter >= 0; $counter--) {
				if ($logs2[$counter] != '') {
					$raw_file_data = explode('.', $logs2[$counter]);
					$date_type = explode('-', $raw_file_data[0]);
					$bu_date = wp_date($date_type[0]);
					//date($date_format, $date_type[0]) . ' ' . date($time_format, $date_type[0]);
					$bu_type = ucfirst($date_type[1]);
					$data = file_get_contents(EZPZOCB . '/backups/logs/current/' . $logs2[$counter]);
					if (strlen($data) > 10) {
						$ZIPSIZE = explode('<!--zipsize-->', $data);
						$zip_size = $ZIPSIZE[1];
						$removeData = explode("<!--removeMe-->", $data);
						$data = $removeData[0] . $removeData[2] . $removeData[4];
						$data = str_replace("id='hide-del-dl-bar'", "id='del-dl-bar'", $data);

						$temp1 = "<div class='bu-log'><h4 onclick=\"toggleLogs('bulog$counter');\"><span style='font-size: 105%; font-style: normal;'>&raquo;</span> $bu_date - $bu_type Backup ($zip_size)</a></h4>
								<div id='bulog$counter' class='inner-bu-log' style='display: none;'><p>$data</p></div></div>
							";
						$temp2 = $temp2 . $temp1;
					}
				}
			}
			if (strlen($data) > 10) {
				array_push($backup_list, $data);
			}
			if ($logCnt > $maxallowed) {
				$numBackups = $maxallowed;
			} else {
				$numBackups = $logCnt;
			}
			update_option('ezpz_ocb_backup_count', $numBackups);
			get_num_backups();
			list_backups();
			update_option('ezpz_ocb_last_log', $backup_list);
			if (!file_exists(EZPZOCB . '/backups/data')) {
				create_folder(EZPZOCB . '/backups/data');
			}
			global $ezpz_ocb_data_key;
			file_put_contents(EZPZOCB . "/backups/data/directory.$ezpz_ocb_data_key.php", $start . $temp2 . $end);
		}
	}
	update_option('ezpz_ocb_backup_type', 'none');
	while (get_option('ezpz_ocb_backup_type') != 'none') {
		usleep(100000);
	}
}

function bg_backup() {
	wp_schedule_single_event(time() - 60, 'ezpz_ocb_run_bg_backup');
}

function run_in_background($Command, $Priority = 0) {
	if ($Priority) {
		$PID = exec("nohup nice -n $Priority $Command 2> /dev/null & echo $!");
	} else {
		$PID = exec("nohup $Command > /dev/null 2> /dev/null & echo $!");
	}
	return ($PID);
}

function is_process_running($PID) {
	if (!file_exists(EZPZOCB . "/backups/in-progress.php")) {
		exec("kill $PID");
	} else {
		exec("ps $PID", $ProcessState);
	}
	return (count($ProcessState) >= 2);
}

function tmp_write($content, $append = true) {

	if ($content == '?EOF?') {
		usleep(250000);
		if (strpos(get_write_file(), "<!--EOF-->") < 10) {
			return true;
		} else {
			return false;
		}
	}
	if ($append) {
		file_put_contents(get_write_file(), $content, FILE_APPEND);
	} else {
		file_put_contents(get_write_file(), $content);
	}
	echo $content;
	flush();
	ob_flush();
	//    echo $content;
}

function get_status($content) {
	global $ezpz_ocb_data_key;
	$last = $content[strlen($content) - 1];
	if ($last === "?") {
		$first = str_replace('?', '', $content);
		if (file_get_contents(EZPZOCB . "/backups/data/" . $first . "-status.$ezpz_ocb_data_key.php") != '') {
			return true;
		} else {
			return false;
		}
	} else {
		return EZPZOCB_URL . "/backups/data/" . $content . "-status.$ezpz_ocb_data_key.php?";
	}
}

function set_status($type, $content) {
	global $ezpz_ocb_data_key;
	
	if($type == 'abort'){
		file_put_contents(EZPZOCB . "/backups/data/backup-status.$ezpz_ocb_data_key.php", $content);
	}

	if ($content !== 'unset') {
		file_put_contents(EZPZOCB . "/backups/data/" . $type . "-status.$ezpz_ocb_data_key.php", $content);
	} else {
		if (file_exists(EZPZOCB . "/backups/data/" . $type . "-status.$ezpz_ocb_data_key.php")) {
			unlink(EZPZOCB . "/backups/data/" . $type . "-status.$ezpz_ocb_data_key.php");
		}
	}
}

function backup_in_progress($content = '?') {
	switch ($content) {
		case '?' :
			if (file_exists(EZPZOCB . "/backups/in-progress.php") === true) {
				return true;
			} else {
				return false;
			}
			break;
		case ('url') :
			return EZPZOCB_URL . "/backups/in-progress.php";
			break;
		case ('type') :
			if (file_exists(file_get_contents(EZPZOCB . "/backups/in-progress.php"))) {
				return file_get_contents(EZPZOCB . "/backups/in-progress.php");
			}
			break;
		case 'remove' :
			unlink(EZPZOCB . "/backups/in-progress.php");
			break;
		default :
			$now = time();
			update_option('ezpz_ocb_backup_type', ucfirst($content));
			file_put_contents(EZPZOCB . "/backups/in-progress.php", strtolower($content));
			break;
	}
}

function get_backup_time($set = '', $time = '') {

	if ($set = 'set') {
		if ($time == '') {
			$time = time();
		}
		update_option('ezpz_ocb_backup_time', $time);
		update_option('ezpz_ocb_last_backup_time', $time);
	} elseif ($set = 'last') {
		return get_option('ezpz_ocb_last_backup_time');
	}
	return get_option('ezpz_ocb_backup_time');
}

function get_write_file() {
	if (!file_exists(EZPZOCB . '/backups/logs')) {
		create_folder(EZPZOCB . '/backups/logs');
	}

	return EZPZOCB . '/backups/logs/current/working.php';
	//"/$ajnow.php";
}

function get_write_file_url() {
	//    $ajname = get_option('ezpz_ocb_ajname');
	//    $ajnow = $ajname['now'];
	//    $ajlast = $ajname['last'];

	return str_replace(EZPZOCB, EZPZOCB_URL, get_write_file());
}

function ezpzocb_cron($id) {
	global $ezpz_ocb_cron_lock;
	cron_log(">>>\t\t    Accessed at " . date('H:i:s.', (float) $id) . get_thousandths_time() . "\n");
	// Bug fix for multiple wp-cron executions
	if($ezpz_ocb_cron_lock == 'off'){
		$ezpz_ocb_cron_lock = 'on';
		(float) $cron_time = (float)$id - 100;
		cron_log("\n  ----  " . date('F jS, Y H:i:s.') . get_thousandths_time() . "  ----\n");
	} else {
		usleep(5000);
		$cron_time = get_microtime();
		$ezpz_ocb_cron_lock = 'skip';
	}
	if($cron_time > $id){
		exit(cron_log("\n---->    " . date('F jS, Y H:i:s.') . get_thousandths_time() . " - STOPPED DUPLICATE ATTEMPT! \n            id = $id | cron_time = $cron_time | cron_lock = '" . $ezpz_ocb_cron_lock . "'\n"));
	}
	
	(float)$time = get_microtime();
	

	global $ezpz_ocb_data_key;
	(int)$last_backup = (int) get_option('ezpz_ocb_last_backup_time');
	(int)$next_backup = $last_backup + 1200;
	// Make sure it's been at least 20 minutes (1200 seconds) since last schedualed backup ran.
	if ($time > $next_backup) {
		cron_log(" ->\t-- Twenty minute timeout test: PASSED!\n  ->        Beginning backup at " . wp_date(time())  . ".\n\n", true);
		if (!file_exists(ezpz_ocb_backup_folder() . "/FILELOCK")) {
			file_put_contents(ezpz_ocb_backup_folder() . "/FILELOCK", "");
			if (file_exists(EZPZOCB . "/backups/data/error-status.$ezpz_ocb_data_key.php")) {
				unlink(EZPZOCB . "/backups/data/error-status.$ezpz_ocb_data_key.php");
			}
			sleep(5); 

			// Make sure EZPZOCB is ready to run a backup.
			if (get_option('ezpz_ocb_backup_type') == 'none') {
				$abort_link =  tab(3) . "<a style='' class='ezpz-btn abort-btn' href='" . ezpz_ocb_sp('abort') . "'>&nbsp;&nbsp;&nbsp;Abort&nbsp;&nbsp;&nbsp;</a>";
				set_status('backup', 'Please wait...' . tab() . "A scheduled backup is in progress. $abort_link");
				update_option('ezpz_ocb_last_backup_time', $time);
				update_option('ezpz_ocb_backup_type', 'Scheduled');
				backup_in_progress('Scheduled');			
				require_once (EZPZOCB . '/functions/ezpz-ocb-run-backup.php');
			}
		}
	} else {
		// Leave error message about cron running too soon and schedule a one time cron task.
		$minutes = floor(($time - $last_backup) / 60);
		$next_time = (int)(round(($next_backup + 31) / 60) * 60);
		cron_log(" ->\t-- Twenty minute timeout test: FAILED!\n  ->        Next attempt will be " . wp_date($next_time) . "\n\n", true);
		$message = "<p>EZPZ OCB could not run the scheduled backup attempted on " . wp_date($time) . ". The last backup occurred only $minutes minutes prior to this attempt. EZPZ OCB requires at least 3 minutes between any backup and a scheduled backup to help reduce server load.</p>
			<p style='text-align: center;'>A new one-time backup is now scheduled for " . wp_date($next_time) . ".</p>
			<p style='text-align: center; font-weight: bolder;'>
			Your normal backup schedule will not be affected.</p>
			<p style='text-align: right; padding-right: 20px; font-size: 75%;'><a href='" . ezpz_ocb_sp('clr-error') . "'>Clear error messages</a></p>";
		file_put_contents(EZPZOCB . "/backups/data/error-status.$ezpz_ocb_data_key.php", $message);
		$ezpz_ocb_cron_lock = 'off';
		while($ezpz_ocb_cron_lock != 'off'){
			usleep(10000);
		}
		wp_schedule_single_event($next_time, 'ezpz_ocb_1time_cron');
	}
}

/***************			   	  Log Functions				***************/

function get_log_file($what = '') {
	$file = '/logs/log-' . get_option('ezpz_ocb_last_log') . '.php';
	if ($what != 'url') {
		return EZPZOCB . $file;
	} else {
		return EZPZOCB_URL . $file;
	}
}

function get_log_file_url() {
	//    $ajname = get_option('ezpz_ocb_ajname');
	//    $ajnow = $ajname['now'];
	//    $ajlast = $ajname['last'];

	return str_replace(EZPZOCB, EZPZOCB_URL, get_write_file());
}

function backup_cleanup($zip, $type) {
	$type = strtolower($type);
	set_status('backup', "Cleaning up the $type backup of $zip");
	update_option('ezpz_ocb_backup_type', 'none');
	set_status('backup', 'unset');
	if (file_exists(EZPZOCB . "/backups/in-progress.php")) {
		unlink(EZPZOCB . "/backups/in-progress.php");
	}
	if (file_exists(ezpz_ocb_backup_folder() . "/FILELOCK")) {
		unlink(ezpz_ocb_backup_folder() . "/FILELOCK");
	}

	manage_backups($zip);
	if (is_dir(ezpz_ocb_backup_folder() . "/running")) {
		$folder_path = ezpz_ocb_backup_folder() . "/running";
		$cmd = "rm -r $folder_path";
		exec($cmd);
	}
}

function ezpz_ocb_abort_backup() {
	$process = get_option('ezpz_ocb_running_process');
	
	if($process != ''){
		exec("kill -KILL $process");
	}
	
	$array = get_option('ezpz_ocb_current_backup_info');
	
	$abort_status = 'Aborting ' . str_replace(ezpz_ocb_backup_folder() . '/', '', $array['zip']) . '!';
	
	set_status('abort', $abort_status);
	
	update_option('ezpz_ocb_aborted', $array['id']);
	while(get_option('ezpz_ocb_aborted') != $array['id']){
		echo " .";
		usleep(100000);
	}

	if (file_exists($array['zip'])) {
		unlink($array['zip']);
	}
	
	if (file_exists($array['log'])) {
		unlink($array['log']);
	}
	
	if (file_exists(EZPZOCB . '/backups/logs/current/working.php')) {
		unlink(EZPZOCB . '/backups/logs/current/working.php');
	}
	
	if (is_dir(ezpz_ocb_backup_folder() . "/running")) {
		$folder_path = ezpz_ocb_backup_folder() . "/running";
		$cmd = "rm -r $folder_path";
		exec($cmd);
	}
	if (is_dir($array['temp'])) {
		$folder_path = $array['temp'];
		$cmd = "rm -r $folder_path";
		exec($cmd);
	}
	
	if (file_exists(ezpz_ocb_backup_folder() . "/EZPZOCB_README.html")) {
		unlink(ezpz_ocb_backup_folder() . "/EZPZOCB_README.html");
	}
	if (backup_in_progress('?')) {
		backup_in_progress('remove');
	}
	
//	if($array['type'] != 'manual'){				
		set_status('error', '<center><b>' . get_option('ezpz_ocb_zip_name') . ' Aborted!</b></center>');
		sleep(7);
/*	} else {
		$error_message = ezpz_ocb_sp() . '&aborted=' . bin2hex(get_option('ezpz_ocb_zip_name') . ' Aborted!');

	echo "
<script type='text/javascript'>
	var goHere = '$error_message';
	window.location = goHere;
</script>
				";
	update_option('ezpz_ocb_backup_type', 'none');
		
	} */
	
	if (file_exists(ezpz_ocb_backup_folder() . "/FILELOCK")) {
		unlink(ezpz_ocb_backup_folder() . "/FILELOCK");
	}	
}

function reset_ezpzocb(){
	global $ezpz_ocb_data_key;
	if (file_exists(ezpz_ocb_backup_folder() . "/FILELOCK")) {
		unlink(ezpz_ocb_backup_folder() . "/FILELOCK");
	}
	empty_folder(EZPZOCB . "/backups/logs");
	empty_folder(ezpz_ocb_backup_folder());
	update_option('ezpz_ocb_backup_type', 'none');
	file_put_contents(EZPZOCB . "/backups/data/directory.$ezpz_ocb_data_key.php", "Backup's cleared on " . wp_date());
}

function cron_log($content, $exit){
	if(!file_exists(EZPZOCB . "/backups/logs/cron/")){
		create_folder(EZPZOCB . "/backups/logs/cron/");
	}
	$tmp = EZPZOCB . "/backups/logs/cron/logTmp.hideMe";
	$log = EZPZOCB . "/backups/logs/cron/log.hideMe";
	if($exit){
		$tmp_output = file_get_contents($log);
		$output = explode('>>>', $tmp_output, 26);
		$cnt = 0;
		foreach ($output as $value){
			if ($cnt < 25 & strlen($value) >= 10){
				$log_content .= ">>>" . $value;
			}
			$cnt++;
		}
		file_put_contents($log, file_get_contents($tmp) . $content .  $log_content);
		if(file_exists($tmp)){
			unlink($tmp);
		}
	} else {
		file_put_contents($tmp, $content, FILE_APPEND);
	}
}

function ezpz_ocb_db_repair(){
	global $wpdb;
	$optimize = true;
	$okay = true;
	$problems = array();
	$tables = $wpdb->tables();
	$ckmrk = '&nbsp;<span style="font-family: tahoma, sans-serif; color: green; font-weight: 900;"> &#10004;<small>OK</small></span>';	

	// Sitecategories may not exist if global terms are disabled.
	if ( is_multisite() && ! $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->sitecategories'" ) )
		unset( $tables['sitecategories'] );

	$tables = array_merge( $tables, (array) apply_filters( 'tables_to_repair', array() ) ); // Return tables with table prefixes.

	// Loop over the tables, checking and repairing as needed.
	tmp_write("<div style='display: none;'>");
	foreach ( $tables as $table ) {
		$check = $wpdb->get_row("CHECK TABLE $table");

		tmp_write('<p>');
		if ( 'OK' == $check->Msg_text ) {
			/* translators: %s: table name */
			tmp_write(sprintf( __( 'The %s table is okay.' ), $table ));
		} else {
			/* translators: 1: table name, 2: error message, */
			tmp_write(sprintf( __( 'The %1$s table is not okay. It is reporting the following error: %2$s.  WordPress will attempt to repair this table&hellip;' ) , $table, "<code>$check->Msg_text</code>" ));

			$repair = $wpdb->get_row("REPAIR TABLE $table");

			tmp_write('<br />&nbsp;&nbsp;&nbsp;&nbsp;');
			if ( 'OK' == $check->Msg_text ) {
				/* translators: %s: table name */
				tmp_write(sprintf( __( 'Successfully repaired the %s table.' ), $table ));
			} else {
				/* translators: 1: table name, 2: error message, */
				tmp_write(sprintf( __( 'Failed to repair the  %1$s table. Error: %2$s' ), $table, "<code>$check->Msg_text</code>" ) . '<br />');
				$problems[$table] = $check->Msg_text;
				$okay = false;
			}
		}

		if ( $okay && $optimize ) {
			$check = $wpdb->get_row("ANALYZE TABLE $table");

			tmp_write('<br />&nbsp;&nbsp;&nbsp;&nbsp');
			if ( 'Table is already up to date' == $check->Msg_text )  {
				/* translators: %s: table name */
				tmp_write(sprintf( __( 'The %s table is already optimized.' ), $table ));
			} else {
				$check = $wpdb->get_row("OPTIMIZE TABLE $table");

				tmp_write('<br />&nbsp;&nbsp;&nbsp;&nbsp');
				if ( 'OK' == $check->Msg_text || 'Table is already up to date' == $check->Msg_text ) {
					/* translators: %s: table name */
					tmp_write(sprintf( __( 'Successfully optimized the %s table.' ), $table ));
				} else {
					/* translators: 1: table name, 2: error message, */
					tmp_write(sprintf( __( 'Failed to optimize the %1$s table. Error: %2$s' ), $table, "<code>$check->Msg_text</code>" ));
				}
			}
		}
		tmp_write('</p>');
	}
	tmp_write('</div>');

	if ( !empty($problems) ) {
		tmp_write(sprintf('<p>'.__('Some database problems could not be repaired. Please copy-and-paste the following list of errors to the <a href="%s">WordPress support forums</a> to get additional assistance.').'</p>', 'http://wordpress.org/support/forum/3'));
		$problem_output = array();
		foreach ( $problems as $table => $problem )
			$problem_output[] = "$table: $problem";
		tmp_write('<textarea name="errors" id="errors" rows="20" cols="60">' . esc_textarea( implode("\n", $problem_output) ) . '</textarea>');
	} else {
		tmp_write($ckmrk . "</li></ul>");
	}
}
?>