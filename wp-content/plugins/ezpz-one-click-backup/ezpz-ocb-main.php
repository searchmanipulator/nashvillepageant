<?php
global $ezpz_ocb_data_key;
$return_to_cpanel = "
				<script type='text/javascript'>
				var goHere = '" . ezpz_ocb_sp() . "';
					window.location = goHere;
				</script>
				";
$abort_link =  tab(3) . "<a style='' class='ezpz-btn abort-btn' href='" . ezpz_ocb_sp('abort') . "'>&nbsp;&nbsp;&nbsp;Abort&nbsp;&nbsp;&nbsp;</a>";

// get subpages
if (!isset($_GET['sp'])) {
	require_once (EZPZOCB . '/ezpz-ocb-cpanel.php');
} else {
	$subpage = $_GET['sp'];
	switch ($subpage) {
		case 'cpanel' :
			require_once (EZPZOCB . '/ezpz-ocb-cpanel.php');
			break;
		case 'backup' :
			update_option('ezpz_ocb_backup_type', 'Manual');
			sleep(1);
			require_once (EZPZOCB . '/ezpz-ocb-backup.php');
			break;
		case 'bu_ajax' :
			require_once (EZPZOCB . '/functions/ezpz-ocb-run-backup.php');
			break;
		case 'bg_backup' :
			if (file_exists(ezpz_ocb_backup_folder() . "/FILELOCK")){
				echo "
				<script type='text/javascript'>
				alert('Cannot perform a Background Backup because a " . get_option('ezpz_ocb_backup_type') . " Backup is currently running.');
				</script>
				" . $return_to_cpanel;			
				} else {
				file_put_contents(ezpz_ocb_backup_folder() . "/FILELOCK", "");
				if (get_option('ezpz_ocb_backup_type') == 'none') {
					update_option('ezpz_ocb_backup_type', 'Background');
					set_status('backup',  "Please wait..." . tab() . "A background backup is in progress. $abort_link");
					backup_in_progress('Background');
					while (!file_exists(EZPZOCB . "/backups/in-progress.php")){
						usleep(250000);
					}
					do_action('ezpz_ocb_run_bg_backup');
//					wp_schedule_single_event(time() - 60, 'ezpz_ocb_run_bg_backup');
					echo $return_to_cpanel;
				} else {
					echo "
				<script type='text/javascript'>
				alert('Cannot perform a Background Backup because a " . get_option('ezpz_ocb_backup_type') . " Backup is currently running.');
				</script>
				" . $return_to_cpanel;
				}
			}
			break;
		case 'zip':
			require_once (EZPZOCB . '/functions/ezpz-archive-cmd.php');
			break;
		case 'clr-error' :
			set_status('error', 'unset');
			break;
		case 'clr-transfer' :
			set_status('transfer', 'unset');
			break;
		case 'delete' :
			$zip_file = urldecode($_GET['zip']);
			delete_backup($zip_file);
			echo $return_to_cpanel;
			break;
		case 'abort' :
			ezpz_ocb_abort_backup();
			set_status('error', 'unset');
			update_option('ezpz_ocb_backup_type', 'none');
			echo $return_to_cpanel;
			break;
		case 'clr-bus' :
			reset_ezpzocb();
			echo $return_to_cpanel;
			break;
		default :
			echo $return_to_cpanel;
	}
}
?>