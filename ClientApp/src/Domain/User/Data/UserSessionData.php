<?php

namespace App\Domain\User\Data;

/**
 * User session data.
 */
class UserSessionData
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $surname;

    /** @var string */
    public $email;

    /** @var string */
    public $locale;

    /** @var string */
    public $token;

    /** @var int */
    public $validateTo;
}
