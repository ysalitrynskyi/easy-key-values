<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function ekvalues_section_text() {
    echo '<span></span>';
}

function ekvalues_settings_page() {
    ?>
    <div class="ekvalues-wrap-settings" style="display: flex; justify-content: space-between;">
        <div style="flex-basis: 50%;">
            <h2><?php echo esc_html__('Easy Key-Values', 'easy-key-values'); ?></h2>
            <form id="ekvalues-form" method="post" action="options.php">
                <?php
                settings_fields('ekvalues_options_group');
                do_settings_sections('ekvalues_settings');
                submit_button();
                ?>
            </form>
        </div>
        <div style="flex-basis: 50%; padding-left: 10px; padding-right: 10px; box-sizing: border-box;" class="ekvalues-quick-guide">
            <h3><?php esc_html_e('Quick Guide', 'easy-key-values'); ?></h3>
            <p><strong><?php esc_html_e('Shortcode:', 'easy-key-values'); ?></strong> <?php esc_html_e('Easily display any setting with', 'easy-key-values'); ?> <code>[ekvalues_value key="your_key"]</code>.</p>
            <p><strong><?php esc_html_e('PHP:', 'easy-key-values'); ?></strong> <?php esc_html_e('Fetch settings directly in PHP:', 'easy-key-values'); ?> <code>echo ekvalues_get_value('your_key');</code>.</p>
            <hr>
            <h3>🚀 <?php esc_html_e('Need More Than a Plugin?', 'easy-key-values'); ?></h3>
            <p>
                <?php esc_html_e("Love Easy Key-Values? It's just a taste of what we can do. From WordPress, Django and Laravel to Cloud Infrastructure, my team and I are multilingual and dedicated to delivering top-notch work at lightning speed. Let's bring your ideas to life! Email me", 'easy-key-values'); ?> <a href="mailto:ysalitrynskyi+wp@gmail.com" onclick="disableBeforeUnloadTemporarily();"><?php esc_html_e('here', 'easy-key-values'); ?></a>.
            </p>
            <p>
                <strong class="ekvalues-ft-14"><?php esc_html_e('Sometimes I answer within 5 minutes!', 'easy-key-values'); ?></strong>
            </p>
            <p>
                <button class="ekvalues-big-button" onclick="disableBeforeUnloadTemporarily(); window.location.href='mailto:ysalitrynskyi+wp@gmail.com';"><?php esc_html_e('Give it a try!', 'easy-key-values');?></button>
            </p>
            <p><?php esc_html_e("Got feedback or ideas? We're all ears. Help us improve, and let's create the perfect solution for you.", 'easy-key-values'); ?></p>
            <div class="ekvalues-donate">
                <form action="https://www.paypal.com/donate" method="post" target="_blank">
                    <input type="hidden" name="business" value="CFSSUM5YGPRME" />
                    <input type="hidden" name="no_recurring" value="0" />
                    <input type="hidden" name="item_name" value="Thank you for supporting Easy Key-Values and independent developers!" />
                    <input type="hidden" name="currency_code" value="USD" />
                    <input type="image" src="<?php echo esc_url(EASY_KEY_VALUES_URL . 'images/btn_donateCC_LG.gif')?>" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" />
                </form>
            </div>
        </div>
    </div>
    <?php
}

function ekvalues_key_value_setting() {
    $options = get_option('ekvalues_options');
    echo '<tr valign="top"><td class="ekvalues-wrap"><div id="ekvalues-pairs">';
    if (empty($options)) {
        echo "<p>" . esc_html__('No saved values yet. Use the "Add New Pair" button to get started.', 'easy-key-values') . "</p>";
    } else {
        foreach ($options as $index => $pair) {
            if (is_array($pair) && isset($pair['key'], $pair['value'])) {
                $visibility = isset($pair['visibility']) ? $pair['visibility'] : '1';
                echo "<div class='ekvalues-pair'>";
                echo "<span class='ekvalues-change-indicator'>✅</span>";
                echo "<input name='ekvalues_options[" . esc_attr($index) . "][key]' size='20' type='text' value='" . esc_attr($pair['key']) . "' ".(!$visibility ? 'disabled' : '').">";
                echo "<textarea name='ekvalues_options[" . esc_attr($index) . "][value]' rows='1' " . (!$visibility ? 'disabled' : '') . ">" . esc_textarea($visibility ? $pair['value'] : '******************') . "</textarea>";
                echo "<button class='button dashicons dashicons-".($visibility ? 'visibility ekvalues-toggle-visibility' : 'hidden ekvalues-toggle-visibility-disabled disabled')." ekvalues-toggle-visibility' type='button'>️</button>";
                echo "<input type='hidden' name='ekvalues_options[" . esc_attr($index) . "][visibility]' value='" . esc_attr($visibility) . "' data-original-value='" . esc_attr($visibility) . "'>";
                echo "<button class='button ekvalues-remove-pair' type='button'>X</button>";
                echo "</div>";
            }
        }
    }
    echo '</div><button id="ekvalues-add-pair" class="button" type="button">' . esc_html__('Add New Pair', 'easy-key-values') . '</button></td></tr>';
}

function ekvalues_options_validate($input) {
    $new_input = array();

    if (is_string($input)) {
        parse_str($input, $parsed);
        $options = $parsed['ekvalues_options'] ?? [];
    } else {
        $options = isset($input[0]['key']) ? $input : ($input['ekvalues_options'] ?? []);
    }

    foreach ($options as $pair) {
        if (!empty($pair['key']) && isset($pair['visibility'])) {
            $sanitized_key = sanitize_text_field($pair['key']);
            $sanitized_value = sanitize_text_field($pair['value']);
            $visibility = sanitize_text_field($pair['visibility']);
            $new_input[] = array('key' => $sanitized_key, 'value' => $sanitized_value, 'visibility' => $visibility);
        }
    }
    return $new_input;
}

add_action('wp_ajax_ekvalues_save_options', 'ekvalues_save_options');
function ekvalues_save_options() {
    check_ajax_referer('ekvalues_nonce', 'nonce');
    if (!current_user_can('easy_key_values')) {
        wp_die('Unauthorized user');
    }

    $existing_options = get_option('ekvalues_options', []);
    $sanitized_options = ekvalues_options_validate($_POST['options'] ?? '');
    foreach ($sanitized_options as &$pair) {
        $existing_pair = ekvalues_array_find($existing_options, function($ep) use ($pair) {
            return $ep['key'] === $pair['key'];
        });
        if ($existing_pair && ($pair['value'] === '******************' || ($pair['value'] === '' && isset($pair['visibility']) && $pair['visibility'] == '0'))) {
            $pair['value'] = $existing_pair['value'];
        }
    }
    unset($pair);

    usort($sanitized_options, function($a, $b) {
        return strcmp($a['key'], $b['key']);
    });

    update_option('ekvalues_options', $sanitized_options);
    wp_send_json_success($sanitized_options);
}
