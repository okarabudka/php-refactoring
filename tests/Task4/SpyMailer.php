<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Tests\Task4;

use Okarabudka\Refactoring\Task4\After\Mailer;

final class SpyMailer implements Mailer
{
    /** @var list<array{to: string, subject: string, body: string}> */
    public array $sent = [];

    public function send(string $to, string $subject, string $body): void
    {
        $this->sent[] = ['to' => $to, 'subject' => $subject, 'body' => $body];
    }
}
