<?php

/** @var \App\Controller\UserController $user */
global $_ROUTE, $user;

$listing_model = (new App\Builder\ListingBuilder())->make();
$chats_model = (new App\Builder\ChatsBuilder())->make();

$title = "Wiadomości";

$current_user_id = (int)($_SESSION['user_id'] ?? null) ?: null;
$req_chat_id = (int)($_ROUTE['id'] ?? null) ?: null;
$req_user_id = (int)($_GET['user_id'] ?? null) ?: null;
$req_listing_id = (int)($_GET['listing_id'] ?? null) ?: null;
$new_chat = isset($req_user_id) || isset($req_listing_id);

$listing = null;

if ($req_listing_id) {
    unset($req_user_id);

    $listing = $listing_model->get($req_listing_id, -1);
    if (!$listing) {
        // todo: error handling, inform user
        header('Location: /messages', true, 303);
        die;
    }
}

if ($req_user_id) {
    $user = $user->user->find_by_id($req_user_id);
    if (!$user || $current_user_id == $user['id']) {
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

if (!$new_chat && $req_chat_id) {
    $res = $chats_model->find_by_id($req_chat_id);
    if ($res) {
        if ($res['buyer_id'] != $current_user_id && $res['seller_id'] != $current_user_id) {
            header('Location: /messages', true, 303);
            die;
        }
    } else {
        header('Location: /messages', true, 303);
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

$render_content = function () use ($user, $current_user_id, $listing_model, $chats_model, $req_user_id, $req_listing_id, $new_chat, $req_chat_id) {
    $chats = $chats_model->find_by_user($_SESSION['user_id']);

    //===== SIDEBAR =========================================================//
    $list = '';

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
                $lis_title = htmlspecialchars($chat['listing_title']);
                $chat_details = <<<HTML
                    <h3>{$lis_title}</h3>
                    <span><i data-lucide="user" aria-hidden="true"></i>
                    {$target_user_name}</span>
                HTML;
            } else {
                $chat_details = sprintf("<h3>%s</h3>", $target_user_name);
            }

            $img_source = '/api/storage/' . (
                $refers_to_listing
                ? sprintf('covers/%s', $chat['cover_file_id'])
                : sprintf(
                    'profile-pictures/%s',
                    $is_seller
                    ? $chat['buyer_pfp_file_id']
                    : $chat['seller_pfp_file_id']
                )
            );

            $img = ($refers_to_listing && !$chat['cover_file_id']) ? '' : sprintf('<img src="%s"%s alt="Okładka czatu">', $img_source, $refers_to_listing ? '' : ' class="pfp"');

            $active = ($req_chat_id == $id) ? ' active' : '';

            $list .= <<<HTML
            <button class="chat{$active}" onclick="window.openChat(event)" data-chat-id="{$id}">
                {$img}
                <div class="chat-details">
                    {$chat_details}
                </div>
            </button>
            HTML;
        }
    }

    //===== NEW CHAT ========================================================//
    $message_box = '';

    if ($new_chat) {
        $destination = '';
        $href = '';
        $img = '';
        $title = '';
        $hidden_input = '';

        if (isset($req_listing_id)) {
            $res = $listing_model->get($req_listing_id, -1);

            if (!isset($res)) {
                header('Location: /404', true, 303);
                die;
            }

            $href = "/listings/{$req_listing_id}";
            $img = $res['cover_file_id'] ? sprintf('<img src="/api/storage/covers/%s" alt="Okładka czatu">', $res['cover_file_id']) : '';
            $title = htmlspecialchars($res["title"]);
            $hidden_input = "<input type='hidden' name='listing_id' value='{$req_listing_id}'>";
        }

        if (isset($req_user_id)) {
            $res = $user->user->get_profile($req_user_id);

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
