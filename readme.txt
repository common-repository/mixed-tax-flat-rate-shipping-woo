=== Mixed Tax Flat Rate Shipping Woo ===
Contributors: BjornTech
Requires at least: 4.9
Tested up to: 5.4
Requires PHP: 7.0
Stable tag: 1.0.3
License: GPL-3.0
License URI: bjorntech.com/flatrateshipping

Creates a new shipping method that enables a fixed price shipping that works with a basket of products with mixed taxes.

== Description ==

In some countries (Sweden) the VAT-rules stipulates that the VAT on shipping should be applied pro rata as the items included in the order.

As an example, an order could contain a book on how to learn embroidery (6% VAT) and piece of cloth to embroider on (25%).

If the book costs 100 and the cloth 200 the VAT on the shipping should be 1/3 6% and 2/3 25%

This plugin allows you to apply the above and still have a fixed shipping price.

A new shipping method with the name “Flat rate including VAT” will be visible when adding a shipping method. The shipping cost should be the cost including VAT presented to customer. 

== Changelog ==
= 1.0.3 =
* Submitted to Wordpress
= 1.0.2 =
* Fix: Rounding error
= 1.0.0 =
* First public release
