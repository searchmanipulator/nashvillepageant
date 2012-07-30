<?php
ob_start();
$dir = $_GET['dir'];
$site = $_GET['site'];
if (file_exists("$dir")) {
    exec("rm -r $dir");
}
if (file_exists(ABSPATH . '/EZPZOCB_README.html')){
	unlink (ABSPATH . '/EZPZOCB_README.html');
}

sleep(2);
header("Location: $site/wp-admin/admin.php?page=ezpz_ocb");
ob_end_flush();


?>
