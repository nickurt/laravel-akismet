<?php

namespace nickurt\Akismet\Tests\Response;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

class IsSpamResponseTest extends TestCase
{
    public function testIsSpamResponseFalse()
    {
        $mock = new MockHandler([
            new Response(200, [
                'X-akismet-server' => '10.2.21.114',
                'X-akismet-guid' => 'bf40a4e4ceb1833111fae80717ae6bc0'
            ], 'false'),
            new Response(202, ['Content-Length' => 5]),
            new RequestException("Error Communicating with Server", new Request('GET', 'test'))
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $response = $client->request('GET', '/');

        $this->assertTrue($response->hasHeader('X-akismet-server'));
        $this->assertTrue($response->hasHeader('X-akismet-guid'));

        $this->assertEquals('false', trim($response->getBody()));
    }

    public function testIsSpamResponseSuccessful()
    {
        $mock = new MockHandler([
            new Response(200, [
                'X-akismet-server' => '10.2.21.114',
                'X-akismet-guid' => 'bf40a4e4ceb1833111fae80717ae6bc0'
            ], 'false'),
            new Response(202, ['Content-Length' => 5]),
            new RequestException("Error Communicating with Server", new Request('GET', 'test'))
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $response = $client->request('GET', '/');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testIsSpamResponseTrue()
    {
        $mock = new MockHandler([
            new Response(200, [
                'X-akismet-server' => '10.2.21.116',
                'X-akismet-guid' => '5b47eb14befa91e4941f93d03859e34d'
            ], 'true'),
            new Response(202, ['Content-Length' => 4]),
            new RequestException("Error Communicating with Server", new Request('GET', 'test'))
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $response = $client->request('GET', '/');

        $this->assertTrue($response->hasHeader('X-akismet-server'));
        $this->assertTrue($response->hasHeader('X-akismet-guid'));

        $this->assertEquals('true', trim($response->getBody()));
    }

    public function testIsSpamResponseMissingRequiredValues()
    {
        $mock = new MockHandler([
            new Response(200, [
                'X-akismet-server' => '10.2.21.109',
                'X-akismet-debug-help' => 'Empty "user_ip" value'
            ], 'Missing required field: user_ip.'),
            new Response(202, ['Content-Length' => 32]),
            new RequestException("Error Communicating with Server", new Request('GET', 'test'))
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $response = $client->request('GET', '/');

        $this->assertTrue($response->hasHeader('X-akismet-server'));
        $this->assertTrue($response->hasHeader('X-akismet-debug-help'));

        $this->assertEquals('Missing required field: user_ip.', trim($response->getBody()));
    }
}
