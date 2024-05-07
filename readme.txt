=== Easy Key-Values ===
Contributors: Fantom3D
Donate link: http://bit.ly/EKVPaypalDonation
Email: ysalitrynskyi+wp@gmail.com
Tags: Custom Fields, Environmental Variables, API Key Management, Configuration Storage, Vault
Requires PHP: 7.4
Requires at least: 5.2
Tested up to: 6.5.3
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Effortlessly manage key-value pairs, save custom settings, and retrieve them across your WordPress site with a shortcode or PHP function.

== Description ==

"Easy Key-Values" helps you manage key-value pairs.. yes, easily.
It lets site managers and developers save settings and use them anywhere on their site with shortcode or PHP function.
Use it for storing API keys, site settings, or showing content simply and quickly. Additionally, the plugin offers the ability to hide sensitive information directly from the WordPress admin, enhancing security and privacy.

= Key Features =

- Simple Management: Easily add, edit, delete, and hide key-value pairs from the WordPress admin area.
- Shortcode Support: Display your settings anywhere on your site with a simple shortcode.
- PHP Functionality: Retrieve values in your theme or plugin with a straightforward PHP function.
- Visibility Control: Decide whether to show or hide specific key-value pairs, making it perfect for sensitive data.
- Lightweight and Efficient: Designed with performance in mind, ensuring minimal impact on your site's speed.
- Optional Caching: Improve performance with optional caching, configurable through `EKVALUES_ENABLE_CACHE` and `EKVALUES_CACHE_DURATION` constants.
- Flexible Menu Placement: Customize the admin menu placement using the `EKVALUES_MENU_LOCATION` constant for better integration.

= Perfect for a Variety of Use Cases =

- API Key Storage: Securely store and retrieve API keys for various services.
- Dynamic Content Display: Quickly show different content based on custom settings or user preferences.
- Site Customization: Easily manage and apply site-wide custom settings without touching code.
- Development Efficiency: Streamline development workflows by managing reusable data through the admin interface.
- Multi-language Support: Store and display language-specific text strings or URLs, enhancing your site's internationalization.

By using "Easy Key-Values", you unlock the potential for enhanced site customization, improved security for sensitive data, and a more efficient development process.

= Help with Localization =

We're looking to make "Easy Key-Values" accessible to even more users by supporting additional languages. If you're fluent in a language other than English and would like to contribute by helping with translation and localization, we would greatly appreciate your support.

This effort not only helps the plugin reach a wider audience but also supports the open-source community in creating tools that are more inclusive and accessible. If you're interested in contributing, please reach out at ysalitrynskyi+wp@gmail.com or open a Pull Request.

Thank you for considering to help us improve!

== Installation ==

1. Upload the `easy-key-values` folder to the `/wp-content/plugins/` directory or install it directly through the WordPress admin panel.
2. Activate the plugin via the 'Plugins' menu in WordPress.
3. Navigate to the 'Easy Key-Values' menu item in the admin area to start adding your key-value pairs.

For inquiries or custom web development services, including a full team capable of handling design, backend, frontend, marketing, and mobile development, contact me at ysalitrynskyi+wp@gmail.com.
My team and I speak multiple languages and are committed to delivering high-quality work faster than anybody else.
Open to improvement suggestions and eager to tackle your next project, let's collaborate to create something exceptional.

== Frequently Asked Questions ==

= How do I display a value in a post or page? =
Use the shortcode `[ekvalues_value key="your_key_name"]` to display the value of a key anywhere shortcodes are supported.

= Can I retrieve a value in my theme or plugin? =
Yes, use `<?php echo ekvalues_get_value('your_key_name'); ?>` to retrieve and display a value within PHP. For hidden values, ensure proper access control is implemented.

= What are `EKVALUES_ENABLE_CACHE` and `EKVALUES_CACHE_DURATION`? =
These constants allow you to enable caching (`EKVALUES_ENABLE_CACHE`) for improved performance and customize the duration (`EKVALUES_CACHE_DURATION`) of the cache in seconds. Define these in your `wp-config.php` for advanced performance tuning.

= How can I customize the admin menu placement? =
Define the `EKVALUES_MENU_LOCATION` constant in your `wp-config.php` file. Setting it to "settings_menu" places the plugin's menu under the 'Settings' section, offering a more integrated admin interface experience.

== Screenshots ==

1. The admin interface for managing key-value pairs, including visibility control options.

== Changelog ==

= 1.0.1 =
* Test with WordPress 6.5.3

= 1.0.0 =
* Initial release
