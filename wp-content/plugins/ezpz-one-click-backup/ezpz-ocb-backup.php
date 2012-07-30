<?php
if (!file_exists(ezpz_ocb_backup_folder() . "/FILELOCK")){
	file_put_contents(ezpz_ocb_backup_folder() . "/FILELOCK", "");
	if (!ini_get('safe_mode')) {
		set_time_limit(1200); // 1200 seconds = 20 minutes
	}
//	$link =  tab(3) . "<a style='text-align: right; font-size: 75%;' class='ezpz-btn' href='" . ezpz_ocb_sp('abort') . "'>Abort</a>";
	$link =  tab(3) . "<a style='' class='ezpz-btn abort-btn' href='" . ezpz_ocb_sp('abort') . "'>&nbsp;&nbsp;&nbsp;Abort&nbsp;&nbsp;&nbsp;</a>";
	set_status('backup', "A manual backup is in progress. $link");
	
	backup_in_progress('Manual');
	while (!file_exists(EZPZOCB . "/backups/in-progress.php")){
		usleep(250000);
	}
	
	$zip_file = ezpz_ocb_backup_folder() . '/' . get_option('ezpz_ocb_zip_name');
	global $wpdb;
	
	$backup_dir = ezpz_ocb_backup_folder();
	
	$timeZone = get_option('ezpz_ocb_save_tz');
	
	$abort = ezpz_ocb_sp('abort');
	
	head_template('Backup', true);
	?>
		<script type="text/javascript">	
            var startTime=new Date();

            function currentTime(){
                var a=Math.floor((new Date()-startTime)/100)/10;
                var m=Math.floor(a/60);
                var s=Math.floor(a-(m*60));
                if (s<10) s='0'+s;
                var t=m+':'+s;
                document.getElementById('endTime').innerHTML=t;
            }

            window.onload=function(){
                clearTimeout(loopTime);
            }
		var loopTime=setInterval("currentTime()",10);
		
		function jsSleep(ms)
		{
			var dt = new Date();
			dt.setTime(dt.getTime() + ms);
			while (new Date().getTime() < dt.getTime());
		}
		function jsTimestamp(){
		    tstmp = new Date();    
		    return tstmp.getTime();
		}
	 
		document.getElementById('cpanelBtn').disabled = 'disabled';
		document.getElementById('bbuBtn').disabled = 'disabled';
		document.getElementById('dbuBtn').disabled = 'disabled';

	</script>
	
	<div id='generated' style='
		font-size: 1.5em;
		padding: 0 0 0 12px;
	'>
	<div style="border: 1px #ffffff solid; margin: 20px;  font-size: 12px;">
		<div style='float: left; font-weight: bold;'>Elapsed Time: <span id="endTime">0:00</span></div>
		<div style='float: right;'><a id='abort-btn' style='text-align: right;' class='ezpz-btn' href='<?php echo ezpz_ocb_sp('abort'); ?>'>Abort This Backup</a></div>
		</div>
		<hr style='height: 5px; color: #ffffff; border: none;' />
	

	<script type="text/javascript">	
		
		function jsSleep(ms)
		{
			var dt = new Date();
			dt.setTime(dt.getTime() + ms);
			while (new Date().getTime() < dt.getTime());
		}
		function jsTimestamp(){
		    tstmp = new Date();    
		    return tstmp.getTime();
		}
	 
	</script>
	<script type="text/javascript">
	var refreshId;
	
	jQuery(document).ready(function() {
		jsSleep(2500);
		jQuery('#ajax_output').load('<?php echo get_write_file_url() . '?a=';?>' + jsTimestamp());	
		refreshId = setInterval(function() {
		jQuery("#ajax_output").load('<?php echo get_write_file_url() . '?a=';?>' + jsTimestamp());}, 1000);
//		jQuery.ajaxSetup({ cache: false });
		jQuery("#cpanelBtn").attr("disabled", "disabled");
		jQuery("#mbuBtn").attr("disabled", "disabled");
		jQuery("#bbuBtn").attr("disabled", "disabled");
		jQuery("#dbuBtn").attr("disabled", "disabled");
	});
		</script>
		<div id="ajax_output" title=""><center><img src='<?php echo EZPZOCB_URL; ?>/images/loading.gif' height='50' width='50' /></center></div><!-- End ajax-output div -->
		<script type="text/javascript">
			window.onload = function() {
				document.getElementById("abort-btn").style.display = "none";				
				jQuery("#cpanelBtn").removeAttr("disabled"); 
				jQuery("#dbuBtn").removeAttr("disabled"); 
				jsSleep(2000);
				clearInterval(refreshId);
	
		</script>
		
	</div>
	<iframe name="invisible_iframe" frameborder="no" width="0" height="0" src="../wp-admin/admin.php?page=ezpz_ocb&sp=bu_ajax"><p>Iframes must be enabled for EZPZ OCB to function properly.</p>
		<p>If you are using any Iframe blocking software make sure it is disabled for your blog.</p></iframe>
	<?php foot_template(); ?>
<?php } ?>
