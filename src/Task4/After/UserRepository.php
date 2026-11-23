<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task4\After;

interface UserRepository
{
    public function save(User $user): void;
}
