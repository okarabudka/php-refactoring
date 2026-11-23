<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task4\After;

use PDO;

final readonly class PdoUserRepository implements UserRepository
{
    public function __construct(private PDO $pdo) {}

    public function save(User $user): void
    {
        $this->pdo
            ->prepare('INSERT INTO users (email, password, created_at) VALUES (?, ?, ?)')
            ->execute([$user->email, $user->passwordHash, $user->createdAt->format('Y-m-d H:i:s')]);
    }
}
