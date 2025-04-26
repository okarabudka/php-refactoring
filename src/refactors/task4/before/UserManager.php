<?php

namespace src\refactors\task4\before;

use Exception;
use stdClass;

class UserManager
{
    public function register(array $data): void {
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email");
        }

        if (strlen($data['password']) < 8) {
            throw new Exception("Password too short");
        }

        $user = new stdClass();
        $user->email = $data['email'];
        $user->password = password_hash($data['password'], PASSWORD_BCRYPT);
        $user->created_at = date('Y-m-d H:i:s');

        $pdo = new PDO('mysql:host=localhost;dbname=test', 'root', '');
        $stmt = $pdo->prepare('INSERT INTO users (email, password, created_at) VALUES (?, ?, ?)');
        $stmt->execute([$user->email, $user->password, $user->created_at]);

        mail($user->email, "Welcome!", "Thanks for registering.");
    }
}
