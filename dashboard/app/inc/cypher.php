<?php

/*
 * PHP mcrypt - Class to provide 2 way encryption of data
 */

class Crypt {

    private $secretkey = '@yoola';
	
    //Encrypts a string
    public function encrypt($text) {
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $data = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->secretkey, $text, MCRYPT_MODE_ECB, $iv);
        return base64_encode($data);
    }

    //Decrypts a string
    public function decrypt($text) {
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $text = base64_decode($text);
        return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->secretkey, $text, MCRYPT_MODE_ECB, $iv);
    }

    public function openSSLEncrypt($text){
        $encryptionMethod = "AES-256-CBC";
        $secretKey = "ThisIsASecretKey123";
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($encryptionMethod));

        $encryptedData = openssl_encrypt($text, $encryptionMethod, $secretKey, 0, $iv,);
        return base64_encode($encryptedData);
    }

    public function openSSLDecrypt($text){
        $decryptionMethod = "AES-256-CBC";
        $secretKey = "ThisIsASecretKey123";
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($encryptionMethod));
        $decryptedData = openssl_decrypt(base64_decode($text), $decryptionMethod, $secretKey, 0, $iv);

        
        return $decryptedData;
    }

}

// a new proCrypt instance
?>