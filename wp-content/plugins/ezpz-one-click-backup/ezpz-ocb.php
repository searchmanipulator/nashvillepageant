<?php

/*
 Plugin Name: EZPZ One Click Backup
 Plugin URI: http://ezpzsolutions.net/ezpz-wordpress-plugins/ezpz-one-click-backup
 Description: EZPZ One Click Backup (<strong>Now with completely revamped control panel, better cross-browser compatibility and multiple backup capability</strong>) is a very easy way to do a complete backup of your entire WordPress site. In fact it's so easy there are absolutely no required user settings, everything's automatic but there are many options to customize your backup the way you want.
 Author: Joe 'UncaJoe' Cook
 Version: 12.03.10
 Author URI: http://ezpzsolutions.net
 */

/*  Copyright 2011-12

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// update #3001

if(!isset($ezpz_ocb_cron_lock)){
	$ezpz_ocb_cron_lock = 'off';
}

function ezpz_ocb_release_num() {
	return "12.03.10";
}

require_once ('functions/ezpz-ocb-functions.php');

// EZPZ OCB Defines

if (!defined('EZPZOCB'))
	define('EZPZOCB', WP_PLUGIN_DIR . '/ezpz-one-click-backup');

if (!defined('EZPZOCB_URL'))
	define('EZPZOCB_URL', WP_PLUGIN_URL . '/ezpz-one-click-backup');

if (!defined('WP_ALLOW_REPAIR'))
	define('WP_ALLOW_REPAIR', true);;

$ezpz_ocb_data_key = get_option('ezpz_ocb_backup_folder_name');

if (!file_exists(EZPZOCB . '/backups')) {
	create_folder(EZPZOCB . '/backups');
}

if (!file_exists(EZPZOCB . "/backups/logs/current/")) {
	create_folder(EZPZOCB . "/backups/logs/current/");
}

if (!file_exists(EZPZOCB . "/backups/data/")) {
	create_folder(EZPZOCB . "/backups/data/");
}

register_activation_hook(__FILE__, 'ezpz_ocb_installer');

register_deactivation_hook(__FILE__, 'ezpz_ocb_uninstall');

add_action('admin_menu', 'ezpz_ocb_plugin_menu');

add_action('admin_notices', 'ezpz_ocb_admin_notices');

add_action('ezpz_ocb_cpanel', 'ezpz_ocb_cpanel');

add_action('ezpz_ocb_run_bg_backup', 'ezpz_ocb_run_bg_backup');

// add_action('save_post', 'ezpz_ocb_run_cron');

add_action('ezpz_ocb_cron', 'ezpz_ocb_run_cron');

add_action('ezpz_ocb_1time_cron', 'ezpz_ocb_run_cron');

add_action('ezpz_ocb_run_dropbox', 'ezpz_ocb_run_dropbox', 10, 5);

add_action('ezpz_ocb_run_ftp', 'ezpz_ocb_run_ftp', 10, 5);

add_action('ezpz_ocb_bu_cleanup', 'ezpz_ocb_bu_cleanup', 10, 2);

add_filter('cron_schedules', 'ezpz_ocb_custom_cron_schedules');

wp_enqueue_script('jquery');

wp_enqueue_script('jquery-ui-core');

wp_enqueue_script('thickbox',null,array('jquery'));

wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');

if(get_option('ezpz_ocb_set_cron') != 'off'){
	if(wp_next_scheduled('ezpz_ocb_1time_cron')){
		set_status('cron',"Next scheduled backup: " . wp_date(wp_next_scheduled('ezpz_ocb_1time_cron')) . " (One-time)");
	} else {
		set_status('cron',"Next scheduled backup: " . wp_date(wp_next_scheduled('ezpz_ocb_cron')));
	}	
} else {
	set_status('cron', "Scheduled backups are turned off.");
}

function ezpz_ocb_installer() {

	// Compatibility pre-checks

	$die_message = '';
	$server_os = php_uname('s');

	if (strtolower(substr($server_os, 0, 7)) == 'windows') {
		$windows_os = true;
		$die_message = $die_message . "<li>EZPZ One Click Backup is not compatible with your " . php_uname('s') . " server.</li>";
	}

	if (!function_exists('exec') && !$windows_os) {
		$die_message = $die_message . "<li>Your $server_os server has disabled the <em>exec</em> function.</li>";
	}

	if ($die_message != '') {
		die($die_message);
	}

	global $wpdb;

	clean_db_options();
	
	reset_ezpzocb();

	$plugin_bu_path = EZPZOCB . "/backups";

	if (!file_exists($plugin_bu_path)) {
		create_folder("$plugin_bu_path");
	}

	if (get_option('ezpz_ocb_hide_backup') != 'yes') {
		if (!file_exists($plugin_bu_path . '/htaccess.txt')) {
			file_put_contents($plugin_bu_path . '/htaccess.txt', "deny from all");
		}
	} else {
		if (!file_exists($plugin_bu_path . '/.htaccess')) {
			file_put_contents($plugin_bu_path . '/.htaccess', "deny from all");
		}
	}

	// Set random folder name and path
	if (!get_option('ezpz_ocb_backup_folder_name')) {
		ezpz_ocb_backup_folder();
	}
	$backup_folder_name = get_option('ezpz_ocb_backup_folder_name');
	$backup_dir = EZPZOCB . '/backups/' . $backup_folder_name;

	if (!file_exists($backup_dir)) {
		create_folder("$backup_dir");
	}
	
	update_option('ezpz_ocb_cron_lock', array('on', ((float) get_microtime() + 7200))); // Delay any scheduled backup by 2 hours

	$zip_date = "2011-01-01";

	set_new_date($zip_date);

	$available_extensions = array(3, array('FTP Extension', 'ftp'), array('Dropbox Extension', 'dbx'), array('Coming Soon... Amazon S3', 'as3'));
	// Coming Soon

	update_option('ezpz_ocb_available_extensions', $available_extensions);
}

function ezpz_ocb_uninstall() {

	global $wpdb;

	wp_clear_scheduled_hook('ezpz_ocb_cron');

	wp_clear_scheduled_hook('ezpz_ocb_bg_backup');

	wp_clear_scheduled_hook('ezpz_ocb_updates');

	remove_action('admin_notices', 'ezpz_ocb_admin_notices');
}

function ezpz_ocb_plugin_menu() {

	add_menu_page('EZPZ One Click Backup', 'EZPZ OCB', 'activate_plugins', 'ezpz_ocb', 'ezpz_ocb_main');
}

function ezpz_ocb_main() {
	require_once (EZPZOCB . '/ezpz-ocb-main.php');
	//get the main page.
}

function ezpz_ocb_cpanel() {
	require_once (EZPZOCB . '/ezpz-ocb-cpanel.php');
	//get the cpanel page.
}

function ezpz_ocb_bu_cleanup($zip, $type){
	backup_cleanup($zip, $type);
}

function ezpz_ocb_run_cron() {
	global $ezpz_ocb_cron_lock;
	$time = get_microtime();
	// Bug fix for multiple wp-cron executions
	if($ezpz_ocb_cron_lock != 'on'){
		$ezpz_ocb_cron_lock = 'on';
		$cron_time = $time - 100;
	} else {
		usleep(5000);
		$cron_time = get_microtime();
		$ezpz_ocb_cron_lock = 'skip';
	}
	cron_log(">>>\t    Accessed on " . date('F jS, Y H:i:s.', (float) get_microtime()) . get_thousandths_time() . "\n\n");
	
	if($cron_time > $time){
		exit(cron_log("\n---->    " . date('F jS, Y H:i:s.') . get_thousandths_time() . " - STOPPED DUPLICATE ATTEMPT! \n            time = $time | cron_time = $cron_time | cron_lock = '" . $ezpz_ocb_cron_lock . "'\n\n     " . str_repeat('_', 60) . "\n\n"));
	}
	

	global $ezpz_ocb_data_key;
	$last_backup = (int) get_option('ezpz_ocb_last_backup_time');
	$next_backup = $last_backup + 1200;
	// Make sure it's been at least 20 minutes (1200 seconds) since last schedualed backup ran.
	if ($time > $next_backup) {
		cron_log("\t    Twenty minute timeout test: PASSED!\n\n\t    Beginning backup at " . wp_date(time())  . ".\n\n     " . str_repeat('_', 60) . "\n\n", true);
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
		$minutes = round(($time - $last_backup) / 60);
		$next_time = round(round(($next_backup + 35) / 60) * 60);
		wp_schedule_single_event($next_time, 'ezpz_ocb_1time_cron');
		cron_log("\t    Twenty minute timeout test: FAILED!\n\t    Next attempt will be " . wp_date($next_time) . "\n     " . str_repeat('_', 60) . "\n\n" , true);
		$message = "<p>EZPZ OCB could not run the scheduled backup attempted on " . wp_date($time) . ". The last backup occurred only $minutes minutes prior to this attempt. EZPZ OCB requires at least 20 minutes between any backup and a scheduled backup to help reduce server load.</p>
			<p style='text-align: center;'>A new one-time backup is now scheduled for " . wp_date($next_time) . ".</p>
			<p style='text-align: center; font-weight: bolder;'>
			Your normal backup schedule will not be affected.</p>";
		file_put_contents(EZPZOCB . "/backups/data/error-status.$ezpz_ocb_data_key.php", $message);
		$ezpz_ocb_cron_lock = 'off';
		while($ezpz_ocb_cron_lock != 'off'){
			usleep(10000);
		}
	}
}

function ezpz_ocb_run_bg_backup() {
	require_once (EZPZOCB . '/functions/ezpz-ocb-run-backup.php');
}

function ezpz_ocb_run_dropbox($backup_dir, $zip_name, $bu_type, $bu_log_path, $bu_path_key) {
	dropbox_me($backup_dir, $zip_name, $bu_type, $bu_log_path, $bu_path_key);
}

function ezpz_ocb_run_ftp($ftp_target, $ftp_source, $bu_path_key, $do_dropbox, $dropbox_args){
	ftp_me($ftp_target, $ftp_source, $bu_path_key, $do_dropbox, $dropbox_args);
}

function ezpz_ocb_reset_backup() {
	reset_backup();
}

function reset_ezpz_ocb_options() {

	update_option('ezpz_ocb_release_num', ezpz_ocb_release_num());

	$blog_tz = get_option('timezone_string');
	if ($blog_tz == '') {
		$blog_tz = 'GMT';
	}

	if (!get_option('ezpz_ocb_zip_date')) {
		update_option('ezpz_ocb_zip_date', $zip_date);
	}

	if (!get_option('ezpz_ocb_backup_folder_name')) {
		update_option('ezpz_ocb_backup_folder_name', $backup_folder_name);
	}

	if (!get_option('ezpz_ocb_set_cron')) {
		update_option('ezpz_ocb_set_cron', 'off');
	}

	if (!get_option('ezpz_ocb_cron_time')) {
		update_option('ezpz_ocb_cron_time', '0');
	}

	if (!get_option('ezpz_ocb_prefix_only')) {
		update_option('ezpz_ocb_prefix_only', 'no');
	}
	if (!get_option('ezpz_ocb_excluded_folders')) {
		update_option('ezpz_ocb_excluded_folders', 'none');
	}

	if (!get_option('ezpz_ocb_stylized')) {
		update_option('ezpz_ocb_stylized', 'yes');
	}

	if (!get_option('ezpz_ocb_save_tz')) {
		update_option('ezpz_ocb_save_tz', $blog_tz);
	}

	if (!get_option('ezpz_ocb_ds_format')) {
		update_option('ezpz_ocb_ds_format', 'Y-m-d');
	}

	if (!get_option('ezpz_ocb_hide_backup')) {
		update_option('ezpz_ocb_hide_backup', 'no');
	}

	if (!get_option('ezpz_ocb_max_allowed')) {
		update_option('ezpz_ocb_max_allowed', 5);
	}

	if (get_option('ezpz_ocb_set_cron') != 'off') {
		$tempTime = date('U', mktime(get_option('ezpz_ocb_cron_time'), 0, 0, date('n'), date('j'), date('Y')) + 86400);
		wp_schedule_event($tempTime, get_option('ezpz_ocb_set_cron'), 'ezpz_ocb_cron');
	}

	if (!get_option('ezpz_ocb_ftp')) {
		$folder = substr(clean_name(), 0, strlen(clean_name()) - 1);
		$ftp = array('active' => '', 'alert' => '', 'host' => '', 'user' => '', 'pass' => '', 'folder' => '', 'count' => 1);
		update_option("ezpz_ocb_ftp", $ftp);
	}

	if (!get_option('ezpz_ocb_permissions')) {
		$folder_perms = '0755';

		$file_perms = '0644';
	} else {
		$perms = get_option('ezpz_ocb_permissions');
		$folder_perms = $perms['folders'];
		$file_perms = $perms['files'];
	}

	$permissions = array('folders' => $folder_perms, 'files' => $file_perms, 'def_folder' => '0755', // $def_folder_perms,
		'def_file' => '0644');
	// $def_file_perms);
	update_option('ezpz_ocb_permissions', $permissions);

	// Ensure all EZPZ OCB folders and files have the proper permissions

	set_permissions();
}

// Ensure all folders and files have the proper permissions
//set_permissions();	// Format = ('Folder', 'File') Default ('0755', '0644')

// Ensure that all EZPZOCB folders have an index.html file
foreach (list_ezpz_ocb_folders() as $item) {
	if (!file_exists($item . '/index.html')) {
		file_put_contents($item . '/index.html', forbidden());
	}
}

//get the extensions functions page if it exists.
if (file_exists(EZPZOCB . '/extensions/ext-functions.php'))
	require ('extensions/ext-functions.php');

if (get_option('ezpz_ocb_release_num') != ezpz_ocb_release_num()) {
	reset_ezpz_ocb_options();
}

function clean_db_options() {
	global $wpdb;
	$needed_options = array('ezpz_ocb_available_extensions', 'ezpz_ocb_backup_count', 'ezpz_ocb_backup_cron_email', 'ezpz_ocb_backup_folder_name', 'ezpz_ocb_backup_in_progress', 'ezpz_ocb_backup_time', 'ezpz_ocb_backup_type', 'ezpz_ocb_cron_lock', 'ezpz_ocb_cron_time', 'ezpz_ocb_current_backup_time', 'ezpz_ocb_db_dump', 'ezpz_ocb_db_repair', 'ezpz_ocb_dropbox', 'ezpz_ocb_dropbox_args', 'ezpz_ocb_ds_format', 'ezpz_ocb_excluded_folders', 'ezpz_ocb_ftp', 'ezpz_ocb_last_backup_time', 'ezpz_ocb_last_log',  'ezpz_ocb_max_allowed', 'ezpz_ocb_options_array', 'ezpz_ocb_permissions', 'ezpz_ocb_prefix_only', 'ezpz_ocb_save_tz', 'ezpz_ocb_set_cron', 'ezpz_ocb_stylized', 'ezpz_ocb_release_num', 'ezpz_ocb_zip_date', 'ezpz_ocb_zip_name');
	
	update_option('ezpz_ocb_options_array' , $needed_options);

	$stored_options = $wpdb -> get_results("SELECT * FROM $wpdb->options WHERE option_name LIKE 'ezpz_ocb_%' ");
	$current_options = array();
	foreach ($stored_options as $option) {
		array_push($current_options, $option -> option_name);
	}

	$delete_these = array_diff($current_options, $needed_options);
	foreach ($delete_these as $key) {
		delete_option($key);
	}
}
?>