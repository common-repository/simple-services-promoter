=== Simple Services Promoter ===
Contributors: corporatezen222
Tags: services, products, showcase, promoter,
Requires at least: 4.0
Tested up to: 4.9
Stable tag: 1.2
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Easily showcase your services, products, or anything else anywhere on your site.

== Description ==
Easily showcase your services, products, or anything else anywhere on your site using responsive and mobile friendly layouts. All you need to do is create your services and use wordpress shortcode to display them anywhere you like.

You can add a featured image to your services, choose tags and categories, and select related pages or posts, or other services.

The shortcode has multiple options you can use to customize your display. The shortcodes you can use are as followed:

[ssp]
[ssp_grid]

[ssp] displays the entire content of the service along with the featured image and tags/categories. 

[ssp_grid] displays the services in a responsive grid layout. Depending on how many services you choose to include or create, the layout will change.

Here are the options you can change with both of these:
'posts_per_page' - this will be the number of services to display, using -1 or leaving empty will display all
'order' - can be 'ASC' or 'DESC' for ascending or descending
'order_by' - this is what to order by, such as title, post_date, ect

For example, to show 6 services ordered by newest first in a grid, use this: 
[ssp_grid posts_per_page="6" order_by="post_date" order="DESC"]


== Changelog ==
1.0
Initial Release

1.2
Bugfixes, tested with wordpress 4.9