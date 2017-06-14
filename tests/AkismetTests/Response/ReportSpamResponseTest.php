<?php

namespace AkismetTests\Response;

use PHPUnit_Framework_TestCase as TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Mock;

class ReportSpamResponseTest extends TestCase
{
    public function testResportSpamResponseInvalid()
    {
        $client = new Client();

        $mock = new Mock();
        $mock->addResponse(__DIR__.'/raw/reportspam-invalid.txt');

        $client->getEmitter()->attach($mock);
        $response = $client->get();

        $this->assertEquals('invalid', trim($response->getBody()));
    }

    public function testResportSpamResponseValid()
    {
        $client = new Client();

        $mock = new Mock();
        $mock->addResponse(__DIR__.'/raw/reportspam-valid.txt');

        $client->getEmitter()->attach($mock);
        $response = $client->get();

        $this->assertEquals('Thanks for making the web a better place.', trim($response->getBody()));
    }
}
