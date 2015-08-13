=== WP Display Users ===
Contributors: Devnath verma
Tags: widgets, users, display users, wp users, wp display user
Requires at least: 3.5
Tested up to: 4.2.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin provides that allows you to display **Users** in any custom template, page and widgetized sidebar. It includes the abilities to display name, description, email, URL.

== Description ==

This plugin provides that allows you to display **Users** in any custom template, page and widgetized sidebar. It includes the abilities to display name, description, email, URL.

= Features = 

* Create multiple rules
* Choose user role which you want to display 
* Include or exclude user by user IDs
* Display name, description, email or URL in frontend side
* Set limit of user display
* Option to display users as selected order by name, id, etc...
* Option to display users as selected order asc, desc
* Option to set Font Size of username frontside
* Option to set Text Transform of username frontside
* Option to set Font Size of content frontside
* Option to set Content Word Limit of content frontside
* Option to enable Pagination on sidebar widget
* Fully Responsive

= shortcode =

[wp_display_user id=rule-id]

You can use the <code>[wp_display_user id=rule-id]</code> shortcode to display users lisiting in page.

You can also use this shortcode for custom template.

`<?php echo do_shortcode("[wp_display_user id=rule-id]"); ?>`

= Notices =

** WP Display Users ** is now also on [GitHub](https://github.com/devnathverma/wp-display-users)!
	
** Anyone can write the CSS for my plugin, I will added it. 

== Installation ==

= Minimum Requirements =

* WordPress 3.5 or greater
* PHP version 5.2.4 or greater
* MySQL version 5.0 or greater

**This section describes how to install the plugin and get it working**

= Manual installation =

1. Download the plugin and extract its contents.
2. Upload the `wp-display-users` folder to the `/wp-content/plugins/` directory.
3. Activate **WP Display Users** plugin through the "Plugins" menu in WordPress.
4. After activating check the side menu -> "WP Display Users".
5. In your admin console, go to Appearance > Widgets, drag the "WP Display Users" to wherever you want it to be and click on Save.

That's it!


== Screenshots ==

1. User listing screen on custom template, page.
2. User listing screen on sidebar widget.
3. Plugin admin screen.
4. Appearence widget menu screen.

== Changelog ==