<?php

/** @var \App\Controller\UserController $user_controller */
global $user_controller;

$SETTINGS_PAGE = [
    'self-url' => '/settings/notifications',
    'head' => '<link rel="stylesheet" href="/_dist/css/settings/notifications.css">',
    'title' => 'Powiadomienia',
    'scripts' => [],
];

ob_start();
?>

<form action="/api/update-notifications" method="POST" enctype="multipart/form-data">
    <table>
        <tbody>
            <tr>
                <td><label for="notifications_message">Powiadomienia o przychodzących wiadomościach</label></td>
                <td>
                    <label class="switch">
                        <input type="checkbox" name="notifications_message" id="notifications_message">
                        <span class="slider"></span>
                    </label>
                </td>
            </tr>
            <tr>
                <td><label for="notifications_reports">Powiadomienia dotyczące zgłoszeń (werdykty, przyjęcie)</label></td>
                <td>
                    <label class="switch">
                        <input type="checkbox" name="notifications_reports" id="notifications_reports">
                        <span class="slider"></span>
                    </label>
                </td>
            </tr>
            <tr>
                <td><label for="notifications_login">Powiadomienia o logowaniu</label></td>
                <td>
                    <label class="switch">
                        <input type="checkbox" name="notifications_login" id="notifications_login">
                        <span class="slider"></span>
                    </label>
                </td>
            </tr>
            <tr>
                <td><label for="notifications_listings">Powiadomienia o ogłoszeniach (dodawanie)</label></td>
                <td>
                    <label class="switch">
                        <input type="checkbox" name="notifications_listings" id="notifications_listings">
                        <span class="slider"></span>
                    </label>
                </td>
            </tr>
            <tr>
                <td><label for="notifications_administrative">Powiadomienia administracyjne (ważne powiadomienia)</label></td>
                <td>
                    <label class="switch">
                        <input type="checkbox" name="notifications_administrative" id="notifications_administrative">
                        <span class="slider"></span>
                    </label>
                </td>
            </tr>
            <tr>
                <td><label for="notifications_contact">Ogłoszenia ogólne</label></td>
                <td>
                    <label class="switch">
                        <input type="checkbox" name="notifications_contact" id="notifications_contact">
                        <span class="slider"></span>
                    </label>
                </td>
            </tr>
            <tr>
                <td><label for="notifications_marketing">Powiadomienia marketingowe</label></td>
                <td>
                    <label class="switch">
                        <input type="checkbox" name="notifications_marketing" id="notifications_marketing">
                        <span class="slider"></span>
                    </label>
                </td>
            </tr>
            <tr>
                <td><label for="mobile_app_notifications">Powiadomienia push (aplikacja)</label></td>
                <td>
                    <label class="switch">
                        <input type="checkbox" name="mobile_app_notifications" id="mobile_app_notifications">
                        <span class="slider"></span>
                    </label>
                </td>
            </tr>
            <tr>
                <td><label for="email_notifications">Powiadomienia mailowe</label></td>
                <td>
                    <label class="switch">
                        <input type="checkbox" name="email_notifications" id="email_notifications">
                        <span class="slider"></span>
                    </label>
                </td>
            </tr>
        </tbody>
    </table>
    <button type="submit" class="btn-accent">
        <i data-lucide="save" aria-hidden="true"></i>
        <span>Zapisz zmiany</span>
    </button>
</form>

<?php
$CONTENT = ob_get_clean();

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/settings.php';

