<?php

namespace nickurt\Akismet\Events;

class ReportSpam
{
    /**
     * @var
     */
    public $email;

    /**
     * ReportSpam constructor.
     * @param $email
     */
    public function __construct($email)
    {
        $this->email = $email;
    }
}