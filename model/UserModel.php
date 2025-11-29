<?php
class UserModel
{
    protected $database;

    public function __construct()
    {
        return $this->database = Database::getInstance();
    }

    public function createNewUser($username, $password, $email)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        return Database::execute("INSERT INTO `users` (username, password, email)
        VALUES (?, ?, ?)", [$username, $hashedPassword, $email]);
    }

    public function getUserByEmail($email)
    {
        $stmt = Database::execute("SELECT * FROM `users` WHERE email = ?", [$email]);
        return $stmt->fetch();
    }

    public function verifyUser($email, $password)
    {
        $user = $this->getUserByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}
