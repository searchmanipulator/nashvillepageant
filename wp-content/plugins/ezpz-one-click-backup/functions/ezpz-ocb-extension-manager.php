<?php

global $wpdb;
$site_url = site_url();
$domain = getDomain();
$a_exts = get_option('ezpz_ocb_available_extensions');

$form_action = str_replace('%7E', '~', $_SERVER['REQUEST_URI']);
if ($_POST['install'] == "Install Dropbox Extension" && $_POST['dbx_action_hidden'] && $_POST['dbx_action_hidden'] == 'Y') {
    install_ext('dbx');
    ezpz_ocb_admin_notices();
//    if (has_ext('dbx')) {
//        $show_notice = 'installed';
//        $_POST['install'] = '';
//    }
}

if ($_POST['remove'] == "Remove Dropbox Extension" && $_POST['dbx_action_hidden'] && $_POST['dbx_action_hidden'] == 'Y') {
    uninstall_ext('dbx');
//    if (!has_ext('dbx')) {
//        $show_notice = 'uninstalled';
//        $_POST['noinstall'] = '';
//    }
}

echo "
	<div id='ex-manager-page' style='padding: 0 10px;'>
	<table width='100%'><tr>
    <td><h2 class='ezpz-title'>FTP Extension</h2></td>
    <td></td>
    <td align='right' width='25%'><h2>Included</h2></td>
    </tr><tr>
    <td colspan='3'><p><b><i>Description:</i></b></p>
    <p>The FTP Extension allows the automatic transfer
    of backups to anywhere you have FTP access. This
    extension is built in and can be activated under
    <a href='#ex-settings' onClick=\"toggle_visibility('ocb-ex-settings'); 
\">Extension Settings</a></p></td>
    </tr></table>";

if (has_ext('dbx')) {
    $dbx = 'Included';
    $dbx_btn = "<input type='submit' name='remove' value='Remove Dropbox Extension' />";
} else {
    $dbx = 'Available';
    $dbx_btn = "<input type='submit' name='install' value='Install Dropbox Extension' />";
}
echo "<div style='clear: both;'><hr style='color: #666666; background-color: #666666; height: 4px; margin-bottom: -4px;'/></div>";
echo "<table width='100%'><tr>
    <td><h2 class='ezpz-title'>Dropbox Extension</h2></td>
    <td align='center' style='padding: 10px 0 0 0;'>
    <form name='dbx_action' method='post' action='" . $form_action . "'>
    <input type='hidden' name='dbx_action_hidden' value='Y' />";
echo "</td>
    <td align='right'><h2>$dbx</h2></td>
    </tr><tr>
    <td colspan='2'>$dbx_license</td>
    </tr><tr>
    <td colspan='3'><p><b><i>Description:</i></b></p>
    <p>The Dropbox Extension allows the automatic transfer
    of backups to your Dropbox account. It can automatically
      transfer files of up to 250MB as a single zip file depending
      on server load and capibility. It can split larger files
      (tested up to 1GB) into smaller pieces for better transfers.This
    extension is built in and can be activated under
    <a href='#ex-settings' onClick=\"toggle_visibility('ocb-ex-settings'); \">Extension Settings</a></p>
      <p>Don't have Dropbox? <a href='http://db.tt/n8su2Cv'
      target='_new' >Get 2GB free online storage here.</a></p></td>
      </tr><tr>
    <td colspan='3'><p style='text-align: center;'>More extensions coming soon.</p></td>
    </tr></table></div>";
?>
