<?php

namespace AkismetTests\Response;

use PHPUnit_Framework_TestCase as TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Mock;

class IsSpamResponseTest extends TestCase 
{
	public function testIsSpamResponseFalse()
	{
		$client = new Client();

		$mock = new Mock();
		$mock->addResponse(__DIR__.'/raw/isspam-false.txt');

		$client->getEmitter()->attach($mock);
		$response = $client->get();

		$this->assertTrue($response->hasHeader("X-akismet-server"));
		$this->assertTrue($response->hasHeader("X-akismet-guid"));

		$this->assertEquals('false', trim($response->getBody()));
	}

	public function testIsSpamResponseSuccessful()
	{
		$client = new Client();

		$mock = new Mock();
		$mock->addResponse(__DIR__.'/raw/isspam-false.txt');

		$client->getEmitter()->attach($mock);
		$response = $client->get();

		$this->assertEquals(200, $response->getStatusCode());
	}

	public function testIsSpamResponseTrue()
	{
		$client = new Client();

		$mock = new Mock();
		$mock->addResponse(__DIR__.'/raw/isspam-true.txt');

		$client->getEmitter()->attach($mock);
		$response = $client->get();

		$this->assertTrue($response->hasHeader("X-akismet-server"));
		$this->assertTrue($response->hasHeader("X-akismet-guid"));

		$this->assertEquals('true', trim($response->getBody()));
	}

	public function testIsSpamResponseMissingRequiredValues()
	{
		$client = new Client();

		$mock = new Mock();
		$mock->addResponse(__DIR__.'/raw/isspam-missing.txt');

		$client->getEmitter()->attach($mock);
		$response = $client->get();

		$this->assertTrue($response->hasHeader("X-akismet-server"));
		$this->assertTrue($response->hasHeader("X-akismet-debug-help"));

		$this->assertSame('Empty "user_ip" value', $response->getHeader('X-akismet-debug-help'));

		$this->assertEquals('Missing required field: user_ip.', trim($response->getBody()));
	}
}