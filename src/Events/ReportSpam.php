<?php

namespace nickurt\Akismet\Events;

class ReportSpam
{
    /** @var string */
    public $email;

    /**
     * @param string $email
     */
    public function __construct($email)
    {
        $this->email = $email;
    }
}