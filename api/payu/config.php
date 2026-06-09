<?php
// PayU Configuration File
// Save as: /api/payu/config.php


if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
} 
// If Composer not available, try manual installation
elseif (file_exists(__DIR__ . '/openpayu/lib/openpayu.php')) {
    require_once __DIR__ . '/openpayu/lib/openpayu.php';
} 
else {
    die('PayU SDK not found! Please install it using: composer require openpayu/openpayu OR download manually from GitHub');
}

use OpenPayU_Configuration;

// SANDBOX CONFIGURATION (for testing)
// After testing, switch to production credentials
OpenPayU_Configuration::setEnvironment('sandbox'); // Change to 'secure' for production

// Sandbox credentials (public test account)
OpenPayU_Configuration::setMerchantPosId('501455');
OpenPayU_Configuration::setSignatureKey('886657ed5d87ef11ea23c88758f81c84');
OpenPayU_Configuration::setOauthClientId('501455');
OpenPayU_Configuration::setOauthClientSecret('22d159578ee6cae69e9e533606ecd460');

// PRODUCTION CONFIGURATION (uncomment when ready for live payments)
/*
OpenPayU_Configuration::setEnvironment('secure');
OpenPayU_Configuration::setMerchantPosId('YOUR_POS_ID');
OpenPayU_Configuration::setSignatureKey('YOUR_SIGNATURE_KEY');
OpenPayU_Configuration::setOauthClientId('YOUR_CLIENT_ID');
OpenPayU_Configuration::setOauthClientSecret('YOUR_CLIENT_SECRET');
*/

//Optional: Set cache directory (recommended for security)
OpenPayU_Configuration::setOauthTokenCache(new OauthCacheFile(__DIR__ . '/cache/'));
?>