=== Plugin Name ===
Contributors: ianmjones
Donate link: https://www.bytepixie.com/
Tags: options, wp_options, admin, administration, search, sort, filter
Requires at least: 3.9
Tested up to: 4.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

List, sort, search and view your WordPress site's options records with style.

== Description ==

List, filter, sort and view options records, even serialized and base64 encoded values.

* List, sort and search options
* "Rich view" of serialized and JSON string values
* Works with base64 encoded serialized and JSON string values
* Highlights broken serialized values
* Supports Multisites

With Options Pixie you can find out what is really going on with your WordPress options.

Your wp_options table holds nearly all the settings that govern how your WordPress site looks and works, and if things aren't working quite as expected it's good to be able to peak into these records right in your site's admin dashboard.

Options Pixie highlights broken serialized values, showing you exactly where that string buried deep in a setting and supposed to be say 128 characters long is actually only 127, something that is otherwise hard to spot.

The Rich View takes those long unwieldy strings of serialized or JSON data and turns them into neat expandable lists of key/value pairs, much easier to read and understand.

Also with Rich View you can drill into those otherwise opaque base64 encoded values, Options Pixie decodes them to show you the serialized data, JSON string or object hidden within.

When installed on a WordPress Multisite it is activated at the network level, with a site selector shown above the list of records to enable switching between the options tables for each subsite.

The usual search and sort functionality you expect from an admin page are there to help you find the records you need, including filter links to switch between seeing all options records, permanent records only or transient options only.

== Installation ==

= From your WordPress dashboard =
1. Visit 'Plugins > Add New'
1. Search for 'Options Pixie'
1. Activate Options Pixie from your Plugins page.

= From WordPress.org =
1. Download Options Pixie.
1. Upload the 'options-pixie' directory to your '/wp-content/plugins/' directory, using your favorite method (ftp, sftp, scp, etc...)
1. Activate Options Pixie from your Plugins page.

== Frequently Asked Questions ==

= Do I have to activate Options Pixie on every Multisite subsite? =

Nope (it's network activated).

= Does Options Pixie support multiple networks? =

Yep.

= Can I add/edit/delete options records with Options Pixie? =

Nope (We're thinking about a Pro plugin for that, [vote for it here](https://www.bytepixie.com/options-pixie-pro).

= Can I fix broken serialized options records with Options Pixie? =

Nope (We're thinking about a Pro plugin for that, [vote for it here](https://www.bytepixie.com/options-pixie-pro).

== Screenshots ==

1. Options Pixie List View.
2. Options Pixie Rich View.

== Changelog ==

= 1.0.1 =
* Fix: Removed extra "/" displayed in multisite selector values for path based multisites.
* Fix: Remember Search & Sort checkbox now shown properly in Screen Options panel for multisites.
* Fix: Very first Search or Switch Site button usage now works as expected.
* Tested: WordPress 4.3

= 1.0 =
* Initial release.
