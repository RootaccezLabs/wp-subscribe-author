=== Wp Subcribe Author ===
Contributors: gchokeen
Donate link: http://code-cocktail.in/donate-me/
Tags: user, author, email, notification, notify, posts, subscribe, subscription
Requires at least: 2.8
Tested up to: 4.3.1
Stable tag: 1.8
License: GPLv2

Wp Subscribe Author plugin is help subscriber to follow his/her favorite author.

== Description ==
Wp Subscribe Author plugin is help subscriber to follow his/her favorite author. Once subscriber starts follow the author, he will get notified all new post of author by email.

How it works ?

Please check the FAQ section.

1. Display subscriber favourite author posts using this shortcode **[favourite-author-posts]** - It will work only for logged in subscribers!
2. Use this shortcode to display the subscribe author button on pages/post **[subscribe-author-button]**


== Installation ==

e.g.

1. Upload the entire `wp-subscribe-author` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress


== Screenshots ==

1. screenshot-1.png

== Frequently Asked Questions ==

= How to get subscribe link in template ? =

Plugin will automatically find the author link and display the pop card. Author link should contain the rel="author" attribute. If it's not working please add the 'data-authorID="<?php echo get_the_author_meta('ID'); ?>"' to author link.

= Can Guest user subcribe author post ? =
Yes, plugin supports guest user subscription option from verion 1.6 .
 
 = Can I customize the favourite author posts template ? =
 Yes, we can customize the template. You can create your custom code template on your active theme with the name **content-favourite-author-posts.php**. If you don't create this file, plugin will find the **content.php** from your active
 wordpress theme.
 

== Changelog ==

= 1.0 =
* Initial release.
= 1.1 =
* English translation support added.
= 1.5 =
* Object Oriented Programming style.
* tipTips jquery plugin replaced by hovercard query plug-in. 
= 1.5.1 =
* Nice authour url also automitcally work now.
= 1.6 =
* Guest user subscription option added.
= 1.6.1 =
* Bug fix: ajax loading text added
= 1.6.5 =
* Shortcode added to display subscriber favourite author posts
= 1.7 =
* Admin settings for card & email
= 1.8 =
* Shortcode added to display subscribe author button on pages/post

== Upgrade Notice ==

The current version of plugin requires WordPress 2.8 or higher. If you use older version of WordPress, you need to upgrade WordPress first.
