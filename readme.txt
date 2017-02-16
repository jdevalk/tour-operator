﻿=== Tour Operators ===
Contributors: feedmymedia
Donate link: https://www.lsdev.biz/
Tags: tour operator, tour operators, tour, tours, tour itinerary, tour itineraries, accommodation, accommodation listings, destinations, regions, tourism, lsx
Requires at least: 4.3
Tested up to: 4.7
Stable tag: 4.7
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

The Tour Operators plugin brings live availability, bookings, digital itineraries, and other post types tour operators need to succeed.

== Description ==

LightSpeed’s LSX Tour Operator plugin is here to help you take your Tour Operator business online. 

The plugin features digital itineraries which include interactive galleries, day-by-day information, integrated maps that show a tour’s progression, information per destination, accommodation, activities and other features that will bring your tour offerings to life online. 

Its destination management features make featuring destinations super simple, and attractive to boot. We’ve also built a “Travel Style” feature so you can categorise tours by the type of experience they offer.

Detailed accommodation listings show off room facilities, image galleries, video material, reviews, interactive digital tours, special offers, team members and more.

= Works with the free LSX Theme =
We've also made a fantastic [free theme](https://www.lsdev.biz/product/lsx-wordpress-theme/) that work well with the Tour Operator plugin.

= It's free, and always will be. =
We’re firm believers in open source - that’s why we’re releasing the Tour Operators plugin for free, forever. We offer free support on the [Tour Operator support forums](https://wordpress.org/support/plugin/tour-operator). 

= Actively Developed =
The Tour Operator plugin is actively developed with new features and exciting enhancements all the time. Keep track on the [Tour Operator GitHub repository](https://github.com/lightspeeddevelopment/tour-operator).
Report any bugs via github issues.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/tour-operator` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->Tour Operator screen to configure the plugin

== Frequently Asked Questions ==

= Where can I find Tour Operator plugin documentation and user guides? =
For help setting up and configuring the Tour Operator plugin please refer to our [user guide](https://www.lsdev.biz/documentation/tour-operator/)

= Where can I get support or talk to other users =
If you get stuck, you can ask for help in the [Tour Operator plugin forum](https://wordpress.org/support/plugin/tour-operator).
For help with premium add-ons from LightSpeed, use [our contact page](https://www.lsdev.biz/contact-us/)

= Will the Tour Operator plugin work with my theme 
Yes; the Tour Operator plugin will work with any theme, but may require some styling to make it match nicely. Please see our [codex](https://www.lsdev.biz/documentation/lsx/) for help. If you're looking for a theme with built in Tour Operator plugin integration we recommend [LSX Theme](https://www.lsdev.biz/product/lsx-wordpress-theme/).

= Where can I report bugs or contribute to the project? =
Bugs can be reported either in our support forum or preferably on the [Tour Operator GitHub repository](https://github.com/lightspeeddevelopment/lsx/issues).

= The Tour Operator plugin is awesome! Can I contribute? =
Yes you can! Join in on our [GitHub repository](https://github.com/lightspeeddevelopment/tour-operator) :)

== Screenshots ==

1. The slick Tour Operator plugin settings panel.
2. Tour itinerary admin panel.
3. Accommodation panel.
4. A tour itinerary archive.
5. A single tour itinerary page.

== Changelog ==

= 1.0.5 =
* Added TO Search as subtab on LSX TO settings page
* Styles from TO Search addon moved to it
* Made the function lsx_to_archive_entry_top function test all active post types, not only the three core post types

= 1.0.4 =
* Removed the last of the LSX_TO_POSTEXPIRATOR_TYPES constants
* Fixed an issue with empty post meta (depart from, end point)
* Removed Certain Travis CI code sniffers
* Added generic business contact details for enquire call to action
* Best time to visit added to destination (copied from tour)
* Enabled compatibility with LSX Blog Customiser (categories carousel)

= 1.0.3 =
* Added in a compatability check for all versions below PHP 7
* Fixed PHP errors when activating the plugin with a non LSX theme
* Hid the "Contact Details" custom field panel from Accommodation, these fields don't output to the frontend yet
* Updated the readme.txt content

= 1.0.2 =
* Fixed a conflict with some plugins using https://github.com/humanmade/Custom-Meta-Boxes
* Added a test to avoid the plugin activate with older versions from PHP than 5.6
* Added a warning for users that have the plugin activated in older versions from PHP than 5.6

= 1.0.1 =
* Allowing the placeholder to overwrite any empty image on all post types
* Fixed PHP warning notice, removed the constant LSX_TO_POSTEXPIRATOR_TYPES
* Fixed the PHP warning with the post order class
* Fixed the selecting of the global default placeholders
* Fixed PHP compatibility errors

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0 =
Initial release no updates available
