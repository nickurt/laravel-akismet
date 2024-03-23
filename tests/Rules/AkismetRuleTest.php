<?php

namespace nickurt\Akismet\tests\Rules;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use nickurt\Akismet\Events\IsSpam;
use nickurt\Akismet\Rules\AkismetRule;
use nickurt\Akismet\tests\TestCase;

class AkismetRuleTest extends TestCase
{
    public function test_it_will_fire_is_spam_event_by_a_spam_email_via_validation_rule()
    {
        Event::fake();

        Http::fake([
            'https://rest.akismet.com/1.1/verify-key' => Http::response('valid'),
            'https://abcdefghijklmnopqrstuvwxyz.rest.akismet.com/1.1/comment-check' => Http::response('true', 200, ['X-akismet-guid' => '33a400f82ab1df44aa75716efa99cc8c']),
        ]);

        $rule = new AkismetRule('akismet-guaranteed-spam@example.com', 'viagra-test-123');

        $this->assertFalse($rule->passes('email', 'email'));

        Event::assertDispatched(IsSpam::class, function ($e) {
            return $e->email == 'akismet-guaranteed-spam@example.com';
        });
    }

    public function test_it_will_not_fire_is_spam_event_by_a_spam_email_via_validation_rule()
    {
        Event::fake();

        Http::fake([
            'https://rest.akismet.com/1.1/verify-key' => Http::response('valid'),
            'https://abcdefghijklmnopqrstuvwxyz.rest.akismet.com/1.1/comment-check' => Http::response('false', 200, ['X-akismet-guid' => '33a400f82ab1df44aa75716efa99cc8c']),
        ]);

        $rule = new AkismetRule('john-doe@doe.nl', 'John Doe');

        $this->assertTrue($rule->passes('email', 'email'));

        Event::assertNotDispatched(IsSpam::class);
    }
}
