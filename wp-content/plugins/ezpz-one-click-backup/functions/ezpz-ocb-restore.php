<?php

function generate_restore($directory) {

    global $wpdb;

    $zip_name = get_option('ezpz_ocb_zip_name');
    $datestamp = date('l, F jS, Y \a\t g:ia', ezpz_time());

    $blog_url = site_url();
    $blog_name = get_bloginfo('name');
    $plugin_home_url = site_url() . '/wp-admin/admin.php?page=ezpz_ocb';
    $wp_dir = str_replace('/wp-content/plugins', '', WP_PLUGIN_DIR);
    $wp_dir_name_tmp = explode('/', $wp_dir);
    $wp_dir_name = end($wp_dir_name_tmp);
    $wp_temp_dir = ABSPATH . "EZPZ_RESTORATION_FILES/$wp_dir_name" . "_EZPZ_TEMP";
    $wp_parent_dir = str_replace("/$wp_dir_name", "", $wp_dir);
    $wp_orig_dir = ABSPATH . "EZPZ_RESTORATION_FILES/$wp_dir_name" . "_EZPZ_COPY";
    $wp_orig_dir_name = "$wp_dir_name" . "_EZPZ_COPY";
    $abspath = ABSPATH;


    $db_user = DB_USER;
    $db_password = DB_PASSWORD;
    $db_host = DB_HOST;
    $db_name = DB_NAME;
    $sql_file = "$wp_parent_dir/EZPZ_RESTORATION_FILES/EZPZ_DB.sql";
    $sql_dump_file = "$wp_orig_dir/EZPZ_DB_DUMP/EZPZ_DB.sql";
    $sql_insert_cmd = "mysql --host=$db_host --user=$db_user --password=$db_password  $db_name < $sql_file";

    $ckmrk = "&nbsp;<span style='font-family: tahoma, sans-serif; color: green; font-weight: 900;'> &#10004;<small>OK</small></span>";
    $failed = "&nbsp;<span style='font-family: tahoma, sans-serif; color: darkred; font-weight: 900;'> X<small> FAILED</small></span>";
    $permissions = get_permissions();
    $restore_script = <<<RESTORE_SCRIPT
<?php
\$padstr = str_pad("",1024," ");
echo "<!-- \$padstr -->";

\$cmd = "mv $wp_dir/$zip_name $wp_dir/EZPZ_RESTORATION_FILES/$zip_name";
exec(\$cmd);
flush();
ob_flush();

echo "
<head><title>Restoring $blog_name</title></head>
<h2 style='text-align: center; font-family: Tahoma,Helvetica,Arial,sans-serif;'>
EZPZ One Click Backup Restoration</h2>
<p>This application will attempt to restore your <b>$blog_name</b>
WordPress installation to the way it was on $datestamp.</p>";
echo "
<p>No files will be deleted during the restoration and a fresh database backup
will be performed before restoration begins.</p>";
flush();
ob_flush();
sleep(2);

echo "<li>Creating a backup of  the current database. <small>(EZPZ_RESTORATION_FILES/EZPZ_DB_DUMP.sql)</small>";
db_dump2("$db_host", "$db_user", "$db_password", "$db_name", "$wp_dir/EZPZ_RESTORATION_FILES/EZPZ_DB_DUMP.sql");
exec("$sql_insert_cmd");
flush();
ob_flush();
sleep(2);

echo " $ckmrk</li><li>Backing up the current $blog_name file structure. <small>(EZPZ_RESTORATION_FILES/orig_$wp_dir_name.zip)</small>";
chdir('$wp_dir');
\$cmd = "zip -r -0 $wp_dir/EZPZ_RESTORATION_FILES/orig_$wp_dir_name.zip * -x \*EZPZ_RESTORATION_FILES\*";
exec(\$cmd);
chdir('$wp_dir/EZPZ_RESTORATION_FILES');
flush();
ob_flush();

echo " $ckmrk</li><li>Restoring database to $datestamp.";
empty_directory('$wp_dir', false);
flush();
ob_flush();
sleep(2);

echo " $ckmrk</li><li>Restoring file system to $datestamp.";
\$cmd = "unzip $wp_dir/EZPZ_RESTORATION_FILES/$wp_dir_name.zip -d $wp_dir";
exec(\$cmd);


echo " $ckmrk</li><br/><iframe width='100%' height='400px' src ='$blog_url' ></iframe>";

echo "<center><h4>Restoration Complete!<br/>
<a href='$plugin_home_url' target='_self'>Go To EZPZ OCB on $blog_name</a>
</center></h4>";

function db_dump2(\$host, \$user, \$pass, \$name, \$backup_file) {

    \$link = mysql_connect(\$host, \$user, \$pass);
    \$return = '';
    mysql_select_db(\$name, \$link);

    //get all of the tables
    \$tables = array();
    \$result = mysql_query('SHOW TABLES');
    while (\$row = mysql_fetch_row(\$result)) {
        \$tables[] = \$row[0];
    }


    //cycle through
    foreach (\$tables as \$table) {

        \$result = mysql_query('SELECT * FROM ' . \$table);
        \$num_fields = mysql_num_fields(\$result);

        \$return.= 'DROP TABLE ' . \$table . ';';
        \$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE ' . \$table));
        \$return.= '\n\n' . \$row2[1] . ';\n\n';

        for (\$i = 0; \$i < \$num_fields; \$i++) {
            while (\$row = mysql_fetch_row(\$result)) {
                \$return.= 'INSERT INTO ' . \$table . ' VALUES(';
                for (\$j = 0; \$j < \$num_fields; \$j++) {
                    \$row[\$j] = addslashes(\$row[\$j]);
                    \$row[\$j] = preg_replace('/\n/', '/\\n/', \$row[\$j]);
                    if (isset(\$row[\$j])) {
                        \$return.= '"' . \$row[\$j] . '"';
                    } else {
                        \$return.= '""';
                    }
                    if (\$j < (\$num_fields - 1)) {
                        \$return.= ',';
                    }
                }
                \$return.= ');\n';
            }
        }
        \$return.='\n\n\n';
    }

    //save file
    \$handle = fopen(\$backup_file, 'w+');
    fwrite(\$handle, \$return);
    fclose(\$handle);
}

function empty_directory(\$dir, \$DeleteMe=false) {
    if(!\$dh = @opendir(\$dir)) return;
    while (false !== (\$obj = readdir(\$dh))) {
        if(\$obj=='.' || \$obj=='..') continue;
        if(strpos(\$dir, 'EZPZ_RESTORATION_FILES')) continue;
        if (!@unlink(\$dir.'/'.\$obj)) empty_directory(\$dir.'/'.\$obj, true);
    }

    closedir(\$dh);
    if (\$DeleteMe){
        @rmdir(\$dir);
    }
}
?>
RESTORE_SCRIPT;


    $restore_php = "$directory/EZPZ_RESTORE.php";

    file_put_contents($restore_php, $restore_script);
}

?>