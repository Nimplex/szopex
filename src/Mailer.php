<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    private PHPMailer $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host = $_ENV['SMTP_HOST'];
        $this->mail->SMTPAuth = isset($_ENV['SMTP_PASS']);
        $this->mail->Username = $_ENV['SMTP_USER'];
        $this->mail->Password = $_ENV['SMTP_PASS'];
        if (isset($_ENV['SMTP_TLS'])) {
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }
        $this->mail->Port = isset($_ENV['SMTP_TLS']) ? 587 : 25;
        $this->mail->setFrom($_ENV['SMTP_USER'], 'Shopex');
        $this->mail->isHTML(true);
        $this->mail->SMTPKeepAlive = true;
    }

    public function send(string $to, string $subject, string $template, array $vars = [], bool $cli = false): bool
    {
        $this->mail->clearAddresses();
        $this->mail->addAddress($to);
        $this->mail->Subject = $subject;
        $this->mail->Body = $this->loadTemplate($template, $vars, $cli);
        return $this->mail->send();
    }

    private function loadTemplate(string $file, array $vars, bool $cli = false): string
    {
        $path = $cli ? getcwd() . "/templates/{$file}.php" : $_SERVER['DOCUMENT_ROOT'] . '/../templates/' . $file . '.php';
        if (!file_exists($path)) {
            throw new \RuntimeException("Template not found: $file");
        }
        extract($vars);
        ob_start();
        include $path;
        return ob_get_clean();
    }
}
