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

    foreach ($chats as $chat) {
        $img = "";
        $display_name = $chat['is_seller'] ? $chat['buyer_name'] : $chat['seller_name'];
        $list .= <<<HTML
        <div class="chat">
            <p>{$display_name}</p>
        </div>
        HTML;
    }

    return <<<HTML
    <section id="chats-list">
        {$list}
    </section>
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
