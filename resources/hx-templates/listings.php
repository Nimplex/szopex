<?php
require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

$listing = (new App\Builder\ListingBuilder())->make();
$page = max($_GET['page'] ?? 1, 1);
?>

<?php foreach ($listing->listAll($page) as $lis): ?>
<a class="offer-card" href="/listings/view.php?listing=<?= urlencode($lis['listing_id']) ?>" role="link">
    <article>
        <?php if (!empty($lis['cover_file_id'])):
            $encoded_file = urlencode($lis['cover_file_id']); ?>
            <header>
                <img height="300" src="/covers.php?file=<?= $encoded_file ?>" alt="<?= htmlspecialchars($lis['title']) ?>">
            </header>
        <?php endif; ?>

        <main>
            <h2><?= htmlspecialchars($lis['title']) ?></h2>
            <ul>
                <li><p><?= htmlspecialchars($lis['display_name']) ?></p></li>
                <li><p><?= htmlspecialchars($lis['created_at']) ?></p></li>
            </ul>
            <span class="small-text">Naciśnij aby wyświetlić więcej szczegółów</span>
        </main>
        <span class="vr"></span>
        <footer>
            <h1><?= htmlspecialchars($lis['price']) ?></h1>
            <div>
                <button
                    type="button"
                    onclick="event.preventDefault(); event.stopPropagation();"
                    aria-label="Dodaj '<?= htmlspecialchars($lis['title']) ?>' do ulubionych">
                        Dodaj do ulubionych
                </button>
                <button
                    type="button"
                    class="btn-accent"
                    onclick="event.preventDefault(); event.stopPropagation();"
                    aria-label="Skontaktuj się z sprzedającym na temat '<?= htmlspecialchars($lis['title']) ?>'">
                        Napisz do ogłoszeniodawcy
                </button>
            </div>
        </footer>
    </article>
</a>
<?php endforeach; ?>
<?php if (!empty($listing->listAll($page + 1))): ?>
<div
    class="target"
    aria-hidden="true"
    hx-get="?page=<?= $page + 1 ?>"
    hx-target="this"
    hx-swap="outerHTML"
    hx-trigger="revealed"
    hx-indicator="#throbber"
></div>
<?php else: ?>
<div class="content-end">
    &mdash; Nic więcej tu nie ma! &mdash;
</div>
<?php endif; ?>

