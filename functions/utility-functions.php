<?php
function ekv_get_value($key, $bypass_cache = false) {
    $is_cache_enabled = defined('EKV_ENABLE_CACHE') ? EKV_ENABLE_CACHE : false;
    $cache_duration = defined('EKV_CACHE_DURATION') ? EKV_CACHE_DURATION : 15;
    $cache_key = 'ekv_value_' . md5($key);
    $found = false;

    if ($is_cache_enabled && wp_using_ext_object_cache() && !$bypass_cache) {
        $cached_value = wp_cache_get($cache_key, 'ekv_values', false, $found);
        if ($found) {
            return $cached_value;
        }
    }

    global $sorted_options;
    if ($sorted_options === null) {
        $sorted_options = get_option('ekv_options', []);
    }

    $value = binarySearch($sorted_options, $key);

    if ($is_cache_enabled && wp_using_ext_object_cache() && !$bypass_cache) {
        wp_cache_set($cache_key, $value, 'ekv_values', $cache_duration);
    }

    return $value;
}

function binarySearch($options, $key) {
    $left = 0;
    $right = count($options) - 1;

    while ($left <= $right) {
        $mid = floor(($left + $right) / 2);

        if ($options[$mid]['key'] === $key) {
            return $options[$mid]['value'];
        }

        if (strcmp($key, $options[$mid]['key']) < 0) {
            $right = $mid - 1;
        } else {
            $left = $mid + 1;
        }
    }

    return null;
}

function array_find($array, $callback) {
    foreach ($array as $key => $value) {
        if ($callback($value, $key)) {
            return $value;
        }
    }
    return false;
}
