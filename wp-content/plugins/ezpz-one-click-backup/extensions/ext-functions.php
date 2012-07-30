<?php

function get_license_id($type) {
    if ($type == 'as3') {
        $id = md5(md5(sha1(md5(sha1(getDomain() . '-AS3')))));
        $id = preg_replace('/[^a-zA-Z0-9]/', '', $id);
        $id = substr($id, 0, 20);
        for ($i = 0; $i < strlen($id); $i += 4) {
            $temp_id = substr($id, $i, 4);
            $new_id = $new_id . $temp_id . '-';
        }
        $id = substr($new_id, 0, strlen($new_id) - 1);
    }
    return $id;
}

// Check for existence of extensions and include them if they exist

if (has_ext('dbx')) {
    require_once(EZPZOCB . "/extensions/Dropbox/dbx-functions.php");
}

function ftp_me($ftp_target, $ftp_source, $bu_path_key, $do_dropbox, $dropbox_args) {
	ezpz_ocb_extend_timeout();
	global $ezpz_ocb_data_key;
	if (!ini_get('safe_mode')) {
		ignore_user_abort(true);
	}
	$source_size = convert_bytes(filesize($ftp_source));
    $zip_name = str_replace(ezpz_ocb_backup_folder() . '/', '', $ftp_source);

    $ftp = get_option('ezpz_ocb_ftp');

    if ($ftp['active'] === 'active' || $ftp['active'] === 'true') {
        $folder = explode('/', $ftp['folder']);

        if ($conn = ftp_connect($ftp['host'])) {

            if (ftp_login($conn, $ftp['user'], $ftp['pass'])) {
            	
				set_status('transfer', "Uploading $ftp_target ($source_size) to FTP account.");

                ftp_pasv($conn, true);

                foreach ($folder as $item) {
                    if (ftp_folder_exists($conn, $item)) {
                        ftp_chdir($conn, $item);
                    } else {
                        ftp_mkdir($conn, $item);
                        ftp_chdir($conn, $item);
                    }
                }

                if (ftp_put($conn, $ftp_target, $ftp_source, FTP_BINARY)) {
                	$m_date = date('g:ia \o\n M jS, Y', ezpz_time());
					$m_site = site_name();
					$m_to = $ftp['mail'];
					$m_subject = "FTP backup added.";
					$m_headers = "From: \"$m_site (EZPZ OCB)\"<mail@" . getDomain() . ">";
					if (!$cron) {
						$m_body = "A backup of $m_site ran successfully at {$m_date}. \r\n\r\n$zip_name was uploaded via FTP to " . $ftp['folder'] . " at " . $ftp['host'] . "";
					} else {
						$m_body = "A scheduled backup of $m_site ran successfully at {$m_date}. \r\n\r\n$zip_name was uploaded via FTP to " . $ftp['folder'] . " at " . $ftp['host'] . "";
					}
					mail($m_to, $m_subject, $m_body, $m_headers);
					set_status('transfer', "unset");
					if (strlen($zip_name) > 50) {
						$completed = "<span style='color: green;'><small>FTP upload of $ftp_target ($source_size) completed.</small></span>";
					} else {
						$completed = "<span style='color: green;'>FTP upload of $ftp_target ($source_size) completed.</span>";
					}
					set_status('transfer', $completed);
					$tmpData = file_get_contents(EZPZOCB . "/backups/data/directory.$ezpz_ocb_data_key.php");
					$tmpData = str_replace("<!--FTPKEY[$bu_path_key]-->FTP transfer pending...<!--FTPKEY[$bu_path_key]-->", "<span style='color: green;'>FTP transfer completed on " . wp_date() . "</span>", $tmpData);
					file_put_contents(EZPZOCB . "/backups/data/directory.$ezpz_ocb_data_key.php", $tmpData);
					unset($tmpData);
					if(strtolower($type) == 'manual'){
						sleep(15);
						set_status('transfer', 'unset');
					}
				} else {
					set_status('error', "<p>FTP upload failed for unknown reason. ($ftp_target)</p>");
					return;
				}
				ftp_close($conn);
            } else { // login fail
                set_status('error', "<p>FTP login failed. Please check login information under \"Extension Settings\".</p>");
                ftp_close($conn);
            }
        } // connection fail
    }
	if ($do_dropbox) {
		wp_schedule_single_event(time(),  'ezpz_ocb_run_dropbox', $dropbox_args);
	}
    return;
}

function ftp_folder_exists($conn, $dir) {
// Get the current working directory
    $origin = ftp_pwd($conn);

// Attempt to change directory, suppress errors
    if (@ftp_chdir($conn, $dir)) {
// If the directory exists, set back to origin
        ftp_chdir($conn, $origin);
        return true;
    }

// Directory does not exist
    return false;
}


?>