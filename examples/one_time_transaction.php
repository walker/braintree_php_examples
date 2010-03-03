<?php
require_once '../_environment.php';

function braintree_text_field($label, $param, $result) {
  echo('<div>' . $label . '</div>');
  $name = $param[0];
  foreach(array_slice($param, 1) as $piece) {
    $name = $name . '[' . $piece . ']';
  }
  $errorMessages = '';
  $fieldValue = '';
  if (isset($result)) {
    $errors = $result->errors;
    $params = $result->params;
    foreach(array_slice($param, 0, -1) as $key) {
      $errors = $errors->forKey(Braintree_Util::delimiterToCamelCase($key));
      $params = $params[Braintree_Util::delimiterToCamelCase($key)];
    }
    $finalKey = Braintree_Util::delimiterToCamelCase(end($param));
    $fieldValue = isset($params[$finalKey]) ? $params[$finalKey] : null;
    $errors = $errors->onAttribute($finalKey);
    if (sizeof($errors) > 0) {
      foreach($errors as $error) {
        $errorMessages = $errorMessages . '<div style="color: red;">' . $error->message . '</div>';
      }
    }
  }
  echo('<div><input type="text" name="' . $name .'" value="' . $fieldValue . '" /></div>');
  echo($errorMessages);
  echo("\n");
}
?>
<html>
  <head>
    <title>Braintree Transparent Redirect PHP Example</title>
  </head>
  <body>
    <?php
      if (isset($_GET["id"])) {
        $result = Braintree_Transaction::createFromTransparentRedirect($_SERVER['QUERY_STRING']);
      }
      if (isset($result) && $result->success) {
        ?>
          <h1>Braintree Transparent Redirect Response</h1>
          <?php        
            $transaction = $result->transaction;
          ?>
          <table>
            <tr><td>transaction id</td><td><?php echo htmlentities($transaction->id); ?></td></tr>
            <tr><td>transaction status</td><td><?php echo htmlentities($transaction->status); ?></td></tr>
            <tr><td>transaction amount</td><td><?php echo htmlentities($transaction->amount); ?></td></tr>
            <tr><td>customer first name</td><td><?php echo htmlentities($transaction->customerDetails->firstName); ?></td></tr>
            <tr><td>customer last name</td><td><?php echo htmlentities($transaction->customerDetails->lastName); ?></td></tr>
            <tr><td>customer email</td><td><?php echo htmlentities($transaction->customerDetails->email); ?></td></tr>
            <tr><td>credit card number</td><td><?php echo htmlentities($transaction->creditCardDetails->maskedNumber); ?></td></tr>
            <tr><td>expiration date</td><td><?php echo htmlentities($transaction->creditCardDetails->expirationDate); ?></td></tr>
          </table>
        <?php
    } else {
      if (!isset($result)) { $result = null; }
      ?>
      <h1>Braintree Transparent Redirect PHP Example</h1>
      <form method="POST" action="<?php echo Braintree_Transaction::createTransactionUrl() ?>" autocomplete="off">
        <h2>Customer Information</h2>
        <?php braintree_text_field('First Name', array('transaction', 'customer', 'first_name'), $result); ?>
        <?php braintree_text_field('Last Name', array('transaction', 'customer', 'last_name'), $result); ?>
        <?php braintree_text_field('Email', array('transaction', 'customer', 'email'), $result); ?>
  
        <h2>Payment Information</h2>
        <?php braintree_text_field('Credit Card Number', array('transaction', 'credit_card', 'number'), $result); ?>
        <?php braintree_text_field('Expiration Date (MM/YY)', array('transaction', 'credit_card', 'expiration_date'), $result); ?>
        <?php braintree_text_field('CVV', array('transaction', 'credit_card', 'cvv'), $result); ?>
    
        <?php $tr_data = Braintree_TransparentRedirect::transactionData(
          array('redirectUrl' => "http://" . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"],
                'transaction' => array('amount' => '10.00', 'type' => 'sale'))) ?>
        <input type="hidden" name="tr_data" value="<?php echo $tr_data ?>" />

        <br />
        <input type="submit" value="Submit" />
      </form>
    <?php } ?>
  </body>
</html>
