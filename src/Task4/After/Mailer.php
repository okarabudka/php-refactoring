<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task4\After;

interface Mailer
{
    public function send(string $to, string $subject, string $body): void;
}
