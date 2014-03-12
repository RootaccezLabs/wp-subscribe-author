=== Wp Subcribe Author ===
Contributors: gchokeen
Donate link: http://code-cocktail.in/donate-me/
Tags: user, author, email, notification, notify, posts, subscribe, subscription
Requires at least: 2.8
Tested up to: 3.4.1
Stable tag: 1.5.1
License: GPLv2

Wp Subscribe Author plugin is help subscriber to follow his/her favorite author.

== Description ==
Wp Subscribe Author plugin is help subscriber to follow his/her favorite author. Once subscriber starts follow the author, he will get notified allnew post of author by email.

How it works ?

Please check the FAQ section.


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
 No, Current version of the plugin  will not support for guest users.
 
 

== Changelog ==

= 1.0 =
* Initial release.
= 1.1 =
* english translation support added
= 1.5 =
* Object Oriented Programming style
* tipTips jquery plugin replaced by hovercard query plug-in 
= 1.5.1 =
* Nice authour url also automitcally work now

== Upgrade Notice ==

The current version of plugin requires WordPress 2.8 or higher. If you use older version of WordPress, you need to upgrade WordPress first.
