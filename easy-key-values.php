<?php
/**
 * Plugin Name: Easy Key-Values
 * Description: Manage key-value pairs. Save custom settings and retrieve using a shortcode or PHP function.
 * Version: 1.0.0
 * Author: Yevhen Salitrynskyi
 * Author's Email: ysalitrynskyi+wp@gmail.com
 * Author's LinkedIn: https://www.linkedin.com/in/yevhen-salitrynskyi/
 * Text Domain: easy-key-values
 * Domain Path: /languages
 * Requires at least: 5.2
 * Requires PHP: 7.4
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

require_once ('functions/settings-management.php');
require_once ('functions/utility-functions.php');

add_action('admin_init', 'ekv_register_settings');
function ekv_register_settings() {
    register_setting('ekv_options_group', 'ekv_options', 'ekv_options_validate');
    add_settings_section('ekv_main_section', '', 'ekv_section_text', 'ekv_settings');
    add_settings_field('ekv_key_value', __('Your Keys & Values', 'easy-key-values'), 'ekv_key_value_setting', 'ekv_settings', 'ekv_main_section');
}

register_activation_hook(__FILE__, 'ekv_add_admin_capabilities');
function ekv_add_admin_capabilities() {
    $admin_role = get_role('administrator');
    if ($admin_role instanceof WP_Role && !$admin_role->has_cap('easy_key_values'))
        $admin_role->add_cap('easy_key_values');
}

add_action('admin_menu', 'ekv_add_to_menu');
function ekv_add_to_menu() {
    if (defined('EKV_MENU_LOCATION') && EKV_MENU_LOCATION === 'settings_menu') {
        add_options_page(__('Easy Key-Values', 'easy-key-values'), __('Easy Key-Values', 'easy-key-values'), 'easy_key_values', 'ekv_settings', 'ekv_settings_page');
    } else {
        add_menu_page(__('Easy Key-Values', 'easy-key-values'), __('Easy Key-Values', 'easy-key-values'), 'easy_key_values', 'ekv_settings', 'ekv_settings_page', 'dashicons-admin-network');
    }
}

add_filter('plugin_action_links_easy-key-values/easy-key-values.php', 'ekv_add_action_links');
function ekv_add_action_links($links) {
    $settings_link = '<a href="' . admin_url('admin.php?page=ekv_settings') . '">Settings</a>';
    $support_link = '<a href="https://github.com/ysalitrynskyi/easy-key-values/issues" target="_blank">Support</a>';
    array_unshift($links, $settings_link, $support_link);
    return $links;
}

add_filter('plugin_row_meta', 'ekv_add_plugin_meta_links', 10, 2);
function ekv_add_plugin_meta_links($links, $file) {
    $plugin_basename = plugin_basename(__DIR__) . '/easy-key-values.php';
    if ($file == $plugin_basename) {
        $links[] = '<a href="https://www.linkedin.com/in/yevhen-salitrynskyi/" target="_blank">Hire the Author!</a>';
        $links[] = '<a href="https://github.com/ysalitrynskyi/easy-key-values/" target="_blank">GitHub</a>';
        if (get_locale() === 'uk') {
            $links[] = '<a href="http://bit.ly/EKVPaypalDonation" target="_blank">Підтримати 🙏</a>';
            $links[] = '<a href="https://savelife.in.ua/donate/" target="_blank">Донат на ЗСУ 🇺🇦</a>';
        } else {
            $links[] = '<a href="http://bit.ly/EKVPaypalDonation" target="_blank">Donate 🙏</a>';
            $links[] = '<a href="https://savelife.in.ua/en/donate-en/" target="_blank">Support Ukraine! 🇺🇦</a>';
        }
    }
    return $links;
}

add_action('admin_head', 'ekv_admin_menu_icon_style');
function ekv_admin_menu_icon_style() {
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    WP_Filesystem();
    global $wp_filesystem;

    $icon_path = plugin_dir_path(__FILE__) . 'images/icon.svg';
    $icon_content = $wp_filesystem->get_contents($icon_path);
    $icon_base64 = base64_encode($icon_content);
    ?>
    <style>
        #toplevel_page_ekv_settings .wp-menu-image {
            background: url('data:image/svg+xml;base64,<?php echo esc_attr($icon_base64); ?>') no-repeat center center;
            background-size: 24px 24px;
        }
        #toplevel_page_ekv_settings .wp-menu-image:before {
            content: '';
        }
    </style>
    <?php
}

add_action('admin_enqueue_scripts', 'ekv_enqueue_scripts');
function ekv_enqueue_scripts($hook) {
    if (!str_contains($hook, 'ekv_settings')) {
        return;
    }
    wp_enqueue_script('ekv-ajax', plugin_dir_url(__FILE__) . 'js/ekv-ajax.js', array('jquery'), '1.0.0', true);
    wp_localize_script('ekv-ajax', 'ekvLang', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ekv_nonce'),
        'confirmRemove' => __('Are you sure you want to remove this field?', 'easy-key-values'),
        'confirmRemoveSure' => __('Are you absolutely sure? This action cannot be undone (after you click "Save")', 'easy-key-values'),
        'errorEmptyKey' => __('Error: You cannot have a value without a corresponding key.', 'easy-key-values'),
        'optionsSaved' => __('Options saved (Refresh is not needed)', 'easy-key-values'),
        'saveFailed' => __('Failed to save options!', 'easy-key-values'),
        'serverError' => __('Error: Could not connect to server.', 'easy-key-values'),
        'unsavedChanges' => __('You have unsaved changes! Are you sure you want to leave?', 'easy-key-values'),
        'saveChanges' => __('Save Changes', 'easy-key-values'),
        'saving' => __('Saving...', 'easy-key-values'),
        'errorDuplicateKey' => __('Error: Duplicate key detected.', 'easy-key-values'),
    ));
}

add_action('admin_enqueue_scripts', 'ekv_enqueue_admin_styles');
function ekv_enqueue_admin_styles($hook) {
    if (strpos($hook, 'ekv_settings') !== false) {
        wp_enqueue_style('ekv-admin-styles', plugins_url('css/ekv-styles.css', __FILE__), [], '1.0.0');
    }
}

add_action('plugins_loaded', 'ekv_load_plugin_textdomain');
function ekv_load_plugin_textdomain() {
    load_plugin_textdomain('easy-key-values', false, basename(dirname(__FILE__)) . '/languages/');
}

add_shortcode('ekv_value', 'ekv_get_value_shortcode');
function ekv_get_value_shortcode($atts) {
    $key = sanitize_text_field($atts['key']);
    $value = ekv_get_value($key);
    return $value !== null ? esc_html($value) : '';
}
