<?php

namespace nickurt\Akismet\tests;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use nickurt\Akismet\Events\IsSpam;
use nickurt\Akismet\Events\ReportHam;
use nickurt\Akismet\Events\ReportSpam;
use nickurt\Akismet\Exception\AkismetException;
use nickurt\Akismet\Exception\MalformedURLException;
use nickurt\Akismet\Facade as Akismet;

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

    public function test_it_can_fill_multiple_comment_filled_values_at_once()
    {
        $this->akismet->fill([
            'comment_type' => 'registration',
            'comment_author' => 'John Doe',
            'comment_author_url' => 'https://google.nl',
            'comment_author_email' => 'info@johndoe.ext',
            'comment_content' => 'It\'s me, John!',
        ]);

        $this->assertSame($this->akismet->getCommentType(), 'registration');
        $this->assertSame($this->akismet->getCommentAuthor(), 'John Doe');
        $this->assertSame($this->akismet->getCommentAuthorUrl(), 'https://google.nl');
        $this->assertSame($this->akismet->getCommentAuthorEmail(), 'info@johndoe.ext');
        $this->assertSame($this->akismet->getCommentContent(), "It's me, John!");
    }

    public function test_it_can_return_the_default_values()
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

    public function test_it_can_set_a_custom_value_for_is_test()
    {
        $this->akismet->setIsTest(true);

        $this->assertSame(true, $this->akismet->getIsTest());

        $this->akismet->setIsTest(false);

        $this->assertSame(false, $this->akismet->getIsTest());
    }

    public function test_it_can_set_a_custom_value_for_the_api_base_url()
    {
        $this->akismet->setApiBaseUrl('rest-ppe.akismet.com');

        $this->assertSame('rest-ppe.akismet.com', $this->akismet->getApiBaseUrl());
    }

    public function test_it_can_set_a_custom_value_for_the_api_key()
    {
        $this->akismet->setApiKey('zyxwvutsrqponmlkjihgfedcba');

        $this->assertSame('zyxwvutsrqponmlkjihgfedcba', $this->akismet->getApiKey());
    }

    public function test_it_can_set_a_custom_value_for_the_api_version()
    {
        $this->akismet->setApiVersion('2.3');

        $this->assertSame('2.3', $this->akismet->getApiVersion());
    }

    public function test_it_can_set_a_custom_value_for_the_blog_url()
    {
        $this->akismet->setBlogUrl('http://akismet-ppe.local');

        $this->assertSame('http://akismet-ppe.local', $this->akismet->getBlogUrl());
    }

    public function test_it_can_set_a_custom_value_for_the_comment_author()
    {
        $this->akismet->setCommentAuthor('John Doe');

        $this->assertSame('John Doe', $this->akismet->getCommentAuthor());
    }

    public function test_it_can_set_a_custom_value_for_the_comment_author_email()
    {
        $this->akismet->setCommentAuthorEmail('john-doe@doe.tld');

        $this->assertSame('john-doe@doe.tld', $this->akismet->getCommentAuthorEmail());
    }

    public function test_it_can_set_a_custom_value_for_the_comment_author_url()
    {
        $this->akismet->setCommentAuthorUrl('https://john-doe.tld');

        $this->assertSame('https://john-doe.tld', $this->akismet->getCommentAuthorUrl());
    }

    public function test_it_can_set_a_custom_value_for_the_comment_content()
    {
        $this->akismet->setCommentContent('bla-bla-bla');

        $this->assertSame('bla-bla-bla', $this->akismet->getCommentContent());
    }

    public function test_it_can_set_a_custom_value_for_the_comment_type()
    {
        $this->akismet->setCommentType('registration');

        $this->assertSame('registration', $this->akismet->getCommentType());
    }

    public function test_it_can_set_a_custom_value_for_the_permalink()
    {
        $this->akismet->setPermalink('http://akismet-permalink.local/a/b/c/d/e/f');

        $this->assertSame('http://akismet-permalink.local/a/b/c/d/e/f', $this->akismet->getPermalink());
    }

    public function test_it_can_set_a_custom_value_for_the_referrer()
    {
        $this->akismet->setReferrer('http://akismet-referrer.local/f/e/d/c/b/a');

        $this->assertSame('http://akismet-referrer.local/f/e/d/c/b/a', $this->akismet->getReferrer());
    }

    public function test_it_can_set_a_custom_value_for_the_user_ip()
    {
        $this->akismet->setUserIp('118.25.6.39');

        $this->assertSame('118.25.6.39', $this->akismet->getUserIp());
    }

    public function test_it_can_set_a_custom_value_for_the_useragent()
    {
        $this->akismet->setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36');

        $this->assertSame('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36', $this->akismet->getUserAgent());
    }

    public function test_it_can_set_a_custom_value_for_the_honeypot_field_name()
    {
        $this->akismet->setHoneypotFieldName('A honeypot fieldname');

        $this->assertSame($this->akismet->getHoneypotFieldName(), 'A honeypot fieldname');
    }

    public function test_it_can_set_a_custom_value_for_the_hidden_honeypot_field()
    {
        $this->akismet->setHiddenHoneypotField('A hidden honeypot field value');

        $this->assertSame($this->akismet->getHiddenHoneypotField(), 'A hidden honeypot field value');
    }

    public function test_it_can_set_null_as_honeypot_field_name()
    {
        $this->akismet->setHoneypotFieldName(null);

        $this->assertSame($this->akismet->getHoneypotFieldName(), null);
    }

    public function test_it_can_set_null_as_hidden_honeypot_field()
    {
        $this->akismet->setHiddenHoneypotField(null);

        $this->assertSame($this->akismet->getHiddenHoneypotField(), null);
    }

    public function test_it_can_set_multiple_comment_empty_values_at_once()
    {
        $this->akismet->fill(['comment_type' => '', 'comment_author' => '', 'comment_author_email' => '', 'comment_content' => '']);

        $this->assertSame($this->akismet->getCommentType(), '');
        $this->assertSame($this->akismet->getCommentAuthor(), '');
        $this->assertSame($this->akismet->getCommentAuthorEmail(), '');
        $this->assertSame($this->akismet->getCommentContent(), '');
    }

    public function test_it_can_set_multiple_comment_nulled_values_at_once()
    {
        $this->akismet->fill(['comment_type' => null, 'comment_author' => null, 'comment_author_email' => null, 'comment_content' => null]);

        $this->assertSame($this->akismet->getCommentType(), null);
        $this->assertSame($this->akismet->getCommentAuthor(), null);
        $this->assertSame($this->akismet->getCommentAuthorEmail(), null);
        $this->assertSame($this->akismet->getCommentContent(), null);
    }

    public function test_it_can_set_multiple_honeypot_empty_values_at_once()
    {
        $this->akismet->fill([
            'honeypot_field_name' => '',
            'hidden_honeypot_field' => '',
        ]);

        $this->assertSame($this->akismet->getHoneypotFieldName(), '');
        $this->assertSame($this->akismet->getHiddenHoneypotField(), '');
    }

    public function test_it_can_set_multiple_honeypot_nulled_values_at_once()
    {
        $this->akismet->fill([
            'honeypot_field_name' => null,
            'hidden_honeypot_field' => null,
        ]);

        $this->assertSame($this->akismet->getHoneypotFieldName(), null);
        $this->assertSame($this->akismet->getHiddenHoneypotField(), null);
    }

    public function test_it_can_work_with_app_instance()
    {
        $this->assertInstanceOf(\nickurt\Akismet\Akismet::class, app('Akismet'));

        $this->assertInstanceOf(\nickurt\Akismet\Akismet::class, $this->app['Akismet']);
    }

    public function test_it_can_work_with_helper_function()
    {
        $this->assertInstanceOf(\nickurt\Akismet\Akismet::class, akismet());
    }

    public function test_it_will_return_false_by_a_non_spam_comment_check()
    {
        Event::fake();

        Http::fake(['https://abcdefghijklmnopqrstuvwxyz.rest.akismet.com/1.1/comment-check' => Http::response('false', 200, ['X-akismet-guid' => '8a0c2c8981324a621707acdf958fc2ad'])]);

        $this->assertFalse($this->akismet->setCommentAuthorEmail('john-doe@doe.nl')->isSpam());

        Event::assertNotDispatched(IsSpam::class);
    }

    public function test_it_will_return_false_by_an_invalid_key_or_blog_url()
    {
        Http::fake(['https://rest.akismet.com/1.1/verify-key' => Http::response('invalid')]);

        $this->assertFalse($this->akismet->validateKey());
    }

    public function test_it_will_return_true_by_a_valid_key_and_blog_url()
    {
        Http::fake(['https://rest.akismet.com/1.1/verify-key' => Http::response('valid')]);

        $this->assertTrue($this->akismet->validateKey());
    }

    public function test_it_will_return_true_by_a_valid_report_ham_to_akismet()
    {
        Event::fake();

        Http::fake(['https://abcdefghijklmnopqrstuvwxyz.rest.akismet.com/1.1/submit-ham' => Http::response('Thanks for making the web a better place.')]);

        $this->assertTrue($this->akismet->setCommentAuthorEmail('it-can-report-ham-to@akismet.com')->reportHam());

        Event::assertDispatched(ReportHam::class, function ($e) {
            return $e->email == 'it-can-report-ham-to@akismet.com';
        });
    }

    public function test_it_will_return_true_by_a_valid_report_spam_to_akismet()
    {
        Event::fake();

        Http::fake(['https://abcdefghijklmnopqrstuvwxyz.rest.akismet.com/1.1/submit-spam' => Http::response('Thanks for making the web a better place.')]);

        $this->assertTrue($this->akismet->setCommentAuthorEmail('it-can-report-spam-to@akismet.com')->reportSpam());

        Event::assertDispatched(ReportSpam::class, function ($e) {
            return $e->email == 'it-can-report-spam-to@akismet.com';
        });
    }

    public function test_it_will_return_true_by_a_valid_spam_comment_check()
    {
        Event::fake();

        Http::fake(['https://abcdefghijklmnopqrstuvwxyz.rest.akismet.com/1.1/comment-check' => Http::response('true', 200, ['X-akismet-guid' => '33a400f82ab1df44aa75716efa99cc8c'])]);

        $this->assertTrue($this->akismet->setCommentAuthorEmail('akismet-guaranteed-spam@example.com')->isSpam());

        Event::assertDispatched(IsSpam::class, function ($e) {
            return $e->email == 'akismet-guaranteed-spam@example.com';
        });
    }

    public function test_it_will_throw_exception_if_it_has_x_akismet_debug_help_header()
    {
        Event::fake();

        Http::fake(['https://abcdefghijklmnopqrstuvwxyz.rest.akismet.com/1.1/comment-check' => Http::response('invalid', 200, ['X-akismet-debug-help' => 'We were unable to parse your blog URI'])]);

        $this->expectException(AkismetException::class);
        $this->expectExceptionMessage('We were unable to parse your blog URI');

        $this->akismet->setCommentAuthorEmail('john-doe@doe.nl')->isSpam();
    }

    public function test_it_can_set_null_as_comment_author_url()
    {
        $this->akismet->setCommentAuthorUrl(null);

        $this->assertNull($this->akismet->getCommentAuthor());
    }

	public function test_it_handles_http_client_exception_gracefully_on_comment_check()
	{
		Event::fake();

		Http::fake([
			'https://abcdefghijklmnopqrstuvwxyz.rest.akismet.com/1.1/comment-check' => function () {
				throw new \Exception('connection failed');
			},
		]);

        $this->expectException(AkismetException::class);
        $this->expectExceptionMessage('connection failed');

		$this->akismet->setCommentAuthorEmail('john-doe@doe.nl')->isSpam();
	}
}
