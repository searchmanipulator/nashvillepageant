<?php

error_reporting(0);

$DM_ALBUMS_IMAGETYPE_PNG = 0;
$DM_ALBUMS_IMAGETYPE_GIF = 1;
$DM_ALBUMS_IMAGETYPE_JPG = 2;
$DM_ALBUMS_IMAGETYPE_UNKNOWN = -1;

if(!defined(DM_CACHE_DIRECTORY))
{
	if(dm_is_wamp())	define(DM_CACHE_DIRECTORY, "cache");
	else				define(DM_CACHE_DIRECTORY, ".cache");
}

function dm_get_album_root()
{
	$root = get_option('siteurl') . "/";

	if(get_option('DM_HOME_DIR') != get_option('DM_ALBUMS_CORE_DEFAULT_HOME_DIR'))	$root = "http://";

	return $root;
}

function dm_download()
{
	//dm_logerrors("dm_download: ");

	$file = basename($_GET["file"]);

	//dm_logerrors("dm_download -> file: " . $file);

	$currdir = str_replace(dm_get_album_root(), get_option('DM_HOME_DIR'), dirname($_GET["file"]));

	//dm_logerrors("dm_download -> curdir: " . $currdir);

	$filename = dm_sanitize($currdir, 1) . "/" . $file;

	//dm_logerrors("dm_download -> filename: " . $filename);

	if(dm_is_image($filename))
	{
		$filesize = filesize($filename);

		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	    header('Content-Description: File Transfer');
	    header('Content-Type: application/octet-stream');
	    header('Content-Length: ' . $filesize);
	    header('Content-Disposition: attachment; filename="' . $file . '"');
	    readfile($filename);
	}
}

function dm_loadjavascript()
{
	?>
	<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js?ver=3.3.1'></script>
	<script type='text/javascript' src='<?php echo plugins_url(); ?>/dm-albums/galleria/galleria-1.2.6.min.js?ver=3.3.1'></script>
	<script type='text/javascript' src='<?php echo plugins_url(); ?>/dm-albums/javascript/galleria-common.js?ver=3.3.1'></script>
	<script type='text/javascript' src='<?php echo plugins_url(); ?>/dm-albums/galleria/themes/classic/galleria.classic.min.js?ver=3.3.1'></script>
	<?php
}

function dm_logerrors($message)
{
	$fh = fopen("error_log", "a+");

	$timestamp = date("D M j G:i:s T Y");

	$debug = "[$timestamp]\t$message\n";

	fwrite($fh, $debug);
}

function dm_is_mobile()
{
	$useragent=$_SERVER['HTTP_USER_AGENT'];

	if(preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
		return true;
}

function dm_mkdir($folder)
{
	if(!function_exists("wp_mkdir_p"))
	{
		//mkdir($folder, 0777, true);
		@mkdir($folder);
		$stat = @stat(dirname($folder));
		$dir_perms = $stat['mode'] & 0007777;  // Get the permission bits.
		@chmod($folder, $dir_perms);
	}

	else
	{
		wp_mkdir_p($folder);
	}
}

function dm_get_imagetype($filename)
{
	global $DM_ALBUMS_IMAGETYPE_PNG;
	global $DM_ALBUMS_IMAGETYPE_GIF;
	global $DM_ALBUMS_IMAGETYPE_JPG;
	global $DM_ALBUMS_IMAGETYPE_UNKNOWN;

	if(function_exists("exif_imagetype_XX"))
	{
		$img_type = exif_imagetype($filename);

		if($img_type == IMAGETYPE_PNG)
			return $DM_ALBUMS_IMAGETYPE_PNG;

		else if($img_type == IMAGETYPE_GIF)
			return $DM_ALBUMS_IMAGETYPE_GIF;

		else if($img_type == IMAGETYPE_JPEG)
			return $DM_ALBUMS_IMAGETYPE_JPG;

		else
			return $DM_ALBUMS_IMAGETYPE_UNKNOWN;
	}

	else
	{
		$img_type = strtoupper(pathinfo($filename, PATHINFO_EXTENSION));

		if($img_type == "PNG")
			return $DM_ALBUMS_IMAGETYPE_PNG;

		else if($img_type == "GIF")
			return $DM_ALBUMS_IMAGETYPE_GIF;

		else if($img_type == "JPG" || $img_type == "JPEG")
			return $DM_ALBUMS_IMAGETYPE_JPG;
		else
			return $DM_ALBUMS_IMAGETYPE_UNKNOWN;
	}
}

function dm_is_image($filename)
{
	global $DM_ALBUMS_IMAGETYPE_UNKNOWN;

	if(dm_get_imagetype($filename) == $DM_ALBUMS_IMAGETYPE_UNKNOWN)	return false;
	else															return true;
}

function dm_get_album_list($dirname, $order_by = "alpha")
{
	$dir = opendir($dirname);

	$contents = array();

	if(!file_exists($dirname))	dm_mkdir($dirname);

	$i = 0;

	// Read files into array
	while(false !== ($file = readdir($dir)))
	{
		$type = filetype($dirname . $file);

		if($type == "dir" && ($file != '.' && $file != '..' && $file != DM_CACHE_DIRECTORY))
		{
			$contents[$i] = array($dirname . $file . "/", filemtime($dirname . $file . "/"));

			$i++;
		}
	}

	closedir($dir);

	if($order_by == "alpha")	usort($contents, 'dm_get_album_alphacmp');
	else						usort($contents, 'dm_get_album_datecmp');

	$contents = array_values($contents);

	return $contents;
}

function dm_get_photo_list($photoalbum, $refresh = false)
{
	if(!$refresh)
	{
		dm_refresh_photo_sortorder($photoalbum);

		$album_sortorder = dm_get_sortorder($photoalbum);

		if(strlen(trim($album_sortorder)) > 0)
		{
			return explode(";", $album_sortorder);
		}
	}

	$dir = opendir($photoalbum);

	$contents = array();

	$i = 0;

	// Read files into array
	while(false !== ($file = readdir($dir)))
	{
		if(dm_get_imagetype($dir . $file) >= 0)
		{
			$contents[$i] = $file;

			$i++;
		}
	}

	closedir($dir);

	natcasesort($contents);

	$contents = array_values($contents);

	return $contents;
}

function dm_get_caption($photo)
{
	$directory = dirname($photo);
	$picturename = basename($photo);

	$photoid = "dma-" . basename($directory) . "-" . $picturename . "-caption";

	$caption = htmlspecialchars(get_option($photoid), ENT_QUOTES);

	if(!empty($caption))	return $caption;

	else if(file_exists(dirname($photo) . "/browse.cap"))
	{
		$lines = file(dirname($photo) . "/browse.cap");

		foreach($lines as $line)
		{
			//line starts with the image name, remove image name and leading whitespace, display caption

		   $matches = array();

		   $matchcount = 0;

		   $matchcount = preg_match_all("/(^" . basename($photo) . ":\s)(.*)/i", $line, $matches);

		   if($matchcount > 0)
		   {
				$filename = $matches[0][1];
			   	$caption = trim($matches[2][0]);

			   	if(strlen($caption) > 0)	return htmlspecialchars(trim("$caption"), ENT_QUOTES);
				else 						return htmlspecialchars(trim($caption), ENT_QUOTES);
		   }
		}
	}

	return $caption;
}

function dm_put_caption($photo, $displaycaption)
{
	$directory = dirname($photo);
	$picturename = basename($photo);

	$photoid = "dma-" . basename($directory) . "-" . $picturename . "-caption";

	update_option($photoid, $displaycaption);
}

function dm_delete_caption($photo)
{
	$directory = dirname($photo);
	$picturename = basename($photo);

	$photoid = "dma-" . basename($directory) . "-" . $picturename . "-caption";

	delete_option($photoid);
}

function dm_get_link($photo)
{
	$directory = dirname($photo);
	$picturename = basename($photo);

	$photoid = "dma-" . basename($directory) . "-" . $picturename . "-link";

	$caption = htmlspecialchars(get_option($photoid), ENT_QUOTES);

	if(!empty($caption))	return $caption;

	else if(file_exists(dirname($photo) . "/browse.cap"))
	{
		$lines = file(dirname($photo) . "/browse.cap");

		foreach($lines as $line)
		{
			//line starts with the image name, remove image name and leading whitespace, display caption

		   $matches = array();

		   $matchcount = 0;

		   $matchcount = preg_match_all("/(^" . basename($photo) . "_LINK:\s)(.*)/i", $line, $matches);

		   if($matchcount > 0)
		   {
				$filename = $matches[0][1];
			   	$caption = trim($matches[2][0]);

			   	if(strlen($caption) > 0)	return trim("$caption");
				else 						return trim($caption);
		   }
		}

		return $caption;
	}

}

function dm_put_link($photo, $displaycaption)
{
	$directory = dirname($photo);
	$picturename = basename($photo);

	$photoid = "dma-" . basename($directory) . "-" . $picturename . "-link";

	update_option($photoid, $displaycaption);
}

function dm_delete_link($photo)
{
	$directory = dirname($photo);
	$picturename = basename($photo);

	$photoid = "dma-" . basename($directory) . "-" . $picturename . "-link";

	delete_option($photoid);
}

function dm_get_title($photoalbum)
{
	$ablum_title = "";

	$albumid = "dma-" . basename($photoalbum) . "-title";

	$ablum_title = htmlspecialchars(get_option($albumid), ENT_QUOTES); //get_option($albumid);

	if(!empty($ablum_title))	return $ablum_title;

	else if(file_exists($photoalbum . "/browse.cap"))
	{
		$lines = file($photoalbum . "/browse.cap");

		foreach($lines as $line)
		{
			//line starts with the image name, remove image name and leading whitespace, display caption

		   $matches = array();

		   $matchcount = 0;

		   $matchcount = preg_match_all("/(^DM_ALBUM_TITLE:\s)(.*)/i", $line, $matches);

		   if($matchcount > 0)
		   {
				$filename = $matches[0][1];
			   	$ablum_title = trim($matches[2][0]);

			   	if(strlen($ablum_title) > 0)	return trim("$ablum_title");
				else 							return trim($ablum_title);
		   }
		}

		return $ablum_title;
	}
}

function dm_put_title($album, $displaycaption)
{
	$directory = $album;

	$albumid = "dma-" . basename($directory) . "-title";

	update_option($albumid, $displaycaption);
}

function dm_delete_title($album)
{
	$directory = $album;

	$albumid = "dma-" . basename($directory) . "-title";

	delete_option($albumid);
}

function dm_get_sortorder($photoalbum)
{
	//echo "dm_get_sortorder: " . $photoalbum . "<br/>";

	$ablum_sortorder = "";

	$albumid = "dma-" . basename($photoalbum) . "-sortorder";

	$ablum_sortorder = get_option($albumid);

	if(!empty($ablum_sortorder))	return $ablum_sortorder;

	else if(file_exists($photoalbum . "/browse.cap"))
	{
		$lines = file($photoalbum . "/browse.cap");

		foreach($lines as $line)
		{
			//line starts with the image name, remove image name and leading whitespace, display caption

		   $matches = array();

		   $matchcount = 0;

		   $matchcount = preg_match_all("/(^DM_ALBUM_SORTORDER:\s)(.*)/i", $line, $matches);

		   if($matchcount > 0)
		   {
				$filename = $matches[0][1];
			   	$ablum_sortorder = trim($matches[2][0]);

			   	if(strlen($ablum_sortorder) > 0)	return trim("$ablum_sortorder");
				else 								return trim($ablum_sortorder);
		   }
		}
	}
	/*

	//echo "ablum_sortorder: " . $ablum_sortorder . "<br/>";

	$album_cleansed_sortorder = array();

	if(is_array($ablum_sortorder))
	{
		foreach($ablum_sortorder as $photo)
		{
			if(file_exists($photoalbum . "/$photo"))	$album_cleansed_sortorder[] = $photo;
		}
	}

	return $album_cleansed_sortorder;*/
}

function dm_put_sortorder($album, $ablum_sortorder)
{
	$directory = $album;

	$albumid = "dma-" . basename($directory) . "-sortorder";

	update_option($albumid, $ablum_sortorder);
}

function dm_delete_sortorder($album)
{
	$directory = $album;

	$albumid = "dma-" . basename($directory) . "-sortorder";

	delete_option($albumid);
}

function dm_remove_photo_from_sortorder($photoalbum, $photo)
{
	$album_sortorder = dm_get_sortorder($photoalbum);

	if(strlen(trim($album_sortorder)) > 0)
	{
		$album_sortorder = str_replace($photo, "", $album_sortorder);
		$album_sortorder = str_replace(";;", "", $album_sortorder); //clean up
		dm_put_sortorder($photoalbum, $album_sortorder);
	}
}

function dm_add_photo_to_sortorder($photoalbum, $photo)
{
	$album_sortorder = dm_get_sortorder($photoalbum);

	if(strlen(trim($album_sortorder)) > 0)
	{
		if(strpos($album_sortorder, $photo) === FALSE)
		{
			$album_sortorder = $album_sortorder . ";$photo";
			dm_put_sortorder($photoalbum, $album_sortorder);
		}
	}
}

function dm_reset_photo_sortorder($photoalbum)
{
	$album_sortorder = dm_get_sortorder($photoalbum);

	if(strlen(trim($album_sortorder)) > 0)
	{
		dm_put_sortorder($photoalbum, "");
	}
}

function dm_refresh_photo_sortorder($photoalbum)
{
	$album_sortorder = dm_get_sortorder($photoalbum);

	if(strlen(trim($album_sortorder)) > 0)
	{
		$album = explode(";", $album_sortorder);
		$photos = dm_get_photo_list($photoalbum, true);

		$missing = array_diff($photos, $album);

		$album_sortorder = $album_sortorder . ";" . implode(";", $missing);

		$album_sortorder = rtrim($album_sortorder, ";");

		$album = explode(";", $album_sortorder);

		$missing = array_diff($album, $photos);

		foreach($missing as $item)
		{
			$album_sortorder = str_replace($item . ";", "", $album_sortorder);
		}

		$album_sortorder = rtrim($album_sortorder, ";");

		dm_put_sortorder($photoalbum, $album_sortorder);
	}
}

function dm_get_album_datecmp($a, $b)
{
	return ($a[1] > $b[1]) ? -1 : 1;
}

function dm_get_album_alphacmp($a, $b)
{
	return (strtolower(basename($a[0])) < strtolower(basename($b[0]))) ? -1 : 1;
}

function dm_get_album_delete($album)
{
	$handle = opendir($album);

	while (false!==($item = readdir($handle)))
	{
		$type = filetype($item);

		if($item != '.' && $item != '..')
		{
			if($type != "link" && is_dir($album.'/'.$item))
			{
				dm_get_album_delete($album.'/'.$item);
			}

			else
			{
				unlink($album.'/'.$item);
				dm_remove_photo_from_sortorder($album.'/'.$item);
				dm_delete_caption($album.'/'.$item);
				dm_delete_link($album.'/'.$item);
			}
		}
	}

	closedir($handle);

	rmdir($album);

	dm_delete_title($album);
	dm_delete_sortorder($album);
}

function dm_sanitize($folder, $soft = 0)
{
	$folder = str_replace("..", "", $folder);

	if($soft == 0)
	{
		$bad_chars = "/[^\w\s\(\)\:\.-]+/";
		$replacement_chars = "";

		$folder = trim(preg_replace($bad_chars, $replacement_chars, $folder), '/\\');

		$folder = str_replace("/", "", $folder);
		$folder = str_replace("\\", "", $folder);
	}

	return $folder;
}

function dm_sanitize_var($var)
{
	$var = html_entity_decode(stripslashes($var));
	$var = htmlentities($var, ENT_QUOTES, 'cp1252');

	return $var;
}

function dm_getuploaddirectory()
{
	global $blog_id;

	if(get_option('DM_ALBUMS_UPLOADDIR') == "" || get_option('DM_ALBUMS_UPLOADDIR') == "/")
	{
		update_option('DM_ALBUMS_UPLOADDIR', get_option('DM_ALBUMS_CORE_DEFAULT_UPLOADDIR'));
	}

	return str_replace("{BLOG_ID}", $blog_id, get_option('DM_ALBUMS_UPLOADDIR'));
}

function dm_user_uploaddirectory()
{
	$DM_UUP = get_option('DM_ALBUMS_UUP');

	if($DM_UUP == 1)
	{
		global $current_user, $_POST, $_GET;
		get_currentuserinfo();

		$user_upload_directory = $current_user->user_email;

		if(!isset($user_upload_directory) || empty($user_upload_directory))
		{
			$user_upload_directory = isset($_POST["dm_uud"]) ? $_POST["dm_uud"] : $_GET["dm_uud"];

			$user_upload_directory = str_replace("../", "", $user_upload_directory);
			$user_upload_directory = str_replace("/", "", $user_upload_directory);
			$user_upload_directory = str_replace("\\", "", $user_upload_directory);
			$user_upload_directory = str_replace("'", "", $user_upload_directory);
			$user_upload_directory = str_replace("\"", "", $user_upload_directory);

			$user_upload_directory = trim($user_upload_directory, '/\\');
		}

		//$user_upload_directory = str_replace("@", "_at_", $user_upload_directory);

		return $user_upload_directory . "/";
	}
}

function dm_is_wpmu()
{
	if(is_dir($_SERVER['DOCUMENT_ROOT'] . '/wp-content/mu-plugins')) return true;
	else return false;
}

function dm_isUserAdmin()
{
	global $blog_id;

	// NON WPMU AND ADMINS
	if(!dm_is_wpmu() && current_user_can('level_10'))	return true;

	// WPMU AND BASE BLOG
	if(dm_is_wpmu() && $blog_id == 1)	return true;

	return false;
}

function dm_is_wamp()
{
	//return eregi("WIN", strtoupper(php_uname()));
	if(strpos(ABSPATH, ":/") === TRUE && strpos(ABSPATH, ":/") == 1)	return true;
}

?>