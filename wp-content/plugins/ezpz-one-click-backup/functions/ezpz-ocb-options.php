<?php
global $wpdb;
global $ezpz_ocb_data_key;
$ezpz_date = ezpz_date();
$ezpz_tz = get_option('ezpz_ocb_save_tz');



// variables for the field and option names
$opt_name = array(
  'db_dump' => 'ezpz_ocb_db_dump',
  'db_repair' => 'ezpz_ocb_db_repair',
  'set_cron' => 'ezpz_ocb_set_cron',
  'cron_time' => 'ezpz_ocb_cron_time',
  'cron_day' => 'ezpz_ocb_cron_day',
  'excluded_folders' => 'ezpz_ocb_excluded_folders',
  'prefix_only' => 'ezpz_ocb_prefix_only',
  'speed_factor' => 'ezpz_ocb_speed_factor',
  'stylized' => 'ezpz_ocb_stylized',
  'save_tz' => 'ezpz_ocb_save_tz',
//  'hide_backup' => 'ezpz_ocb_hide_backup',
  'bu_number' => 'ezpz_ocb_max_allowed',
  'ds_format' => 'ezpz_ocb_ds_format');

$hidden_field_name = 'ezpz_ocb_submit_hidden';

if (get_option('ezpz_ocb_db_dump') == 'alt') {
    $posted_prefix = "";
} else {
    $posted_prefix = $_POST[$opt_name['prefix_only']];
}

// Read in existing option value from database
$opt_val = array(
  'db_dump' => get_option($opt_name['db_dump']),
  'db_repair' => get_option($opt_name['db_repair']),
  'set_cron' => get_option($opt_name['set_cron']),
  'cron_day' => get_option($opt_name['cron_day']),
  'cron_time' => get_option($opt_name['cron_time']),
  'excluded_folders' => get_option($opt_name['excluded_folders']),
  'prefix_only' => $posted_prefix,
  'speed_factor' => get_option($opt_name['speed_factor']),
  'stylized' => get_option($opt_name['stylized']),
  'save_tz' => get_option($opt_name['save_tz']),
//  'hide_backup' => get_option($opt_name['hide_backup']),
  'bu_number' => get_option($opt_name['bu_number']),
  'ds_format' => get_option($opt_name['ds_format']),
  'backup_folder_name' => get_option($opt_name['backup_folder_name']),
  'ezpz_ocb_backup_cron_email' => get_option($opt_name['ezpz_ocb_backup_cron_email']));

// See if the user has posted us some information
// If they did, this hidden field will be set to 'Y'
if (isset($_POST[$hidden_field_name]) && $_POST[$hidden_field_name] == 'Y') {
    // Read their posted value

    $opt_val = array(
      'db_dump' => $_POST[$opt_name['db_dump']],
      'db_repair' => $_POST[$opt_name['db_repair']],
      'set_cron' => $_POST[$opt_name['set_cron']],
      'cron_time' => $_POST[$opt_name['cron_time']],
      'cron_day' => $_POST[$opt_name['cron_day']],
      'excluded_folders' => $_POST[$opt_name['excluded_folders']],
      'prefix_only' => $_POST[$opt_name['prefix_only']],
      'speed_factor' => $_POST[$opt_name['speed_factor']],
      'stylized' => $_POST[$opt_name['stylized']],
      'save_tz' => $_POST[$opt_name['save_tz']],
//      'hide_backup' => $_POST[$opt_name['hide_backup']],
      'ds_format' => $_POST[$opt_name['ds_format']],
      'bu_number' => $_POST[$opt_name['bu_number']],
      'tmp_ds_format' => $_POST['tmp_ds_format'],
      'backup_folder_name' => $_POST['backup_folder_name'],
      'ezpz_ocb_backup_cron_email' => $_POST['ezpz_ocb_backup_cron_email']);
	  
	if($opt_val['backup_folder_name'] != get_option('backup_folder_name')) {
		rename_backup_folder($opt_val['backup_folder_name']);
	}

    if ($opt_val['tmp_ds_format'] != get_option('ezpz_ocb_ds_format')) {
        $opt_val['tmp_ds_format'] = preg_replace("#[^0-9a-zA-Z\_\.\-\,\@\s]#", "", $opt_val['tmp_ds_format']);
        $opt_val['tmp_ds_format'] = str_replace(" ", "_", $opt_val['tmp_ds_format']);
        $opt_val['tmp_ds_format'] = str_replace("\\", "", $opt_val['tmp_ds_format']);
        $opt_val['ds_format'] = $opt_val['tmp_ds_format'];
    }



    if ($opt_val['set_cron'] != get_option('ezpz_ocb_set_cron') || $opt_val['cron_time'] != get_option('ezpz_ocb_cron_time')) {    	

        wp_clear_scheduled_hook('ezpz_ocb_cron');
		while(wp_get_schedule('ezpz_ocb_cron')){
			usleep(100000);
		}

        if (get_option('timezone_string') == '') {
            $default_tz = 'GMT';
            $blog_tz = "GMT";
            $gmt_offset = get_option('gmt_offset');
            if ($gmt_offset > 0) {
                $gmt_offset = "+" . $gmt_offset;
            }
            $gmt_offset = str_replace('.5', ':30', $gmt_offset);
            $gmt_offset = str_replace('.75', ':45', $gmt_offset);
            $blog_tz_adjusted = "<b>UTC$gmt_offset</b> so <b>GMT</b> will be used as default";
            $pseudo_tz = 'GMT';
        } else {

            $default_tz = get_option('timezone_string');
            $blog_tz = get_option('timezone_string');
            $blog_tz_adjusted = "<b>$blog_tz</b>";
            $pseudo_tz = '';
        }
        $ezpz_time = strtotime(ezpz_date());

        if ($opt_val['set_cron'] != 'off') {
            $hour = $opt_val['cron_time'];
            $month = date('n', $ezpz_time); // $ezpz_date->format('n');
            $day = date('j', $ezpz_time); // $ezpz_date->format('j');
            $year = date('Y', $ezpz_time); // $ezpz_date->format('Y');
            if (get_option('ezpz_ocb_set_cron') == 'weekly') {
                update_option('ezpz_ocb_cron_day', date('l', $ezpz_time)); // $ezpz_date->format('l'));
            }
            date_default_timezone_set(get_option('ezpz_ocb_save_tz'));
            $cron_time_tmp = date('U', mktime($hour, 0, 0, $month, $day, $year));
            $wp_tz = get_option('timezone_string');
            if ($wp_tz == "") {
                $wp_tz = 'GMT';
            }
            date_default_timezone_set($wp_tz);
			
 //       Delay cron if backup is scheduled within 1 hour of current time to avoid conflicts
          if ($cron_time_tmp <= time() + 3600) { // 3600 = 60 minutes
	          switch (get_option('ezpz_ocb_set_cron')){
			  	case 'daily':
					$cron_time_tmp = $cron_time_tmp + 86400; // 86400 = 1 day
					break;
				case 'twicedaily':
					$cron_time_tmp = $cron_time_tmp + 43200; // 43200 = 12 hours
					break;
				case 'twodays':
					$cron_time_tmp = $cron_time_tmp + 172800; // 172800 = 2 days
					break;
				case 'weekly':
					$cron_time_tmp = $cron_time_tmp + 604800; // 604800 = 7 days
					break;
				case '4daily':
					$cron_time_tmp = $cron_time_tmp + 21600; // 21600 = 6 hours
					break;
	          }
             
          }
            wp_schedule_event($cron_time_tmp, $opt_val['set_cron'], 'ezpz_ocb_cron');
        }
    }

/*    if ($opt_val['hide_backup'] != get_option('ezpz_ocb_hide_backup')) {
        $hide_bu = $backup_folder_path . "/.htaccess";
        $show_bu = $backup_folder_path . "/htaccess.txt";
        if ($opt_val['hide_backup'] != 'yes') { //
            if (file_exists($hide_bu)) { // show backup
                rename($hide_bu, $show_bu);
            }
        } elseif (file_exists($show_bu)) { // hide backup
            rename($show_bu, $hide_bu);
        }
    } */

    if ($opt_val['backup_folder_name'] != get_option('ezpz_ocb_backup_folder_name')) {
    	if($opt_val['backup_folder_name'] != "") {
			$opt_val['backup_folder_name'] = preg_replace("#[^0-9a-zA-Z\_\.\-\s]#", "", $opt_val['backup_folder_name']);
			$opt_val['backup_folder_name'] = str_replace(" ", "_", $opt_val['backup_folder_name']);
			$cmd = "mv $tmp_old_folder $tmp_new_folder";
			exec($cmd);
		} else {			
			$opt_val['backup_folder_name'] = get_option('ezpz_ocb_backup_folder_name');
			if(file_exists(EZPZOCB . "/backups/" . get_option('ezpz_ocb_zip_name'))) {
				$cmd_data = EZPZOCB . "/backups/" . get_option('ezpz_ocb_zip_name') . " " . EZPZOCB . "/backups/" . $opt_val['backup_folder_name'] . "/" . get_option('ezpz_ocb_zip_name');
				exec("mv $cmd_data");
			}
		}
    }

    $permissions = get_option('ezpz_ocb_permissions');
    if ($permissions['folders'] != $_POST['dir_perms'] || $permissions['files'] != $_POST['file_perms']) {
        $permissions['folders'] = $_POST['dir_perms'];
        $permissions['files'] = $_POST['file_perms'];
        update_option('ezpz_ocb_permissions', $permissions);
        set_permissions($_POST['dir_perms'], $_POST['file_perms']);
    }


// Save the posted value in the database
    update_option($opt_name['db_dump'], $opt_val['db_dump']);
    update_option($opt_name['set_cron'], $opt_val['set_cron']);
    $cron_status_time = wp_date(wp_next_scheduled('ezpz_ocb_cron'));
	if($opt_val['set_cron'] != 'off'){
		set_status('cron',"Next scheduled backup: $cron_status_time");
	} else {
		set_status('cron', "Scheduled backups are turned off.");
	}
    update_option($opt_name['cron_time'], $opt_val['cron_time']);
    if ($opt_val['excluded_folders'] == '') {
        $opt_val['excluded_folders'] = 'none';
    }
    update_option($opt_name['db_repair'], $opt_val['db_repair']);
    update_option($opt_name['excluded_folders'], $opt_val['excluded_folders']);
    update_option($opt_name['prefix_only'], $opt_val['prefix_only']);
    update_option($opt_name['speed_factor'], $opt_val['speed_factor']);
    update_option($opt_name['stylized'], $opt_val['stylized']);
    update_option($opt_name['save_tz'], $opt_val['save_tz']);
//    update_option($opt_name['hide_backup'], $opt_val['hide_backup']);
    update_option($opt_name['bu_number'], $opt_val['bu_number']);
    update_option($opt_name['ds_format'], $opt_val['ds_format']);
    update_option('ezpz_ocb_backup_folder_name', $opt_val['backup_folder_name']);
    update_option('ezpz_ocb_backup_cron_email', $_POST['ezpz_ocb_backup_cron_email']);
////    $permissions = get_option('ezpz_ocb_permissions');
//    $permissions['folders'] = $_POST['dir_perms'];
//    $permissions['files'] = $_POST['file_perms'];
// Put an options updated message on the screen
    echo '<div style="background-color:#ffffe0;
             border-color:#e6db55;
             border-width:1px;
             border-style:solid;
             padding:0 .6em;
             margin:5px 15px 2px;
             -moz-border-radius:3px;
             -khtml-border-radius:3px;
             -webkit-border-radius:3px;
             border-radius:3px;" >
            <p><strong>Options saved.</strong></p></div>';
}


if (get_option('timezone_string') == '') {
    $default_tz = 'GMT';
    $blog_tz = "GMT";
    $gmt_offset = get_option('gmt_offset');
    if ($gmt_offset > 0) {
        $gmt_offset = "+" . $gmt_offset;
    }
    $gmt_offset = str_replace('.5', ':30', $gmt_offset);
    $gmt_offset = str_replace('.75', ':45', $gmt_offset);
    $blog_tz_adjusted = "<b>UTC$gmt_offset</b> so <b>GMT</b> will be used as default";
    $pseudo_tz = 'GMT';
} else {

    $default_tz = get_option('timezone_string');
    $blog_tz = get_option('timezone_string');
    $blog_tz_adjusted = "<b>$blog_tz</b>";
    $pseudo_tz = '';
}

// head_template('Options');

if (get_option('ezpz_ocb_db_dump') == 'alt') {
    update_option('ezpz_ocb_prefix_only', '');
    $pf_disabled = 'disabled="disabled"';
    echo "<style type='text/css'>
    #pf-only {border: medium #adadad groove !important;
              background-color: #f7f7f7 !important;
              color: #adadad !important;
    }
    #pf-button {display: none;}
</style>";
} else {
    $pf_disabled = "";
}
date_default_timezone_set(get_option('ezpz_ocb_save_tz'));
$cron_time = date('F jS, Y \@ g:ia \(e\)', wp_next_scheduled('ezpz_ocb_cron'));
$current_time = date('F jS, Y \@ g:ia \(e\)');
if ($opt_val['set_cron'] != 'off') {
    $cron_schedule = "Next scheduled backup: $cron_time";
} else {
    $cron_schedule = "Scheduled backups are turned off.";
}
$wp_tz = get_option('timezone_string');
if ($wp_tz == "") {
    $wp_tz = 'GMT';
}
date_default_timezone_set($wp_tz);

$perms = get_option('ezpz_ocb_permissions');
//$dir_perms = $perms['folders'];
//$file_perms = $perms['files'];
?>
<script language="javascript" type="text/javascript">

    function randomString() {
        var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
        var string_length = 4;
        var randomstring = '';
        for (var i=0; i<string_length; i++) {
            var rnum = Math.floor(Math.random() * chars.length);
            randomstring += chars.substring(rnum,rnum+1);
        }
        var chars1 = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz-_";
        var string_length1 = 12;
        var randomstring1 = '';
        for (var i=0; i<string_length1; i++) {
            var rnum1 = Math.floor(Math.random() * chars1.length);
            randomstring1 += chars1.substring(rnum1,rnum1+1);
        }
        randomstring2 = randomstring + randomstring1;
        document.setOptions.backup_folder_name.value = randomstring2;
    }
</script>

<style type="text/css">
    li.options {border: medium #dddddd groove; padding: 5px 8px; margin: 0 0 20px 12px;}

</style>
<form name="setOptions" method="post" action="<?php echo site_url() . "/wp-admin/admin.php?page=ezpz_ocb&options=saved"; ?>">
    <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
    <ul >
        <li class="options">
            <p style="font-weight: bold; text-align: center; margin:0 0 4px 0;"><?php echo current_schedule(get_option('ezpz_ocb_cron_time'), get_option('ezpz_ocb_set_cron')); ?></p>
            <label class="options">Schedule backups: <span style="float: right; margin-right: 10px;">Defaults: Off / 12 am</span></label>
            <select style="width: 180px;" name="<?php echo $opt_name['set_cron']; ?>">
                <option value="off" <?php echo ($opt_val['set_cron'] == "off") ? 'selected="selected"' : ''; ?> >Off - Do Not Schedule</option>
                <option value="4daily" <?php echo ($opt_val['set_cron'] == "4daily") ? 'selected="selected"' : ''; ?> >Four times a day</option>
                <option value="twicedaily" <?php echo ($opt_val['set_cron'] == "twicedaily") ? 'selected="selected"' : ''; ?> >Two times a day</option>
                <option value="daily" <?php echo ($opt_val['set_cron'] == "daily") ? 'selected="selected"' : ''; ?> >One time a day</option>
                <option value="twodays" <?php echo ($opt_val['set_cron'] == "twodays") ? 'selected="selected"' : ''; ?> >Every other day</option>
                <option value="weekly" <?php echo ($opt_val['set_cron'] == "weekly") ? 'selected="selected"' : ''; ?> >Once a week</option>
            </select>
            <label class="options"> Time: </label>
            <select style="width: 78px; text-align: right;" name="<?php echo $opt_name['cron_time']; ?>">
<?php
$ii = "";
for ($i = 0; $i <= 23; $i++) {
    if ($i == 0) {
        $ii = "12:00am";
    } elseif ($i == 12) {
        $ii = "12:00pm";
    } elseif ($i >= 13) {
        $ii = "$i" - 12;
        $ii = "$ii:00pm";
    } else {
        $ii = "$i:00am";
    }
    echo "<option value='$i'";
    echo ($opt_val['cron_time'] == $i) ? 'selected="selected"' : '';
    echo ">$ii</option>";
}
?>
            </select>
            <br/><label class="options">Email a notification of a completed scheduled backup to: </label>
            <input style="font-family: 'Lucida Console', courier, monospace;" type="text" name="ezpz_ocb_backup_cron_email" value="<?php echo get_option('ezpz_ocb_backup_cron_email'); ?>" size="26" onFocus="this.select();" />
            <br/><small>Leave the box blank to receive no email notifications.</small>
            <p>Every time someone visits a page on your site, WordPress checks to see if any of it's scheduled functions need to be executed.
                EZPZ OCB can take advantage of this feature to automate your backups. These backup schedules are a kind of pseudo-cron job.
                Basically, if no one visits your site the backup won't run as scheduled, it will however
                run as soon as anyone visits after the scheduled time. If you get even 10 visitors daily this type of scheduling should work fine.</p>
            <table>
                <tr><td colspan="2" style="padding: 0 0 10px 0;"><center><strong><?php echo $cron_schedule; ?></strong>
                    <br/>All times are approximate due to the nature of wp-cron.</center></td>
                </tr>
                <tr>
                    <td width="65%">
                        <small>Four times a day backups will occur every 6 hours starting with X time.
                            <br/>Two times a day backups will occur every 12 hours (Xam and Xpm).
                            <br/>Daily backups will occur at the selected time every day.
                            <br/>Weekly and Every other day backups will occur starting on the day the option is activated
                            and continue until the schedule is changed.</small></td>
                    <td valign="bottom"><p style="text-align: right; margin: 0 0 0 0;"><input type="submit" name="Submit" value="Update Options" class="button" /></p>
                    </td>
                </tr>
            </table>
        </li>
        <li class="options">
            <input id="db_repair" type="checkbox" value="yes" name="<?php echo $opt_name['db_repair']; ?>" <?php echo (isset($opt_val['db_repair']) && $opt_val['db_repair'] == "yes") ? 'checked="true"' : ''; ?>  />
            <label class="options">Perform database repair and optimization.
                <span style="float: right; margin-right: 10px; font-size: smaller;">Default: Unchecked</span></label>
            <p>Selecting this option will allow EZPZ OCB to perform WordPress' built in database repair and optimization during backups.</p>
            <p style="text-align: right; margin-bottom: 0;"><input type="submit" name="Submit" value="Update Options" class="button" /></p>
        </li>
        <li class="options">
            <label class="options">Set File Permissions.
                <span style="float: right; margin-right: 10px; font-size: smaller;">Default: 0755 | 0644</span></label><br/>
        <center>
            Folders <input type="text" size="4" value="<?php echo $perms['folders']; ?>" name="dir_perms" >
<?php echo tab(3); ?>
            Files <input type="text" size="4" value="<?php echo $perms['files']; ?>" name="file_perms" >
        </center>

        <p>If you are experiencing folder or file permission problems
            you may change the permissions here.</p>
        <p>Try <b>0755</b> for folders and <b>0644</b> for files or
            contact your hosting provider for their recommended settings.</p>

        <p style="text-align: right; margin-bottom: 0;"><input type="submit" name="Submit" value="Update Options" class="button" /></p>
        </li>
        <li class="options">
            <label class="options">Change your backup folder name:
                <input style="font-family: 'Lucida Console', courier, monospace;" type="text" name="backup_folder_name" value="<?php echo get_option('ezpz_ocb_backup_folder_name'); ?>" size="26" onFocus="this.select();" />
                <input type="button" value="Generate Random Name" onClick="randomString();" />
                <br/><small>While not necessary, a random folder name is more secure.</small>
                <br/>Only alpha-numeric characters(A-Z, a-z, 0-9), hyphens(-) &amp; underscores(_) are allowed.
                <br/>All other characters will be removed.
                <br/><small>NOTE: Spaces will be converted to underscores(_).</small></label>
            <p>Currently your backup folder name is <b><?php echo get_option('ezpz_ocb_backup_folder_name'); ?></b>.</p>
            <p style="text-align: right; margin:-20px 0 0 0;"><input type="submit" name="Submit" value="Update Options" class="button" /></p>
        </li>
        <li class="options">
            <label class="options">Select a timezone for backup datestamp:
                <span style="float: right; margin-right: 10px; font-size: smaller;">Default: <?php echo $blog_tz; ?></span></label>
            <select style="width: 180px;" name="<?php echo $opt_name['save_tz']; ?>">
<?php include('ezpz-ocb-timezone-array.php'); //get the timezone options array.        ?>
            </select>
            <p>Your WordPress timezone setting is <?php echo $blog_tz_adjusted ?>
                <br/>Your backup's timezone is <b><?php echo $ezpz_tz; ?></b>
            </p>
            <p>Changing this option will not effect your WordPress timezone choice, it only applies to EZPZ OCB Backups.</p>
<?php
$wp_tz = get_option('timezone_string');
if ($wp_tz == "") {
    $wp_tz = 'GMT';
}
$wp_time = mysql2date('F jS, Y g:ia', date('Y-m-d H:i:s'));
if ($blog_tz == 'GMT') {
    $zz = "GMT";
} else {
    $zz = "WordPress";
}
?>
            <p><b>WordPress Time is <?php echo date('F jS, Y g:ia'); ?>
                    <br/>EZPZ OCB Time is <?php echo date('F jS, Y g:ia', ezpz_time()); ?></b></p>
            <p style="text-align: right; margin:0 0 0 0;"><input type="submit" name="Submit" value="Update Options" class="button" /></p>
        </li>
        <li class="options">
            <label class="options">Select the maximum number of backups to keep on the server:
                <span style="float: right; margin-right: 10px; font-size: smaller;">Default: 5</span></label>
            <select style="margin-left: 2px; width: 55px; text-align: center;" name="<?php echo $opt_name['bu_number']; ?>">
<?php
$ii = "";
for ($i = 1; $i <= 10; $i++) { 
    echo "<option value='$i'";
    echo ($opt_val['bu_number'] == $i) ? 'selected="selected"' : '';
    echo ">" . str_pad($i, 2, '0', STR_PAD_LEFT) . "</option>";
}
?>
            </select>
            <p>
            	This setting will take effect after the next backup occurs.
            </p>
            <p style="text-align: right; margin:0 0 0 0;"><input type="submit" name="Submit" value="Update Options" class="button" /></p>
        </li>
<?php
$dsf = array(// Datestamp format
  1 => "y-m-d",
  2 => "y-m-d_h.ia",
  3 => "Y-m-d",
  4 => "Y-m-d_h.ia",
  5 => "d-m-Y",
  6 => "d-m-Y_h.ia",
  7 => "dMy",
  8 => "dMy_Hi",
  9 => "MjS-Y",
  10 => "MjS-Y_h.ia");

if (in_array(get_option('ezpz_ocb_ds_format'), $dsf)) {
    $dspf = "";
} else {
    $dspf = "Custom: ";
}
?>
        <li class="options">
        <?php $ds_format = get_option('ezpz_ocb_ds_format'); ?>
            <label class="options">Select a pre-defined datestamp format:
                <span style="float: right; margin-right: 10px; font-size: smaller;">Default: <?php echo date('Y-m-d', ezpz_time()); ?></span></label>
            <select style="width: 220px;" name="<?php echo $opt_name['ds_format']; ?>">
                <option value="<?php echo get_option('ezpz_ocb_ds_format'); ?>" <?php echo ($opt_val['ds_format'] == get_option('ezpz_ocb_ds_format')) ? 'selected="selected"' : ''; ?> ><?php echo $dspf . date($ds_format, ezpz_time()); ?></option>
                <option value="<?php echo $dsf[1] ?>" <?php echo ($opt_val['ds_format'] == $dsf[1]) ? 'selected="selected"' : ''; ?> ><?php echo date('y-m-d', ezpz_time()); ?></option>
                <option value="<?php echo $dsf[2] ?>" <?php echo ($opt_val['ds_format'] == $dsf[2]) ? 'selected="selected"' : ''; ?> ><?php echo date('y-m-d_h.ia', ezpz_time()); ?></option>
                <option value="<?php echo $dsf[3] ?>" <?php echo ($opt_val['ds_format'] == $dsf[3]) ? 'selected="selected"' : ''; ?> ><?php echo date('Y-m-d', ezpz_time()); ?></option>
                <option value="<?php echo $dsf[4] ?>" <?php echo ($opt_val['ds_format'] == $dsf[4]) ? 'selected="selected"' : ''; ?> ><?php echo date('Y-m-d_h.ia', ezpz_time()); ?></option>
                <option value="<?php echo $dsf[5] ?>" <?php echo ($opt_val['ds_format'] == $dsf[5]) ? 'selected="selected"' : ''; ?> ><?php echo date('d-m-Y', ezpz_time()); ?></option>
                <option value="<?php echo $dsf[6] ?>" <?php echo ($opt_val['ds_format'] == $dsf[6]) ? 'selected="selected"' : ''; ?> ><?php echo date('d-m-Y_h.ia', ezpz_time()); ?></option>
                <option value="<?php echo $dsf[7] ?>" <?php echo ($opt_val['ds_format'] == $dsf[7]) ? 'selected="selected"' : ''; ?> ><?php echo date('dMy', ezpz_time()); ?></option>
                <option value="<?php echo $dsf[8] ?>" <?php echo ($opt_val['ds_format'] == $dsf[8]) ? 'selected="selected"' : ''; ?> ><?php echo date('dMy_Hi', ezpz_time()); ?></option>
                <option value="<?php echo $dsf[9] ?>" <?php echo ($opt_val['ds_format'] == $dsf[9]) ? 'selected="selected"' : ''; ?> ><?php echo date('MjS-Y', ezpz_time()); ?></option>
                <option value="<?php echo $dsf[10] ?>" <?php echo ($opt_val['ds_format'] == $dsf[10]) ? 'selected="selected"' : ''; ?> ><?php echo date('MjS-Y_h.ia', ezpz_time()); ?></option>
            </select><br/>
            <p>Or customize your own <a href="http://php.about.com/od/learnphp/ss/php_functions_3.htm" target="_blank"> valid PHP date format</a>:
                <input style="font-family: 'Lucida Console', courier, monospace;" type="text" name="tmp_ds_format" value="<?php echo $opt_val['ds_format']; ?>" size="26" onFocus="this.select();" />
                <br/>Allowed separators are periods(.) hyphens(-) commas(,) at symbols(@) &amp; underscores(_).
                <br/>All other characters will be removed.
                <br/><small>NOTE: Spaces will be converted to underscores(_).</small></p>
            <p>Currently your datestamp is <b><?php echo date(get_option('ezpz_ocb_ds_format', ezpz_time())); ?></b>.</p>
            <p style="text-align: right; margin:-20px 0 0 0;"><input type="submit" name="Submit" value="Update Options" class="button" /></p>
        </li>
        <li class="options" id="pf-only">
            <input id="prefix_only" type="checkbox" value="yes" <?php echo $pf_disabled; ?> name="<?php echo $opt_name['prefix_only']; ?>" <?php echo (get_option('ezpz_ocb_prefix_only') == "yes") ? 'checked="true"' : ''; ?>  />
            <label class="options">Backup only the database tables with the prefix <strong><?php echo $wpdb->prefix; ?></strong>.
                <span style="float: right; margin-right: 10px; font-size: smaller;">Default: Unchecked</span></label>
            <p>Useful only if you are using a shared database. This option will only backup database tables prefixed with <em><?php echo $wpdb->prefix; ?></em>.
                If you are using a dedicated database this option is moot and should be unchecked for better performance.
                <br/><small>NOTE: Some servers may not allow use of this option.</small>
                <br/><small>NOTE: This option is not available with the alternative database backup method.</small></p>
            <div id="pf-button"><p style="text-align: right; margin-bottom: 0;"><input type="submit" name="Submit" value="Update Options" class="button" /></p></div>
        </li>
        <li class="options">
            <input id="db_dump" type="checkbox" value="alt" name="<?php echo $opt_name['db_dump']; ?>" <?php echo (isset($opt_val['db_dump']) && $opt_val['db_dump'] == "alt") ? 'checked="true"' : ''; ?>  />
            <label class="options">Use alternate database backup method.
                <span style="float: right; margin-right: 10px; font-size: smaller;">Default: Unchecked</span></label>
            <p>Select this option only if you are getting <em>mysqldump</em> database warnings. This alternative method is slower
                but does not use <em>mysqldump</em> for backing up your database.
                <br/><small>NOTE: Using this option disables the <em><?php echo $wpdb->prefix; ?></em> prefix only option.</small></p>
            <p style="text-align: right; margin-bottom: 0;"><input type="submit" name="Submit" value="Update Options" class="button" /></p>
        </li>
        <li class="options">
            <table>
                <tr>
                    <td colspan="2"><label class="options">Excluded Folders
                            <span style="float: right; margin-right: 10px; font-size: smaller;">Default: none</span></label></td></tr>
                <tr><td valign="top"><textarea style="border:thin black inset" cols="30" rows="5" name="<?php echo $opt_name['excluded_folders'] ?>" value="<?php echo $opt_val['excluded_folders']; ?>"><?php echo $opt_val['excluded_folders']; ?></textarea>
                    </td>
                    <td valign="top" style="padding: 0 10px 0 15px;">Here you can list specific folders you wish to exclude from your backup. These folders as well as all their content will be excluded from backups.
                        <p>Enter each folder which you wish to exclude separated by commas.</p>
                    </td>
                </tr>
                <tr><td colspan="2">
                        <p>Be as specific as possible with folder names to avoid unwanted exclusions.
                            <br/>For example, if you enter "<em>tmp</em>" ALL folders named "<em>tmp</em>" (<em>thisFolder/tmp</em> AND <em>thatFolder/tmp</em>) would be excluded.
                            &nbsp;If you enter <em>thisFolder/tmp</em>, <em>thatFolder/tmp</em> is backed up while <em>thisFolder/tmp</em> is not.
                            <br/><small>Note: Wildcards(*) are <b>NOT</b> allowed. Folder names <b>SHOULD NOT</b> contain leading and/or trailing slashes.</small></p>
                    </td></tr>
            </table>
            <p style="text-align: right; margin-bottom: 0;"><input type="submit" name="Submit" value="Update Options" class="button" /></p>
        </li>
        <?php
/*        <li class="options">
            <input type="checkbox" value="yes" name="<?php echo $opt_name['hide_backup']; ?>" <?php echo (isset($opt_val['hide_backup']) && $opt_val['hide_backup'] == "yes") ? 'checked="checked"' : ''; ?>  />
            <label class="options">Hide backup from web access.
                <span style="float: right; margin-right: 10px; font-size: smaller;">Default: Unchecked</span></label>
            <p>Selecting this option will change the permissions on your backup folder and it will be totally
                inaccessible by browsers. You will have to download your backup via FTP or your server's control panel.</p>
            <p><small>NOTE: While it is slightly more secure it's not necessary to use this option.<br/>
                    NOTE: Some servers may not allow the use of this option.</small></p>
            <p style="text-align: right; margin: -40px 0 0 0;"><input type="submit" name="Submit" value="Update Options" class="button" /></p>
        </li>
 */
        ?>
        <li class="options">
            <input type="checkbox" value="no" name="<?php echo $opt_name['stylized']; ?>" <?php echo (isset($opt_val['stylized']) && $opt_val['stylized'] == "no") ? 'checked="checked"' : ''; ?>  />
            <label class="options">Remove colorful stylization on backup status display.
                <span style="float: right; margin-right: 10px; font-size: smaller;">Default: Unchecked</span></label>
            <p>Choosing this option will remove the light hearted color and font styling from the backup status report. </p>
            <p style="text-align: right; margin-bottom: 0;"><input type="submit" name="Submit" value="Update Options" class="button" /></p>
        </li>

    </ul>
</form>

<?php // foot_template(); ?>