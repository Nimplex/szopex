<?php

$chatsModel = (new App\Builder\ChatsBuilder())->make();

$title = "Wiadomości";

$render_head = function (): string {
    return <<<HTML
    <link rel="stylesheet" href="/_dist/css/messages.css">
    HTML;
};

$render_content = function () use ($chatsModel) {
    $chats = $chatsModel->find_by_user($_SESSION['user_id']);

    $list = "";

    if (!isset($chats) || empty($chats)) {
        $list = <<<HTML
            <div id="no-chats">
                <i class="big-icon" data-lucide="message-circle-off" aria-hidden="true"></i>
                <span>Nie znaleziono czatów</span>
            </div>
        HTML;
    } else {
        foreach ($chats as $chat) {
            $is_seller = $chat['is_seller'];
            $refers_to_listing = $chat['contains_listing'];

            $top_text = $refers_to_listing ? $chat['listing_title'] : ($chat['is_seller'] ? $chat['buyer_name'] : $chat['seller_name']);
            $bottom_text = $refers_to_listing ? ($chat['is_seller'] ? $chat['buyer_name'] : $chat['seller_name']) : '';
            $img = sprintf("/api/storage/%s", $refers_to_listing ? sprintf('covers/%s', $chat['cover_file_id']) : sprintf('profile-pictures/%s', $is_seller ? $chat['buyer_profile_file_id'] : $chat['seller_profile_file_id']));
        
            $list .= <<<HTML
            <div class="chat">
                <img src="{$img}">
                <div class="chat-details">
                    <h3>{$top_text}</h3>
                    <p>{$bottom_text}</p>
                </div>
            </div>
            HTML;
        }
    }

    return <<<HTML
    <noscript>
        <div id="noscript">
            <h1>Ta strona wymaga działania skryptów JS do prawidłowego działania!</h1>
            <p>Włącz działanie skryptów aby przejść dalej!</p>
            <ul>
                <li><a href="https://support.google.com/adsense/answer/12654">Google Chrome</a></li>
                <li><a href="https://support.mozilla.org/en-US/kb/javascript-settings-for-interactive-web-pages">Firefox</a></li>
                <li><a href="https://support.microsoft.com/en-us/microsoft-edge">Microsoft Edge</a></li>
                <li><a href="https://support.apple.com/safari">Safari</a></li>
            </ul>
        </div>
    </noscript>
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

    </section>
    HTML;
};

$render_scripts = function (): string {
    return <<<HTML
    <script type="module" src="/_dist/js/messages.js"></script>
    HTML;
};

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
