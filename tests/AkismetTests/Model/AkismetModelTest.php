<?php

namespace AkismetTests\Model;

use PHPUnit_Framework_TestCase as TestCase;
use nickurt\Akismet\Akismet;

class AkismetModelTest extends TestCase
{
    public function testAkismetModel()
    {
        $akismet = new Akismet();

        $this->assertSame($akismet->getApiBaseUrl(), 'rest.akismet.com');
        $this->assertSame($akismet->getApiVersion(), '1.1');
        $this->assertSame($akismet->getApiKey(), null);
    }

    public function testAkismetCustomApiInformation()
    {
        $akismet = new Akismet();

        $akismet->setApiBaseUrl('rest.internal.akismet.com');
        $akismet->setApiVersion('2.2');
        $akismet->setApiKey('123qwe456rty');

        $this->assertSame($akismet->getApiBaseUrl(), 'rest.internal.akismet.com');
        $this->assertSame($akismet->getApiVersion(), '2.2');
        $this->assertSame($akismet->getApiKey(), '123qwe456rty');
    }

    public function testAkismetValidBlogUrlInformation()
    {
        $akismet = new Akismet();
        $akismet->setBlogUrl('https://google.nl');

        $this->assertSame($akismet->getBlogUrl(), 'https://google.nl');
    }

    /**
     * @expectedException \nickurt\Akismet\Exception\MalformedURLException
     */
    public function testAkismetInvalidBlogUrlInformation()
    {
        $akismet = new Akismet();
        $akismet->setBlogUrl('foobar');
    }

    public function testAkismetCommentAuthorInformation()
    {
        $akismet = new Akismet();

        $akismet->setCommentType('registration');
        $akismet->setCommentAuthor('John Doe');
        $akismet->setCommentAuthorUrl('https://google.nl');
        $akismet->setCommentAuthorEmail('info@johndoe.ext');
        $akismet->setCommentContent("It's me, John!");

        $this->assertSame($akismet->getCommentType(), 'registration');
        $this->assertSame($akismet->getCommentAuthor(), 'John Doe');
        $this->assertSame($akismet->getCommentAuthorUrl(), 'https://google.nl');
        $this->assertSame($akismet->getCommentAuthorEmail(), 'info@johndoe.ext');
        $this->assertSame($akismet->getCommentContent(), "It's me, John!");
    }

    public function testAkismetNulledCommentAuthorInformation()
    {
        $akismet = new Akismet();

        $akismet->setCommentType(null);
        $akismet->setCommentAuthor(null);
        $akismet->setCommentAuthorEmail(null);
        $akismet->setCommentContent(null);

        $this->assertSame($akismet->getCommentType(), null);
        $this->assertSame($akismet->getCommentAuthor(), null);
        $this->assertSame($akismet->getCommentAuthorEmail(), null);
        $this->assertSame($akismet->getCommentContent(), null);
    }

    public function testAkismetEmptyCommentAuthorInformation()
    {
        $akismet = new Akismet();

        $akismet->setCommentType('');
        $akismet->setCommentAuthor('');
        $akismet->setCommentAuthorEmail('');
        $akismet->setCommentContent('');

        $this->assertSame($akismet->getCommentType(), '');
        $this->assertSame($akismet->getCommentAuthor(), '');
        $this->assertSame($akismet->getCommentAuthorEmail(), '');
        $this->assertSame($akismet->getCommentContent(), '');
    }

    /**
     * @expectedException \nickurt\Akismet\Exception\MalformedURLException
     */
    public function testAkismetInvalidCommentAuthorInformation()
    {
        $akismet = new Akismet();
        $akismet->setCommentAuthorUrl('foobar');
    }
}
