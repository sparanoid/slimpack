=== Slimpack - Lightweight Jetpack ===

Contributors: Sparanoid, jjloomis
Donate link: http://sparanoid.com/donate/
Tags: Slimpack, WordPress.com, statistics, stats, views, tweets, twitter, widget, gravatar, hovercards, profile, equations, latex, math, maths, youtube, shortcode, archives, audio, blip, bliptv, dailymotion, digg, flickr, googlevideo, google, googlemaps, kyte, kytetv, livevideo, redlasso, rockyou, rss, scribd, slide, slideshare, soundcloud, vimeo, shortlinks, wp.me, subscriptions, notifications, notes, json, api, rest, mosaic, gallery, slideshow, videopress, monitor, search, omnisearch, sso, jet pack
Requires at least: 4.1.1
Tested up to: 4.6
Stable tag: 1.0.17

License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Slimpack — Lightweight Jetpack. Super-fast performance without modules that requires contracting WordPress.com.

== Description ==

Slimpack — Lightweight Jetpack. Super-fast performance without modules that requires contracting WordPress.com.

It provides better performance than original Jetpack. All features that require a WordPress.com account or need contracting / syncing back to WordPress.com servers have been removed. You don't need to connect to WordPress.com to use this plugin. If your site got slow response time (TTFB) after activating the original Jetpack, you should definitely try out this plugin.

More information please visit my [site](http://sparanoid.com/work/slimpack/).

View source code and submit issue at [GitHub](https://github.com/sparanoid/slimpack)

Love this? Please consider [buying me a cup of coffee](http://sparanoid.com/donate/).

Note: Please do not submit issue unless the issue cannot be reproduced using the original Jetpack module.

= Features =

* Transfer from Jetpack to Slimpack seamlessly, just deactivate Jetpack and activate Slimpack, all settings and module status will be kept and work just like before.
* All source code is untouched and synced from Jetpack
* Better performance than original Jetpack. All features that require a WordPress.com account have been removed. You don't need to connect to WordPress.com to use this plugin.
* Multilingual support (and languages have already bundled in Slimpack!)

= Available Modules =

The following modules are available in Slimpack:

* Carousels
* Contact Form
* Custom CSS
* Custom Content Types
* Infinite Scroll
* Gravatar Hovercards
* Beautiful Math (LaTeX)
* Markdown
* Omnisearch
* Sharing (Sharedaddy)
* Shortcode Embeds
* Site Icon
* Site Verification
* Widget Visibility
* Widgets

Additional notes:

* `devicepx-jetpack.js` is already packed in the plugin! No external script will slow down your website.
* Beautiful Math: still need internet connection to get generated LaTeX images from `wp.com`.
* Site Icon: Sync icon to WordPress.com via Jetpack server is disabled.
* VideoPress shortcode: VideoPress shortcode is removed since VideoPress is not available in Slimpack .

= Under-the-hood Features =

The following hidden modules are also available in Slimpack:

* Holiday Snow!
* Feature Content support
* Open Graph
* Twitter Cards
* Other theme tools originally shipped from Jetpack

= Work in Progress =

The following modules are working-in-progress, I'll add them later.

* Mobile Theme (Minileven)
* VaultPress

= Dropped Modules =

The following modules **should** be working in Slim version but…

* Tiled Galleries: It requires Photon to retrieve images generated from WordPress.com proxy.

= Unsupported Modules =

The following modules in Jetpack are not supported in Slimpack.

* Enhanced Distribution
* JSON API
* Jetpack Comments
* Jetpack Single Sign On
* Likes
* Manage
* Monitor
* Notifications
* Photon
* Post by Email
* Protect
* Publicize
* Related Posts
* Spelling and Grammar
* Subscriptions
* VideoPress
* WP.me Shortlinks
* WordPress.com Stats

= Known Issues =

* VaultPress doesn't work with this plugin (at this time), I'm looking for a solution.
* Please note: Slimpack is not fully tested on multisite, if you see any issue with multisite, feel free to give me feedback.

Love this? Please consider [buying me a cup of coffee](http://sparanoid.com/donate/).

== Installation ==

This section describes how to install the plugin and get it working.

WordPress:

Transfer from Jetpack to Slimpack seamlessly, just deactivate Jetpack and activate Slimpack, all settings and module status will be kept and work just like before.

1. Download it from your WordPress Plugin page
2. Deactivate Jetpack and activate Slimpack in Plugin page

or you can upload the plugin manually:

1. Upload the extracted files to the `/wp-content/plugins/` directory
2. Deactivate Jetpack and activate Slimpack in Plugin page

Multisite enabled WordPress:

1. Upload the extracted files to the `/wp-content/plugins/` directory
2. In 'Site Admin' mode (You need log in as site admin), go to 'Plugins' page, choose 'Network Activate'
3. Just FYI, you can also activate this plugin individually for different sites.

Old-styled WordPress Mu:

1. Upload the extracted files to the `/wp-content/mu-plugins/` directory
2. That's all.

== Frequently Asked Questions ==

= Is original Jepack required for this plugin? =

No.

= Can I use this with original Jetpack at the same time? =

No. It's not compatible with the original Jetpack. You can't activate Jetpack and Slimpack at the same time.

== Screenshots ==

1. Settings page

== Changelog ==

= 1.0.17 =
* Update to Jetpack core 4.0.4, updater: JJ Loomis (@jjloomis) and Tunghsiao Liu (@sparanoid)

= 1.0.16 =
* Update to Jetpack core 4.0.3, updater: JJ Loomis (@jjloomis) and Tunghsiao Liu (@sparanoid)

= 1.0.15 =
* Compatibility check for 4.5, nothing new, just bump version to tell everyone this plugin still works.

= 1.0.14 =
* Update to Jetpack core 3.9.6, updater: JJ Loomis (@jjloomis)

= 1.0.13 =
* Update to Jetpack core 3.9.4, updater: JJ Loomis (@jjloomis)

= 1.0.12 =
* Fix missing Infinite Scroll for Twenty Sixteen, props @chupo_cro

= 1.0.11 =
* Beautiful Math doesn't work if Infinite Scroll disabled

= 1.0.10 =
* Update to Jetpack core 3.9.1, updater: JJ Loomis (@jjloomis)

= 1.0.9 =
* Update to Jetpack core 3.8.2, updater: JJ Loomis (@jjloomis)

= 1.0.8 =
* Fix Test up to tag

= 1.0.7 =
* Update to Jetpack core 3.8.0, updater: JJ Loomis (@jjloomis)

= 1.0.6 =
* Update to Jetpack core 3.7.2
* Welcome our first co-maintainer JJ Loomis (@jjloomis)!!!

= 1.0.5 =
* Update to Jetpack core 3.7
* Fix PHP fatal errors with YouTube links
* Maintainer wanted, see https://github.com/sparanoid/slimpack/issues/1 for more info

= 1.0.4 =
* Update to Jetpack core 3.6.1
* Fix conflict with Contact Form 7
* Maintainer wanted, see https://github.com/sparanoid/slimpack/issues/1 for more info

= 1.0.3 =
* Update readme
* Sync scripts from Jetpack

= 1.0.2 =
* Add Custom Content Types support
* Add Gravatar Hovercards support (internet connection is required to display hovercards)
* Add Omnisearch support
* Add Sharing (Sharedaddy) support
* Add Open Graph support
* Add Twitter Cards support

= 1.0.1 =
* Update plugin name and bump tested up to section

= 1.0.0 =
* First release

== Upgrade Notice ==

= 1.0.17 =
* Update to Jetpack core 4.0.4, updater: JJ Loomis (@jjloomis) and Tunghsiao Liu (@sparanoid)

= 1.0.16 =
* Update to Jetpack core 4.0.3, updater: JJ Loomis (@jjloomis) and Tunghsiao Liu (@sparanoid)

= 1.0.15 =
* Compatibility check for 4.5, nothing new, just bump version to tell everyone this plugin still works.

= 1.0.14 =
* Update to Jetpack core 3.9.4, updater: JJ Loomis (@jjloomis)

= 1.0.13 =
* Update to Jetpack core 3.9.6, updater: JJ Loomis (@jjloomis)

= 1.0.12 =
* Fix missing Infinite Scroll for Twenty Sixteen, props @chupo_cro

= 1.0.11 =
* Beautiful Math doesn't work if Infinite Scroll disabled

= 1.0.10 =
* Update to Jetpack core 3.9.1, updater: JJ Loomis (@jjloomis)

= 1.0.9 =
* Update to Jetpack core 3.8.2, updater: JJ Loomis (@jjloomis)

= 1.0.8 =
* Fix Test up to tag

= 1.0.7 =
* Update to Jetpack core 3.8.0, updater: JJ Loomis (@jjloomis)

= 1.0.6 =
* Update to Jetpack core 3.7.2

= 1.0.5 =
* Update to Jetpack core 3.7, Fix PHP fatal errors with YouTube links, Maintainer wanted, see https://github.com/sparanoid/slimpack/issues/1 for more info

= 1.0.4 =
* Update to Jetpack core 3.6.1, Fix conflict with Contact Form 7, Maintainer wanted: https://github.com/sparanoid/slimpack/issues/1

= 1.0.3 =
* Sync scripts from Jetpack, many modules added from Jetpack in version 1.0.2

= 1.0.2 =
* Add Custom Content Types, Gravatar Hovercards, Omnisearch, Sharing (Sharedaddy), Open Graph, Twitter Cards support.

= 1.0.1 =
* Update plugin name and bump tested up to section

= 1.0.0 =
* First release
