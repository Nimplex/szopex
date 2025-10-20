<nav aria-label="Main navigation">
    <div role="presentation" class="nav-inner">
        <ul>
            <li><a href="/">Strona główna</a></li>
            <li><a href="/listings/all.php">Ogłoszenia</a></li>
        </ul>

        <form class="inline-input" role="search" action="/listings/search.php" method="get">
            <input type="search" id="site-search" name="q" placeholder="Wyszukaj...">
            <button type="submit" aria-label="Wyszukaj">
                <i data-lucide="search" aria-hidden="true"></i>
            </button>
        </form>

        <ul>
            <li>
                <?php
                @session_start();
                if (!isset($_SESSION['user_id'])) {
                    echo <<<HTML
                    <a href="/login.php">Zaloguj się</a>
                    HTML;
                } else {
                    echo <<<HTML
                    <a href="/profile/@me">
                        <i data-lucide="user"></i>
                        Witaj {$_SESSION['user_login']}
                    </a>
                    HTML;
                }
                ?>
            </li>
        </ul>
    </div>
</nav>
