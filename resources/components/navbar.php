<style>
nav {
}

nav > ul {
    display: flex;
    justify-content: space-between;
}
</style>

<nav aria-label="Main navigation">
  <ul>
    <li><a href="/">Strona główna</a></li>
    <li><a href="/listings/all.php">Ogłoszenia</a></li>
    <li>
        <?php
        @session_start();
        if (!isset($_SESSION['user_id'])) {
            echo '<a href="/login.php">Zaloguj się</a>';
        } else {
            echo "<a href=\"/profile/@me\">Witaj {$_SESSION['user_login']}</a>";
        }
        ?>
    </li>
  </ul>
</nav>
