<?php
use App\Helper\DateHelper;

$listing = (new App\Builder\ListingBuilder())->make();
$page = max($_GET['page'] ?? 1, 1);
$is_raw = $_SERVER['HTTP_RAW_REQUEST'] ?? null;
?>

<?php foreach ($listing->listAll($page, $_SESSION['user_id']) as $lis): ?>
<article class="<?= $is_raw ? 'settling' : '' ?>">
    <a href="/listings/<?= urlencode($lis['listing_id']) ?>" role="link" aria-label="Zobacz szczegóły ogłoszenia <?= htmlspecialchars($lis['title']) ?>">
        <?php if (!empty($lis['cover_file_id'])):
            $encoded_file = urlencode($lis['cover_file_id']); ?>
        <header>
            <img height="300" width="300" src="/storage/covers/<?= $encoded_file ?>" alt="Zdjęcie: <?= htmlspecialchars($lis['title']) ?>" loading="lazy">
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
                onclick="event.preventDefault(); event.stopPropagation(); favourite(event)"
                class="<?= $lis['is_favourited'] ? 'btn-red' : '' ?>"
                data-listing-id="<?= urlencode($lis['listing_id']) ?>"
                aria-pressed="<?= $lis['is_favourited'] ? 'true' : 'false' ?>"
                aria-label="<?= sprintf($lis['is_favourited'] ? 'Usuń %s z ulubionych' : 'Dodaj %s do ulubionych', htmlspecialchars($lis['title'])) ?>">
                <i data-lucide="star" aria-hidden="true"></i>
                <span><?= $lis['is_favourited'] ? 'Usuń z ulubionych' : 'Dodaj do ulubionych' ?></span>
            </button>
            <button
                type="button"
                class="btn-accent"
                onclick="event.preventDefault(); event.stopPropagation(); message(event)"
                data-listing-id="<?= urlencode($lis['listing_id']) ?>"
                aria-label="Skontaktuj się z sprzedającym na temat '<?= htmlspecialchars($lis['title']) ?>'">
                <i data-lucide="message-circle" aria-hidden="true"></i>
                <span>Napisz do ogłoszeniodawcy</span>
            </button>
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
    <i data-lucide="inbox" aria-hidden="true"></i>
    <p>Nic więcej tu nie ma!</p>
</div>
<?php endif; ?>
