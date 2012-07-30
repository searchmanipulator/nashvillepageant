<?php
;
function getDirectory( $path = '.', $level = 0 ){

$ignore = array( 'cgi-bin', 'cache', 'ezpz-one-click-backup', '.', '..' );
// Directories to ignore when listing output. Many hosts
// will deny PHP access to the cgi-bin.

$dh = @opendir( $path );
// Open the directory to the handle $dh

	while( false !== ( $file = readdir( $dh ) ) ){
	// Loop through the directory
	
		if( !in_array( $file, $ignore ) ){
		// Check that this file is not to be ignored
		
		$spaces = str_repeat( '&nbsp;', ( $level * 8 ) );
		// Just to add spacing to the list, to better
		// show the directory tree.
		
			if( is_dir( "$path/$file" ) ){
			// Its a directory, so we need to keep reading down...
			$orig_dir = getcwd();
			chdir("$path/$file");
			$dir_size = trim(exec('du -m'), '.');
			if($dir_size > 2){
				$this_path = str_replace('../', '', "$path/$file");
				echo "<strong>$spaces $this_path: $dir_size</strong>
				<br />
				";
			}			
			chdir($orig_dir);
			getDirectory( "$path/$file", ($level+1) );
			// Re-call this same function but on a new directory.
			// this is what makes function recursive.
			
			}
		}	
	}

closedir( $dh );
// Close the directory handle

}
global $wpdb, $ezpz_ocb_data_key, $ezpz_ocb_cron_lock;

$status_file = EZPZOCB_URL . "/backups/data/status.$ezpz_ocb_data_key.php";

// $uploads =  getDirectory("../wp-content");
// 
// echo "<p><pre>$uploads</pre></p>";

if ($_GET['options'] == 'saved') {
	$options_display = 'block';
} else {
	$options_display = 'none';
}
$type = ucfirst(get_option('ezpz_ocb_backup_type'));

if(strtolower(get_option('ezpz_ocb_backup_type')) == 'none' ){
	count_backups();	
}


delete_option('ezpz_ocb_backup_aborted');
while(get_option('ezpz_ocb_backup_aborted')){
	usleep(100000);
}
head_template('Control Panel');

?>
<script type='text/javascript'>
<!--
	
function jsTimestamp(){
    tstmp = new Date();
    var mins = tstmp.getMinutes();
    if (mins < 10){
  	mins = "0" + mins;
	}
    var secs = tstmp.getSeconds();
    if (secs < 10){
  	secs = "0" + secs;
	}
    var ret =  tstmp.getHours() + ':' + mins + ':' + secs + '.' + tstmp.getMilliseconds();  
    return ret;
}

function jsSleep(ms)
{
	var dt = new Date();
	dt.setTime(dt.getTime() + ms);
	while (new Date().getTime() < dt.getTime());
}

isBackupRunning = function(){jQuery.ajax({
	url:'<?php echo backup_in_progress('url') . "?a=";?>' + jsTimestamp(),
		error:
			function(){
				document.getElementById('bbuBtn').disabled = '';
				document.getElementById('mbuBtn').disabled = '';
				document.getElementById('dbuBtn').disabled = '';
			},
		success:
			function (){
				disableBtns();
				document.getElementById('dummyIframe').src = "";
			}
	});
	}
function disableBtns(){	
	document.getElementById('bbuBtn').disabled = 'disabled';
	document.getElementById('mbuBtn').disabled = 'disabled';
	document.getElementById('dbuBtn').disabled = 'disabled';
}	
function runBgBackup() {
	disableBtns();
	document.getElementById('dummyIframe').src = "<?php echo ezpz_ocb_sp('bg_backup'); ?>";
	clearInterval('ajaxCheck()');
	jsSleep(4000);
	ajaxCheck();
	setInterval('ajaxCheck()', 10000);
}

function clearTransfer(){
	document.getElementById('dummyIframe2').src = "<?php echo ezpz_ocb_sp('clr-transfer') . '&a='; ?>" + jsTimestamp();	
//	clearInterval('getStatus()');
	jsSleep(1000);
	getStatus();
//	setInterval('getStatus()', 3500);	
}

function clearError(){
	document.getElementById('dummyIframe2').src = "<?php echo ezpz_ocb_sp('clr-error') . '&a='; ?>" + jsTimestamp();	
	clearInterval('ajaxCheck()');
	jsSleep(1000);
	setInterval('ajaxCheck()', 10000);	
}

function getStatus() {
	
	jQuery('#ezpz-next-scheduled').load('<?php echo EZPZOCB_URL . "/backups/data/cron-status.$ezpz_ocb_data_key.php?a=";?>' + jsTimestamp());
	
	jQuery("#bu-log-title").load('<?php echo EZPZOCB_URL . "/backups/data/buNum.$ezpz_ocb_data_key.php?a=";?>' + jsTimestamp());
		
	jQuery.ajax({
			url:'<?php echo EZPZOCB_URL . "/backups/data/backup-status.$ezpz_ocb_data_key.php?a=";?>' + jsTimestamp(),
				error:
					function(){
						document.getElementById('backupStatus').style.display = 'none';
						document.getElementById('readyStatus').style.display = 'block';
						document.getElementById('readyStatus').innerHTML = 'Ready to backup <?php echo get_bloginfo('name'); ?>';
					},
				success:
					function (){
						jQuery("#backupStatus").load('<?php echo EZPZOCB_URL . "/backups/data/backup-status.$ezpz_ocb_data_key.php";?>');
						document.getElementById('backupStatus').style.display = 'block';
						document.getElementById('readyStatus').style.display = 'none';
					}
		});
		
	jQuery.ajax({
			url:'<?php echo EZPZOCB_URL . "/backups/data/transfer-status.$ezpz_ocb_data_key.php?a=";?>' + jsTimestamp(),
				error:
					function(){
						document.getElementById('transferStatus').style.display = 'none';
					},
				success:
					function (){
						jQuery("#transferStatus").load('<?php echo EZPZOCB_URL . "/backups/data/transfer-status.$ezpz_ocb_data_key.php?a=";?>' + jsTimestamp());
						document.getElementById('transferStatus').style.display = 'block';
					}
		});

	
	jQuery.ajax({
			url:'<?php echo EZPZOCB_URL . "/backups/data/error-status.$ezpz_ocb_data_key.php?a=";?>' + jsTimestamp(),
				error:
					function(){
						document.getElementById('errorStatus').style.display = 'none';
					},
				success:
					function (){
						jQuery("#errorStatus").load('<?php echo EZPZOCB_URL . "/backups/data/error-status.$ezpz_ocb_data_key.php?a=";?>' + jsTimestamp());
						document.getElementById('errorStatus').style.display = 'block';
					}
		});
}

function toggle_marker(id) {
	var e = document.getElementById(id);
	var trimmed = e.innerHTML.replace(/^\s+|\s+$/g, '') ;
	if(trimmed == '+') {
		e.innerHTML = '-';
		e.title = 'Click to collapse';
	} else {
		e.innerHTML = '+';
		e.title = 'Click to expand';
	}
}

function toggle_visibility(id) {
	var e = document.getElementById(id);
	if(e.style.display == 'block') {
		e.style.display = 'none';
	} else {
		e.style.display = 'block';
	}
}

function toggleLogs(id) {
	var ID = id;
	var idState = '';
	var goHere = "<?php echo ezpz_ocb_sp(); ?>" + "#logArea";
	var e = document.getElementById(id);
	if(e.style.display == 'block') {
		idState = 'block';
	} else {
		idState = 'none';
	}
	
	jQuery('.inner-bu-log').hide();
	if(idState != "block"){
		e.style.display = 'block';
	} else {
		e.style.display = 'none';
	}
	document.location = goHere;
}

function loadLogFile(clicked) {
	
	jQuery('.inner-bu-log').hide();
	jQuery.ajax({
		<?php $loadKey =  EZPZOCB_URL . "/backups/data/directory.$ezpz_ocb_data_key.php?a="?>
		url : "<?php echo $loadKey;?>" + jsTimestamp(),
		success : 
			function(data) {
				jQuery("#logArea").html(data);
				document.getElementById(clicked).style.display = 'block';
			}
	});
}

function confirmEzpzOcbReset(){
	var answer = confirm("This will reset EZPZ OCB and delete ALL it's backups stored on this server.\nIf you wish to download a backup choose cancel and download the backup.\n\nYour saved EZPZ OCB options will not be affected.\n\nDropbox and/or FTP transferred backups will remain stored on their servers. \n\n\n\t\t\t\t\t\tDo you wish to proceed?");
	if (answer){
		location.href='<?php echo ezpz_ocb_sp('clr-bus');?>
			';
	} else {
		alert("Action Canceled");
	}
}

function ajaxCheck(){
	isBackupRunning();
	getStatus();
}

function pageTimeout(){
	var answer = confirm("Page has timed out. Do you wish to continue?");
	if (answer){		
		setTimeout('pageTimeout()', 600000);	
		// loadLogFile();	
		// ajaxCheck();
		// setInterval('ajaxCheck()', 15000);
	} else {
		location.href='<?php echo admin_url();?>'
	}
}

jQuery(document).ready(function() {
	setTimeout('pageTimeout()', 600000);		
	loadLogFile();	
	ajaxCheck();
	setInterval('ajaxCheck()', 15000);
//	getStatus();
//	setInterval('getStatus()', 3500);
 });
-->
</script>

<div id='bu-frame'></div>

<div id='cpanel'>

<div class='ezpzocb-box'>
<div id='status-area'>
	<ul>
		<li id='readyStatus'>Checking status...<?php echo tab(2) . "<img src='" . EZPZOCB_URL . "/images/loading.gif' height='20' width='20' align='ABSMIDDLE' />"; ?></li>
		<li id='backupStatus'></li>
		<li id='transferStatus' onclick='clearTransfer();' title='Click to remove this message'></li>
		<li id='errorStatus' onclick='clearError();' title='Click to remove this error message'></li>
	</ul>
</div>
</div>
	<ul>
		<div class='ezpzocb-box'>
			<div class='label' onClick="toggle_visibility('ocb-options'); toggle_marker('options-label');" title="Here you can set EZPZ OCB's options such as scheduled backups, exclude certain folders and more.">
				<div class='toggler' id="options-label" title="Click to expand">
					+
				</div>
				Options
			</div>
			<div id="ocb-options" style="display: <?php echo $options_display;?>;">
				<li class="controls">
					<?php
					require_once (EZPZOCB . "/functions/ezpz-ocb-options.php");
					?>
				</li>
			</div>
		</div><!-- ezpzocb-box -->
		<div class='ezpzocb-box'>
			<a name="ex-settings"></a>
			<div class='label' onClick="toggle_visibility('ocb-ex-settings'); toggle_marker('ex-settings-label');" title='Here you can enter the settings for the EZPZ OCB extensions you have installed such as the Dropbox extension or the FTP extension. (there will be more extensions available in the future)'>
				<div class='toggler' id="ex-settings-label" title="Click to expand">
					+
				</div>
				Extension Settings
			</div>
			<div id="ocb-ex-settings" style="display: none;">
				<li class="controls">
					<?php
					require_once (EZPZOCB . "/extensions/ezpz-ocb-extensions.php");
					?>
				</li>
			</div>
		</div><!-- ezpzocb-box -->
		<div class='ezpzocb-box'>
			<div class='label' onClick="toggle_visibility('ocb-ex-manager'); toggle_marker('ex-manager-label');" title="In the future you will be able to add or remove optional EZPZ OCB extensions here.">
				<div class='toggler' id="ex-manager-label" title="Click to expand">
					+
				</div>
				Extension Manager
			</div>
			<div id="ocb-ex-manager" style="display: none;">
				<li class="controls">
					<?php
					require_once (EZPZOCB . "/functions/ezpz-ocb-extension-manager.php");
					?>
				</li>
			</div>
		</div><!-- ezpzocb-box -->
		<div class='ezpzocb-box' style='display: none;'>
			<div class='label' onClick="toggle_visibility('ocb-news'); toggle_marker('news-label');" title="Here you can see news about EZPZ OCB via our RSS feed">
				<div class='toggler' id="news-label" title="Click to expand">
					+
				</div>
				News
			</div>
			<div id="ocb-news" style="display: none;">
				<li class="controls">
					<?php
					require_once (EZPZOCB . "/functions/ezpz-ocb-news.php");
					?>
				</li>
			</div>
		</div><!-- ezpzocb-box -->
		<div class='ezpzocb-box'>
			<div class='label' onClick="toggle_visibility('ocb-faqs'); toggle_marker('faqs-label');" title="Here you can get answers to the most frequently asked questions about EZPZ OCB.">
				<div class='toggler' id="faqs-label" title="Click to expand">
					+
				</div>
				FAQs
			</div>
			<div id="ocb-faqs" style="display: none;">
				<li class="controls">
					<?php
					require_once (EZPZOCB . "/functions/ezpz-ocb-faq.php");
					?>
				</li>
			</div>
		</div><!-- ezpzocb-box -->
		<div class='ezpzocb-box' style='display: block;'>
			<div class='label' onClick="loadLogFile(); toggle_visibility('ocb-old-bu'); toggle_marker('old-bu-label');" >
				<div class='toggler' id="old-bu-label" title="Click to expand">
					+
				</div><span id='bu-log-title'><?php echo get_num_backups(); ?></span>				
					<div id='remove-backups' style='float: right; margin: 0px 20px 0 0; font-size: 12px; font-family: Tahoma, sans-serif; z-index: 10;' ><input type='button' class='button' value='Reset EZPZ OCB' onclick='confirmEzpzOcbReset();'/>						
					</div>
			</div>
			<div id="ocb-old-bu" style="display: none;">
				<li class="controls">
				<a name='#logArea'></a>
					<div id="logArea">
					</div>
				</li>
			</div>
		</div><!-- ezpzocb-box -->
		<!-- Template Begin -->
		<div class='ezpzocb-box' style='display: none;'>
			<div class='label' onClick="toggle_visibility('ocb-changeMe'); toggle_marker('changeMeToo-label');">
				<div class='toggler' id="changeMeToo-label" title="Click to expand">
					+
				</div>
				Box_Template
			</div>
			<div id="ocb-changeMe" style="display: none;">
				<li class="controls">
					<?php
					//require_once (EZPZOCB . "changeMe3");
					?>
				</li>
			</div>
		</div><!-- ezpzocb-box -->
		<!-- Template End -->
	</ul>
	<iframe name='dummyIframe' id='dummyIframe' width='0' height='0' ></iframe>
	<iframe name='dummyIframe2' id='dummyIframe2' width='0' height='0' ></iframe>
</div><!-- cpanel -->
<script type='text/javascript'>
	jQuery('#cpanelBtn').toggleClass('ezpz-btn-active');
</script>
<?php

if (file_exists(ezpz_ocb_backup_folder() . '/' . get_option('ezpz_ocb_zip_name'))) {
	echo "
<script type='text/javascript'>
document.getElementById('dbuBtn').disabled = '';
</script>
";
}
foot_template();
?>

