<?php

namespace nickurt\Akismet;

use \GuzzleHttp\Client;
use \nickurt\Akismet\Exception\MalformedURLException;

class Akismet {

    /**
     * @var string
     */
    protected $apiBaseUrl = 'rest.akismet.com';

    /**
     * @var string
     */
    protected $apiVersion = '1.1';

    /**
     * @var
     */
    protected $apiKey;

    /**
     * @var
     */
    protected $userIp;

    /**
     * @var
     */
    protected $userAgent;

    /**
     * @var
     */
    protected $referrer;

    /**
     * @var
     */
    protected $permalink;

    /**
     * @var
     */
    protected $blogUrl;

    /**
     * @var
     */
    protected $commentType;

    /**
     * @var
     */
    protected $commentAuthor;

    /**
     * @var
     */
    protected $commentAuthorEmail;

    /**
     * @var
     */
    protected $commentAuthorUrl;

    /**
     * @var
     */
    protected $commentContent;

    /**
     * @var
     */
    protected $isTest = false;

    public function __construct()
    {
        $this->userIp = class_exists('\Illuminate\Support\Facades\Request') ? \Request::getClientIp() : $_SERVER['REMOTE_ADDR'];
        $this->userAgent = class_exists('\Illuminate\Support\Facades\Request') ? \Request::server('HTTP_USER_AGENT') : $_SERVER['HTTP_USER_AGENT'];
        $this->referrer = class_exists('\Illuminate\Support\Facades\URL') ? \URL::previous() : $_SERVER['HTTP_REFERER'];
        $this->permalink = class_exists('\Illuminate\Support\Facades\Request') ? \Request::url() : $_SERVER['REQUEST_URI'];
    }

    /**
     * @return string
     */
    public function getApiBaseUrl()
    {
        return $this->apiBaseUrl;
    }

    /**
     * @param $apiBaseUrl
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
     * @param $apiVersion
     * @return $this
     */
    public function setApiVersion($apiVersion)
    {
        $this->apiVersion = $apiVersion;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param $apiKey
     * @return $this
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBlogUrl()
    {
        return $this->blogUrl;
    }

    /**
     * @param $blogUrl
     * @throws \nickurt\Akismet\Exception\MalformedURLException
     * @return $this
     */
    public function setBlogUrl($blogUrl)
    {
        if( filter_var($blogUrl, FILTER_VALIDATE_URL) === false ) {
            throw new MalformedURLException();
        }

        $this->blogUrl = $blogUrl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCommentType()
    {
        return $this->commentType;
    }

    /**
     * @param $commentType
     * @return $this
     */
    public function setCommentType($commentType)
    {
        $this->commentType = $commentType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCommentAuthor()
    {
        return $this->commentAuthor;
    }

    /**
     * @param $commentAuthor
     * @return $this
     */
    public function setCommentAuthor($commentAuthor)
    {
        $this->commentAuthor = $commentAuthor;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCommentAuthorEmail()
    {
        return $this->commentAuthorEmail;
    }

    /**
     * @param $commentAuthorEmail
     * @return $this
     */
    public function setCommentAuthorEmail($commentAuthorEmail)
    {
        $this->commentAuthorEmail = $commentAuthorEmail;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCommentAuthorUrl()
    {
        return $this->commentAuthorUrl;
    }

    /**
     * @param $commentAuthorUrl
     * @throws \nickurt\Akismet\Exception\MalformedURLException
     * @return $this
     */
    public function setCommentAuthorUrl($commentAuthorUrl)
    {
        if( filter_var($commentAuthorUrl, FILTER_VALIDATE_URL) === false ) {
            throw new MalformedURLException();
        }

        $this->commentAuthorUrl = $commentAuthorUrl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCommentContent()
    {
        return $this->commentContent;
    }

    /**
     * @param $commentContent
     * @return $this
     */
    public function setCommentContent($commentContent)
    {
        $this->commentContent = $commentContent;
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
     * @param $isTest
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
    public function getUserIp()
    {
        return $this->userIp;
    }

    /**
     * @param $userIp
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
     * @param $userAgent
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
     * @param $referrer
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
     * @param $permalink
     * @return $this
     */
    public function setPermalink($permalink)
    {
        $this->permalink = $permalink;
        return $this;
    }

    /**
     * @return bool
     */
    public function validateKey()
    {
        $client = new Client();
        $requestOption = $this->getRequestOption();
        $response = $client->post(sprintf('https://%s/%s/verify-key', $this->getApiBaseUrl(), $this->getApiVersion()), [$requestOption => [
            'key'   => $this->getApiKey(),
            'blog'  => $this->getBlogUrl(),
        ]]);

        return (bool) ($response->getBody() == 'valid');
    }

    /**
     * isSpam
     * @return bool
     */
    public function isSpam()
    {
        $response = $this->getResponseData(
            sprintf('https://%s.%s/%s/comment-check',
                $this->getApiKey(),
                $this->getApiBaseUrl(),
                $this->getApiVersion()
            ));

        return (bool) (trim($response->getBody()) == 'true');
    }

    /**
     * reportSpam
     * @return bool
     */
    public function reportSpam()
    {
        $response = $this->getResponseData(
            sprintf('https://%s.%s/%s/submit-spam',
                $this->getApiKey(),
                $this->getApiBaseUrl(),
                $this->getApiVersion()
            ));

        return (bool) (trim($response->getBody()) == 'Thanks for making the web a better place.');
    }

    /**
     * reportHam
     * @return bool
     */
    public function reportHam()
    {
        $response = $this->getResponseData(
            sprintf('https://%s.%s/%s/submit-ham',
                $this->getApiKey(),
                $this->getApiBaseUrl(),
                $this->getApiVersion()
            ));

        return (bool) (trim($response->getBody()) == 'Thanks for making the web a better place.');
    }

    /**
     * @param $url
     * @throws \Exception
     * @return \GuzzleHttp\Message\FutureResponse|\GuzzleHttp\Message\ResponseInterface|\GuzzleHttp\Ring\Future\FutureInterface|null
     */
    private function getResponseData($url)
    {
        $client = new Client();
        $requestOption = $this->getRequestOption();
        $request = $client->post($url, [$requestOption => $this->toArray()]);

        // Check if the response contains a X-akismet-debug-help header
        if($request->getHeader('X-akismet-debug-help'))
        {
            throw new \Exception($request->getHeader('X-akismet-debug-help'));
        }

        return $request;
    }

    /**
     * @return string
     */
    private function getRequestOption()
    {
        return (version_compare(\GuzzleHttp\ClientInterface::VERSION, '6.0.0', '<')) ? 'body' : 'form_params';
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'user_ip'               =>  $this->getUserIp(),
            'user_agent'            =>  $this->getUserAgent(),
            'referrer'              =>  $this->getReferrer(),
            'permalink'             =>  $this->getPermalink(),
            'comment_type'          =>  $this->getCommentType(),
            'comment_author'        =>  $this->getCommentAuthor(),
            'comment_author_email'  =>  $this->getCommentAuthorEmail(),
            'comment_author_url'    =>  $this->getCommentAuthorUrl(),
            'comment_content'       =>  $this->getCommentContent(),
            'blog'                  =>  $this->getBlogUrl(),
            'is_test'               =>  $this->getIsTest(),
        ];
    }
}
