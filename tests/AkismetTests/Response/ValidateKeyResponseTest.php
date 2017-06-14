<?php

namespace AkismetTests\Response;

use PHPUnit_Framework_TestCase as TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Mock;

class ValidateKeyResponseTest extends TestCase
{
    public function testValidateKeyResponseInvalid()
    {
        $client = new Client();

        $mock = new Mock();
        $mock->addResponse(__DIR__.'/raw/validatekey-invalid.txt');

        $client->getEmitter()->attach($mock);
        $response = $client->get();

        $this->assertTrue($response->hasHeader("X-akismet-server"));

        $this->assertEquals('invalid', trim($response->getBody()));
    }

    public function testValidateKeyResponseValid()
    {
        $client = new Client();

        $mock = new Mock();
        $mock->addResponse(__DIR__.'/raw/validatekey-valid.txt');

        $client->getEmitter()->attach($mock);
        $response = $client->get();

        $this->assertTrue($response->hasHeader("X-akismet-server"));

        $this->assertEquals('valid', trim($response->getBody()));
    }

    public function testValidateKeyResponseMissing()
    {
        $client = new Client();

        $mock = new Mock();
        $mock->addResponse(__DIR__.'/raw/validatekey-invalid-missing1.txt');

        $client->getEmitter()->attach($mock);
        $response = $client->get();

        $this->assertTrue($response->hasHeader("X-akismet-server"));
        $this->assertTrue($response->hasHeader("X-akismet-debug-help"));

        $this->assertEquals('Empty "blog" value', $response->getHeader("X-akismet-debug-help"));

        $this->assertEquals('invalid', trim($response->getBody()));
    }

    public function testValidateKeyResponseMissing2()
    {
        $client = new Client();
        
        $mock = new Mock();
        $mock->addResponse(__DIR__.'/raw/validatekey-invalid-missing2.txt');

        $client->getEmitter()->attach($mock);
        $response = $client->get();

        $this->assertTrue($response->hasHeader("X-akismet-server"));
        $this->assertTrue($response->hasHeader("X-akismet-debug-help"));

        $this->assertEquals('Empty "key" value', $response->getHeader("X-akismet-debug-help"));

        $this->assertEquals('invalid', trim($response->getBody()));
    }
}
