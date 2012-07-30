<?php
/*
Plugin Name: Brainbits Flickr Gallery 
Plugin URI: http://brainbits.ca/brainbits-flickr-gallery/
Description: Displays a gallery of your flickr photosets.
Version: 1.1
Author: Colin Ligertwood
Author URI: http://brainbits.ca/
License: GPL2
*/

/*  Copyright 2010 Colin Ligertwood  (email : colin@brainbits.ca)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define(PLUGIN_PATH, '/brainbits-flickr-gallery');

function BFGShowGallery($flickr_id){
	$output = '
	<p>
		<ul class="brainbits-flickrgallery" userid="'.$flickr_id.'">
			<li class="brainbits-flickrgallery-display">
				<img>
			</li>
			<li class="brainbits-flickrgallery-description">
				<p></p>
			</li>
			<li class="brainbits-flickrgallery-photos">
				<h3></h3>
				<ul></ul>
			</li>
			<li class="brainbits-flickrgallery-photosets">
				<h3>Photosets</h3>
				<ul>
					<li><img class="brainbits-flickrgallery-publicphotos" alt="Photo Stream" title="Photo Stream" /></li>
				</ul>
			</li>
			</ul>
		</ul>
	</p>
	';
	return $output;
}

function BFGShortCode($atts){
        extract(shortcode_atts(array(
                'flickr_id' => '',
        ), $atts));

        if (strlen($flickr_id)){
                return BFGShowGallery($flickr_id);
        }
}


function BFGStyleSheet() {
	echo '<link type="text/css" rel="stylesheet" href="' . WP_PLUGIN_URL . PLUGIN_PATH .'/brainbits-flickr-gallery.css" />' . "\n";
}

add_action('wp_head', 'BFGStyleSheet');

wp_enqueue_script('jquery');
wp_enqueue_script('brainbits-flickr-gallery', WP_PLUGIN_URL . PLUGIN_PATH . '/brainbits-flickr-gallery.js');

add_shortcode('bfg', 'BFGShortCode');

?>
