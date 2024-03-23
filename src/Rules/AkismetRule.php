<?php

namespace nickurt\Akismet\Rules;

use Illuminate\Contracts\Validation\Rule;

class AkismetRule implements Rule
{
    /** @var string */
    protected $email;

    /** @var string */
    protected $author;

    /**
     * @param  string  $email
     * @param  string  $author
     */
    public function __construct($email, $author)
    {
        $this->email = $email;
        $this->author = $author;
    }

    /**
     * @return array|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function message()
    {
        return trans('akismet::akismet.it_is_currently_not_possible_to_register_with_your_specified_information_please_try_later_again');
    }

    /**
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        /** @var \nickurt\Akismet\Akismet $akismet */
        $akismet = \Akismet::getFacadeRoot();

        if ($akismet->validateKey()) {
            $akismet->setCommentAuthor($this->author)
                ->setCommentAuthorEmail($this->email)
                ->setCommentType('registration');

            return $akismet->isSpam() ? false : true;
        }

        return true;
    }
}
