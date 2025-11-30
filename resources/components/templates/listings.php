<?php
use App\Helper\DateHelper;

$listing = (new App\Builder\ListingBuilder())->make();
$page = max($_GET['page'] ?? 1, 1);
$is_partial = $_SERVER['HTTP_PARTIAL_REQ'] ?? null;
?>

<?php foreach ($listing->listAll($page, $_SESSION['user_id']) as $lis): ?>
<article class="<?= $is_partial ? 'settling' : '' ?>">
    <a href="/listings/<?= urlencode($lis['listing_id']) ?>" role="link" aria-label="Zobacz szczegóły ogłoszenia <?= htmlspecialchars($lis['title']) ?>">
        <?php if (!empty($lis['cover_file_id'])):
            $encoded_file = urlencode($lis['cover_file_id']); ?>
        <header>
            <img height="300" width="300" src="/api/storage/covers/<?= $encoded_file ?>" alt="Zdjęcie: <?= htmlspecialchars($lis['title']) ?>" loading="lazy">
        </header>
        <?php endif; ?>

        <main>
            <h2><?= htmlspecialchars($lis['title']) ?></h2>
            <ul>
                <li class="with-icon">
                    <i data-lucide="user" aria-hidden="true"></i>
                    <span><?= htmlspecialchars($lis['display_name']) ?></span>
                </li>
                <li class="with-icon">
                    <i data-lucide="calendar" aria-hidden="true"></i>
                    <time datetime="<?= htmlspecialchars($lis['created_at']) ?>">
                        <?= DateHelper::relative($lis['created_at']) ?>
                    </time>
                </li>
            </ul>
            <span class="small-text">Naciśnij aby wyświetlić więcej szczegółów</span>
        </main>
    </a>
    <span class="vr" role="presentation"></span>
    <footer>
        <h1>
            <?= htmlspecialchars($lis['price']) ?>
        </h1>
        <div>
            <button
                type="button"
                onclick="favourite(event)"
                class="<?= $lis['is_favourited'] ? 'favourited' : '' ?>"
                data-listing-id="<?= urlencode($lis['listing_id']) ?>"
                aria-pressed="<?= $lis['is_favourited'] ? 'true' : 'false' ?>"
                aria-label="<?= sprintf($lis['is_favourited'] ? 'Usuń %s z ulubionych' : 'Dodaj %s do ulubionych', htmlspecialchars($lis['title'])) ?>">
                <i data-lucide="star" aria-hidden="true"></i>
                <span><?= $lis['is_favourited'] ? 'Usuń z ulubionych' : 'Dodaj do ulubionych' ?></span>
            </button>
            <form action="/messages" method="get">
                <input type="hidden" name="new_message" value="t">
                <input type="hidden" name="listing_id" value="<?= urlencode($lis['listing_id']) ?>">
                <button
                    type="submit"
                    class="btn-accent"
                    data-listing-id="<?= urlencode($lis['listing_id']) ?>"
                    aria-label="Skontaktuj się z sprzedającym na temat '<?= htmlspecialchars($lis['title']) ?>'"
                    <?= ($_SESSION['user_id'] == $lis['user_id']) ? 'disabled' : ''?>>
                    <i data-lucide="message-circle" aria-hidden="true"></i>
                    <span><?= ($_SESSION['user_id'] == $lis['user_id']) ? 'Nie możesz napisać sam do siebie' : 'Napisz do ogłoszeniodawcy' ?></span>
                </button>
            </form>
        </div>
    </footer>
</article>
<?php endforeach; ?>

<?php if (!empty($listing->listAll($page + 1, $_SESSION['user_id']))): ?>
<div id="sentinel" data-next-page="<?= $page + 1 ?>" role="status" aria-live="polite" aria-label="Ładowanie kolejnych ogłoszeń"></div>
<div id="throbber" aria-hidden="true">Wczytywanie...</div>
<noscript>
    <div id="next-page">
        <a class="btn-primary" href="?page=<?= $page + 1 ?>">Następna strona</a>
    </div>
</noscript>
<?php else: ?>
<div class="content-end" role="status">
    <i class="big-icon" data-lucide="package-open" aria-hidden="true"></i>
    <p>Nic więcej tu nie ma!</p>
</div>
<?php endif; ?>
