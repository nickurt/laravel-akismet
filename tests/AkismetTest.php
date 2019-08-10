<?php

namespace nickurt\Akismet\Tests;

use Akismet;
use Event;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Application;
use nickurt\Akismet\Events\IsSpam;
use nickurt\Akismet\Events\ReportHam;
use nickurt\Akismet\Events\ReportSpam;
use nickurt\Akismet\Exception\AkismetException;
use nickurt\Akismet\Exception\MalformedURLException;
use nickurt\Akismet\Facade;
use nickurt\Akismet\Rules\AkismetRule;
use nickurt\Akismet\ServiceProvider;
use Orchestra\Testbench\TestCase;

class AkismetTest extends TestCase
{
    /** @var \nickurt\Akismet\Akismet */
    protected $akismet;

    public function setUp(): void
    {
        parent::setUp();

        /** @var \nickurt\Akismet\Akismet akismet */
        $this->akismet = Akismet::getFacadeRoot();
    }

    /** @test */
    public function it_can_fill_multiple_comment_filled_values_at_once()
    {
        $this->akismet->fill([
            'comment_type' => 'registration',
            'comment_author' => 'John Doe',
            'comment_author_url' => 'https://google.nl',
            'comment_author_email' => 'info@johndoe.ext',
            'comment_content' => 'It\'s me, John!'
        ]);

        $this->assertSame($this->akismet->getCommentType(), 'registration');
        $this->assertSame($this->akismet->getCommentAuthor(), 'John Doe');
        $this->assertSame($this->akismet->getCommentAuthorUrl(), 'https://google.nl');
        $this->assertSame($this->akismet->getCommentAuthorEmail(), 'info@johndoe.ext');
        $this->assertSame($this->akismet->getCommentContent(), "It's me, John!");
    }

    /** @test */
    public function it_can_get_the_http_client()
    {
        $this->assertInstanceOf(Client::class, $this->akismet->getClient());
    }

    /** @test */
    public function it_can_return_the_default_values()
    {
        $this->assertSame('abcdefghijklmnopqrstuvwxyz', $this->akismet->getApiKey());
        $this->assertSame('rest.akismet.com', $this->akismet->getApiBaseUrl());
        $this->assertSame('1.1', $this->akismet->getApiVersion());
        $this->assertSame('http://akismet.local', $this->akismet->getBlogUrl());

        $this->assertSame('http://localhost', $this->akismet->getPermalink());
        $this->assertSame('http://localhost', $this->akismet->getReferrer());
        $this->assertSame('Symfony', $this->akismet->getUserAgent());
        $this->assertSame('127.0.0.1', $this->akismet->getUserIp());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_is_test()
    {
        $this->akismet->setIsTest(true);

        $this->assertSame(true, $this->akismet->getIsTest());

        $this->akismet->setIsTest(false);

        $this->assertSame(false, $this->akismet->getIsTest());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_api_base_url()
    {
        $this->akismet->setApiBaseUrl('rest-ppe.akismet.com');

        $this->assertSame('rest-ppe.akismet.com', $this->akismet->getApiBaseUrl());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_api_key()
    {
        $this->akismet->setApiKey('zyxwvutsrqponmlkjihgfedcba');

        $this->assertSame('zyxwvutsrqponmlkjihgfedcba', $this->akismet->getApiKey());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_api_version()
    {
        $this->akismet->setApiVersion('2.3');

        $this->assertSame('2.3', $this->akismet->getApiVersion());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_blog_url()
    {
        $this->akismet->setBlogUrl('http://akismet-ppe.local');

        $this->assertSame('http://akismet-ppe.local', $this->akismet->getBlogUrl());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_comment_author()
    {
        $this->akismet->setCommentAuthor('John Doe');

        $this->assertSame('John Doe', $this->akismet->getCommentAuthor());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_comment_author_email()
    {
        $this->akismet->setCommentAuthorEmail('john-doe@doe.tld');

        $this->assertSame('john-doe@doe.tld', $this->akismet->getCommentAuthorEmail());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_comment_author_url()
    {
        $this->akismet->setCommentAuthorUrl('https://john-doe.tld');

        $this->assertSame('https://john-doe.tld', $this->akismet->getCommentAuthorUrl());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_comment_content()
    {
        $this->akismet->setCommentContent('bla-bla-bla');

        $this->assertSame('bla-bla-bla', $this->akismet->getCommentContent());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_comment_type()
    {
        $this->akismet->setCommentType('registration');

        $this->assertSame('registration', $this->akismet->getCommentType());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_permalink()
    {
        $this->akismet->setPermalink('http://akismet-permalink.local/a/b/c/d/e/f');

        $this->assertSame('http://akismet-permalink.local/a/b/c/d/e/f', $this->akismet->getPermalink());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_referrer()
    {
        $this->akismet->setReferrer('http://akismet-referrer.local/f/e/d/c/b/a');

        $this->assertSame('http://akismet-referrer.local/f/e/d/c/b/a', $this->akismet->getReferrer());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_user_ip()
    {
        $this->akismet->setUserIp('118.25.6.39');

        $this->assertSame('118.25.6.39', $this->akismet->getUserIp());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_useragent()
    {
        $this->akismet->setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36');

        $this->assertSame('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36', $this->akismet->getUserAgent());
    }

    /** @test */
    public function it_can_set_multiple_comment_empty_values_at_once()
    {
        $this->akismet->fill(['comment_type' => '', 'comment_author' => '', 'comment_author_email' => '', 'comment_content' => '']);

        $this->assertSame($this->akismet->getCommentType(), '');
        $this->assertSame($this->akismet->getCommentAuthor(), '');
        $this->assertSame($this->akismet->getCommentAuthorEmail(), '');
        $this->assertSame($this->akismet->getCommentContent(), '');
    }

    /** @test */
    public function it_can_set_multiple_comment_nulled_values_at_once()
    {
        $this->akismet->fill(['comment_type' => null, 'comment_author' => null, 'comment_author_email' => null, 'comment_content' => null]);

        $this->assertSame($this->akismet->getCommentType(), null);
        $this->assertSame($this->akismet->getCommentAuthor(), null);
        $this->assertSame($this->akismet->getCommentAuthorEmail(), null);
        $this->assertSame($this->akismet->getCommentContent(), null);
    }

    /** @test */
    public function it_can_work_with_app_instance()
    {
        $this->assertInstanceOf(\nickurt\Akismet\Akismet::class, app('Akismet'));

        $this->assertInstanceOf(\nickurt\Akismet\Akismet::class, $this->app['Akismet']);
    }

    /** @test */
    public function it_can_work_with_helper_function()
    {
        $this->assertInstanceOf(\nickurt\Akismet\Akismet::class, akismet());
    }

    /** @test */
    public function it_will_fire_is_spam_event_by_a_spam_email_via_validation_rule()
    {
        Event::fake();

        $this->akismet->setClient(new Client([
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

        $this->akismet->setClient(new Client([
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

    /** @test */
    public function it_will_return_false_by_a_non_spam_comment_check()
    {
        Event::fake();

        $this->assertFalse($this->akismet->setClient(new Client([
            'handler' => new MockHandler([
                new Response(200, [
                    "X-akismet-guid" => "8a0c2c8981324a621707acdf958fc2ad"
                ], 'false')
            ]),
        ]))->setCommentAuthorEmail('john-doe@doe.nl')->isSpam());

        Event::assertNotDispatched(IsSpam::class);
    }

    /** @test */
    public function it_will_return_false_by_an_invalid_key_or_blog_url()
    {
        $this->assertFalse($this->akismet->setClient(new Client([
            'handler' => new MockHandler([
                new Response(200, [], 'invalid')
            ]),
        ]))->validateKey());
    }

    /** @test */
    public function it_will_return_true_by_a_valid_key_and_blog_url()
    {
        $this->assertTrue($this->akismet->setClient(new Client([
            'handler' => new MockHandler([
                new Response(200, [], 'valid')
            ]),
        ]))->validateKey());

        $this->assertSame('https://rest.akismet.com/1.1/verify-key', (string)$this->akismet->getClient()->getConfig()['handler']->getLastRequest()->getUri());
    }

    /** @test */
    public function it_will_return_true_by_a_valid_report_ham_to_akismet()
    {
        Event::fake();

        $this->assertTrue($this->akismet->setClient(new Client([
            'handler' => new MockHandler([
                new Response(200, [], 'Thanks for making the web a better place.')
            ]),
        ]))->setCommentAuthorEmail('it-can-report-ham-to@akismet.com')->reportHam());

        $this->assertSame('https://abcdefghijklmnopqrstuvwxyz.rest.akismet.com/1.1/submit-ham', (string)$this->akismet->getClient()->getConfig()['handler']->getLastRequest()->getUri());

        Event::assertDispatched(ReportHam::class, function ($e) {
            return ($e->email == 'it-can-report-ham-to@akismet.com');
        });
    }

    /** @test */
    public function it_will_return_true_by_a_valid_report_spam_to_akismet()
    {
        Event::fake();

        $this->assertTrue($this->akismet->setClient(new Client([
            'handler' => new MockHandler([
                new Response(200, [], 'Thanks for making the web a better place.')
            ]),
        ]))->setCommentAuthorEmail('it-can-report-spam-to@akismet.com')->reportSpam());

        $this->assertSame('https://abcdefghijklmnopqrstuvwxyz.rest.akismet.com/1.1/submit-spam', (string)$this->akismet->getClient()->getConfig()['handler']->getLastRequest()->getUri());

        Event::assertDispatched(ReportSpam::class, function ($e) {
            return ($e->email == 'it-can-report-spam-to@akismet.com');
        });
    }

    /** @test */
    public function it_will_return_true_by_a_valid_spam_comment_check()
    {
        Event::fake();

        $this->assertTrue($this->akismet->setClient(new Client([
            'handler' => new MockHandler([
                new Response(200, [
                    "X-akismet-guid" => "33a400f82ab1df44aa75716efa99cc8c"
                ], 'true')
            ]),
        ]))->setCommentAuthorEmail('akismet-guaranteed-spam@example.com')->isSpam());

        $this->assertSame('https://abcdefghijklmnopqrstuvwxyz.rest.akismet.com/1.1/comment-check', (string)$this->akismet->getClient()->getConfig()['handler']->getLastRequest()->getUri());

        Event::assertDispatched(IsSpam::class, function ($e) {
            return ($e->email == 'akismet-guaranteed-spam@example.com');
        });
    }

    /** @test */
    public function it_will_throw_exception_if_it_has_x_akismet_debug_help_header()
    {
        $this->expectException(AkismetException::class);
        $this->expectExceptionMessage('We were unable to parse your blog URI');

        $this->akismet->setClient(new Client([
            'handler' => new MockHandler([
                new Response(200, [
                    'X-akismet-debug-help' => 'We were unable to parse your blog URI'
                ], 'invalid')
            ]),
        ]))->setCommentAuthorEmail('john-doe@doe.nl')->isSpam();
    }

    /** @test */
    public function it_will_throw_malformed_url_exception()
    {
        $this->expectException(MalformedURLException::class);

        $this->akismet->setCommentAuthorUrl('malformed_url');
    }

    /**
     * Define environment setup.
     *
     * @param Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('akismet.api_key', 'abcdefghijklmnopqrstuvwxyz');
        $app['config']->set('akismet.blog_url', 'http://akismet.local');
    }

    /**
     * @param Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Akismet' => Facade::class
        ];
    }

    /**
     * @param Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class
        ];
    }
}
