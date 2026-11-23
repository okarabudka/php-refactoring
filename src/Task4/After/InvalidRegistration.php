<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task4\After;

use DomainException;

final class InvalidRegistration extends DomainException
{
    public static function invalidEmail(): self
    {
        return new self('Invalid email');
    }

    public static function passwordTooShort(): self
    {
        return new self('Password too short');
    }
}
