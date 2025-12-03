<?php

use App\FlashMessage;

/** @var \App\Controller\UserController $user_controller */
global $_ROUTE, $user_controller;

$listing_model = (new App\Builder\ListingBuilder())->make();
$chats_model = (new App\Builder\ChatsBuilder())->make();


// I've tried FILTER_NULL_ON_FAILURE, but for whatever reason it started returning false
$req_chat_id = filter_var($_ROUTE['id'] ?? null, FILTER_VALIDATE_INT);
$req_user_id = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);
$req_listing_id = filter_input(INPUT_GET, 'listing_id', FILTER_VALIDATE_INT);

$new_chat = ($_ROUTE['id'] ?? null) == 'new' && (isset($req_user_id) || isset($req_listing_id));
$show_ui = $new_chat || $req_chat_id;

$title = $new_chat ? 'Nowy czat' : 'Wiadomości';

// Check if user has provided both queries
if (isset($req_listing_id) && isset($req_user_id)) {
    (new FlashMessage())->setErr('i18n:invalid_query_parameters');
    header('Location: /messages', true, 303);
    die;
}

$listing = null;
$user = null;

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

    $existing = $chats_model->find_listings($listing['user_id'], $_SESSION['user_id'], $listing['id']);

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

$chat = null;
$chats = $chats_model->find_by_user($_SESSION['user_id']);
$no_chats = empty($chats);

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

$render_head = function (): string {
    return <<<HTML
    <link rel="stylesheet" href="/_dist/css/messages.css">
    HTML;
};


$x = function () use ($user_controller, $current_user_id, $listing_model, $chats_model, $req_user_id, $req_listing_id, $new_chat, $req_chat_id) {
    //===== NEW CHAT ========================================================//
    $message_box = '';

    if ($new_chat) {
        $destination = '';
        $href = '';
        $img = '';
        $title = '';
        $hidden_input = '';

        if (isset($req_listing_id)) {
            $href = "/listings/{$req_listing_id}";
            $img = $res['cover_file_id'] ? sprintf('<img src="/api/storage/covers/%s" alt="Okładka czatu">', $res['cover_file_id']) : '';
            $title = htmlspecialchars($res["title"]);
            $hidden_input = "<input type='hidden' name='listing_id' value='{$req_listing_id}'>";
        }

        if (isset($req_user_id)) {
            $res = $user_controller->user->get_profile($req_user_id);

            if (!isset($res)) {
                header('Location: /404', true, 303);
                die;
            }

            $href = "/profile/{$req_user_id}";
            $img = sprintf('<img src="/api/storage/profile-pictures/%s" alt="Okładka czatu">', $res['picture_id'] ?: "nil");
            $title = htmlspecialchars($res['display_name']);
            $hidden_input = "<input type='hidden' name='user_id' value='{$req_user_id}'>";
        }

        $message_box .= <<<HTML
        <a href="{$href}" id="message-to">
            {$img}
            <span>{$title}</span>
        </a>
        <div class="no-chats">
            <i class="big-icon" data-lucide="message-square-dashed" aria-hidden="true"></i>
            <span>Napisz pierwszą wiadomość</span>
        </div>
        <section id="message-input">
            <form method="POST" action="/api/new-chat">
                {$hidden_input}
                <input type="text" name="content" placeholder="Treść wiadomości..." minlength="1" required>
                <button type="submit">
                    <i data-lucide="send" aria-hidden="true"></i>
                </button>
            </form>
        </section>
        HTML;
        //===== NO CHAT =========================================================//
    } elseif (!$req_chat_id) {
        $message_box .= <<<HTML
        <div class="no-chats">
            <i class="big-icon" data-lucide="arrow-big-down-dash"></i>
            <span>Tutaj znajdzie się twój czat!</span>
        </div>
        HTML;
        //===== CHAT ============================================================//
    } else {
        $messages = $chats_model->get_messages($req_chat_id);
        $chat = $chats_model->find_by_id($req_chat_id);
        $listing_id = $chat['listing_id'];
        $is_seller = $chat['seller_id'] == $current_user_id;
        
        $hidden_input = "<input type='hidden' name='chat_id' value='{$chat['chat_id']}'>";
        
        $href = '';
        $img = '';
        $title = '';

        if ($chat['contains_listing']) {
            $title = htmlspecialchars($chat['listing_title']);
            $href = "/listings/{$chat['listing_id']}";
            $image_source = sprintf('/api/storage/covers/%s', $chat['cover_file_id']);
            $img = $chat['cover_file_id'] ? sprintf('<img src="%s" alt="Zdjęcie czatu">', $image_source) : '';
        } else {
            $title = htmlspecialchars($is_seller ? $chat['buyer_name'] : $chat['seller_name']);
            $href = '/profile/' . ($is_seller ? $chat['buyer_id'] : $chat['seller_id']);
            $image_source = '/api/storage/profile-pictures/' . ($is_seller ? $chat['buyer_pfp_file_id'] : $chat['seller_pfp_file_id']);
            $img = sprintf('<img src="%s" alt="Zdjęcie czatu">', $image_source);
        }

        $template = "";

        $no_chats = empty($messages) ? <<<HTML
        <div class="no-chats">
            <i class="big-icon" data-lucide="message-square-dashed" aria-hidden="true"></i>
            <span>Napisz pierwszą wiadomość</span>
        </div>
        HTML : '';

        foreach ($messages as $message) {
            [
                'content' => $content,
                'display_name' => $display_name,
                'user_id' => $user_id,
            ] = $message;

            $is_author = $current_user_id == $user_id;
            $class = $is_author ? ' author' : '';
            $display_name = htmlspecialchars($display_name);

            $template .= <<<HTML
            <div class="message{$class}">
                <p class="message-author">{$display_name}</p>
                <p class="message-content">{$content}</p>
            </div>
            HTML;
        }

        $message_box .= <<<HTML
        <a href="{$href}" id="message-to">
            {$img}
            <span>{$title}</span>
        </a>
        <section id="message-list">
            {$no_chats}{$template}
        </section>
        <section id="message-input">
            <form method="POST" action="/api/new-message">
                <input type="text" name="content" placeholder="Treść wiadomości..." minlength="1" required>
                <button type="submit">
                    <i data-lucide="send" aria-hidden="true"></i>
                </button>
            </form>
        </section>
        HTML;
    }

    return <<<HTML
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
    <template id="chat-template">
        
    </template>
    <div id="chats-sidebar">
        <section id="tabs">
            <ul>
                <li><label>Wszystkie<input type="radio" class="sr-only" name="tab" value="all" checked></label></li>
                <li><label>Kupno<input type="radio" class="sr-only" name="tab" value="buy"></label></li>
                <li><label>Sprzedaż<input type="radio" class="sr-only" name="tab" value="sell"></label></li>
            </ul>
        </section>
        <section id="chats-list">
        </section>
    </div>
    <section id="message-box">
        {$message_box}
    </section>
    HTML;
};

$render_scripts = function (): string {
    return <<<HTML
    <script type="module" src="/_dist/js/messages.js"></script>
    HTML;
};


ob_start();
?>

<section id="chats-sidebar">
    <section id="tabs">
        <ul>
            <li><label>Wszystkie<input type="radio" class="sr-only" name="tab" value="all" checked></label></li>
            <li><label>Kupno<input type="radio" class="sr-only" name="tab" value="buy"></label></li>
            <li><label>Sprzedaż<input type="radio" class="sr-only" name="tab" value="sell"></label></li>
        </ul>
    </section>
    <section id="chats-list">
        <?php if ($no_chats): ?>
        <div class="no-chats">
            <i class="big-icon" data-lucide="message-circle-off" aria-hidden="true"></i>
            <span>Nie znaleziono czatów</span>
        </div>
        <?php else: foreach ($chats as $chat): ?>
        <button class="chat<?= $req_chat_id == $chat['chat_id'] ? ' active' : '' ?>" onclick="window.openChat(event)" data-chat-id="<?= $chat['chat_id'] ?>">
            <?php if (!$chat['contains_listing']): ?>
            <img src="/api/storage/profile-pictures/<?= $chat['is_seller'] ? $chat['buyer_pfp_file_id'] : $chat['seller_pfp_file_id'] ?>" alt="Okładka czatu">
            <?php elseif ($chat['cover_file_id']): ?>
            <img src="/api/storage/covers/<?= $chat['cover_file_id'] ?>" alt="Okładka czatu">
            <?php endif ?>
            <div class="chat-details">
                <?php if (!$chat['contains_listing']): ?>
                <h3><?= htmlspecialchars($chat['is_seller'] ? $chat['buyer_name'] : $chat['seller_name']) ?></h3>
                <?php else: ?>
                <h3><?= htmlspecialchars($chat['listing_title']) ?></h3>
                <span><i data-lucide="user" aria-hidden="true"></i> <?= htmlspecialchars($chat['is_seller'] ? $chat['buyer_name'] : $chat['seller_name']) ?></span>
                <?php endif ?>
            </div>
        </button>
        <?php endforeach; endif;?>
    </section>
</section>
<section id="message-box">
    <?php if ($show_ui): ?>
    <a id="message-to" href="<?= isset($req_listing_id) ? "/listings/{$listing['id']}" : "/profile/{$user['id']}" ?>">
        <?php if (isset($req_listing_id) && $listing['cover_file_id']): ?>
        <img src="/api/storage/covers/<?= $listing['cover_file_id'] ?>" alt="Okładka czatu">
        <span><?= $listing['title'] ?></span>
        <?php elseif (isset($req_user_id)): ?>
        <img src="/api/storage/profile-pictures/<?= $user['picture_id'] ?>" alt="Okładka czatu">
        <span><?= $user['display_name'] ?></span>
        <?php endif ?>
    </a>
    <?php endif ?>
    <div id="message-list" class="<?= $new_chat ? '' : 'no-chats' ?>">
        <?php if ($new_chat): ?>
        <i class="big-icon" data-lucide="message-square-dashed" aria-hidden="true"></i>
        <i class="big-icon" data-lucide="arrow-big-down-dash"></i>
        <span>Tutaj znajdzie się twój czat!</span>
        <span>Napisz swoją pierwszą wiadomość</span>
        <?php endif ?>
    </div>
    <?php if ($show_ui): ?> 
    <div id="message-input">
        <form method="POST" action="/api/new-chat">
            <?php if (isset($req_chat_id)): ?>
            <input type="hidden" name="chat_id" value="<?= $chat['id'] ?>">
            <?php elseif (isset($req_listing_id)): ?>
            <input type="hidden" name="listing_id" value="<?= $listing['id'] ?>">
            <?php elseif (isset($req_user_id)): ?>
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
$render_content = ob_get_clean();

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
