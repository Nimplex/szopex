<?php
/** @var array<string,mixed> $SIDEBAR_CFG */
/*

SIDEBAR_CFG structure:

[
    'header' => string
    'groups' => [[name, dest, icon_name]]
    'group_names' => [string]
]

*/
?>

<section id="sidebar">
    <h2><?= $SIDEBAR_CFG['title'] ?></h2>
    <?php foreach ($SIDEBAR_CFG['groups'] as $index => $group): ?>
    <?= isset($SIDEBAR_CFG['group_names'][$index])
        ? "<span class=\"group-title\">{$SIDEBAR_CFG['group_names'][$index]}</span>"
        : ''
        ?>
    <ul>
        <?php foreach ($group as $page): ?>
        <li<?= $page[1] == $SIDEBAR_CFG['selected'] ? ' class="selected"' : '' ?>>
            <?php # [icon, page_name, page_dest]?>
            <a href="<?= $page[1] ?>">
                <i data-lucide="<?= $page[2] ?>" aria-hidden="true"></i>
                <?= $page[0] ?>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php endforeach; ?>
</section>
<div class="vr"></div>
