<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task4\After;

use DateTimeImmutable;

final readonly class User
{
    private function __construct(
        public string $email,
        public string $passwordHash,
        public DateTimeImmutable $createdAt,
    ) {}

    public static function register(Registration $registration, DateTimeImmutable $now): self
    {
        return new self($registration->email, password_hash($registration->password, PASSWORD_BCRYPT), $now);
    }
}
