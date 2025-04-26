<?php

namespace src\refactors\task4\after\Notifications;

use src\refactors\task4\after\Model\Model;

interface Notification
{
    public function send(string $from, string $to, string $subject, string $message):void;
}
