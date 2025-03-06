<?php

// Make API GET request
function fraudchecker_get_data($phone_number) {
    $api_url = "https://www.fraudchecker.org/api/check?phone=" . urlencode($phone_number);

    $api_token = get_option('fraudchecker_api_token', '');
    if (empty($api_token)) {
        echo '<div class="notice notice-error"><p><strong>' . esc_html__('এপিআই টোকেন দিতে হবে। অনুগ্রহ করে', 'fraudchecker') . 
        ' <a href="' . esc_url(admin_url('options-general.php?page=fraudchecker')) . '" style="color: blue; text-decoration: underline;">' . 
        esc_html__('FraudChecker Settings', 'fraudchecker') . '</a> ' . esc_html__('অপশনে গিয়ে টোকেন প্রদান করুন।', 'fraudchecker') . '</strong></p></div>';
        return null;
    }

    error_log('API Request URL: ' . $api_url);

    $response = wp_remote_get($api_url, [
        'headers' => [
            'Authorization' => 'Bearer ' . $api_token,
            'Accept'        => 'application/json',
        ],
        'timeout' => 10,
    ]);

    if (is_wp_error($response)) {
        error_log('API Request Error: ' . $response->get_error_message());
        return null;
    }

    $body = wp_remote_retrieve_body($response);
    error_log('API Response: ' . $body);

    return json_decode($body, true);
}