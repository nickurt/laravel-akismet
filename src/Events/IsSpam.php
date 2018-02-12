<?php

namespace nickurt\Akismet\Events;

class IsSpam
{
    /**
     * @var
     */
    public $email;

    /**
     * IsSpam constructor.
     * @param $email
     */
    public function __construct($email)
    {
        $this->email = $email;
    }
}