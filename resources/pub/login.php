<?php

$TITLE = 'Logowanie';
$NO_NAVBAR = true;
$HEAD = '<link rel="stylesheet" href="/_dist/css/login.css">';

ob_start();
?>

<div role="presentation" id="inner-container">
    <form action="/api/login" method="POST">
        <h1>Zaloguj się</h1>
        <?= $_GET['redir'] ? sprintf('<input type="hidden" name="redir" value="%s">', $_GET['redir']) : '' ?>
        <label>E-mail: <input type="email" name="email" placeholder="E-mail lub login" maxlength="255" required></label><br>
        <label>Hasło: <input type="password" name="password" placeholder="Hasło" minlength="8" required></label><br>
        <input type="submit" value="Zaloguj się">
    </form>
    <span class="vr"></span>
    <div class="right">
        <h2>Nie posiadasz jeszcze konta?</h2>
        <div>
            <form action="/register" method="GET">
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

<?php
$CONTENT = ob_get_clean();

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
