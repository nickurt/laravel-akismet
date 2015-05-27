<?php

namespace nickurt\Akismet;

class Akismet {

    protected $apiBaseUrl = 'rest.akismet.com';
    protected $apiVersion = '1.1';
    protected $apiKey;
    protected $blogUrl;

    public function __construct($apiKey, $blogUrl)
    {
        $this->setApiKey($apiKey);
        $this->setBlogUrl($blogUrl);
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function setBlogUrl($blogUrl)
    {
        $this->blogUrl = $blogUrl;
    }

    public function setCommentAuthor($commentAuthor)
    {
        $this->commentAuthor = $commentAuthor;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function getApiBaseUrl()
    {
        return $this->apiBaseUrl;
    }

    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    public function getBlogUrl()
    {
        return $this->blogUrl;
    }

    public function getCommentAuthor()
    {
        return $this->commentAuthor;
    }

    public function validateKey()
    {
        $client = new \GuzzleHttp\Client();
        $request = $client->post(sprintf('https://%s/%s/verify-key', $this->getApiBaseUrl(), $this->getApiVersion()), ['body' => [
            'key'   => $this->getApiKey(),
            'blog'  => $this->getBlogUrl(),
        ]]);

        return (bool) ($request->getBody() == 'valid');
    }

    public function isSpam()
    {
        $client = new \GuzzleHttp\Client();
        $request = $client->post(sprintf('https://%s.%s/%s/comment-check', $this->getApiKey(), $this->getApiBaseUrl(), $this->getApiVersion()), ['body' => [
            'user_ip'				=>	$_SERVER['REMOTE_ADDR'],
            'user_agent'			=>	$_SERVER['HTTP_USER_AGENT'],
            'referrer'				=>	$_SERVER['HTTP_REFERER'],
//          'permalink'				=>	'',
//          'comment_type'			=>	'',
            'comment_author'		=>	$this->getCommentAuthor(),
//          'comment_author_email'	=>	'',
//          'comment_author_url'	=>	'',
//          'comment_content'		=>	'',
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