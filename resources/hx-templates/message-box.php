<?php
$lookup_table = [
    'Err' => 'Błąd',
    'Ok' => 'Sukces'
];

$msg = new App\FlashMessage();
if (!$msg->exists()) {
    return;
}

$title = $lookup_table[$msg->peekType()->name];
?>

<div id="toast" class="toast-<?= $msg->peekType()->name; ?>">
    <h4><?= $title ?></h4>
    <hr>
    <?= $msg->getMsg(); ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const toast = document.getElementById('toast');
    if (toast) {
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 5000);
    }
});
</script>
