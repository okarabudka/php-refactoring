<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task4\After;

use RuntimeException;

final class NativeMailer implements Mailer
{
    public function send(string $to, string $subject, string $body): void
    {
        if (!mail($to, $subject, $body)) {
            throw new RuntimeException("Mail to {$to} was not accepted for delivery.");
        }
    }
}
