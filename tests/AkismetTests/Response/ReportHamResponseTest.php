<?php

namespace AkismetTests\Response;

use PHPUnit_Framework_TestCase as TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Mock;

class ReportHamResponseTest extends TestCase
{
    public function testReportHamResponseValid()
    {
        $client = new Client();

        $mock = new Mock();
        $mock->addResponse(__DIR__.'/raw/reportham-valid.txt');

        $client->getEmitter()->attach($mock);
        $response = $client->get();

        $this->assertEquals('Thanks for making the web a better place.', trim($response->getBody()));
    }
}
