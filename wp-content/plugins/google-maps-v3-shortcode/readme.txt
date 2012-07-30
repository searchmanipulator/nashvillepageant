=== Google Maps v3 Shortcode ===
Contributors: yohman
Donate link: http://gis.yohman.com/gmaps-plugin/
Tags: google, google maps, google maps api, kml, network links, fusion, fusion tables, fusion layers, shortcode, shortcodes, google maps v3, v3, geocode, geocoding, address, infowindow, infowindows, map, mapping, maps, latitude, longitude, api, traffic, bike, marker, markers
Requires at least: 2.8
Tested up to: 3.21
Stable tag: 1.2.1
Last udated:  8/11/2011

This plugin allows you to add one or more maps (via the Google Maps v3 API) to your page/post using shortcodes. 


== Description ==

This plugin allows you to add a google map into your post/page using shortcodes. 

Features:

* default world map
* show/hide map controls
* set map size
* set zoom level
* set map type
* multiple maps on the same post
* set location by latitude/longitude
* set location by address
* add marker
* Info windows
* show/hide infowindow by default
* add custom image as map icon
* add KML via URL link
* option to disable autozoom to KML bounds
* add a Fusion Table Layer
* show traffic
* show bike lanes
* disable scroll wheel zoom
* add scale bar

See a full description here:

http://gis.yohman.com/gmaps-plugin/

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `google-maps-v3-shortcode` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Add shortcodes in your posts (ex: [map address="New York, USA"])

== Frequently Asked Questions ==
= Is there documentation for this plugin? =

Yes! See a full description of available shortcodes here:

http://gis.yohman.com/gmaps-plugin/

= How do I add a map to my post =

Using shortcodes in the edit box for your post.  The address parameter for the address, and the "z" parameter for zoom level (ex: 0=world, 20=really zoomed in)

Ex: [map address="New York, USA" z="15"]


= Can I add multiple maps to the same post? =

Yes!  But make sure you use the "id" parameter to create unique id's for each map.

Ex: 
[map id="map1" address="New York, USA"]
[map id="map2" address="Los Angeles, USA"]

= Can I change the size of the map? =
Yes!  Just add your own width and height parameters (the default is 400x300).

Ex:
[map w="200" h="100"]

= Can you add info bubbles? =
Yes!  Add the "infowindow" parameter

Ex:
[map address="New York" marker="yes" infowindow="Hello New York!"]

= Can you add KML's? =
Yes!  Just provide the url link to the KML file.  The map will auto center and zoom to the extent of your KML.

Ex:
[map kml="http://gmaps-samples.googlecode.com/svn/trunk/ggeoxml/cta.kml"]

= Can you add Fusion Table Layers? =
Yes!  Just provide the Fusion Layer ID as "fusion" parameter.  

Ex:
[map address="90095" z=9 fusion="825831"]


== Screenshots ==

See full working examples here:

http://gis.yohman.com/gmaps-plugin/

== Changelog ==

= 1.2.1 =
* fixed bug that was not allowing Google My Map KML to display
* added option to disable scroll wheel zoom
* added option to display scale bar

= 1.2 =
* added support for fusion table layers
* added option to disable autozoom to KML bounds
* added bike layer support
* added ability to show info window by default
* added ability to hide map controls

= 1.1 =
* Added info window support
* Got rid of red border around maps
* Fixed bug that did not geocode maps in IE

= 1.0 =
* First release

== Upgrade Notice ==

= 1.2.1 =
* fixed bug that was not allowing Google My Map KML to display
* added option to disable scroll wheel zoom
* added option to display scale bar

= 1.0 =
* First release
