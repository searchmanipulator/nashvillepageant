<?php
$ftp = get_option('ezpz_ocb_ftp');
require_once (EZPZOCB . '/extensions/ext-functions.php');
$dropbox = get_option('ezpz_ocb_dropbox');

if (isset($_POST['dropbox-hidden']) && $_POST['dropbox-hidden'] == 'Y') {
	$dropbox = array('active' => $_POST['active'], 'pass' => urlencode(stripslashes($_POST['pass'])), 'mail' => $_POST['mail'], 'folder' => preg_replace("#[^0-9a-zA-Z\_\-\/\s]#", "", $_POST['folder']), 'split' => $_POST['split'], 'os' => $_POST['os']);

	update_option('ezpz_ocb_dropbox', $dropbox);

	echo "<div id='notice' class='updated'>
            <p><strong>Dropbox Extension Settings Saved.</strong></p></div>";
}

$dropbox = get_option('ezpz_ocb_dropbox');
$drpbximg = "<img style='margin: 2px 2px -2px 2px; height: 15px; width: 58px;' src='" . EZPZOCB_URL . "/images/dropbox.png' />";

if ($dropbox['active'] === 'active' || $dropbox['active'] === 'true') {
	$db_active = "checked = 'checked'";
} else {
	$db_active = "";
}
if ($dropbox['os'] === 'both') {
	$os_both = "checked = 'checked'";
} else {
	$os_both = "";
}
if ($dropbox['os'] === 'linux') {
	$os_linux = "checked = 'checked'";
} else {
	$os_linux = "";
}
if ($dropbox['os'] === 'windows') {
	$os_windows = "checked = 'checked'";
} else {
	$os_windows = "";
}
if ($dropbox['split'] === 'split' || $dropbox['split'] === 'true') {
	$split_zip = "checked = 'checked'";
} else {
	$split_zip = "";
}

$default_folder = "EZPZ OCB Backups/" . substr(clean_name(), 0, strlen(clean_name()) - 1);
if ($dropbox['license'] != 'EXPIRED') {
	$db_ckbox_disabled = "";
} else {
	$db_active = "";
	$db_ckbox_disabled = "disabled=\"disabled\"";
}

if (isset($_POST['ftp-hidden']) && $_POST['ftp-hidden'] == 'Y') {
	$ftp = array('active' => $_POST['active'], 'alert' => $_POST['alert'], 'host' => $_POST['host'], 'user' => $_POST['user'], 'pass' => urlencode(stripslashes($_POST['pass'])), 'folder' => preg_replace("#[^0-9a-zA-Z\_\-\/\s]#", "", $_POST['folder']), 'mail' => $_POST['mail'], 'count' => 1);

	update_option('ezpz_ocb_ftp', $ftp);

	echo "<div id='notice' class='updated'>
            <p><strong>FTP Extension Settings Saved.</strong></p></div>";
}
if ($ftp['active'] === 'active' || $ftp['active'] === 'true') {
	$ftp_active = "checked = 'checked'";
} else {
	$ftp_active = "";
}
if ($ftp['alert'] === 'alert' || $ftp['alert'] === 'true') {
	$ftp_alert = "checked = 'checked'";
} else {
	$ftp_alert = "";
}
?>
<div id='ex-settings-page' style='padding: 0 10px;'>
	<h2 class="ezpz-title">Dropbox Extension Settings</h2>
	<br/>
	<form name="ezpz_ocb_dropbox_settings" method="post" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']);?>">
		<input type="hidden" name="dropbox-hidden" value="Y" />
		<ul>
			<script type="text/javascript">
				ezpz_ocb_dropbox_settings.pass.value = '<?php echo htmlentities(urldecode($dropbox['pass']), ENT_QUOTES);?>';
			</script>
			<li>
				Automatically save backups to Dropbox:
				<input type="checkbox" name="active" value="active" <?php echo "$db_active $db_ckbox_disabled";?> />
			</li>
			<li>
				Your Dropbox e-mail address:
				<input style="width: 220px;" type="text" name="mail" value="<?php echo $dropbox['mail'];?>" />
			</li>
			<li>
				Your Dropbox password:
				<input style="width: 220px;" type="password" name="pass" id='dbpw' value="<?php echo htmlentities(urldecode($dropbox['pass']), ENT_QUOTES);?>" />
			</li>
			<li>
				Dropbox directory: EZPZ OCB Backups/
				<input style="width: 220px;" type="text" name="folder" value="<?php echo $dropbox['folder'];?>" />
			</li>
			<li>
				<small>If you're having trouble with files uploading to your Dropbox try the split zip file option.</small>
				<br/>
				Split the zip file for Dropbox.
				<input type="checkbox" name="split" value="split" <?php echo "$split_zip";?> />
				<?php echo tab(1);?>I primarily use:
				<input type="radio" name="os" value="windows" <?php echo $os_windows;?> >
				Windows<?php echo tab(1);?></input>
				<input type="radio" name="os" value="linux" <?php echo $os_linux;?> >
				Linux<?php echo tab(1);?> </input>
				<input type="radio" name="os" value="both" <?php echo $os_both;?> >
				Both<?php echo tab(1);?> </input>
			</li>
			<li>
				<input type="submit" name ="save" value="Save Dropbox Extension Settings" />
			</li>
		</ul>
	</form>
	<p>
		Don't have <?php echo $drpbximg;?> <a href="http://db.tt/n8su2Cv" target="_new" >Get 2GB free online storage here.</a>
	</p>
	<div style="clear: both;"><hr style='color: #666666; background-color: #666666; height: 4px; margin-bottom: -4px;'/></div>
	<h2 class="ezpz-title">FTP Extension Settings</h2>
	<br/>
	<form name="ezpz_ocb_ftp_settings" method="post" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']);?>">
		<input type="hidden" name="ftp-hidden" value="Y" />
		<ul>
			<script type="text/javascript">
				ezpz_ocb_ftp_settings.pass.value = '<?php echo htmlentities(urldecode($ftp['pass']), ENT_QUOTES);?>';
			</script>
			<li>
				Automatically save backups via FTP:
				<input type="checkbox" name="active" value="active" <?php echo $ftp_active;?> />
			</li>
			<li>
				Receive email alerts when backups are sent via FTP:
				<input type="checkbox" name="alert" value="alert" <?php echo $ftp_alert;?> />
			</li>
			<li>
				Alert e-mail address:
				<input style="width: 220px;" type="text" name="mail" value="<?php echo $ftp['mail'];?>" />
			</li>
			<li>
				Your FTP host:
				<input style="width: 220px;" type="text" name="host" value="<?php echo $ftp['host'];?>" />
			</li>
			<li>
				Your FTP Username:
				<input style="width: 220px;" type="text" name="user" value="<?php echo $ftp['user'];?>" />
			</li>
			<li>
				Your FTP password:
				<input style="width: 220px;" type="password" name="pass" value="<?php echo htmlentities(urldecode($ftp['pass']), ENT_QUOTES);?>" />
			</li>
			<li>
				FTP save directory:
				<input style="width: 220px;" type="text" name="folder" value="<?php echo $ftp['folder'];?>" />
			</li>
			<li>
				<input type="submit" name ="save" value="Save FTP Extension Settings" />
			</li>
		</ul>
	</form>
</div>