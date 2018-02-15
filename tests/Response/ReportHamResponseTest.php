<?php

namespace nickurt\Akismet\Tests\Response;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

class ReportHamResponseTest extends TestCase
{
    public function testReportHamResponseValid()
    {
        $mock = new MockHandler([
            new Response(200, [], 'Thanks for making the web a better place.'),
            new Response(202, ['Content-Length' => 41]),
            new RequestException("Error Communicating with Server", new Request('GET', 'test'))
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $response = $client->request('GET', '/');

        $this->assertEquals('Thanks for making the web a better place.', trim($response->getBody()));
    }
}
