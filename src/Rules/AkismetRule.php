<?php

namespace nickurt\Akismet\Rules;

use Illuminate\Contracts\Validation\Rule;

class AkismetRule implements Rule
{
    /**
     * @var
     */
    protected $email;

    /**
     * @var
     */
    protected $author;

    /**
     * Create a new rule instance.
     *
     * @param $email
     * @param $author
     *
     * @return void
     */
    public function __construct($email, $author)
    {
        $this->email = $email;
        $this->author = $author;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('akismet::akismet.it_is_currently_not_possible_to_register_with_your_specified_information_please_try_later_again');
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $akismet = akismet();

        if ($akismet->validateKey()) {
            $akismet->setCommentAuthor($this->author)
                ->setCommentAuthorEmail($this->email)
                ->setCommentType('registration');

            return $akismet->isSpam() ? false : true;
        }

        return true;
    }
}
