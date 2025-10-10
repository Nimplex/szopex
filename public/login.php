<!DOCTYPE HTML>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/_css/main.css">
    <title>Logowanie</title>
</head>

<body>
    <?php require $_SERVER['DOCUMENT_ROOT'] . '/../resources/hx-templates/message-box.php'; ?>
    <form action="/api/login" method="POST">
        <label>E-mail: <input type="email" name="email" required></label><br>
        <label>Hasło: <input type="password" name="password" minlength="8" required></label><br>
        <input type="submit" value="Zaloguj się">
    </form>
</body>

</html>
