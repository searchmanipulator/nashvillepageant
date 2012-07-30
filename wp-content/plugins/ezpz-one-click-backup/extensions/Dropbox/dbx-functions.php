<?php

function dropbox_me($backup_dir, $zip_name, $type, $bu_log_path, $bu_path_key, $cron = false) {
	global $wpdb, $ezpz_ocb_data_key;
	$zip_size = convert_bytes(filesize("$backup_dir/$zip_name"));
	if(strlen($zipname) > 40) {
		$working = "<small>Uploading $zip_name ($zip_size) to Dropbox.</small>";
	} else {
		$working = "Uploading $zip_name ($zip_size) to Dropbox.";
	}

	if (!ini_get('safe_mode')) {
		set_time_limit(1200);
		// 1200 seconds = 20 minutes
		ignore_user_abort(true);
	}

	$ckmrk = '&nbsp;<span style="font-family: tahoma, sans-serif; color: green; font-weight: 900;"> &#10004;<small>OK</small></span>';

	$dropbox = get_option('ezpz_ocb_dropbox');
	if ($dropbox['active'] === 'active' || $dropbox['active'] === 'true') {
		$ezpzocb = site_url() . '/wp-content/plugins/ezpz-one-click-backup';
		$drpbximg = "<img style='margin: 2px 2px -2px 2px; height: 15px; width: 58px;' src='$ezpzocb/images/dropbox.png' />";

		require_once EZPZOCB . '/extensions/Dropbox/dbupload.php';
		try {
			$uploader = new EZPZDBXUploader($dropbox['mail'], urldecode($dropbox['pass']));
		} catch(Exception $e) {
			set_status('error', $e -> getMessage());
			set_status('transfer', 'unset');
//			file_put_contents($status_file, $e -> getMessage());
			die();
		}
		set_status('transfer', $working);
		if (filesize("$backup_dir/$zip_name") > 1048576 * 200 && $dropbox['split'] != 'split') {
			$zipSize = convert_bytes(filesize("$backup_dir/$zip_name"));
			$dropbox['split'] = 'split';
			if (strlen($dropbox['os']) < 4) {
				$dropbox['os'] = 'both';
			}
			update_option('ezpz_ocb_dropbox', $dropbox);
		}

		if ($dropbox['split'] == 'split') {
			if (filesize("$backup_dir/$zip_name") > 1048576 * 200) {
				$Mb = 50;
				$split = true;
				$new_folder = str_replace(".zip", "", $zip_name);
				$pieces = floor(filesize("$backup_dir/$zip_name") / (1048576 * $Mb)) + 1;

				split_file($backup_dir, $zip_name, $new_folder, $Mb);
			} else {
				$split = false;
			}
		} else {
			$split = false;
		}

		$subfolder = "$type Backups";

		if (!$split) {
			$db_time = time();
			$uploader = new EZPZDBXUploader($dropbox['mail'], urldecode($dropbox['pass']));
			try {
				$uploader -> upload("$backup_dir/$zip_name", "EZPZ OCB Backups/" . $dropbox['folder'] . "/$subfolder");
			} catch(Exception $e) {
				set_status('error', $e -> getMessage());
				set_status('transfer', 'unset');
//				file_put_contents($status_file, $e -> getMessage());
				die();
			}

		} else {
			$db_time = time();
			for ($i = 0; $i < $pieces; $i++) {
				$ii = str_pad($i + 1, 3, "0", STR_PAD_LEFT);
				$uploader = new EZPZDBXUploader($dropbox['mail'], urldecode($dropbox['pass']));
				try {
					$uploader -> upload("$backup_dir/$new_folder/part-$ii.2ez", "EZPZ OCB Backups/" . $dropbox['folder'] . "/$subfolder/$new_folder");
				} catch(Exception $e) {
					set_status('error', $e -> getMessage());
					set_status('transfer', 'unset');
					die();
				}
			}
			$batch_files = create_batch($new_folder, $pieces, $subfolder, $new_folder, $zip_name);
			$cmd = "rm -r $backup_dir/$new_folder";
			exec($cmd);
		}
		if ($dropbox['alert'] === 'alert') {
			$m_date = date('g:ia \o\n M jS, Y', ezpz_time());
			$m_site = site_name();
			$m_to = get_option('admin_email');
			$m_subject = "Dropbox backup added.";
			if (!$cron) {
				$m_body = "A manual backup of $m_site ran successfully at $m_date and $zip_name was uploaded to your Dropbox account.";
			} else {
				$m_body = "A scheduled backup of $m_site ran successfully at $m_date and $zip_name was uploaded to your Dropbox account.";
				mail($m_to, $m_subject, $m_body);
			}
		}
		while (!file_exists($bu_log_path)) {
			sleep(5);
		}

		$tmpData = file_get_contents($bu_log_path);
		$tmpData = str_replace("<!--DBKEY[$bu_path_key]-->Dropbox transfer pending...<!--DBKEY[$bu_path_key]-->", "<span style='color: green;'>Dropbox transfer completed on " . wp_date() . "</span>", $tmpData);
		file_put_contents($bu_log_path, $tmpData);
	unset($tmpData);
		$tmpData = file_get_contents(EZPZOCB . "/backups/data/directory.$ezpz_ocb_data_key.php");
		$tmpData = str_replace("<!--DBKEY[$bu_path_key]-->Dropbox transfer pending...<!--DBKEY[$bu_path_key]-->", "<span style='color: green;'>Dropbox transfer completed on " . wp_date() . "</span>", $tmpData);
		//		$tmpData = $tmpData . "----------DB Key = $bu_path_key -----------";
		file_put_contents(EZPZOCB . "/backups/data/directory.$ezpz_ocb_data_key.php", $tmpData);
		unset($tmpData);

		if (!get_status('error?')) {
			if(strlen($zipname) > 40) {
				$completed = "<span style='color: green;'><small>Dropbox upload of $zip_name ($zip_size) completed.</small></span>";
			} else {
				$completed = "<span style='color: green;'>Dropbox upload of $zip_name ($zip_size) completed.</span>";
			}
			set_status('transfer', $completed);
		}
		if(strtolower($type) == 'manual'){
			sleep(15);
			set_status('transfer', 'unset');
		}
	}
}

function create_batch($file_name, $pieces, $subfolder, $new_folder, $zip_name) {

	$file_name = preg_replace("#[^0-9a-zA-Z]#", "_", $file_name);
	$dropbox = get_option('ezpz_ocb_dropbox');
	if ($dropbox['os'] == 'windows' || $dropbox['os'] == 'both') {
		$output = "copy /b ";
		$output2 = "";
		for ($i = 1; $i < $pieces + 1; $i++) {
			$ii = str_pad($i, 3, "0", STR_PAD_LEFT);
			$output .= "part-$ii.2ez + ";
			$output2 .= "del part-$ii.2ez\n";
		}
		$output = substr($output, 0, strlen($output) - 2);
		$output .= "$file_name.zip";
		$output = $output . "\n" . $output2 . "del Merge_Files_Linux.sh\ndel Merge_Files_Windows.bat";
		file_put_contents(ezpz_ocb_backup_folder() . "/$new_folder/Merge_Files_Windows.bat", $output);

		$uploader = new EZPZDBXUploader($dropbox['mail'], urldecode($dropbox['pass']));
		$uploader -> upload(ezpz_ocb_backup_folder() . "/$new_folder/Merge_Files_Windows.bat", "EZPZ OCB Backups/" . $dropbox['folder'] . "/$subfolder/$new_folder");
	}
	if ($dropbox['os'] == 'linux' || $dropbox['os'] == 'both') {
		$output = "cat ";
		$output2 = "";
		for ($i = 1; $i < $pieces + 1; $i++) {
			$ii = str_pad($i, 3, "0", STR_PAD_LEFT);
			$output .= "part-$ii.2ez ";
			$output2 .= "\nrm part-$ii.2ez";
		}
		$output .= "> $file_name.zip";
		$output = $output . $output2 . "\nrm Merge_Files_Windows.bat\nrm Merge_Files_Linux.sh";
		file_put_contents(ezpz_ocb_backup_folder() . "/$new_folder/Merge_Files_Linux.sh", $output);
		$uploader = new EZPZDBXUploader($dropbox['mail'], urldecode($dropbox['pass']));
		$uploader -> upload(ezpz_ocb_backup_folder() . "/$new_folder/Merge_Files_Linux.sh", "EZPZ OCB Backups/" . $dropbox['folder'] . "/$subfolder/$new_folder");
	}
	return true;
}

/* ------------------------------------------------------------------
 -                           SPLIT_FILE                                  -
 -  This function splits a file into equal pieces of less than $Mb           -
 -------------------------------------------------------------------- */

function split_file($file_folder, $file, $new_folder_name = "", $Mb = "25") {

	if (!file_exists("$file_folder/$new_folder_name") && $new_folder_name != "") {
		create_folder("$file_folder/$new_folder_name");
	} else {
		return null;
	}

	$file_name = "$file_folder/$file";
	$parts_num = floor(filesize($file_name) / ($Mb * 1048576)) + 1;
	$F_read = fopen($file_name, 'rb') or die("error opening file");
	$file_size = filesize($file_name);
	$parts_size = floor($file_size / $parts_num);
	$modulus = $file_size % $parts_num;
	for ($i = 0; $i < $parts_num; $i++) {
		if ($modulus != 0 & $i == $parts_num - 1) {
			$Write_me = fread($F_read, $parts_size + $modulus) or die("error reading file");
		} else {
			$Write_me = fread($F_read, $parts_size) or die("error reading file");
		}

		$ii = str_pad(($i + 1), 3, "0", STR_PAD_LEFT);
		$F_write = fopen("$file_folder/$new_folder_name/part-$ii.2ez", 'wb') or die("error opening file for writing");
		fwrite($F_write, $Write_me) or die("error writing split file");
		unset($Write_me);
		fclose($F_write) or die("error closing file handle");
	}
	fclose($F_read) or die("error closing file handle");
	return $parts_num;
}

///* ------------------------------------------------------------------
//  -                           MERGE                                  -
//  - This function merges split files -                             -
//  -------------------------------------------------------------------- */
//
//function merge_file($merged_file_name, $parts_num) {
//
//    $handle2 = fopen("$merged_file_name", 'ab') or die("error creating/opening merged file");
//    for ($i = 0; $i < $parts_num; $i++) {
//        $ii = str_pad(($i + 1), 2, "0", STR_PAD_LEFT);
//        $file_size = filesize("$merged_file_name(part_0$ii)");
//        $handle = fopen("$merged_file_name.$ii", 'rb') or die("error opening file");
////        $handle = fopen('splited_' . $i, 'rb') or die("error opening file");
//        fwrite($handle2, fread($handle, $file_size)) or die("error writing to merged file");
//        fclose($handle);
////        $content .= fread($handle, $file_size) or die("error reading file");
//    }
////write content to merged file
////    $handle2 = fopen($merged_file_name, 'ab') or die("error creating/opening merged file");
////    fwrite($handle2, $content) or die("error writing to merged file");
//    fclose($handle2);
//    return 'OK';
//}
?>
