=== AffiliateWP - Affiliate Product Rates ===
Contributors: sumobi, mordauk
Tags: AffiliateWP, affiliate, Pippin Williamson, Andrew Munro, mordauk, pippinsplugins, sumobi, ecommerce, e-commerce, e commerce, selling, referrals, easy digital downloads, digital downloads, woocommerce, woo, products, product, rates
Requires at least: 4.6
Tested up to: 5.5
Requires PHP: 5.3
Stable tag: 1.0.5.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows you to set product referral rates on a per-affiliate level in AffiliateWP.

== Description ==

> This plugin requires [AffiliateWP](http://affiliatewp.com/ "AffiliateWP") v1.5.2+ in order to function.

At its most basic level AffiliateWP allows you to set a global referral rate which all affiliates share. For integrations that support per-product referral rates, affiliates can earn different commissions based on which product/s are purchased. AffiliateWP also allows you to set referral rates on a per-affiliate level which will override any per-product referral rate.

Affiliate Product Rates expands this even further, allowing different per-product referral rates on a per-affiliate level.

Here are some examples of how you might use this plugin:

1. Give Affiliate X 10% commission for Product A, 80% commission for Product B, and a flat rate of $50 for Product C.
2. Give Affiliate Y 50% commission for Product A, but a lower commission than Affiliate X for Products B and C.
3. Give Affiliate Z a flat-rate of $10 commission for Product A, 65% commission for Product B and 22% commission for Product C.

The possibilities are endless! You can also set per-product referral rates per-affiliate per-integration! (for those that happen to be running both integrations listed below).

**Currently Supported Integrations**

1. Easy Digital Downloads
2. WooCommerce

**Known Issues**

The add-on doesn’t yet prevent a product from existing in more than 1 product rate. Be careful you don’t accidentally add a product to more than location or one rate will be ignored.

**What is AffiliateWP?**

[AffiliateWP](http://affiliatewp.com/ "AffiliateWP") provides a complete affiliate management system for your WordPress website that seamlessly integrates with all major WordPress e-commerce and membership platforms. It aims to provide everything you need in a simple, clean, easy to use system that you will love to use.


== Installation ==

1. Unpack the entire contents of this plugin zip file into your `wp-content/plugins/` folder locally
1. Upload to your site
1. Navigate to `wp-admin/plugins.php` on your site (your WP Admin plugin page)
1. Activate this plugin

OR you can just install it with WordPress by going to Plugins &rarr; Add New &rarr; and type this plugin's name

Go to Affiliates &rarr; Affiliates, and click "edit" next to the affiliate you'd like to set up the product rates for.

== Screenshots ==

1. The product rates UI. Shown with 2 integrations active, Easy Digital Downloads and WooCommerce. The same UI is also available when adding an affiliate.

== Changelog ==

= 1.0.5 =
* Fix: Flat rate shows USD instead of the currency selected in the general settings
* Tweak: Update the Tested up to WordPress version to 5.1
* Tweak: Add translation files for the Polish language (pl_PL)

= 1.0.4 =
* Fix: Limit products to 300 in the product drop down

= 1.0.3 =
* Fix: Product rates not working correctly in WooCommerce

= 1.0.2 =
* Fix: Email tags were empty in affiliate administration email
* Fix: Affiliates were registered as "pending" even though affiliate approval was disabled


= 1.0.1 =
* Fix: Display issues on the edit affiliate page

= 1.0 =
* Initial release
