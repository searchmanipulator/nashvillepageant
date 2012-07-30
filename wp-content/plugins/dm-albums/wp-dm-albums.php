<?php
/*
Plugin Name: DM Albums
Description: DM Albums is an inline photo album/gallery plugin that displays high quality images and thumbnails perfectly sized to your blog.
Plugin URI:  http://www.dutchmonkey.com/?file=products/dm-albums/dm-albums.html
Version:     3.1.4
Author:      Frank D. Strack
Author URI:  http://www.dutchmonkey.com/
*/

/*  Copyright 2005-2012  Frank D. Strack  (email : development@dutchmonkey.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/*
Change log:

	3.1.4
		* Two new options added: Transition Effect and Transition Speed.

	3.1.4
		* Fullscreen bugfix. No functional change over 3.1.3.

	3.1.3.4
		* Emergency security vulnerability fix. No functional change over 3.1.3.
		* Implemented QJuery Noconflics mode for improved JQuery library support

    3.1.3.3
        * Bug fix in "Manual" setting. No functional change over 3.1.3.

    3.1.3.2
        * Fixed Fullscreen "image not found" bug. No functional change over 3.1.3.

    3.1.3.1
        * Refreshed commit to Wordpress SVN database. No functional change over 3.1.3.

	3.1.3
		* Fixed dm-albums-external.php (you also need to call dm_loadjavascript(); somewhere on your page prior to calling dm_printalbum()
		* Re-introduced download option for photos
		* Added options for choosing which JQuery library to load, or to go into Manual mode to allow for the library to be specified in theme
		* Improved IE 8 support
		* Improved special characters handling on captions
		* Minor UI tweaks

	3.1.2
		* De-registered jquery to help avoid conflics

	3.1.1 8 January 2012
		* Full-screen bug fixed.

	3.1 7 January 2012
		* Fixed code-commit issue

	3.0 6 January 2012
		* Major update to front-end.
		* Front-end now uses Galleria (http://galleria.io/) for display
		* Full javascript/css support
		* Full iPhone/iPad/Android support
		* Major bugfixes
		* Caption/links/Sort order data moved to WordPress Options database
		* [album:] syntax dropped for WordPress shortcode support
		* [album:] syntax still suppported for legacy albums
		* Some config options have been removed
		* New config options introducted
		* Custom stylesheet bug fixed
		* Caption deletion bug fixed

	2.4.8.2 14 November 2010
		* Misc. bug fixed for AJAX support.
		* Disabled inline caption editing to protect against accidental caption loss.

	2.4.8.1 14 April 2010
		* Further improvements for Windows server (i.e. WAMP) support.

	2.4.8 13 April 2010
		* Improved Windows server (WAMP) support.

	2.4.7 6 April 2010
		* Fixed some small CSS bugs
		* Improved support for Google Chrome and Safari
		* Misc bug fixes
		* Added support in the image processing API for cropping images (not used in photo rendering)

	2.4.6 11 February 2010
		* Fixed typos

	2.4.5 11 February 2010
		* Added option to parse comments for [album:] syntax
		* Improved WAMP support
		* Misc bug fixes

	2.4.4 29 December 2009
		* Fixed compatibility issue with versions of WP prior to 2.9

	2.4.3 29 December 2009
		* Added controls for viewing/disabling all controls including "powered by", "slideshow", "fullscreen", and "browsing hints".
		* Misc minor bug fixes

	2.4.2 22 December 2009
		* In compliance with the fourth restriction of the WordPress Plugin Doctrine (http://wordpress.org/extend/plugins/about/), we have updated our plugin to make the Powered By tagline optionally displayed (off by default).

	2.4.1 18 December 2009
		* Minor mkdir patch to improve fault tollerance.

	2.4 18 December 2009
		* Security service updates, including an important download vulnerability patch
		* Photo Sorter (by popular request).  This gives drag-n-drop photo sorting capabilities through the DM Albums WordPress editor.  (Buggy in IE)
		* Photo Link support (by popular request).  The ability to add main photo links has been added.  Add your links through the DM Albums Album Detail Manager.
		* Misc minor bug fixes

	2.3.4 9 Novemeber 2009
		* Security service updates
		* Enhanced (beta) WAMP support
		* Misc minor bug fixes

	2.3.2 9 Novemeber 2009
		* Added php include for including DM Albums outside your WordPress installation.  More information: http://blog.dutchmonkey.com/product-release/dm-albums-external-demo/
		* Improved support for Safari

	2.3.1 31 October 2009
		* Service release containing several minor enhancements
		* Misc Bug fixes

	2.3 29 October 2009
		* Album Detail Manager added to allow adding captions and deleting individual photos out of albums.
		* Add support for external CSS style sheet for overriding default styles.  Place this stylesheet outside the dm-albums plugin directory to ensure WordPress does not delete your stylesheet during automatic updates.
		* Continued hardnening of upload/delete security.  Thanks to safety of nDarkness.com for continuing to assist in tightening things up.
		* Continued expansion of WPMU support and multi-user security.  Continued thanks to Adam of nDarkness.com for his tireless testing, coding, and recommendations.
		* Path mapping and upload permissions updates for more reliable file uploading and album management.  If you are having difficulty uploading and seeing your albums, use the recommended default settings as found in the DM Albums Options (Admin) panel.
		* PHP EXIF bug fix (for users without EXIF support on their systems.  This most often manifests itself in showing a blank DM Albums Options (Admin) panel.
		* Misc PHP Bug fixes
		* Misc CSS Bug fixes

	2.2 25 October 2009
		* Support for WPMU - Many thanks to Adam of nDarkness.com for his continued help testing, coding, and making recommendations to support WPMU.
		* Misc PHP Bug fixes
		* Misc CSS Bug fixes

	2.1.1 23 October 2009
		* Additional Security enhancements as outlined here: http://secunia.com/advisories/37119/
		* Misc Bug fixes to support PNG and GIF photo albums.

	2.1 22 October 2009
		* Security enhancements. Thanks to safety of nDarkness.com for alerting us to a vulnerability and recommending a solution.
		* Feature Request: Allow users to separate user upload directories.  Log into the DM Albums Admin Panel and set "Unique Author Upload Folders" to YES under "Advanced Settings".  This is turned off by default for backward compatibility.
		* Feature Request: Restrict "Advanced Settings" area to Administrators only.

	2.0.1 21 October 2009
		* Emergency security patch. Thanks to safety of nDarkness.com for alerting us to a vulnerability and recommending a solution.

	2.0 16 October 2009
		* Added support for IE 8
		* Added Photo Upload/Album Management support (directly through the WordPress Editor)
		* Added Support to automatically insert the [album: ] code syntax into post or page

	1.9.10 4 October 2009

		* Fixed bug when EXIF extensions are not installed to allow DM Albums to run without them
		* Added option to DM Albums Admin Panel to disable Loading Message
		* Added option to DM Albums Admin Panel to disable Loading Message only during Slideshows

	1.9.9.2 25 September 2009

		* Fix for issue introduced in 1.9.9.1 which was seen on some systems.

	1.9.9.1 24 September 2009

		* Added a light sharpening algorithm that was somehow left out of today's earlier release

	1.9.9 24 September 2009

		* Added "autoplay" for slideshow (by request)
		* Autoplay is enabled as a URL parameter: autoplay=true will enable autoplay
		* Fixed bug where some plugins tamper with URLs and break DM Album's "slideshow" and "fullscreen" buttons
		* Misc Bug Fixes

	1.9.8 3 August 2009

		* Added slideshow controls to main display (by request)
		* Misc Bug Fixes

	1.9.7 3 August 2009

		* Added quality option in admin panel (by request)

	1.9.6 30 July 2009

		* Caption editing bug fix

	1.9.5 24 July 2009

		* New Feature: Fade In/Out transision added to images.  This feature was highly requested and has been added
		* Additional security checks for more proactive detection of malicious use

	1.9.4 04 July 2009

		* Service Release: Minor bug fix in albums.php

	1.9.3 04 July 2009

		* Service Release: Patch to close security hole in albums.php

	1.9.2 25 June 2009

		* Service Release: Remove hotlinking of dm.css and made that file a local resource

	1.9.1 31 March 2009

		* Bug Fix: Corrected javascript errors on left/right arrow keys for navigating images when keyboard focus is on thumbnails

	1.9 23 March 2009

		* Bug Fix: Corrected issue to allow automatic update of plugins

	1.8 23 March 2009

		* Improvements:
			- Auto-centering of thumbnails</li>
			- Improved Keyboard Arrow Controls</li>
		* Misc Bug Fixes

	1.7 (19 Dec 2008)

		* Improvements:
			- Restructured files to improve ease of installation
		* New Feature:
			- Add option to merge subdirectories into one photo album (improved album management)

	1.6 (7 May 2008)

		* Improvements:
			- Improved image output quality
			- Support of Firefox 3.0 Beta (and, hopefully, the production release of FF3)

	1.5 (9 January 2008)

		* Improvements:
			- Fixed memory leak (thanks to Phong Long mailto:phonglong@gmail.com http://www.phonglong.com/)
			- Improved memory management and error reporting
			- Hide context menu after 5 seconds of inactivity (to hide menu when mouse leaves screen)
		* Misc Bug Fixes

	1.4 (7 January 2008)

		* New Features:
			- Context menu added with next, previous, slideshow options
			- EXIF Information displayed via context menu > Properties
			- Direct Download option via context menu
		* Check for GD lib in DM Albums Admin panel
		* Check for memory_limit > 32M
		* iFrame sizing improved for Windows Internet Explorer
		* Windows Internet Explorer 6 support improved
		* Misc Bug Fixes

	1.3 (6 January 2008)

		* New Features:
			- Context menu added with next, previous, slideshow options
			- EXIF Information displayed via context menu > Properties
			- Direct Download option via context menu
		* Check for GD lib in DM Albums Admin panel
		* Check for memory_limit > 32M
		* iFrame sizing improved for Windows Internet Explorer
		* Windows Internet Explorer 6 support improved
		* Misc Bug Fixes

	1.2 (12 December 2007)

		* Windows Compatability issues resolved
		* Misc Bug Fixes

	1.1 (7 November 2007)

		* WP Compatability issues resolved

	1.0 (25 October 2007)

		* Initial Release with several bug fixes

	0.9 (4 September 2007)

		* Initial Public Beta Version
*/

//error_reporting(0);

require_once('php/includes.php');

if($_GET["download"] == "yes")
{
	dm_download();
}

// Global variables
$DM_PHOTOALBUM_APP_VERSION = "3.1.4";
$DM_PHOTOALBUM_APP_DOCS = "http://www.dutchmonkey.com/wp-plugins/";

$DEFAULT_HOME = ABSPATH; //realpath($_SERVER['DOCUMENT_ROOT']) . "/";

if(dm_is_wamp())	$DEFAULT_HOME = realpath($_SERVER['DOCUMENT_ROOT']) . "\\";

// ****************************************
// DEFAULT CORE OPTIONS *** DO NOT MODIFY!

delete_option('DM_ALBUMS_CORE_DEFAULT_HOME_DIR');
delete_option('DM_ALBUMS_CORE_DEFAULT_PHOTOALBUM_APP');
delete_option('DM_ALBUMS_CORE_DEFAULT_ALBUM_PLUGIN_APP');
delete_option('DM_ALBUMS_CORE_DEFAULT_UPLOADDIR');

add_option('DM_ALBUMS_CORE_DEFAULT_PHOTOALBUM_APP', get_option('siteurl') . '/wp-content/plugins/dm-albums/dm-albums.php', false);
add_option('DM_ALBUMS_CORE_DEFAULT_ALBUM_PLUGIN_APP', get_option('siteurl') . '/wp-content/plugins/dm-albums/dm-albums.php', 'Web path to DM Photo Albums Plugin application', false);
add_option('DM_ALBUMS_CORE_DEFAULT_HOME_DIR', $DEFAULT_HOME, 'Root directory for web space', false);

if(dm_is_wpmu())	add_option('DM_ALBUMS_CORE_DEFAULT_UPLOADDIR', WP_CONTENT_DIR . '/blogs.dir/{BLOG_ID}/uploads/dm-albums/', 'Upload root for photo albums', false);
else 				add_option('DM_ALBUMS_CORE_DEFAULT_UPLOADDIR', WP_CONTENT_DIR . '/uploads/dm-albums/', 'Upload root for photo albums', false);

// END DEFAULT CORE OPTIONS

if(get_option('DM_SHOW_TAGLINE') == "")
{
	add_option('DM_SHOW_TAGLINE', 'false', 'Switch off taglines by default', false);
}

if(get_option('DM_JQUERY_LIB') == "")
{
	add_option('DM_JQUERY_LIB', 'GOOGLE', 'JQuery API', false);
}

if(get_option('DM_CAPTION_EDITORS') == "")
{
	add_option('DM_CAPTION_EDITORS', '2', 'Minium User Level to Edit Captions', false);
}

if(get_option('DM_PHOTOALBUM_PHOTO_QUALITY') == "")
{
	add_option('DM_PHOTOALBUM_PHOTO_QUALITY', '85', 'Photo output quality', false);
}

if(get_option('DM_PHOTOALBUM_SLIDESHOW_AUTOPLAY') == "")
{
	add_option('DM_PHOTOALBUM_SLIDESHOW_AUTOPLAY', 'false', 'Autostart Slideshow', false);
}

if(get_option('DM_PHOTOALBUM_HIDE_LOADING_MESSAGE') == "")
{
	add_option('DM_PHOTOALBUM_HIDE_LOADING_MESSAGE', 'false', 'Hide the Photo Loading Message', false);
}

if(get_option('DM_PHOTOALBUM_HIDE_LOADING_MESSAGE_SLIDESHOW') == "")
{
	add_option('DM_PHOTOALBUM_HIDE_LOADING_MESSAGE_SLIDESHOW', 'false', 'Hide the Photo Loading Message (for Slideshows)', false);
}

if(get_option('DM_ALBUMS_UPLOADDIR') == "")
{
	add_option('DM_ALBUMS_UPLOADDIR', get_option('DM_ALBUMS_CORE_DEFAULT_UPLOADDIR'), 'Upload root for photo albums', false);
}

if(get_option('DM_ALBUMS_UUP') == "")
{
	add_option('DM_ALBUMS_UUP', '0', 'Unique Upload Folders', false);
}

if(get_option('DM_ALBUMS_EXTERNAL_CSS') == "")
{
	add_option('DM_ALBUMS_EXTERNAL_CSS', '', 'Custom Styelsheet to override existing', false);
}

if(get_option('DM_ALBUMS_EXTERNAL_LINK_TARGET') == "")
{
	add_option('DM_ALBUMS_EXTERNAL_LINK_TARGET', '_top', 'Link Target', false);
}

if(get_option('DM_TRANSITION_EFFECT') == "")
{
	add_option('DM_TRANSITION_EFFECT', 'fadeslide', '', false);
}

if(get_option('DM_TRANSITION_SPEED') == "")
{
	add_option('DM_TRANSITION_SPEED', '400', '', false);
}

// IF DM PHOTO ALBUMS HAVE NOT BEEN CONFIGURED, LOAD DEFAULT CONFIGURATION
if(get_option('DM_PHOTOALBUM_CONFIGURED') == '')
{
	dm_set_default_config();
}

// Advanced Settings
/*
$DM_PHOTOALBUM_APP = get_option('DM_PHOTOALBUM_APP');
$DM_ALBUM_PLUGIN_APP = get_option('DM_ALBUM_PLUGIN_APP');
$DM_PHOTOALBUM_PREFIX = get_option('DM_PHOTOALBUM_PREFIX');
$DM_PHOTOALBUM_PRESERVE_LINK = get_option('DM_PHOTOALBUM_PRESERVE_LINK');
$DM_PHOTOALBUM_APP_WIDTH = get_option('DM_PHOTOALBUM_APP_WIDTH');
$DM_PHOTOALBUM_APP_HEIGHT = get_option('DM_PHOTOALBUM_APP_HEIGHT');
$DM_ALBUM_ID = 0;
*/

$DM_PHOTOALBUM_APP = get_option('DM_PHOTOALBUM_APP');
$DM_ALBUM_PLUGIN_APP = get_option('siteurl') . '/wp-content/plugins/dm-albums/dm-albums.php';
$DM_PHOTOALBUM_PREFIX = get_option('DM_PHOTOALBUM_PREFIX');
$DM_PHOTOALBUM_APP_WIDTH = (int) get_option('DM_PHOTOALBUM_APP_WIDTH');
$DM_PHOTOALBUM_APP_HEIGHT = (int) get_option('DM_PHOTOALBUM_APP_HEIGHT');
$DM_ALBUM_ID = 0;


$DM_PHOTOALBUM_PRESERVE_LINK = FALSE;
if(get_option('DM_PHOTOALBUM_PRESERVE_LINK') == "true")	$DM_PHOTOALBUM_PRESERVE_LINK = TRUE;

// Declare instances global variable
$dm_instances = array();

// Filter function (inserts album instances according to tag type)
function dm_insert_dm_albums($content = '')
{
	global $dm_instances, $DM_PHOTOALBUM_PREFIX, $DM_PHOTOALBUM_APP;

	// Reset instance array
	$dm_instances = array();

	$dm_photoalbum_regexp = str_replace("/", "\/", $DM_PHOTOALBUM_APP);

	$content = preg_replace_callback( "/$DM_PHOTOALBUM_PREFIX\s*<a ([^=]+=\"[^\"]+\" )*href=\"$dm_photoalbum_regexp([^\"]+)\">[^<]+<\/a>/i", "dm_replaceurl", $content );

	// Replace [album: syntax]
	$content = preg_replace_callback( "/\[album:(\s*)(([^]]+))]/i", "dm_replaceurl", $content);

	return $content;
}

// Callback function for preg_replace_callback
function dm_replaceurl($matches)
{
	global $ap_albumURL, $dm_instances, $DM_PHOTOALBUM_APP, $DM_PHOTOALBUM_PREFIX, $DM_PHOTOALBUM_PRESERVE_LINK;

	$footer = "";

	//if((strpos($matches[0], $DM_PHOTOALBUM_PREFIX) !== FALSE) && $DM_PHOTOALBUM_PRESERVE_LINK === TRUE)	$footer = "\n" . $matches[0] . "";

	// Split options
	$data = preg_split("/[\|]/", $matches[2]);

	$files = array();

	$path = $data[0];

	// If url is not dm app, convert
	if(strpos($path, "http://") === 0 && strpos($path, $DM_PHOTOALBUM_APP) === FALSE)
	{
		$url = str_replace("http://", "", $path);

		$dir = substr($url, strpos($url, "/"));

		if(strrpos($dir, "/") != (strlen($dir) - 1))	$dir = dirname($dir) . "/";

		if(get_option('DM_HOME_DIR') != $_SERVER['DOCUMENT_ROOT'] . "/")
		{
			$home = str_replace("www.", "", $url);

			$home = substr($url, 0, strpos($home, "/"));

			$dir = "/" . $home . $dir;
		}

		$path = $DM_PHOTOALBUM_APP . "?currdir=" . $dir;
	}

	$data[0] = $path;

	foreach( explode( ",", $data[0] ) as $afile )
	{
		$afile = str_replace($DM_PHOTOALBUM_APP, "", $afile);

		array_push( $files, $afile );

		// Add source file to instances already added to the post
		array_push( $dm_instances, $afile );
	}

	$options = array();

	for($i=1; $i < count($data); $i++)
	{
		$pair = explode("=", $data[$i]);

		$options[strtolower($pair[0])] = $pair[1];
	}

	$file = implode( ",", $files );

	return (dm_getalbum($file, $options) . $footer);
}

// Generic player instance function (returns object tag code)
function dm_getalbum($source, $options = array())
{
	global $DM_ALBUM_ID, $DM_PHOTOALBUM_APP, $DM_ALBUM_PLUGIN_APP, $DM_PHOTOALBUM_APP_WIDTH, $DM_PHOTOALBUM_APP_HEIGHT, $DM_ALBUM_FULLSCREEN_ICON;

	$frame_width = $DM_PHOTOALBUM_APP_WIDTH;
	$frame_height = $DM_PHOTOALBUM_APP_HEIGHT;

	$style = "";


	$files = explode(",", $source);

	$url_options = "";

	foreach($options as $key => $value)
	{
		if($key == "width")			$frame_width = $value;
		else if($key == "height")	$frame_height = $value;
		else if($key == "controls")	$controls = $value;
		else $url_options .= '&' . $key . '=' . rawurlencode($value);
	}

	list($dump, $album) = explode("=", $source);

	return get_galleria($album, $frame_width, $frame_height);
}

function get_galleria($album, $width, $height, $forcesize)
{
	$DM_ALBUMS_EXTERNAL_CSS = get_option("DM_ALBUMS_EXTERNAL_CSS");

	if($DM_ALBUM_ID == 0 && !empty($DM_ALBUMS_EXTERNAL_CSS))
	{
		echo '<link rel="stylesheet" type="text/css" href="' . $DM_ALBUMS_EXTERNAL_CSS . '">';
	}

	$DM_ALBUM_ID++;

	$albumid = uniqid();

	$controls = get_option('DM_PHOTOALBUM_SLIDESHOW_CONTROLS');
	$show_powered_by = get_option('DM_SHOW_TAGLINE');
	$show_fullscreen = get_option('DM_SHOW_FULLSCREEN');
	$show_download = get_option('DM_PHOTOALBUM_ALLOWDOWNLOAD');

	$style = '';

	if($forcesize)
	{
		$style = ' style="width: ' . $width . 'px; height: ' . $height .'px;"';
	}

	ob_start();
	?>
	<div id="dm-warn-full-screen"><div id="dm-warn-full-screen-message"></div></div>
	<div class="dm-albums-galleria-container" <?php echo $style; ?>><div style="display: block; width: 100%" id="galleria-<?php echo $albumid; ?>" class="dm-album-galleria"></div>
	<?php if($controls != "false") {?><div class="dm-albums-slideshow"><div>Slideshow:</div> <a class="pause" href="javascript:void(0);" onClick="$('#galleria-<?php echo $albumid; ?>').data('galleria').pause(); dm_show_slideshow_pause();"></a> <a class="play" href="javascript:void(0);" onClick="$('#galleria-<?php echo $albumid; ?>').data('galleria').play(); dm_show_slideshow_play();"></a></div><?php } ?>
	<?php if($show_fullscreen != "false") {?><div class="dm-albums-fullscreen"><div>Fullscreen:</div> <a class="fullscreen" href="javascript:void(0);" onClick="$('#galleria-<?php echo $albumid; ?>').data('galleria').enterFullscreen(dm_warn_fullscreen); g_DM_FULLSCREEN_GALLERY_ID = 'galleria-<?php echo $albumid; ?>'"></a></div><?php } ?>
	<?php if($show_download == "true") {?><div class="dm-albums-download"><div>Download:</div> <a class="fullscreen" href="javascript:void(0);" onClick="g_DM_FULLSCREEN_GALLERY_ID = 'galleria-<?php echo $albumid; ?>'; dm_download_file();"></a></div><?php } ?>
	<?php if($show_powered_by != "false" && !dm_is_mobile()) {?><div class="dm-albums-powered-by"><a href="http://www.dutchmonkey.com/wp-plugins/">Powered by DM Albums&#153;</a></div><?php } ?>
	</div><div class="clear"></div>
	<script type="text/javascript" class="dm-album-galleria-script" src="<?php echo plugins_url(); ?>/dm-albums/javascript/galleria.php?albumid=<?php echo $albumid; ?>&album=<?php echo $album; ?>&width=<?php echo ($width + 10); ?>&height=<?php echo ($height + 10); ?>"></script>
	<?
	$thecontent = ob_get_contents();

  	ob_end_clean();

  	return $thecontent;
}

function dm_enqueu_scripts()
{
	if(get_option('DM_JQUERY_LIB') == "GOOGLE")
	{
		wp_register_script( 'dm_jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js');
		wp_enqueue_script( 'dm_jquery' );

		wp_register_script( 'dm_galleria', plugins_url() . '/dm-albums/galleria/galleria-1.2.6.min.js', 'dm_jquery');
    	wp_enqueue_script( 'dm_galleria' );
	}

	else if(get_option('DM_JQUERY_LIB') == "REPLACE")
	{
		wp_deregister_script( 'jquery' );
    	wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js');
    	wp_enqueue_script( 'jquery' );

    	wp_register_script( 'dm_galleria', plugins_url() . '/dm-albums/galleria/galleria-1.2.6.min.js', 'jquery');
    	wp_enqueue_script( 'dm_galleria' );
	}

	else
	{
		wp_register_script( 'dm_galleria', plugins_url() . '/dm-albums/galleria/galleria-1.2.6.min.js');
    	wp_enqueue_script( 'dm_galleria' );
	}

	wp_register_script( 'dm_galleria_common', plugins_url() . '/dm-albums/javascript/galleria-common.js');
    wp_enqueue_script( 'dm_galleria_common' );
    wp_register_script( 'dm_galleria_theme', plugins_url() . '/dm-albums/galleria/themes/classic/galleria.classic.min.js');
    wp_enqueue_script( 'dm_galleria_theme' );
}

add_action('wp_enqueue_scripts', 'dm_enqueu_scripts');

function dm_albums_embed_shortcode($attr, $content = null)
{
	extract(shortcode_atts(array(
		'path' => '',
		'width' => 0,
		'height' => 0
	), $attr));

	$style = true;

	if($width == 0)
	{
		$style = false;
		$width = (int) get_option('DM_PHOTOALBUM_APP_WIDTH');
	}

	if($height == 0)
	{
		$style = false;
		$height = (int) get_option('DM_PHOTOALBUM_APP_HEIGHT');
	}

	$thecontent = "";

	if(empty($path))	$thecontent = '<div class="dm-albums-fatal-error">Error: DM Albums is missing the required parameter, \'path\'.</div>';
	else				$thecontent = get_galleria($path, $width, $height, $style);

	return $thecontent;
}

add_shortcode('dmalbum', 'dm_albums_embed_shortcode');

// Add filter hook
if(get_option('DM_PHOTOALBUM_CONFIGURED_CORRECTLY') == 'YES')
{
	add_filter('the_content', 'dm_insert_dm_albums');
	if(get_option('DM_PHOTOALBUM_INCLUDE_COMMENTS') != 'NO')	add_action('comment_text', 'dm_insert_dm_albums', 1);
}

// Helper function for displaying a system message
function dm_showMessage( $message )
{
	echo '<div id="message" class="updated fade"><p><strong>' . $message . '</strong></p></div>';
}

// Helper function for displaying a system message
function dm_showErrorMessage( $message )
{
	echo '<div id="message" class="error fade"><p><strong>' . $message . '</strong></p></div>';
}

function dm_set_default_config()
{
	delete_option('DM_PHOTOALBUM_CONFIGURED_CORRECTLY');
	delete_option('DM_PHOTOALBUM_APP');
	delete_option('DM_ALBUM_PLUGIN_APP');
	delete_option('DM_JQUERY_LIB');

	delete_option('DM_SHOW_FULLSCREEN');
	delete_option('DM_SHOW_TAGLINE');
	delete_option('DM_PHOTOALBUM_HIDE_LOADING_MESSAGE');
	delete_option('DM_PHOTOALBUM_HIDE_LOADING_MESSAGE_SLIDESHOW');
	delete_option('DM_PHOTOALBUM_SLIDESHOW_CONTROLS');
	delete_option('DM_PHOTOALBUM_SLIDESHOW_AUTOPLAY');
	delete_option('DM_PHOTOALBUM_ALLOWDOWNLOAD');
	delete_option('DM_PHOTOALBUM_MERGESUBDIRECTORIES');
	delete_option('DM_PHOTOALBUM_PREFIX');
	delete_option('DM_PHOTOALBUM_PRESERVE_LINK');
	delete_option('DM_PHOTOALBUM_APP_WIDTH');
	delete_option('DM_PHOTOALBUM_APP_HEIGHT');
	delete_option('DM_HOME_DIR');
	delete_option('DM_THUMBNAIL_LOCATION');
	delete_option('DM_THUMBNAIL_SIZE');
	delete_option('DM_THUMBNAIL_PADDING');
	delete_option('DM_DISPLAY_CAPTIONS');
	delete_option('DM_DISPLAY_PHOTOCOUNT');
	delete_option('DM_CAPTION_HEIGHT');
	delete_option('DM_PHOTO_PADDING');
	delete_option('DM_TITLE_PREFIX');
	delete_option('DM_LEFTMARGIN');
	delete_option('DM_RIGHTMARGIN');
	delete_option('DM_TOPMARGIN');
	delete_option('DM_BOTTOMMARGIN');
	delete_option('DM_CAPTION_EDITORS');
	delete_option('DM_ALBUMS_UPLOADDIR');
	delete_option('DM_ALBUMS_UUP');
	delete_option('DM_ALBUMS_EXTERNAL_CSS');
	delete_option('DM_ALBUMS_EXTERNAL_LINK_TARGET');
	delete_option('DM_PHOTOALBUM_INCLUDE_COMMENTS');
	delete_option('DM_ALBUMS_LIGHTBOX');

	add_option('DM_JQUERY_LIB', 'GOOGLE', 'JQuery API', false);
	add_option('DM_SHOW_TAGLINE', 'false', 'Switch off taglines by default', false);
	add_option('DM_PHOTOALBUM_HIDE_LOADING_MESSAGE', 'false', 'Hide the Photo Loading Message', false);
	add_option('DM_PHOTOALBUM_HIDE_LOADING_MESSAGE_SLIDESHOW', 'false', 'Hide the Photo Loading Message (for Slideshows)', false);
	add_option('DM_PHOTOALBUM_SLIDESHOW_CONTROLS', 'true', 'Display Slideshow controls', false);
	add_option('DM_PHOTOALBUM_SLIDESHOW_AUTOPLAY', 'false', 'Autostart Slideshow', false);
	add_option('DM_PHOTOALBUM_ALLOWDOWNLOAD', 'false', 'Allow direct photo download', false);
	add_option('DM_PHOTOALBUM_MERGESUBDIRECTORIES', 'false', 'Automatically Merge Subdirectories into Photo Album', false);
	add_option('DM_PHOTOALBUM_PREFIX', 'Album:', 'DM Photo Albums embed link prefix', false);
	add_option('DM_PHOTOALBUM_PRESERVE_LINK', 'false', 'Preserve original DM Photo Albums embed link', false);
	add_option('DM_PHOTOALBUM_APP_WIDTH', '500', 'Width of embedded Photo Album', false);
	add_option('DM_PHOTOALBUM_APP_HEIGHT', '492', 'Height of embedded Photo Album', false);
	add_option('DM_THUMBNAIL_LOCATION', 'TOP', 'Display the thumbnail bar along the top or bottom of photo album', false);
	add_option('DM_THUMBNAIL_SIZE', '60', 'Height of thumbnail bar', false);
	add_option('DM_THUMBNAIL_PADDING', '5', 'padding around each thumbnail', false);
	add_option('DM_DISPLAY_CAPTIONS', '1', 'Display photo captions', false);
	add_option('DM_DISPLAY_PHOTOCOUNT', '1', 'Display photo count (Photo x of y)', false);
	add_option('DM_CAPTION_HEIGHT', '32', 'Height of bar below photo, used to display caption and photo count', false);
	add_option('DM_PHOTO_PADDING', '0', 'padding around the main photo', false);
	add_option('DM_TITLE_PREFIX', '', 'Prefix for Photo Album displayed in title bar when viewing album in Full Screen.', false);
	add_option('DM_LEFTMARGIN', '0', 'Size of left margin', false);
	add_option('DM_RIGHTMARGIN', '0', 'Size of right margin', false);
	add_option('DM_TOPMARGIN', '0', 'Size of top margin', false);
	add_option('DM_BOTTOMMARGIN', '0', 'Size of bottom margin', false);
	add_option('DM_CAPTION_EDITORS', '2', 'Minium User Level to Edit Captions', false);
	add_option('DM_PHOTOALBUM_PHOTO_QUALITY', '85', 'Photo output quality', false);
	add_option('DM_ALBUMS_UUP', '0', 'Unique Upload Folders', false);
	add_option('DM_ALBUMS_EXTERNAL_CSS', '', 'Custom Styelsheet to override existing', false);
	add_option('DM_ALBUMS_EXTERNAL_LINK_TARGET', '_top', 'Custom Styelsheet to override existing', false);
	add_option('DM_PHOTOALBUM_INCLUDE_COMMENTS', 'YES', 'Include comments for parsing', false);

	add_option('DM_ALBUMS_UPLOADDIR', get_option('DM_ALBUMS_CORE_DEFAULT_UPLOADDIR'), 'Upload root for photo albums', false);
	add_option('DM_PHOTOALBUM_APP', get_option('DM_ALBUMS_CORE_DEFAULT_PHOTOALBUM_APP'), false);
	add_option('DM_ALBUM_PLUGIN_APP', get_option('DM_ALBUMS_CORE_DEFAULT_ALBUM_PLUGIN_APP'), 'Web path to DM Photo Albums Plugin application', false);
	add_option('DM_HOME_DIR', get_option('DM_ALBUMS_CORE_DEFAULT_HOME_DIR'), 'Root directory for web space', false);

	add_option('DM_ALBUMS_LIGHTBOX', 'true', 'Endable lightbox', false);
	add_option('DM_TRANSITION_EFFECT', 'SLIDE', '', false);
	add_option('DM_TRANSITION_SPEED', '400', '', false);
}

// Option panel functionality
function dm_options_subpanel()
{
	global $file_prefix;

	if($_POST['reset_config'] && dm_isUserAdmin())
	{
		dm_set_default_config();
		dm_showMessage( "Default settings have been restored.");
	}

	if( $_POST['Submit'] )
	{
		if(dm_isUserAdmin())
		{
			if(get_option('DM_PHOTOALBUM_CONFIGURED') == '')	add_option('DM_PHOTOALBUM_CONFIGURED', 'YES', "Setting indicating if the Photo Album has been configured.", true);

			$slash = "/";

			if(dm_is_wamp())	$slash = "\\";

			if(strrpos($_POST['DM_HOME_DIR'], $slash) == (strlen($_POST['DM_HOME_DIR']) - 1))
			{
				$home = $_POST['DM_HOME_DIR'];
			}

			else
			{
				$home = $_POST['DM_HOME_DIR'] . $slash;
			}

			if(strrpos($_POST['DM_ALBUMS_UPLOADDIR'], "/") == (strlen($_POST['DM_ALBUMS_UPLOADDIR']) - 1))
			{
				$DM_ALBUMS_UPLOADDIR = $_POST['DM_ALBUMS_UPLOADDIR'];
			}

			else
			{
				$DM_ALBUMS_UPLOADDIR = $_POST['DM_ALBUMS_UPLOADDIR'] . "/";
			}

			if(strpos($DM_ALBUMS_UPLOADDIR, $home) !== 0)
			{
				dm_showErrorMessage( "Warning: The Album Upload Folder and Home Folder need to share a path (i.e. Upload Directory needs to start with Home Directory).  Home Directory and/or Album Upload Folder names were not updated.");
			}
			else
			{
				if(isset($_POST['DM_HOME_DIR']) && !empty($_POST['DM_HOME_DIR']))	update_option('DM_HOME_DIR', $home);
				if(isset($_POST['DM_ALBUMS_UPLOADDIR']) && !empty($_POST['DM_ALBUMS_UPLOADDIR']))	update_option('DM_ALBUMS_UPLOADDIR', $DM_ALBUMS_UPLOADDIR);
			}

			// Update Options
			if(isset($_POST['DM_FULLSCREEN_APP']) && !empty($_POST['DM_FULLSCREEN_APP']))	update_option('DM_PHOTOALBUM_APP', $_POST['DM_FULLSCREEN_APP']);
			if(isset($_POST['DM_ALBUM_PLUGIN_APP']) && !empty($_POST['DM_ALBUM_PLUGIN_APP']))	update_option('DM_ALBUM_PLUGIN_APP', $_POST['DM_ALBUM_PLUGIN_APP']);
			if(isset($_POST['DM_ALBUMS_UUP']) && !empty($_POST['DM_ALBUMS_UUP']))	update_option('DM_ALBUMS_UUP', $_POST['DM_ALBUMS_UUP']);

		}

		update_option('DM_JQUERY_LIB', $_POST['DM_JQUERY_LIB']);
		update_option('DM_PHOTOALBUM_HIDE_LOADING_MESSAGE', $_POST['DM_PHOTOALBUM_HIDE_LOADING_MESSAGE']);
		update_option('DM_PHOTOALBUM_HIDE_LOADING_MESSAGE_SLIDESHOW', $_POST['DM_PHOTOALBUM_HIDE_LOADING_MESSAGE_SLIDESHOW']);
		update_option('DM_PHOTOALBUM_SLIDESHOW_CONTROLS', $_POST['DM_PHOTOALBUM_SLIDESHOW_CONTROLS']);
		update_option('DM_PHOTOALBUM_SLIDESHOW_AUTOPLAY', $_POST['DM_PHOTOALBUM_SLIDESHOW_AUTOPLAY']);
		update_option('DM_PHOTOALBUM_ALLOWDOWNLOAD', $_POST['DM_PHOTOALBUM_ALLOWDOWNLOAD']);
		update_option('DM_PHOTOALBUM_MERGESUBDIRECTORIES', $_POST['DM_PHOTOALBUM_MERGESUBDIRECTORIES']);
		update_option('DM_PHOTOALBUM_PREFIX', trim($_POST['DM_PHOTOALBUM_PREFIX']));
		update_option('DM_PHOTOALBUM_PRESERVE_LINK', $_POST['DM_PHOTOALBUM_PRESERVE_LINK']);
		update_option('DM_PHOTOALBUM_APP_WIDTH', $_POST['DM_PHOTOALBUM_APP_WIDTH']);
		update_option('DM_PHOTOALBUM_APP_HEIGHT', $_POST['DM_PHOTOALBUM_APP_HEIGHT']);
		update_option('DM_THUMBNAIL_LOCATION', $_POST['DM_THUMBNAIL_LOCATION']);
		update_option('DM_THUMBNAIL_SIZE', $_POST['DM_THUMBNAIL_SIZE']);
		update_option('DM_THUMBNAIL_PADDING', $_POST['DM_THUMBNAIL_PADDING']);
		update_option('DM_DISPLAY_CAPTIONS', $_POST['DM_DISPLAY_CAPTIONS']);
		update_option('DM_DISPLAY_PHOTOCOUNT', $_POST['DM_DISPLAY_PHOTOCOUNT']);
		update_option('DM_CAPTION_HEIGHT', $_POST['DM_CAPTION_HEIGHT']);
		update_option('DM_PHOTO_PADDING', $_POST['DM_PHOTO_PADDING']);
		update_option('DM_TITLE_PREFIX', $_POST['DM_TITLE_PREFIX']);
		update_option('DM_LEFTMARGIN', $_POST['DM_LEFTMARGIN']);
		update_option('DM_RIGHTMARGIN', $_POST['DM_RIGHTMARGIN']);
		update_option('DM_TOPMARGIN', $_POST['DM_TOPMARGIN']);
		update_option('DM_BOTTOMMARGIN', $_POST['DM_BOTTOMMARGIN']);
		update_option('DM_CAPTION_EDITORS', $_POST['DM_CAPTION_EDITORS']);
		update_option('DM_PHOTOALBUM_PHOTO_QUALITY', $_POST['DM_PHOTOALBUM_PHOTO_QUALITY']);
		update_option('DM_ALBUMS_EXTERNAL_CSS', $_POST['DM_ALBUMS_EXTERNAL_CSS']);
		update_option('DM_ALBUMS_EXTERNAL_LINK_TARGET', $_POST['DM_ALBUMS_EXTERNAL_LINK_TARGET']);
		update_option('DM_SHOW_TAGLINE', $_POST['DM_SHOW_TAGLINE']);
		update_option('DM_SHOW_FULLSCREEN', $_POST['DM_SHOW_FULLSCREEN']);
		update_option('DM_SHOW_NAVIGATION_HINTS', $_POST['DM_SHOW_NAVIGATION_HINTS']);
		update_option('DM_PHOTOALBUM_INCLUDE_COMMENTS', $_POST['DM_PHOTOALBUM_INCLUDE_COMMENTS']);
		update_option('DM_ALBUMS_LIGHTBOX', $_POST['DM_ALBUMS_LIGHTBOX']);
		update_option('DM_TRANSITION_EFFECT', $_POST['DM_TRANSITION_EFFECT']);
		update_option('DM_TRANSITION_SPEED', $_POST['DM_TRANSITION_SPEED']);

		//dm_display_config();

		if(function_exists('wp_cache_clean_cache')) 	wp_cache_clean_cache($file_prefix);

		// Print confirmation message
		dm_showMessage( "Options updated.");
	}

	// Include options panel
	include("wp-dm-adminoptions.php");
}

function dm_display_config()
{
		echo 'DM_PHOTOALBUM_ALLOWDOWNLOAD: ' . get_option('DM_PHOTOALBUM_ALLOWDOWNLOAD') . '<br>';
		echo 'DM_PHOTOALBUM_MERGESUBDIRECTORIES: ' . get_option('DM_PHOTOALBUM_MERGESUBDIRECTORIES') . '<br>';
		echo 'DM_PHOTOALBUM_APP: ' . get_option('DM_PHOTOALBUM_APP') . '<br>';
		echo 'DM_ALBUM_PLUGIN_APP: ' . get_option('DM_ALBUM_PLUGIN_APP') . '<br>';
		echo 'DM_PHOTOALBUM_PREFIX: ' . get_option('DM_PHOTOALBUM_PREFIX') . '<br>';
		echo 'DM_PHOTOALBUM_PRESERVE_LINK: ' . get_option('DM_PHOTOALBUM_PRESERVE_LINK') . '<br>';
		echo 'DM_PHOTOALBUM_APP_WIDTH: ' . get_option('DM_PHOTOALBUM_APP_WIDTH') . '<br>';
		echo 'DM_PHOTOALBUM_APP_HEIGHT: ' . get_option('DM_PHOTOALBUM_APP_HEIGHT') . '<br>';
		echo 'DM_HOME_DIR: ' . get_option('DM_HOME_DIR') . '<br>';
		echo 'DM_THUMBNAIL_LOCATION: ' . get_option('DM_THUMBNAIL_LOCATION') . '<br>';
		echo 'DM_THUMBNAIL_SIZE: ' . get_option('DM_THUMBNAIL_SIZE') . '<br>';
		echo 'DM_THUMBNAIL_PADDING: ' . get_option('DM_THUMBNAIL_PADDING') . '<br>';
		echo 'DM_DISPLAY_CAPTIONS: ' . get_option('DM_DISPLAY_CAPTIONS') . '<br>';
		echo 'DM_DISPLAY_PHOTOCOUNT: ' . get_option('DM_DISPLAY_PHOTOCOUNT') . '<br>';
		echo 'DM_CAPTION_HEIGHT: ' . get_option('DM_CAPTION_HEIGHT') . '<br>';
		echo 'DM_PHOTO_PADDING: ' . get_option('DM_PHOTO_PADDING') . '<br>';
		echo 'DM_TITLE_PREFIX: ' . get_option('DM_TITLE_PREFIX') . '<br>';
		echo 'DM_LEFTMARGIN: ' . get_option('DM_LEFTMARGIN') . '<br>';
		echo 'DM_RIGHTMARGIN: ' . get_option('DM_RIGHTMARGIN') . '<br>';
		echo 'DM_TOPMARGIN: ' . get_option('DM_TOPMARGIN') . '<br>';
		echo 'DM_BOTTOMMARGIN: ' . get_option('DM_BOTTOMMARGIN') . '<br>';
		echo 'DM_CAPTION_EDITORS: ' . get_option('DM_CAPTION_EDITORS') . '<br>';
		echo 'DM_PHOTOALBUM_PHOTO_QUALITY: ' . get_option('DM_PHOTOALBUM_PHOTO_QUALITY') . '<br>';
		echo 'DM_PHOTOALBUM_SLIDESHOW_CONTROLS: ' . get_option('DM_PHOTOALBUM_SLIDESHOW_CONTROLS') . '<br>';
		echo 'DM_PHOTOALBUM_SLIDESHOW_AUTOPLAY: ' . get_option('DM_PHOTOALBUM_SLIDESHOW_AUTOPLAY') . '<br>';
		echo 'DM_PHOTOALBUM_HIDE_LOADING_MESSAGE: ' . get_option('DM_PHOTOALBUM_HIDE_LOADING_MESSAGE') . '<br>';
		echo 'DM_PHOTOALBUM_HIDE_LOADING_MESSAGE_SLIDESHOW: ' . get_option('DM_PHOTOALBUM_HIDE_LOADING_MESSAGE_SLIDESHOW') . '<br>';
		echo 'DM_ALBUMS_UPLOADDIR: ' . get_option('DM_ALBUMS_UPLOADDIR') . '<br>';
		echo 'DM_ALBUMS_UUP: ' . get_option('DM_ALBUMS_UUP') . '<br>';
		echo 'DM_ALBUMS_LIGHTBOX: ' . get_option('DM_ALBUMS_LIGHTBOX') . '<br>';
}

// Check all core defaults (HOME_DIR, UPDLOAD DIR, APP) and set if missing
$dm_reset_defaults = false;

if(get_option('DM_ALBUMS_UPLOADDIR') == "" || get_option('DM_ALBUMS_UPLOADDIR') == "/")
{
	add_option('DM_ALBUMS_UPLOADDIR', get_option('DM_ALBUMS_CORE_DEFAULT_UPLOADDIR'), 'Upload root for photo albums', false);
	$dm_reset_defaults = true;
}

if(get_option('DM_PHOTOALBUM_APP') == "")
{
	add_option('DM_PHOTOALBUM_APP', get_option('DM_ALBUMS_CORE_DEFAULT_PHOTOALBUM_APP'), false);
	$dm_reset_defaults = true;
}

if(get_option('DM_ALBUM_PLUGIN_APP') == "")
{
	add_option('DM_ALBUM_PLUGIN_APP', get_option('DM_ALBUMS_CORE_DEFAULT_ALBUM_PLUGIN_APP'), 'Web path to DM Photo Albums Plugin application', false);
	$dm_reset_defaults = true;
}

if(get_option('DM_HOME_DIR') == "" || get_option('DM_HOME_DIR') == "/")
{
	add_option('DM_HOME_DIR', get_option('DM_ALBUMS_CORE_DEFAULT_HOME_DIR'), 'Root directory for web space', false);
	$dm_reset_defaults = true;
}

// If resest core defaults, Print warning message
if($dm_reset_defaults && $_POST['Submit'])	dm_showErrorMessage( "Warning: problems were found in some settings and they were reset to their defaults.");

// Add options page to admin menu
function dm_post_add_options()
{
	add_options_page('DM Albums Options', 'DM Albums', 8, basename(__FILE__), 'dm_options_subpanel');
}

function dm_manage_dmalbums_metabox()
{
	if(function_exists('add_meta_box') && current_user_can('upload_files'))
	{
		add_meta_box( 'dmalbums_manager_id', __( 'DM Albums&#153; Album Manager', 'dm_albums_textdomain' ),
                'dm_manage_albums_box', 'post', 'side', 'high');
   	 	add_meta_box( 'dmalbums_manager_id', __( 'DM Albums&#153; Album Manager', 'dm_albums_textdomain' ),
                'dm_manage_albums_box', 'page', 'side', 'high');
	}
}

function dm_manage_albums_box()
{
	// Use nonce for verification
	echo '<input type="hidden" name="dm_manage_albums_box_noncename" id="dm_manage_albums_box_noncename" value="' .
    	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

    include_once('wp-dm-albums-admin-header.php');
    include_once('wp-dm-albums-manager.php');
    include_once('wp-dm-albums-detail-manager.php');
    include_once('wp-dm-albums-sort-manager.php');
}

add_action('admin_menu', 'dm_post_add_options');
add_action('admin_menu', 'dm_manage_dmalbums_metabox');


?>