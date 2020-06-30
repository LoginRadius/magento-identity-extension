<?php

/**
  * Encrypt and decrypt
  *
  * @param string $string string to be encrypted/decrypted
  * @param string $action what to do with this? e for encrypt, d for decrypt
  */     
  function lr_secret_encrypt_and_decrypt( $string, $secretIv, $action) {
    $secret_key = $secretIv;
    $secret_iv = $secretIv;
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash( 'sha256', $secret_key );
    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
    if( $action == 'e' ) {
    $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
    }
    else if( $action == 'd' ) {
    $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv ); 
    }   
    return $output;
  }
?>