<?php

use App\FlashMessage;

/** @var \App\Controller\UserController $user_controller */
global $_ROUTE, $user_controller;

$listing_model = (new App\Builder\ListingBuilder())->make();
$chats_model = (new App\Builder\ChatsBuilder())->make();

// I've tried FILTER_NULL_ON_FAILURE, but for whatever reason it started returning false
$req_chat_id = filter_var($_ROUTE['id'] ?? null, FILTER_VALIDATE_INT);
$req_new_chat = filter_input(INPUT_GET, 'new_chat', FILTER_VALIDATE_BOOLEAN);
$req_user_id = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);
$req_listing_id = filter_input(INPUT_GET, 'listing_id', FILTER_VALIDATE_INT);

$new_chat = $req_new_chat && (isset($req_user_id) || isset($req_listing_id));
$show_ui = $new_chat || $req_chat_id;

$TITLE = $new_chat ? 'Nowy czat' : 'Wiadomości';
$HEAD = '<link rel="stylesheet" href="/_dist/css/messages.css">';
$SCRIPTS = ['/_dist/js/messages.js'];

// Check if user has provided both queries
if (isset($req_listing_id) && isset($req_user_id)) {
    (new FlashMessage())->setErr('i18n:invalid_query_parameters');
    header('Location: /messages', true, 303);
    die;
}

$listing = null;
$user = null;
$chat = null;
$messages = null;

if ($new_chat && $req_listing_id) {
    $listing = $listing_model->get($req_listing_id, $_SESSION['user_id']);
    $error = false;

    // check if listing exists
    if (!$listing) {
        $error = true;
        (new FlashMessage())->setErr('i18n:listing_not_found');
    }

    // check if the user tries to message himself
    if ($listing['user_id'] == $_SESSION['user_id']) {
        $error = true;
        (new FlashMessage())->setErr('i18n:do_not_message_yourself');
    }

    if ($error) {
        header('Location: /messages', true, 303);
        die;
    }

    $existing = $chats_model->find_listings($listing['user_id'], $_SESSION['user_id'], $listing['listing_id']);

    if ($existing) {
        header("Location: /messages/{$existing['id']}", true, 303);
        die;
    }
}

if ($new_chat && $req_user_id) {
    $user = $user_controller->user->get_profile($req_user_id);
    $error = false;

    // check if user exists
    if (!$user) {
        $error = true;
        (new FlashMessage())->setErr('i18n:user_not_found');
    }

    // check if the user tries to message himself
    if ($user['id'] == $_SESSION['user_id']) {
        $error = true;
        (new FlashMessage())->setErr('i18n:do_not_message_yourself');
    }

    if ($error) {
        header('Location: /messages', true, 303);
        die;
    }

    $existing = $chats_model->find_standalone($user['id'], $_SESSION['user_id']);
    
    if ($existing) {
        header("Location: /messages/{$existing['id']}", true, 303);
        die;
    }
}

if ($req_chat_id) {
    $chat = $chats_model->find_by_id($req_chat_id);

    if (
        ($chat && (
            $chat['buyer_id'] != $_SESSION['user_id'] &&
            $chat['seller_id'] != $_SESSION['user_id']
        )) || !isset($chat)
    ) {
        header('Location: /messages', true, 303);
        die;
    }
}

$is_partial = isset($_SERVER['HTTP_PARTIAL_REQ']);

if (isset($_SERVER['HTTP_PARTIAL_REQ'])) {
    require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/templates/messages.php';
    die;
}

$chats = $chats_model->find_by_user($_SESSION['user_id']);
$no_chats = empty($chats);

$display_image = "";
$href = "";
$title = "";

if ($show_ui) {
    if (isset($chat)) {
        if ($chat['contains_listing']) {
            $display_image_source = !empty($chat['cover_file_id'])
                ? "/api/storage/covers/{$chat['cover_file_id']}"
                : null;
            $href = "/listings/{$chat['listing_id']}";
            $title = htmlspecialchars($chat['listing_title']);
        } else {
            $is_seller_me = ($chat['seller_id'] == $_SESSION['user_id']);
            $profile_id = $is_seller_me
                ? $chat['buyer_id']
                : $chat['seller_id'];
            $pfp_id = $is_seller_me
                            ? $chat['buyer_pfp_file_id']
                            : $chat['seller_pfp_file_id'];
            $display_image_source = $pfp_id
                ? "/api/storage/profile-pictures/{$pfp_id}"
                : null;
            $href = "/profile/{$profile_id}";
            $title = htmlspecialchars($is_seller_me ? $chat['buyer_name'] : $chat['seller_name']);
        }
    } elseif (isset($listing)) {
        $display_image_source = !empty($listing['cover_file_id'])
            ? "/api/storage/covers/{$listing['cover_file_id']}"
            : null;
        $href = "/listings/{$listing['listing_id']}";
        $title = htmlspecialchars($listing['title']);
    } else {
        $display_image_source = "/api/storage/profile-pictures/{$user['picture_id']}";
        $href = "/profile/{$user['id']}";
        $title = htmlspecialchars($user['display_name']);
    }
 
    $display_image = $display_image_source
        ? "<img src='{$display_image_source}' alt='Okładka chatu'>"
        : "";
}

ob_start();
?>

<noscript>
    <div id="noscript">
        <h1>Ta strona wymaga działania skryptów JS do prawidłowego działania!</h1>
        <p>Włącz działanie skryptów aby przejść dalej</p>
        <ul>
            <li><a href="https://support.google.com/adsense/answer/12654">Google Chrome</a></li>
            <li><a href="https://support.mozilla.org/en-US/kb/javascript-settings-for-interactive-web-pages">Firefox</a></li>
            <li><a href="https://support.microsoft.com/en-us/office/enable-javascript-7bb9ee74-6a9e-4dd1-babf-b0a1bb136361">Microsoft Edge</a></li>
            <li><a href="https://support.apple.com/safari">Safari</a></li>
        </ul>
    </div>
</noscript>

<section id="chats-sidebar">
    <section id="tabs">
        <ul>
            <li><label>Wszystkie<input type="radio" class="sr-only" name="tab" value="all" checked></label></li>
            <li><label>Kupno<input type="radio" class="sr-only" name="tab" value="buy"></label></li>
            <li><label>Sprzedaż<input type="radio" class="sr-only" name="tab" value="sell"></label></li>
        </ul>
    </section>
    <section id="chats-list" <?= $no_chats ? 'class="no-chats"' : "" ?>>
        <?php if ($no_chats): ?>
        <i class="big-icon" data-lucide="message-circle-off" aria-hidden="true"></i>
        <span>Nie znaleziono czatów</span>
        <?php else: foreach ($chats as $listing_chat): ?>
        <button class="chat<?= $req_chat_id == $listing_chat['chat_id'] ? ' active' : '' ?>" onclick="window.openChat(event)" data-chat-id="<?= $listing_chat['chat_id'] ?>">
            <?php if (!$listing_chat['contains_listing']): ?>
            <img src="/api/storage/profile-pictures/<?= $listing_chat['is_seller'] ? $listing_chat['buyer_pfp_file_id'] : $listing_chat['seller_pfp_file_id'] ?>" alt="Okładka czatu">
            <?php elseif ($listing_chat['cover_file_id']): ?>
            <img src="/api/storage/covers/<?= $listing_chat['cover_file_id'] ?>" alt="Okładka czatu">
            <?php endif ?>
            <div class="chat-details">
                <?php if (!$listing_chat['contains_listing']): ?>
                <h3><?= htmlspecialchars($listing_chat['is_seller'] ? $listing_chat['buyer_name'] : $listing_chat['seller_name']) ?></h3>
                <?php else: ?>
                <h3><?= htmlspecialchars($listing_chat['listing_title']) ?></h3>
                <span><i data-lucide="user" aria-hidden="true"></i> <?= htmlspecialchars($listing_chat['is_seller'] ? $listing_chat['buyer_name'] : $listing_chat['seller_name']) ?></span>
                <?php endif ?>
            </div>
        </button>
        <?php endforeach; endif;?>
    </section>
</section>

<section id="message-box">
    <?php if ($show_ui): ?>
    <a id="message-to" href="<?= $href ?>">
        <?= $display_image ?>
        <span><?= $title ?></span>
    </a>
    <?php endif ?>

<div id="message-list" class="<?= (!$show_ui || $new_chat) ? 'no-chats' : '' ?>">
    <?php if ($new_chat): ?>
    <i class="big-icon" data-lucide="message-square-dashed" aria-hidden="true"></i>
    <span>Napisz swoją pierwszą wiadomość</span>
    <?php elseif (!$show_ui): ?>
    <i class="big-icon" data-lucide="arrow-big-down-dash"></i>
    <span>Tutaj znajdzie się twój czat!</span>
    <?php else: ?>
    <?php require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/templates/messages.php'; ?>
    <?php endif ?>
</div>

<?php if ($show_ui): ?>
<div id="message-input">
    <form method="POST" action="<?= $new_chat ? '/api/new-chat' : '/api/new-message' ?>">
        <?php if (isset($chat)): ?>
        <input type="hidden" name="chat_id" value="<?= $chat['chat_id'] ?>">
        <?php elseif (isset($listing)): ?>
        <input type="hidden" name="listing_id" value="<?= $listing['listing_id'] ?>">
        <?php elseif (isset($user)): ?>
        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
        <?php endif ?>
        <input type="text" name="content" placeholder="Treść wiadomości..." minlength="1" required>
        <button type="submit">
            <i data-lucide="send" aria-hidden="true"></i>
        </button>
    </form>
</div>
<?php endif ?>
</section>

<?php
$CONTENT = ob_get_clean();

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
