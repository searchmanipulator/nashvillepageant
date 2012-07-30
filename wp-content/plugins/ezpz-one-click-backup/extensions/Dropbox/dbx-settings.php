<?php

$dbx_html = <<<DBXHTML
    <h2 class="ezpz-title">Dropbox Extension Settings</h2><br/>
    <form name="ezpz_ocb_dropbox_settings" method="post" action="$form_action">
        <input type="hidden" name="dropbox-hidden" value="Y" />
        <ul>
        $header
            <input type="hidden" name="license" value="$db_license" /><?php } ?>
            <li>Automatically save backups to Dropbox: <input type="checkbox" name="active" value="active" $db_active $db_ckbox_disabled /></li>
            <li>Your Dropbox e-mail address: <input style="width: 220px;" type="text" name="mail" value="$db_mail" /></li>
            <li>Your Dropbox password: <input style="width: 220px;" type="password" name="pass" value="$db_pass" /></li>
            <li>Dropbox directory: EZPZ OCB Backups/<input style="width: 220px;" type="text" name="folder" value="$db_folder" /></li>
            <li><small>If you're having trouble with files uploading to your Dropbox try the split zip file option.</small>
                <br/>Split the zip file for Dropbox. <input type="checkbox" name="split" value="split" $split_zip />
                                                            &nbsp;&nbsp;&nbsp;&nbsp;I primarily use: <input type="radio" name="os" value="windows" $os_windows > Windows&nbsp;&nbsp;&nbsp;</input>
                <input type="radio" name="os" value="linux" $os_linux > Linux&nbsp;&nbsp;&nbsp;</input>
                <input type="radio" name="os" value="both" $os_both > Both&nbsp;&nbsp;&nbsp;</input></li>
            <li><input type="submit" name ="save" value="Save Dropbox Extension Settings" /></li>

        </ul>
    </form>
    <p>Don&apos;t have $drpbximg <a href="http://db.tt/n8su2Cv" target="_new" >Get 2GB free online storage here.</a></p>
DBXHTML;
?>
