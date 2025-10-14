<?php
require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

$listing = (new App\Builder\ListingBuilder())->make();
$page = max($_GET['page'] ?? 1, 1);
?>

<?php foreach ($listing->listAll($page) as $lis): ?>
<article>
    <?php if (!empty($lis['cover_file_id'])) {
        echo "
            <header>
                <img src=\"/covers/${lis['cover_file_id']}\">
                <hr />
            </header>
        ";
        }
    ?>
    <main>
        <h2><?= $lis['title'] ?></h2>
        <p><?= $lis['description'] ?></p>
    </main>
    <footer>
        <hr />
        <h2><?= $lis['price'] ?></h2>
    </footer>
</article>
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
<?php endif; ?>

