<?php
global $wpdb, $ezpz_ocb_cron_lock;

if (!ini_get('safe_mode')) {
	set_time_limit(900);
	// 900 seconds = 15 minutes
}

ezpz_ocb_extend_timeout();
set_status('error', 'unset');
get_backup_time('set');
if (!file_exists(ezpz_ocb_backup_folder() . "/running")) {
	create_folder(ezpz_ocb_backup_folder() . "/running");
} else {
	die();
}

//remove_old_backup();
$datestamp = ezpz_date('', get_option('ezpz_ocb_ds_format'));

set_new_date();

$bu_type = ucfirst(get_option('ezpz_ocb_backup_type'));

$bu_path_key = ezpz_ocb_rnd_alpha_numeric(8);

$log_time['total'] = time();
$startTime = $log_time['total'];

$db_prefix = $wpdb -> prefix;

$site_name = stripslashes(get_bloginfo('name'));
$excluded_list = get_option('ezpz_ocb_excluded_folders');
$prefix_only = get_option('ezpz_ocb_prefix_only');
$stylized = get_option('ezpz_ocb_stylized');

$blog_path = str_replace('/wp-content/plugins', '', WP_PLUGIN_DIR);
$wp_temp = explode('/', $blog_path);
$wp_dir = end($wp_temp);

$backup_dir = ezpz_ocb_backup_folder() . "/running";
$clean_name = clean_name();
$zip_name = $clean_name . get_zip_date() . '.zip';
$bu_log_path = EZPZOCB . "/backups/logs/current/" . get_backup_time() . "-" . strtolower($bu_type) . "." . $clean_name . get_zip_date() . '.php';
update_option('ezpz_ocb_zip_name', $zip_name);
$zip_file = ezpz_ocb_backup_folder() . '/' . $zip_name;
$zip_url = str_replace(ABSPATH, site_url() . '/', $zip_file);
$archive_name = clean_name() . get_zip_date();
$archive_dir = $backup_dir . "/" . clean_name() . get_zip_date();
$tmp_archive_dir = $backup_dir . "/EZPZ_RESTORATION_FILES";
$load_img = "<span class='load-img' style='display: inline;'>" . tab() . "<img src='" . EZPZOCB_URL . "/images/loading.gif' height='20' width='20' align='ABSMIDDLE' /></span>";
$alt_db = get_option('ezpz_ocb_db_dump');

$sql_file = "$tmp_archive_dir/EZPZ_DB.sql";

$save_path = $backup_dir;
$save_wpcontent_path = $blog_path . '/wp-content/';
$ckmrk = '&nbsp;<span style="font-family: tahoma, sans-serif; color: green; font-weight: 900;"> &#10004;<small>OK</small></span>';
$failed = '&nbsp;<span style="font-family: tahoma, sans-serif; color: darkred; font-weight: 900;"> X<small> FAILED</small></span>';
$bullet = '&nbsp;&nbsp;&nbsp;&nbsp;<img alt ="" src="' . site_url() . '/wp-content/plugins/' . ezpz_ocb_slug() . '/images/bullet.png" />&nbsp;&nbsp;';
if ($stylized != 'no') {
	$gold_span = "<span style='color: rgb(204,153,0);'>";
	$blue_span = "<span style='color: rgb(0,0,160); font-weight: bold;'>";
	$dkred_span = "<span style='color: darkred; font-size: 16px; font-family: Comic Sans MS, cursive, sans-serif; font-style: italic;'>";
} else {
	$gold_span = "<span>";
	$blue_span = "<span style='font-weight: bold;'>";
	$dkred_span = "<span>";
}

$running_div = "
<!--removeMe--><div id='running' style='text-align: center; display: block; background-color: #ff9999; max-height: 50px; min-height: 30px; z-index: 10; position: relative;
'> <p>Please&nbsp;do&nbsp;not&nbsp;close&nbsp;this&nbsp;page&nbsp;or&nbsp;navigate away&nbsp;until&nbsp;the&nbsp;backup&nbsp;completes.</p></div><!--removeMe-->";

$button_bar = "<div style='float: left;'><a style='text-align: center;' class='button' href='" . ezpz_ocb_sp('delete') . "&zip=" . urlencode($zip_name) . "'>Delete This Backup</a></div><div style='float: right; margin-right: 15px;' ><a class='button' href='$zip_url'>Download This Backup</a></div>";

update_option('ezpz_ocb_current_backup_info', array('id' => $bu_id, 'log' => $bu_log_path, 'temp' => $tmp_archive_dir, 'zip' => $zip_file));

tmp_write("
<style type='text/css'>
#hide-del-dl-bar {
	display: none;
}
#del-dl-bar {
	display: block;
}
</style>
$running_div
<div id='hide-del-dl-bar'>$button_bar</div>
<div style='height: 15px; width: 100%; clear: both;'></div>
<div><ol style='margin: 0 0 0 16px;'><li><b>First we need to gather some information about " . $gold_span . "<em>$site_name</em></span></b>", false);
usleep(500000);


$db_info = array(
	array('Database host', DB_HOST),
	array('Database name', DB_NAME),
	array('Database user', DB_USER),
	array('Database password', DB_PASSWORD)
);

foreach ($db_info as $db_key){
	if (pack('H*', bin2hex($db_key[1])) != $db_key[1]){
		tmp_write("<h3>Cannot decode " . $db_key[0] . "</h3>");
		$no_dump = true;
	}
}

if ($no_dump){
	tmp_write("<h3>Aborting backup...</h3>");
	sleep(5);
	ezpz_ocb_abort_backup();
	tmp_write("
		<script type='text/javascript'>
		var goHere = '" . ezpz_ocb_sp() . "';
			window.location = goHere;
		</script>
	");
}

tmp_write("<br/>" . $dkred_span . "Don't worry, sit back and I'll do all the work.</span></li>");
sleep(1);
tmp_write("
  <li><b>Calculating the size of $gold_span <em>" . stripslashes(get_bloginfo('name')) . "</em></span></b>");

$folder_size = get_folder_size(ABSPATH);

if (get_option('ezpz_ocb_excluded_folders') == 'none') {
	$folder_size['excluded'] = 0;
}
//echo "&nbsp; &raquo; <b>" . $folder_size['total']; // . "</b>$ckmrk</li>";
if (get_option('ezpz_ocb_excluded_folders') != 'none') {
	tmp_write("<br/>Total size: " . $folder_size['total'] . " | Excluding: " . $folder_size['excluded'] . " | Backing up: " . $folder_size['backup'] . "$ckmrk<br/></li>");
} else {
	tmp_write(tab() . $folder_size['total'] . " </b>$ckmrk</li>");
}

tmp_write("<li style='margin-top: 0px;'><b>Get the required database information.</b></li>");
sleep(1);

tmp_write("<ul class='ezpz-ul-circle'><li>DB Host: ");

tmp_write($blue_span . DB_HOST . "</span>" . $ckmrk . '</li><li>' . "DB Name: ");

tmp_write($blue_span . DB_NAME . "</span>" . $ckmrk . "</li><li>DB User: ");

tmp_write($blue_span . DB_USER . "</span>" . $ckmrk . '</li><li>' . "DB Password: ");

$bull_num = strlen(DB_PASSWORD);

tmp_write($blue_span . "<sub><span style='font-size: 14px; margin: -3px 0 3px 0; letter-spacing: 2px'> " . str_repeat("&bull;", $bull_num) . "</span></sub>$ckmrk");


if ( get_option('ezpz_ocb_db_repair') == 'yes') {
	$db_repair = ' repair and optimize the database,';
} else {
	$db_repair = '';
}

if ($prefix_only == "yes") {
	$db_txt = "Now let's get your $db_repair$blue_span $db_prefix</span> database tables and create";
	tmp_write("<li>DB Prefix: ");

	tmp_write($blue_span . $db_prefix . $ckmrk) . "</li>";
} else {
	$db_txt = "Now let's$db_repair get the database contents and create";
}
tmp_write("</li><li>Website URL: ");

tmp_write($blue_span . site_url() . "</span>" . $ckmrk . "</li><li>Server path to WordPress: ");

tmp_write($blue_span . $blog_path . "</span>" . $ckmrk . "</li></ul></li>");
tmp_write("<li><b>$db_txt the database backup file.</b>");

$log_time['db'] = time();
if ( get_option('ezpz_ocb_db_repair') == 'yes') {
	tmp_write("</li><ul class = 'ezpz-ul-circle' style='margin-top: 0px;'><li>Repairing and Optimizing your Database.");
	define('WP_REPAIRING', true);
	ezpz_ocb_db_repair();
	define('WP_REPAIRING', false);
}
tmp_write("</li><ul class = 'ezpz-ul-circle' style='margin-top: 0px;'><li>Archiving Your Database");

if (!file_exists(EZPZOCB . "/backups/index.html")) {

	file_put_contents(EZPZOCB . "/backups/index.html", forbidden());
}
create_folder("$tmp_archive_dir");
get_sql_dump($sql_file, true);
remove_load_img('getSql');
$log_time['db'] = convert_secs(time() - $log_time['db']);
if (file_exists($sql_file)) {
	$db_info = "and the database";
	tmp_write($ckmrk . "</li></ul>");
} else {
	$db_info = "<span style='color: darkred;'><s>and the database</s></span>";
	tmp_write($failed . "<div class='ezpz-ocb-warning'><b>EZPZ OCB</b> was unable to backup the database. This is usually
    due to <em>mysqldump</em> being disabled by your server. Please try the option <b>&quot;Use alternate database backup method&quot;</b>
    on <b>EZPZ OCB\'</b> options page. The backup process will continue but it will not be possible
    to include a database file during this backup.</div>");
}
tmp_write("<li><b>Finally we'll archive " . stripslashes(get_bloginfo('name')) . ", the restoration script $db_info ...</b>");

$log_time['archive']['tar'] = time();
$excluded_folders = "";
if ($excluded_list != "" && $excluded_list != "none") {
	if ($folder_size['excluded'] != 0) {
		tmp_write("<br/>Excluding the following folders (" . $folder_size['excluded'] . ") from the backup.<ul>");
	} else {
		tmp_write("<br/>EZPZ OCB will not exclude the following folders. They either do not exists or are empty.<ul>");
	}

	$excluded = explode(",", $excluded_list);

	foreach ($excluded as $item) {
		$item = trim($item);
		$item = trim($item, "/\*");

		$excluded_folders = $excluded_folders . "\*/$item/\* ./$item/\* ";
		$item = str_replace('~/', $wp_dir . '/', $item);
		tmp_write("<li style='display: inline'>" . tab(2) . "$blue_span $item </span></li>");
	}
	tmp_write("</ul>");
}

$excluded_extensions = ".jpg:.jpeg:.png:.gif:.tif:.tiff:.psd:.pspimage:.mp4:.mpg:.wmv:.rm:.vob:.swf:.flv:.avi:.divx:.mov:.pdf:.m4a:.mp3:.ra:.m4a:.ogg:.ace:.alz:.apz:.ar:.arc:.b64:.ba:.bz:.bz2:.cbr:.deb:.gz:.pkg:.rar:.rpm:.sfx:.sea:.sit:.sitx:.taz:.tgz:.war:.zip:.zipx:.zz";

$exclude_bu_dir = str_replace(EZPZOCB . "/backups/", "", $backup_dir);

$orig_dir = exec("pwd");
$bu_folder = "ezpz-one-click-backup/backups";
chdir(ABSPATH);
$backups_dir = EZPZOCB . '/backups';
$myDir = ABSPATH;
//copy(ABSPATH . '.htaccess', ABSPATH . 'EZPZOCB_htaccess');
$copy_cmd = "zip -r -n $excluded_extensions $tmp_archive_dir/$wp_dir.zip * -x \*$bu_folder/\* \*EZPZ_RUNNING $excluded_folders";
//$copy_cmd = "rsync -a  --exclude \*$bu_folder/\* --exclude \*EZPZ_RUNNING --exclude $excluded_folders $blog_path $tmp_archive_dir";

tmp_write("<ul class='ezpz-ul-circle' style='margin: 6px 0 0 -5px;'><li>Copying $site_name. " . show_load_img('copyProcess'));
$copyProcess = run_in_background("$copy_cmd");
update_option('ezpz_ocb_running_process', $copyProcess);
tmp_write("<!-- Copy process ID is $copyProcess -->");
while (is_process_running($copyProcess)) {
	tmp_write("<img alt ='' style='height: 12px; width: 4px; padding-left: 2px;' src='" . EZPZOCB_URL . "/images/seg.jpg' />");
	usleep(500000);
}
update_option('ezpz_ocb_running_process', '');
$add_htaccess_cmd = "zip -g $tmp_archive_dir/$wp_dir.zip .htaccess";
exec($add_htaccess_cmd);
remove_load_img('copyProcess');
chdir($orig_dir);
$log_time['archive']['tar'] = convert_secs(time() - $log_time['archive']['tar']);

tmp_write("$ckmrk</li></ul>");

generate_restore($tmp_archive_dir);
file_put_contents($backup_dir . "/EZPZOCB_README.html", restore_readme());
$orig_dir = exec("pwd");
chdir("$backup_dir");
tmp_write("<ul class='ezpz-ul-circle' style='margin: 6px 0 0 -5px;'><li>Compressing everything into &quot;$zip_name&quot;.  " . show_load_img('zipProcess'));
//	$zip_cmd = "zip -r $backup_dir/$zip_name * -x EZPZ_RUNNING index.html";
$zip_cmd = "zip -r -3 -n $excluded_extensions $backup_dir/$zip_name * -x index.html ";
$zipProcess = run_in_background("$zip_cmd");
update_option('ezpz_ocb_running_process', $zipProcess);
tmp_write("<!-- Zip process ID is $zipProcess -->");
while (is_process_running($zipProcess)) {
	tmp_write("<img alt ='' style='height: 12px; width: 4px; padding-left: 2px;' src='" . EZPZOCB_URL . "/images/seg.jpg' />");
	usleep(1500000);
}
update_option('ezpz_ocb_running_process', '');
remove_load_img('zipProcess');
tmp_write('<script type="text/javascript">document.getElementById("abort-btn").style.display = "none";</script>');

rename("$backup_dir/$zip_name", ezpz_ocb_backup_folder() . "/$zip_name");
while (file_exists("$backup_dir/$zip_name")) {
	usleep(250000);
}

chdir($orig_dir);

$log_time['archive']['zip'] = time();

$old_dir = getcwd();

$log_time['archive']['zip'] = convert_secs(time() - $log_time['archive']['zip']);
$backup_dir = ezpz_ocb_backup_folder();
if (file_exists("$backup_dir/$zip_name")) {
	$cmd = "rm -r $tmp_archive_dir";
	exec($cmd);
}

if (file_exists("$backup_dir/$zip_name")) {
	$zip_size = convert_bytes(filesize("$backup_dir/$zip_name"));
	tmp_write(" $ckmrk</li></ul>");
	tmp_write("<li><b>Backup compressed from " . $folder_size['backup'] . " to <!--zipsize-->$zip_size<!--zipsize-->.</b></li>");

	$dropbox = get_option('ezpz_ocb_dropbox');
	$ftp = get_option('ezpz_ocb_ftp');
	$ftp_target = $zip_name;
	$ftp_source = "$backup_dir/$zip_name";

	if ($ftp['active'] === 'active' || $ftp['active'] === 'true') {// Automatically save backup via FTP if elgible.
		if ($dropbox['active'] === 'active' || $dropbox['active'] === 'true') {
			$do_dropbox = true;
			$dropbox_args = array($backup_dir, $zip_name, ucfirst(get_option('ezpz_ocb_backup_type')), $bu_log_path, $bu_path_key);
		} else {
			$do_dropbox = false;
			$dropbox_args = (array)"";
		}
		$args = array($ftp_target, $ftp_source, $bu_path_key, $do_dropbox, $dropbox_args);
		wp_schedule_single_event(time(), 'ezpz_ocb_run_ftp', $args);
		tmp_write("<li><b>Initializing FTP transfer</b> (will run in the background).</li>");
	}
	if ($dropbox['active'] === 'active' || $dropbox['active'] === 'true') {// Automatically save backup to Dropbox if elgible.
		if ($ftp['active'] !== 'active' && $ftp['active'] !== 'true') {
			$args = array($backup_dir, $zip_name, ucfirst(get_option('ezpz_ocb_backup_type')), $bu_log_path, $bu_path_key);
			if($bu_type == 'Manual'){
			wp_schedule_single_event(time(), 'ezpz_ocb_run_dropbox', $args);
				spawn_cron(time() + 10);
			} else {
				wp_schedule_single_event(time() + 10, 'ezpz_ocb_run_dropbox', $args);
			}
		}
		tmp_write("<li><b>Initializing Dropbox transfer</b> (will run in the background).</li>");
	}
	$log_time['final'] = convert_secs(time() - $log_time['total']);
	$stopTime = time();
	$totalTime = convert_secs(($stopTime + 1) - $startTime);

	$rel_zip_path = str_replace(ABSPATH, '', $zip_file);
	$bu_location = "<p>The path to this backup relative to WordPress is:<br/>/$rel_zip_path</p>
	<!--removeMe--><div style='padding: 0 15px;'>$button_bar</div><!--removeMe-->
	<div style='clear: both;'></div>";
	tmp_write("</ol>$bu_location</div></body></html>");
}

$ezpz_ocb_cron_lock = 'off';
while ($ezpz_ocb_cron_lock != 'off') {
	usleep(10000);
}

stop_refresh($totalTime, $bu_log_path, $bu_path_key, $zip_file);

if (get_option('ezpz_ocb_backup_cron_email') != '' && $bu_type == "Scheduled") {
	$m_date = date(get_option('date_format') . ' \a\t ' . get_option('time_format'));
	$m_site = site_name();
	$bu_url = site_url() . "/wp-admin/admin.php?page=ezpz_ocb_download";
	$m_to = get_option('ezpz_ocb_backup_cron_email');
	$m_subject = "$bu_type backup of \"$m_site\" completed.";
	$m_headers = "From: \"$m_site (EZPZ OCB)\"<mail@" . getDomain() . ">";
	$m_body = "A " . strtolower($bu_type) . " backup of $m_site ran successfully on {$m_date}. \rYou may download this backup at $zip_url.";
	mail($m_to, $m_subject, $m_body, $m_headers);
}

function stop_refresh($totalTime, $bu_log_path, $bu_path_key, $zip_file) {
	$type = get_option('ezpz_ocb_backup_type');
	$last = EZPZOCB . "/backups/logs/current/last.php";

	$working = EZPZOCB . "/backups/logs/current/working.php";
	$now = time();
	$type = get_option('ezpz_ocb_backup_type');
	$date = date(get_option('date_format'), $now);
	$time = date(get_option('time_format'), $now);
	$new_message = "<b><span style='font-size: 14px;'>$type Backup Completed on $date at $time </span></b>";
	$old_ajax = file_get_contents(get_write_file());
	$new_ajax = str_replace("#ff9999", "#d5f9bb", $old_ajax);
	$new_ajax = str_replace("<div id='running' style='display: block;'>", "<div id='running' style='display: none;'>", $new_ajax);
	$new_ajax = str_replace("Please&nbsp;do&nbsp;not&nbsp;close&nbsp;this&nbsp;page&nbsp;or&nbsp;navigate away&nbsp;until&nbsp;the&nbsp;backup&nbsp;completes.", $new_message, $new_ajax);
	$dropbox = get_option('ezpz_ocb_dropbox');
	if ($dropbox['active'] === 'active' || $dropbox['active'] === 'true') {
		$new_ajax .= "<div style='clear: both; text-align: center; margin-left: auto; margin-right: auto;'><b><!--DBKEY[$bu_path_key]-->Dropbox transfer pending...<!--DBKEY[$bu_path_key]--></b></div>";
	}
	$ftp = get_option('ezpz_ocb_ftp');
	if ($ftp['active'] === 'active' || $ftp['active'] === 'true') {
		$new_ajax .= "<div style='clear: both; text-align: center; margin-left: auto; margin-right: auto;'><b><!--FTPKEY[$bu_path_key]-->FTP transfer pending...<!--FTPKEY[$bu_path_key]--></b></div>";
	}
	$new_ajax .= "
	
	
	<script type='text/javascript'>
		jQuery('#cpanelBtn').removeAttr('disabled'); 
		jQuery('#dbuBtn').removeAttr('disabled');
		clearTimeout(loopTime);
	</script>
	
	
<!--EOF-->";

	tmp_write($new_ajax, false);
	sleep(2);

	while (!tmp_write('?EOF?')) {
		usleep(100000);
	}
	rename($working, $bu_log_path);

	update_option('ezpz_ocb_backup_type', 'none');
	set_status('backup', 'unset');
	if (file_exists(EZPZOCB . "/backups/in-progress.php")) {
		unlink(EZPZOCB . "/backups/in-progress.php");
	}
	if (file_exists(ezpz_ocb_backup_folder() . "/FILELOCK")) {
		unlink(ezpz_ocb_backup_folder() . "/FILELOCK");
	}

	manage_backups($zip_file);
	if (is_dir(ezpz_ocb_backup_folder() . "/running")) {
		$folder_path = ezpz_ocb_backup_folder() . "/running";
		$cmd = "rm -r $folder_path";
		exec($cmd);
	}
}
?>