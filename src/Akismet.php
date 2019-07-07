<?php

namespace nickurt\Akismet;

use \GuzzleHttp\Client;
use \nickurt\Akismet\Exception\MalformedURLException;
use nickurt\PwnedPasswords\PwnedPasswords;

class Akismet
{
    /** @var string */
    protected $apiBaseUrl = 'rest.akismet.com';

    /** @var string */
    protected $apiKey;

    /** @var string */
    protected $apiVersion = '1.1';

    /** @var string */
    protected $blogUrl;

    /** @var \GuzzleHttp\Client */
    protected $client;

    /** @var string */
    protected $commentAuthor;

    /** @var string */
    protected $commentAuthorEmail;

    /** @var string */
    protected $commentAuthorUrl;

    /** @var string */
    protected $commentContent;

    /** @var string */
    protected $commentType;

    /** @var bool */
    protected $isTest = false;

    /** @var string */
    protected $permalink;

    /** @var string */
    protected $referrer;

    /** @var string */
    protected $userAgent;

    /** @var string */
    protected $userIp;

    /**
     * @param array $attributes
     * @return $this
     */
    public function fill(array $attributes)
    {
        if (isset($attributes['user_ip'])) {
            $this->setUserIp($attributes['user_ip']);
        }
        if (isset($attributes['user_agent'])) {
            $this->setUserAgent($attributes['user_agent']);
        }
        if (isset($attributes['referrer'])) {
            $this->setReferrer($attributes['referrer']);
        }
        if (isset($attributes['permalink'])) {
            $this->setPermalink($attributes['permalink']);
        }
        if (isset($attributes['comment_type'])) {
            $this->setCommentType($attributes['comment_type']);
        }
        if (isset($attributes['comment_author'])) {
            $this->setCommentAuthor($attributes['comment_author']);
        }
        if (isset($attributes['comment_author_email'])) {
            $this->setCommentAuthorEmail($attributes['comment_author_email']);
        }
        if (isset($attributes['comment_author_url'])) {
            $this->setCommentAuthorUrl($attributes['comment_author_url']);
        }
        if (isset($attributes['comment_content'])) {
            $this->setCommentContent($attributes['comment_content']);
        }
        if (isset($attributes['blog'])) {
            $this->setBlogUrl($attributes['blog']);
        }
        if (isset($attributes['is_test'])) {
            $this->setIsTest($attributes['is_test']);
        }

        return $this;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function isSpam()
    {
        $response = $this->getResponseData(
            sprintf('https://%s.%s/%s/comment-check',
                $this->getApiKey(),
                $this->getApiBaseUrl(),
                $this->getApiVersion()
            ));

        if ((bool)(trim($response->getBody()) == 'true')) {
            event(new \nickurt\Akismet\Events\IsSpam($this->getCommentAuthorEmail()));

            return true;
        }

        return false;
    }

    /**
     * @param string $url
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    private function getResponseData($url)
    {
        try {
            $response = $this->getClient()->post($url, ['form_params' => $this->toArray()]);
        } catch (\Exception $e) {
            $response = $e->getResponse();
        }

        if ($response->hasHeader('X-akismet-debug-help')) {
            throw new \nickurt\Akismet\Exception\AkismetException($response->getHeaderLine('X-akismet-debug-help'));
        }

        return $response;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        if (!isset($this->client)) {
            $this->client = new \GuzzleHttp\Client();

            return $this->client;
        }

        return $this->client;
    }

    /**
     * @param $client
     * @return $this
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'user_ip' => $this->getUserIp(),
            'user_agent' => $this->getUserAgent(),
            'referrer' => $this->getReferrer(),
            'permalink' => $this->getPermalink(),
            'comment_type' => $this->getCommentType(),
            'comment_author' => $this->getCommentAuthor(),
            'comment_author_email' => $this->getCommentAuthorEmail(),
            'comment_author_url' => $this->getCommentAuthorUrl(),
            'comment_content' => $this->getCommentContent(),
            'blog' => $this->getBlogUrl(),
            'is_test' => $this->getIsTest(),
        ];
    }

    /**
     * @return string
     */
    public function getUserIp()
    {
        return $this->userIp;
    }

    /**
     * @param string $userIp
     * @return $this
     */
    public function setUserIp($userIp)
    {
        $this->userIp = $userIp;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     * @return $this
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferrer()
    {
        return $this->referrer;
    }

    /**
     * @param string $referrer
     * @return $this
     */
    public function setReferrer($referrer)
    {
        $this->referrer = $referrer;

        return $this;
    }

    /**
     * @return string
     */
    public function getPermalink()
    {
        return $this->permalink;
    }

    /**
     * @param string $permalink
     * @return $this
     */
    public function setPermalink($permalink)
    {
        $this->permalink = $permalink;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommentType()
    {
        return $this->commentType;
    }

    /**
     * @param string $commentType
     * @return $this
     */
    public function setCommentType($commentType)
    {
        $this->commentType = $commentType;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommentAuthor()
    {
        return $this->commentAuthor;
    }

    /**
     * @param string $commentAuthor
     * @return $this
     */
    public function setCommentAuthor($commentAuthor)
    {
        $this->commentAuthor = $commentAuthor;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommentAuthorEmail()
    {
        return $this->commentAuthorEmail;
    }

    /**
     * @param string $commentAuthorEmail
     * @return $this
     */
    public function setCommentAuthorEmail($commentAuthorEmail)
    {
        $this->commentAuthorEmail = $commentAuthorEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommentAuthorUrl()
    {
        return $this->commentAuthorUrl;
    }

    /**
     * @param string $commentAuthorUrl
     * @return $this
     */
    public function setCommentAuthorUrl($commentAuthorUrl)
    {
        if (filter_var($commentAuthorUrl, FILTER_VALIDATE_URL) === false) {
            throw new MalformedURLException();
        }

        $this->commentAuthorUrl = $commentAuthorUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommentContent()
    {
        return $this->commentContent;
    }

    /**
     * @param string $commentContent
     * @return $this
     */
    public function setCommentContent($commentContent)
    {
        $this->commentContent = $commentContent;

        return $this;
    }

    /**
     * @return string
     */
    public function getBlogUrl()
    {
        return $this->blogUrl;
    }

    /**
     * @param string $blogUrl
     * @return $this
     */
    public function setBlogUrl($blogUrl)
    {
        if (filter_var($blogUrl, FILTER_VALIDATE_URL) === false) {
            throw new MalformedURLException();
        }

        $this->blogUrl = $blogUrl;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsTest()
    {
        return $this->isTest;
    }

    /**
     * @param bool $isTest
     * @return $this
     */
    public function setIsTest($isTest)
    {
        $this->isTest = $isTest;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     * @return $this
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiBaseUrl()
    {
        return $this->apiBaseUrl;
    }

    /**
     * @param string $apiBaseUrl
     * @return $this
     */
    public function setApiBaseUrl($apiBaseUrl)
    {
        $this->apiBaseUrl = $apiBaseUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * @param string $apiVersion
     * @return $this
     */
    public function setApiVersion($apiVersion)
    {
        $this->apiVersion = $apiVersion;

        return $this;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function reportHam()
    {
        $response = $this->getResponseData(
            sprintf('https://%s.%s/%s/submit-ham',
                $this->getApiKey(),
                $this->getApiBaseUrl(),
                $this->getApiVersion()
            ));

        if ((bool)(trim($response->getBody()) == 'Thanks for making the web a better place.')) {
            event(new \nickurt\Akismet\Events\ReportHam($this->getCommentAuthorEmail()));

            return true;
        }

        return false;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function reportSpam()
    {
        $response = $this->getResponseData(
            sprintf('https://%s.%s/%s/submit-spam',
                $this->getApiKey(),
                $this->getApiBaseUrl(),
                $this->getApiVersion()
            ));

        if ((bool)(trim($response->getBody()) == 'Thanks for making the web a better place.')) {
            event(new \nickurt\Akismet\Events\ReportSpam($this->getCommentAuthorEmail()));

            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function validateKey()
    {
        try {
            $response = $this->getClient()->post(sprintf('https://%s/%s/verify-key', $this->getApiBaseUrl(), $this->getApiVersion()), ['form_params' => [
                'key' => $this->getApiKey(),
                'blog' => $this->getBlogUrl(),
            ]]);
        } catch (\Exception $e) {
            $response = $e->getResponse();
        }

        if ($response->hasHeader('X-akismet-debug-help')) {
            throw new \nickurt\Akismet\Exception\AkismetException($response->getHeaderLine('X-akismet-debug-help'));
        }

        return (bool)((string)$response->getBody() == 'valid');
    }
}
