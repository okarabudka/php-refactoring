<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task9\Before;

use DateTime;
use Okarabudka\Refactoring\Legacy\Database;

class TrialService
{
    public function status($userId)
    {
        $row = Database::connection()->query('SELECT trial_started_at FROM users WHERE id = ' . $userId)->fetch();
        if (!$row) {
            return 'unknown';
        }

        $started = new DateTime($row['trial_started_at']);
        $days = (new DateTime())->diff($started)->days;

        if ($days > Config::$trialDays) {
            Logger::log("Trial expired for user $userId");
            return 'expired';
        }
        if ($days >= Config::$trialDays - 3) {
            return 'ending';
        }
        return 'active';
    }
}
