<?php
header('Content-type: text/javascript');

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . "/wp-load.php");

$exceptions = array("velominati.com","","frank.dutchmonkey.com");

$albumid = dm_sanitize_var($_GET["albumid"]);
$album = dm_sanitize_var($_GET["album"]);
$width = dm_sanitize_var($_GET["width"]);
$height = dm_sanitize_var($_GET["height"]);

$root = dm_get_album_root();

$album_url = $root . ltrim($album, '/'); //"http://" . ltrim($album, '/');
$album_dir = get_option('DM_HOME_DIR') . $album;

$maxsize = ($width > $height) ? $width : $height;

$photos = dm_get_photo_list(get_option('DM_HOME_DIR') . $album);
$count = count($photos);

$size_thumb = "&width=60&height=40"; //"&width=" . get_option('DM_THUMBNAIL_SIZE') . "&height=" . get_option('DM_THUMBNAIL_SIZE');
$size_main = "&width=" . $maxsize . "&height=" . $maxsize;
$imagapp = plugins_url() . "/dm-albums/php/image.php?degrees=0&scale=yes&maintain_aspect=yes&quality=95&rounding=nearest&image=" . get_option('DM_HOME_DIR') . $album;

if(dm_is_mobile())
{
	$width = 300;
	$height = 300;
}
?>

var data = [
    <?php foreach($photos as $photo)
    {
    	$i++;
    	$photourl = $album_url . $photo;
    	$photocaption = dm_get_caption($album_dir . $photo);
    	$photolink = dm_get_link($album_dir . $photo);

    	$caption = "";

    	if(!empty($photocaption))	$caption = "<div class=\"galleria-caption\"><p> " . $photocaption . "</p></div>";

    	$layer = "description: '" . $caption . "'";

    	if(!empty($photolink))		$layer .= ",\n";

    	$link = "";

    	if(!empty($photolink))		$link = "link: '" . $photolink . "'\n";

	    ?>
		{
		   	thumb: '<?php echo $imagapp . $photo . $size_thumb; ?>',
	        image: '<?php echo $imagapp . $photo . $size_main; ?>',
	        big: '<?php echo $photourl; ?>',
	        <?php echo $layer; ?>
	        <?php echo $link; ?>
	    }<?php if($i < $count)	echo ","; ?>

    <?php  } ?>
];

$('#galleria-<?php echo $albumid; ?>').galleria({
    dataSource: data,
    transition: "<?php echo get_option("DM_TRANSITION_EFFECT"); ?>",
    transitionSpeed: <?php echo get_option("DM_TRANSITION_SPEED"); ?>,
    width: document.getElementById("galleria-<?php echo $albumid; ?>").clientWidth,
    height: document.getElementById("galleria-<?php echo $albumid; ?>").clientWidth * 0.75,
        lightbox: <?php if(get_option("DM_ALBUMS_LIGHTBOX") == "false") {?>false<?php } else { ?>true<?php } ?>,
        idleTime: 2000,
        queue: false,
        layerFollow: false,
        popupLinks: <?php if(get_option("DM_ALBUMS_EXTERNAL_LINK_TARGET") == "_top") {?>false<?php } else { ?>true<?php } ?>,
        fullscreenCrop: false,
        <?php if(get_option("DM_PHOTOALBUM_SLIDESHOW_AUTOPLAY") == "true") {?>autoplay: true,<?php } ?>
        <?php if(get_option("DM_SHOW_NAVIGATION_HINTS") == "false") {?>showImagenav: false,<?php } ?>
        dummy: '<?php echo plugins_url(); ?>/dm-albums/galleria/themes/classic/dummy.png',
        extend:function() {
            this.attachKeyboard({
                left: this.prev,
                right: this.next
            });
        },
        debug: false
    });

<?php if(dm_is_mobile()) { ?>

var g_ORIENTATION_CHANGE = false;

window.onorientationchange = function()
{
	if(g_ORIENTATION_CHANGE)	clearTimeout(g_ORIENTATION_CHANGE);
	g_ORIENTATION_CHANGE = setTimeout("dm_resize_gallerias()", 500);
}
<?php } ?>