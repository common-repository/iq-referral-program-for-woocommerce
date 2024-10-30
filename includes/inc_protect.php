<?php
if(!defined("IQ_RPW_ACCESS")) { die(); }

function func_iq_rpw_out_protect($str, $secret = IQ_RPW_SECRET_CODE) {
	$ciphering = "AES-128-ECB";
	$decryption = openssl_decrypt(
					base64_encode(hex2bin($str)), 
					$ciphering,
					substr(openssl_digest( $secret, 'sha512'), 0, 16)
				);
			
	return $decryption;
}

function func_iq_rpw_in_protect($str, $secret, $IsSQL = false) {
	if($IsSQL) {
		if(empty($str)) {
			return '\'\'';
		}
		$szCrypt = "HEX(AES_ENCRYPT( '".$str."', SUBSTR(SHA2('".$secret."', 512), 1, 16)))";
	} else {
		if(empty($str)) {
			return false;
		}
		$ciphering = "AES-128-ECB";
		$szCrypt = strtoupper(bin2hex(base64_decode(
			openssl_encrypt(
				$str, 
				$ciphering, 
				substr(openssl_digest( $secret, 'sha512'), 0, 16)
			)
		)));
	}
	return $szCrypt;
}