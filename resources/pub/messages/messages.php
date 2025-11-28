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
            $img = "";
            $display_name = $chat['is_seller'] ? $chat['buyer_name'] : $chat['seller_name'];
            $list .= <<<HTML
            <div class="chat">
                <p>{$display_name}</p>
            </div>
            HTML;
        }
    }

    return <<<HTML
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
