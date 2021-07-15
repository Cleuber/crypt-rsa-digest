<?php

require_once '../vendor/autoload.php';
require_once '../utils/CryptRsaUtil.php';


$rsaUtil = new CryptRsaUtil();

$hashAlg = 'sha256';
$message = "Cleuber";
$digest = hash($hashAlg, $message);

//Generate RSA Key
$rsaUtil->setPublicKeyFormat(CRYPT_RSA_PUBLIC_FORMAT_RAW);
$key = $rsaUtil->createKey(1024);


$rsaUtil->loadKey($key['privatekey']);
$rsaUtil->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
$rsaUtil->setHash($hashAlg);

$signature = base64_encode($rsaUtil->signDigest(hex2bin($hash)));

echo "Signature: ";
print_r($signature);
