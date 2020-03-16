<?php

namespace nickurt\Akismet\tests\Rules;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Event;
use nickurt\Akismet\Events\IsSpam;
use nickurt\Akismet\Facade as Akismet;
use nickurt\Akismet\Rules\AkismetRule;
use nickurt\Akismet\tests\TestCase;

class AkismetRuleTest extends TestCase
{
    /** @test */
    public function it_will_fire_is_spam_event_by_a_spam_email_via_validation_rule()
    {
        Event::fake();

        Akismet::setClient(new Client([
            'handler' => new MockHandler([
                new Response(200, [], 'valid'),
                new Response(200, [
                    "X-akismet-guid" => "33a400f82ab1df44aa75716efa99cc8c"
                ], 'true')
            ]),
        ]));

        $rule = new AkismetRule('akismet-guaranteed-spam@example.com', 'viagra-test-123');

        $this->assertFalse($rule->passes('email', 'email'));

        Event::assertDispatched(IsSpam::class, function ($e) {
            return ($e->email == 'akismet-guaranteed-spam@example.com');
        });
    }

    /** @test */
    public function it_will_not_fire_is_spam_event_by_a_spam_email_via_validation_rule()
    {
        Event::fake();

        Akismet::setClient(new Client([
            'handler' => new MockHandler([
                new Response(200, [], 'valid'),
                new Response(200, [
                    "X-akismet-guid" => "33a400f82ab1df44aa75716efa99cc8c"
                ], 'false')
            ]),
        ]));

        $rule = new AkismetRule('john-doe@doe.nl', 'John Doe');

        $this->assertTrue($rule->passes('email', 'email'));

        Event::assertNotDispatched(IsSpam::class);
    }
}
