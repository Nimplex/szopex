<?php
$user_item = "<li>";
if (!isset($_SESSION['user_id'])) {
    $user_item .= <<<HTML
    <a href="/login.php">Zaloguj się</a>
    HTML;
} else {
    $user_item .= <<<HTML
    <a href="/profile/@me">
        <i data-lucide="user"></i>
        Witaj {$_SESSION['user_login']}
    </a>
    HTML;
}
$user_item .= "</li>";
?>

<nav aria-label="Main navigation">
    <div role="presentation" class="nav-inner">
        <ul class="desktop">
            <li><a href="/">Strona główna</a></li>
            <li><a href="/listings/all.php">Ogłoszenia</a></li>
        </ul>

        <form class="inline-input" role="search" action="/listings/search.php" method="get">
            <input type="search" id="site-search" name="q" placeholder="Wyszukaj...">
            <button type="submit" aria-label="Wyszukaj">
                <i data-lucide="search" aria-hidden="true"></i>
            </button>
        </form>

        <ul class="desktop">
            <?= $user_item ?>
        </ul>

        <button id="menu-toggle" class="mobile" aria-expanded="false" aria-controls="mobile-container">
          ≡
        </button>
   </div>
    <ul id="mobile-container" hidden>
        <li><a href="/">Strona główna</a></li>
        <li><a href="/listings/all.php">Ogłoszenia</a></li>
        <?= $user_item ?>
    </ul>
</nav>
