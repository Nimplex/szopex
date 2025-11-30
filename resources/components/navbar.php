<?php
$user_item = "";

if (!isset($_SESSION['user_id'])) {
    $user_item .= <<<HTML
    <li>
        <a href="/login">Zaloguj się</a>
    </li>
    HTML;
} else {
    $user_login = htmlspecialchars($_SESSION['user_login']);
    $user_item .= <<<HTML
    <li>
        <a href="/listings">
            <i data-lucide="package" aria-hidden="true"></i>
            Ogłoszenia
        </a>
    </li>
    <li>
        <a href="/profile/favourites">
            <i data-lucide="star" aria-hidden="true"></i>
            Polubione
        </a>
    </li>
    <li>
        <a href="/messages">
            <i data-lucide="message-circle" aria-hidden="true"></i>
            Wiadomości
        </a>
    </li>
    <li>
        <a href="/profile/{$_SESSION['user_id']}">
            <i data-lucide="user" aria-hidden="true"></i>
            Witaj {$user_login}
        </a>
    </li>
    HTML;
}
?>

<nav aria-label="Main navigation">
    <div role="presentation" class="nav-inner">
        <ul class="desktop">
            <li><a href="/"><img src="/_assets/thumbnail.png" height="40"></a></li>
        </ul>
        <form class="inline-input" role="search" action="/listings/search.php" method="get">
            <input type="search" id="site-search" name="q" placeholder="Wyszukaj...">
            <button type="submit" aria-label="Wyszukaj">
                <i data-lucide="search" aria-hidden="true"></i>
            </button>
        </form>
        <ul class="desktop"><?= $user_item ?></ul>
        <button id="menu-toggle" class="mobile" aria-expanded="false" aria-controls="mobile-container">≡</button>
    </div>
    <ul id="mobile-container" hidden>
        <li>
            <a href="/">
                <i data-lucide="house" aria-hidden="true"></i>
                Strona główna
            </a>
        </li>
        <?= $user_item ?>
    </ul>
</nav>
