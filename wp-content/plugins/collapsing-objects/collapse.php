<?php
/*
Plugin Name:			collapsing-objects
Plugin URI:			http://moodlechat.bounceme.net/~lebowski/blog/
Description:			Collapsible content blocks
Author:				DrLebowski
Version:			0.2
*/

function addJsToHead()
{
	if(function_exists('wp_enqueue_script'))
		wp_enqueue_script('haz_collapse_js', get_bloginfo('url') . '/wp-content/plugins/collapsing-objects/collapse.js');
}

function subTag($tag)
{
	$ran = rand(1, 10000);
	global $r;
		
	while(in_array($ran, $r))
		$ran = rand(1, 10000);
	
	$r[] = $ran;
	$link = "<a href=\"javascript:collapseExpand('$ran')\">$tag[1]</a>";
	$eDiv = "<div id=\"$ran\" style=\"display:none;\"> $tag[2] </div>";
	return $link . $eDiv;
}

function cfilter($body)
{
	return preg_replace_callback("/\[expand title=([^\[]*)\]([^\[]*)\[\/expand\]/", "subTag", $body);
}

$r = array();
add_filter('the_content','cfilter');
add_action('template_redirect', 'addJsToHead');
?>
