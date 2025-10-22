<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/../src/WebpackManifest.php';
use App\WebpackManifest;

$title = 'Logowanie';
$no_navbar = true;

function render_head(): string
{
    $style_path = WebpackManifest::asset('login.css');
    return <<<HTML
    <link rel="stylesheet" href="{$style_path}">
    HTML;
}

function render_content(): string
{
    return <<<HTML
    <div role="presentation" id="inner-container">
        <form action="/api/login" method="POST">
            <h1>Zaloguj się</h1>
            <label>E-mail: <input type="email" name="email" placeholder="E-mail lub login" maxlength="255" required></label><br>
            <label>Hasło: <input type="password" name="password" placeholder="Hasło" minlength="8" required></label><br>
            <input type="submit" value="Zaloguj się">
        </form>
        <span class="vr"></span>
        <div class="right">
            <h2>Nie posiadasz jeszcze konta?</h2>
            <div>
                <form action="/register.php" method="get">
                    <input type="submit" value="Zarejestruj się!">
                </form>
                <footer role="contentinfo">
                    <p class="small-text">
                        Korzystając z serwisu, akceptujesz 
                        <a href="/terms-of-use">Regulamin</a> i 
                        <a href="/privacy-policy">Politykę prywatności</a>.
                    </p>
                </footer>
            </div>
        </div>
    </div>
    HTML;
}

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
