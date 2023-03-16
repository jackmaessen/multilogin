<?php
$ciphering = "AES-128-CTR"; 
$iv_length = openssl_cipher_iv_length($ciphering);
$options = 0;
$encryption_iv = '1234567891011121';
$encryption_key = "MUL2022";

$decryption_iv = '1234567891011121';
$decryption_key = "MUL2022";

//$encryption = openssl_encrypt($string, $ciphering, $encryption_key, $options, $encryption_iv);
//$decryption = openssl_decrypt ($encryption, $ciphering, $decryption_key, $options, $decryption_iv);
?>