<?php

if(!function_exists("get_galleria"))	require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/wp-load.php");

function dm_printalbum($path, $width = 0, $height = 0)
{
	/*$directory = str_replace(get_option("DM_HOME_DIR"), "", $directory);

	print(dm_getalbum("?currdir=$directory"));
	echo get_galleria($directory, (int) get_option('DM_PHOTOALBUM_APP_WIDTH'), get_option());*/

	if($width == 0)		$width = (int) get_option('DM_PHOTOALBUM_APP_WIDTH');
	if($height == 0)	$height = (int) get_option('DM_PHOTOALBUM_APP_HEIGHT');

	$thecontent = "";

	if(empty($path))	$thecontent = '<div class="dm-albums-fatal-error">Error: DM Albums is missing the required parameter, \'path\'.</div>';
	else				$thecontent = get_galleria($path, $width, $height);

	echo $thecontent;
}

?>