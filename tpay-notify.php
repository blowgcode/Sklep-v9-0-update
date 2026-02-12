<?php
// tpay-notify.php - stable webhook endpoint for Tpay notifications
// Must output only TRUE/FALSE and terminate quickly.

@ini_set('display_errors', '0');
@ini_set('html_errors', '0');
error_reporting(0);

// Only accept POST
if (!isset($_SERVER['REQUEST_METHOD']) || strtoupper((string)$_SERVER['REQUEST_METHOD']) !== 'POST') {
    header('Content-Type: text/plain; charset=UTF-8');
    http_response_code(200);
    echo 'TRUE';
    exit;
}

// Ensure CHBS handler recognizes this as a notification even if headers are stripped.
$_REQUEST['action'] = 'payment_tpay';
$_GET['action'] = 'payment_tpay';

// Bootstrap WordPress
define('WP_USE_THEMES', false);
require_once __DIR__ . '/wp-load.php';

// Execute CHBS Tpay webhook handler (it will echo TRUE/FALSE and exit)
if (class_exists('CHBSPaymentTpay')) {
    $handler = new CHBSPaymentTpay();
    $handler->receivePayment();
}

// Fallback (should not happen if plugin is active)
header('Content-Type: text/plain; charset=UTF-8');
http_response_code(200);
echo 'FALSE';
exit;
