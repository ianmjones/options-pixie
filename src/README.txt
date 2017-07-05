=== Plugin Name ===
Contributors: ianmjones
Donate link: https://www.bytepixie.com/
Tags: options, wp_options, admin, administration, search, sort, filter, view
Requires at least: 3.9
Tested up to: 4.8
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

= Inspect Your Site's Options =
With Options Pixie you can find out what is really going on with your WordPress options.

Your wp_options table holds nearly all the settings that govern how your WordPress site looks and works, and if things aren't working quite as expected it's good to be able to peak into these records right in your site's admin dashboard.

= Broken Serialized Values =
Options Pixie highlights broken serialized values, showing you exactly where that string buried deep in a setting and supposed to be 128 characters long is actually only 127, something that is otherwise very hard to spot.

= Rich View =
The Rich View takes those long unwieldy strings of serialized or JSON data and turns them into neat expandable lists of key/value pairs, much easier to read and understand.

= Decodes Base64 Encoded Values =
Also with Rich View you can drill into those otherwise opaque base64 encoded values, Options Pixie decodes them to show you the serialized data, JSON string or object hidden within.

= Multisite Support =
When installed on a WordPress Multisite it is activated at the network level, with a site selector shown above the list of records to enable switching between the options tables for each subsite.

= Search & Sort =
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

Nope, but we have a Pro addon for that called [Options Pixie Pro](https://www.bytepixie.com/options-pixie-pro).

= Can I fix broken serialized options records with Options Pixie? =

Nope, but we have a Pro addon for that called [Options Pixie Pro](https://www.bytepixie.com/options-pixie-pro).

== Screenshots ==

1. Rich View.
2. List View.
3. Multisites Supported.
4. Screen Options Pane.
5. Help Pane.

== Changelog ==

= 1.1.2 =
* Fix: Deprecation notice for `wp_get_sites` on WP 4.6+.

= 1.1.1 =
* Fix: Fixed "PHP Notice: Trying to get property of non-object" in debug log when trying to expand already deleted record.
* Change: Minor improvements to README and wp.org banner image.
* Tested: WordPress 4.7.

= 1.1 =
* New: There is now a small promo for Options Pixie Pro in the footer of Option Pixie.
* Change: Header text now complies with WordPress 4.3+ changes, but remains backwards compatible.
* Change: Primary column set to "Option Name" in WordPress 4.3+.
* Change: Security improvements.
* Change: "Option ID" column moved away from first column to get around a bug in WordPress 4.3's mobile view.
* Change: Records now sorted by the "Option Name" column by default.
* Change: Values from the `option_value` field are now HTML escaped.
* Fix: Ensure default sort column shows the sort indicator on very first usage.
* Fix: "O" now shown correctly in Type column when value is a serialized Object.
* Fix: Stopped Option Pixie's CSS and JavaScript being included in other admin pages.
* Fix: Fixed problem with browser URL not being updated to match current search.

= 1.0.1 =
* Fix: Removed extra "/" displayed in multisite selector values for path based multisites.
* Fix: Remember Search & Sort checkbox now shown properly in Screen Options panel for multisites.
* Fix: Very first Search or Switch Site button usage now works as expected.
* Tested: WordPress 4.3.

= 1.0 =
* Initial release.
