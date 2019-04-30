<?php
/**
 * This file is part of the PHPEthereumTools package
 *
 * PHP Version 7.1
 * 
 * @category PHPEthereumTools
 * @package  PHPEthereumTools
 * @author   Micky Socaci <micky@nowlive.ro>
 * @license  https://github.com/mickys/php-eth-tools/blob/master/LICENSE.md MIT
 * @link     https://github.com/mickys/php-eth-tools/
 */
namespace Tests;

use \EthereumTools\Utils;

/**
 * This file is part of the PHPEthereumTools package
 *
 * PHP Version 7.1
 * 
 * @category PHPEthereumTools
 * @package  PHPEthereumTools
 * @author   Micky Socaci <micky@nowlive.ro>
 * @license  https://github.com/mickys/php-eth-tools/blob/master/LICENSE.md MIT
 * @link     https://github.com/mickys/php-eth-tools/
 */
class UtilsTest extends BaseTest
{
    /**
     * Test create a new ethereum private key
     * 
     * @return array
     */
    function testGenerateANewEthereumPrivateKey() 
    {
        $privateKey = Utils::generateNewPrivateKey();
        $publicKey = Utils::privateKeyToPublicKey($privateKey);
        $addressFromPrivate = Utils::privateKeyToAddress($privateKey);

        echo "  Private Key: ".$privateKey."\n";
        echo "  Public Key:  ".$publicKey."\n";
        echo "  Address:     ".$addressFromPrivate."\n\n";

        $this->assertTrue(true);

        return [ "key" => $privateKey, "address" => $addressFromPrivate ];
    }

    /**
     * Test recovery
     * 
     * @return void
     */
    function testValidateJavascriptSignedMessage()
    {
        $message = "Hello!!";

        $fromPrivateKey = "0xDFA6394B4E5779B3130F5E6ED215B015D67E1346BB2DC99C93D217E2A751E762";
        $fromAddress    = "0x1c4476b864c4a848374a65ce2c09efd56163fafa";
        $singedMessage  = "0x45af4b8d46a208dec17c90883a33c74e8c08c75e122fd64c52a2b24fff02108c2992ae257bbbbbd28fdb3478a2f2d864ebcdda6be26a1fbeedc449fb11db10441c";

        $signed = Utils::personalSign($fromPrivateKey, $message);
        
        $this->assertTrue($signed == $singedMessage);

        $recoveredAddress  = Utils::personalEcRecover($message, $signed);
        $recoveredAddress2 = Utils::personalEcRecover($message, $singedMessage);

        $this->assertTrue($fromAddress == $recoveredAddress);
        $this->assertTrue($recoveredAddress == $recoveredAddress2);
    }

    /**
     * Test create a new ethereum private key
     * 
     * @param array $ethkeys Ethereum Private Key
     * 
     * @depends testGenerateANewEthereumPrivateKey
     * 
     * @return void
     */
    function testSignMessageAndRecoverSignerSuccessfully($ethkeys) 
    {
        $message = "Hello world!";
        $signed = Utils::personalSign($ethkeys["key"], $message);
        $recoveredAddress  = Utils::personalEcRecover($message, $signed);
        $this->assertTrue($recoveredAddress == $ethkeys["address"]);
    }
}
