<?php

namespace nickurt\Akismet\Events;

class ReportHam
{
    /**
     * @var
     */
    public $email;

    /**
     * ReportHam constructor.
     * @param $email
     */
    public function __construct($email)
    {
        $this->email = $email;
    }
}