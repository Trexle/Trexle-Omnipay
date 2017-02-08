<?php
require __DIR__ . '/vendor/autoload.php';
use Omnipay\Omnipay;

$gateway = Omnipay::create('Trexle');
$gateway->setSecretKey('J5RGMpDlFlTfv9mEFvNWYoqHufyukPP4');

$formData = array('number' => '4242424242424242', 'expiryMonth' => '6', 'expiryYear' => '2018', 'cvv' => '123');
$response = $gateway->purchase(array('description' => 'test transactiongi', 'email' => 'test@email.com', 'amount' => '10.00', 'currency' => 'USD', 'card' => $formData))->send();

if ($response->isSuccessful()) {
    // payment was successful: update database
    print_r($response);
} elseif ($response->isRedirect()) {
    // redirect to offsite payment gateway
    $response->redirect();
} else {
    // payment failed: display message to customer
    echo $response->getMessage();
}
?>