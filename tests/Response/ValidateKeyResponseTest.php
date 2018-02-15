<?php

namespace nickurt\Akismet\Tests\Response;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

class ValidateKeyResponseTest extends TestCase
{
    public function testValidateKeyResponseInvalid()
    {
        $mock = new MockHandler([
            new Response(200, [
                'X-akismet-server' => '10.2.21.107',
            ], 'invalid'),
            new Response(202, ['Content-Length' => 7]),
            new RequestException("Error Communicating with Server", new Request('GET', 'test'))
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $response = $client->request('GET', '/');

        $this->assertTrue($response->hasHeader("X-akismet-server"));

        $this->assertEquals('invalid', trim($response->getBody()));
    }

    public function testValidateKeyResponseValid()
    {
        $mock = new MockHandler([
            new Response(200, [
                'X-akismet-server' => '10.2.21.107',
            ], 'valid'),
            new Response(202, ['Content-Length' => 7]),
            new RequestException("Error Communicating with Server", new Request('GET', 'test'))
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $response = $client->request('GET', '/');

        $this->assertTrue($response->hasHeader("X-akismet-server"));

        $this->assertEquals('valid', trim($response->getBody()));
    }

    public function testValidateKeyResponseMissing()
    {
        $mock = new MockHandler([
            new Response(200, [
                'X-akismet-server' => '10.2.21.107',
                'X-akismet-debug-help' => 'Empty "blog" value'
            ], 'invalid'),
            new Response(202, ['Content-Length' => 7]),
            new RequestException("Error Communicating with Server", new Request('GET', 'test'))
        ]);

        $handler = HandlerStack::create($mock);

        $client = new Client(['handler' => $handler]);

        $response = $client->request('GET', '/');

        $this->assertTrue($response->hasHeader("X-akismet-server"));
        $this->assertTrue($response->hasHeader("X-akismet-debug-help"));

        $this->assertEquals('Empty "blog" value', $response->getHeader("X-akismet-debug-help")[0]);

        $this->assertEquals('invalid', trim($response->getBody()));
    }

    public function testValidateKeyResponseMissing2()
    {
        $mock = new MockHandler([
            new Response(200, [
                'X-akismet-server' => '10.2.21.90',
                'X-akismet-debug-help' => 'Empty "key" value'
            ], 'invalid'),
            new Response(202, ['Content-Length' => 7]),
            new RequestException("Error Communicating with Server", new Request('GET', 'test'))
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $response = $client->request('GET', '/');

        $this->assertTrue($response->hasHeader("X-akismet-server"));
        $this->assertTrue($response->hasHeader("X-akismet-debug-help"));

        $this->assertEquals('Empty "key" value', $response->getHeader("X-akismet-debug-help")[0]);

        $this->assertEquals('invalid', trim($response->getBody()));
    }
}
