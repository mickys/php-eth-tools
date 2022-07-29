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
        $fromAddress    = "0x1c4476B864c4a848374a65ce2c09eFD56163faFA";
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
        $this->assertTrue($recoveredAddress == \Web3\Utils::toChecksumAddress($ethkeys["address"]));
    }


    /**
     * Test ethers.js recovery
     * 
     * @return void
     */
    function testValidateEthersJsPersonalSignedMessageRecoveryWorksTheSame()
    {
        $fromPrivateKey = "0xDFA6394B4E5779B3130F5E6ED215B015D67E1346BB2DC99C93D217E2A751E762";
        $fromAddress    = "0x1c4476B864c4a848374a65ce2c09eFD56163faFA";

        $message = '{"website":"https://localhost:3000","address":"0x1c4476B864c4a848374a65ce2c09eFD56163faFA","timestamp":1659051676,"email":"micky@x.com"}';

        $messageHash = Utils::hashPersonalMessage($message);
        $this->assertTrue($messageHash === "0x612eee380e09119c1cb8e055ad4aaaf729d9cf7cce6d533eff1fcb52817648b5");

        $signed = Utils::personalSign($fromPrivateKey, $messageHash);
        $ethersJsSigned = "0x8b0cb075ff682741d36f904efc9258cc81d155e2f1c295ffa5a6c8144ac011ee2e573b66bfa32046f668f5b8c0e7ca8c749091316377553dfb8557bb9568bf641b";
        $this->assertTrue($signed === $ethersJsSigned);

        $recoveredAddress = Utils::personalEcRecover($messageHash, $ethersJsSigned);
        $this->assertTrue($recoveredAddress === $fromAddress);

    }

    /**
     * Test ethers.js recovery
     * 
     * @return void
     */
    function testValidateLedgerPersonalSignedMessageRecovery()
    {
        $fromAddress    = "0x566Ed72d83229d073EA9a9324b0EEC49bfA82DA4";
        $message        = '{"website":"https://localhost:3000","address":"0x566Ed72d83229d073EA9a9324b0EEC49bfA82DA4","timestamp":1659061994,"email":"micky@x.com"}';
        $messageHash    = "0x4875da822e9a95343758bbeb6f962954198bb56bf78abc667642b46e6c9bf60c";
        $signedMessage  = "0x212bcfdd870d14a8dfaab333d237407199df91c284cde09a2448c97999acf36c724731637dbb4f5eb0dbaca3be594b8267f4b106b6218218271836b3ad05e66b01";

        $hash = Utils::hashPersonalMessage($message);
        $this->assertTrue($messageHash === $hash);

        $recoveredAddress = Utils::personalEcRecover($hash, $signedMessage);
        $this->assertTrue($recoveredAddress === $fromAddress);
    }
}
