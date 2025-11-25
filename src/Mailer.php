<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;

$dotenv = \Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT'] . '/..');
$dotenv->load();

class Mailer
{
    private PHPMailer $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host = $_ENV['SMTP_HOST'];
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $_ENV['SMTP_USER'];
        $this->mail->Password = $_ENV['SMTP_PASS'];
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 587;
        $this->mail->setFrom($_ENV['SMTP_USER'], 'Shopex');
        $this->mail->isHTML(true);
    }

    public function send(string $to, string $subject, string $template, array $vars = []): bool
    {
        $this->mail->clearAddresses();
        $this->mail->addAddress($to);
        $this->mail->Subject = $subject;
        $this->mail->Body = $this->loadTemplate($template, $vars);
        return $this->mail->send();
    }

    private function loadTemplate(string $file, array $vars): string
    {
        $path = $_SERVER['DOCUMENT_ROOT'] . '/../templates/' . $file . '.php';
        if (!file_exists($path)) {
            throw new \RuntimeException("Template not found: $file");
        }
        extract($vars);
        ob_start();
        include $path;
        return ob_get_clean();
    }
}
