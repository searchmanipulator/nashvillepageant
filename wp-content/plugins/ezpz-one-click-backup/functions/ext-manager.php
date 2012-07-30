<?php
site_url();

function install_ext($extension) {
    if ($extension === 'dbx' && !has_ext('dbx')) {
        $old_dir = getcwd();
        chdir(EZPZOCB . "/extensions");
        // Remove existing Dropbox folder if it exists
        if(is_dir(EZPZOCB . "/extensions/Dropbox")){
            exec("rm -r Dropbox");
            }
        // Get Dropbox extension zip file
        $wget_cmd = "wget https://s3.amazonaws.com/ezpz-ocb/extensions/dbx_v1.0_auto.zip";
        exec($wget_cmd);
        // Unzip Dropbox extension zip file
        $unzip_cmd = "unzip dbx_v1.0_auto.zip";
        exec($unzip_cmd);
        // Remove Dropbox extension zip file if installation succeded
        if (file_exists (EZPZOCB . "/extensions/dbx_v1.0_auto.zip") && has_ext('dbx')) {
            unlink(EZPZOCB . "/extensions/dbx_v1.0_auto.zip");
        }
        chdir($old_dir);
    }
}
?>
