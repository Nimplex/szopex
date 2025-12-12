<?php

namespace App\Controller;

use App\Mailer;
use App\Model\Activation;
use App\Model\Favourites;
use App\Model\Reports;
use App\Model\User;
use PDO;

class UserController
{
    public User $user;
    public Activation $activation;
    public Favourites $favourites;
    public Reports $reports;
    public const int MAX_PFP_FILE_SIZE = 5_000_000;
    public const string PASSWORD_PATTERN = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';


    public function __construct(PDO $db)
    {
        $this->user = new User($db);
        $this->activation = new Activation($db);
        $this->favourites = new Favourites($db);
        $this->reports = new Reports($db);
    }

    /**
     * This function checks if the password has:
     * - at least 1 _lowercase letter_ (a-z),
     * - at least 1 _uppercase letter_ (A-Z),
     * - at least 1 _symbol_ [@, $, !, %, *, ?, &],
     * - 8 or more of the characters described above
     */
    private function _check_password_complexity(string $password): bool
    {
        if (!preg_match(UserController::PASSWORD_PATTERN, $password)) {
            return false;
        }

        return true;
    }

    /**
     * @param array<string,string|null> $image
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function set_profile_picture(int $user_id, array $image): void
    {
        if (empty($image)) {
            throw new \InvalidArgumentException("No image provided");
        }

        $err = $image['error'];
        
        if ($err !== UPLOAD_ERR_OK) {
            throw new \RuntimeException("Upload error (code: $err)");
        }

        [
            'tmp_name' => $tmp_name,
            'size' => $size,
        ] = $image;

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($tmp_name);
        $allowed = ['image/jpeg' => '.jpg', 'image/png' => '.png'];

        if (!isset($allowed[$mime])) {
            throw new \InvalidArgumentException('Unsupported file type');
        }

        if ($size <= 0 || $size > UserController::MAX_PFP_FILE_SIZE) {
            throw new \InvalidArgumentException('Invalid file size');
        }

        $new_name = 'pfp_' . $user_id . $allowed[$mime];

        $target = $_SERVER['DOCUMENT_ROOT'] . '/../storage/profile-pictures/' . $new_name;

        if (!move_uploaded_file($tmp_name, $target)) {
            throw new \RuntimeException('Failed to move uploaded file');
        }

        $stmt = $this->db->prepare(
            'INSERT INTO profile_pictures (user_id, file_id) VALUES (:user_id, :file_id)'
        );

        $res = $stmt->execute([
            ':user_id' => $user_id,
            ':file_id' => $new_name,
        ]);

        if (!$res) {
            throw new \RuntimeException("Couldn't insert a record into the database");
        }
    }

    /**
     * @param array<string,string|null> $request
     * @return int User ID
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function register_from_request(array $request): int
    {
        $login = $request['login'] ?? null;
        $display_name = filter_var($request['display_name'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_var($request['email'] ?? null, FILTER_VALIDATE_EMAIL);
        $password = $request['password'] ?? null;

        if (!isset($login) || !isset($display_name) || !isset($email) || !isset($password)) {
            throw new \InvalidArgumentException('i18n:missing_parameters', 1);
        }

        $login = strtolower(trim($login));
        $display_name = trim($display_name);
        $email = strtolower(trim($email));
        $password = trim($password);

        if (!$this->_check_password_complexity($password)) {
            throw new \InvalidArgumentException('i18n:password_too_weak', 1);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('i18n:invalid_email', 1);
        }

        $res = $this->user->create($login, $display_name, $email, $password);

        if (!$res) {
            throw new \Exception('Database exception occured');
        }

        $token = $this->activation->create($res);
        $host = $_SERVER['SERVER_NAME'];
        $protocol = ($host == 'localhost' || $host == '127.0.0.1') ? 'http' : 'https';
        $mailer = new Mailer();
        $mailer->send(
            $email,
            'Potwierdzenie rejestracji',
            'activation',
            [
                'name' => $display_name,
                'login' => $login,
                'link' => "$protocol://$host/api/activate/$res/$token"
            ],
        );

        return $res;
    }

    /**
     * @param array<string,string|null> $request
     * @throws \InvalidArgumentException
     */
    public function login_from_request(array $request): void
    {
        $email = $request['email'] ?? null;
        $password = $request['password'] ?? null;

        if (!isset($email) || !isset($password)) {
            throw new \InvalidArgumentException('i18n:missing_parameters');
        }

        $email = strtolower(trim($email));
        $password = trim($password);

        $user = null;
 
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $user = $this->user->find_by_login($email);
        } else {
            $user = $this->user->find_by_email($email);
        }

        if (!$user) {
            throw new \InvalidArgumentException('i18n:invalid_account', 1);
        }

        if (!password_verify($password, $user['password_hash'])) {
            throw new \InvalidArgumentException('i18n:invalid_account', 1);
        }

        if (!$user['active']) {
            $activation = $this->activation->find_by_user_id($user['id']);

            $res = null;

            if (!$activation) {
                $res = $this->activation->create($user['id']);
            } elseif ($activation['expired']) {
                $res = $this->activation->regenerate_expired($activation['id']);
            }

            if (!$res) {
                throw new \InvalidArgumentException('i18n:account_not_activated', 1);
            }

            $user_id = $user['id'];
            $host = $_SERVER['SERVER_NAME'];
            $protocol = ($host == 'localhost' || $host == '127.0.0.1') ? 'http' : 'https';
            $mailer = new Mailer();
            $mailer->send(
                $user['email'],
                'Potwierdzenie rejestracji',
                'activation',
                [
                    'name' => $user['display_name'],
                    'login' => $user['login'],
                    'link' => "$protocol://$host/api/activate/$user_id/$res"
                ],
            );

            throw new \InvalidArgumentException('i18n:resent_code', 1);
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_display_name'] = $user['display_name'];
        $_SESSION['user_login'] = $user['login'];
    }

    /**
     * @param array<string,string|null> $request
     * @return bool Success
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function activate_from_request(array $request): bool
    {
        $id = filter_var($request['id'] ?? null, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        $token = filter_var($request['token'] ?? null, FILTER_SANITIZE_SPECIAL_CHARS);

        if (!isset($id) || !isset($token)) {
            throw new \InvalidArgumentException('i18n:missing_parameters', 1);
        }
    
        $id = trim($id);
        $token = trim($token);

        $activation = $this->activation->find_by_token($token);

        if (!$activation || $activation['user_id'] != $id) {
            throw new \InvalidArgumentException('i18n:activation:invalid_url', 1);
        }

        if ($activation['expired']) {
            $this->activation->delete($activation['id']);
            throw new \InvalidArgumentException('i18n:activation:expired_activation', 1);
        }

        return $this->activation->activate($activation['id']);
    }

    /**
     * @param array<string,string|null> $request
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function favourite_from_request(array $request): bool
    {
        if (!isset($_SESSION['user_id'])) {
            throw new \Exception('i18n:not_logged_in', 1);
        }

        $listing_id = filter_var($request['listing_id'] ?? null, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

        if (!isset($listing_id)) {
            throw new \InvalidArgumentException('i18n:missing_parameters', 1);
        }

        $existing = $this->favourites->exists($listing_id, $_SESSION['user_id']);

        if (!empty($existing)) {
            $this->favourites->delete($existing['id']);
            return false;
        }

        $this->favourites->create($listing_id, $_SESSION['user_id']);
        return true;
    }
}
