<?php

// Add FraudChecker meta box in WooCommerce order details page
function fraudchecker_add_meta_box() {
    add_meta_box('fraudchecker_box', 'FraudChecker Report', 'fraudchecker_display_data', 'shop_order', 'side', 'high');
}
add_action('add_meta_boxes', 'fraudchecker_add_meta_box');

// Display the fraud report in order details page
function fraudchecker_display_data($post) {
    $phone_number = get_post_meta($post->ID, '_billing_phone', true);
    if (!$phone_number) {
        echo '<p>এই অর্ডারের জন্য ফোন নম্বর পাওয়া যায়নি।</p>';
        return;
    }

    $response = fraudchecker_get_data($phone_number);

    if (!$response || $response['status'] !== 200) {
        echo "<p style='color: #0073aa; font-size: 16px; font-weight: bold; text-align: center;'>" . (isset($response['message']) ? $response['message'] : 'ফ্রড রিপোর্ট পাওয়া যায়নি।') . "</p>";
        return;
    }

    // Get the message and delivery status from the API response
    $delivery_status_message = $response['message'];

    // Check if there is no data (total parcels is 0)
    if ($response['data']['summary']['total_parcel'] == 0) {
        echo "<p style='color: #0073aa; font-size: 16px; font-weight: bold; text-align: center;'>" . $delivery_status_message . "</p>";
        return;
    }

    // Table headers
    echo "<table style='width:100%; border-collapse: collapse; margin-top: 10px;'>
            <thead>
                <tr style='background: #0073aa; color: #fff; text-align: center;'>
                    <th style='padding: 5px; border: 1px solid #ddd;'>কুরিয়ার</th>
                    <th style='padding: 5px; border: 1px solid #ddd;'>অর্ডার</th>
                    <th style='padding: 5px; border: 1px solid #ddd;'>ডেলিভারি</th>
                    <th style='padding: 5px; border: 1px solid #ddd;'>বাতিল</th>
                </tr>
            </thead>
            <tbody>";

    $total_orders = $total_delivered = $total_canceled = 0;

    // Loop through data and generate table rows
    foreach ($response['data'] as $courier => $details) {
        if ($courier === 'summary') continue;

        $failed_parcel = $details['total_parcel'] - $details['success_parcel'];
        $total_orders += $details['total_parcel'];
        $total_delivered += $details['success_parcel'];
        $total_canceled += $failed_parcel;

        echo "<tr style='text-align: center;'>
                <td style='padding: 5px; border: 1px solid #ddd;'>" . ucfirst($courier) . "</td>
                <td style='padding: 5px; border: 1px solid #ddd;'>{$details['total_parcel']}</td>
                <td style='padding: 5px; border: 1px solid #ddd;'>{$details['success_parcel']}</td>
                <td style='padding: 5px; border: 1px solid #ddd; color: red;'>{$failed_parcel}</td>
              </tr>";
    }

    // Total row
    echo "<tr style='text-align: center; font-weight: bold; background: #f2f2f2;'>
            <td style='padding: 5px; border: 1px solid #ddd;'>মোট</td>
            <td style='padding: 5px; border: 1px solid #ddd;'>{$total_orders}</td>
            <td style='padding: 5px; border: 1px solid #ddd;'>{$total_delivered}</td>
            <td style='padding: 5px; border: 1px solid #ddd; color: red;'>{$total_canceled}</td>
          </tr>
        </tbody>
    </table>";

    // Success ratio section
    echo "<div style='margin-top: 15px; text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;'>
            <p style='font-size: 14px; color: #555; margin: 0;'>
                সফল ডেলিভারির অনুপাতঃ <span style='font-size: 20px; font-weight: bold; color: #007bff;'>{$response['data']['summary']['success_ratio']}%</span>
            </p>
            <p style='font-size: 16px; font-weight: bold; color: " . ($response['data']['summary']['success_ratio'] >= 90 ? 'green' : ($response['data']['summary']['success_ratio'] >= 50 ? 'orange' : 'red')) . "; margin: 0;'>{$delivery_status_message}</p>
          </div>";
    
	// Add notice
    echo "<p style='font-size: 12px; color: #888; text-align: center; margin-top: 10px;'>
        কুরিয়ারের API'র সীমাবদ্ধতার কারণে মাঝে মাঝে তথ্যের অমিল হতে পারে। সন্দেহ হলে দয়া করে আবার চেক করে নিশ্চিত হবেন। ⚠️
          </p>";
}