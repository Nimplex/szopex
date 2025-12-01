<?php

/** @var \App\Controller\UserController $user */
global $_ROUTE, $user;

$listing_model = (new App\Builder\ListingBuilder())->make();
$chats_model = (new App\Builder\ChatsBuilder())->make();

$title = "Wiadomości";

$current_user_id = $_SESSION['user_id'] ?: null;
$req_chat_id = $_ROUTE['id'] ?: null;
$req_user_id = $_GET['user_id'] ?: null;
$req_listing_id = $_GET['listing_id'] ?: null;
$new_chat = isset($req_user_id) || isset($req_listing_id);

$listing = null;

if ($req_listing_id) {
    $listing = $listing_model->get($req_listing_id, -1);
    if (!$listing) {
        // todo: error handling, inform user
        header('Location: /messages', true, 303);
        die;
    }
}

if ($req_user_id) {
    $exists = $user->user->exists($req_user_id);
    if (!$exists) {
        // todo: error handling, inform user
        header('Location: /messages', true, 303);
        die;
    }
}

$seller_id = $req_listing_id ? $listing['user_id'] : $req_user_id;

if ($req_listing_id) {
    $res = $chats_model->find_listings($seller_id, $current_user_id, $req_listing_id);
    if ($res) {
        header("Location: /messages/{$res['id']}", true, 303);
        die;
    }
} elseif ($req_user_id) {
    $res = $chats_model->find_standalone($seller_id, $current_user_id);
    if ($res) {
        header("Location: /messages/{$res['id']}", true, 303);
        die;
    }
}

if ($new_chat) {
    $title = "Nowy czat";
}

$render_head = function (): string {
    return <<<HTML
    <link rel="stylesheet" href="/_dist/css/messages.css">
    HTML;
};

$render_content = function () use ($user, $listing_model, $chats_model, $req_user_id, $req_listing_id, $new_chat, $req_chat_id) {
    $chats = $chats_model->find_by_user($_SESSION['user_id']);

    $list = "";

    if (!isset($chats) || empty($chats)) {
        $list = <<<HTML
        <div class="no-chats">
            <i class="big-icon" data-lucide="message-circle-off" aria-hidden="true"></i>
            <span>Nie znaleziono czatów</span>
        </div>
        HTML;
    } else {
        foreach ($chats as $chat) {
            $id = $chat['chat_id'];
            $is_seller = $chat['is_seller'];
            $refers_to_listing = $chat['contains_listing'];

            $chat_details = "";
            $target_user_name = $is_seller
                ? htmlspecialchars($chat['buyer_name'])
                : htmlspecialchars($chat['seller_name']);
            
            if ($refers_to_listing) {
                $chat_details = <<<HTML
                    <h3>{$chat['listing_title']}</h3>
                    <span><i data-lucide="user" aria-hidden="true"></i>
                    {$target_user_name}</span>
                HTML;
            } else {
                $chat_details = sprintf("<h3>%s</h3>", $target_user_name);
            }

            $img = '/api/storage/' . (
                $refers_to_listing
                ? sprintf('covers/%s', $chat['cover_file_id'])
                : sprintf(
                    'profile-pictures/%s',
                    $is_seller
                    ? $chat['buyer_pfp_file_id']
                    : $chat['seller_pfp_file_id']
                )
            );

            $active = ($req_chat_id === $id) ? ' active' : '';
            $pfp_class = $refers_to_listing ? '' : 'class="pfp" ';

            $list .= <<<HTML
            <button class="chat{$active}" onclick="window.openChat(event)" data-chat-id="{$id}">
                <img {$pfp_class}src="{$img}">
                <div class="chat-details">
                    {$chat_details}
                </div>
            </button>
            HTML;
        }
    }

    $message_box = '';

    if ($new_chat) {
        $destination = '';
        $image_source = '';
        $title = '';
        $hidden_input = '';
        $href = '';

        if (isset($req_listing_id)) {
            $res = $listing_model->get($req_listing_id, -1);

            if (!isset($res)) {
                header('Location: /404', true, 303);
                die;
            }

            $href = "/listings/{$req_listing_id}";
            $image_source = "/api/storage/covers/{$res['cover_file_id']}";
            $title = htmlspecialchars($res["title"]);
            $hidden_input = "<input type='hidden' name='listing_id' value='{$req_listing_id}'>";
        }

        if (isset($req_user_id)) {
            $res = $user->user->get_profile($req_user_id);

            if (!isset($res)) {
                header('Location: /404', true, 303);
                die;
            }

            $pic_id = $res['picture_id'] ?: "nil";
            $href = "/profile/{$req_user_id}";
            $image_source = "/api/storage/profile-pictures/{$pic_id}";
            $title = htmlspecialchars($res['display_name']);
            $hidden_input = "<input type='hidden' name='user_id' value='{$req_user_id}'>";
        }

        $message_box .= <<<HTML
        <a href="{$href}" id="message-to">
            <img src="{$image_source}" alt="Zdjęcie czatu">
            <span>{$title}</span>
        </a>
        <section id="new-message">
            <i data-lucide="message-square-dashed" aria-hidden="true"></i>
            <h3>Napisz pierwszą wiadomość!</h3>
        </section>
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
    } elseif (!$req_chat_id) {
        $message_box .= <<<HTML
            <div class="no-chats">
                <i class="big-icon" data-lucide="arrow-big-down-dash"></i>
                <span>Tutaj znajdzie się twój czat!</span>
            </div>
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
            {$list}
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

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
