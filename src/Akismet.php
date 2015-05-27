<?php

namespace nickurt\Akismet;

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
     * @param $apiKey
     * @param $blogUrl
     */
    public function __construct($apiKey, $blogUrl)
    {
        $this->setApiKey($apiKey);
        $this->setBlogUrl($blogUrl);
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
     * @return $this
     */
    public function setBlogUrl($blogUrl)
    {
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
     * @return $this
     */
    public function setCommentAuthorUrl($commentAuthorUrl)
    {
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
    public function validateKey()
    {
        $client = new \GuzzleHttp\Client();
        $request = $client->post(sprintf('https://%s/%s/verify-key', $this->getApiBaseUrl(), $this->getApiVersion()), ['body' => [
            'key'   => $this->getApiKey(),
            'blog'  => $this->getBlogUrl(),
        ]]);

        return (bool) ($request->getBody() == 'valid');
    }

    /**
     * isSpam
     * @return bool
     */
    public function isSpam()
    {
        $client = new \GuzzleHttp\Client();
        $request = $client->post(sprintf('https://%s.%s/%s/comment-check', $this->getApiKey(), $this->getApiBaseUrl(), $this->getApiVersion()), ['body' => [
            'user_ip'               =>	$_SERVER['REMOTE_ADDR'],
            'user_agent'            =>	$_SERVER['HTTP_USER_AGENT'],
            'referrer'              =>	$_SERVER['HTTP_REFERER'],
//          'permalink'             =>	'',
            'comment_type'          =>	$this->getCommentType(),
            'comment_author'        =>	$this->getCommentAuthor(),
            'comment_author_email'  =>	$this->getCommentAuthorEmail(),
            'comment_author_url'    =>	$this->getCommentAuthorUrl(),
            'comment_content'       =>	$this->getCommentContent(),
            'blog'                  =>  $this->getBlogUrl(),
        ]]);

        return (bool) (trim($request->getBody()) == 'true');
    }

    public function reportSpam()
    {

    }

    public function reportHam()
    {

    }
}