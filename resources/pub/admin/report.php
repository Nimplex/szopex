<?php

use App\FlashMessage;

/** @var \App\Controller\UserController $user_controller */
global $user_controller, $_ROUTE;

$id = filter_var($_ROUTE['id'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

if (!isset($id)) {
    (new FlashMessage())->setErr('i18n:invalid_query_parameters');
    header('Location: /admin/reports', true, 303);
    die;
}

$report = $user_controller->reports->find_by_id($id);

if (!isset($report)) {
    require $_SERVER['DOCUMENT_ROOT'] . '/../resources/errors/404.php';
    die;
}

$HEAD = <<<HTML
<style>
table {
    margin-top: 1rem;
}
table, tr, th, td {
    border: 1px solid var(--border-gray);
}

th, td {
    padding: 0.5rem;
    text-align:left;
}
</style>
HTML;

ob_start();
?>

<h1>Zgłoszenie #<?= $report['id'] ?></h1>

<table>
    <tbody>
        <?php foreach ($report as $key => $value): ?>
            <tr>
                <th><?= $key ?></th>
                <td>
                    <?php
                    if ($key == 'reporter_id') {
                        $url = "/profile/{$value}";
                    }
                    if ($key == 'reported_id') {
                        $url = "/profile/{$value}";
                    }
                    if ($key == 'listing_id') {
                        $url = "/listings/{$value}";
                    }
                    if (isset($url)) {
                        echo "<a href=\"{$url}\">{$value}</a>";
                        unset($url);
                    } else {
                        echo htmlspecialchars($value);
                    }
                    ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>

<?php
$CONTENT = ob_get_clean();

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
