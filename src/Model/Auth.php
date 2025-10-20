<?php

namespace App\Model;

use PDO;

class Auth extends BaseDBModel
{
    private const MIN_PASS_LENGTH = 8;
    
    private function _findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private function _login_repeats(string $login): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM users WHERE login = ?');
        $stmt->execute([$login]);
        return !empty($stmt->fetch(PDO::FETCH_ASSOC) ?: null);
    }

    private function _email_repeats(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return !empty($stmt->fetch(PDO::FETCH_ASSOC) ?: null);
    }

    private function _create(string $login, string $display_name, string $email, string $password): bool
    {
        if ($this->_login_repeats($login)) {
            throw new \InvalidArgumentException('Ten login już jest w użyciu', 1);
        }

        if ($this->_email_repeats($email)) {
            throw new \InvalidArgumentException('Ten adres e-mail już jest w użyciu', 1);
        }
        
        $stmt = $this->db->prepare('INSERT INTO users (login, display_name, email, password_hash) VALUES (:login, :display_name, :email, :password_hash)');

        // https://www.php.net/manual/en/function.password-hash.php
        // I decided that ARGON2ID is stronger than bcrypt
        $hash = password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 1 << 16,
            'time_cost' => 4,
            'threads' => 2,
        ]);

        return $stmt->execute([
            ':login' => $login,
            ':display_name' => $display_name,
            ':email' => $email,
            ':password_hash' => $hash,
        ]);
    }

    // --- public methods ---

    /**
     * @throws \InvalidArgumentException
     */
    public function register(string $login, string $display_name, string $email, string $password): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Nieprawidłowy adres e-mail', 1);
        }
        
        if (strlen($password) < Auth::MIN_PASS_LENGTH) {
            throw new \InvalidArgumentException("Hasło musi składać się z conajmniej ${Auth::MIN_PASS_LENGTH} znaków", 1);
        }

        return $this->_create($login, $display_name, $email, $password);
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function login(string $email, string $password): array
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Nieprawidłowy adres e-mail', 1);
        }

        $user = $this->_findByEmail($email);

        if ($user && password_verify($password, $user['password_hash'])) {
            return [
                'login' => $user['login'],
                'email' => $user['email'],
                'display_name' => $user['display_name'],
                'id' => $user['id'],
            ];
        }

        throw new \InvalidArgumentException('Nieprawidłowy e-mail lub hasło', 1);
    }

    /**
     * @param array<int,string|null> $request
     * @throws \InvalidArgumentException
     */
    public function register_from_request(array $request): void
    {
        $login = $request['login'] ?? null;
        $display_name = $request['display_name'] ?? null;
        $email = $request['email'] ?? null;
        $password = $request['password'] ?? null;

        if (!$login || !$display_name || !$email || !$password) {
            throw new \InvalidArgumentException('Missing fields');
        }

        $this->register($login, $display_name, $email, $password);
    }

    /**
     * @param array<int,string|null> $request
     * @throws \InvalidArgumentException
     */
    public function login_from_request(array $request): void
    {
        $email = $request['email'] ?? null;
        $password = $request['password'] ?? null;

        $res = $this->login($email, $password);

        $_SESSION['user_id'] = $res['id'];
        $_SESSION['user_email'] = $res['email'];
        $_SESSION['user_display_name'] = $res['display_name'];
        $_SESSION['user_login'] = $res['login'];
    }
}
