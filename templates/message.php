<!DOCTYPE HTML>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <p>Witaj <b><?= htmlspecialchars($name) ?></b>,</p>
    <p>Dostałeś nową wiadomość od użytkownika: <code><?= htmlspecialchars($messager) ?></code><?= isset($listing) ? "Dotyczącą {$listing}" : '' ?></p>
    <p>Treść wiadomości:</p>
    <code><?= htmlspecialchars($content) ?></code>
</body>
