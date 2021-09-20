﻿=== Maps for BuddyBeacon ===
Contributors: wpkaren
Donate Link: https://karenattfield.com/giving/
Tags: google maps, viewranger, buddybeacon, live maps, map routes, live tracking, shortcode
Requires at least: 4.4
Tested up to: 5.8.1
Requires PHP: 5.6.25
Stable tag: 1.1.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Serving map tracks in real time via ViewRanger BuddyBeacon.

== Description ==

Maps for BuddyBeacon is a plugin that allows you to display map tracks over a set time period or live via ViewRanger's BuddyBeacon capability. The markers are shown on a Google Map, with an optional info box displaying map title, date range and distance covered. Info Windows provide information for each beacon (date, altitude, longitude and latitude).

Customization options range from map type (eg. roadmap, satellite, terrain, hybrid), to beacon and route colours, beacon shape, map alignment and size, and beacon time adjustment. 

To display a map in a page or post just add the shortcode: [bb_maps id="{id}"] where {id} is the id of the map you want to display, for example [bb_maps id="3"]. For more information see the 'FAQ' section.



### Features

* Display map tracks live or over a specified date range on a Google map.
* Customize your map route display - beacon shape and colour, route colour.
* Customize your map display - satellite, hybrid, terrain or roadmap. 
* Display map route information - total distance covered (in km or miles), date range, map title.
* Display individual beacon information via Google Maps InfoWindows (showing latitute, longitude, altitude and date).
* Delete beacons from routes.
* Adjusting beacon times based on your timezone.
* Manage all your maps from the manage maps settings page.
* Use a shortcode to display your chosen map in any page or post.

== Installation ==


1. Upload `maps-buddybeacon` to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Go to the Maps for BuddyBeacon main menu page, where you will be guided through how to create your first map. in the admin to set whether you'd like each portfolio item to be available on a page of it's own by default.


== Frequently Asked Questions ==

= What are the shortcodes? =

[bb_maps id="{id}"] will output the map you want to display (for example [bb_maps id="3"]).

= Do I need a Google Maps API Key as well as ViewRanger API key and BuddyBeacon username and pin to make this work =

Yes, in order to retrieve your beacon information including dates, locations and altitude from ViewRanger, and to show this on a Google Map, you need to provide this information. Links are provided to help you go through this process.

= Can I put more than one map on each page/post? =

Yes, you can add as many shortcodes per page/ post as you want, displaying as many maps as you want.



= Can you add... =

Feel free to get in touch with any ideas you have, or even better contribute to this plugin. You can get in touch with me at mail AT karenattfield DOT com. Note - since the initial development of this plugin I have very little time for upkeep / changes but am still keen to hear from you.

== Screenshots ==

1. Sample map output with info box
2. 'Add map' settings page
3. Saved 'add map' page showing map shortcode at top
4. 'Manage maps' settings page
5. Settings page for Google Maps and ViewRanger BuddyBeacon

== Changelog ==

= 1.1.2 =
* Refactoring front end JavaScript for better performance

= 1.1.1 = 
* Implementing a fix for a PHP warning in PHP 8.0

= 1.1.0 = 
* Added ability to make adjustments to beacon times based on different timezones
* BuddyBeacon username field (on settings page) now validates whether an email address has been entered or not
* Ensuring all echoed strings are internationalised
* Resolved 'The response is not a valid JSON response' error message when saving a shortcode using a shortcode block
* Updating JavaScript variable declarations
* Fixing the display issue with the datetimepicker to make sure the calendars open next to their respective input fields
* Added a troubleshooting section to the 'How to use' page

= 1.0.0 =
* First stable release
* Editing map route: adding beacon to start of route
* Adding capability to delete beacons from individual maps

= 0.1.0 =
* Initial release

== Upgrade Notice ==

= 1.1.2 = 
* Refactoring front end JavaScript for better performance

= 1.1.1 = 
* Implementing a fix for a PHP warning in PHP 8.0

= 1.1.0 =
* Adds ability to make adjustments to beacon times based on different timezones, plus a few settings display fixes and more helpful guidance notes.

= 1.0.0 =
* First stable release. Adding beacon to route start, and adding capability to delete beacons.

= 0.1.0 =
* Initial release, no upgrade notice

