<!DOCTYPE HTML>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rejestracja</title>
</head>

<body>
    <form action="/register" method="POST">
        <label>Nazwa użytkownika: <input type="text" name="username" required></label><br>
        <label>E-mail: <input type="email" name="email" required></label><br>
        <label>Hasło: <input type="password" name="password" required></label><br>
        <input type="submit" value="Zarejestruj się">
    </form>
</body>

</html>
