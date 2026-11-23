<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task4\After;

use Psr\Clock\ClockInterface;

/**
 * Thin application service: validation, persistence and mail are behind collaborators.
 */
final readonly class UserManager
{
    public function __construct(
        private UserRepository $users,
        private Mailer $mailer,
        private ClockInterface $clock,
    ) {}

    /** @param array<mixed> $data form input with "email" and "password" */
    public function register(array $data): void
    {
        $user = User::register(Registration::fromArray($data), $this->clock->now());

        $this->users->save($user);
        $this->mailer->send($user->email, 'Welcome!', 'Thanks for registering.');
    }
}
