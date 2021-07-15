<?php 

require_once '../vendor/autoload.php';

class CryptRsaUtil extends Crypt_RSA {

    /**
     * Create a signature
     *
     * @see self::verify()
     * @access public
     * @param string $digest
     * @return string
     */
    function signDigest($digest)
    {
        if (empty($this->modulus) || empty($this->exponent)) {
            return false;
        }

        switch ($this->signatureMode) {
            case CRYPT_RSA_SIGNATURE_PKCS1:
                return $this->_rsassa_pkcs1_v1_5_sign_digest($digest);
            //case CRYPT_RSA_SIGNATURE_PSS:
            default:
                return false;
        }
    } 

    function _rsassa_pkcs1_v1_5_sign_digest($m)
    {
        // EMSA-PKCS1-v1_5 encoding

        $em = $this->_emsa_pkcs1_v1_5_encode_digest($m, $this->k);
        if ($em === false) {
            user_error('RSA modulus too short');
            return false;
        }

        // RSA signature

        $m = $this->_os2ip($em);
        $s = $this->_rsasp1($m);
        $s = $this->_i2osp($s, $this->k);

        // Output the signature S

        return $s;
    }

    function _emsa_pkcs1_v1_5_encode_digest($h, $emLen)
    {

        // see http://tools.ietf.org/html/rfc3447#page-43
        switch ($this->hashName) {
            case 'md2':
                $t = pack('H*', '3020300c06082a864886f70d020205000410');
                break;
            case 'md5':
                $t = pack('H*', '3020300c06082a864886f70d020505000410');
                break;
            case 'sha1':
                $t = pack('H*', '3021300906052b0e03021a05000414');
                break;
            case 'sha256':
                $t = pack('H*', '3031300d060960864801650304020105000420');
                break;
            case 'sha384':
                $t = pack('H*', '3041300d060960864801650304020205000430');
                break;
            case 'sha512':
                $t = pack('H*', '3051300d060960864801650304020305000440');
        }
        $t.= $h;
        $tLen = strlen($t);

        if ($emLen < $tLen + 11) {
            user_error('Intended encoded message length too short');
            return false;
        }

        $ps = str_repeat(chr(0xFF), $emLen - $tLen - 3);

        $em = "\0\1$ps\0$t";

        return $em;
    }
}