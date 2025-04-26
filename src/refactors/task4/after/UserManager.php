<?php

namespace src\refactors\task4\after;

use src\refactors\task4\after\Notifications\Notification;
use src\refactors\task4\after\Repository\UserRepository;
use src\refactors\task4\after\Validator\Validator;

class UserManager
{
    public function __construct(
        private Notification $notification,
        private UserRepository $userRepository,
        private Validator $validator
    )
    {}

    public function register(array $data): void {
        //TODO logic here
        //$validator->validate()
        //$repository->save()
        //$notification->send()
    }
}
