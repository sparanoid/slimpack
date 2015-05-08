=== Slimpack - Lightweight Jetpack ===

Contributors: Sparanoid
Donate link: http://sparanoid.com/donate/
Tags: api, archives, audio, Blip, blip.tv, dailymotion, Digg, equations, flickr, gallery, google, googlemaps, googlevideo, gravatar, hovercards, jet pack, json, kyte, kytetv, latex, livevideo, math, maths, monitor, mosaic, notes, notifications, omnisearch, profile, redlasso, REST, rockyou, rss, scribd, search, shortcode, shortlinks, slide, slideshare, slideshow, soundcloud, sso, statistics, stats, subscriptions, tweets, twitter, videopress, views, vimeo, widget, WordPress.com, wp.me, youtube
Requires at least: 4.1
Tested up to: 4.2.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Slimpack — Lightweight Jetpack. Super-fast performance without modules that requires contracting WordPress.com.

== Description ==

Slimpack — Lightweight Jetpack. Super-fast performance without modules that requires contracting WordPress.com.

It provides better performance than original Jetpack. All features that require a WordPress.com account or need contracting / syncing back to WordPress.com servers have been removed. You don't need to connect to WordPress.com to use this plugin. If your site got slow response time (TTFB) after activating the original Jetpack, you should definitely try out this plugin.

More information please visit my [site](http://sparanoid.com/work/slimpack/).

View source code and submit issue at [GitHub](https://github.com/sparanoid/slimpack)

Note: Please do not submit issue unless the issue cannot be reproduced using the original Jetpack module.

**Features**

* All source code is untouched and synced from Jetpack
* Better performance than original Jetpack. All features that require a WordPress.com account have been removed. You don't need to connect to WordPress.com to use this plugin.
* Multilingual support

**Available Modules**

The following modules are available in Slimpack:

* Carousels
* Contact Form
* Custom CSS
* Infinite Scroll
* Beautiful Math (LaTeX)
* Markdown
* Shortcode Embeds
* Site Icon
* Site Verification
* Widget Visibility
* Widgets

Additional note:

* `devicepx-jetpack.js` is already packed in the plugin! No external script will slow down your website.
* WordPress version check is missing in Slim version, so make sure your WordPress is up-to-date.
* Beautiful Math: still need internet connection to get generated LaTeX images from `wp.com`.
* Site Icon: Sync icon to WordPress.com via Jetpack server is disabled.

**Under-the-hood Features**

The following hidden modules are also available in Slimpack:

* Holiday Snow module
* Feature Content support
* Other theme tools originally shipped from Jetpack

**Work in Progress**

The following modules are working-in-progress, I'll add them later.

* Gravatar Hovercards
* Omnisearch
* Sharing Support
* Mobile Theme (Minileven)
* VaultPress

**Dropped Modules**

The following modules **should** be working in Slim version but…

* Custom Content Types: It requires too many Jetpack dependencies.
* Tiled Galleries: It requires Photon to retrieve images generated from WordPress.com proxy.

**Unsupported Modules**

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

== Installation ==

This section describes how to install the plugin and get it working.

WordPress:

1. Upload the extracted files to the `/wp-content/plugins/` directory
2. In 'Plugins' page, choose 'Activate'

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

1. A great demo

2. Option page

== Changelog ==

= 1.0.1 =
* Update plugin name and bump tested up to section

= 1.0.0 =
* First release

== Upgrade Notice ==

= 1.0.1 =
* Update plugin name and bump tested up to section

= 1.0.0 =
* First release
