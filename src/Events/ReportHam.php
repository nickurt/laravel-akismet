<?php

namespace nickurt\Akismet\Events;

class ReportHam
{
    /** @var string */
    public $email;

    /**
     * @param  string  $email
     */
    public function __construct($email)
    {
        $this->email = $email;
    }
}
