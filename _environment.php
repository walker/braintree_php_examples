<?php
require_once('braintree-php-2.0.0/lib/Braintree.php');

Braintree_Configuration::environment('development');
Braintree_Configuration::merchantId('integration_merchant_id');
Braintree_Configuration::publicKey('integration_public_key');
Braintree_Configuration::privateKey('integration_private_key');
?>
