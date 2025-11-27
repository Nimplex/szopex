<?php

$title = 'Rejestracja';
$no_navbar = true;

$render_head = function (): string {
    return <<<HTML
    <link rel="stylesheet" href="/_dist/css/register.css">
    HTML;
};

$render_content = function (): string {
    return <<<HTML
    <form action="/api/register" method="POST">
        <h1>Zarejestruj się</h1>
        <label>Nazwa użytkownika: <input type="text" name="display_name" placeholder="Nazwa wyświetlana" maxlength="255" required></label><br>
        <label>Login: <input type="text" name="login" placeholder="Login" maxlength="255" required></label><br>
        <label>E-mail: <input type="email" name="email" placeholder="E-mail" maxlength="255" required></label><br>
        <label>Hasło: <input type="password" name="password" placeholder="Hasło" minlength="8" required></label><br>
        <input type="submit" value="Zarejestruj się">
        <footer role="contentinfo">
            <p class="small-text">
                Korzystając z serwisu, akceptujesz
                <a href="/terms-of-use">Regulamin</a> i
                <a href="/privacy-policy">Politykę prywatności</a>.
            </p>
        </footer>
    </form>
    HTML;
};

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
