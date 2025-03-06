<?php

// Add settings menu
function fraudchecker_settings_menu() {
    add_options_page(
        __('FraudChecker Settings', 'fraudchecker'),
        __('FraudChecker', 'fraudchecker'),
        'manage_options',
        'fraudchecker',
        'fraudchecker_settings_page'
    );
}
add_action('admin_menu', 'fraudchecker_settings_menu');

// Settings page
function fraudchecker_settings_page() {
    ?>
    <div class="wrap">
        <h2><?php esc_html_e('FraudChecker Settings', 'fraudchecker'); ?></h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('fraudchecker_settings');
            do_settings_sections('fraudchecker');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register settings
function fraudchecker_register_settings() {
    register_setting('fraudchecker_settings', 'fraudchecker_api_token');
    
    add_settings_section('fraudchecker_main_section', '', null, 'fraudchecker');

    add_settings_field(
        'fraudchecker_api_token',
        __('Enter API Token*', 'fraudchecker'),
        'fraudchecker_api_token_field',
        'fraudchecker',
        'fraudchecker_main_section'
    );
}
add_action('admin_init', 'fraudchecker_register_settings');

// API Token input field
function fraudchecker_api_token_field() {
    $api_token = get_option('fraudchecker_api_token', '');
    echo "<input type='text' name='fraudchecker_api_token' value='" . esc_attr($api_token) . "' required />";
}