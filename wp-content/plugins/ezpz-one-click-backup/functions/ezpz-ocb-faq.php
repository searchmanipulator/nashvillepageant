<?php
global $wpdb;

?>
<style type='text/css'>
	 #ezpzocbfaq div.faq-answer {
		display: none;
	}
</style>
<?php echo restore_readme(); ?>
	<h3 title='Click to toggle answer' onclick="toggle_visibility('a-2')">Q: How do I use the Dropbox extension?</h3>
	<div class="faq-answer" id='a-2'><p>A: If you don't already have a Dropbox account

            you can get 2gb of free online storage by visiting <a href="http://db.tt/n8su2Cv" target="_blank">

                here</a>.</p>

        <p>Go to Dropbox Settings and enter your Dropbox email and password. Check the box

            to "Automatically save backups to Dropbox" and your done. All of your backups

            , manual and scheduled, will now be saved to your Dropbox account which

            in turn downloads a copy to your computer(s) connected to the Dropbox account.</p>
	</div>
	<h3 title='Click to toggle answer' onclick="toggle_visibility('a-3')">Q: Why should I use a Background Backup?</h3>
	<div class="faq-answer" id='a-3'>
		<p>A: A Background Backup is exactly what it sounds like, a backup

            running in the background. By running in the background and not reporting directly to

            the browser the server can lower it's priority.</p>

        <p>This is much less demanding on your server's resources. It may take a little longer

            to complete the backup but the trade off is well worth it. It also allows you to close the page or

            even shut down your browser and the backup will continue.</p>
	</div>
	<h3 title='Click to toggle answer' onclick="toggle_visibility('a-4')">Q: What do I do if EZPZ OCB locks up during a backup?</h3>
	<div class="faq-answer" id='a-4'>
		<p>A: You can abort a running backup which seems to be locked up by clicking on the "Abort" button either in the status bar or on the manual backup page.</p>
		<p>If that doesn't work you can click the "Clear Backups" button on the control panel page. This will stop all backup processes and delete all backup's and other files used for running a backup. It will not change any of your option settings or affect any files outside the EZPZ OCB plugin.</p>
	</div>