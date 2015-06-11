=== WordPress Praiser Widget ===
Plugin Name: WordPress Praiser Widget
Author URI: http://www.designonastick.com/
Author:  c Shell Franklin
Version: 1.5
Donate link: 
Contributors: cShellFranklin
Tags: praises, random quote, testimonials, quotes, quotations, sidebar, widget, image
Requires at least: 3.0
Tested up to: 3.2
Stable tag: 1.5
License: GPLv2 or later

WP Praiser lets you display Praises (or testimonials) in a sidebar in WordPress or fill a whole page. This plug-in allows for an image of the prasier.

== Description ==

WordPress Praiser plug-in lets you display Praises (or testimonials if you like) in a sidebar on your WordPress blog or fill a whole page using a short code. This plug-in allows for an image of the prasier.

**Features and notes**

* All praises (subject to filters/tags) are in the html source so will be seen by search engines like Google. 
* **Admin interface**: A simple interface to add, edit and manage praises, praiser, and their images. Details such as author, their image, and source of the quote, and attributes like tags, font size, visibility, and what page you are using as a full page of praises, can be specified. 
* **Sidebar widget**: The Praises sidebar widget loads a praise then rotates through all available praises that are tagged as public. The following is the list of options in the widget control panel:
	* Widget title (optional)
	* Option to show/hide author
	* Option to show/hide source
	* Choose random or sequential order for refresh
	* CHoose to show the quote marks or not
	* Show only quotes with certain tags
	* Specify a character limit and filter out bigger quotes
	* Refresh Interval (In seconds)
	* Font size (Praiser name will appear one sixe larger)
	* Page chooser for full page of praises
* Compatible with WordPress 3.0 multi-site functionality.

== Installation ==
1. Upload `wp_praises` directory to the `/wp-content/plugins/` directory
2. Activate the 'WordPress Praiser Widget' plugin through the 'Plugins' menu in WordPress
3. Add and manage the quotes through the 'Our Praises' menu in the WordPress admin area
4. To display praises in the sidebar, go to 'Widgets' menu and drag the ' WordPress Praiser Widget' widget into the sidebar
5. To add all the praises to a page, place the shortcode [wppraise] on the chosen page, then in the widget->Advanced options chose that page from the drop down (this keeps the random praises from appearing on that page).

== Frequently Asked Questions ==

= How do I stop parts of the praises (text/author/source) from being truncated? =

Specify a larger minimum height in the WordPress Praiser Widget, see screenshot 2.

= Is there a way to get rid of the quotation marks that surround the random praise? =

Yes. Now it is even simpler. The option is on the widget, see screenshot 2.

= How to change the admin access level setting for the quotes collection admin page? =

Change the value of the variable `$wp_praiser_admin_userlevel` on line 30 of the wp-praiser.php file. Refer [WordPress documentation](http://codex.wordpress.org/Roles_and_Capabilities) for more information about user roles and capabilities.

== Screenshots ==

1. Admin interface (in WordPress 3.2)
2. 'WordPress Praiser' widget options (WordPress 3.2)
3. A praise in the sidebar (WordPress 3.2)
4. A full page of praises (WordPress 3.2)
	

==Changelog==
* **2015-06-10: Version 1.5**
	* Fixed image upload capability
	* Fixed install issue
	* Tested in WP 4.2
* **2011-08-30: Version 1.3**
	* Added image upload capability
	* added option to turn on and off quote make to widget
* **2011-08-26: Version 1.2**
	* Added full page option with shortcode [wppraise]
* **2011-08-01: Version 1.1**
	* Added Character Font size option
* **2011-07-01: Version 1.0**
	* initial release

== Upgrade Notice ==
= 1.5 =
This version fixed several issues caused during installs.
= 1.3 =
This version added the upload image button to the admin page and added option to widget to remove quote mark. 
= 1.2 =
This version added the shortcode for creating a full page of praises. 
= 1.1 =
This version allows you to change font size.
