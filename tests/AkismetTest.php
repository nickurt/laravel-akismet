<?php

namespace nickurt\Akismet\Tests;

use Orchestra\Testbench\TestCase;
use Akismet;

class AkismetTest extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('akismet.api_key', 'abcdefghijklmnopqrstuvwxyz');
        $app['config']->set('akismet.blog_url', 'http://akismet.local');
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Akismet' => \nickurt\Akismet\Facade::class
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \nickurt\Akismet\ServiceProvider::class
        ];
    }

    /** @test */
    public function it_can_fill_multiple_comment_filled_values_at_once()
    {
        $akismet = \Akismet::getFacadeRoot();

        $akismet->fill(['comment_type' => 'registration', 'comment_author' => 'John Doe', 'comment_author_url' => 'https://google.nl', 'comment_author_email' => 'info@johndoe.ext', 'comment_content' => 'It\'s me, John!',]);

        $this->assertSame($akismet->getCommentType(), 'registration');
        $this->assertSame($akismet->getCommentAuthor(), 'John Doe');
        $this->assertSame($akismet->getCommentAuthorUrl(), 'https://google.nl');
        $this->assertSame($akismet->getCommentAuthorEmail(), 'info@johndoe.ext');
        $this->assertSame($akismet->getCommentContent(), "It's me, John!");
    }

    /** @test */
    public function it_can_get_the_http_client()
    {
        $this->assertInstanceOf(\GuzzleHttp\Client::class, \Akismet::getClient());
    }

    /** @test */
    public function it_can_return_the_default_values()
    {
        $akismet = \Akismet::getFacadeRoot();

        $this->assertSame('abcdefghijklmnopqrstuvwxyz', $akismet->getApiKey());
        $this->assertSame('rest.akismet.com', $akismet->getApiBaseUrl());
        $this->assertSame('1.1', $akismet->getApiVersion());
        $this->assertSame('http://akismet.local', $akismet->getBlogUrl());

        $this->assertSame('http://localhost', $akismet->getPermalink());
        $this->assertSame('http://localhost', $akismet->getReferrer());
        $this->assertSame('Symfony', $akismet->getUserAgent());
        $this->assertSame('127.0.0.1', $akismet->getUserIp());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_is_test()
    {
        $akismet = \Akismet::setIsTest(true);

        $this->assertSame(true, $akismet->getIsTest());

        $akismet = \Akismet::setIsTest(false);

        $this->assertSame(false, $akismet->getIsTest());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_api_base_url()
    {
        $akismet = \Akismet::setApiBaseUrl('rest-ppe.akismet.com');

        $this->assertSame('rest-ppe.akismet.com', $akismet->getApiBaseUrl());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_api_key()
    {
        $akismet = \Akismet::setApiKey('zyxwvutsrqponmlkjihgfedcba');

        $this->assertSame('zyxwvutsrqponmlkjihgfedcba', $akismet->getApiKey());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_api_version()
    {
        $akismet = \Akismet::setApiVersion('2.3');

        $this->assertSame('2.3', $akismet->getApiVersion());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_blog_url()
    {
        $akismet = \Akismet::setBlogUrl('http://akismet-ppe.local');

        $this->assertSame('http://akismet-ppe.local', $akismet->getBlogUrl());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_comment_author()
    {
        $akismet = \Akismet::setCommentAuthor('John Doe');

        $this->assertSame('John Doe', $akismet->getCommentAuthor());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_comment_author_email()
    {
        $akismet = \Akismet::setCommentAuthorEmail('john-doe@doe.tld');

        $this->assertSame('john-doe@doe.tld', $akismet->getCommentAuthorEmail());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_comment_author_url()
    {
        $akismet = \Akismet::setCommentAuthorUrl('https://john-doe.tld');

        $this->assertSame('https://john-doe.tld', $akismet->getCommentAuthorUrl());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_comment_content()
    {
        $akismet = \Akismet::setCommentContent('bla-bla-bla');

        $this->assertSame('bla-bla-bla', $akismet->getCommentContent());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_comment_type()
    {
        $akismet = \Akismet::setCommentType('registration');

        $this->assertSame('registration', $akismet->getCommentType());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_permalink()
    {
        $akismet = \Akismet::setPermalink('http://akismet-permalink.local/a/b/c/d/e/f');

        $this->assertSame('http://akismet-permalink.local/a/b/c/d/e/f', $akismet->getPermalink());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_referrer()
    {
        $akismet = \Akismet::setReferrer('http://akismet-referrer.local/f/e/d/c/b/a');

        $this->assertSame('http://akismet-referrer.local/f/e/d/c/b/a', $akismet->getReferrer());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_user_ip()
    {
        $akismet = \Akismet::setUserIp('118.25.6.39');

        $this->assertSame('118.25.6.39', $akismet->getUserIp());
    }

    /** @test */
    public function it_can_set_a_custom_value_for_the_useragent()
    {
        $akismet = \Akismet::setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36');

        $this->assertSame('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36', $akismet->getUserAgent());
    }

    /** @test */
    public function it_can_set_multiple_comment_empty_values_at_once()
    {
        $akismet = \Akismet::getFacadeRoot();

        $akismet->fill(['comment_type' => '', 'comment_author' => '', 'comment_author_email' => '', 'comment_content' => '']);

        $this->assertSame($akismet->getCommentType(), '');
        $this->assertSame($akismet->getCommentAuthor(), '');
        $this->assertSame($akismet->getCommentAuthorEmail(), '');
        $this->assertSame($akismet->getCommentContent(), '');
    }

    /** @test */
    public function it_can_set_multiple_comment_nulled_values_at_once()
    {
        $akismet = \Akismet::getFacadeRoot();

        $akismet->fill(['comment_type' => null, 'comment_author' => null, 'comment_author_email' => null, 'comment_content' => null]);

        $this->assertSame($akismet->getCommentType(), null);
        $this->assertSame($akismet->getCommentAuthor(), null);
        $this->assertSame($akismet->getCommentAuthorEmail(), null);
        $this->assertSame($akismet->getCommentContent(), null);
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
        \Event::fake();

        \Akismet::setClient(new \GuzzleHttp\Client([
            'handler' => new \GuzzleHttp\Handler\MockHandler([
                new \GuzzleHttp\Psr7\Response(200, [], 'valid'),
                new \GuzzleHttp\Psr7\Response(200, [
                    "X-akismet-guid" => "33a400f82ab1df44aa75716efa99cc8c"
                ], 'true')
            ]),
        ]));

        $rule = new \nickurt\Akismet\Rules\AkismetRule('akismet-guaranteed-spam@example.com', 'viagra-test-123');

        $this->assertFalse($rule->passes('email', 'email'));

        \Event::assertDispatched(\nickurt\Akismet\Events\IsSpam::class, function ($e) {
            return ($e->email == 'akismet-guaranteed-spam@example.com');
        });
    }

    /** @test */
    public function it_will_not_fire_is_spam_event_by_a_spam_email_via_validation_rule()
    {
        \Event::fake();

        \Akismet::setClient(new \GuzzleHttp\Client([
            'handler' => new \GuzzleHttp\Handler\MockHandler([
                new \GuzzleHttp\Psr7\Response(200, [], 'valid'),
                new \GuzzleHttp\Psr7\Response(200, [
                    "X-akismet-guid" => "33a400f82ab1df44aa75716efa99cc8c"
                ], 'false')
            ]),
        ]));

        $rule = new \nickurt\Akismet\Rules\AkismetRule('john-doe@doe.nl', 'John Doe');

        $this->assertTrue($rule->passes('email', 'email'));

        \Event::assertNotDispatched(\nickurt\Akismet\Events\IsSpam::class);
    }

    /** @test */
    public function it_will_return_false_by_a_non_spam_comment_check()
    {
        \Event::fake();

        $httpClient = new \GuzzleHttp\Client([
            'handler' => new \GuzzleHttp\Handler\MockHandler([
                new \GuzzleHttp\Psr7\Response(200, [
                    "X-akismet-guid" => "8a0c2c8981324a621707acdf958fc2ad"
                ], 'false')
            ]),
        ]);

        $this->assertFalse(\Akismet::setClient($httpClient)->setCommentAuthorEmail('john-doe@doe.nl')->isSpam());

        \Event::assertNotDispatched(\nickurt\Akismet\Events\IsSpam::class);
    }

    /** @test */
    public function it_will_return_false_by_an_invalid_key_or_blog_url()
    {
        $httpClient = new \GuzzleHttp\Client([
            'handler' => new \GuzzleHttp\Handler\MockHandler([
                new \GuzzleHttp\Psr7\Response(200, [], 'invalid')
            ]),
        ]);

        $this->assertFalse(\Akismet::setClient($httpClient)->validateKey());
    }

    /** @test */
    public function it_will_return_true_by_a_valid_key_and_blog_url()
    {
        $httpClient = new \GuzzleHttp\Client([
            'handler' => new \GuzzleHttp\Handler\MockHandler([
                new \GuzzleHttp\Psr7\Response(200, [], 'valid')
            ]),
        ]);

        $this->assertTrue(\Akismet::setClient($httpClient)->validateKey());
    }

    /** @test */
    public function it_will_return_true_by_a_valid_report_ham_to_akismet()
    {
        \Event::fake();

        $httpClient = new \GuzzleHttp\Client([
            'handler' => new \GuzzleHttp\Handler\MockHandler([
                new \GuzzleHttp\Psr7\Response(200, [], 'Thanks for making the web a better place.')
            ]),
        ]);

        $this->assertTrue(\Akismet::setClient($httpClient)->setCommentAuthorEmail('it-can-report-ham-to@akismet.com')->reportHam());

        \Event::assertDispatched(\nickurt\Akismet\Events\ReportHam::class, function ($e) {
            return ($e->email == 'it-can-report-ham-to@akismet.com');
        });
    }

    /** @test */
    public function it_will_return_true_by_a_valid_report_spam_to_akismet()
    {
        \Event::fake();

        $httpClient = new \GuzzleHttp\Client([
            'handler' => new \GuzzleHttp\Handler\MockHandler([
                new \GuzzleHttp\Psr7\Response(200, [], 'Thanks for making the web a better place.')
            ]),
        ]);

        $this->assertTrue(\Akismet::setClient($httpClient)->setCommentAuthorEmail('it-can-report-spam-to@akismet.com')->reportSpam());

        \Event::assertDispatched(\nickurt\Akismet\Events\ReportSpam::class, function ($e) {
            return ($e->email == 'it-can-report-spam-to@akismet.com');
        });
    }

    /** @test */
    public function it_will_return_true_by_a_valid_spam_comment_check()
    {
        \Event::fake();

        $httpClient = new \GuzzleHttp\Client([
            'handler' => new \GuzzleHttp\Handler\MockHandler([
                new \GuzzleHttp\Psr7\Response(200, [
                    "X-akismet-guid" => "33a400f82ab1df44aa75716efa99cc8c"
                ], 'true')
            ]),
        ]);

        $this->assertTrue(\Akismet::setClient($httpClient)->setCommentAuthorEmail('akismet-guaranteed-spam@example.com')->isSpam());

        \Event::assertDispatched(\nickurt\Akismet\Events\IsSpam::class, function ($e) {
            return ($e->email == 'akismet-guaranteed-spam@example.com');
        });
    }

    /** @test */
    public function it_will_throw_exception_if_it_has_x_akismet_debug_help_header()
    {
        $this->expectException(\nickurt\Akismet\Exception\AkismetException::class);
        $this->expectExceptionMessage('We were unable to parse your blog URI');

        $httpClient = new \GuzzleHttp\Client([
            'handler' => new \GuzzleHttp\Handler\MockHandler([
                new \GuzzleHttp\Psr7\Response(200, [
                    'X-akismet-debug-help' => 'We were unable to parse your blog URI'
                ], 'invalid')
            ]),
        ]);

        \Akismet::setClient($httpClient)->setCommentAuthorEmail('john-doe@doe.nl')->isSpam();
    }

    /** @test */
    public function it_will_throw_malformed_url_exception()
    {
        $this->expectException(\nickurt\Akismet\Exception\MalformedURLException::class);

        \Akismet::setCommentAuthorUrl('malformed_url');
    }
}