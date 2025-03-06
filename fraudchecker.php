<?php
/**
 * Plugin Name: FraudChecker for WooCommerce
 * Description: Checks customer order history via FraudChecker.org API.
 * Plugin URI: https://fraudchecker.org
 * Author: FraudChecker.org (Md Rasel Khan)
 * Author URI: https://github.com/immdraselkhan/fraudchecker-woocommerce
 * Version: 1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define Plugin Path
define('FRAUDCHECKER_PATH', plugin_dir_path(__FILE__));

// Include Necessary Files
require_once FRAUDCHECKER_PATH . 'includes/settings.php';
require_once FRAUDCHECKER_PATH . 'includes/api.php';
require_once FRAUDCHECKER_PATH . 'includes/meta-box.php';