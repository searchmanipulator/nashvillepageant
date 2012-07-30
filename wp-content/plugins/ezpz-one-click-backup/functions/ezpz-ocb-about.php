<?php
global $wpdb;

function domain($domainb) {
	$bits = explode('/', $domainb);
	if ($bits[0] == 'http:' || $bits[0] == 'https:') {
		$domainb = $bits[2];
	} else {
		$domainb = $bits[0];
	}
	unset($bits);
	$bits = explode('.', $domainb);
	$idz = count($bits);
	$idz -= 3;
	if (strlen($bits[($idz + 2)]) == 2) {
		$url = $bits[$idz] . '.' . $bits[($idz + 1)] . '.' . $bits[($idz + 2)];
	} else if (strlen($bits[($idz + 2)]) == 0) {
		$url = $bits[($idz)] . '.' . $bits[($idz + 1)];
	} else {
		$url = $bits[($idz + 1)] . '.' . $bits[($idz + 2)];
	}
	return $url;
}

$blog_tz = get_option('timezone_string');
if ($blog_tz == "" || $blog_tz == null) {
	$blog_tz = "UTC";
}

$backup_folder_path = WP_PLUGIN_DIR . '/' . ezpz_ocb_slug() . '/backups';
?>
<div style="clear: both;"></div>
<div id="about-page" style="text-align: justify; padding: 10px;">
	<p>
		EZPZ One Click Backup, or EZPZ OCB as we call it, is a very
		easy way to do a complete backup of your entire Wordpress site.
		In fact it's so easy to use there are no required user settings,
		everything is automatic.
		Just one click and presto, you'll have a complete backup stored on your
		server.
		One more click and you can download the entire backup to your own
		computer.
	</p>
	<p>
		If you prefer to download your backup via FTP the path you'll
		need is also included.
		EZPZ OCB also stores your last backup on the server
		should you ever need to download it again.
	</p>
	<p>
		With the new <b>EZPZ Easy Restore</b> feature
		restoring your site is a simple two step process. <small>See <a
			href="<?php echo site_url();?>/wp-admin/admin.php?page=ezpz_ocb_faq"
			target="_self"> FAQs</a> for instructions.</small>
	</p>
	<p>
		Now just because no settings are required doesn't mean there
		are no <a
		href="<?php echo site_url();?>/wp-admin/admin.php?page=ezpz_ocb_options"
		target="_self">options</a>. There are several choices
		that can make your backup the way you want.
	</p>
	<ol>
		<li>
			You can schedule backups ranging from 4 times a day to once
			per week.
		</li>
		<li>
			You can optionally save backups to your Dropbox account or
			via FTP.
		</li>
		<li>
			You can save your backups to an FTP folder on any FTP
			server you have access to.
		</li>
		<li>
			You can choose to receive email alerts upon successful FTP
			transfers.
		</li>
		<li>
			The option to choose the timezone your backup's datestamp
			is based on.
		</li>
		<li>
			Choose the timezone and datestamp format for your backup.
		</li>
		<li>
			If you're using a shared database you can choose to backup
			only the tables needed for your WordPress installation.
		</li>
		<li>
			You can adjust the speed of EZPZ OCB to best match your
			server's capabilities.
		</li>
		<li>
			You can choose to exclude selected folders you don't want
			to include in the backup.
		</li>
	</ol>
	<p>
		Like most applications EZPZ OCB has certain limitations and
		requirements.
		First and foremost, EZPZ OCB <strong>only works on Linux servers
		running PHP 5 and above</strong>
		and only those servers which allow certain required php functions with <em>exec</em>
		seeming to be the most frequently unavailable one. EZPZ OCB has
		improved error messaging to help
		determine if it is compatible with your server.
	</p>
	<p>
		Most WordPress users will have no problems but there are some
		servers with which EZPZ OCB is simply incompatible. Sorry...
	</p>
	<p>
		<span style="font-size: large;">On the drawing
			board...</span>
	</p>
	<ul>
		<li>
			Amazon S3 (Simple Storage Service) integration.
		</li>
		<li>
			Internationalization.
		</li>
		<li>
			Multiple onboard backup storage.
		</li>
	</ul>
</div>
