<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task4\After;

/**
 * A registration request that has passed validation.
 */
final readonly class Registration
{
    public const int MIN_PASSWORD_LENGTH = 8;

    public function __construct(
        public string $email,
        #[\SensitiveParameter]
        public string $password,
    ) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw InvalidRegistration::invalidEmail();
        }
        if (strlen($password) < self::MIN_PASSWORD_LENGTH) {
            throw InvalidRegistration::passwordTooShort();
        }
    }

    /** @param array<mixed> $data form input with "email" and "password" */
    public static function fromArray(array $data): self
    {
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        return new self(is_string($email) ? $email : '', is_string($password) ? $password : '');
    }
}
