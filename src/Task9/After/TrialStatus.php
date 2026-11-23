<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task9\After;

enum TrialStatus: string
{
    case Active = 'active';
    case Ending = 'ending';
    case Expired = 'expired';
    case Unknown = 'unknown';
}
