=== FeedWordPress Advanced Filters ===
Contributors: basszje
Donate link: http://www.weblogmechanic.com/plugins/feedwordpress-advanced-filters/
Author URI: http://www.weblogmechanic.com
Plugin URI: http://www.weblogmechanic.com/plugins/feedwordpress-advanced-filters/
Tags: syndication, aggregation, feed, atom, rss, feedwordpress, filters, filters
Requires at least: 3.0
Tested up to: 4.0.1
Stable tag: 0.6.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


== Short Description ==

FeedWordPress Advanced Filters (FAF) gives you powerful options to filter your aggregated Feedwordpress items. Tidy output, import images,make posts expire, rewrite your links... New features are being added constantly. 

== Description ==

* Author: [Bas Schuiling](http://www.weblogmechanic.com/about)
* Project URI: <http://www.weblogmechanic.com/plugins/feedwordpress-advanced-filters/>
* License: GPL 2. See License below for copyright jots and tittles.

FeedWordPress Advanced Filters (FAF) gives you powerful options to filter your aggregated Feedwordpress items. Tidy output, import images,make posts expire, rewrite your links... New features are being added constantly.

Features: 

* Remove Keywords or HTML tags
* Get and save images into your blog in your defined sizes
* Set featured images from your feeds
* Put posts in different categories based on the keywords you want
* Make aggregated posts expire after some time
* Set links to open in new window or use your outbound tracker URL

**FAF requires Feedwordpress plugin and PHP 5.3 to be installed!**

Expire posts filter requires post expirator plugin

  [WordPress]: http://wordpress.org/
  [WordPress MU]: http://mu.wordpress.org/
  [3.0]: http://codex.wordpress.org/Version_3.0

== Installation ==

To use FeedWordPress Advanced Filters, you will need:

* 	an installed and configured copy of [WordPress][] or [WordPress MU][]
	(version 3.0 or later).

*	FTP, SFTP or shell access to your web host

*	The Feedwordpress plugin

= New Installations =

1.  	Make sure Feedwordpress is installed!

2.	Download the FeedWordPress Advanced Filter installation package and extract the files on
	your computer. 

3.	Extract the map feedwordpress_advanced_filters to your plugin directory

4.	Log in to the WordPress Dashboard and activate the FeedWordPress Advanced Filters plugin.

5. 	Once activated new filters will appear in your Feedwordpress syndication settings under 'Feedwordpress Advanced Filters
	in the tabs 'Post & Links' and 'Categories & Tabs'


= Upgrades =

To *upgrade* an existing installation of FeedWordPress Advanced Filters to the most recent
release:

1.	Download the FeedWordPress Advanced Filters installation package and extract the files on
	your computer. 

2.	Overwrite the directory faf with the new files
	

== Screenshots ==

1. Settings screen


== Changelog ==

= 0.6.2 = 

* Translations made possible - handling better
* Image filename filter for manual filename control.

= 0.6 = 

Fixes: 

* No help text when adding new filters and help texts now don't fall off the screen 
* Featured images now work as they should
* Image handling more robust
* Fixed shorthand PHP tag problem in category filter
* Process complete will only remove metadata on success ( prevents images not being attached to correct post )

New: 

* Support for Image enclosures in feed
* Ability to set enclosures as featured image 
* Loading icon in interface when new filter settings are loading
* Main filter checks if any post data is given back, if not assumes a filter failed and returns unfiltered post
* New Link filter allows you to set image to open in new window and/or rewrite URL to pass by an internal URL ( for link tracking ) [beta]

= 0.5.9 =

Bugs fixed: 

* Multiple keywords for category filter breaks filter
* Cron jobs will now function as should (broke filters in some cases)
* PHP 5.3.X version fileInfo not loading filters bug fixed
* Loading order in add submenu fixed (interface)
* If no per feed filters are defined and global filters are off on feed nothing will happen
* Save local images tested and will now work with more exceptions ( cases, special characters, strange URL's etc )
* Image filter more robust; excerpt and content checked seperately 

New: 

* Rudimentary help texts for filters
* Featured images can be set
* Images can be excluded from filtering (and removed from content)

= 0.5.8 = 

New: 

* Expire post filter
* Image attachment will now be attached correctly to post
* Filters can now process things after the feed update
* Rudimentary overview page for all settings
* Screenshot

Fixed: 

* FAF now checks for PHP 5.3 in code
* Fixed compatibility between PHP 5.3 and PHP 5.3.26 regarding directory lookup
* Fixed compatibility on calling subclasses between PHP versions
* Plugin break when using Apache server Short_open_tags = Off
* Default behavior for global settings is now more logic  
 
= 0.5.5 = 

Fixed: 

* FAF no longer loads itself on the front page (performance)
* Filter numbering in Javascript interface
* Bug when default checkbox values would reset itself 
* Pattern flaw in 'match entire word' filter option
* Images with querystrings are now picked up correctly
	
New:	

* Moved Filter control to its respective classes
* FAF now allows adding custom filters easily
	

= 0.5 = 

Fixed:

* Some option descriptions worded more clear
* Small layout fixes

Updated:

* PHPdoc documentation
* All functions and class variables have defined scope

= 0.4.5 = 

New:

* Plugin activation and clean removal
* JS form validation and Required fields]
* Support for default field values
* Basic filter overview
* Checks if Feedwordpress is present
	
Bugs Fixed:

* Filter numbering 
* JS bug when deleting new filters

= 0.4.1 =

New: 

* Even more presentable interface (Ajax, Divs and CSS-layout)
* Basic error reporting back to user
* Option to partial match keywords or full words only
* Split interface class and filter class
* Image Filters: more robust against exceptions and malformed code
* Image Filters: Now one filter with multiple options
* Options interface dynamic and reusable
* Customizable HTML removal filter 
	
Bugs fixed:

* Image filters filtered only first image in content. Fixed to include all.
* Empty selections no longer cause errors
* Removed remaining Call-By-Reference calls
* Removed Debug messages
* Output error bug when activating plugin

= 0.3 = 

New:

* More filter options: Image filters
* Possible options per filter more dynamic
* Interface more readable; added javascript and better layout
* Checks for malformed user input
* Filter: Basic HTML removal

= 0.2 = 

Features:

* More filter options: match multiple keywords comma-seperated
* Readme file added ( this thing ) 

Bugs fixed:

* Unserialize error using update_option
* Empty category not visible

= 0.1 = 

* Basic interfaces
* First basic filter

== Frequently Asked Questions == 

**Unexpected T_PAAMAYIM_NEKUDOTAYIM or double colon error** 

FAF will not run on PHP version lower than PHP 5.3. Most likely you are using 
5.2.X or even an older version. Consider upgrading or bug your hoster about it. 

	
== License ==

The FeedWordPress Advanced Filter plugin is copyright © 2013 by Bas Schuiling. It uses
code derived or translated from:


according to the terms of the [GNU General Public License][].

This program is free software; you can redistribute it and/or modify it under
the terms of the [GNU General Public License][] as published by the Free
Software Foundation; either version 2 of the License, or (at your option) any
later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details.

  [GNU General Public License]: http://www.gnu.org/copyleft/gpl.html

