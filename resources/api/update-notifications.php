<?php

/** @var \App\Controller\UserController $user_controller */
global $user_controller;

$notifications_message = filter_input(INPUT_POST, 'notifications_message', FILTER_VALIDATE_BOOL);
$notifications_reports = filter_input(INPUT_POST, 'notifications_reports', FILTER_VALIDATE_BOOL);
$notifications_login = filter_input(INPUT_POST, 'notifications_login', FILTER_VALIDATE_BOOL);
$notifications_listings = filter_input(INPUT_POST, 'notifications_listings', FILTER_VALIDATE_BOOL);
$notifications_administrative = filter_input(INPUT_POST, 'notifications_administrative', FILTER_VALIDATE_BOOL);
$notifications_contact = filter_input(INPUT_POST, 'notifications_contact', FILTER_VALIDATE_BOOL);
$notifications_marketing = filter_input(INPUT_POST, 'notifications_marketing', FILTER_VALIDATE_BOOL);
$mobile_app_notifications = filter_input(INPUT_POST, 'mobile_app_notifications', FILTER_VALIDATE_BOOL);
$email_notifications = filter_input(INPUT_POST, 'email_notifications', FILTER_VALIDATE_BOOL);

$user_controller->user->update_notifications(
    $_SESSION['user_id'],
    $notifications_message || false,
    $notifications_reports || false,
    $notifications_login || false,
    $notifications_listings || false,
    $notifications_administrative || false,
    $notifications_contact || false,
    $notifications_marketing || false,
    $mobile_app_notifications || false,
    $email_notifications || false,
);

header('Location: /settings/notifications', true, 303);
