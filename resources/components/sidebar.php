<?php
/** @var array<string,mixed> $SIDEBAR_CFG */
/*

SIDEBAR_CFG structure:

[
    'type' => 'link' | 'raw'

    link:
    'header' => string
    'groups' => [[name, dest, icon_name]]
    'group_names' => [string]

    raw:
    'content' => string
]

*/
?>

<?php if ($SIDEBAR_CFG['type'] === 'link'): ?>

<div id="sidebar-bg-fadeout" onclick="window.closeSidebar();"></div>
<section id="sidebar" hidden>
    <div id="sidebar-heading">
        <h2><?= $SIDEBAR_CFG['title'] ?></h2>
        <button id="sidebar-close-button" type="button" onclick="window.closeSidebar();">×</button>
    </div>
    <?php foreach ($SIDEBAR_CFG['groups'] as $index => $group): ?>
    <?php if (isset($SIDEBAR_CFG['group_names'][$index])): ?>
    <span class="group-title"><?= $SIDEBAR_CFG['group_names'][$index] ?></span>
    <?php endif; ?>
    <ul>
        <?php foreach ($group as $page): ?>
        <li<?= $page[1] == $SIDEBAR_CFG['selected'] ? ' class="selected"' : '' ?>>
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

<?php elseif ($SIDEBAR_CFG['type'] === 'raw'): ?>

<div id="sidebar-bg-fadeout" onclick="window.closeSidebar();"></div>
<section id="sidebar" hidden>
    <?= $SIDEBAR_CFG['content'] ?>
</section>

<?php endif; ?>
