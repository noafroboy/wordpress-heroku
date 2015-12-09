=== Plugin Name ===
Contributors: NerdCow
Tags: auto tweeting, auto tweet, automated tweeting, blog, blogging, cron, feed, social, timeline, twitter, tweet, publish, free, google, manage, post, posts, pages, plugin, seo, profile, sharing, social, social follow, social following, social share, social media, community, wp cron, traffic, optimization, conversion, drive traffic, schedule, scheduling, timing, loop, custom post type, woocommerce, shop, products, easy digital downloads, portfolio, tweet content, pages, page, e-commerce
Requires at least: 3.8
Tested up to: 4.3
Stable tag: 1.4.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Tweet Wheel is a simple and yet powerful tool that every website owner will fall in love with. The idea behind Tweet Wheel is to automatically tweet posts from users' website and take the burden off their shoulders and let them focus on the thing they are best at. Turn your website into a traffic-and-business-driving tool in no time!

First, install and activate the plugin. You may notice not many options visible at start, but it's only until you authorise our Twitter app to access your Twitter account. Once authorised, you can enjoy your website gaining on social media attention even when you are not looking.

Unlike other Twitter plugins, this one works automatically and does not require your constant care. You can get up and running in a few clicks, but if you want to make more out of our solution, you can add multiple, interesting templates for each post. This will reduce your chance of sounding robotic and will let you test headings to see which one comes the most engaging.

**Current features**

* Automated queueing system, which is the core of the plugin. It handles all the automation.
* Multi-templating for posts helps you to specify limitless amount of tweet variations for each post.
* Advanced scheduling gives you more control over time of tweetings. Specify days and times at which you want your post published.
* Handling of custom post types - fully compatible with woocommerceshop products!
* Customising the queue let's you to supervise the order in which posts are tweeted.
* Looping is optional, but very useful. If on, it will automatially append just tweeted post at the end of queue. Keeps going infinitely this way.
* Queue posts on their publishing. When you create a new post you can ask plugin to automatically queue it for you.
* Pausing and resuming queue comes useful when you need a bit more control. No need to deactivate the plugin to put it on hold.
* Tweet about a post on publish or update action.
* Convenient bulk actions - queue, dequeue and exclude multiple posts at once.
* Option to tweet instantly without waiting for post's turn - perfect for hot news!
* Simple view which minifies the queue look so you can fit more items on your screen - helpful for shuffling!
* Health check tab that let's you know if your website is ready for Tweet Wheel and what to fix.
* Attach featured images to your tweets with one click
* Use your favorite domain for shortening URLs (by Bit.ly)
* Track clicks and tweets history to improve performance
* Automated updates - no more manual labor!
* Plenty minor improvements which overally boost user experience and easy of use

If you have a suggestion for improvement or a new feature, feel free to use [the Support forum](https://wordpress.org/support/plugin/tweet-wheel) or contact us directly via [our website](https://nerdcow.co.uk/contact-us).

Want regular updates? Follow us on [Twitter](https://twitter.com/NerdCowUK) 

== Installation ==

**Minimum requirements**

* WordPress 3.8 or greater
* PHP version 5.2 or greater
* MySQL version 5.0 or greater
* WP Cron enabled in your WordPress installation

There are two ways to install Tweet Wheel

**via WordPress plugin upload - automated**

Navigate to Plugins > Upload New Plugin and upload the zip file you have downloaded. Voilla!

**via FTP client - manual**

1. Unzip the zip file you have downloaded
1. Upload unzipped folder `tweet-wheel` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to Tweet Wheel > Authorize, provide required details and authorize our plugin to access your Twitter acount

== Frequently Asked Questions ==

[http://tweet-wheel.com/docs/faq/](http://tweet-wheel.com/docs/faq/)

== Changelog ==

= 1.4.1 = 
* Switched the auto-updating system to our own meaning better stability and not interrupted service. Please make sure you update your plugins as always, because any further releases will be provided through the new channel.

= 1.4 =
* VERY IMPORTANT UPDATE: Changed from one-click authorisation to standard Twitter authorisation that requires an app created on user's Twitter account. (PLEASE NOTE: You need to re-authorise your plugin with Twitter in order to keep using it) 

= 1.3.10 =
* Bug fixes

= 1.3.9.1 =
* Fixed character counter, which was incorrectly counting foreign characters such as ü or ä.

= 1.3.9 =
* Added a setting to disable analytics, which cause high CPU load for some users.

= 1.3.8 =
* Fixed conflict with Customizer

= 1.3.7.1 =
* Bug fixes

= 1.3.7 =
* Fixed the issue where the plugin was tweeting same post over and over - usually the very top one. This fix may also help those who experience random anomalities with the queue looping only first 7-8 tweets.

= 1.3.6 =
* IMPORTANT! This update fixes major issue with analytics data growing into enormous size in your database. Installing this version will cause your stats to be purged for a short amount of time until the recurring has task been run again. Please wait patiently.
* Increased interval between checking for new analytics date from 1 minute to 1 hour to decrease CPU load.
* Changed the way plugin queries analytics from the database to speed up the page loading, which was taking too long.
* Added pagination. You will be able to browse analytics based on weeks.

= 1.3.5 =
* Fixed template characters counter, which was allowing to exceed 140 characters limit without showing an error.
* Improved compatibility and precision of the Schedule in various timezones - plugin will now use your local time to control the timing.

= 1.3.4 =
* Featured images will be attached to all posts by default. Added an option to exclude featured images individually on the pots edit screen.

= 1.3.3 =
* Forced WP Cron jobs to be setup by the plugin. Some users experienced troubles with tweets going out. This update should fix it.
* Removed the "Save Changes" queue button. Now the Queue will save itself dynamically when the order has changed.
* Improved compatibility with WooZone - WooCommerce Amazon Affiliates plugin.

= 1.3.2 =
* Fixed analytics not working for some users.
* Fixed automatic updates. From this version up, you can update plugin from your WP dashboard.

= 1.3.1 =
* Fixed compatibility bug with the wpMandrill plugin

= 1.3 =
* Added "Tweet On-Save" feature which allows to send a tweet using any template on publish or update action.
* Added responsive interface. You can use Tweet Wheel Pro on mobile devices now.
* Fixed bug making "use as tweet's image" checkbox to disappear when inserting a featured image to a post.
* Fixed bug which was causing unpublished post being added to the queue.
* Hid bulk actions from all list screens apart from "All" and "Published".

= 1.2 =
* Added settings data importer from Tweet Wheel Lite to Tweet Wheel Pro. Includes things like authorisation details, post templates, settings, queue etc. The migration is now seamless.
* Fixed a bug which prevented Tweet Wheel Pro from removing custom database table on uninstallation.

= 1.1.3 =
* Fixed a bug preventing some users from saving tweet templates

= 1.1.2 =
* Hid plugin's metaboxes on edit screens from unauthorised users
* Fixed PHP errors when saving a post without any templates in the debug mode
* Revisited jQuery validation on post edit screens to increase compatiblity with other plugins

= 1.1.1 =
* Fixed post templates validation bug
* Fixed error notification when WP Cron is disabled

= 1.1 =
* Fixed queue re-fill counter
* Fixed issue with the "attached image" icon showing for all queued up items
* Added licensing

= 1.0 =
* Initial release